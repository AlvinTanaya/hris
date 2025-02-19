<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\recruitment_demand;
use App\Models\recruitment_applicant;
use App\Models\recruitment_applicant_education;
use App\Models\recruitment_applicant_family;
use App\Models\recruitment_applicant_work_experience;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class VacancyController extends Controller
{
    public function index(Request $request)
    {
        //dd($request->all());
        $today = Carbon::now();
        $query = recruitment_demand::where('status_demand', 'Approved')
            ->where('qty_needed', '>', 0)
            ->where('opening_date', '<=', $today)
            ->where('closing_date', '>=', $today);

        // Apply department filter
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Apply position filter
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        // Apply date filter if provided
        if ($request->filled('filter_date')) {
            $date = Carbon::parse($request->filter_date);
            $query->where('opening_date', '<=', $date)
                ->where('closing_date', '>=', $date);
        }

        $demand = $query->get();

        //dd($demand);

        return view('job_vacancy.index', compact('demand'));
    }







    public function store(Request $request, $id)
    {

        //dd($request->all());
        try {

            DB::beginTransaction();

            // First create the applicant record without files
            $applicant = recruitment_applicant::create([
                'recruitment_demand_id' => $id,
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'ID_number' => $request->nik,
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,
                'religion' => $request->religion,
                'gender' => $request->gender,
                'ID_address' => $request->ID_address,
                'domicile_address' => $request->domicile_address,
                'weight' => $request->weight,
                'height' => $request->height,
                'blood_type' => $request->blood_type,
                'bpjs_health' => $request->bpjs_health,
                'bpjs_employment' => $request->bpjs_employment,
                'status_applicant' => 'Pending',
            ]);

            // Handle file uploads with custom naming
            if ($request->hasFile('photo_profile_path')) {
                $profileFile = $request->file('photo_profile_path');
                $profileExtension = $profileFile->getClientOriginalExtension();
                $profileFileName = 'profile_' . Str::slug($applicant->name) . '_' . $applicant->id . '.' . $profileExtension;

                $profilePath = $profileFile->storeAs(
                    'job_vacancy/photo_profile_applicant',
                    $profileFileName,
                    'public'
                );

                $applicant->photo_profile_path = $profilePath;
            }

            if ($request->hasFile('cv_path')) {
                $cvFile = $request->file('cv_path');
                $cvExtension = $cvFile->getClientOriginalExtension();
                $cvFileName = 'cv_' . Str::slug($applicant->name) . '_' . $applicant->id . '.' . $cvExtension;

                $cvPath = $cvFile->storeAs(
                    'job_vacancy/cv_applicant',
                    $cvFileName,
                    'public'
                );

                $applicant->cv_path = $cvPath;
            }

            if ($request->hasFile('ID_card_path')) {
                $id_cardFile = $request->file('ID_card_path');
                $id_cardExtension = $id_cardFile->getClientOriginalExtension();
                $id_cardFileName = 'ID_card_' . Str::slug($applicant->name) . '_' . $applicant->id . '.' . $id_cardExtension;

                $id_cardPath = $id_cardFile->storeAs(
                    'job_vacancy/id_card_applicant',
                    $id_cardFileName,
                    'public'
                );

                $applicant->ID_card_path = $id_cardPath;
            }


            // dd($applicant);


            // Save the updated paths
            $applicant->save();



            // Store education history
            foreach ($request->degree as $key => $level) {
                recruitment_applicant_education::create([
                    'applicant_id' => $applicant->id,
                    'degree' => $level,
                    'educational_place' => $request->educational_place[$key],
                    'start_education' => $request->start_education[$key],
                    'end_education' => $request->end_education[$key],
                    'grade' => $request->grade[$key],
                    'major' => $request->major[$key]
                ]);
            }

            // Store family information
            foreach ($request->family_name as $key => $name) {
                recruitment_applicant_family::create([
                    'applicant_id' => $applicant->id,
                    'name' => $name,
                    'relation' => $request->relation[$key],
                    'birth_date' => $request->birth_date_family[$key],
                    'birth_place' => $request->birth_place_family[$key],
                    'ID_number' => $request->ID_number_family[$key],
                    'phone_number' => $request->family_phone[$key],
                    'address' => $request->address[$key],
                    'gender' => $request->family_gender[$key],
                    'job' => $request->job[$key]
                ]);
            }

            // Store work experience
            foreach ($request->company_name as $key => $company) {
                recruitment_applicant_work_experience::create([
                    'applicant_id' => $applicant->id,
                    'company_name' => $company,
                    'position' => $request->position[$key],
                    'working_start' => $request->working_start[$key],
                    'working_end' => $request->working_end[$key]
                ]);
            }

            DB::commit();



            return redirect()->route('welcome')->with('success', 'Application submitted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded files if they exist
            if (isset($profilePath)) {
                Storage::disk('public')->delete($profilePath);
            }
            if (isset($cvPath)) {
                Storage::disk('public')->delete($cvPath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error submitting application: ' . $e->getMessage()
            ], 500);
        }
    }



    public function create($id)
    {

        $demand = recruitment_demand::where('id', $id)->first();
        // dd($demand);
        return view('job_vacancy.create', compact('demand'));
    }
}
