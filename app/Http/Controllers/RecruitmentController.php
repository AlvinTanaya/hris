<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


use App\Models\recruitment_demand;
use App\Models\recruitment_applicant;
use App\Models\recruitment_applicant_education;
use App\Models\recruitment_applicant_training;
use App\Models\recruitment_applicant_family;
use App\Models\recruitment_applicant_work_experience;
use App\Models\recruitment_applicant_organization;
use App\Models\recruitment_applicant_language;
use App\Models\User;
use App\Models\users_education;
use App\Models\users_family;
use App\Models\users_work_experience;
use App\Models\users_language;
use App\Models\users_training;
use App\Models\users_organization;
use App\Models\Notification;
use App\Models\EmployeeDepartment;
use App\Models\EmployeePosition;

use App\Mail\InterviewScheduledMail;
use App\Mail\InterviewRescheduledMail;
use App\Mail\ApplicantStatusMail;
use App\Mail\PositionExchangedMail;
use App\Mail\AcceptedApplicantMail;
use App\Mail\NewEmployeeNotificationMail;
use App\Mail\LaborDemandCreate;
use App\Mail\LaborDemandUpdate;
use App\Mail\LaborDemandApproved;
use App\Mail\LaborDemandDeclined;
use App\Mail\LaborDemandRevised;

use Carbon\Carbon;

