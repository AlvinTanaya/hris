<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
            ->leftJoin('users as maker', 'recruitment_demand.maker_id', '=', 'maker.id')
            ->leftJoin('users as responder', 'recruitment_demand.response_id', '=', 'responder.id')
            ->leftJoin('employee_departments', 'recruitment_demand.department_id', '=', 'employee_departments.id')
            ->leftJoin('employee_positions', 'recruitment_demand.position_id', '=', 'employee_positions.id')
            ->select(
                'recruitment_demand.id',
                'recruitment_demand.recruitment_demand_id',
                'recruitment_demand.status_demand',
                'recruitment_demand.department_id',
                'recruitment_demand.position_id',
                'recruitment_demand.opening_date',
                'recruitment_demand.closing_date',
                'recruitment_demand.status_job',
                'recruitment_demand.qty_needed',
                'recruitment_demand.qty_fullfil',
                'recruitment_demand.response_reason',
                'maker.name as maker_name',
                'responder.name as responder_name',
                'employee_departments.department as department_name',
                'employee_positions.position as position_name'
            );



        // Apply filters (keep your existing filter code)
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

        // Get distinct values for dropdowns
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

    public function show_labor_demand($id)
    {
        $demand = recruitment_demand::with([
            'departmentRelation',
            'positionRelation',
            'responder' // Eager load the responder relationship
        ])->find($id);

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
            'response_id' => $demand->response_id,
            'response_reason' => $demand->response_reason,
            'skills' => $demand->skills,
            'responder_name' => $demand->responder->name ?? 'N/A' // Add this line
        ]);
    }

    public function create_labor_demand()
    {
        $departments = EmployeeDepartment::all();
        $positions = EmployeePosition::all();

        return view('recruitment/labor_demand/create', compact('departments', 'positions'));
    }


    public function edit_labor_demand($id)
    {
        // Use eager loading to ensure relationships are loaded
        $demand = recruitment_demand::with(['departmentRelation', 'positionRelation'])->findOrFail($id);

        // dd($demand->department_id);

        $departments = EmployeeDepartment::all();
        $positions = EmployeePosition::all();

        return view('recruitment/labor_demand/update', compact('demand', 'departments', 'positions'));
    }
    public function approve_labor_demand($id)
    {
        $demand = recruitment_demand::with([
            'departmentRelation',
            'positionRelation',
            'maker',
            'responder'
        ])->findOrFail($id);

        // Update status
        $demand->status_demand = 'Approved';
        $demand->response_id = Auth::user()->id;
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
            'maker',
            'responder'
        ])->findOrFail($id);

        // Update status and reason
        $demand->status_demand = 'Declined';
        $demand->response_reason = $request->response_reason;
        $demand->response_id = Auth::user()->id;
        $demand->save();

        // Get names from relationships
        $positionName = $demand->positionRelation->position ?? 'Unknown Position';

        // Notification message
        $message = "Labor demand request {$demand->recruitment_demand_id} for position: {$positionName} has been declined with reason: {$request->response_reason}";

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
            'maker',
            'responder'
        ])->findOrFail($id);

        // Update status and revision reason
        $demand->status_demand = 'Revised';
        $demand->response_reason = $request->revision_reason;
        $demand->response_id = Auth::user()->id;
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


        // dd($request->all());

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
        $demands =  recruitment_demand::with(['departmentRelation', 'positionRelation'])->whereIn('id', $recruitmentDemandIds)
            ->where('status_demand', 'Approved')
            ->whereColumn('qty_needed', '>', 'qty_fullfil')
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






    private function calculateExperienceDurationScore($applicantId, $config = null)
    {
        // Ensure the applicantId is valid
        if (empty($applicantId) || !is_numeric($applicantId)) {
            return 0;
        }

        $experiences = recruitment_applicant_work_experience::where('applicant_id', $applicantId)->get();

        if ($experiences->isEmpty()) {
            return 0; // Default low score for no experience
        }

        $totalDuration = 0;
        $count = $experiences->count();

        foreach ($experiences as $exp) {
            if (empty($exp->working_start)) {
                continue; // Skip invalid entries
            }

            try {
                $startDate = Carbon::parse($exp->working_start);
                $endDate = $exp->working_end ? Carbon::parse($exp->working_end) : Carbon::now();
                $totalDuration += $startDate->diffInYears($endDate);
            } catch (\Exception $e) {
                // Skip this entry if date parsing fails
                continue;
            }
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
        // Ensure the applicantId is valid
        if (empty($applicantId) || !is_numeric($applicantId)) {
            return 0;
        }

        $trainings = recruitment_applicant_training::where('applicant_id', $applicantId)->get();

        if ($trainings->isEmpty()) {
            return 0;
        }

        $count = $trainings->count();
        $totalDuration = 0;

        foreach ($trainings as $training) {
            if ($training->start_date && $training->end_date) {
                try {
                    $startDate = Carbon::parse($training->start_date);
                    $endDate = Carbon::parse($training->end_date);
                    $totalDuration += $startDate->diffInMonths($endDate);
                } catch (\Exception $e) {
                    // Skip this entry if date parsing fails
                    continue;
                }
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
        // Ensure the applicantId is valid
        if (empty($applicantId) || !is_numeric($applicantId)) {
            return 0;
        }

        $organizations = recruitment_applicant_organization::where('applicant_id', $applicantId)->get();

        if ($organizations->isEmpty()) {
            return 0;
        }

        $count = $organizations->count();
        $totalDuration = 0;

        foreach ($organizations as $org) {
            if ($org->start_date) {
                try {
                    $startDate = Carbon::parse($org->start_date);
                    $endDate = $org->end_date ? Carbon::parse($org->end_date) : Carbon::now();
                    $totalDuration += $startDate->diffInMonths($endDate);
                } catch (\Exception $e) {
                    // Skip this entry if date parsing fails
                    continue;
                }
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
        // Ensure the applicantId is valid
        if (empty($applicantId) || !is_numeric($applicantId)) {
            return 0;
        }

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

    private function calculateExpectedSalaryScore($salary, $config = null)
    {
        // Return 0 if salary is empty or invalid
        if (empty($salary) || !is_numeric($salary)) {
            return 0;
        }

        // Convert salary to float to ensure it's numeric
        $salary = (float)$salary;

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

    // Improved getScoreFromRanges function to handle all edge cases
    public function getScoreFromRanges($value, $ranges)
    {
        // If value is not numeric, return 0
        if (!is_numeric($value)) {
            return 0;
        }

        // Convert to float to ensure proper comparison
        $value = (float)$value;

        if (empty($ranges)) {
            return 0;
        }

        // Sort ranges by rank
        $ranges = collect($ranges)->sortBy('rank')->values()->all();
        $totalRanges = count($ranges);

        foreach ($ranges as $range) {
            // Konversi eksplisit ke float jika perlu
            $min = isset($range['min']) ? (float)$range['min'] : 0;
            $max = isset($range['max']) ? (float)$range['max'] : 0;

            // Ensure rank is valid
            $rank = isset($range['rank']) ? (int)$range['rank'] : $totalRanges;

            // Skip invalid ranges
            if (!is_numeric($min) || !is_numeric($max) || !is_numeric($rank)) {
                continue;
            }

            // Untuk semua range, gunakan upper bound inklusif
            if ($value >= $min && $value <= $max) {
                return 1 - (($rank - 1) / $totalRanges);
            }
        }

        // Value is outside all defined ranges
        return 1 / ($totalRanges + 1);
    }


    // Improved helper functions for all criteria calculations
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



    private function calculateDistanceScore($distance, $config = null)
    {
        // Return 0 if distance is empty or invalid
        if (empty($distance) || !is_numeric($distance)) {
            return 0;
        }

        // Convert distance to float to ensure it's numeric
        $distance = (float)$distance;

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
        // Return 0 if education is null or empty
        if (!$education) {
            return 0; // No education data
        }

        // Check if the degree property exists
        if (!isset($education->degree) || empty($education->degree)) {
            return 0; // Missing degree information
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

            if ($totalLevels > 0) {
                // Find the education level in configured levels
                foreach ($levels as $level) {
                    if ($level['name'] === $education->degree) {
                        $levelScore = 1 - (($level['rank'] - 1) / $totalLevels);
                        break;
                    }
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

        // Calculate grade score with better validation
        if (isset($education->grade) && $education->grade !== null && is_numeric($education->grade)) {
            if (in_array($education->degree, ['SMK', 'SMA'])) {
                $gradeScore = min(1, max(0, $education->grade / 100));
            } else {
                $gradeScore = min(1, max(0, $education->grade / 4));
            }
        }

        // Combined weighted score
        return ($levelScore * $levelWeight) + ($gradeScore * $gradeWeight);
    }

    // Modified calculateApplicantScoresAHP function to ensure consistent data structure// Fixed calculateApplicantScoresAHP function to preserve actual AHP scores
    private function calculateApplicantScoresAHP($applicants, $weights, $criteriaConfigs)
    {
        // Langkah 1: Hitung nilai untuk setiap kriteria pelamar
        $applicantCriteriaMatrix = [];

        foreach ($applicants as $applicant) {
            $criteriaValues = [];

            // Hitung nilai untuk setiap kriteria
            foreach ($weights as $criterion => $weight) {
                $rawScore = 0; // Default value

                switch ($criterion) {
                    case 'age':
                        $rawScore = $this->calculateAgeScore(
                            $applicant->birth_date,
                            $criteriaConfigs[$criterion] ?? null
                        );
                        break;
                    case 'expected_salary':
                        $rawScore = $this->calculateExpectedSalaryScore(
                            $applicant->expected_salary ?? 0,
                            $criteriaConfigs[$criterion] ?? null
                        );
                        break;
                    case 'distance':
                        $rawScore = $this->calculateDistanceScore(
                            $applicant->distance ?? 0,
                            $criteriaConfigs[$criterion] ?? null
                        );
                        break;
                    case 'education':
                        $education = recruitment_applicant_education::where('applicant_id', $applicant->id)
                            ->orderBy('end_education', 'desc')
                            ->first();
                        $rawScore = $this->calculateEducationScore(
                            $education,
                            $criteriaConfigs[$criterion] ?? null
                        );
                        break;
                    case 'experience_duration':
                        $rawScore = $this->calculateExperienceDurationScore(
                            $applicant->id,
                            $criteriaConfigs[$criterion] ?? null
                        );
                        break;
                    case 'training':
                        $rawScore = $this->calculateTrainingScore(
                            $applicant->id,
                            $criteriaConfigs[$criterion] ?? null
                        );
                        break;
                    case 'language':
                        $rawScore = $this->calculateLanguageScore(
                            $applicant->id,
                            $criteriaConfigs[$criterion] ?? null
                        );
                        break;
                    case 'organization':
                        $rawScore = $this->calculateOrganizationScore(
                            $applicant->id,
                            $criteriaConfigs[$criterion] ?? null
                        );
                        break;
                }

                // Ensure we have a valid numeric value
                $criteriaValues[$criterion] = is_numeric($rawScore) ? (float)$rawScore : 0;
            }

            $applicantCriteriaMatrix[$applicant->id] = $criteriaValues;
        }

        // Langkah 2: Buat matriks perbandingan berpasangan untuk setiap kriteria
        $criteriaWeights = [];

        foreach ($weights as $criterion => $weight) {
            if (count($applicants) < 2) {
                // Jika hanya ada satu pelamar, tidak perlu perbandingan berpasangan
                $criteriaWeights[$criterion] = [$applicants[0]->id => 1];
                continue;
            }

            // Buat matriks perbandingan berpasangan untuk kriteria ini
            $pairwiseMatrix = [];
            $applicantIds = [];

            foreach ($applicants as $applicant) {
                $applicantIds[] = $applicant->id;
                $pairwiseMatrix[$applicant->id] = [];
            }

            // Isi matriks perbandingan berpasangan
            foreach ($applicantIds as $id1) {
                foreach ($applicantIds as $id2) {
                    // Diagonal utama selalu bernilai 1
                    if ($id1 == $id2) {
                        $pairwiseMatrix[$id1][$id2] = 1;
                        continue;
                    }

                    // Jika nilai telah dihitung untuk pasangan sebaliknya, gunakan kebalikannya
                    if (isset($pairwiseMatrix[$id2][$id1])) {
                        $pairwiseMatrix[$id1][$id2] = 1 / $pairwiseMatrix[$id2][$id1];
                        continue;
                    }

                    // Dapatkan nilai kriteria untuk kedua pelamar
                    $value1 = $applicantCriteriaMatrix[$id1][$criterion] ?? 0;
                    $value2 = $applicantCriteriaMatrix[$id2][$criterion] ?? 0;

                    // Handle kasus khusus untuk nilai 0
                    if ($value1 == 0 && $value2 == 0) {
                        $pairwiseMatrix[$id1][$id2] = 1; // Keduanya sama
                    } elseif ($value1 == 0) {
                        $pairwiseMatrix[$id1][$id2] = 1 / 9; // Nilai minimum untuk skala Saaty
                    } elseif ($value2 == 0) {
                        $pairwiseMatrix[$id1][$id2] = 9; // Nilai maksimum untuk skala Saaty
                    } else {
                        // Hitung rasio dan konversi ke skala Saaty (1-9)
                        $ratio = $value1 / $value2;

                        // Konversi rasio ke skala Saaty
                        if ($ratio >= 1) {
                            $pairwiseMatrix[$id1][$id2] = min(9, round($ratio, 2));
                        } else {
                            $pairwiseMatrix[$id1][$id2] = max(1 / 9, round($ratio, 2));
                        }
                    }
                }
            }

            // Langkah 3: Normalisasi matriks perbandingan berpasangan
            $normalizedMatrix = [];
            $columnSums = [];

            // Hitung jumlah setiap kolom
            foreach ($applicantIds as $id2) {
                $columnSums[$id2] = 0;
                foreach ($applicantIds as $id1) {
                    $columnSums[$id2] += $pairwiseMatrix[$id1][$id2];
                }
            }

            // Normalisasi matriks
            foreach ($applicantIds as $id1) {
                $normalizedMatrix[$id1] = [];

                foreach ($applicantIds as $id2) {
                    if ($columnSums[$id2] > 0) {
                        $normalizedMatrix[$id1][$id2] = $pairwiseMatrix[$id1][$id2] / $columnSums[$id2];
                    } else {
                        $normalizedMatrix[$id1][$id2] = 0;
                    }
                }
            }

            // Langkah 4: Hitung vektor prioritas (bobot lokal) untuk setiap pelamar
            $priorityVector = [];

            foreach ($applicantIds as $id) {
                $rowSum = 0;
                foreach ($applicantIds as $otherId) {
                    $rowSum += $normalizedMatrix[$id][$otherId];
                }
                $priorityVector[$id] = $rowSum / count($applicantIds);
            }

            // Menyimpan bobot prioritas untuk kriteria ini
            $criteriaWeights[$criterion] = $priorityVector;
        }

        // Langkah 5: Hitung skor akhir menggunakan bobot kriteria global
        $finalScores = [];

        foreach ($applicants as $applicant) {
            $finalScore = 0;
            $breakdownScores = [];

            foreach ($weights as $criterion => $weight) {
                // Get the raw unweighted score (from our calculation functions)
                $rawScore = $applicantCriteriaMatrix[$applicant->id][$criterion] ?? 0;

                // Get the normalized score (from AHP comparison)
                $normalizedScore = $criteriaWeights[$criterion][$applicant->id] ?? 0;

                // Calculate the weighted score - keep the actual AHP scores
                $weightedScore = $normalizedScore * $weight;

                // Add to final score
                $finalScore += $weightedScore;

                // Store the breakdown with CONSISTENT structure
                $breakdownScores[$criterion] = [
                    'raw_score' => $rawScore,
                    'normalized_score' => $normalizedScore,
                    'weighted_score' => $weightedScore,  // This is already the actual AHP weighted score
                    'weight' => $weight
                ];
            }

            $finalScores[] = [
                'applicant' => $applicant,
                'score' => $finalScore,  // This is the actual AHP final score without normalization to 100%
                'breakdown' => $breakdownScores
            ];
        }

        // Sort by score descending - we preserve the actual AHP scores
        return collect($finalScores)->sortByDesc('score')->values();
    }

    // Fungsi calculate yang memanggil AHP
    public function calculate(Request $request)
    {
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

            // DEBUG: Generate matrix data for debugging
            $debugMatrices = $this->debugMatrices($applicants, $weights, $criteriaConfigs);

            // You can either dd() here to see all matrices:
            dd($debugMatrices);

            // Or continue with your normal AHP calculation:
            $rankings = $this->calculateApplicantScoresAHP($applicants, $weights, $criteriaConfigs);

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


    // Add this function to your class to display matrices in a readable format
    private function debugMatrices($applicants, $weights, $criteriaConfigs)
    {
        // Store all matrices for debugging
        $debugData = [
            'criteria_weights' => $weights,
            'criteria_matrix' => [],
            'applicant_criteria_values' => [],
            'pairwise_matrices' => [],
            'normalized_matrices' => [],
            'priority_vectors' => []
        ];

        // Step 1: Collect raw applicant criteria values
        foreach ($applicants as $applicant) {
            $criteriaValues = [];

            foreach ($weights as $criterion => $weight) {
                $rawScore = 0;

                switch ($criterion) {
                    case 'age':
                        $rawScore = $this->calculateAgeScore(
                            $applicant->birth_date,
                            $criteriaConfigs[$criterion] ?? null
                        );
                        break;
                    case 'expected_salary':
                        $rawScore = $this->calculateExpectedSalaryScore(
                            $applicant->expected_salary ?? 0,
                            $criteriaConfigs[$criterion] ?? null
                        );
                        break;
                    case 'distance':
                        $rawScore = $this->calculateDistanceScore(
                            $applicant->distance ?? 0,
                            $criteriaConfigs[$criterion] ?? null
                        );
                        break;
                    case 'education':
                        $education = recruitment_applicant_education::where('applicant_id', $applicant->id)
                            ->orderBy('end_education', 'desc')
                            ->first();
                        $rawScore = $this->calculateEducationScore(
                            $education,
                            $criteriaConfigs[$criterion] ?? null
                        );
                        break;
                    case 'experience_duration':
                        $rawScore = $this->calculateExperienceDurationScore(
                            $applicant->id,
                            $criteriaConfigs[$criterion] ?? null
                        );
                        break;
                    case 'training':
                        $rawScore = $this->calculateTrainingScore(
                            $applicant->id,
                            $criteriaConfigs[$criterion] ?? null
                        );
                        break;
                    case 'language':
                        $rawScore = $this->calculateLanguageScore(
                            $applicant->id,
                            $criteriaConfigs[$criterion] ?? null
                        );
                        break;
                    case 'organization':
                        $rawScore = $this->calculateOrganizationScore(
                            $applicant->id,
                            $criteriaConfigs[$criterion] ?? null
                        );
                        break;
                }

                $rawScore = is_numeric($rawScore) ? (float)$rawScore : 0;
                $criteriaValues[$criterion] = $rawScore;
            }

            $debugData['applicant_criteria_values'][$applicant->id] = [
                'name' => $applicant->name,
                'values' => $criteriaValues
            ];
        }

        // Step 2: Build the criteria pairwise comparison matrix
        // This would be manually entered or calculated elsewhere
        // Here we're just showing a placeholder structure
        $criteria = array_keys($weights);
        foreach ($criteria as $c1) {
            $debugData['criteria_matrix'][$c1] = [];
            foreach ($criteria as $c2) {
                // This should be replaced with your actual criteria matrix values
                if ($c1 === $c2) {
                    $debugData['criteria_matrix'][$c1][$c2] = 1;
                } else {
                    // Placeholder - in your real code this would come from user input or calculation
                    $debugData['criteria_matrix'][$c1][$c2] = 0; // Placeholder
                }
            }
        }

        // Step 3: Calculate pairwise matrices for each criterion
        foreach ($weights as $criterion => $weight) {
            $pairwiseMatrix = [];
            $normalizedMatrix = [];
            $priorityVector = [];

            $applicantIds = [];
            foreach ($applicants as $applicant) {
                $applicantIds[] = $applicant->id;
                $pairwiseMatrix[$applicant->id] = [];
            }

            // Fill the pairwise comparison matrix for this criterion
            foreach ($applicantIds as $id1) {
                foreach ($applicantIds as $id2) {
                    if ($id1 == $id2) {
                        $pairwiseMatrix[$id1][$id2] = 1;
                        continue;
                    }

                    if (isset($pairwiseMatrix[$id2][$id1])) {
                        $pairwiseMatrix[$id1][$id2] = 1 / $pairwiseMatrix[$id2][$id1];
                        continue;
                    }

                    $value1 = $debugData['applicant_criteria_values'][$id1]['values'][$criterion] ?? 0;
                    $value2 = $debugData['applicant_criteria_values'][$id2]['values'][$criterion] ?? 0;

                    if ($value1 == 0 && $value2 == 0) {
                        $pairwiseMatrix[$id1][$id2] = 1;
                    } elseif ($value1 == 0) {
                        $pairwiseMatrix[$id1][$id2] = 1 / 9;
                    } elseif ($value2 == 0) {
                        $pairwiseMatrix[$id1][$id2] = 9;
                    } else {
                        $ratio = $value1 / $value2;
                        if ($ratio >= 1) {
                            $pairwiseMatrix[$id1][$id2] = min(9, round($ratio, 2));
                        } else {
                            $pairwiseMatrix[$id1][$id2] = max(1 / 9, round($ratio, 2));
                        }
                    }
                }
            }

            // Calculate column sums for normalization
            $columnSums = [];
            foreach ($applicantIds as $id2) {
                $columnSums[$id2] = 0;
                foreach ($applicantIds as $id1) {
                    $columnSums[$id2] += $pairwiseMatrix[$id1][$id2];
                }
            }

            // Normalize the matrix
            foreach ($applicantIds as $id1) {
                $normalizedMatrix[$id1] = [];
                foreach ($applicantIds as $id2) {
                    if ($columnSums[$id2] > 0) {
                        $normalizedMatrix[$id1][$id2] = $pairwiseMatrix[$id1][$id2] / $columnSums[$id2];
                    } else {
                        $normalizedMatrix[$id1][$id2] = 0;
                    }
                }
            }

            // Calculate priority vector (row averages)
            foreach ($applicantIds as $id) {
                $rowSum = 0;
                foreach ($applicantIds as $otherId) {
                    $rowSum += $normalizedMatrix[$id][$otherId];
                }
                $priorityVector[$id] = $rowSum / count($applicantIds);
            }

            $debugData['pairwise_matrices'][$criterion] = $pairwiseMatrix;
            $debugData['normalized_matrices'][$criterion] = $normalizedMatrix;
            $debugData['priority_vectors'][$criterion] = $priorityVector;
            $debugData['column_sums'][$criterion] = $columnSums;
        }

        // Step 4: Calculate final scores
        $finalScores = [];
        foreach ($applicants as $applicant) {
            $finalScore = 0;
            $breakdownScores = [];

            foreach ($weights as $criterion => $weight) {
                $priorityValue = $debugData['priority_vectors'][$criterion][$applicant->id] ?? 0;
                $weightedScore = $priorityValue * $weight;
                $finalScore += $weightedScore;

                $breakdownScores[$criterion] = [
                    'raw_score' => $debugData['applicant_criteria_values'][$applicant->id]['values'][$criterion],
                    'normalized_score' => $priorityValue,
                    'weighted_score' => $weightedScore,
                    'weight' => $weight
                ];
            }

            $finalScores[$applicant->id] = [
                'name' => $applicant->name,
                'score' => $finalScore,
                'breakdown' => $breakdownScores
            ];
        }

        $debugData['final_scores'] = $finalScores;

        // Output in debug format
        return $debugData;
    }


    public function index_interview(Request $request)
    {
        $query = DB::table('recruitment_demand')
            ->join('users', 'recruitment_demand.maker_id', '=', 'users.id')
            ->join('employee_departments', 'recruitment_demand.department_id', '=', 'employee_departments.id')
            ->join('employee_positions', 'recruitment_demand.position_id', '=', 'employee_positions.id')
            ->whereColumn('qty_needed', '>', 'qty_fullfil')
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

        // Subqueries to count applicants by status for each demand
        $pendingCount = DB::table('recruitment_applicant')
            ->select('recruitment_demand_id', DB::raw('COUNT(*) as count'))
            ->where('status_applicant', 'Pending')
            ->groupBy('recruitment_demand_id');

        $approvedCount = DB::table('recruitment_applicant')
            ->select('recruitment_demand_id', DB::raw('COUNT(*) as count'))
            ->where('status_applicant', 'Approved')
            ->groupBy('recruitment_demand_id');

        $declinedCount = DB::table('recruitment_applicant')
            ->select('recruitment_demand_id', DB::raw('COUNT(*) as count'))
            ->where('status_applicant', 'Declined')
            ->groupBy('recruitment_demand_id');

        $doneCount = DB::table('recruitment_applicant')
            ->select('recruitment_demand_id', DB::raw('COUNT(*) as count'))
            ->where('status_applicant', 'Done')
            ->groupBy('recruitment_demand_id');

        // Join the subqueries with the main query
        $query = $query->leftJoinSub($pendingCount, 'pending', function ($join) {
            $join->on('recruitment_demand.id', '=', 'pending.recruitment_demand_id');
        })
            ->leftJoinSub($approvedCount, 'approved', function ($join) {
                $join->on('recruitment_demand.id', '=', 'approved.recruitment_demand_id');
            })
            ->leftJoinSub($declinedCount, 'declined', function ($join) {
                $join->on('recruitment_demand.id', '=', 'declined.recruitment_demand_id');
            })
            ->leftJoinSub($doneCount, 'done', function ($join) {
                $join->on('recruitment_demand.id', '=', 'done.recruitment_demand_id');
            })
            ->addSelect(
                DB::raw('COALESCE(pending.count, 0) as pending_count'),
                DB::raw('COALESCE(approved.count, 0) as approved_count'),
                DB::raw('COALESCE(declined.count, 0) as declined_count'),
                DB::raw('COALESCE(done.count, 0) as done_count')
            );

        // Get distinct values for dropdowns from actual tables
        $departments = EmployeeDepartment::whereHas('demands', function ($q) {
            $q->whereColumn('qty_needed', '>', 'qty_fullfil')
                ->where('status_demand', 'Approved');
        })
            ->select('id', 'department')
            ->get();

        $positions = EmployeePosition::whereHas('demands', function ($q) {
            $q->whereColumn('qty_needed', '>', 'qty_fullfil')
                ->where('status_demand', 'Approved');
        })
            ->select('id', 'position')
            ->get();

        $jobStatuses = DB::table('recruitment_demand')
            ->whereColumn('qty_needed', '>', 'qty_fullfil')
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
        $demand = recruitment_demand::with(['positionRelation', 'departmentRelation'])->where('id', $id)->first();
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

    public function exchange_position(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'new_demand_id' => 'required',
            'exchange_reason' => 'required|string',
            'needs_reschedule' => 'nullable|string',
            'interview_date' => 'required_if:needs_reschedule,on|nullable|date',
            'interview_note' => 'required_if:needs_reschedule,on|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $applicant = recruitment_applicant::findOrFail($request->applicant_id);

        // Get new position data with relationships
        $newDemand = recruitment_demand::with(['departmentRelation', 'positionRelation'])
            ->findOrFail($request->new_demand_id);

        // Update database with position exchange
        $updateData = [
            'recruitment_demand_id' => $request->new_demand_id,
            'exchange_note' => $request->exchange_reason,
            'updated_at' => now(),
        ];

        // Check if reschedule is needed - checkbox is "on" when checked
        $needsReschedule = $request->has('needs_reschedule') && $request->needs_reschedule === 'on';
        $oldInterviewDate = null;

        if ($needsReschedule) {
            $oldInterviewDate = $applicant->interview_date; // Save old date for email

            // Add interview schedule data to update
            $updateData['interview_date'] = $request->interview_date;
            $updateData['interview_note'] = str_replace("\r\n", "\n", $request->interview_note);
        }

        // Update the applicant record
        $applicant->update($updateData);

        // Send position exchange email
        Mail::to($applicant->email)->send(new PositionExchangedMail(
            $applicant,
            $newDemand->positionRelation->position ?? 'Unknown Position',
            $newDemand->departmentRelation->department ?? 'Unknown Department'
        ));

        // Send interview reschedule email if needed
        if ($needsReschedule && $oldInterviewDate) {
            Mail::to($applicant->email)->send(new InterviewRescheduledMail($applicant, $oldInterviewDate));
        } elseif ($needsReschedule && is_null($oldInterviewDate)) {
            // If no previous interview was scheduled, send the initial interview email
            Mail::to($applicant->email)->send(new InterviewScheduledMail($applicant));
        }

        return response()->json(['message' => 'Position exchanged successfully' . ($needsReschedule ? ' and interview rescheduled' : '')]);
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



    public function get_exchange($id)
    {
        // dd($id);
        $applicant = recruitment_applicant::where('id', $id)->first();

        // Ambil posisi yang tersedia kecuali ID yang sedang dipilih
        $positions = recruitment_demand::with(['departmentRelation', 'positionRelation'])
            ->whereColumn('qty_needed', '>', 'qty_fullfil')
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

            // Set contract dates based on employee status
            $contractStartDate = null;
            $contractEndDate = null;

            if ($demand->status_job == 'Part Time' || $demand->status_job == 'Contract') {
                $contractStartDate = $joinDate;
                // If length_of_working exists, calculate end date
                if (!is_null($demand->length_of_working)) {
                    $contractEndDate = $joinDate->copy()->addMonths($demand->length_of_working);
                }
            }

            // Create new employee
            $employee = User::create([
                'employee_id' => $employeeId,
                'name' => $applicant->name,
                'position_id' => $demand->position_id,
                'department_id' => $demand->department_id,
                'email' => $applicant->email,
                'phone_number' => $applicant->phone_number,
                'employee_status' => $demand->status_job,
                'contract_start_date' => $contractStartDate,
                'contract_end_date' => $contractEndDate,
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
            // $demand->decrement('qty_needed');
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
            $newEducation = users_education::create([
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

            // Copy transcript file if exists
            if (!empty($education->transcript_file_path)) {
                $fileName = "transcript_{$userId}_{$education->degree}_{$newEducation->id}." .
                    pathinfo($education->transcript_file_path, PATHINFO_EXTENSION);

                $newPath = "user/transcript_user/{$fileName}";

                // Copy the file to the new location
                $transcriptPath = $this->copyFile($education->transcript_file_path, $newPath);

                // Update the new education record with the transcript path
                if ($transcriptPath) {
                    $newEducation->update(['transcript_file_path' => $transcriptPath]);
                }
            }
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
}
