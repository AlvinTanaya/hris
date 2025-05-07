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
use App\Models\EmployeeDepartment;
use App\Models\EmployeePosition;


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
        $today = Carbon::now();

        // Base query with relationships
        $query = recruitment_demand::with(['departmentRelation', 'positionRelation'])
            ->where('status_demand', 'Approved')
            ->where('qty_needed', '>', 0)
            ->where('opening_date', '<=', $today)
            ->where('closing_date', '>=', $today);

        // Apply department filter
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Apply position filter
        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        // Apply date filter if provided
        if ($request->filled('filter_date')) {
            $date = Carbon::parse($request->filter_date);
            $query->where('opening_date', '<=', $date)
                ->where('closing_date', '>=', $date);
        }

        // Get distinct departments and positions for filters
        $departments = EmployeeDepartment::whereHas('demands', function ($q) use ($today) {
            $q->where('status_demand', 'Approved')
                ->where('qty_needed', '>', 0)
                ->where('opening_date', '<=', $today)
                ->where('closing_date', '>=', $today);
        })->get(['id', 'department']);

        $positions = EmployeePosition::whereHas('demands', function ($q) use ($today) {
            $q->where('status_demand', 'Approved')
                ->where('qty_needed', '>', 0)
                ->where('opening_date', '<=', $today)
                ->where('closing_date', '>=', $today);
        })->get(['id', 'position']);

        // Paginate results
        $demand = $query->paginate(5);
        $demand->appends($request->all());

        return view('job_vacancy.index', compact('demand', 'departments', 'positions'));
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
                'emergency_contact' => $request->emergency_contact,
                'domicile_address' => $request->domicile_address,
                'weight' => $request->weight,
                'height' => $request->height,
                'blood_type' => $request->blood_type,
                'distance' => $request->distance,
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



            // Simpan data pendidikan
            foreach ($request->degree ?? [] as $key => $level) {
                $education = recruitment_applicant_education::create([
                    'applicant_id' => $applicant->id,
                    'degree' => $level,
                    'educational_place' => $request->educational_place[$key] ?? null,
                    'educational_city' => $request->education_city[$key] ?? null,
                    'educational_province' => $request->education_province[$key] ?? null,
                    'start_education' => $request->start_education[$key] ?? null,
                    'end_education' => $request->end_education[$key] ?? null,
                    'grade' => $request->grade[$key] ?? null,
                    'major' => $request->major[$key] ?? null,
                    'transcript_file_path' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Handle file upload untuk sertifikat/transkrip
                if ($request->hasFile("education_certificate.$key")) {
                    $file = $request->file("education_certificate.$key");
                    $extension = $file->getClientOriginalExtension();

                    // Buat nama file yang sesuai
                    $fileName = "transcript_" . "{$applicant->id}_{$level}_{$education->id}.{$extension}";

                    // Simpan file ke public/job_vacancy/transcript_applicant
                    $filePath = $file->storeAs('job_vacancy/transcript_applicant', $fileName, 'public');

                    // Update path file di database
                    $education->update(['transcript_file_path' => "job_vacancy/transcript_applicant/{$fileName}"]);
                }
            }




            // Simpan data family - dengan pengecekan isset untuk semua field
            if (isset($request->family_name)) {
                foreach ($request->family_name as $index => $name) {
                    // Inisialisasi semua nilai dengan null terlebih dahulu
                    $familyData = [
                        'applicant_id' => $applicant->id,
                        'name' => $name,
                        'relation' => null,
                        'birth_date' => null,
                        'birth_place' => null,
                        'ID_number' => null,
                        'phone_number' => null,
                        'address' => null,
                        'gender' => null,
                        'job' => null,
                    ];

                    // Isi nilai hanya jika field tersebut ada di request
                    if (isset($request->relation[$index])) {
                        $familyData['relation'] = $request->relation[$index];
                    }

                    if (isset($request->birth_date_family[$index])) {
                        $familyData['birth_date'] = $request->birth_date_family[$index];
                    }

                    if (isset($request->birth_place_family[$index])) {
                        $familyData['birth_place'] = $request->birth_place_family[$index];
                    }

                    if (isset($request->ID_number_family[$index])) {
                        $familyData['ID_number'] = $request->ID_number_family[$index];
                    }

                    if (isset($request->family_phone[$index])) {
                        $familyData['phone_number'] = $request->family_phone[$index];
                    }

                    if (isset($request->address[$index])) {
                        $familyData['address'] = $request->address[$index];
                    }

                    if (isset($request->gender_family[$index])) {
                        $familyData['gender'] = $request->gender_family[$index];
                    }

                    if (isset($request->job[$index])) {
                        $familyData['job'] = $request->job[$index];
                    }

                    recruitment_applicant_family::create($familyData);
                }
            }




            // Simpan data training
            foreach ($request->training_name ?? [] as $key => $level) {
                recruitment_applicant_training::create([
                    'applicant_id' => $applicant->id,
                    'training_name' => $request->training_name[$key] ?? null,
                    'training_city' => $request->training_city[$key] ?? null,
                    'training_province' => $request->training_province[$key] ?? null,
                    'start_date' => $request->start_training[$key] ?? null,
                    'end_date' => $request->end_training[$key] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }


            // Simpan pengalaman kerja
            foreach ($request->company_name ?? [] as $key => $company) {
                recruitment_applicant_work_experience::create([
                    'applicant_id' => $applicant->id,
                    'company_name' => $company,
                    'position' => $request->position[$key] ?? null,
                    'working_start' => $request->working_start[$key] ?? null,
                    'working_end' => $request->working_end[$key] ?? null,
                    'company_address' => $request->company_address[$key] ?? null,
                    'company_phone' => $request->company_phone[$key] ?? null,
                    'salary' => $request->previous_salary[$key] ?? null,
                    'supervisor_name' => $request->supervisor_name[$key] ?? null,
                    'supervisor_phone' => $request->supervisor_phone[$key] ?? null,
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
                // Jika bahasa "Other" tetapi tidak ada isian, lewati iterasi
                if ($language === 'Other') {
                    $otherLanguage = $request->other_language[$key] ?? null;
                    if (empty($otherLanguage)) {
                        continue;
                    }
                    $language = $otherLanguage; // Gunakan input dari other_language
                }

                recruitment_applicant_language::create([
                    'applicant_id' => $applicant->id,
                    'language' => $language,
                    'verbal' => $request->verbal_proficiency[$key] ?? null,
                    'written' => $request->written_proficiency[$key] ?? null,
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
                    'position' => $request->org_position[$key] ?? null,
                    'city' => $request->org_city[$key] ?? null,
                    'province' => $request->org_province[$key] ?? null,
                    'start_date' => $request->org_start_date[$key] ?? null,
                    'end_date' => $request->org_end_date[$key] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            Mail::to($applicant->email)->send(new ApplicantSubmitted($applicant));

            // Find existing applicant by email or name
            $existingApplicant = recruitment_applicant::where('email', $request->email)
                ->orWhere('name', $request->name)
                ->first();

            // Get HR department users with proper relationship
            $hrDepartment = EmployeeDepartment::where('department', 'Human Resources')->first();
            $hrEmails = $hrDepartment ?
                User::where('department_id', $hrDepartment->id)
                ->pluck('email') :
                collect();

            // Get applicant data with proper relationships
            $query = DB::table('recruitment_demand')
                ->join('recruitment_applicant', 'recruitment_demand.id', '=', 'recruitment_applicant.recruitment_demand_id')
                ->join('employee_departments', 'recruitment_demand.department_id', '=', 'employee_departments.id')
                ->join('employee_positions', 'recruitment_demand.position_id', '=', 'employee_positions.id')
                ->select(
                    'recruitment_applicant.name as name',
                    'recruitment_applicant.email as email',
                    'recruitment_demand.recruitment_demand_id as labor_demand_id',
                    'employee_departments.department as department_name',
                    'employee_positions.position as position_name'
                )
                ->where('recruitment_demand.id', $applicant->recruitment_demand_id) // Fixed typo in column name
                ->first();

            // Send email to all HR
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