class RecruitmentController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('recruitment_demand')
            ->join('users', 'recruitment_demand.maker_id', '=', 'users.id')
            ->join('employee_departments', 'recruitment_demand.department_id', '=', 'employee_departments.id')
            ->join('employee_positions', 'recruitment_demand.position_id', '=', 'employee_positions.id')
            ->select(
                'users.name as maker_name',
                'recruitment_demand.*',
                'employee_departments.department as department_name',
                'employee_positions.position as position_name'
            );

        // Apply filters
        if ($request->filled('status_demand')) {
            $query->where('recruitment_demand.status_demand', $request->status_demand);
        }

        if ($request->filled('department_id')) {
            $query->where('recruitment_demand.department_id', $request->department_id);
        }

        if ($request->filled('position_id')) {
            $query->where('recruitment_demand.position_id', $request->position_id);
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

        // Get distinct values for dropdowns from the actual tables
        $departments = DB::table('employee_departments')->select('id', 'department')->get();
        $positions = DB::table('employee_positions')->select('id', 'position')->get();
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
        $departments = EmployeeDepartment::all();
        $positions = EmployeePosition::all();

        return view('recruitment/labor_demand/create', compact('departments', 'positions'));
    }

    public function edit_labor_demand($id)
    {
        $demand = recruitment_demand::with(['departmentRelation', 'positionRelation'])->findOrFail($id);
        $departments = EmployeeDepartment::all();
        $positions = EmployeePosition::all();

        return view('recruitment/labor_demand/update', compact('demand', 'departments', 'positions'));
    }
    public function approve_labor_demand($id)
    {
        $demand = recruitment_demand::with([
            'departmentRelation',
            'positionRelation',
            'maker'
        ])->findOrFail($id);

        // Update status
        $demand->status_demand = 'Approved';
        $demand->save();

        // Get names from relationships
        $positionName = $demand->positionRelation->position ?? 'Unknown Position';
        $departmentName = $demand->departmentRelation->department ?? 'Unknown Department';

        // Notification message
        $message = "Labor demand request {$demand->recruitment_demand_id} for position: {$positionName} in {$departmentName} department has been approved";

        if ($demand->maker) {
            // Send to maker
            Mail::to($demand->maker->email)->send(new LaborDemandApproved($demand));

            Notification::create([
                'users_id' => $demand->maker->id,
                'message' => $message,
                'type' => 'labor_demand_approved',
                'maker_id' => Auth::id(),
                'status' => 'Unread'
            ]);
        } else {
            // Send to HR department
            $hrDepartment = EmployeeDepartment::where('department', 'Human Resources')->first();

            if ($hrDepartment) {
                $hrUsers = User::where('department_id', $hrDepartment->id)->get();

                foreach ($hrUsers as $user) {
                    Mail::to($user->email)->send(new LaborDemandApproved($demand));

                    Notification::create([
                        'users_id' => $user->id,
                        'message' => $message,
                        'type' => 'labor_demand_approved',
                        'maker_id' => Auth::id(),
                        'status' => 'Unread'
                    ]);
                }
            }
        }

        return redirect()->route('recruitment.index')
            ->with('success', 'Job request has been approved successfully');
    }

    public function decline_labor_demand(Request $request, $id)
    {
        $demand = recruitment_demand::with([
            'departmentRelation',
            'positionRelation',
            'maker'
        ])->findOrFail($id);

        // Update status and reason
        $demand->status_demand = 'Declined';
        $demand->response = $request->response;
        $demand->save();

        // Get names from relationships
        $positionName = $demand->positionRelation->position ?? 'Unknown Position';

        // Notification message
        $message = "Labor demand request {$demand->recruitment_demand_id} for position: {$positionName} has been declined with reason: {$request->response}";

        if ($demand->maker) {
            // Send to maker
            Mail::to($demand->maker->email)->send(new LaborDemandDeclined($demand));

            Notification::create([
                'users_id' => $demand->maker->id,
                'message' => $message,
                'type' => 'labor_demand_declined',
                'maker_id' => Auth::id(),
                'status' => 'Unread'
            ]);
        } else {
            // Send to HR department
            $hrDepartment = EmployeeDepartment::where('department', 'Human Resources')->first();

            if ($hrDepartment) {
                $hrUsers = User::where('department_id', $hrDepartment->id)->get();

                foreach ($hrUsers as $user) {
                    Mail::to($user->email)->send(new LaborDemandDeclined($demand));

                    Notification::create([
                        'users_id' => $user->id,
                        'message' => $message,
                        'type' => 'labor_demand_declined',
                        'maker_id' => Auth::id(),
                        'status' => 'Unread'
                    ]);
                }
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('recruitment.index')
            ->with('success', 'Job request has been declined successfully');
    }

    public function revise_labor_demand(Request $request, $id)
    {
        $demand = recruitment_demand::with([
            'departmentRelation',
            'positionRelation',
            'maker'
        ])->findOrFail($id);

        // Update status and revision reason
        $demand->status_demand = 'Revised';
        $demand->response_reason = $request->revision_reason;
        $demand->save();

        // Get names from relationships
        $positionName = $demand->positionRelation->position ?? 'Unknown Position';

        // Notification message
        $message = "Labor demand request {$demand->recruitment_demand_id} for position: {$positionName} requires revision: {$request->revision_reason}";

        if ($demand->maker) {
            // Send to maker
            Mail::to($demand->maker->email)->send(new LaborDemandRevised($demand));

            Notification::create([
                'users_id' => $demand->maker->id,
                'message' => $message,
                'type' => 'labor_demand_revised',
                'maker_id' => Auth::id(),
                'status' => 'Unread'
            ]);
        } else {
            // Send to HR department
            $hrDepartment = EmployeeDepartment::where('department', 'Human Resources')->first();

            if ($hrDepartment) {
                $hrUsers = User::where('department_id', $hrDepartment->id)->get();

                foreach ($hrUsers as $user) {
                    Mail::to($user->email)->send(new LaborDemandRevised($demand));

                    Notification::create([
                        'users_id' => $user->id,
                        'message' => $message,
                        'type' => 'labor_demand_revised',
                        'maker_id' => Auth::id(),
                        'status' => 'Unread'
                    ]);
                }
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('recruitment.index')
            ->with('success', 'Job request has been sent for revision');
    }

    public function store_labor_demand(Request $request)
    {
        // Validasi input  
        $validated = $request->validate([
            'department_id' => 'required|exists:employee_departments,id',
            'position_id' => 'required|exists:employee_positions,id',
            'opening_date' => 'required|date',
            'closing_date' => 'required|date',
            'reason' => 'required|string|max:255',
            'qty_needed' => 'required|integer',
            'gender' => 'required|string|max:255',
            'job_goal' => 'required|string|max:255',
            'education' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'experience' => 'required|string|max:255',
            'status_job' => 'required|string|in:Full Time,Part Time,Contract',
            'length_of_working' => [
                'nullable',
                'required_if:status_job,Part Time,Contract',
                'integer',
            ],
            'skills' => 'required|string|max:255',
            'time_work_experience' => 'nullable|string|max:255',
        ]);

        // Tambahkan status dan informasi tambahan  
        $validated['status_demand'] = 'Pending';
        $validated['qty_fullfil'] = 0;
        $validated['created_at'] = now();
        $validated['updated_at'] = now();
        $validated['maker_id'] = $request->maker_id;

        // Generate unique ID
        $count = recruitment_demand::count();
        $validated['recruitment_demand_id'] = 'ptk_' . ($count + 1);

        // Format multi-line inputs
        $validated['reason'] = implode("\n", array_map('trim', explode("\n", $request->reason)));
        $validated['job_goal'] = implode("\n", array_map('trim', explode("\n", $request->job_goal)));
        $validated['experience'] = implode("\n", array_map('trim', explode("\n", $request->experience)));
        $validated['skills'] = implode("\n", array_map('trim', explode("\n", $request->skills)));

        // Simpan data ke database  
        $demand = recruitment_demand::create($validated);

        // Get department and position names for notification
        $departmentName = $demand->departmentRelation->department ?? 'Unknown Department';
        $positionName = $demand->positionRelation->position ?? 'Unknown Position';

        // Send email to General Managers
        $gmDepartment = EmployeeDepartment::where('department', 'General Manager')->first();
        $gmPosition = EmployeePosition::where('position', 'General Manager')->first();

        if ($gmDepartment && $gmPosition) {
            $gmUsers = User::where('department_id', $gmDepartment->id)
                ->where('position_id', $gmPosition->id)
                ->get();

            foreach ($gmUsers as $user) {
                Mail::to($user->email)->send(new LaborDemandCreate($demand));

                Notification::create([
                    'users_id' => $user->id,
                    'message' => "New labor demand request {$demand->recruitment_demand_id} for position: {$positionName} in {$departmentName} department. Please review and respond.",
                    'type' => 'labor_demand_created',
                    'maker_id' => $request->maker_id,
                    'status' => 'Unread'
                ]);
            }
        }

        return redirect()->route('recruitment.index')
            ->with('success', 'Job request has been created successfully');
    }


    public function update_labor_demand(Request $request, $id)
    {
        // Validasi input  
        $validated = $request->validate([
            'department_id' => 'required|exists:employee_departments,id',
            'position_id' => 'required|exists:employee_positions,id',
            'opening_date' => 'required|date',
            'closing_date' => 'required|date',
            'reason' => 'required|string|max:255',
            'qty_needed' => 'required|integer',
            'gender' => 'required|string|max:255',
            'job_goal' => 'required|string|max:255',
            'education' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'experience' => 'required|string|max:255',
            'status_job' => 'required|string|in:Full Time,Part Time,Contract',
            'length_of_working' => [
                'nullable',
                'required_if:status_job,Part Time,Contract',
                'integer',
            ],
            'skills' => 'required|string|max:255',
        ]);

        try {
            $demand = recruitment_demand::with(['departmentRelation', 'positionRelation'])->findOrFail($id);

            $validated['updated_at'] = now();
            $validated['maker_id'] = $request->maker_id;
            $validated['time_work_experience'] = $request->time_work_experience ?: null;
            $validated['status_demand'] = $demand->status_demand;
            $validated['qty_fullfil'] = $demand->qty_fullfil;
            $validated['recruitment_demand_id'] = $demand->recruitment_demand_id;

            $demand->update($validated);

            // Get department and position names for notification
            $departmentName = $demand->departmentRelation->department ?? 'Unknown Department';
            $positionName = $demand->positionRelation->position ?? 'Unknown Position';

            // Send email to General Managers
            $gmDepartment = EmployeeDepartment::where('department', 'General Manager')->first();
            $gmPosition = EmployeePosition::where('position', 'General Manager')->first();

            if ($gmDepartment && $gmPosition) {
                $gmUsers = User::where('department_id', $gmDepartment->id)
                    ->where('position_id', $gmPosition->id)
                    ->get();

                foreach ($gmUsers as $user) {
                    Mail::to($user->email)->send(new LaborDemandUpdate($demand));

                    Notification::create([
                        'users_id' => $user->id,
                        'message' => "Labor demand request {$demand->recruitment_demand_id} for position: {$positionName} has been updated. Please review the changes.",
                        'type' => 'labor_demand_updated',
                        'maker_id' => $request->maker_id,
                        'status' => 'Unread'
                    ]);
                }
            }

            return redirect()->route('recruitment.index')
                ->with('success', 'Job request has been updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update job request: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show_labor_demand($id)
    {
        $demand = recruitment_demand::with(['departmentRelation', 'positionRelation'])->find($id);

        if (!$demand) {
            return response()->json(['message' => 'Labor Demand not found'], 404);
        }

        return response()->json([
            'id' => $demand->id,
            'recruitment_demand_id' => $demand->recruitment_demand_id,
            'status_demand' => $demand->status_demand,
            'department_id' => $demand->department_id,
            'department_name' => $demand->departmentRelation->department ?? 'Unknown Department',
            'position_id' => $demand->position_id,
            'position_name' => $demand->positionRelation->position ?? 'Unknown Position',
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
            'response' => $demand->response,
            'skills' => $demand->skills,
        ]);
    }







    /**
     * Display AHP recommendation page
     */

    private $criteria = [
        'age' => 'Age',
        'expected_salary' => 'Expected Salary',
        'distance' => 'Distance',
        'education' => 'Education',
        'experience_duration' => 'Experience',
        'language' => 'Language',
        'organization' => 'Organization',
        'training' => 'Training',
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

    public function ahp_schedule_interview(Request $request, $id)
    {
        $request->validate([
            'interview_date' => 'required|date',
            'interview_note' => 'required|string',
        ]);

        $applicant = recruitment_applicant::findOrFail($id);

        // Store old interview date to check if this is the first interview
        $oldInterviewDate = $applicant->interview_date;

        $applicant->update([
            'interview_date' => $request->interview_date,
            'interview_note' => str_replace("\r\n", "\n", $request->interview_note),
            'updated_at' => now(),
        ]);

        // Jika sebelumnya NULL, berarti panggilan pertama
        if (is_null($oldInterviewDate)) {
            Mail::to($applicant->email)->send(new InterviewScheduledMail($applicant));
        }

        return response()->json(['message' => 'Interview scheduled successfully']);
    }


    public function calculate(Request $request)
    {
        // Remove debugging statement
        // dd($request->all());

        try {
            // Validate the request - basic validation first
            $validationRules = [
                'demandId' => 'required',
            ];

            // Only validate criteria that are included in the request
            $totalPercentage = 0;
            $criteriaKeys = [
                'age',
                'expected_salary',
                'distance',
                'education',
                'experience_duration',
                'organization',
                'language',
                'training'
            ];

            foreach ($criteriaKeys as $key) {
                if ($request->has($key)) {
                    $validationRules[$key] = 'required|numeric|min:0|max:100';
                    $totalPercentage += (float)$request->$key;
                }
            }

            $request->validate($validationRules);

            // Validate that percentages sum to 100%
            if (abs($totalPercentage - 100) > 0.01) { // Allow small floating point error
                return response()->json([
                    'success' => false,
                    'message' => 'Total persentase harus 100%'
                ], 422);
            }

            // Get configurations from request
            $criteriaConfigs = $request->criteria_configs ?? [];

            // Convert percentages to weights (0-1 scale)
            $weights = [];
            foreach ($criteriaKeys as $key) {
                if ($request->has($key)) {
                    $weights[$key] = $request->$key / 100;
                }
            }

            // Get applicants data
            $demand = recruitment_demand::findOrFail($request->demandId);
            $applicants = recruitment_applicant::where('recruitment_demand_id', $demand->id)
                ->where('status_applicant', 'Pending')
                ->whereNull('interview_date')->get();

            if ($applicants->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada pelamar untuk demand ini'
                ], 200);
            }

            $rankings = $this->calculateApplicantScores($applicants, $weights, $criteriaConfigs);

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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateApplicantScores($applicants, $weights, $criteriaConfigs)
    {
        $scores = [];

        foreach ($applicants as $applicant) {
            $criteriaScores = [];

            // Only calculate scores for criteria that have weights
            foreach ($weights as $criterion => $weight) {
                switch ($criterion) {
                    case 'age':
                        $criteriaScores['age'] = $this->calculateAgeScore(
                            $applicant->birth_date,
                            $criteriaConfigs['age'] ?? null
                        );
                        break;
                    case 'expected_salary':
                        $criteriaScores['expected_salary'] = $this->calculateExpectedSalaryScore(
                            $applicant->expected_salary ?? 0,
                            $criteriaConfigs['expected_salary'] ?? null
                        );
                        break;
                    case 'distance':
                        $criteriaScores['distance'] = $this->calculateDistanceScore(
                            $applicant->distance ?? 0,
                            $criteriaConfigs['distance'] ?? null
                        );
                        break;
                    case 'education':
                        $education = recruitment_applicant_education::where('applicant_id', $applicant->id)
                            ->orderBy('end_education', 'desc')
                            ->first();
                        $criteriaScores['education'] = $this->calculateEducationScore(
                            $education,
                            $criteriaConfigs['education'] ?? null
                        );
                        break;
                    case 'experience_duration':
                        $criteriaScores['experience_duration'] = $this->calculateExperienceDurationScore(
                            $applicant->id,
                            $criteriaConfigs['experience_duration'] ?? null
                        );
                        break;
                    case 'training':
                        $criteriaScores['training'] = $this->calculateTrainingScore(
                            $applicant->id,
                            $criteriaConfigs['training'] ?? null
                        );
                        break;
                    case 'language':
                        $criteriaScores['language'] = $this->calculateLanguageScore(
                            $applicant->id,
                            $criteriaConfigs['language'] ?? null
                        );
                        break;
                    case 'organization':
                        $criteriaScores['organization'] = $this->calculateOrganizationScore(
                            $applicant->id,
                            $criteriaConfigs['organization'] ?? null
                        );
                        break;
                }
            }

            // Calculate weighted sum
            $totalScore = 0;
            foreach ($weights as $criterion => $weight) {
                if (isset($criteriaScores[$criterion])) {
                    $totalScore += $criteriaScores[$criterion] * $weight;
                }
            }

            $scores[] = [
                'applicant' => $applicant,
                'score' => $totalScore,
                'breakdown' => $criteriaScores
            ];
        }

        return collect($scores)->sortByDesc('score')->values();
    }

    // New helper function to handle range calculations consistently
    public function getScoreFromRanges($value, $ranges)
    {
        // Tambahkan debugging untuk tipe data
        //dd(gettype($value), $value, $ranges);

        if (empty($ranges)) {
            return 0;
        }

        // Sort ranges by rank
        $ranges = collect($ranges)->sortBy('rank')->values()->all();
        $totalRanges = count($ranges);

        foreach ($ranges as $range) {
            // Konversi eksplisit ke float jika perlu
            $min = floatval($range['min']);
            $max = floatval($range['max']);
            $value = floatval($value);

            // Untuk semua range, gunakan upper bound inklusif
            if ($value >= $min && $value <= $max) {
                // dd($min, $value, $max, $range['rank'], $totalRanges);
                return 1 - (($range['rank'] - 1) / $totalRanges);
            }
        }

        // Value is outside all defined ranges
        return 1 / ($totalRanges + 1);
    }

    private function calculateAgeScore($birthDate, $config = null)
    {
        // Return 0 if birthDate is empty, null, or invalid
        if (empty($birthDate) || !Carbon::hasFormat($birthDate, 'Y-m-d')) {
            return 0;
        }

        $age = Carbon::parse($birthDate)->age;

        // Return 0 if age calculation results in 0 or negative
        if ($age <= 0) {
            return 0;
        }

        // If no config is provided or ranges are empty, use default calculation
        if (empty($config) || empty($config['ranges'])) {
            return match (true) {
                $age <= 17 => 1.0,  // Very young, highest score
                $age <= 25 => 0.9,
                $age <= 30 => 0.8,
                $age <= 35 => 0.7,
                $age <= 40 => 0.6,
                $age <= 50 => 0.5,
                $age <= 60 => 0.4,
                $age <= 70 => 0.3,
                $age <= 80 => 0.2,
                $age <= 90 => 0.1,
                default => 0
            };
        }

        return $this->getScoreFromRanges($age, $config['ranges']);
    }

    private function calculateExpectedSalaryScore($salary, $config = null)
    {
        // Return 0 if salary is empty or invalid
        if (empty($salary) || !is_numeric($salary)) {
            return 0;
        }

        // If no config is provided or ranges are empty, use default calculation
        if (empty($config) || empty($config['ranges'])) {
            return match (true) {
                $salary >= 5000001 && $salary <= 8000000 => 1.0,  // Best range
                $salary >= 2000001 && $salary <= 5000000 => 0.8,
                $salary >= 8000001 && $salary <= 10000000 => 0.6,
                $salary >= 0 && $salary <= 2000000 => 0.4,
                $salary >= 10000001 && $salary <= 15000000 => 0.2,
                default => 0
            };
        }

        return $this->getScoreFromRanges($salary, $config['ranges']);
    }

    private function calculateDistanceScore($distance, $config = null)
    {
        // Return 0 if distance is empty or invalid
        if (empty($distance) || !is_numeric($distance)) {
            return 0;
        }

        // If no config is provided or ranges are empty, use default calculation
        if (empty($config) || empty($config['ranges'])) {
            return match (true) {
                $distance >= 0 && $distance <= 3 => 1.0,  // Best range (closest)
                $distance >= 3.1 && $distance <= 5 => 0.75,
                $distance >= 5.1 && $distance <= 8 => 0.5,
                $distance >= 10.1 && $distance <= 15 => 0.25,
                default => 0
            };
        }

        return $this->getScoreFromRanges($distance, $config['ranges']);
    }


    private function calculateEducationScore($education, $config = null)
    {
        if (!$education) {
            return 0; // No education data
        }

        // Equal intervals for 5 levels (0.2 increment each)
        $defaultLevelScores = [
            'SMA' => 0.2,  // 20%
            'SMK' => 0.4,  // 40%
            'D3' => 0.6,   // 60%
            'S1' => 0.8,   // 80%
            'S2' => 1.0    // 100%
        ];

        $levelScore = 0;
        $gradeScore = 0;
        $levelWeight = 0.7; // 70% weight for level
        $gradeWeight = 0.3; // 30% weight for grade

        // Use configuration if available
        if ($config && isset($config['levels']['list']) && !empty($config['levels']['list'])) {
            $levels = collect($config['levels']['list'])
                ->whereIn('name', array_keys($defaultLevelScores))
                ->sortBy('rank')
                ->values()
                ->all();

            $totalLevels = count($levels);

            // Find the education level in configured levels
            foreach ($levels as $level) {
                if ($level['name'] === $education->degree) {
                    $levelScore = 1 - (($level['rank'] - 1) / $totalLevels);
                    break;
                }
            }

            // If not found in configured levels, use default or zero
            if ($levelScore === 0) {
                $levelScore = $defaultLevelScores[$education->degree] ?? 0;
            }

            // Override weights if configured
            if (isset($config['weights'])) {
                $levelWeight = $config['weights']['level'] / 100;
                $gradeWeight = $config['weights']['grade'] / 100;
            }
        } else {
            // Use default level scores
            $levelScore = $defaultLevelScores[$education->degree] ?? 0;
        }

        // Calculate grade score (same as before)
        if ($education->grade !== null) {
            if (in_array($education->degree, ['SMK', 'SMA'])) {
                $gradeScore = min(1, max(0, $education->grade / 100));
            } else {
                $gradeScore = min(1, max(0, $education->grade / 4));
            }
        }

        // Combined weighted score
        return ($levelScore * $levelWeight) + ($gradeScore * $gradeWeight);
    }

    private function calculateExperienceDurationScore($applicantId, $config = null)
    {
        $experiences = recruitment_applicant_work_experience::where('applicant_id', $applicantId)->get();

        if ($experiences->isEmpty()) {
            return 0; // Default low score for no experience
        }

        $totalDuration = 0;
        $count = $experiences->count();

        foreach ($experiences as $exp) {
            $startDate = Carbon::parse($exp->working_start);
            $endDate = $exp->working_end ? Carbon::parse($exp->working_end) : Carbon::now();
            $totalDuration += $startDate->diffInYears($endDate);
        }

        // Default configuration if none provided
        if (empty($config)) {
            $periodRanges = [
                ['min' => 5, 'max' => 8, 'rank' => 1],
                ['min' => 3, 'max' => 5, 'rank' => 2],
                ['min' => 1, 'max' => 3, 'rank' => 3],
                ['min' => 0, 'max' => 1, 'rank' => 4]
            ];

            $amountRanges = [
                ['min' => 6, 'max' => 7, 'rank' => 1],
                ['min' => 4, 'max' => 5, 'rank' => 2],
                ['min' => 2, 'max' => 3, 'rank' => 3],
                ['min' => 0, 'max' => 1, 'rank' => 4]
            ];

            $periodScore = $this->getScoreFromRanges($totalDuration, $periodRanges);
            $amountScore = $this->getScoreFromRanges($count, $amountRanges);

            return ($periodScore * 0.7) + ($amountScore * 0.3);
        }

        // Use provided configuration
        $periodScore = isset($config['period']) ? $this->getScoreFromRanges($totalDuration, $config['period']) : 0;
        $amountScore = isset($config['amount']) ? $this->getScoreFromRanges($count, $config['amount']) : 0;

        $periodWeight = isset($config['weights']['period']) ? ($config['weights']['period'] / 100) : 0.7;
        $amountWeight = isset($config['weights']['amount']) ? ($config['weights']['amount'] / 100) : 0.3;

        return ($periodScore * $periodWeight) + ($amountScore * $amountWeight);
    }

    private function calculateTrainingScore($applicantId, $config = null)
    {
        $trainings = recruitment_applicant_training::where('applicant_id', $applicantId)->get();

        if ($trainings->isEmpty()) {
            return 0;
        }

        $count = $trainings->count();
        $totalDuration = 0;

        foreach ($trainings as $training) {
            if ($training->start_date && $training->end_date) {
                $startDate = Carbon::parse($training->start_date);
                $endDate = Carbon::parse($training->end_date);
                $totalDuration += $startDate->diffInMonths($endDate);
            }
        }

        // Convert duration to years for scoring
        $durationInYears = $totalDuration / 12;

        // Default configuration if none provided
        if (empty($config)) {
            $periodRanges = [
                ['min' => 5.1, 'max' => 8, 'rank' => 1],
                ['min' => 3.1, 'max' => 5, 'rank' => 2],
                ['min' => 1.1, 'max' => 3, 'rank' => 3],
                ['min' => 0, 'max' => 1, 'rank' => 4]
            ];

            $amountRanges = [
                ['min' => 6, 'max' => 7, 'rank' => 1],
                ['min' => 4, 'max' => 5, 'rank' => 2],
                ['min' => 2, 'max' => 3, 'rank' => 3],
                ['min' => 0, 'max' => 1, 'rank' => 4]
            ];

            $periodScore = $this->getScoreFromRanges($durationInYears, $periodRanges);
            $amountScore = $this->getScoreFromRanges($count, $amountRanges);

            return ($periodScore * 0.7) + ($amountScore * 0.3);
        }

        // Use provided configuration
        $periodScore = isset($config['period']) ? $this->getScoreFromRanges($durationInYears, $config['period']) : 0;
        $amountScore = isset($config['amount']) ? $this->getScoreFromRanges($count, $config['amount']) : 0;

        $periodWeight = isset($config['weights']['period']) ? ($config['weights']['period'] / 100) : 0.7;
        $amountWeight = isset($config['weights']['amount']) ? ($config['weights']['amount'] / 100) : 0.3;

        return ($periodScore * $periodWeight) + ($amountScore * $amountWeight);
    }

    private function calculateOrganizationScore($applicantId, $config = null)
    {
        $organizations = recruitment_applicant_organization::where('applicant_id', $applicantId)->get();

        if ($organizations->isEmpty()) {
            return 0;
        }

        $count = $organizations->count();
        $totalDuration = 0;

        foreach ($organizations as $org) {
            if ($org->start_date) {
                $startDate = Carbon::parse($org->start_date);
                $endDate = $org->end_date ? Carbon::parse($org->end_date) : Carbon::now();
                $totalDuration += $startDate->diffInMonths($endDate);
            }
        }

        // Convert duration to years for scoring
        $durationInYears = $totalDuration / 12;

        // Default configuration if none provided
        if (empty($config)) {
            $periodRanges = [
                ['min' => 5.1, 'max' => 8, 'rank' => 1],
                ['min' => 3.1, 'max' => 5, 'rank' => 2],
                ['min' => 1.1, 'max' => 3, 'rank' => 3],
                ['min' => 0, 'max' => 1, 'rank' => 4]
            ];

            $amountRanges = [
                ['min' => 6, 'max' => 7, 'rank' => 1],
                ['min' => 4, 'max' => 5, 'rank' => 2],
                ['min' => 2, 'max' => 3, 'rank' => 3],
                ['min' => 0, 'max' => 1, 'rank' => 4]
            ];

            $periodScore = $this->getScoreFromRanges($durationInYears, $periodRanges);
            $amountScore = $this->getScoreFromRanges($count, $amountRanges);

            return ($periodScore * 0.4) + ($amountScore * 0.6);
        }

        // Use provided configuration
        $periodScore = isset($config['period']) ? $this->getScoreFromRanges($durationInYears, $config['period']) : 0;
        $amountScore = isset($config['amount']) ? $this->getScoreFromRanges($count, $config['amount']) : 0;

        $periodWeight = isset($config['weights']['period']) ? ($config['weights']['period'] / 100) : 0.4;
        $amountWeight = isset($config['weights']['amount']) ? ($config['weights']['amount'] / 100) : 0.6;

        return ($periodScore * $periodWeight) + ($amountScore * $amountWeight);
    }


    private function calculateLanguageScore($applicantId, $config = null)
    {
        $languages = recruitment_applicant_language::where('applicant_id', $applicantId)->get();

        if ($languages->isEmpty()) {
            return 0; // Default score for no languages
        }

        $verbalWeight = 0.7; // Default 70% weight for verbal skills
        $writtenWeight = 0.3; // Default 30% weight for written skills

        // Override weights if configured
        if ($config && isset($config['weights'])) {
            $verbalWeight = $config['weights']['verbal'] / 100;
            $writtenWeight = $config['weights']['written'] / 100;
        }

        $score = 0;
        $count = 0;

        foreach ($languages as $language) {
            $verbalScore = ($language->verbal === 'Active') ? 1 : 0.5;
            $writtenScore = ($language->written === 'Active') ? 1 : 0.5;

            // Weighted score for this language
            $languageScore = ($verbalScore * $verbalWeight) + ($writtenScore * $writtenWeight);
            $score += $languageScore;
            $count++;
        }

        // Return average language score
        return $count > 0 ? min(1.0, $score / $count) : 0.2;
    }

    public function index_interview(Request $request)
    {
        $query = DB::table('recruitment_demand')
            ->join('users', 'recruitment_demand.maker_id', '=', 'users.id')
            ->join('employee_departments', 'recruitment_demand.department_id', '=', 'employee_departments.id')
            ->join('employee_positions', 'recruitment_demand.position_id', '=', 'employee_positions.id')
            ->where('qty_needed', '>', 0)
            ->where('status_demand', 'Approved')
            ->select(
                'users.name as maker_name',
                'recruitment_demand.*',
                'employee_departments.department as department_name',
                'employee_positions.position as position_name'
            );

        // Apply filters
        if ($request->filled('status_demand')) {
            $query->where('recruitment_demand.status_demand', $request->status_demand);
        }

        if ($request->filled('department_id')) {
            $query->where('recruitment_demand.department_id', $request->department_id);
        }

        if ($request->filled('position_id')) {
            $query->where('recruitment_demand.position_id', $request->position_id);
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

        // Get distinct values for dropdowns from actual tables
        $departments = EmployeeDepartment::whereHas('demands', function ($q) {
            $q->where('qty_needed', '>', 0)
                ->where('status_demand', 'Approved');
        })
            ->select('id', 'department')
            ->get();

        $positions = EmployeePosition::whereHas('demands', function ($q) {
            $q->where('qty_needed', '>', 0)
                ->where('status_demand', 'Approved');
        })
            ->select('id', 'position')
            ->get();

        $jobStatuses = DB::table('recruitment_demand')
            ->where('qty_needed', '>', 0)
            ->where('status_demand', 'Approved')
            ->distinct()
            ->pluck('status_job');

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
        $applicantTraining = recruitment_applicant_training::where('applicant_id', $applicant->id)->get();
        $applicantLanguage = recruitment_applicant_language::where('applicant_id', $applicant->id)->get();
        $applicantOrganization = recruitment_applicant_organization::where('applicant_id', $applicant->id)->get();


        return response()->json([
            'applicant' => $applicant,
            'family' => $applicantFamily,
            'education' => $applicantEducation,
            'experience' => $applicantWorkExperience,
            'training' => $applicantTraining,
            'language' => $applicantLanguage,
            'organization' => $applicantOrganization,
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

        $oldInterviewDate = $applicant->interview_date; // Ambil tanggal sebelumnya

        $applicant->update([
            'interview_date' => $request->interview_date,
            'interview_note' => str_replace("\r\n", "\n", $request->interview_note),
            'updated_at' => now(),
        ]);

        // Jika sebelumnya NULL, berarti panggilan pertama
        if (is_null($oldInterviewDate)) {
            Mail::to($applicant->email)->send(new InterviewScheduledMail($applicant));
        } else {
            Mail::to($applicant->email)->send(new InterviewRescheduledMail($applicant, $oldInterviewDate));
        }

        return response()->json(['message' => 'Interview scheduled successfully']);
    }


    public function update_status(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Declined',
            'status_note' => 'required|string',
        ]);

        $applicant = recruitment_applicant::findOrFail($id);

        // Tentukan pesan berdasarkan status
        if ($request->status == 'Declined') {
            $messageContent = "We regret to inform you that you did not pass the interview. We appreciate your time and effort. We hope there will be other opportunities for you in the future.";
        } else {
            $messageContent = "Congratulations! You have passed the interview. Please wait for further information from us regarding the next steps.";
        }

        // Update database
        $applicant->update([
            'status_applicant' => $request->status,
            'status_note' => $request->status_note,
            'updated_at' => now(),
        ]);

        // Kirim email ke applicant
        Mail::to($applicant->email)->send(new ApplicantStatusMail($applicant, $messageContent));

        return response()->json(['message' => 'Status updated and email sent successfully']);
    }

    public function exchange_position(Request $request, $id)
    {
        $request->validate([
            'new_demand_id' => 'required'
        ]);

        $applicant = recruitment_applicant::findOrFail($request->applicant_id);

        // Get new position data with relationships
        $newDemand = recruitment_demand::with(['departmentRelation', 'positionRelation'])
            ->findOrFail($request->new_demand_id);

        // Update database
        $applicant->update([
            'recruitment_demand_id' => $request->new_demand_id,
            'exchange_note' => $request->exchange_reason,
            'updated_at' => now(),
        ]);

        // Send email with proper department and position names
        Mail::to($applicant->email)->send(new PositionExchangedMail(
            $applicant,
            $newDemand->positionRelation->position ?? 'Unknown Position',
            $newDemand->departmentRelation->department ?? 'Unknown Department'
        ));

        return response()->json(['message' => 'Position exchanged and email sent successfully']);
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

    public function add_to_employee(Request $request, $id)
    {
        try {
            $applicant = recruitment_applicant::findOrFail($id);
            $demand = recruitment_demand::with(['departmentRelation', 'positionRelation'])
                ->findOrFail($applicant->recruitment_demand_id);
    
            // Get department and position names from relationships
            $departmentName = $demand->departmentRelation->department ?? 'Unknown Department';
            $positionName = $demand->positionRelation->position ?? 'Unknown Position';
    
            // Ambil join_date dari request
            $joinDate = $request->join_date ? Carbon::parse($request->join_date) : now();
            $yearMonth = $joinDate->format('Ym'); // Format YYYYMM
    
            // Cari employee terakhir di bulan tersebut
            $lastEmployee = User::where('employee_id', 'like', "{$yearMonth}%")
                ->orderBy('employee_id', 'desc')
                ->first();
    
            // Tentukan nomor urut
            $newEmployeeNumber = 1;
            if ($lastEmployee) {
                $lastNumber = intval(substr($lastEmployee->employee_id, -3)); // Ambil 3 angka terakhir
                $newEmployeeNumber = $lastNumber + 1;
            }
            $employeeId = $yearMonth . str_pad($newEmployeeNumber, 3, '0', STR_PAD_LEFT);
    
            // Handle file copying
            $photoProfilePath = $this->copyFile($applicant->photo_profile_path, "user/photos_profile/{$employeeId}");
            $cvPath = $this->copyFile($applicant->cv_path, "user/cv_user/cv_{$employeeId}");
            $idCardPath = $this->copyFile($applicant->ID_card_path, "user/ID_card/ID_card_{$employeeId}");
            $achievementPath = $this->copyFile($applicant->achievement_path, "user/achievement_user/achievement_{$employeeId}");
    
            // Create new employee
            $employee = User::create([
                'employee_id' => $employeeId,
                'name' => $applicant->name,
                'position_id' => $demand->position_id,
                'department_id' => $demand->department_id,
                'email' => $applicant->email,
                'phone_number' => $applicant->phone_number,
                'employee_status' => $demand->status_job,
                'contract_start_date' => now(),
                'contract_end_date' => now()->addMonths($demand->length_of_working),
                'user_status' => 'Unbanned',
                'join_date' => $joinDate,
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
                'sim' => $applicant->sim,
                'distance' => $applicant->distance,
                'emergency_contact' => $applicant->emergency_contact,
                'sim_number' => $applicant->sim_number,
                'photo_profile_path' => $photoProfilePath,
                'cv_path' => $cvPath,
                'achievement_path' => $achievementPath,
                'ID_card_path' => $idCardPath,
                'password' => Hash::make($applicant->name . '12345'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
    
            // Copy additional data
            $this->copyEducation($applicant->id, $employee->id);
            $this->copyFamily($applicant->id, $employee->id);
            $this->copyWorkExperience($applicant->id, $employee->id);
            $this->copyLanguages($applicant->id, $employee->id);
            $this->copyTraining($applicant->id, $employee->id);
            $this->copyOrganization($applicant->id, $employee->id);
    
            // Update demand quantities
            $demand->decrement('qty_needed');
            $demand->increment('qty_fullfil');
    
            // Update applicant status
            $applicant->update(['status_applicant' => 'Done']);
    
            // **KIRIM EMAIL KE PESERTA**
            Mail::to($applicant->email)->send(new AcceptedApplicantMail(
                $applicant,
                $positionName,
                $departmentName,
                $joinDate->toFormattedDateString()
            ));
    
            // Ambil semua email user KECUALI email peserta yang baru diterima
            $userEmails = User::where('email', '!=', $applicant->email)
                ->pluck('email')
                ->toArray();
    
            // Kirim email ke semua user (kecuali peserta yang baru diterima)
            Mail::to($userEmails)->send(new NewEmployeeNotificationMail($employee));
    
            return response()->json(['message' => 'Employee added successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to add employee: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Copy file from one location to another
     */
    private function copyFile($sourcePath, $destinationPath)
    {
        if ($sourcePath && Storage::disk('public')->exists($sourcePath)) {
            $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
            $newPath = $destinationPath . '.' . $extension;

            Storage::disk('public')->copy($sourcePath, $newPath);
            return $newPath;
        }
        return null;
    }

    /**
     * Copy applicant education data to user education
     */
    private function copyEducation($applicantId, $userId)
    {
        $educations = recruitment_applicant_education::where('applicant_id', $applicantId)->get();
        foreach ($educations as $education) {
            users_education::create([
                'users_id' => $userId,
                'degree' => $education->degree,
                'educational_place' => $education->educational_place,
                'educational_city' => $education->educational_city,
                'educational_province' => $education->educational_province,
                'start_education' => $education->start_education,
                'end_education' => $education->end_education,
                'grade' => $education->grade,
                'major' => $education->major,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Copy applicant family data to user family
     */
    private function copyFamily($applicantId, $userId)
    {
        $families = recruitment_applicant_family::where('applicant_id', $applicantId)->get();
        foreach ($families as $family) {
            users_family::create([
                'users_id' => $userId,
                'name' => $family->name,
                'relation' => $family->relation,
                'birth_date' => $family->birth_date,
                'birth_place' => $family->birth_place,
                'ID_number' => $family->ID_number,
                'phone_number' => $family->phone_number,
                'address' => $family->address,
                'gender' => $family->gender,
                'job' => $family->job,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Copy applicant work experience data to user work experience
     */
    private function copyWorkExperience($applicantId, $userId)
    {
        $experiences = recruitment_applicant_work_experience::where('applicant_id', $applicantId)->get();
        foreach ($experiences as $experience) {
            users_work_experience::create([
                'users_id' => $userId,
                'company_name' => $experience->company_name,
                'position' => $experience->position,
                'start_working' => $experience->working_start,
                'end_working' => $experience->working_end,
                'company_address' => $experience->company_address,
                'company_phone' => $experience->company_phone,
                'salary' => $experience->salary,
                'supervisor_name' => $experience->supervisor_name,
                'supervisor_phone' => $experience->supervisor_phone,
                'job_desc' => $experience->job_desc,
                'reason' => $experience->reason,
                'benefit' => $experience->benefit,
                'facility' => $experience->facility,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Copy applicant languages to user languages
     */
    private function copyLanguages($applicantId, $userId)
    {
        $languages = recruitment_applicant_language::where('applicant_id', $applicantId)->get();
        foreach ($languages as $language) {
            users_language::create([
                'users_id' => $userId,
                'language' => $language->language,
                'verbal' => $language->verbal,
                'written' => $language->written,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Copy applicant training to user training
     */
    private function copyTraining($applicantId, $userId)
    {
        $trainings = recruitment_applicant_training::where('applicant_id', $applicantId)->get();
        foreach ($trainings as $training) {
            users_training::create([
                'users_id' => $userId,
                'training_name' => $training->training_name,
                'training_city' => $training->training_city,
                'training_province' => $training->training_province,
                'start_date' => $training->start_date,
                'end_date' => $training->end_date,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Copy applicant organization to user organization
     */
    private function copyOrganization($applicantId, $userId)
    {
        $organizations = recruitment_applicant_organization::where('applicant_id', $applicantId)->get();
        foreach ($organizations as $organization) {
            users_organization::create([
                'users_id' => $userId,
                'organization_name' => $organization->organization_name,
                'position' => $organization->position,
                'city' => $organization->city,
                'province' => $organization->province,
                'activity_type' => $organization->activity_type,
                'start_date' => $organization->start_date,
                'end_date' => $organization->end_date,
                'created_at' => now(),
                'updated_at' => now()
            ]);
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
