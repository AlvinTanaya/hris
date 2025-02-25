<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\recruitment_demand;
use App\Models\recruitment_applicant;
use App\Models\recruitment_applicant_education;
use App\Models\recruitment_applicant_training;
use App\Models\recruitment_applicant_family;
use App\Models\recruitment_applicant_work_experience;
use App\Models\recruitment_applicant_organization;
use App\Models\recruitment_applicant_language;
use App\Models\User;
use Carbon\Carbon;
use App\Mail\ApplicantSubmitted;
use Illuminate\Support\Facades\Mail;
use App\Mail\HRDNotificationApplicantMail;

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


        // Cek apakah email atau nama sudah terdaftar
        $existingApplicant = recruitment_applicant::where('email', $request->email)
            ->orWhere('name', $request->name)
            ->first();

        if ($existingApplicant) {
            return redirect()->back()->with('error', 'You have already applied. You can only submit once.');
        }

        // dd($request->all());
        // dd($request->has('no_license'));

        try {
            DB::beginTransaction();

            // Simpan data pelamar tanpa file terlebih dahulu
            $applicant = recruitment_applicant::create([
                'recruitment_demand_id' => $id,
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'ID_number' => $request->ID_number,
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
                'expected_salary' => $request->expected_salary,
                'expected_benefits' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->expected_benefits ?? '')),
                'expected_facilities' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->expected_facilities ?? '')),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Handle file uploads
            $fileFields = [
                'photo_profile_path' => ['path' => 'job_vacancy/photo_profile_applicant', 'prefix' => 'profile'],
                'cv_path' => ['path' => 'job_vacancy/cv_applicant', 'prefix' => 'cv'],
                'ID_card_path' => ['path' => 'job_vacancy/id_card_applicant', 'prefix' => 'ID_card'],
                'achievement_path' => ['path' => 'job_vacancy/achievement_applicant', 'prefix' => 'achievement']
            ];

            foreach ($fileFields as $field => $config) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $extension = $file->getClientOriginalExtension();

                    // Format nama file sesuai dengan struktur yang ada di gambar
                    $fileName = "{$config['prefix']}_" . Str::slug($applicant->name) . "_{$applicant->id}.{$extension}";
                    $filePath = $file->storeAs($config['path'], $fileName, 'public');

                    // Simpan path lengkap + nama file di database
                    $applicant->$field = "{$config['path']}/{$fileName}";
                }
            }

            if (!$request->has('no_license') || $request->has('sim')) {
                // Lakukan sesuatu
                $applicant->sim = $request->has('sim') ? implode(',', $request->sim) : null;

                // Ambil nomor SIM sesuai SIM yang dipilih
                $selectedSimNumbers = [];
                if ($request->has('sim')) {
                    foreach ($request->sim as $simType) {
                        if (!empty($request->sim_number[$simType])) {
                            $selectedSimNumbers[$simType] = $request->sim_number[$simType];
                        }
                    }
                }

                // Simpan nomor SIM dengan format JSON
                $applicant->sim_number = !empty($selectedSimNumbers) ? json_encode($selectedSimNumbers) : null;
            } else {
                $applicant->sim = null;
                $applicant->sim_number = null;
            }


            $applicant->save();


            // Simpan data family

            // Simpan setiap anggota keluarga ke dalam database
            foreach ($request->family_name as $index => $name) {
                recruitment_applicant_family::create([
                    'applicant_id' => $applicant->id,
                    'name' => $name,
                    'relation' => $request->relation[$index],
                    'birth_date' => $request->birth_date_family[$index],
                    'birth_place' => $request->birth_place_family[$index],
                    'ID_number' => $request->ID_number_family[$index],
                    'phone_number' => $request->family_phone[$index],
                    'address' => $request->address[$index],
                    'gender' => $request->gender_family[$index],
                    'job' => $request->job[$index],
                ]);
            }


            // Simpan data pendidikan
            foreach ($request->degree ?? [] as $key => $level) {
                recruitment_applicant_education::create([
                    'applicant_id' => $applicant->id,
                    'degree' => $level,
                    'educational_place' => $request->educational_place[$key] ?? '',
                    'educational_city' => $request->education_city[$key] ?? '',
                    'start_education' => $request->start_education[$key] ?? '',
                    'end_education' => $request->end_education[$key] ?? '',
                    'grade' => $request->grade[$key] ?? '',
                    'major' => $request->major[$key] ?? '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Simpan data training
            foreach ($request->training_name ?? [] as $key => $level) {
                recruitment_applicant_education::create([
                    'applicant_id' => $applicant->id,
                    'training_name' => $request->training_name[$key] ?? '',
                    'training_city' => $request->training_city[$key] ?? '',
                    'start_date' => $request->start_training[$key] ?? '',
                    'end_date' => $request->end_training[$key] ?? '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }


            // Simpan pengalaman kerja
            foreach ($request->company_name ?? [] as $key => $company) {
                recruitment_applicant_work_experience::create([
                    'applicant_id' => $applicant->id,
                    'company_name' => $company,
                    'position' => $request->position[$key] ?? '',
                    'working_start' => $request->working_start[$key] ?? '',
                    'working_end' => $request->working_end[$key] ?? '',
                    'company_address' => $request->company_address[$key] ?? '',
                    'company_phone' => $request->company_phone[$key] ?? '',
                    'salary' => $request->previous_salary[$key] ?? '',
                    'supervisor_name' => $request->supervisor_name[$key] ?? '',
                    'supervisor_phone' => $request->supervisor_phone[$key] ?? '',
                    'job_desc' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->job_description[$key] ?? '')),
                    'reason' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->leaving_reason[$key] ?? '')),
                    'benefit' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->previous_benefits[$key] ?? '')),
                    'facility' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->previous_facilities[$key] ?? '')),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Simpan data bahasa
            foreach ($request->language ?? [] as $key => $language) {
                recruitment_applicant_language::create([
                    'applicant_id' => $applicant->id,
                    'language' => $language,
                    'verbal' => $request->verbal_proficiency[$key] ?? '',
                    'written' => $request->written_proficiency[$key] ?? '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Simpan data organisasi
            foreach ($request->org_name ?? [] as $key => $org) {
                recruitment_applicant_organization::create([
                    'applicant_id' => $applicant->id,
                    'organization_name' => $org,
                    'activity_type' => str_replace(["\r", "\n", "-", "  "], ["", ";", "", " "], trim($request->activity_type[$key] ?? '')),
                    'position' => $request->org_position[$key] ?? '',
                    'city' => $request->org_city[$key] ?? '',
                    'start_date' => $request->org_start_date[$key] ?? '',
                    'end_date' => $request->org_end_date[$key] ?? '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            Mail::to($applicant->email)->send(new ApplicantSubmitted($applicant));

            $existingApplicant = recruitment_applicant::where('email', $request->email)
                ->orWhere('name', $request->name)
                ->first();

            $hrEmails = User::where('department', 'Human Resources')->pluck('email');

            $query = DB::table('recruitment_demand')
                ->join('recruitment_applicant', 'recruitment_demand.id', '=', 'recruitment_applicant.recruitment_demand_id')
                ->select(
                    'recruitment_applicant.name as name',
                    'recruitment_applicant.email as email',
                    'recruitment_demand.recruitment_demand_id as labor_demand_id'
                )->where('recruitment_demand.id', $applicant->recreuitment_demand_id)->first();

            // Kirim email ke semua HR
            if ($hrEmails->isNotEmpty()) {
                Mail::to($hrEmails)->send(new HRDNotificationApplicantMail($query));
            }

            return redirect()->route('welcome')->with('success', 'Application submitted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
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
