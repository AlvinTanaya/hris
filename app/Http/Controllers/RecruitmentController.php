<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\recruitment_demand;
use App\Models\recruitment_applicant;
use App\Models\recruitment_applicant_education;
use App\Models\recruitment_applicant_family;
use App\Models\recruitment_applicant_work_experience;
use App\Models\User;
use App\Models\users_education;
use App\Models\users_family;
use App\Models\users_work_experience;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class RecruitmentController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('recruitment_demand')
            ->join('users', 'recruitment_demand.maker_id', '=', 'users.id')
            ->select(
                'users.name as maker_name',
                'recruitment_demand.*'
            );

        // Apply filters
        if ($request->filled('status_demand')) {
            $query->where('recruitment_demand.status_demand', $request->status_demand);
        }

        if ($request->filled('department')) {
            $query->where('recruitment_demand.department', $request->department);
        }

        if ($request->filled('position')) {
            $query->where('recruitment_demand.position', $request->position);
        }

        if ($request->filled('status_job')) {
            $query->where('recruitment_demand.status_job', $request->status_job);
        }

        if ($request->filled('opening_date')) {
            $query->whereDate('recruitment_demand.opening_date', $request->opening_date);
        }

        if ($request->filled('closing_date')) {
            $query->whereDate('recruitment_demand.closing_date', $request->closing_date);
        }

        // Get distinct values for dropdowns
        $departments = DB::table('recruitment_demand')->distinct()->pluck('department');
        $positions = DB::table('recruitment_demand')->distinct()->pluck('position');
        $jobStatuses = DB::table('recruitment_demand')->distinct()->pluck('status_job');

        $demand = $query->get();

        return view('recruitment/labor_demand/index', compact(
            'demand',
            'departments',
            'positions',
            'jobStatuses'
        ));
    }

    public function create_labor_demand()
    {
        return view('recruitment/labor_demand/create');
    }

    public function edit_labor_demand($id)
    {

        $demand = recruitment_demand::where('id', $id)->first();


        return view('recruitment/labor_demand/update', compact('demand'));
    }


    public function approve_labor_demand($id)
    {
        recruitment_demand::where('id', $id)->update([
            'status_demand' => 'Approved'
        ]);
        return redirect()->route('recruitment.index')
            ->with('success', 'Job request has been approved successfully');
    }
    public function decline_labor_demand(Request $request, $id)
    {
        recruitment_demand::where('id', $id)->update([
            'status_demand' => 'Declined',
            'declined_reason' => $request->declined_reason,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('recruitment.index')
            ->with('success', 'Job request has been declined successfully');
    }

    public function store_labor_demand(Request $request)
    {
        // Validasi input  
        $validated = $request->validate([
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'opening_date' => 'required|date',
            'closing_date' => 'required|date',
            'status_job' => 'required|string|max:255',
            'reason' => 'required|string|max:255',
            'qty_needed' => 'required|integer',
            'gender' => 'required|string|max:255',
            'job_goal' => 'required|string|max:255',
            'education' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'experience' => 'required|string|max:255',
            'length_of_working' => 'required|integer',
            'skills' => 'required|string|max:255',
            'time_work_experience' => 'nullable|string|max:255',
        ]);

        // Tambahkan status dan informasi tambahan  
        $validated['status_demand'] = 'Pending';
        $validated['qty_fullfil'] = 0;
        $validated['created_at'] = now();
        $validated['updated_at'] = now();
        $validated['maker_id'] = $request->maker_id;

        // Menggunakan ID yang unik  
        $lastId = recruitment_demand::max('id');
        $newId = $lastId ? $lastId + 1 : 1;
        $validated['ptk_id'] = 'ptk_' . $newId;

        // Proses input untuk menyimpan ke database  
        $validated['reason'] = implode("\n", array_map('trim', explode("\n", $request->reason)));
        $validated['job_goal'] = implode("\n", array_map('trim', explode("\n", $request->job_goal)));
        $validated['experience'] = implode("\n", array_map('trim', explode("\n", $request->experience)));
        $validated['skills'] = implode("\n", array_map('trim', explode("\n", $request->skills)));

        // Simpan data ke database  
        recruitment_demand::create($validated);

        return redirect()->route('recruitment.index')
            ->with('success', 'Job request has been created successfully');
    }

    public function show_labor_demand($id)
    {
        // dd($id);
        $demand = recruitment_demand::find($id);

        if (!$demand) {
            return response()->json(['message' => 'Labor Demand not found'], 404);
        }

        return response()->json([
            'id' => $demand->id,
            'recruitment_demand_id' => $demand->recruitment_demand_id,
            'status_demand' => $demand->status_demand,
            'department' => $demand->department,
            'position' => $demand->position,
            'opening_date' => $demand->opening_date,
            'closing_date' => $demand->closing_date,
            'status_job' => $demand->status_job,
            'reason' => $demand->reason,
            'qty_needed' => $demand->qty_needed,
            'qty_fullfil' => $demand->qty_fullfil,
            'gender' => $demand->gender,
            'job_goal' => $demand->job_goal,
            'education' => $demand->education,
            'major' => $demand->major,
            'experience' => $demand->experience,
            'length_of_working' => $demand->length_of_working,
            'time_work_experience' => $demand->time_work_experience,
            'declined_reason' => $demand->declined_reason,
            'skills' => $demand->skills,
        ]);
    }
    public function update_labor_demand(Request $request, $id)
    {
        // Validasi input  
        $validated = $request->validate([
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'opening_date' => 'required|date',
            'closing_date' => 'required|date',
            'status_job' => 'required|string|max:255',
            'reason' => 'required|string|max:255',
            'qty_needed' => 'required|integer',
            'gender' => 'required|string|max:255',
            'job_goal' => 'required|string|max:255',
            'education' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'experience' => 'required|string|max:255',
            'length_of_working' => 'required|integer',
            'skills' => 'required|string|max:255',
        ]);

        try {

            $demand = recruitment_demand::findOrFail($id);
            $validated['updated_at'] = now();
            $validated['maker_id'] = $request->maker_id;
            if ($request->time_work_experience == null) {
                $validated['time_work_experience'] = null;
            } else {
                $validated['time_work_experience'] = $request->time_work_experience;
            }
            $validated['status_demand'] = $demand->status_demand;
            $validated['qty_fullfil'] = $demand->qty_fullfil;
            $validated['ptk_id'] = $demand->ptk_id;


            $demand->update($validated);

            // Redirect dengan pesan sukses  
            return redirect()->route('recruitment.index')
                ->with('success', 'Job request has been updated successfully');
        } catch (\Exception $e) {
            // Tangani error  
            return redirect()->back()
                ->with('error', 'Failed to update job request: ' . $e->getMessage())
                ->withInput();
        }
    }






    /**
     * Display AHP recommendation page
     */

    private $criteria = [
        'age' => 'Usia',
        'experience_duration' => 'Lama Pengalaman',
        'company_count' => 'Jumlah Perusahaan',
        'education_grade' => 'Nilai Pendidikan',
        'education_level' => 'Tingkat Pendidikan'
    ];

    public function index_ahp()
    {
        // Retrieve all recruitment applicants and pluck their unique recruitment_demand_ids  
        $recruitmentDemandIds = recruitment_applicant::pluck('recruitment_demand_id')->unique();

        // Filter recruitment_demand based on the unique recruitment_demand_ids  
        $demands = recruitment_demand::whereIn('id', $recruitmentDemandIds)
            ->where('status_demand', 'Approved')
            ->where('qty_needed', '>', 0)
            ->get();

        // Return the filtered recruitment_demand data to the view  
        return view('recruitment/ahp_recruitment/index', [
            'demands' => $demands,
            'criteria' => $this->criteria
        ]);
    }

    // AHPController.php

    public function calculateWeights(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'demandId' => 'required',
                'age_education' => 'required|numeric|min:0.11|max:9',
                'age_grade' => 'required|numeric|min:0.11|max:9',
                'age_experience' => 'required|numeric|min:0.11|max:9',
                'education_experience' => 'required|numeric|min:0.11|max:9',
                'experience_company' => 'required|numeric|min:0.11|max:9',
            ]);

            // Create comparison matrix from input
            $matrix = $this->createComparisonMatrix($request);

            // Calculate weights using AHP
            $weights = $this->calculateAHPWeights($matrix);

            return response()->json([
                'success' => true,
                'weights' => $weights
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error calculating weights: ' . $e->getMessage()
            ], 422);
        }
    }

    public function calculateRankings(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'demandId' => 'required',
                'weights' => 'required|array'
            ]);

            $demand = recruitment_demand::findOrFail($request->demandId);
            $applicants = recruitment_applicant::where('recruitment_demand_id', $demand->id)->get();

            if ($applicants->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada pelamar untuk demand ini'
                ], 200);
            }

            $rankings = $this->calculateApplicantScores($applicants, $request->weights);

            return response()->json([
                'success' => true,
                'rankings' => $rankings
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error calculating rankings: ' . $e->getMessage()
            ], 422);
        }
    }


    public function calculate(Request $request)
    {

        try {
            // Validate the request
            $request->validate([
                'demandId' => 'required',
                'age_education' => 'required|numeric|min:0.11|max:9',
                'age_grade' => 'required|numeric|min:0.11|max:9',
                'age_experience' => 'required|numeric|min:0.11|max:9',
                'education_experience' => 'required|numeric|min:0.11|max:9',
                'experience_company' => 'required|numeric|min:0.11|max:9',
            ]);

            // Create comparison matrix from input
            $matrix = $this->createComparisonMatrix($request);

            // Calculate weights using AHP
            $weights = $this->calculateAHPWeights($matrix);

            // Get applicants data

            $demand = recruitment_demand::findOrFail($request->demandId);
            $applicants = recruitment_applicant::where('recruitment_demand_id', $demand->id)->get();

            if ($applicants->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada pelamar untuk demand ini'
                ], 200);
            }

            $rankings = $this->calculateApplicantScores($applicants, $weights);

            return response()->json([
                'success' => true,
                'rankings' => $rankings
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data yang dikirim tidak valid',
                'errors' => $e->errors()
            ], 422);
        }
    }
    private function createComparisonMatrix($request)
    {
        $criteriaCount = count($this->criteria);
        $matrix = array_fill(0, $criteriaCount, array_fill(0, $criteriaCount, 1));

        // Fill matrix with comparison values
        $comparisons = [
            ['age', 'education_level', 'age_education'],
            ['age', 'education_grade', 'age_grade'],
            ['age', 'experience_duration', 'age_experience'],
            ['education_level', 'experience_duration', 'education_experience'],
            ['experience_duration', 'company_count', 'experience_company']
        ];

        foreach ($comparisons as $comp) {
            $value = $request->input($comp[2]);
            $i = array_search($comp[0], array_keys($this->criteria));
            $j = array_search($comp[1], array_keys($this->criteria));

            $matrix[$i][$j] = $value;
            $matrix[$j][$i] = 1 / $value;
        }

        return $matrix;
    }

    private function calculateAHPWeights($matrix)
    {
        $n = count($matrix);
        $weights = array_fill(0, $n, 0);

        // Calculate eigenvalues
        for ($i = 0; $i < $n; $i++) {
            $product = 1;
            for ($j = 0; $j < $n; $j++) {
                $product *= $matrix[$i][$j];
            }
            $weights[$i] = pow($product, 1 / $n);
        }

        // Normalize weights
        $sum = array_sum($weights);
        $weights = array_map(function ($w) use ($sum) {
            return $w / $sum;
        }, $weights);

        return array_combine(array_keys($this->criteria), $weights);
    }

    private function calculateApplicantScores($applicants, $weights)
    {
        $scores = [];

        foreach ($applicants as $applicant) {
            $criteriaScores = [
                'age' => $this->calculateAgeScore($applicant->birth_date),
                'experience_duration' => $this->calculateExperienceDurationScore(
                    recruitment_applicant_work_experience::where('applicant_id', $applicant->id)->get()
                ),
                'company_count' => $this->calculateCompanyCountScore(
                    recruitment_applicant_work_experience::where('applicant_id', $applicant->id)->get()
                )
            ];


            $education = recruitment_applicant_education::where('applicant_id', $applicant->id)
                ->orderBy('end_education', 'desc')
                ->first();

            $criteriaScores['education_level'] = $education ?
                $this->calculateEducationLevelScore($education->degree) : 0;
            $criteriaScores['education_grade'] = $education ?
                $this->calculateEducationGradeScore($education->degree, $education->grade) : 0;

            // Calculate weighted sum
            $totalScore = 0;
            foreach ($weights as $criterion => $weight) {
                $totalScore += $criteriaScores[$criterion] * $weight;
            }

            $scores[] = [
                'applicant' => $applicant,
                'score' => $totalScore,
                'breakdown' => $criteriaScores
            ];
        }

        return collect($scores)->sortByDesc('score')->values();
    }


    private function calculateAgeScore($birthDate)
    {
        $age = Carbon::parse($birthDate)->age;

        if ($age <= 25) return 1.0;
        if ($age <= 30) return 0.8;
        if ($age <= 35) return 0.6;
        return 0.4;
    }

    private function calculateEducationLevelScore($degree)
    {
        $scores = [
            'SD' => 0.2,
            'SMP' => 0.3,
            'SMA' => 0.4,
            'SMK' => 0.4,
            'D3' => 0.6,
            'S1' => 0.8,
            'S2' => 1.0,
            'S3' => 1.0
        ];

        return $scores[$degree] ?? 0.4;
    }

    private function calculateEducationGradeScore($degree, $grade)
    {
        if ($grade === null) {
            return 0; // Handle jika nilai kosong
        }

        if (in_array($degree, ['SD', 'SMP', 'SMA', 'SMK'])) {
            return min(1, max(0, $grade / 100));
        } else {
            return min(1, max(0, $grade / 4));
        }
    }

    private function calculateExperienceDurationScore($experiences)
    {
        $totalDuration = 0;

        foreach ($experiences as $exp) {
            $startDate = Carbon::parse($exp->working_start);
            $endDate = $exp->working_end ? Carbon::parse($exp->working_end) : Carbon::now();
            $totalDuration += $startDate->diffInYears($endDate);
        }

        if ($totalDuration >= 5) return 1.0;
        if ($totalDuration >= 3) return 0.8;
        if ($totalDuration >= 1) return 0.6;
        return 0.4;
    }

    private function calculateCompanyCountScore($experiences)
    {
        $companyCount = $experiences->count();

        if ($companyCount >= 3) return 1.0;
        if ($companyCount == 2) return 0.7;
        if ($companyCount == 1) return 0.5;
        return 0.2;
    }








    /**
     * Display interview page
     */


    public function index_interview(Request $request)
    {
        $query = DB::table('recruitment_demand')
            ->join('users', 'recruitment_demand.maker_id', '=', 'users.id')
            ->where('qty_needed', '>', 0)
            ->where('status_demand', 'Approved')
            ->select(
                'users.name as maker_name',
                'recruitment_demand.*'
            );

        // Apply filters
        if ($request->filled('status_demand')) {
            $query->where('recruitment_demand.status_demand', $request->status_demand);
        }

        if ($request->filled('department')) {
            $query->where('recruitment_demand.department', $request->department);
        }

        if ($request->filled('position')) {
            $query->where('recruitment_demand.position', $request->position);
        }

        if ($request->filled('status_job')) {
            $query->where('recruitment_demand.status_job', $request->status_job);
        }

        if ($request->filled('opening_date')) {
            $query->whereDate('recruitment_demand.opening_date', $request->opening_date);
        }

        if ($request->filled('closing_date')) {
            $query->whereDate('recruitment_demand.closing_date', $request->closing_date);
        }

        // Get distinct values for dropdowns
        $departments = DB::table('recruitment_demand')->where('qty_needed', '>', 0)
            ->where('status_demand', 'Approved')->distinct()->pluck('department');
        $positions = DB::table('recruitment_demand')->where('qty_needed', '>', 0)
            ->where('status_demand', 'Approved')->distinct()->pluck('position');
        $jobStatuses = DB::table('recruitment_demand')->where('qty_needed', '>', 0)
            ->where('status_demand', 'Approved')->distinct()->pluck('status_job');

        $demand = $query->get();

        return view('recruitment/interview/index', compact(
            'demand',
            'departments',
            'positions',
            'jobStatuses'
        ));
    }

    public function applicant_list($id)
    {
        $demand = recruitment_demand::where('id', $id)->first();
        $applicant = recruitment_applicant::where('recruitment_demand_id', $id)->where('status_applicant', 'Pending')->whereNull('interview_date')->get();


        $applicantInterview = recruitment_applicant::where('recruitment_demand_id', $id)->where('status_applicant', '=', 'Pending')->whereNotNull('interview_date')->get();
        $applicantApproved = recruitment_applicant::where('recruitment_demand_id', $id)
            ->whereIn('status_applicant', ['Approved', 'Done'])
            ->get();

        $applicantDeclined = recruitment_applicant::where('recruitment_demand_id', $id)->where('status_applicant', '=', 'Declined')->get();

        return view('recruitment/interview/applicant', compact('demand', 'applicant', 'applicantInterview', 'applicantApproved', 'applicantDeclined'));
    }

    public function show_applicant($id)
    {
        $applicant = recruitment_applicant::where('id', $id)->first();

        if (!$applicant) {
            return response()->json(['message' => 'Applicant not found'], 404);
        }

        $applicantFamily = recruitment_applicant_family::where('applicant_id', $applicant->id)->get();
        $applicantEducation = recruitment_applicant_education::where('applicant_id', $applicant->id)->get();
        $applicantWorkExperience = recruitment_applicant_work_experience::where('applicant_id', $applicant->id)->get();

        return response()->json([
            'applicant' => $applicant,
            'family' => $applicantFamily,
            'education' => $applicantEducation,
            'experience' => $applicantWorkExperience
        ]);
    }



    public function schedule_interview(Request $request, $id)
    {
        $request->validate([
            'interview_date' => 'required|date',
            'interview_note' => 'required|string',
            'updated_at' => now(),

        ]);

        $applicant = recruitment_applicant::findOrFail($id);

        $applicant->update([
            'interview_date' => $request->interview_date,
            'interview_note' => $request->interview_note,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Interview scheduled successfully']);
    }

    public function update_status(Request $request, $id)
    {
        // dd($id);
        //dd($request->all());
        $request->validate([
            'status' => 'required|in:Approved,Declined',
            'status_note' => 'required|string',
            'updated_at' => now(),

        ]);



        $applicant = recruitment_applicant::findOrFail($id);

        $applicant->update([
            'status_applicant' => $request->status,
            'status_note' => $request->status_note,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Status updated successfully']);
    }



    public function exchange_position(Request $request, $id)
    {
        //dd($request->all());
        $request->validate([
            'new_demand_id' => 'required'
        ]);

        $applicant = recruitment_applicant::findOrFail($request->applicant_id);

        $applicant->update([
            'recruitment_demand_id' => $request->new_demand_id,
            'exchange_note' => $request->exchange_reason,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Position exchanged successfully']);
    }


    public function get_exchange($id)
    {
        // dd($id);
        $applicant = recruitment_applicant::where('id', $id)->first();

        // Ambil posisi yang tersedia kecuali ID yang sedang dipilih
        $positions = recruitment_demand::where('qty_needed', '>', 0)
            ->where('status_demand', 'Approved')
            // ->where('id', '!=', $applicant->id)
            ->get();

        // Cek apakah data kosong
        if ($positions->isEmpty()) {
            return response()->json(['message' => 'Labor Demand not found'], 404);
        }

        return response()->json([
            'applicant' => $applicant,
            'positions' => $positions,
        ]);
    }

    public function add_to_employee($id)
    {
        try {
            $applicant = recruitment_applicant::where('id', $id)->first();
            $demand = recruitment_demand::where('id', $applicant->recruitment_demand_id)->first();

            // Get the latest employee ID and increment
            $lastEmployee = User::orderBy('id', 'desc')
                ->where('employee_id', 'like', 'emp_%')
                ->first();

            $newEmployeeNumber = 1;
            if ($lastEmployee) {
                $lastNumber = intval(substr($lastEmployee->employee_id, 4));
                $newEmployeeNumber = $lastNumber + 1;
            }
            $employeeId = 'emp_' . $newEmployeeNumber;

            // Handle file copying
            $photoProfilePath = null;
            $cvPath = null;
            $idCardPath = null;

            // Copy profile photo if exists
            if ($applicant->photo_profile_path && Storage::disk('public')->exists($applicant->photo_profile_path)) {
                $extension = pathinfo($applicant->photo_profile_path, PATHINFO_EXTENSION);
                $newPhotoPath = 'user/photos_profile/' . $employeeId . '.' . $extension;

                Storage::disk('public')->copy($applicant->photo_profile_path, $newPhotoPath);
                $photoProfilePath = $newPhotoPath;
            }

            // Copy CV if exists
            if ($applicant->cv_path && Storage::disk('public')->exists($applicant->cv_path)) {
                $extension = pathinfo($applicant->cv_path, PATHINFO_EXTENSION);
                $newCvPath = 'user/cv_user/cv_' . $employeeId . '.' . $extension;

                Storage::disk('public')->copy($applicant->cv_path, $newCvPath);
                $cvPath = $newCvPath;
            }

            // Copy ID Card if exists
            if ($applicant->ID_card_path && Storage::disk('public')->exists($applicant->ID_card_path)) {
                $extension = pathinfo($applicant->ID_card_path, PATHINFO_EXTENSION);
                $newIdCardPath = 'user/ID_card/ID_card_' . $employeeId . '.' . $extension;

                Storage::disk('public')->copy($applicant->ID_card_path, $newIdCardPath);
                $idCardPath = $newIdCardPath;
            }
            // dd($idCardPath);


            // Create new employee
            $employee = User::create([
                'employee_id' => $employeeId,
                'name' => $applicant->name,
                'position' => $demand->position,
                'department' => $demand->department,
                'email' => $applicant->email,
                'phone_number' => $applicant->phone_number,
                'employee_status' => $demand->status_job,
                'contract_start_date' => now(),
                'contract_end_date' => now()->addMonths($demand->length_of_working),
                'user_status' => 'Unbanned',
                'join_date' => now(),
                'ID_number' => $applicant->ID_number,
                'birth_date' => $applicant->birth_date,
                'birth_place' => $applicant->birth_place,
                'religion' => $applicant->religion,
                'gender' => $applicant->gender,
                'ID_address' => $applicant->ID_address,
                'domicile_address' => $applicant->domicile_address,
                'height' => $applicant->height,
                'weight' => $applicant->weight,
                'blood_type' => $applicant->blood_type,
                'bpjs_employment' => $applicant->bpjs_employment,
                'bpjs_health' => $applicant->bpjs_health,
                'photo_profile_path' => $photoProfilePath,
                'cv_path' => $cvPath,
                'ID_card_path' => $idCardPath,
                'password' => Hash::make($applicant->name . '12345'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Copy education records
            $educations = recruitment_applicant_education::where('applicant_id', $applicant->id)->get();
            foreach ($educations as $education) {
                users_education::create([
                    'users_id' => $employee->id,
                    'degree' => $education->degree,
                    'educational_place' => $education->educational_place,
                    'start_education' => $education->start_education,
                    'end_education' => $education->end_education,
                    'grade' => $education->grade,
                    'major' => $education->major
                ]);
            }

            // Copy family records
            $families = recruitment_applicant_family::where('applicant_id', $applicant->id)->get();
            foreach ($families as $family) {
                users_family::create([
                    'users_id' => $employee->id,
                    'name' => $family->name,
                    'relation' => $family->relation,
                    'birth_date' => $family->birth_date,
                    'birth_place' => $family->birth_place,
                    'ID_number' => $family->ID_number,
                    'phone_number' => $family->phone_number,
                    'address' => $family->address,
                    'gender' => $family->gender,
                    'job' => $family->job
                ]);
            }

            // Copy work experience records
            $experiences = recruitment_applicant_work_experience::where('applicant_id', $applicant->id)->get();
            foreach ($experiences as $experience) {
                users_work_experience::create([
                    'users_id' => $employee->id,
                    'company_name' => $experience->company_name,
                    'position' => $experience->position,
                    'start_working' => $experience->working_start,
                    'end_working' => $experience->working_end
                ]);
            }

            // Update demand quantities
            $demand->update([
                'qty_needed' => $demand->qty_needed - 1,
                'qty_fullfil' => $demand->qty_fullfil + 1,
                'updated_at' => now()
            ]);

            // Update demand quantities
            $applicant->update([
                'status_applicant' => 'Done',
                'updated_at' => now()
            ]);

            return response()->json(['message' => 'Employee added successfully']);
        } catch (\Exception $e) {
            // If there's an error, we should clean up any files that were copied
            if (isset($photoProfilePath) && Storage::disk('public')->exists($photoProfilePath)) {
                Storage::disk('public')->delete($photoProfilePath);
            }
            if (isset($cvPath) && Storage::disk('public')->exists($cvPath)) {
                Storage::disk('public')->delete($cvPath);
            }
            if (isset($idCardPath) && Storage::disk('public')->exists($idCardPath)) {
                Storage::disk('public')->delete($idCardPath);
            }

            return response()->json(['message' => 'Failed to add employee: ' . $e->getMessage()], 500);
        }
    }



    /**
     * Store a new job request
     */
    public function storeJobRequest(Request $request)
    {
        // Add validation and storage logic
    }

    /**
     * Store AHP calculation results
     */
    public function storeAhp(Request $request)
    {
        // Add validation and storage logic
    }

    /**
     * Store interview results
     */
    public function storeInterview(Request $request)
    {
        // Add validation and storage logic
    }

    /**
     * Update job request status
     */
    public function updateJobRequest(Request $request, $id)
    {
        // Add validation and update logic
    }

    /**
     * Update AHP recommendation
     */
    public function updateAhp(Request $request, $id)
    {
        // Add validation and update logic
    }

    /**
     * Update interview status
     */
    public function updateInterview(Request $request, $id)
    {
        // Add validation and update logic
    }
}
