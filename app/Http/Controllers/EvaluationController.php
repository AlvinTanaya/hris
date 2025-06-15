<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


use Illuminate\Support\Facades\Response;

use Illuminate\Contracts\Validation\Rule;
use App\Imports\EmployeesImport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use DateInterval;
use DatePeriod;

use Illuminate\Bus\UpdatedBatchJobCounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;


use App\Models\history_transfer_employee;
use App\Models\RulePerformanceGrade;
use App\Models\RuleDisciplineGrade;
use App\Models\RuleEvaluationCriteriaPerformance;
use App\Models\RuleEvaluationReductionPerformance;
use App\Models\RuleEvaluationWeightPerformance;
use App\Models\EmployeePosition;
use App\Models\EmployeeDepartment;
use App\Models\Notification;
use App\Models\User;
use App\Models\EvaluationPerformance;
use App\Models\EvaluationPerformanceDetail;
use App\Models\EvaluationPerformanceReduction;
use App\Models\EvaluationPerformanceMessage;
use App\Models\TimeOffPolicy;
use App\Models\WarningLetterRule;
use App\Models\WarningLetter;
use App\Models\DisciplineRule;
use App\Models\CustomHoliday;
use App\Models\EmployeeAbsent;
use App\Models\RequestTimeOff;
use App\Models\EmployeeFinalEvaluation;
use App\Models\RuleEvaluationGradeSalaryFinal;
use App\Models\EmployeeSalary;
use App\Models\SalaryHistory;

use App\Models\elearning_invitation;
use App\Models\elearning_answer;
use App\Models\elearning_lesson;
use App\Models\elearning_schedule;
use App\Models\RuleElearningGrade;








class EvaluationController extends Controller
{


    /**
     * Display a listing of rule performance evaluations
     */
    public function rule_performance_criteria_index()
    {
        $criteria_performances = RuleEvaluationCriteriaPerformance::all();
        return view('/evaluation/rule/performance/criteria/index', compact('criteria_performances'));
    }

    /**
     * Show the form for creating a new rule performance evaluation
     */
    public function rule_performance_criteria_create()
    {
        return view('evaluation.rule.performance.criteria.create');
    }

    /**
     * Store a newly created rule performance evaluation in storage
     */
    public function rule_performance_criteria_store(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|string|max:255',
        ]);

        // Check for duplicate type
        if (RuleEvaluationCriteriaPerformance::where('type', $validatedData['type'])->exists()) {
            return response()->json([
                'message' => 'The performance type you entered already exists. Please use a different type.'
            ], 409); // 409 Conflict status code
        }

        try {
            $performance = RuleEvaluationCriteriaPerformance::create($validatedData);

            return response()->json([
                'message' => 'Rule Performance created successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while saving the data. Please try again.'
            ], 500);
        }
    }


    /**
     * Show the form for editing the specified rule performance evaluation
     */
    public function rule_performance_criteria_edit($id)
    {
        $criteria_performances = RuleEvaluationCriteriaPerformance::findOrFail($id);
        return view('/evaluation/rule/performance/criteria/update', compact('criteria_performances'));
    }

    /**
     * Update the specified rule performance evaluation in storage
     */
    public function rule_performance_criteria_update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'type' => 'required|string|max:255',
        ]);

        // Check for duplicate type (excluding current record)
        if (RuleEvaluationCriteriaPerformance::where('type', $validatedData['type'])
            ->where('id', '!=', $id)
            ->exists()
        ) {
            return response()->json([
                'message' => 'The performance type already exists. Please use a different type.'
            ], 409);
        }

        try {
            $performance = RuleEvaluationCriteriaPerformance::findOrFail($id);
            $performance->update($validatedData);

            return response()->json([
                'message' => 'Performance rule updated successfully!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update performance rule: ' . $e->getMessage()
            ], 500);
        }
    }





    public function weight_performance_index(Request $request)
    {
        // Get query parameters for filtering
        $positionFilter = $request->input('position');
        $criteriaFilter = $request->input('criteria');
        $statusFilter = $request->input('status');

        // Start with a base query
        $query = RuleEvaluationWeightPerformance::with(['position', 'criteria']);

        // Apply filters if provided
        if ($positionFilter) {
            $query->whereHas('position', function ($q) use ($positionFilter) {
                $q->where('id', $positionFilter);
            });
        }

        if ($criteriaFilter) {
            $query->whereHas('criteria', function ($q) use ($criteriaFilter) {
                $q->where('id', $criteriaFilter);
            });
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        // Order and get results
        $weights = $query->orderBy('position_id')
            ->orderBy('criteria_id')
            ->get();

        // Group weights by position_id
        $groupedWeights = $weights->groupBy('position_id');

        $positions = EmployeePosition::all();
        $criteria = RuleEvaluationCriteriaPerformance::all();
        $statuses = ['Active', 'Inactive'];

        return view('/evaluation/rule/performance/weight/index', compact(
            'groupedWeights',
            'positions',
            'criteria',
            'statuses',
            'positionFilter',
            'criteriaFilter',
            'statusFilter'
        ));
    }



    public function weight_performance_create()
    {
        $positions = EmployeePosition::all();
        $criterias = RuleEvaluationCriteriaPerformance::all();

        return view('evaluation.rule.performance.weight.create', compact('positions', 'criterias'));
    }


    public function weight_performance_store(Request $request)
    {
        $request->validate([
            'position_id' => 'required|array',
            'position_id.*' => 'exists:employee_positions,id',
            'criteria_id' => 'required|exists:rule_evaluation_criteria_performance,id',
            'weight' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:Active,Inactive',
        ]);

        try {
            $criteria_id = $request->criteria_id;
            $weight = $request->weight;
            $status = $request->status;
            $successCount = 0;
            $duplicateCount = 0;

            foreach ($request->position_id as $position_id) {
                $exists = RuleEvaluationWeightPerformance::where([
                    'position_id' => $position_id,
                    'criteria_id' => $criteria_id
                ])->exists();

                if ($exists) {
                    $duplicateCount++;
                    continue;
                }

                RuleEvaluationWeightPerformance::create([
                    'position_id' => $position_id,
                    'criteria_id' => $criteria_id,
                    'weight' => $weight,
                    'status' => $status,
                ]);

                $successCount++;
            }

            if ($successCount > 0) {
                $message = "$successCount weight performance(s) have been created successfully.";

                if ($duplicateCount > 0) {
                    $message .= " $duplicateCount position(s) skipped due to existing entries.";
                }

                return response()->json([
                    'message' => $message
                ]);
            } else {
                return response()->json([
                    'message' => 'No records created. All selected positions already have criteria assignments.'
                ], 400); // Bad Request status code
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error_details' => 'File: ' . $e->getFile() . ' Line: ' . $e->getLine()
            ], 500);
        }
    }

    public function weight_performance_update(Request $request, $id)
    {

        $request->validate([
            'position_id' => 'required|exists:employee_positions,id',
            'criteria_id' => 'required|exists:rule_evaluation_criteria_performance,id',
            'weight' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:Active,Inactive',
        ]);



        try {

            $weight = RuleEvaluationWeightPerformance::findOrFail($id);



            // Check for duplicate
            if (($weight->position_id != $request->position_id || $weight->criteria_id != $request->criteria_id) &&
                RuleEvaluationWeightPerformance::where('position_id', $request->position_id)
                ->where('criteria_id', $request->criteria_id)
                ->where('id', '!=', $id)
                ->exists()
            ) {
                return response()->json([
                    'message' => 'A weight performance already exists for this position and criteria combination.'
                ], 409);
            }

            $weight->update($request->only(['position_id', 'criteria_id', 'weight', 'status']));

            return response()->json([
                'message' => 'Weight performance updated successfully',
                'data' => $weight
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function weight_performance_edit($id)
    {
        $weight = RuleEvaluationWeightPerformance::findOrFail($id);
        $positions = EmployeePosition::all();
        $criterias = RuleEvaluationCriteriaPerformance::all();

        return view('/evaluation/rule/performance/weight/update', compact('weight', 'positions', 'criterias'));
    }



    public function rule_performance_reduction_index()
    {
        $reductions = RuleEvaluationReductionPerformance::with('warningLetterRule')->get();

        return view('evaluation.rule.performance.reduction.index', compact('reductions'));
    }

    public function rule_performance_reduction_create()
    {
        $types = WarningLetterRule::pluck('name', 'id'); // Changed to use id as value
        return view('evaluation.rule.performance.reduction.create', compact('types'));
    }

    public function rule_performance_reduction_edit($id)
    {
        $reduction = RuleEvaluationReductionPerformance::findOrFail($id);
        $types = WarningLetterRule::pluck('name', 'id'); // Changed to use id as value

        return view('/evaluation/rule/performance/reduction/update', compact('reduction', 'types'));
    }

    public function checkTypeExists(Request $request)
    {
        $type_id = $request->input('type_id');
        $exists = RuleEvaluationReductionPerformance::where('type_id', $type_id)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function rule_performance_reduction_store(Request $request)
    {
        $request->validate([
            'type_id' => 'required|exists:rule_warning_letter,id', // Validate that type_id exists in warning letter rules
            'weight' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
        ]);

        // If status Inactive, set weight = 0
        $weight = ($request->status === 'Inactive') ? 0 : $request->weight;

        try {
            RuleEvaluationReductionPerformance::create([
                'type_id' => $request->type_id, // Store the id instead of name
                'weight' => $weight,
                'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Performance reduction rule created successfully.',
                'redirect' => route('evaluation.rule.performance.reduction.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create performance reduction rule.'
            ], 500);
        }
    }

    public function rule_performance_reduction_update(Request $request, $id)
    {
        $request->validate([
            'type_id' => 'required|exists:rule_warning_letter,id', // Validate that type_id exists in warning letter rules
            'weight' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
        ]);

        // If status Inactive, set weight = 0
        $weight = ($request->status === 'Inactive') ? 0 : $request->weight;

        try {
            $reduction = RuleEvaluationReductionPerformance::findOrFail($id);
            $reduction->update([
                'type_id' => $request->type_id, // Store the id instead of name
                'weight' => $weight,
                'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Performance reduction rule updated successfully.',
                'redirect' => route('evaluation.rule.performance.reduction.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update performance reduction rule.'
            ], 500);
        }
    }




    /**
     * Display a listing of the evaluations assigned by the current user.
     */
    public function assign_performance_index(Request $request, $id)
    {
        // Get current user
        $user = User::findOrFail($id);
        $userPosition = $user->position_id;
        $currentPosition = EmployeePosition::findOrFail($userPosition);
        $currentRank = $currentPosition->ranking;

        // Check if user is Director or GM
        $isDirectorOrGM = in_array($currentPosition->title, ['Director', 'General Manager'])
            || in_array($currentPosition->id, [1, 2]); // Adjust IDs as needed

        // Get current month and year for default filtering
        $currentMonth = $request->input('month', date('n'));
        $currentYear = $request->input('year', date('Y'));

        // Get subordinate users based on hierarchy
        $subordinateQuery = User::query();

        if ($isDirectorOrGM) {
            // Directors/GMs can see everyone with lower ranking
            $subordinateQuery->whereHas('position', function ($query) use ($currentRank) {
                $query->where('ranking', '>', $currentRank);
            });
        } else {
            // Managers and below can only see their department with lower ranking
            $subordinateQuery->whereHas('position', function ($query) use ($currentRank) {
                $query->where('ranking', '>', $currentRank);
            })->where('department_id', $user->department_id);
        }

        $subordinateIds = $subordinateQuery->pluck('id')->toArray();

        // Get available years from evaluations for subordinates
        $availableYears = EvaluationPerformance::whereIn('user_id', $subordinateIds)
            ->selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [$currentYear];
        }

        // Query evaluations for subordinates
        $query = EvaluationPerformance::with([
            'user',
            'user.position',
            'user.department',
            'details.weightPerformance.criteria'
        ])
            ->whereIn('user_id', $subordinateIds)
            ->orderBy('date', 'desc');

        // Jika ini request awal (tanpa parameter filter), gunakan bulan dan tahun saat ini
        if (
            !$request->has('month') && !$request->has('year') && !$request->has('employee') &&
            !$request->has('position') && !$request->has('department')
        ) {
            $query->whereMonth('date', date('n'))
                ->whereYear('date', date('Y'));
        } else {
            // Jika ada filter yang diberikan, gunakan filter tersebut
            if ($request->filled('month')) {
                $query->whereMonth('date', $request->month);
            }

            if ($request->filled('year')) {
                $query->whereYear('date', $request->year);
            }

            if ($request->filled('employee')) {
                $query->where('user_id', $request->employee);
            }
        }

        $evaluations = $query->get();

        // Process each evaluation to add historical position and department
        foreach ($evaluations as $evaluation) {
            // Find the transfer history record closest to but before the evaluation date
            $history = history_transfer_employee::where('users_id', $evaluation->user_id)
                ->where('created_at', '<', $evaluation->date)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($history) {
                // Use historical position and department
                $evaluation->historical_position_id = $history->new_position_id;
                $evaluation->historical_department_id = $history->new_department_id;

                // Add the historical position and department objects
                $evaluation->historical_position = EmployeePosition::find($history->new_position_id);
                $evaluation->historical_department = EmployeeDepartment::find($history->new_department_id);
            } else {
                // No history found, use current position and department
                $evaluation->historical_position_id = $evaluation->user->position_id;
                $evaluation->historical_department_id = $evaluation->user->department_id;

                $evaluation->historical_position = $evaluation->user->position;
                $evaluation->historical_department = $evaluation->user->department;
            }
        }

        // Post-query filtering for position and department using historical data
        if ($request->filled('position')) {
            $evaluations = $evaluations->filter(function ($eval) use ($request) {
                return $eval->historical_position_id == $request->position;
            });
        }

        if ($request->filled('department')) {
            $evaluations = $evaluations->filter(function ($eval) use ($request) {
                return $eval->historical_department_id == $request->department;
            });
        }

        // Get employees list for filter dropdown
        $employeesList = User::whereIn('id', $subordinateIds)
            ->orderBy('name')
            ->get();

        // Create a collection of unique historical positions and departments from evaluations
        $historicalPositionIds = $evaluations->pluck('historical_position_id')->unique();
        $historicalDepartmentIds = $evaluations->pluck('historical_department_id')->unique();

        // Get positions and departments for filter dropdowns based on historical data
        $positionsList = EmployeePosition::whereIn('id', $historicalPositionIds)->orderBy('position')->get();
        $departmentsList = EmployeeDepartment::whereIn('id', $historicalDepartmentIds)->orderBy('department')->get();

        return view('evaluation.assign.performance.index', compact(
            'evaluations',
            'employeesList',
            'positionsList',
            'departmentsList',
            'currentMonth',
            'currentYear',
            'availableYears'
        ));
    }

    public function assign_performance_create($id)
    {
        // Get the current user
        $user = User::findOrFail($id);
        $userPosition = $user->position_id;
        $currentPosition = EmployeePosition::findOrFail($userPosition);
        $currentRank = $currentPosition->ranking;

        // Check if user is Director or GM (assuming these positions have specific IDs or titles)
        $isDirectorOrGM = in_array($currentPosition->title, ['Director', 'General Manager'])
            || in_array($currentPosition->id, [1, 2]); // Adjust IDs as needed

        // Initialize query for subordinates
        $subordinatesQuery = User::query()
            ->with('position', 'department');

        if ($isDirectorOrGM) {
            // Directors/GMs can see everyone with lower ranking
            $subordinatesQuery->whereHas('position', function ($query) use ($currentRank) {
                $query->where('ranking', '>', $currentRank);
            });
        } else {
            // Managers and below can only see their department with lower ranking
            $subordinatesQuery->whereHas('position', function ($query) use ($currentRank) {
                $query->where('ranking', '>', $currentRank);
            })->where('department_id', $user->department_id);
        }

        $subordinates = $subordinatesQuery->get();

        return view('evaluation.assign.performance.create', compact('subordinates'));
    }


    /**
     * Calculate the evaluation score for a user on a specific date.
     */
    private function calculateEvaluationScore($userId, $date)
    {
        // Get all evaluation entries for this user and date
        $evaluationEntries = EvaluationPerformance::where('user_id', $userId)
            ->whereDate('date', $date)
            ->with('weightPerformance')
            ->get();

        // Calculate raw score (sum of value * weight)
        $totalScore = 0;
        $totalWeight = 0;

        foreach ($evaluationEntries as $entry) {
            if ($entry->weightPerformance) {
                $totalScore += $entry->value * $entry->weightPerformance->weight;
                $totalWeight += $entry->weightPerformance->weight;
            }
        }

        // Calculate weighted average score (out of 100)
        // $weightedScore = $totalWeight > 0 ? ($totalScore / $totalWeight) * 100 / 3 : 0;
        $weightedScore =   $totalScore;
        // Get month and year from evaluation date
        $month = date('n', strtotime($date));
        $year = date('Y', strtotime($date));

        // Get reduction from warning letters
        $reductionAmount = $this->calculateWarningLetterReduction($userId, $month, $year);

        // Final score after reduction
        $finalScore = max(0, $weightedScore - $reductionAmount);



        return $finalScore;
    }

    /**
     * Calculate the reduction amount based on warning letters.
     */
    private function calculateWarningLetterReduction($userId, $month, $year)
    {
        // Find warning letters for this user in the specified month/year
        $warningLetters = WarningLetter::where('user_id', $userId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();

        // Get active reduction rules
        $reductionRules = RuleEvaluationReductionPerformance::where('status', 'Active')
            ->get();

        // Calculate total reduction
        $totalReduction = 0;
        foreach ($warningLetters as $letter) {
            // Find matching reduction rule
            $reduction = $reductionRules->where('type_id', $letter->type_id)->first();
            if ($reduction) {
                $totalReduction += $reduction->weight;
            }
        }

        return $totalReduction;
    }

    /**
     * Filter evaluations by month and year (AJAX endpoint)
     */
    public function assign_performance_filter(Request $request)
    {
        $month = $request->month;
        $year = $request->year;
        $userId = Auth::id();

        $evaluations = EvaluationPerformance::with(['user.position', 'user.department'])
            ->where('evaluator_id', $userId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($eval) {
                return [
                    'id' => $eval->id,
                    'user' => [
                        'name' => $eval->user->name,
                        'position' => ['name' => $eval->user->position->position ?? 'N/A'],
                        'department' => ['name' => $eval->user->department->department ?? 'N/A']
                    ],
                    'period' => $eval->date->format('F Y'),
                    'score' => $eval->final_score,
                    'created_at' => $eval->created_at->format('d-m-Y H:i')
                ];
            });

        return response()->json(['evaluations' => $evaluations]);
    }
    /**
     * Show detailed evaluation score
     */
    public function assign_performance_detail($id)
    {
        $evaluation = EvaluationPerformance::with([
            'user.position',
            'user.department',
            'details.weightPerformance.criteria',
            'messages',
            'reductions.warningLetter.rule'
        ])->findOrFail($id);

        // Hitung kriteria dan skor
        $criteriaScores = $evaluation->details->map(function ($detail) {
            return [
                'criteria' => $detail->weightPerformance->criteria->type,
                'weight' => $detail->weightPerformance->weight,
                'value' => $detail->value,
                'score' => $detail->score,
                'max_score' => 3 * $detail->weightPerformance->weight
            ];
        });

        // Hitung reduksi
        $reductionDetails = $evaluation->reductions->map(function ($reduction) {
            return [
                'letter_number' => $reduction->warningLetter->warning_letter_number,
                'type' => $reduction->warningLetter->rule->name,
                'date' => $reduction->warningLetter->created_at->format('Y-m-d'),
                'reduction' => $reduction->reduction_amount
            ];
        });

        return view('evaluation.assign.performance.detail', [
            'user' => $evaluation->user,
            'evaluation' => $evaluation,
            'criteriaScores' => $criteriaScores,
            'weightedScore' => $evaluation->total_score,
            'reductionDetails' => $reductionDetails,
            'totalReduction' => $evaluation->total_reduction,
            'finalScore' => $evaluation->final_score
        ]);
    }

    /**
     * Show the form for creating a new evaluation.
     */


    /**
     * Store a newly created evaluation in storage.
     */
    public function assign_performance_store(Request $request)
    {
        //dd($request->all());

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'evaluator_id' => 'required|exists:users,id',
            'evaluation_date' => 'required|date',
            'weight_performance_id' => 'required|array',
            'weight_performance_id.*' => 'exists:rule_evaluation_weight_performance,id',
            'value' => 'required|array',
            'value.*' => 'numeric|min:1|max:3',
        ]);



        // Cek evaluasi yang sudah ada
        $existingEvaluation = EvaluationPerformance::where('user_id', $request->user_id)
            ->whereDate('date', $request->evaluation_date)
            ->exists();

        if ($existingEvaluation) {
            return redirect()->back()->withErrors(['error' => 'An evaluation already exists for this employee on the selected date.']);
        }

        // 1. Buat evaluasi utama
        $evaluation = EvaluationPerformance::create([
            'user_id' => $request->user_id,
            'evaluator_id' => $request->evaluator_id,
            'date' => $request->evaluation_date,
        ]);

        // 2. Simpan detail penilaian
        $totalScore = 0;
        foreach ($request->weight_performance_id as $weight_id) {
            if (!isset($request->value[$weight_id])) {
                continue;
            }

            $weightPerformance = RuleEvaluationWeightPerformance::find($weight_id);


            $score = $request->value[$weight_id] * $weightPerformance->weight;

            EvaluationPerformanceDetail::create([
                'evaluation_id' => $evaluation->id,
                'weight_performance_id' => $weight_id,
                'value' => $request->value[$weight_id],
                'weight' => $weightPerformance->weight,

            ]);

            $totalScore += $score;
        }

        // 3. Hitung dan simpan reduksi dari warning letter
        $month = Carbon::parse($request->evaluation_date)->month;
        $year = Carbon::parse($request->evaluation_date)->year;
        $totalReduction = 0;

        $warningLetters = WarningLetter::where('user_id', $request->user_id)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        foreach ($warningLetters as $letter) {
            $reductionRule = RuleEvaluationReductionPerformance::where('type_id', $letter->type_id)
                ->first();

            if ($reductionRule) {
                EvaluationPerformanceReduction::create([
                    'evaluation_id' => $evaluation->id,
                    'warning_letter_id' => $letter->id,
                    'reduction_amount' => $reductionRule->weight
                ]);

                $totalReduction += $reductionRule->weight;
            }
        }

        // 4. Update total score dan reduksi
        $evaluation->update([
            'total_score' => $totalScore,
            'total_reduction' => $totalReduction
        ]);

        // 5. Simpan pesan
        if ($request->has('evaluation_messages')) {
            $messages = json_decode($request->evaluation_messages, true);

            foreach ($messages as $message) {
                EvaluationPerformanceMessage::create([
                    'evaluation_id' => $evaluation->id,
                    'message' => $message['message']
                ]);
            }
        }

        return redirect()->route('evaluation.assign.performance.index', Auth::user()->id)
            ->with('success', 'Performance evaluation has been assigned successfully');
    }



    /**
     * Check if an evaluation already exists for a user in a specific period.
     */
    public function check_existing_evaluation(Request $request)
    {
        $exists = EvaluationPerformance::where('user_id', $request->user_id)
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    /**
     * Get performance criteria for a specific position (for AJAX request)
     */
    public function get_performance_criteria(Request $request)
    {
        $positionId = $request->position_id;

        // Get performance criteria weights for this position
        $performanceWeights = RuleEvaluationWeightPerformance::where('position_id', $positionId)
            ->where('status', 'Active')
            ->with('criteria')
            ->get();

        return response()->json(['criteria' => $performanceWeights]);
    }

    /**
     * Get warning letters for a user in a specific month/year
     */
    public function get_warning_letters(Request $request)
    {
        $userId = $request->user_id;
        $month = $request->month;
        $year = $request->year;

        // Find warning letters for this user in the specified month/year
        $warningLetters = WarningLetter::where('user_id', $userId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->with('rule') // Load the rule relationship
            ->get();



        // Get active reduction rules
        $reductionRules = RuleEvaluationReductionPerformance::where('status', 'Active')
            ->with('warningLetterRule')
            ->get();



        // Calculate total reduction
        $totalReduction = 0;
        $reductionDetails = [];

        foreach ($warningLetters as $letter) {
            // Find matching reduction rule
            $reduction = $reductionRules->where('type_id', $letter->type_id)->first();

            if ($reduction) {
                $totalReduction += $reduction->weight;

                // Add to reduction details
                $reductionDetails[] = [
                    'letter_number' => $letter->warning_letter_number,
                    'type' => $letter->rule->name,
                    'date' => $letter->created_at->format('Y-m-d'),
                    'reduction' => $reduction->weight
                ];
            }
        }

        return response()->json([
            'warning_letters' => $warningLetters,
            'reduction_details' => $reductionDetails,
            'total_reduction' => $totalReduction
        ]);
    }





    /**
     * Show the form for editing the specified evaluation.
     */
    public function assign_performance_edit($id)
    {
        // Find the main evaluation with all related data
        $evaluation = EvaluationPerformance::with([
            'user.position',
            'user.department',
            'details.weightPerformance.criteria',
            'messages',
            'reductions.warningLetter.rule'
        ])->findOrFail($id);

        // Get the evaluated user with position
        $user = $evaluation->user;

        // Get performance criteria weights for this position
        $performanceWeights = RuleEvaluationWeightPerformance::where('position_id', $user->position_id)
            ->where('status', "Active")
            ->with('criteria')
            ->get();

        // Get existing values for each criteria
        $existingValues = [];
        foreach ($evaluation->details as $detail) {
            $existingValues[$detail->weight_performance_id] = $detail->value;
        }

        // Get warning letters for the evaluation period
        $month = Carbon::parse($evaluation->date)->month;
        $year = Carbon::parse($evaluation->date)->year;

        $warningLetters = WarningLetter::where('user_id', $user->id)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->with('rule')
            ->get();

        // Get reduction rules
        $reductionRules = RuleEvaluationReductionPerformance::where('status', 'Active')
            ->get();

        // Calculate reduction details
        $reductionDetails = [];
        $totalReduction = $evaluation->total_reduction;

        foreach ($warningLetters as $letter) {
            $reduction = $reductionRules->where('type_id', $letter->type_id)->first();
            if ($reduction) {
                $reductionDetails[] = [
                    'letter_number' => $letter->warning_letter_number,
                    'type' => $letter->rule->name,
                    'date' => $letter->created_at->format('Y-m-d'),
                    'reduction' => $reduction->weight
                ];
            }
        }

        return view('evaluation/assign/performance/update', compact(
            'evaluation',
            'user',
            'performanceWeights',
            'existingValues',
            'reductionDetails',
            'totalReduction'
        ));
    }

    /**
     * Update the specified evaluation in storage.
     */
    public function assign_performance_update(Request $request, $id)
    {
        $request->validate([
            'value' => 'required|array',
            'value.*' => 'numeric|min:1|max:3',
        ]);

        // Find the evaluation
        $evaluation = EvaluationPerformance::findOrFail($id);

        // Calculate total score
        $totalScore = 0;
        $totalReduction = $request->input('total_reduction', 0);

        // Update evaluation details
        foreach ($request->value as $weightId => $value) {
            // Find the detail with this weight_id
            $detail = EvaluationPerformanceDetail::where('evaluation_id', $evaluation->id)
                ->where('weight_performance_id', $weightId)
                ->first();

            $weightPerformance = RuleEvaluationWeightPerformance::find($weightId);
            $score = $value * $weightPerformance->weight;
            $totalScore += $score;

            if ($detail) {
                // Update existing detail
                $detail->update([
                    'value' => $value,
                    'weight' => $weightPerformance->weight,
                    'score' => $score
                ]);
            } else {
                // Create new detail if it doesn't exist
                EvaluationPerformanceDetail::create([
                    'evaluation_id' => $evaluation->id,
                    'weight_performance_id' => $weightId,
                    'value' => $value,
                    'weight' => $weightPerformance->weight,
                    'score' => $score
                ]);
            }
        }

        // Update evaluation messages if provided
        if ($request->has('evaluation_messages')) {
            $messages = json_decode($request->evaluation_messages, true);

            // First delete existing messages
            EvaluationPerformanceMessage::where('evaluation_id', $evaluation->id)->delete();

            // Add new messages
            foreach ($messages as $message) {
                EvaluationPerformanceMessage::create([
                    'evaluation_id' => $evaluation->id,
                    'message' => $message['message']
                ]);
            }
        }

        // Update main evaluation record
        $evaluation->update([
            'total_score' => $totalScore,
            'total_reduction' => $totalReduction,
            'final_score' => max(0, $totalScore - $totalReduction)
        ]);

        return redirect()->route('evaluation.assign.performance.index', Auth::user()->id)
            ->with('success', 'Performance evaluation has been updated successfully');
    }


    /**
     * Display a listing of performance evaluation reports with grade functionality.
     */
    public function report_performance_index(Request $request)
    {
        // Get current year for default filtering
        $currentYear = date('Y');

        // Get filter parameters
        $employeeFilter = $request->input('employee');
        $positionFilter = $request->input('position');
        $departmentFilter = $request->input('department');
        $yearFilter = $request->input('year', $currentYear);

        // Get available years from evaluations
        $availableYears = EvaluationPerformance::select(DB::raw('DISTINCT YEAR(date) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [$currentYear];
        }

        // Base query for evaluations in the selected year
        $evaluationsQuery = EvaluationPerformance::whereYear('date', $yearFilter);

        // Apply employee filter if provided
        if ($employeeFilter) {
            $evaluationsQuery->where('user_id', $employeeFilter);
        }

        // Get all evaluations for the year
        $evaluations = $evaluationsQuery->get();

        // Group evaluations by user
        $evaluationsByUser = $evaluations->groupBy('user_id');

        // Prepare user data with their historical position and department
        $userHistoricalData = [];
        $userIds = $evaluations->pluck('user_id')->unique()->toArray();

        foreach ($userIds as $userId) {
            // Find the most recent transfer history for the user
            $history = history_transfer_employee::where('users_id', $userId)
                ->where('created_at', '<', $yearFilter . '-12-31')
                ->orderBy('created_at', 'desc')
                ->first();

            $user = User::find($userId);

            if ($history) {
                // Use historical position and department
                $userHistoricalData[$userId] = [
                    'position_id' => $history->new_position_id,
                    'department_id' => $history->new_department_id,
                    'position' => EmployeePosition::find($history->new_position_id),
                    'department' => EmployeeDepartment::find($history->new_department_id)
                ];
            } else {
                // No history found, use current position and department
                $userHistoricalData[$userId] = [
                    'position_id' => $user->position_id,
                    'department_id' => $user->department_id,
                    'position' => $user->position,
                    'department' => $user->department
                ];
            }
        }

        // Filter users based on position and department
        $filteredUserIds = $userIds;

        if ($positionFilter) {
            $filteredUserIds = array_filter($filteredUserIds, function ($userId) use ($userHistoricalData, $positionFilter) {
                return isset($userHistoricalData[$userId]) && $userHistoricalData[$userId]['position_id'] == $positionFilter;
            });
        }

        if ($departmentFilter) {
            $filteredUserIds = array_filter($filteredUserIds, function ($userId) use ($userHistoricalData, $departmentFilter) {
                return isset($userHistoricalData[$userId]) && $userHistoricalData[$userId]['department_id'] == $departmentFilter;
            });
        }

        // Calculate final evaluations for filtered users
        $finalEvaluations = [];
        foreach ($filteredUserIds as $userId) {
            if (!isset($evaluationsByUser[$userId])) {
                continue;
            }

            $userEvaluations = $evaluationsByUser[$userId];

            // Total score is the sum of total scores for all evaluations
            $totalScore = $userEvaluations->sum('total_score') / 12;

            // Total reduction is the sum of total reductions
            $totalReduction = $userEvaluations->sum('total_reduction');

            // Final score is total score minus total reduction
            $finalScore = ($totalScore - $totalReduction);


            $user = User::find($userId);

            // Add historical position and department to the user object
            $historicalUser = clone $user;
            $historicalUser->historical_position = $userHistoricalData[$userId]['position'];
            $historicalUser->historical_department = $userHistoricalData[$userId]['department'];

            // Get performance grade rules
            $gradeRules = RulePerformanceGrade::orderBy('min_score', 'desc')->get();

            // Determine grade based on final score
            $grade = $gradeRules->first(function ($rule) use ($finalScore) {
                $belowMaxOrNoMax = ($rule->max_score === null || $finalScore <= $rule->max_score);
                $aboveMinOrNoMin = ($rule->min_score === null || $finalScore >= $rule->min_score);
                return $aboveMinOrNoMin && $belowMaxOrNoMax;
            });

            $finalEvaluations[] = (object)[
                'user_id' => $userId,
                'user' => $historicalUser,
                'total_score' => $totalScore,
                'total_reduction' => $totalReduction,
                'final_score' => $finalScore,
                'month_count' => $userEvaluations->count(),
                'year' => $yearFilter,
                'grade' => $grade ? $grade->grade : 'N/A',
                'grade_description' => $grade ? $grade->description : 'Not Available'
            ];
        }

        // Get collection of all historical positions and departments for dropdowns
        $historicalPositionIds = collect($userHistoricalData)->pluck('position_id')->unique()->filter();
        $historicalDepartmentIds = collect($userHistoricalData)->pluck('department_id')->unique()->filter();

        // Get position and department lists for dropdowns
        $positionsList = EmployeePosition::whereIn('id', $historicalPositionIds)
            ->select('id', 'position')
            ->orderBy('position')
            ->get();

        $departmentsList = EmployeeDepartment::whereIn('id', $historicalDepartmentIds)
            ->select('id', 'department')
            ->orderBy('department')
            ->get();

        // Get employee list for employee dropdown
        $employeesList = User::whereIn('id', $userIds)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('evaluation.report.performance.index', compact(
            'finalEvaluations',
            'employeesList',
            'positionsList',
            'departmentsList',
            'currentYear',
            'availableYears',
            'gradeRules'
        ));
    }


    public function exportExcelAll()
    {
        // Get filter parameters
        $employee_id = request('employee');
        $position_id = request('position');
        $department_id = request('department');
        $year = request('year', date('Y'));

        // Get employees based on filters
        $employeeIds = EvaluationPerformance::whereYear('date', $year)
            ->pluck('user_id')
            ->unique();

        $employees = User::query()
            ->with(['position', 'department'])
            ->whereIn('id', $employeeIds)
            ->when($employee_id, function ($query) use ($employee_id) {
                return $query->where('id', $employee_id);
            })
            ->when($position_id, function ($query) use ($position_id) {
                return $query->where('position_id', $position_id);
            })
            ->when($department_id, function ($query) use ($department_id) {
                return $query->where('department_id', $department_id);
            })
            ->get();

        // Month names for display
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Performance Report');

        // Define column width
        $sheet->getColumnDimension('A')->setWidth(5);  // No
        $sheet->getColumnDimension('B')->setWidth(20); // Employee
        $sheet->getColumnDimension('C')->setWidth(15); // Position
        $sheet->getColumnDimension('D')->setWidth(15); // Department
        $sheet->getColumnDimension('E')->setWidth(15); // Period

        // Style for headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '0062CC']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
            ]
        ];

        // Style for title
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];

        // Add report title
        $sheet->mergeCells('A1:E2');
        $sheet->setCellValue('A1', 'PT. TIMUR JAYA INDOSTEEL - PERFORMANCE EVALUATION ' . $year);
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        // Excel row counter
        $row = 4;

        // Process each employee
        foreach ($employees as $employeeIndex => $user) {
            // Get all criteria types and weights
            $criteria = RuleEvaluationWeightPerformance::with('criteria')
                ->where('Status', "Active")
                ->get()
                ->groupBy('criteria.type')
                ->map(function ($items) {
                    return [
                        'id' => $items->first()->id,
                        'name' => $items->first()->criteria->type,
                        'weight' => $items->first()->weight
                    ];
                })
                ->values();

            $criteriaList = $criteria->toArray();

            // Initialize monthly data structure
            $monthlyData = [];
            foreach (range(0, 11) as $monthIndex) {
                $monthlyData[$monthIndex] = [
                    'scores' => [],
                    'rawScore' => 0,
                    'finalScore' => 0,
                    'deductions' => 0
                ];
            }

            // Get all evaluations for the user in the selected year
            $evaluations = EvaluationPerformance::with(['details.weightPerformance.criteria', 'reductions.warningLetter'])
                ->where('user_id', $user->id)
                ->whereYear('date', $year)
                ->get();

            // Process each month
            foreach (range(1, 12) as $monthNumber) {
                $monthIndex = $monthNumber - 1;
                $monthEvaluations = $evaluations->filter(function ($eval) use ($monthNumber) {
                    $date = is_string($eval->date) ? Carbon::parse($eval->date) : $eval->date;
                    return $date->month == $monthNumber;
                });

                if ($monthEvaluations->isNotEmpty()) {
                    // Process criteria scores
                    foreach ($criteriaList as $criterion) {
                        $values = [];
                        $scores = [];

                        foreach ($monthEvaluations as $eval) {
                            $details = $eval->details->filter(function ($detail) use ($criterion) {
                                return $detail->weightPerformance->criteria->type == $criterion['name'];
                            });

                            foreach ($details as $detail) {
                                $values[] = $detail->value;
                                $scores[] = $detail->value * $criterion['weight'];
                            }
                        }

                        if (!empty($values)) {
                            $avgValue = array_sum($values) / count($values);
                            $avgScore = array_sum($scores) / count($scores);

                            $monthlyData[$monthIndex]['scores'][] = [
                                'name' => $criterion['name'],
                                'value' => $avgValue,
                                'score' => $avgScore
                            ];

                            $monthlyData[$monthIndex]['rawScore'] += $avgScore;
                        }
                    }
                }
            }

            // Fixed version - always divide by 12 months:
            $totalRawScore = array_sum(array_column($monthlyData, 'rawScore'));
            $overallRawAverage = $totalRawScore / 12;

            // Reset the deductions in monthly data to avoid duplicate counting
            foreach ($monthlyData as $monthIndex => $month) {
                $monthlyData[$monthIndex]['deductions'] = 0;
            }

            // Process yearly reductions
            $reductionRules = RuleEvaluationReductionPerformance::where('Status', "Active")->get();
            $yearlyReductions = [];
            $totalDeductions = 0;

            foreach ($reductionRules as $rule) {
                $ruleData = [
                    'id' => $rule->id,
                    'name' => $rule->warningLetterRule->name ?? $rule->name,
                    'weight' => $rule->weight,
                    'monthly' => array_fill(1, 12, ['count' => 0, 'reduction' => 0]),
                    'total_count' => 0,
                    'total_reduction' => 0
                ];

                // Get warning letters that have been applied to evaluations
                $warningLetters = WarningLetter::where('user_id', $user->id)
                    ->where('type_id', $rule->type_id)
                    ->whereYear('created_at', $year)
                    ->whereHas('evaluationReductions')
                    ->get();

                foreach ($warningLetters as $letter) {
                    // FIXED: Use the warning letter's actual date for counting
                    $letterDate = $letter->created_at ?? now();
                    $monthNumber = $letterDate->month;

                    // Sum only the actual reductions applied
                    $reductionAmount = $letter->evaluationReductions
                        ->filter(function ($reduction) use ($year) {
                            // Only count reductions where the associated evaluation is from this year
                            $evaluation = $reduction->evaluation;
                            if (!$evaluation || !$evaluation->date) return false;
                            return Carbon::parse($evaluation->date)->year == $year;
                        })
                        ->sum('reduction_amount');

                    $ruleData['monthly'][$monthNumber]['count']++;
                    $ruleData['monthly'][$monthNumber]['reduction'] += $reductionAmount;
                    $ruleData['total_count']++;
                    $ruleData['total_reduction'] += $reductionAmount;
                }

                $yearlyReductions[$rule->id] = $ruleData;
                $totalDeductions += $ruleData['total_reduction'];
            }

            // Apply deductions based on warning letter dates, not evaluation dates
            foreach ($yearlyReductions as $ruleId => $ruleData) {
                foreach ($ruleData['monthly'] as $monthNumber => $monthData) {
                    $monthIndex = $monthNumber - 1;
                    if (isset($monthlyData[$monthIndex])) {
                        $monthlyData[$monthIndex]['deductions'] += $monthData['reduction'];
                    }
                }
            }

            // Recalculate final scores for each month
            foreach ($monthlyData as $monthIndex => $month) {
                $monthlyData[$monthIndex]['finalScore'] = max(
                    0,
                    $monthlyData[$monthIndex]['rawScore'] - $monthlyData[$monthIndex]['deductions']
                );
            }

            // Calculate the final score with the correctly summed deductions
            $overallAverage = max(0, $overallRawAverage - $totalDeductions);

            // Calculate criterion averages
            $averageValues = [];
            $averageTotals = [];

            foreach ($criteriaList as $criterion) {
                $sumValues = 0;
                $sumScores = 0;
                $count = 0;

                foreach ($monthlyData as $month) {
                    $criterionData = collect($month['scores'])->firstWhere('name', $criterion['name']);
                    if ($criterionData) {
                        $sumValues += $criterionData['value'];
                        $sumScores += $criterionData['score'];
                        $count++;
                    }
                }

                $averageValues[$criterion['name']] = $count ? $sumValues / $count : 0;
                $averageTotals[$criterion['name']] = $count ? $sumScores / $count : 0;
            }

            // Get grade
            $finalScore = $overallAverage;
            $grade = RulePerformanceGrade::query()
                ->where(function ($q) use ($finalScore) {
                    $q->where(function ($sub) use ($finalScore) {
                        $sub->whereNotNull('min_score')
                            ->whereNotNull('max_score')
                            ->where('min_score', '<=', $finalScore)
                            ->where('max_score', '>=', $finalScore);
                    })->orWhere(function ($sub) use ($finalScore) {
                        $sub->whereNotNull('min_score')
                            ->whereNull('max_score')
                            ->where('min_score', '<=', $finalScore);
                    })->orWhere(function ($sub) use ($finalScore) {
                        $sub->whereNull('min_score')
                            ->whereNotNull('max_score')
                            ->where('max_score', '>=', $finalScore);
                    });
                })
                ->orderBy('min_score', 'asc')
                ->first();

            // Fallback if no grade was found
            $gradeValue = $grade ? $grade->grade : '?';
            $gradeDescription = $grade ? $grade->description : 'Undefined performance';

            // Add employee header
            $sheet->setCellValue('A' . $row, 'Employee #' . ($employeeIndex + 1));
            $sheet->mergeCells('A' . $row . ':E' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;

            // Add employee information
            $sheet->setCellValue('A' . $row, 'Name:');
            $sheet->setCellValue('B' . $row, $user->name);
            $row++;

            $sheet->setCellValue('A' . $row, 'Position:');
            $sheet->setCellValue('B' . $row, $user->position->position ?? 'N/A');
            $row++;

            $sheet->setCellValue('A' . $row, 'Department:');
            $sheet->setCellValue('B' . $row, $user->department->department ?? 'N/A');
            $row++;

            $sheet->setCellValue('A' . $row, 'Final Score:');
            $sheet->setCellValue('B' . $row, number_format($overallAverage, 0));
            $row++;

            $sheet->setCellValue('A' . $row, 'Grade:');
            $sheet->setCellValue('B' . $row, $gradeValue . ' - ' . $gradeDescription);
            $row++;

            // Performance data header (month columns)
            $row++;
            $sheet->setCellValue('A' . $row, 'No');
            $sheet->setCellValue('B' . $row, 'Criteria');
            $sheet->setCellValue('C' . $row, 'Weight');

            $col = 'D';
            foreach ($monthNames as $monthIndex => $month) {
                $sheet->setCellValue($col++ . $row, $month . ' Score');
                $sheet->setCellValue($col++ . $row, $month . ' Total');
            }
            $sheet->setCellValue($col++ . $row, 'Final Score');
            $sheet->setCellValue($col . $row, 'Final Total');

            $lastColumn = $col;
            $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->applyFromArray($headerStyle);
            $row++;

            // Performance criteria data
            foreach ($criteriaList as $index => $criterion) {
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $criterion['name']);
                $sheet->setCellValue('C' . $row, $criterion['weight']);

                $col = 'D';
                // For each month, add score and total
                foreach ($monthlyData as $monthIndex => $month) {
                    $criteriaScore = collect($month['scores'])->firstWhere('name', $criterion['name']);
                    $value = $criteriaScore['value'] ?? null;
                    $total = $criteriaScore['score'] ?? null;

                    $sheet->setCellValue($col++ . $row, $value !== null ? number_format($value, 1) : '');
                    $sheet->setCellValue($col++ . $row, $total !== null ? number_format($total, 0) : '');
                }

                // Add final averages
                $finalValue = $averageValues[$criterion['name']] ?? 0;
                $finalTotal = $averageTotals[$criterion['name']] ?? 0;

                $sheet->setCellValue($col++ . $row, number_format($finalValue, 1));
                $sheet->setCellValue($col . $row, number_format($finalTotal, 0));

                $row++;
            }

            // Total Evaluation Score row
            $sheet->setCellValue('A' . $row, '');
            $sheet->setCellValue('B' . $row, 'TOTAL EVALUATION SCORE');
            $sheet->setCellValue('C' . $row, '');
            $sheet->getStyle('B' . $row)->getFont()->setBold(true);

            $col = 'D';
            // For each month, add empty cell and total raw score
            foreach ($monthlyData as $monthIndex => $month) {
                $sheet->setCellValue($col++ . $row, '');
                $sheet->setCellValue($col++ . $row, $month['rawScore'] ? number_format($month['rawScore'], 0) : '');
            }

            // Add final raw score
            $sheet->setCellValue($col++ . $row, '');
            $sheet->setCellValue($col . $row, number_format($overallRawAverage, 0));
            $sheet->getStyle($col . $row)->getFont()->setBold(true);

            $row++;

            // Deductions section header
            $sheet->setCellValue('A' . $row, '');
            $sheet->mergeCells('A' . $row . ':' . $lastColumn . $row);
            $sheet->setCellValue('A' . $row, 'WARNING LETTERS & DEDUCTIONS');
            $sheet->getStyle('A' . $row)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'DC3545']]
            ]);
            $row++;

            // Add warning letters to excel
            foreach ($yearlyReductions as $ruleId => $ruleData) {
                $sheet->setCellValue('A' . $row, '');
                $sheet->setCellValue('B' . $row, $ruleData['name']);
                $sheet->setCellValue('C' . $row, '-' . $ruleData['weight']);
                $sheet->getStyle('C' . $row)->getFont()->getColor()->setRGB('DC3545');
                $col = 'D';
                // For each month, add warning count and reduction amount
                foreach ($monthNames as $monthIndex => $month) {
                    $monthNumber = $monthIndex + 1;
                    $monthData = $ruleData['monthly'][$monthNumber] ?? ['count' => 0, 'reduction' => 0];
                    $hasDeduction = $monthData['count'] > 0;

                    $sheet->setCellValue($col++ . $row, $hasDeduction ? $monthData['count'] : '');
                    $sheet->setCellValue($col++ . $row, $hasDeduction ? '-' . $monthData['reduction'] : '0');
                    if ($hasDeduction) {
                        $prevCol = chr(ord($col) - 1);
                        $sheet->getStyle($prevCol . $row)->getFont()->getColor()->setRGB('DC3545');
                    }
                }

                // Add final warning count and reduction
                $sheet->setCellValue($col++ . $row, $ruleData['total_count'] > 0 ? $ruleData['total_count'] : '');
                $sheet->setCellValue($col . $row, '-' . $ruleData['total_reduction']);
                $sheet->getStyle($col . $row)->getFont()->getColor()->setRGB('DC3545');

                $row++;
            }

            // Total Deductions row
            $sheet->setCellValue('A' . $row, '');
            $sheet->setCellValue('B' . $row, 'TOTAL DEDUCTIONS');
            $sheet->setCellValue('C' . $row, '');
            $sheet->getStyle('B' . $row)->getFont()->setBold(true);

            $col = 'D';
            // For each month, add empty cell and deduction amount
            foreach ($monthlyData as $monthIndex => $month) {
                $deduction = $month['deductions'] ?? 0;
                $sheet->setCellValue($col++ . $row, '');
                $sheet->setCellValue($col++ . $row, $deduction > 0 ? '-' . number_format($deduction, 0) : '0');
            }

            // Add final deductions
            $sheet->setCellValue($col++ . $row, '');
            $sheet->setCellValue($col . $row, '-' . number_format($totalDeductions, 0));
            $sheet->getStyle($col . $row)->getFont()->setBold(true);

            $row++;

            // Final Score row
            $sheet->setCellValue('A' . $row, '');
            $sheet->setCellValue('B' . $row, 'FINAL SCORE');
            $sheet->setCellValue('C' . $row, '');
            $sheet->getStyle('B' . $row)->getFont()->setBold(true);

            $col = 'D';
            // For each month, add empty cell and final score
            foreach ($monthlyData as $monthIndex => $month) {
                $sheet->setCellValue($col++ . $row, '');
                $sheet->setCellValue($col++ . $row, $month['finalScore'] ? number_format($month['finalScore'], 0) : '');
            }

            // Add overall average
            $sheet->setCellValue($col++ . $row, '');
            $sheet->setCellValue($col . $row, number_format($overallAverage, 0));
            $sheet->getStyle($col . $row)->getFont()->setBold(true);

            $row++;

            // Add separator between employees
            $sheet->mergeCells('A' . $row . ':' . $lastColumn . $row);
            $sheet->getStyle('A' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EEEEEE');
            $row += 2; // Add extra blank row
        }

        // Set output filename
        $filename = 'Performance_Report_' . $year . '_' . date('YmdHis') . '.xlsx';

        // Create writer and output
        $writer = new Xlsx($spreadsheet);

        // Save to file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }


    public function exportEmployeeExcel(Request $request)
    {
        // Get employee ID and year from request (supports both POST and GET)
        $employee_id = $request->get('id');  // Get from query parameters instead of route parameters
        $year = $request->get('year', date('Y'));

        if (!$employee_id) {
            return back()->with('error', 'Employee ID is required for export');
        }

        // Get employee data
        $employee = User::with(['position', 'department'])
            ->findOrFail($employee_id);

        // Month names for display
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Performance Report');

        // Define column width
        $sheet->getColumnDimension('A')->setWidth(5);  // No
        $sheet->getColumnDimension('B')->setWidth(20); // Employee
        $sheet->getColumnDimension('C')->setWidth(15); // Position
        $sheet->getColumnDimension('D')->setWidth(15); // Department
        $sheet->getColumnDimension('E')->setWidth(15); // Period

        // Style for headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '0062CC']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
            ]
        ];

        // Style for title
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];

        // Add report title
        $sheet->mergeCells('A1:E2');
        $sheet->setCellValue('A1', 'PT. TIMUR JAYA INDOSTEEL - PERFORMANCE EVALUATION ' . $year);
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        // Excel row counter
        $row = 4;

        // Add employee header - similar to exportExcelAll
        $sheet->setCellValue('A' . $row, 'Employee Details');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;

        // Get all criteria types and weights
        $criteria = RuleEvaluationWeightPerformance::with('criteria')
            ->where('Status', "Active")
            ->get()
            ->groupBy('criteria.type')
            ->map(function ($items) {
                return [
                    'id' => $items->first()->id,
                    'name' => $items->first()->criteria->type,
                    'weight' => $items->first()->weight
                ];
            })
            ->values();

        $criteriaList = $criteria->toArray();

        // Initialize monthly data structure
        $monthlyData = [];
        foreach (range(0, 11) as $monthIndex) {
            $monthlyData[$monthIndex] = [
                'scores' => [],
                'rawScore' => 0,
                'finalScore' => 0,
                'deductions' => 0
            ];
        }

        // Get all evaluations for the user in the selected year
        $evaluations = EvaluationPerformance::with(['details.weightPerformance.criteria', 'reductions.warningLetter'])
            ->where('user_id', $employee->id)
            ->whereYear('date', $year)
            ->get();

        // Process each month
        foreach (range(1, 12) as $monthNumber) {
            $monthIndex = $monthNumber - 1;
            $monthEvaluations = $evaluations->filter(function ($eval) use ($monthNumber) {
                $date = is_string($eval->date) ? Carbon::parse($eval->date) : $eval->date;
                return $date->month == $monthNumber;
            });

            if ($monthEvaluations->isNotEmpty()) {
                // Process criteria scores
                foreach ($criteriaList as $criterion) {
                    $values = [];
                    $scores = [];

                    foreach ($monthEvaluations as $eval) {
                        $details = $eval->details->filter(function ($detail) use ($criterion) {
                            return $detail->weightPerformance->criteria->type == $criterion['name'];
                        });

                        foreach ($details as $detail) {
                            $values[] = $detail->value;
                            $scores[] = $detail->value * $criterion['weight'];
                        }
                    }

                    if (!empty($values)) {
                        $avgValue = array_sum($values) / count($values);
                        $avgScore = array_sum($scores) / count($scores);

                        $monthlyData[$monthIndex]['scores'][] = [
                            'name' => $criterion['name'],
                            'value' => $avgValue,
                            'score' => $avgScore
                        ];

                        $monthlyData[$monthIndex]['rawScore'] += $avgScore;
                    }
                }
            }
        }

        // Fixed version - always divide by 12 months:
        $totalRawScore = array_sum(array_column($monthlyData, 'rawScore'));
        $overallRawAverage = $totalRawScore / 12;

        // Reset the deductions in monthly data to avoid duplicate counting
        foreach ($monthlyData as $monthIndex => $month) {
            $monthlyData[$monthIndex]['deductions'] = 0;
        }

        // Process yearly reductions
        $reductionRules = RuleEvaluationReductionPerformance::where('Status', "Active")->get();
        $yearlyReductions = [];
        $totalDeductions = 0;

        foreach ($reductionRules as $rule) {
            $ruleData = [
                'id' => $rule->id,
                'name' => $rule->warningLetterRule->name ?? $rule->name,
                'weight' => $rule->weight,
                'monthly' => array_fill(1, 12, ['count' => 0, 'reduction' => 0]),
                'total_count' => 0,
                'total_reduction' => 0
            ];

            // Get warning letters that have been applied to evaluations
            $warningLetters = WarningLetter::where('user_id', $employee->id)
                ->where('type_id', $rule->type_id)
                ->whereYear('created_at', $year)
                ->whereHas('evaluationReductions')
                ->get();

            foreach ($warningLetters as $letter) {
                // FIXED: Use the warning letter's actual date for counting
                $letterDate = $letter->created_at ?? now();
                $monthNumber = $letterDate->month;

                // Sum only the actual reductions applied
                $reductionAmount = $letter->evaluationReductions
                    ->filter(function ($reduction) use ($year) {
                        // Only count reductions where the associated evaluation is from this year
                        $evaluation = $reduction->evaluation;
                        if (!$evaluation || !$evaluation->date) return false;
                        return Carbon::parse($evaluation->date)->year == $year;
                    })
                    ->sum('reduction_amount');

                $ruleData['monthly'][$monthNumber]['count']++;
                $ruleData['monthly'][$monthNumber]['reduction'] += $reductionAmount;
                $ruleData['total_count']++;
                $ruleData['total_reduction'] += $reductionAmount;
            }

            $yearlyReductions[$rule->id] = $ruleData;
            $totalDeductions += $ruleData['total_reduction'];
        }

        // Apply deductions based on warning letter dates, not evaluation dates
        foreach ($yearlyReductions as $ruleId => $ruleData) {
            foreach ($ruleData['monthly'] as $monthNumber => $monthData) {
                $monthIndex = $monthNumber - 1;
                if (isset($monthlyData[$monthIndex])) {
                    $monthlyData[$monthIndex]['deductions'] += $monthData['reduction'];
                }
            }
        }

        // Recalculate final scores for each month
        foreach ($monthlyData as $monthIndex => $month) {
            $monthlyData[$monthIndex]['finalScore'] = max(
                0,
                $monthlyData[$monthIndex]['rawScore'] - $monthlyData[$monthIndex]['deductions']
            );
        }

        // Calculate the final score with the correctly summed deductions
        $overallAverage = max(0, $overallRawAverage - $totalDeductions);

        // Calculate criterion averages
        $averageValues = [];
        $averageTotals = [];

        foreach ($criteriaList as $criterion) {
            $sumValues = 0;
            $sumScores = 0;
            $count = 0;

            foreach ($monthlyData as $month) {
                $criterionData = collect($month['scores'])->firstWhere('name', $criterion['name']);
                if ($criterionData) {
                    $sumValues += $criterionData['value'];
                    $sumScores += $criterionData['score'];
                    $count++;
                }
            }

            $averageValues[$criterion['name']] = $count ? $sumValues / $count : 0;
            $averageTotals[$criterion['name']] = $count ? $sumScores / $count : 0;
        }

        // Get grade
        $finalScore = $overallAverage;
        $grade = RulePerformanceGrade::query()
            ->where(function ($q) use ($finalScore) {
                $q->where(function ($sub) use ($finalScore) {
                    $sub->whereNotNull('min_score')
                        ->whereNotNull('max_score')
                        ->where('min_score', '<=', $finalScore)
                        ->where('max_score', '>=', $finalScore);
                })->orWhere(function ($sub) use ($finalScore) {
                    $sub->whereNotNull('min_score')
                        ->whereNull('max_score')
                        ->where('min_score', '<=', $finalScore);
                })->orWhere(function ($sub) use ($finalScore) {
                    $sub->whereNull('min_score')
                        ->whereNotNull('max_score')
                        ->where('max_score', '>=', $finalScore);
                });
            })
            ->orderBy('min_score', 'asc')
            ->first();

        // Fallback if no grade was found
        $gradeValue = $grade ? $grade->grade : '?';
        $gradeDescription = $grade ? $grade->description : 'Undefined performance';

        // Add employee information
        $sheet->setCellValue('A' . $row, 'Name:');
        $sheet->setCellValue('B' . $row, $employee->name);
        $row++;

        $sheet->setCellValue('A' . $row, 'Position:');
        $sheet->setCellValue('B' . $row, $employee->position->position ?? 'N/A');
        $row++;

        $sheet->setCellValue('A' . $row, 'Department:');
        $sheet->setCellValue('B' . $row, $employee->department->department ?? 'N/A');
        $row++;

        $sheet->setCellValue('A' . $row, 'Final Score:');
        $sheet->setCellValue('B' . $row, number_format($overallAverage, 0));
        $row++;

        $sheet->setCellValue('A' . $row, 'Grade:');
        $sheet->setCellValue('B' . $row, $gradeValue . ' - ' . $gradeDescription);
        $row++;

        // Performance data header (month columns)
        $row++;
        $sheet->setCellValue('A' . $row, 'No');
        $sheet->setCellValue('B' . $row, 'Criteria');
        $sheet->setCellValue('C' . $row, 'Weight');

        $col = 'D';
        foreach ($monthNames as $monthIndex => $month) {
            $sheet->setCellValue($col++ . $row, $month . ' Score');
            $sheet->setCellValue($col++ . $row, $month . ' Total');
        }
        $sheet->setCellValue($col++ . $row, 'Final Score');
        $sheet->setCellValue($col . $row, 'Final Total');

        $lastColumn = $col;
        $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->applyFromArray($headerStyle);
        $row++;

        // Performance criteria data
        foreach ($criteriaList as $index => $criterion) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $criterion['name']);
            $sheet->setCellValue('C' . $row, $criterion['weight']);

            $col = 'D';
            // For each month, add score and total
            foreach ($monthlyData as $monthIndex => $month) {
                $criteriaScore = collect($month['scores'])->firstWhere('name', $criterion['name']);
                $value = $criteriaScore['value'] ?? null;
                $total = $criteriaScore['score'] ?? null;

                $sheet->setCellValue($col++ . $row, $value !== null ? number_format($value, 1) : '');
                $sheet->setCellValue($col++ . $row, $total !== null ? number_format($total, 0) : '');
            }

            // Add final averages
            $finalValue = $averageValues[$criterion['name']] ?? 0;
            $finalTotal = $averageTotals[$criterion['name']] ?? 0;

            $sheet->setCellValue($col++ . $row, number_format($finalValue, 1));
            $sheet->setCellValue($col . $row, number_format($finalTotal, 0));

            $row++;
        }

        // Total Evaluation Score row
        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('B' . $row, 'TOTAL EVALUATION SCORE');
        $sheet->setCellValue('C' . $row, '');
        $sheet->getStyle('B' . $row)->getFont()->setBold(true);

        $col = 'D';
        // For each month, add empty cell and total raw score
        foreach ($monthlyData as $monthIndex => $month) {
            $sheet->setCellValue($col++ . $row, '');
            $sheet->setCellValue($col++ . $row, $month['rawScore'] ? number_format($month['rawScore'], 0) : '');
        }

        // Add final raw score
        $sheet->setCellValue($col++ . $row, '');
        $sheet->setCellValue($col . $row, number_format($overallRawAverage, 0));
        $sheet->getStyle($col . $row)->getFont()->setBold(true);

        $row++;

        // Deductions section header
        $sheet->setCellValue('A' . $row, '');
        $sheet->mergeCells('A' . $row . ':' . $lastColumn . $row);
        $sheet->setCellValue('A' . $row, 'WARNING LETTERS & DEDUCTIONS');
        $sheet->getStyle('A' . $row)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'DC3545']]
        ]);
        $row++;

        // Add warning letters to excel
        foreach ($yearlyReductions as $ruleId => $ruleData) {
            $sheet->setCellValue('A' . $row, '');
            $sheet->setCellValue('B' . $row, $ruleData['name']);
            $sheet->setCellValue('C' . $row, '-' . $ruleData['weight']);
            $sheet->getStyle('C' . $row)->getFont()->getColor()->setRGB('DC3545');
            $col = 'D';
            // For each month, add warning count and reduction amount
            foreach ($monthNames as $monthIndex => $month) {
                $monthNumber = $monthIndex + 1;
                $monthData = $ruleData['monthly'][$monthNumber] ?? ['count' => 0, 'reduction' => 0];
                $hasDeduction = $monthData['count'] > 0;

                $sheet->setCellValue($col++ . $row, $hasDeduction ? $monthData['count'] : '');
                $sheet->setCellValue($col++ . $row, $hasDeduction ? '-' . $monthData['reduction'] : '0');
                if ($hasDeduction) {
                    $prevCol = chr(ord($col) - 1);
                    $sheet->getStyle($prevCol . $row)->getFont()->getColor()->setRGB('DC3545');
                }
            }

            // Add final warning count and reduction
            $sheet->setCellValue($col++ . $row, $ruleData['total_count'] > 0 ? $ruleData['total_count'] : '');
            $sheet->setCellValue($col . $row, '-' . $ruleData['total_reduction']);
            $sheet->getStyle($col . $row)->getFont()->getColor()->setRGB('DC3545');

            $row++;
        }

        // Total Deductions row
        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('B' . $row, 'TOTAL DEDUCTIONS');
        $sheet->setCellValue('C' . $row, '');
        $sheet->getStyle('B' . $row)->getFont()->setBold(true);

        $col = 'D';
        // For each month, add empty cell and deduction amount
        foreach ($monthlyData as $monthIndex => $month) {
            $deduction = $month['deductions'] ?? 0;
            $sheet->setCellValue($col++ . $row, '');
            $sheet->setCellValue($col++ . $row, $deduction > 0 ? '-' . number_format($deduction, 0) : '0');
        }

        // Add final deductions
        $sheet->setCellValue($col++ . $row, '');
        $sheet->setCellValue($col . $row, '-' . number_format($totalDeductions, 0));
        $sheet->getStyle($col . $row)->getFont()->setBold(true);

        $row++;

        // Final Score row
        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('B' . $row, 'FINAL SCORE');
        $sheet->setCellValue('C' . $row, '');
        $sheet->getStyle('B' . $row)->getFont()->setBold(true);

        $col = 'D';
        // For each month, add empty cell and final score
        foreach ($monthlyData as $monthIndex => $month) {
            $sheet->setCellValue($col++ . $row, '');
            $sheet->setCellValue($col++ . $row, $month['finalScore'] ? number_format($month['finalScore'], 0) : '');
        }

        // Add overall average
        $sheet->setCellValue($col++ . $row, '');
        $sheet->setCellValue($col . $row, number_format($overallAverage, 0));
        $sheet->getStyle($col . $row)->getFont()->setBold(true);

        // Set output filename
        $filename = 'Performance_Report_' . $employee->name . '_' . $year . '_' . date('YmdHis') . '.xlsx';

        // Create writer and output
        $writer = new Xlsx($spreadsheet);

        // Save to file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }


    /**
     * Display detailed performance evaluation report for a specific employee.
     */
    public function report_performance_detail($id)
    {
        // Get the user with their position and department
        $user = User::with(['position', 'department'])->findOrFail($id);

        // Get the current year for default period
        $year = request('year', date('Y'));

        // Month names for display
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Get all active criteria types and weights
        $criteria = RuleEvaluationWeightPerformance::with('criteria')
            ->where('Status', "Active")
            ->get()
            ->groupBy('criteria.type')
            ->map(function ($items) {
                return [
                    'id' => $items->first()->id,
                    'name' => $items->first()->criteria->type,
                    'weight' => $items->first()->weight
                ];
            })
            ->values();

        $criteriaList = $criteria->toArray();

        // Initialize monthly data structure - using 1-based indexing for consistency
        $monthlyData = [];
        foreach (range(1, 12) as $monthNumber) {
            $monthlyData[$monthNumber] = [
                'scores' => [],
                'rawScore' => 0,
                'finalScore' => 0,
                'deductions' => 0
            ];
        }

        // Get all evaluations for the user in the selected year
        $evaluations = EvaluationPerformance::with(['details.weightPerformance.criteria', 'reductions.warningLetter'])
            ->where('user_id', $user->id)
            ->whereYear('date', $year)
            ->get();

        // Process each month
        foreach (range(1, 12) as $monthNumber) {
            $monthEvaluations = $evaluations->filter(function ($eval) use ($monthNumber) {
                // Ensure date is parsed as Carbon instance
                $date = is_string($eval->date) ? Carbon::parse($eval->date) : $eval->date;
                return $date->month == $monthNumber;
            });

            if ($monthEvaluations->isNotEmpty()) {
                // Process criteria scores
                foreach ($criteriaList as $criterion) {
                    $values = [];
                    $scores = [];

                    foreach ($monthEvaluations as $eval) {
                        $details = $eval->details->filter(function ($detail) use ($criterion) {
                            return $detail->weightPerformance->criteria->type == $criterion['name'];
                        });

                        foreach ($details as $detail) {
                            $values[] = $detail->value;
                            $scores[] = $detail->value * $criterion['weight'];
                        }
                    }

                    if (!empty($values)) {
                        $avgValue = array_sum($values) / count($values);
                        $avgScore = array_sum($scores) / count($scores);

                        $monthlyData[$monthNumber]['scores'][] = [
                            'name' => $criterion['name'],
                            'value' => $avgValue,
                            'score' => $avgScore
                        ];

                        $monthlyData[$monthNumber]['rawScore'] += $avgScore;
                    }
                }
            }
        }

        // BAGIAN YANG DIMODIFIKASI: Proses deduction hanya berdasarkan warning letter yang terhubung dengan evaluasi
        // Reset dan inisialisasi ulang semua deduction
        foreach (range(1, 12) as $monthNumber) {
            $monthlyData[$monthNumber]['deductions'] = 0;
        }

        // Proses deduction secara ketat berdasarkan data evaluasi dan reduction yang terkait
        foreach ($evaluations as $evaluation) {
            $monthNumber = Carbon::parse($evaluation->date)->month;

            // Hanya ambil reduction yang memiliki warning letter yang valid
            $reductions = $evaluation->reductions->filter(function ($reduction) {
                return $reduction->warning_letter_id && $reduction->warningLetter;
            });

            // Tambahkan ke deduction bulan tersebut
            $monthlyData[$monthNumber]['deductions'] += $reductions->sum('reduction_amount');

            // Update final score untuk bulan ini
            $monthlyData[$monthNumber]['finalScore'] = max(
                0,
                $monthlyData[$monthNumber]['rawScore'] - $monthlyData[$monthNumber]['deductions']
            );
        }

        // BAGIAN YANG DIMODIFIKASI: Hitung rata-rata dengan membagi total dengan 12 (semua bulan)
        $totalRawScore = 0;
        foreach (range(1, 12) as $monthNumber) {
            $totalRawScore += $monthlyData[$monthNumber]['rawScore'];
        }
        $overallRawAverage = $totalRawScore / 12;

        // Process yearly reductions dengan pendekatan yang lebih ketat
        $reductionRules = RuleEvaluationReductionPerformance::where('Status', "Active")->get();
        $maxPossibleDeductions = $reductionRules->sum('weight');
        $yearlyReductions = [];
        $totalDeductions = 0;

        foreach ($reductionRules as $rule) {
            $ruleData = [
                'id' => $rule->id,
                'name' => $rule->warningLetterRule->name ?? $rule->name,
                'weight' => $rule->weight,
                'monthly' => array_fill(1, 12, ['count' => 0, 'reduction' => 0]),
                'total_count' => 0,
                'total_reduction' => 0
            ];

            // Untuk setiap bulan, hitung reduction berdasarkan evaluasi
            foreach ($evaluations as $evaluation) {
                $monthNumber = Carbon::parse($evaluation->date)->month;

                // Filter reduction yang termasuk dalam tipe/aturan ini
                $reductionsForThisRule = $evaluation->reductions->filter(function ($reduction) use ($rule) {
                    return $reduction->warningLetter && $reduction->warningLetter->type_id == $rule->type_id;
                });

                if ($reductionsForThisRule->isNotEmpty()) {
                    $count = $reductionsForThisRule->count();
                    $amount = $reductionsForThisRule->sum('reduction_amount');

                    $ruleData['monthly'][$monthNumber]['count'] += $count;
                    $ruleData['monthly'][$monthNumber]['reduction'] += $amount;
                    $ruleData['total_count'] += $count;
                    $ruleData['total_reduction'] += $amount;
                }
            }

            $yearlyReductions[$rule->id] = $ruleData;
            $totalDeductions += $ruleData['total_reduction'];
        }

        // Calculate total possible score
        $totalPossible = $criteria->sum('weight') * 3; // Assuming max score per criterion is 3

        // Calculate individual criteria averages
        $averageValues = [];
        $averageTotals = [];

        foreach ($criteriaList as $criterion) {
            $sumValues = 0;
            $sumScores = 0;
            $count = 0;

            foreach ($monthlyData as $month) {
                $criterionData = collect($month['scores'])->firstWhere('name', $criterion['name']);
                if ($criterionData) {
                    $sumValues += $criterionData['value'];
                    $sumScores += $criterionData['score'];
                    $count++;
                }
            }

            // Jika tidak ada data, set nilai rata-rata menjadi 0
            if ($count == 0) {
                $averageValues[$criterion['name']] = 0;
                $averageTotals[$criterion['name']] = 0;
            } else {
                $averageValues[$criterion['name']] = $sumValues / $count;
                $averageTotals[$criterion['name']] = $sumScores / $count;
            }
        }

        // Calculate the final score
        $overallAverage = max(0, $overallRawAverage - $totalDeductions);

        // Get evaluation messages
        $evaluationMessages = EvaluationPerformanceMessage::whereIn(
            'evaluation_id',
            EvaluationPerformance::where('user_id', $user->id)
                ->whereYear('date', $year)
                ->pluck('id')
        )
            ->select(['id', 'message', 'created_at', 'evaluation_id'])
            ->orderBy('created_at', 'asc')
            ->get();

        $finalScore = $overallAverage;
        $grade = RulePerformanceGrade::query()
            ->where(function ($q) use ($finalScore) {
                $q->where(function ($sub) use ($finalScore) {
                    $sub->whereNotNull('min_score')
                        ->whereNotNull('max_score')
                        ->where('min_score', '<=', $finalScore)
                        ->where('max_score', '>=', $finalScore);
                })->orWhere(function ($sub) use ($finalScore) {
                    $sub->whereNotNull('min_score')
                        ->whereNull('max_score')
                        ->where('min_score', '<=', $finalScore);
                })->orWhere(function ($sub) use ($finalScore) {
                    $sub->whereNull('min_score')
                        ->whereNotNull('max_score')
                        ->where('max_score', '>=', $finalScore);
                });
            })
            ->orderBy('min_score', 'asc') // Prioritaskan range yang lebih rendah
            ->first();

        // Fallback jika tidak ada grade yang cocok
        if (!$grade) {
            $grade = (object)[
                'grade' => '?',
                'description' => 'Undefined performance'
            ];
        }

        return view('evaluation.report.performance.detail', compact(
            'user',
            'year',
            'grade',
            'monthNames',
            'criteria',
            'criteriaList',
            'monthlyData',
            'overallRawAverage',
            'overallAverage',
            'reductionRules',
            'yearlyReductions',
            'totalDeductions',
            'totalPossible',
            'averageValues',
            'averageTotals',
            'maxPossibleDeductions',
            'evaluationMessages'
        ));
    }





    /**
     * Rule Discipline
     */
    public function rule_discipline_score_index()
    {
        $attendanceRules = DisciplineRule::where('rule_type', 'attendance')
            ->orderBy('min_value')
            ->get();

        $lateRules = DisciplineRule::where('rule_type', 'late')
            ->orderBy('min_value')
            ->get();

        $otherRules = [
            'early_leave' => DisciplineRule::where('rule_type', 'early_leave')->first(),
            'afternoon_shift' => DisciplineRule::where('rule_type', 'afternoon_shift')->first(),
            'st' => DisciplineRule::where('rule_type', 'st')->first(),
            'sp' => DisciplineRule::where('rule_type', 'sp')->first(),
        ];

        return view('evaluation.rule.discipline.score.index', compact(
            'attendanceRules',
            'lateRules',
            'otherRules'
        ));
    }

    public function rule_discipline_score_store(Request $request)
    {
        try {
            $ruleType = $request->rule_type;
            $rules = $this->getValidationRules($ruleType);

            $validatedData = $request->validate($rules);

            // Special case for 100-100 attendance rule
            if ($ruleType === 'attendance' && $request->min_value == 100 && $request->max_value == 100) {
                $this->validate100AttendanceRule();
            }

            // Check for overlaps
            if (in_array($ruleType, ['attendance', 'late'])) {
                $this->checkRangeOverlap($request);
            }

            // Ensure all required fields are present
            $ruleData = [
                'rule_type' => $validatedData['rule_type'],
                'min_value' => $validatedData['min_value'] ?? null,
                'max_value' => $validatedData['max_value'] ?? null,
                'occurrence' => $validatedData['occurrence'] ?? null,
                'score_value' => $validatedData['score_value'],
                'operation' => $validatedData['operation'],
            ];



            return redirect()->route('evaluation.rule.discipline.score.index');
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'value' => 'There is Something Error',
            ]);
        }
    }

    public function rule_discipline_score_update(Request $request, $id)
    {
        try {
            $rule = DisciplineRule::findOrFail($id);
            $ruleType = $request->rule_type;
            $rules = $this->getValidationRules($ruleType);

            $validatedData = $request->validate($rules);

            // Special case for 100-100 attendance rule
            if ($ruleType === 'attendance' && $request->min_value == 100 && $request->max_value == 100) {
                $this->validate100AttendanceRule($id);
            }

            // Check for overlaps (excluding current rule)
            if (in_array($ruleType, ['attendance', 'late'])) {
                $this->checkRangeOverlap($request, $id);
            }

            $updateData = [
                'min_value' => $validatedData['min_value'] ?? null,
                'max_value' => $validatedData['max_value'] ?? null,
                'occurrence' => $validatedData['occurrence'] ?? null,
                'score_value' => $validatedData['score_value'],
                'operation' => $validatedData['operation']
            ];

            $rule->update($updateData);


            return redirect()->route('evaluation.rule.discipline.score.index');
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'value' => 'There is Something Error',
            ]);
        }
    }

    public function rule_discipline_score_destroy($id)
    {
        try {
            $rule = DisciplineRule::findOrFail($id);
            $rule->delete();


            return redirect()->route('evaluation.rule.discipline.score.index');
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'value' => 'There is Something Error',
            ]);
        }
    }

    private function getValidationRules($ruleType)
    {
        $rules = [
            'rule_type' => 'required|string',
            'score_value' => 'required|numeric',
            'operation' => 'required|in:set,add,subtract,multiply',
        ];

        if ($ruleType === 'attendance') {
            $rules['min_value'] = 'required|integer|min:0|max:100';
            $rules['max_value'] = 'nullable|integer|min:0|max:100';
            $rules['operation'] = 'required|in:set';
        } elseif ($ruleType === 'late') {
            $rules['min_value'] = 'required|integer|min:0';
            $rules['max_value'] = 'nullable|integer|min:0';
            $rules['operation'] = 'required|in:subtract';
        } else {
            $rules['occurrence'] = 'required|integer|min:1';
            $rules['operation'] = 'required|in:subtract';
        }

        return $rules;
    }

    private function validate100AttendanceRule($excludeId = null)
    {
        $existing100Rule = DisciplineRule::where('rule_type', 'attendance')
            ->where('min_value', 100)
            ->where(function ($q) {
                $q->where('max_value', 100)->orWhereNull('max_value');
            })
            ->when($excludeId, function ($query) use ($excludeId) {
                $query->where('id', '!=', $excludeId);
            })
            ->exists();

        if ($existing100Rule) {
            throw ValidationException::withMessages([
                'value' => 'Already have a rule for 100% attendance',

            ]);
        }
    }

    private function checkRangeOverlap($request, $excludeId = null)
    {
        $min = $request->min_value;
        $max = $request->max_value;
        $ruleType = $request->rule_type;

        // Special handling for 100-100 attendance rule
        if ($ruleType === 'attendance' && $min == 100 && $max == 100) {
            $this->validate100AttendanceRule($excludeId);
            return;
        }

        // Normal range validation
        if ($max !== null && $max <= $min) {
            throw ValidationException::withMessages([
                'max_value' => 'max_value must be greater than min_value'
            ]);
        }

        $query = DisciplineRule::where('rule_type', $ruleType)
            ->where(function ($q) use ($min, $max) {
                // Case 1: New min falls within existing range
                $q->where(function ($q1) use ($min) {
                    $q1->where('min_value', '<=', $min)
                        ->where(function ($q2) use ($min) {
                            $q2->where('max_value', '>', $min)
                                ->orWhereNull('max_value');
                        });
                })
                    // Case 2: New max falls within existing range
                    ->orWhere(function ($q1) use ($max) {
                        if ($max !== null) {
                            $q1->where('min_value', '<', $max)
                                ->where(function ($q2) use ($max) {
                                    $q2->where('max_value', '>=', $max)
                                        ->orWhereNull('max_value');
                                });
                        }
                    })
                    // Case 3: Existing range falls within new range
                    ->orWhere(function ($q1) use ($min, $max) {
                        $q1->where('min_value', '>=', $min);
                        if ($max !== null) {
                            $q1->where(function ($q2) use ($max) {
                                $q2->where('max_value', '<=', $max)
                                    ->orWhereNull('max_value');
                            });
                        }
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {

            throw ValidationException::withMessages([
                'value' => 'This range overlaps with an existing rule',

            ]);
        }
    }



    /**
     * Report Discipline
     */

    public function report_discipline_index()
    {
        $employees = User::all();
        $departments = EmployeeDepartment::all();
        $positions = EmployeePosition::all();

        // Get available years from absence data
        $years = EmployeeAbsent::selectRaw('YEAR(date) as year')
            ->distinct()
            ->pluck('year')
            ->sortDesc();

        // Current month and year for default view
        $currentMonth = date('m');
        $currentYear = date('Y');

        return view('evaluation.report.discipline.index', compact(
            'employees',
            'departments',
            'positions',
            'years',
            'currentMonth',
            'currentYear'
        ));
    }


    /**
     * Get Discipline Report Data
     */
    public function getDisciplineReportData(Request $request)
    {
        set_time_limit(900); // 15 menit

        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $employeeId = $request->input('employee_id');
        $departmentId = $request->input('department_id');
        $positionId = $request->input('position_id');

        // Handle 'final' month case
        if ($month === 'final') {
            return $this->getFinalYearlyData($year, $request);
        }

        // Get reference date for historical position/department lookup
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $referenceDate = Carbon::create($year, $month, $daysInMonth)->format('Y-m-d');

        // Base query for getting employees
        $query = User::query();

        // Base filtering
        if ($employeeId) {
            $query->where('id', $employeeId);
        }

        // Filter by department and position based on historical data
        if ($departmentId || $positionId) {
            // Get all employees who might match our filters based on history
            $matchingUserIds = [];

            // First get all users
            $allPotentialUsers = User::select('id', 'department_id', 'position_id')->get();

            foreach ($allPotentialUsers as $user) {
                // Find the most recent transfer history before the reference date
                $history = history_transfer_employee::where('users_id', $user->id)
                    ->where('created_at', '<', $referenceDate)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $effectiveDepartmentId = $history ? $history->new_department_id : $user->department_id;
                $effectivePositionId = $history ? $history->new_position_id : $user->position_id;

                // Check if this user matches our department/position filters
                $deptMatch = !$departmentId || $effectiveDepartmentId == $departmentId;
                $posMatch = !$positionId || $effectivePositionId == $positionId;

                if ($deptMatch && $posMatch) {
                    $matchingUserIds[] = $user->id;
                }
            }

            // Filter the original query by these matching user IDs
            $query->whereIn('id', $matchingUserIds);
        }

        // Untuk testing - ambil hanya 10 orang pertama
        // if (env('APP_ENV') === 'local' || $request->has('test_mode')) {
        //     $query->limit(10);
        // }

        $employees = $query->get();

        // Get discipline rules
        $attendanceRules = DisciplineRule::where('rule_type', 'attendance')->orderBy('min_value', 'desc')->get();
        $lateRules = DisciplineRule::where('rule_type', 'late')->orderBy('min_value', 'asc')->get();
        $afternoonShiftRule = DisciplineRule::where('rule_type', 'afternoon_shift')->first();
        $earlyLeaveRule = DisciplineRule::where('rule_type', 'early_leave')->first();
        $stRule = DisciplineRule::where('rule_type', 'st')->first();
        $spRule = DisciplineRule::where('rule_type', 'sp')->first();

        $startDate = Carbon::create($year, $month, 1)->format('Y-m-d');
        $endDate = Carbon::create($year, $month, $daysInMonth)->format('Y-m-d');

        $customHolidays = CustomHoliday::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->pluck('date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        $reportData = [];

        foreach ($employees as $employee) {
            // Get absences
            $absences = EmployeeAbsent::where('user_id', $employee->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();

            // Get time off requests
            $timeOffRequests = RequestTimeOff::where('user_id', $employee->id)
                ->where('status', 'Approved')
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($innerQ) use ($startDate, $endDate) {
                            $innerQ->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                })
                ->with('timeOffPolicy')
                ->get();

            // Get warning letters
            $warningLetters = WarningLetter::where('user_id', $employee->id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->get();

            // Find the most recent transfer history before the reference date
            $history = history_transfer_employee::where('users_id', $employee->id)
                ->where('created_at', '<', $referenceDate)
                ->orderBy('created_at', 'desc')
                ->first();

            // Get historical position and department
            if ($history) {
                $position = EmployeePosition::find($history->new_position_id);
                $department = EmployeeDepartment::find($history->new_department_id);
            } else {
                // If no history, use current position and department
                $position = $employee->position;
                $department = $employee->department;
            }

            // Calculate working days
            $workingDays = $this->calculateWorkingDays($year, $month, $customHolidays);

            // Calculate presence count
            $presenceCount = $absences->count();
            $attendancePercentage = $workingDays > 0 ? round(($presenceCount / $workingDays) * 100) : 0;

            // Calculate counts (raw numbers)
            $lateArrivals = $this->countLateArrivals($absences);
            $afternoonShiftCount = $this->countAfternoonShifts($timeOffRequests);
            $earlyDepartures = $this->countEarlyDepartures($absences);
            $permissionCount = $this->countTimeOffByType($timeOffRequests, 'permission');
            $sickLeaveCount = $this->countTimeOffByType($timeOffRequests, 'sick');
            $stCount = $this->countWarningLetters($warningLetters, 'ST');
            $spCount = $this->countWarningLetters($warningLetters, 'SP');

            // Calculate scores
            $attendanceScore = $this->calculateAttendanceScore($attendancePercentage, $attendanceRules, $presenceCount);
            $lateScore = $this->calculateLateScore($lateArrivals, $lateRules);
            $afternoonShiftScore = $this->calculateOccurrenceScore($afternoonShiftCount, $afternoonShiftRule);
            $earlyDepartureScore = $this->calculateOccurrenceScore($earlyDepartures, $earlyLeaveRule);
            $stScore = $this->calculateOccurrenceScore($stCount, $stRule);
            $spScore = $this->calculateOccurrenceScore($spCount, $spRule);

            // Calculate total score
            $totalScore = $attendanceScore - $lateScore - $afternoonShiftScore - $earlyDepartureScore - $stScore - $spScore;

            $reportData[] = [
                'employee_id' => $employee->employee_id,
                'name' => $employee->name,
                'position' => $position ? $position->position : '',
                'department' => $department ? $department->department : '',
                'working_days' => $workingDays,
                'presence' => $presenceCount,
                'attendance_percentage' => $attendancePercentage,
                'late_arrivals' => $lateArrivals,
                'permission' => $permissionCount,
                'afternoon_shift_count' => $afternoonShiftCount,
                'early_departures' => $earlyDepartures,
                'sick_leave' => $sickLeaveCount,
                'st_count' => $stCount,
                'sp_count' => $spCount,
                'attendance_score' => $attendanceScore,
                'late_score' => $lateScore > 0 ? -$lateScore : null,
                'afternoon_shift_score' => $afternoonShiftScore > 0 ? -$afternoonShiftScore : null,
                'early_departure_score' => $earlyDepartureScore > 0 ? -$earlyDepartureScore : null,
                'st_score' => $stScore > 0 ? -$stScore : null,
                'sp_score' => $spScore > 0 ? -$spScore : null,
                'total_score' => $totalScore
            ];
        }

        return response()->json($reportData);
    }


    /**
     * Get Final Yearly Report Data
     */
    private function getFinalYearlyData($year, Request $request)
    {
        set_time_limit(900); // 15 menit

        // Get filter parameters from request
        $departmentId = $request->input('department_id');
        $positionId = $request->input('position_id');

        // Base query for getting employees
        $query = User::query();

        // Untuk testing - ambil hanya 10 orang pertama
        // if (env('APP_ENV') === 'local' || $request->has('test_mode')) {
        //     $query->limit(25);
        // }

        // Filter by department and position based on historical data
        if ($departmentId || $positionId) {
            // Get all employees who might match our filters based on history
            $matchingUserIds = [];

            // First get all users
            $allPotentialUsers = User::select('id', 'department_id', 'position_id')->get();

            // Reference date is the end of the year
            $referenceDate = Carbon::create($year, 12, 31)->format('Y-m-d');

            foreach ($allPotentialUsers as $user) {
                // Find the most recent transfer history before the reference date
                $history = history_transfer_employee::where('users_id', $user->id)
                    ->where('created_at', '<', $referenceDate)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $effectiveDepartmentId = $history ? $history->new_department_id : $user->department_id;
                $effectivePositionId = $history ? $history->new_position_id : $user->position_id;

                // Check if this user matches our department/position filters
                $deptMatch = !$departmentId || $effectiveDepartmentId == $departmentId;
                $posMatch = !$positionId || $effectivePositionId == $positionId;

                if ($deptMatch && $posMatch) {
                    $matchingUserIds[] = $user->id;
                }
            }

            // Filter the original query by these matching user IDs
            $query->whereIn('id', $matchingUserIds);
        }

        $employees = $query->get();

        $reportData = [];

        // Get discipline rules once
        $attendanceRules = DisciplineRule::where('rule_type', 'attendance')->orderBy('min_value', 'desc')->get();
        $lateRules = DisciplineRule::where('rule_type', 'late')->orderBy('min_value', 'asc')->get();
        $afternoonShiftRule = DisciplineRule::where('rule_type', 'afternoon_shift')->first();
        $earlyLeaveRule = DisciplineRule::where('rule_type', 'early_leave')->first();
        $stRule = DisciplineRule::where('rule_type', 'st')->first();
        $spRule = DisciplineRule::where('rule_type', 'sp')->first();

        // Get grade rules from database
        $gradeRules = RuleDisciplineGrade::orderBy('min_score', 'desc')->get();

        foreach ($employees as $employee) {
            $yearlyData = [
                'employee_id' => $employee->employee_id,
                'name' => $employee->name,
                'working_days' => 0,
                'presence' => 0,
                'late_arrivals' => 0,
                'permission' => 0,
                'afternoon_shift_count' => 0,
                'early_departures' => 0,
                'sick_leave' => 0,
                'st_count' => 0,
                'sp_count' => 0,
                'monthly_scores' => [] // To store monthly scores for summation
            ];

            // Initialize total scores
            $totalAttendanceScore = 0;
            $totalLateScore = 0;
            $totalAfternoonShiftScore = 0;
            $totalEarlyDepartureScore = 0;
            $totalStScore = 0;
            $totalSpScore = 0;

            // Find the most recent transfer history for the entire year
            $history = history_transfer_employee::where('users_id', $employee->id)
                ->where('created_at', '<', Carbon::create($year, 12, 31)->format('Y-m-d'))
                ->orderBy('created_at', 'desc')
                ->first();

            // Get historical position and department
            if ($history) {
                $position = EmployeePosition::find($history->new_position_id);
                $department = EmployeeDepartment::find($history->new_department_id);
            } else {
                // If no history, use current position and department
                $position = $employee->position;
                $department = $employee->department;
            }

            for ($month = 1; $month <= 12; $month++) {
                $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);

                // Always calculate working days for each month
                $customHolidays = CustomHoliday::whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->pluck('date')
                    ->map(function ($date) {
                        return Carbon::parse($date)->format('Y-m-d');
                    })
                    ->toArray();

                $workingDays = $this->calculateWorkingDays($year, $monthStr, $customHolidays);
                $yearlyData['working_days'] += $workingDays;

                // Get absences for the month (if any)
                $absences = EmployeeAbsent::where('user_id', $employee->id)
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->get();

                $startDate = Carbon::create($year, $month, 1)->format('Y-m-d');
                $endDate = Carbon::create($year, $month)->endOfMonth()->format('Y-m-d');

                $timeOffRequests = RequestTimeOff::where('user_id', $employee->id)
                    ->where('status', 'Approved')
                    ->where(function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('start_date', [$startDate, $endDate])
                            ->orWhereBetween('end_date', [$startDate, $endDate])
                            ->orWhere(function ($innerQ) use ($startDate, $endDate) {
                                $innerQ->where('start_date', '<=', $startDate)
                                    ->where('end_date', '>=', $endDate);
                            });
                    })
                    ->with('timeOffPolicy')
                    ->get();

                $warningLetters = WarningLetter::where('user_id', $employee->id)
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->get();

                // Sum all monthly values (including zeros if no data)
                $monthPresence = $absences->count();
                $yearlyData['presence'] += $monthPresence;
                $monthLateArrivals = $this->countLateArrivals($absences);
                $yearlyData['late_arrivals'] += $monthLateArrivals;
                $monthEarlyDepartures = $this->countEarlyDepartures($absences);
                $yearlyData['early_departures'] += $monthEarlyDepartures;
                $monthSickLeave = $this->countTimeOffByType($timeOffRequests, 'sick');
                $yearlyData['sick_leave'] += $monthSickLeave;
                $monthPermission = $this->countTimeOffByType($timeOffRequests, 'permission');
                $yearlyData['permission'] += $monthPermission;
                $monthAfternoonShift = $this->countAfternoonShifts($timeOffRequests);
                $yearlyData['afternoon_shift_count'] += $monthAfternoonShift;
                $monthStCount = $this->countWarningLetters($warningLetters, 'ST');
                $yearlyData['st_count'] += $monthStCount;
                $monthSpCount = $this->countWarningLetters($warningLetters, 'SP');
                $yearlyData['sp_count'] += $monthSpCount;

                // Calculate monthly attendance percentage
                $monthAttendancePercentage = $workingDays > 0
                    ? round(($monthPresence / $workingDays) * 100)
                    : 0;

                // Calculate monthly scores
                $monthAttendanceScore = $monthPresence === 0 ? 0 : $this->calculateAttendanceScore($monthAttendancePercentage, $attendanceRules, $monthPresence);
                $monthLateScore = $this->calculateLateScore($monthLateArrivals, $lateRules);
                $monthAfternoonShiftScore = $this->calculateOccurrenceScore($monthAfternoonShift, $afternoonShiftRule);
                $monthEarlyDepartureScore = $this->calculateOccurrenceScore($monthEarlyDepartures, $earlyLeaveRule);
                $monthStScore = $this->calculateOccurrenceScore($monthStCount, $stRule);
                $monthSpScore = $this->calculateOccurrenceScore($monthSpCount, $spRule);

                // Sum up all monthly scores
                $totalAttendanceScore += $monthAttendanceScore;
                $totalLateScore += $monthLateScore;
                $totalAfternoonShiftScore += $monthAfternoonShiftScore;
                $totalEarlyDepartureScore += $monthEarlyDepartureScore;
                $totalStScore += $monthStScore;
                $totalSpScore += $monthSpScore;
            }

            // Calculate total score from summed monthly scores
            $totalScore = $totalAttendanceScore - $totalLateScore - $totalAfternoonShiftScore - $totalEarlyDepartureScore - $totalStScore - $totalSpScore;

            // Calculate attendance percentage for display
            $attendancePercentage = $yearlyData['working_days'] > 0
                ? round(($yearlyData['presence'] / $yearlyData['working_days']) * 100)
                : 0;

            // Calculate final grade based on summed scores
            $grade = '';
            if ($yearlyData['presence'] > 0) {
                foreach ($gradeRules as $rule) {
                    if (
                        $totalScore >= $rule->min_score &&
                        ($rule->max_score === null || $totalScore <= $rule->max_score)
                    ) {
                        $grade = $rule->grade;
                        break;
                    }
                }
            }

            // Prepare final data with empty fields instead of zeros
            $finalData = [
                'employee_id' => $yearlyData['employee_id'],
                'name' => $yearlyData['name'],
                'position' => $position ? $position->position : '', // Added position
                'department' => $department ? $department->department : '', // Added department
                'working_days' => $yearlyData['working_days'] > 0 ? $yearlyData['working_days'] : '',
                'presence' => $yearlyData['presence'] > 0 ? $yearlyData['presence'] : '',
                'attendance_percentage' => $yearlyData['presence'] > 0 ? $attendancePercentage : '',
                'late_arrivals' => $yearlyData['late_arrivals'] > 0 ? $yearlyData['late_arrivals'] : '',
                'permission' => $yearlyData['permission'] > 0 ? $yearlyData['permission'] : '',
                'afternoon_shift_count' => $yearlyData['afternoon_shift_count'] > 0 ? $yearlyData['afternoon_shift_count'] : '',
                'early_departures' => $yearlyData['early_departures'] > 0 ? $yearlyData['early_departures'] : '',
                'sick_leave' => $yearlyData['sick_leave'] > 0 ? $yearlyData['sick_leave'] : '',
                'st_count' => $yearlyData['st_count'] > 0 ? $yearlyData['st_count'] : '',
                'sp_count' => $yearlyData['sp_count'] > 0 ? $yearlyData['sp_count'] : '',
                'attendance_score' => $totalAttendanceScore > 0 ? $totalAttendanceScore : '',
                'late_score' => $totalLateScore > 0 ? -$totalLateScore : '',
                'afternoon_shift_score' => $totalAfternoonShiftScore > 0 ? -$totalAfternoonShiftScore : '',
                'early_departure_score' => $totalEarlyDepartureScore > 0 ? -$totalEarlyDepartureScore : '',
                'st_score' => $totalStScore > 0 ? -$totalStScore : '',
                'sp_score' => $totalSpScore > 0 ? -$totalSpScore : '',
                'total_score' => $yearlyData['presence'] > 0 ? $totalScore : '',
                'grade' => $grade // Add the grade from database
            ];

            $reportData[] = $finalData;
        }

        return response()->json($reportData);
    }


    /**
     * Simplified counting methods
     */
    private function countAfternoonShifts($timeOffRequests)
    {
        $count = 0;
        foreach ($timeOffRequests as $request) {
            $policyName = strtolower($request->timeOffPolicy->time_off_name ?? '');
            if (str_contains($policyName, 'masuk siang')) {
                $count++;
            }
        }
        return $count;
    }

    private function countLateArrivals($absences)
    {
        return $absences->where('is_late', true)->count();
    }

    private function countEarlyDepartures($absences)
    {
        return $absences->where('is_early', true)->count();
    }

    private function countTimeOffByType($timeOffRequests, $type)
    {
        $count = 0;
        foreach ($timeOffRequests as $request) {
            $policyName = strtolower($request->timeOffPolicy->time_off_name ?? '');
            if (str_contains($policyName, $type)) {
                $count++;
            }
        }
        return $count;
    }

    private function countWarningLetters($warningLetters, $type)
    {
        return $warningLetters->filter(function ($letter) use ($type) {
            return stripos($letter->rule->name ?? '', $type) === 0;
        })->count();
    }

    /**
     * Score calculation methods
     */
    private function calculateAttendanceScore($percentage, $rules, $presenceCount = 0)
    {
        // Return 0 if there's no presence at all
        if ($presenceCount === 0) {
            return 0;
        }

        foreach ($rules as $rule) {
            if ($percentage >= $rule->min_value && ($rule->max_value === null || $percentage <= $rule->max_value)) {
                return $rule->score_value;
            }
        }
        return 0;
    }
    private function calculateLateScore($count, $rules)
    {
        foreach ($rules as $rule) {
            if ($count >= $rule->min_value && ($rule->max_value === null || $count <= $rule->max_value)) {
                return $rule->score_value;
            }
        }
        return 0;
    }

    private function calculateOccurrenceScore($count, $rule)
    {
        if (!$rule || $count <= 0) return 0;
        return $count * $rule->score_value;
    }

    /**
     * Calculate working days in a month
     */
    private function calculateWorkingDays($year, $month, $customHolidays = [])
    {
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $workingDays = 0;

        $nationalHolidays = $this->getNationalHolidays($year, $month);

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day);
            $formattedDate = $date->format('Y-m-d');

            if ($date->dayOfWeek === Carbon::SUNDAY) {
                continue;
            }

            if (in_array($formattedDate, $nationalHolidays) || in_array($formattedDate, $customHolidays)) {
                continue;
            }

            $workingDays++;
        }

        return $workingDays;
    }

    private function getNationalHolidays($year, $month)
    {
        try {
            $response = Http::get('https://api-harilibur.vercel.app/api');
            if ($response->successful()) {
                return collect($response->json())
                    ->filter(function ($holiday) use ($year, $month) {
                        try {
                            $date = Carbon::parse($holiday['holiday_date'] ?? '');
                            return $date->year == $year &&
                                $date->month == $month &&
                                ($holiday['is_national_holiday'] ?? false);
                        } catch (\Exception $e) {
                            return false;
                        }
                    })
                    ->pluck('holiday_date')
                    ->toArray();
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch national holidays: ' . $e->getMessage());
        }
        return [];
    }


    /**
     * Get Grade Settings
     */
    public function getDisciplineGradeSettings()
    {
        $grades = RuleDisciplineGrade::orderBy('min_score', 'desc')->get();
        return response()->json($grades);
    }







    /**
     * Export discipline report to Excel
     */

    // public function exportDisciplineReport(Request $request)
    // {
    //     // Increase memory limit and execution time for large exports
    //     ini_set('memory_limit', '512M');
    //     ini_set('max_execution_time', 300); // 5 minutes

    //     $year = $request->input('year', date('Y'));
    //     $employeeId = $request->input('employee_id');
    //     $departmentId = $request->input('department_id');
    //     $positionId = $request->input('position_id');
    //     $month = $request->input('month');
    //     $exportType = $request->input('export_type', 'all');

    //     // Get employees based on filters
    //     $employees = $this->getFilteredEmployees($employeeId, $departmentId, $positionId);

    //     // Use caching to improve performance
    //     $cacheKey = "discipline_report_{$year}_{$employeeId}_{$departmentId}_{$positionId}_{$exportType}";
    //     $expiresAt = now()->addMinutes(60);

    //     // Create new Spreadsheet object
    //     $spreadsheet = new Spreadsheet();

    //     // Set document properties for better metadata
    //     $spreadsheet->getProperties()
    //         ->setCreator('HR System')
    //         ->setLastModifiedBy('HR System')
    //         ->setTitle('Discipline Report ' . $year)
    //         ->setSubject('Employee Discipline Report')
    //         ->setDescription('Employee Discipline Report for ' . $year);

    //     // Handle different export types with optimized processing
    //     if ($exportType == 'monthly') {
    //         // Export all monthly sheets
    //         $this->createMonthlySheets($spreadsheet, $request, $year);
    //         $fileName = 'Discipline_Report_Monthly_' . $year . '.xlsx';
    //     } elseif ($exportType == 'final') {
    //         // Add Final sheet only
    //         $finalData = $this->getFinalYearlyData($employees, $year)->original;
    //         $this->createFinalYearlySheet($spreadsheet, $finalData, $year);
    //         $fileName = 'Discipline_Report_Final_' . $year . '.xlsx';
    //     } else {
    //         // Export everything (all monthly + yearly sheets)
    //         $this->createMonthlySheets($spreadsheet, $request, $year);

    //         // Add Final sheet as the last sheet
    //         $spreadsheet->createSheet();
    //         $spreadsheet->setActiveSheetIndex(12);
    //         $finalData = $this->getFinalYearlyData($employees, $year)->original;
    //         $this->createFinalYearlySheet($spreadsheet, $finalData, $year);
    //         $fileName = 'Discipline_Report_Complete_' . $year . '.xlsx';
    //     }

    //     // Set first sheet as active for better UX
    //     if ($spreadsheet->getSheetCount() > 0) {
    //         $spreadsheet->setActiveSheetIndex(0);
    //     }

    //     // Add custom header/footer information
    //     foreach ($spreadsheet->getAllSheets() as $sheet) {
    //         $sheet->getHeaderFooter()
    //             ->setOddHeader('&C&BDiscipline Report ' . $year)
    //             ->setOddFooter('&L&B' . $sheet->getTitle() . '&R&P of &N');
    //     }

    //     // Create Excel writer with better caching options for performance
    //     $writer = new Xlsx($spreadsheet);
    //     $writer->setPreCalculateFormulas(false); // Performance improvement

    //     // Use temporary file to avoid memory issues with large files
    //     $tempFile = tempnam(sys_get_temp_dir(), 'discipline_report_');
    //     $writer->save($tempFile);

    //     // Return the response with proper headers
    //     return response()->download($tempFile, $fileName, [
    //         'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    //         'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
    //         'Cache-Control' => 'max-age=0',
    //     ])->deleteFileAfterSend(true);
    // }


    /**
     * Export discipline report to Excel
     */
    public function exportDisciplineReport(Request $request)
    {
        // Increase memory limit and execution time for large exports
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300); // 5 minutes

        $year = $request->input('year', date('Y'));
        $employeeId = $request->input('employee_id');
        $departmentId = $request->input('department_id');
        $positionId = $request->input('position_id');
        $month = $request->input('month');
        $exportType = $request->input('export_type', 'all');

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();

        // Set document properties for better metadata
        $spreadsheet->getProperties()
            ->setCreator('HR System')
            ->setLastModifiedBy('HR System')
            ->setTitle('Discipline Report ' . $year)
            ->setSubject('Employee Discipline Report')
            ->setDescription('Employee Discipline Report for ' . $year);

        // Handle different export types with optimized processing
        if ($exportType == 'monthly') {
            // Export all monthly sheets
            $this->createMonthlySheets($spreadsheet, $request, $year);
            $fileName = 'Discipline_Report_Monthly_' . $year . '.xlsx';
        } elseif ($exportType == 'final') {
            // Add Final sheet only
            // Pass request object directly instead of employees
            $finalData = $this->getFinalYearlyData($year, $request)->original;
            $this->createFinalYearlySheet($spreadsheet, $finalData, $year);
            $fileName = 'Discipline_Report_Final_' . $year . '.xlsx';
        } else {
            // Export everything (all monthly + yearly sheets)
            $this->createMonthlySheets($spreadsheet, $request, $year);

            // Add Final sheet as the last sheet
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex(12);
            // Pass request object directly instead of employees
            $finalData = $this->getFinalYearlyData($year, $request)->original;
            $this->createFinalYearlySheet($spreadsheet, $finalData, $year);
            $fileName = 'Discipline_Report_Complete_' . $year . '.xlsx';
        }

        // Set first sheet as active for better UX
        if ($spreadsheet->getSheetCount() > 0) {
            $spreadsheet->setActiveSheetIndex(0);
        }

        // Add custom header/footer information
        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $sheet->getHeaderFooter()
                ->setOddHeader('&C&BDiscipline Report ' . $year)
                ->setOddFooter('&L&B' . $sheet->getTitle() . '&R&P of &N');
        }

        // Create Excel writer with better caching options for performance
        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false); // Performance improvement

        // Use temporary file to avoid memory issues with large files
        $tempFile = tempnam(sys_get_temp_dir(), 'discipline_report_');
        $writer->save($tempFile);

        // Return the response with proper headers
        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Helper function to get filtered employees
     */
    private function getFilteredEmployees($employeeId, $departmentId, $positionId)
    {
        $query = User::query();

        if ($employeeId) {
            $query->where('id', $employeeId);
        }

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        if ($positionId) {
            $query->where('position_id', $positionId);
        }

        return $query->get();
    }

    /**
     * Create monthly report sheets with optimized formatting
     */
    private function createMonthlySheets($spreadsheet, $request, $year)
    {
        for ($m = 1; $m <= 12; $m++) {
            $currentMonth = str_pad($m, 2, '0', STR_PAD_LEFT);
            $request->merge(['month' => $currentMonth, 'year' => $year]);
            $monthData = $this->getDisciplineReportData($request)->original;
            $monthName = date('F', mktime(0, 0, 0, $m, 10));

            if ($m > 1) {
                $spreadsheet->createSheet();
                $spreadsheet->setActiveSheetIndex($m - 1);
            }

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle($monthName);
            $this->formatMonthlySheet($sheet, $monthData, $monthName, $year);
        }
    }

    /**
     * Format monthly report sheet with complete score columns
     */
    private function formatMonthlySheet($sheet, $data, $monthName, $year)
    {
        // Set company information
        $sheet->setCellValue('A1', 'LAPORAN KEDISIPLINAN');
        $sheet->setCellValue('A2', 'Bulan: ' . $monthName . ' ' . $year);
        $sheet->mergeCells('A1:S1');
        $sheet->mergeCells('A2:S2');

        // Set headers with complete score columns 
        $headers = [
            'A4' => 'NIK',
            'B4' => 'NAMA',
            'C4' => 'TTL KERJA/BL',
            'D4' => 'KEHADIRAN',
            'E4' => '%',
            'F4' => 'TERLAMBAT',
            'G4' => 'IJIN',
            'H4' => 'MASUK SIANG',
            'I4' => 'PULANG AWAL',
            'J4' => 'SAKIT',
            'K4' => 'ST',
            'L4' => 'SP',
            'M4' => 'KEHADIRAN',
            'N4' => 'TERLAMBAT',
            'O4' => 'MASUK SIANG',
            'P4' => 'PULANG AWAL',
            'Q4' => 'ST',
            'R4' => 'SP',
            'S4' => 'TOTAL'
        ];

        // Add score label above score columns
        $sheet->setCellValue('M3', 'SCORE');
        $sheet->mergeCells('M3:S3');
        $sheet->getStyle('M3')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '9ACD32'],
            ]
        ]);

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Add data rows
        $row = 5;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['employee_id']);
            $sheet->setCellValue('B' . $row, $item['name']);
            $sheet->setCellValue('C' . $row, $item['working_days']);
            $sheet->setCellValue('D' . $row, $item['presence']);
            $sheet->setCellValue('E' . $row, $item['attendance_percentage']);
            $sheet->setCellValue('F' . $row, $item['late_arrivals']);
            $sheet->setCellValue('G' . $row, $item['permission']);
            $sheet->setCellValue('H' . $row, $item['afternoon_shift_count']);
            $sheet->setCellValue('I' . $row, $item['early_departures']);
            $sheet->setCellValue('J' . $row, $item['sick_leave']);
            $sheet->setCellValue('K' . $row, $item['st_count']);
            $sheet->setCellValue('L' . $row, $item['sp_count']);

            // Score columns
            $sheet->setCellValue('M' . $row, $item['attendance_score']);
            $sheet->setCellValue('N' . $row, $item['late_score']);
            $sheet->setCellValue('O' . $row, $item['afternoon_shift_score']);
            $sheet->setCellValue('P' . $row, $item['early_departure_score']);
            $sheet->setCellValue('Q' . $row, $item['st_score']);
            $sheet->setCellValue('R' . $row, $item['sp_score']);

            // Calculate and display total score
            $totalScore = $item['attendance_score'] +
                ($item['late_score'] ? $item['late_score'] : 0) +
                ($item['afternoon_shift_score'] ? $item['afternoon_shift_score'] : 0) +
                ($item['early_departure_score'] ? $item['early_departure_score'] : 0) +
                ($item['st_score'] ? $item['st_score'] : 0) +
                ($item['sp_score'] ? $item['sp_score'] : 0);

            $sheet->setCellValue('S' . $row, $item['total_score']);

            $row++;
        }

        // Set column widths and auto-size for better readability
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(30);

        for ($col = 'C'; $col <= 'S'; $col++) {
            $sheet->getColumnDimension($col)->setWidth(15);
        }

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '9ACD32'], // Light green color from image
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A4:S4')->applyFromArray($headerStyle);

        // Style data cells
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A5:S' . ($row - 1))->applyFromArray($dataStyle);

        // Set numeric columns to center alignment
        $numericColumns = ['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S'];
        foreach ($numericColumns as $col) {
            $sheet->getStyle($col . '5:' . $col . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Apply title styles
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];

        $sheet->getStyle('A1:S2')->applyFromArray($titleStyle);

        // Apply conditional formatting to highlight important data
        $conditionalStyles = $sheet->getStyle('S5:S' . ($row - 1));
        $conditionalStyles->getConditionalStyles();

        $conditionalStyles = [];

        // Red for negative scores
        $conditionalStyleNegative = new Conditional();
        $conditionalStyleNegative->setConditionType(Conditional::CONDITION_CELLIS);
        $conditionalStyleNegative->setOperatorType(Conditional::OPERATOR_LESSTHAN);
        $conditionalStyleNegative->addCondition('0');
        $conditionalStyleNegative->getStyle()->getFont()->getColor()->setRGB('FF0000');
        $conditionalStyles[] = $conditionalStyleNegative;

        // Green for positive scores
        $conditionalStylePositive = new Conditional();
        $conditionalStylePositive->setConditionType(Conditional::CONDITION_CELLIS);
        $conditionalStylePositive->setOperatorType(Conditional::OPERATOR_GREATERTHANOREQUAL);
        $conditionalStylePositive->addCondition('0');
        $conditionalStylePositive->getStyle()->getFont()->getColor()->setRGB('008000');
        $conditionalStyles[] = $conditionalStylePositive;

        $sheet->getStyle('S5:S' . ($row - 1))->setConditionalStyles($conditionalStyles);
    }

    /**
     * Create final yearly report sheet with grades
     */
    // private function createFinalYearlySheet($spreadsheet, $data, $year)
    // {
    //     // Set active sheet
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $sheet->setTitle('FINAL');

    //     // Set company information
    //     $sheet->setCellValue('A1', 'LAPORAN KEDISIPLINAN TAHUNAN');
    //     $sheet->setCellValue('A2', 'Tahun: ' . $year);
    //     $sheet->mergeCells('A1:P1');
    //     $sheet->mergeCells('A2:P2');

    //     // Create "FINAL" header
    //     $sheet->setCellValue('A3', 'FINAL');
    //     $sheet->mergeCells('A3:P3');
    //     $sheet->getStyle('A3')->applyFromArray([
    //         'font' => ['bold' => true, 'size' => 12],
    //         'alignment' => [
    //             'horizontal' => Alignment::HORIZONTAL_CENTER,
    //         ],
    //         'fill' => [
    //             'fillType' => Fill::FILL_SOLID,
    //             'startColor' => ['rgb' => 'FFFF00'], // Yellow highlight for FINAL
    //         ],
    //     ]);

    //     // Set headers based on the screenshot shared
    //     $headers = [
    //         'A4' => 'NIK',
    //         'B4' => 'NAMA',
    //         'C4' => 'TTL KERJA/BL',
    //         'D4' => 'KEHADIRAN',
    //         'E4' => '%',
    //         'F4' => 'TERLAMBAT',
    //         'G4' => 'MASUK SIANG',
    //         'H4' => 'PULANG AWAL',
    //         'I4' => 'SAKIT',
    //         'J4' => 'ST',
    //         'K4' => 'SP',
    //         'L4' => 'KEHADIRAN',
    //         'M4' => 'TERLAMBAT',
    //         'N4' => 'MASUK SIANG',
    //         'O4' => 'PULANG AWAL',
    //         'P4' => 'ST',
    //         'Q4' => 'SP',
    //         'R4' => 'TOTAL',
    //         'S4' => 'GRADE',
    //     ];

    //     // Add SCORE header above score columns
    //     $sheet->setCellValue('L3', 'SCORE');
    //     $sheet->mergeCells('L3:S3');
    //     $sheet->getStyle('L3')->applyFromArray([
    //         'font' => ['bold' => true],
    //         'alignment' => [
    //             'horizontal' => Alignment::HORIZONTAL_CENTER,
    //         ],
    //         'fill' => [
    //             'fillType' => Fill::FILL_SOLID,
    //             'startColor' => ['rgb' => '9ACD32'], // Light green for score header
    //         ]
    //     ]);

    //     foreach ($headers as $cell => $value) {
    //         $sheet->setCellValue($cell, $value);
    //     }

    //     // Add data rows
    //     $row = 5;
    //     foreach ($data as $item) {
    //         $sheet->setCellValue('A' . $row, $item['employee_id']);
    //         $sheet->setCellValue('B' . $row, $item['name']);
    //         $sheet->setCellValue('C' . $row, $item['working_days']);
    //         $sheet->setCellValue('D' . $row, $item['presence']);
    //         $sheet->setCellValue('E' . $row, $item['attendance_percentage']);
    //         $sheet->setCellValue('F' . $row, $item['late_arrivals']);
    //         $sheet->setCellValue('G' . $row, $item['afternoon_shift_count']);
    //         $sheet->setCellValue('H' . $row, $item['early_departures']);
    //         $sheet->setCellValue('I' . $row, $item['sick_leave']);
    //         $sheet->setCellValue('J' . $row, $item['st_count']);
    //         $sheet->setCellValue('K' . $row, $item['sp_count']);

    //         // Score columns
    //         $sheet->setCellValue('L' . $row, $item['attendance_score']);
    //         $sheet->setCellValue('M' . $row, $item['late_score']);
    //         $sheet->setCellValue('N' . $row, $item['afternoon_shift_score']);
    //         $sheet->setCellValue('O' . $row, $item['early_departure_score']);
    //         $sheet->setCellValue('P' . $row, $item['st_score']);
    //         $sheet->setCellValue('Q' . $row, $item['sp_score']);
    //         $sheet->setCellValue('R' . $row, $item['total_score']);
    //         $sheet->setCellValue('S' . $row, $item['grade']);

    //         // Apply grade formatting
    //         if (!empty($item['grade'])) {
    //             $gradeColor = $this->getGradeColor($item['grade']);
    //             $sheet->getStyle('S' . $row)->applyFromArray([
    //                 'fill' => [
    //                     'fillType' => Fill::FILL_SOLID,
    //                     'startColor' => ['rgb' => $gradeColor],
    //                 ],
    //                 'font' => [
    //                     'bold' => true,
    //                     'color' => ['rgb' => 'FFFFFF'],
    //                 ],
    //             ]);
    //         }

    //         $row++;
    //     }

    //     // Set column widths
    //     $sheet->getColumnDimension('A')->setWidth(15);
    //     $sheet->getColumnDimension('B')->setWidth(30);

    //     for ($col = 'C'; $col <= 'S'; $col++) {
    //         $sheet->getColumnDimension($col)->setWidth(15);
    //     }

    //     // Style headers
    //     $headerStyle = [
    //         'font' => ['bold' => true],
    //         'alignment' => [
    //             'horizontal' => Alignment::HORIZONTAL_CENTER,
    //             'vertical' => Alignment::VERTICAL_CENTER,
    //         ],
    //         'fill' => [
    //             'fillType' => Fill::FILL_SOLID,
    //             'startColor' => ['rgb' => '9ACD32'], // Light green color
    //         ],
    //         'borders' => [
    //             'allBorders' => [
    //                 'borderStyle' => Border::BORDER_THIN,
    //             ],
    //         ],
    //     ];

    //     $sheet->getStyle('A4:S4')->applyFromArray($headerStyle);

    //     // Style data cells
    //     $dataStyle = [
    //         'borders' => [
    //             'allBorders' => [
    //                 'borderStyle' => Border::BORDER_THIN,
    //             ],
    //         ],
    //     ];

    //     $sheet->getStyle('A5:S' . ($row - 1))->applyFromArray($dataStyle);

    //     // Set numeric columns to center alignment
    //     $numericColumns = ['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S'];
    //     foreach ($numericColumns as $col) {
    //         $sheet->getStyle($col . '5:' . $col . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //     }

    //     // Apply title styles
    //     $titleStyle = [
    //         'font' => ['bold' => true, 'size' => 14],
    //         'alignment' => [
    //             'horizontal' => Alignment::HORIZONTAL_CENTER,
    //         ],
    //     ];

    //     $sheet->getStyle('A1:S2')->applyFromArray($titleStyle);
    // }


    /**
     * Create final yearly report sheet with grades
     */
    private function createFinalYearlySheet($spreadsheet, $data, $year)
    {
        // Set active sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('FINAL');

        // Set company information
        $sheet->setCellValue('A1', 'LAPORAN KEDISIPLINAN TAHUNAN');
        $sheet->setCellValue('A2', 'Tahun: ' . $year);
        $sheet->mergeCells('A1:S1');
        $sheet->mergeCells('A2:S2');

        // Create "FINAL" header
        $sheet->setCellValue('A3', 'FINAL');
        $sheet->mergeCells('A3:S3');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00'], // Yellow highlight for FINAL
            ],
        ]);

        // Add SCORE header above score columns
        $sheet->setCellValue('L3', 'SCORE');
        $sheet->mergeCells('L3:R3');
        $sheet->getStyle('L3')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '9ACD32'], // Light green for score header
            ]
        ]);

        // Set headers based on the requirements
        $headers = [
            'A4' => 'NIK',
            'B4' => 'NAMA',
            'C4' => 'JABATAN',
            'D4' => 'DEPARTEMEN',
            'E4' => 'TTL KERJA/TH',
            'F4' => 'KEHADIRAN',
            'G4' => '%',
            'H4' => 'TERLAMBAT',
            'I4' => 'IJIN',
            'J4' => 'MASUK SIANG',
            'K4' => 'PULANG AWAL',
            'L4' => 'KEHADIRAN',
            'M4' => 'TERLAMBAT',
            'N4' => 'MASUK SIANG',
            'O4' => 'PULANG AWAL',
            'P4' => 'ST',
            'Q4' => 'SP',
            'R4' => 'TOTAL',
            'S4' => 'GRADE',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Add data rows
        $row = 5;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['employee_id']);
            $sheet->setCellValue('B' . $row, $item['name']);
            $sheet->setCellValue('C' . $row, $item['position']);
            $sheet->setCellValue('D' . $row, $item['department']);
            $sheet->setCellValue('E' . $row, $item['working_days']);
            $sheet->setCellValue('F' . $row, $item['presence']);
            $sheet->setCellValue('G' . $row, $item['attendance_percentage']);
            $sheet->setCellValue('H' . $row, $item['late_arrivals']);
            $sheet->setCellValue('I' . $row, $item['permission']);
            $sheet->setCellValue('J' . $row, $item['afternoon_shift_count']);
            $sheet->setCellValue('K' . $row, $item['early_departures']);

            // Score columns
            $sheet->setCellValue('L' . $row, $item['attendance_score']);
            $sheet->setCellValue('M' . $row, $item['late_score']);
            $sheet->setCellValue('N' . $row, $item['afternoon_shift_score']);
            $sheet->setCellValue('O' . $row, $item['early_departure_score']);
            $sheet->setCellValue('P' . $row, $item['st_score']);
            $sheet->setCellValue('Q' . $row, $item['sp_score']);
            $sheet->setCellValue('R' . $row, $item['total_score']);
            $sheet->setCellValue('S' . $row, $item['grade']);

            // Apply grade formatting
            if (!empty($item['grade'])) {
                $gradeColor = $this->getGradeColor($item['grade']);
                $sheet->getStyle('S' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $gradeColor],
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                ]);
            }

            $row++;
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);

        for ($col = 'E'; $col <= 'S'; $col++) {
            $sheet->getColumnDimension($col)->setWidth(15);
        }

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '9ACD32'], // Light green color
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A4:S4')->applyFromArray($headerStyle);

        // Style data cells
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A5:S' . ($row - 1))->applyFromArray($dataStyle);

        // Set numeric columns to center alignment
        $numericColumns = ['E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S'];
        foreach ($numericColumns as $col) {
            $sheet->getStyle($col . '5:' . $col . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Apply title styles
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];

        $sheet->getStyle('A1:S2')->applyFromArray($titleStyle);

        // Apply conditional formatting to highlight important data
        $conditionalStyles = [];

        // Red for negative scores
        $conditionalStyleNegative = new Conditional();
        $conditionalStyleNegative->setConditionType(Conditional::CONDITION_CELLIS);
        $conditionalStyleNegative->setOperatorType(Conditional::OPERATOR_LESSTHAN);
        $conditionalStyleNegative->addCondition('0');
        $conditionalStyleNegative->getStyle()->getFont()->getColor()->setRGB('FF0000');
        $conditionalStyles[] = $conditionalStyleNegative;

        // Green for positive scores
        $conditionalStylePositive = new Conditional();
        $conditionalStylePositive->setConditionType(Conditional::CONDITION_CELLIS);
        $conditionalStylePositive->setOperatorType(Conditional::OPERATOR_GREATERTHANOREQUAL);
        $conditionalStylePositive->addCondition('0');
        $conditionalStylePositive->getStyle()->getFont()->getColor()->setRGB('008000');
        $conditionalStyles[] = $conditionalStylePositive;

        $sheet->getStyle('R5:R' . ($row - 1))->setConditionalStyles($conditionalStyles);
    }

    /**
     * Helper function to get the color for each grade
     */
    private function getGradeColor($grade)
    {
        $gradeColors = [
            'A' => '008000', // Green
            'B' => '0000FF', // Blue
            'C' => 'FFA500', // Orange
            'D' => 'FF0000', // Red
            'E' => '800000', // Dark Red
            'F' => '000000', // Black
        ];

        return $gradeColors[$grade] ?? '808080'; // Default gray if grade not found
    }








    // Performance Grade

    public function grade_performance_index()
    {
        $grades = RulePerformanceGrade::orderBy('min_score', 'desc')->get();
        return view('evaluation.rule.performance.grade.index', compact('grades'));
    }

    public function grade_performance_create()
    {
        return view('evaluation.rule.performance.grade.create');
    }

    public function grade_performance_store(Request $request)
    {
        // Validate basic requirements
        $request->validate([
            'grade' => 'required|string|max:2|unique:rule_performance_grades,grade',
            'min_score' => 'nullable|integer|min:0',
            'max_score' => 'nullable|integer|gte:min_score',
            'description' => 'nullable|string'
        ]);

        // Ensure min_score is never negative
        $min_score = $request->min_score ?? 0;
        if ($min_score < 0) {
            $min_score = 0;
        }

        // Check for overlapping ranges
        $this->validateOverlappingRanges($min_score, $request->max_score);

        // Create the record
        RulePerformanceGrade::create([
            'grade' => strtoupper($request->grade),
            'min_score' => $min_score,
            'max_score' => $request->max_score,
            'description' => $request->description
        ]);

        return redirect()->route('evaluation.rule.performance.grade.index')
            ->with('success', 'Performance grade rule created successfully.');
    }

    public function grade_performance_edit($id)
    {
        $grade = RulePerformanceGrade::findOrFail($id);
        return view('evaluation.rule.performance.grade.edit', compact('grade'));
    }

    public function grade_performance_update(Request $request, $id)
    {
        // Validate basic requirements
        $request->validate([
            'grade' => 'required|string|max:2|unique:rule_performance_grades,grade,' . $id,
            'min_score' => 'nullable|integer|min:0',
            'max_score' => 'nullable|integer|gte:min_score',
            'description' => 'nullable|string'
        ]);

        $grade = RulePerformanceGrade::findOrFail($id);

        // Ensure min_score is never negative
        $min_score = $request->min_score ?? 0;
        if ($min_score < 0) {
            $min_score = 0;
        }

        // Check for overlapping ranges, excluding the current record
        $this->validateOverlappingRanges($min_score, $request->max_score, $id);

        // Update the record
        $grade->update([
            'grade' => strtoupper($request->grade),
            'min_score' => $min_score,
            'max_score' => $request->max_score,
            'description' => $request->description
        ]);

        return redirect()->route('evaluation.rule.performance.grade.index')
            ->with('success', 'Performance grade rule updated successfully.');
    }

    /**
     * Check if the given range overlaps with existing ranges
     */
    private function validateOverlappingRanges($min_score, $max_score, $excludeId = null)
    {
        $query = RulePerformanceGrade::query();

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        // If max_score is NULL, then it means "above min_score"
        if ($max_score === null) {
            // Check if there's any range where min_score is >= our min_score
            $overlapping = $query->where('min_score', '>=', $min_score)->exists();

            // Or check if there's any range where max_score is > our min_score
            $overlapping = $overlapping || $query->where('max_score', '>', $min_score)->exists();
        } else {
            // Check for any overlapping ranges
            $overlapping = $query->where(function ($q) use ($min_score, $max_score) {
                // Case 1: Existing range starts within our range
                $q->where('min_score', '>=', $min_score)
                    ->where('min_score', '<', $max_score);
            })->orWhere(function ($q) use ($min_score, $max_score) {
                // Case 2: Existing range ends within our range
                $q->where('max_score', '>', $min_score)
                    ->where('max_score', '<=', $max_score);
            })->orWhere(function ($q) use ($min_score, $max_score) {
                // Case 3: Our range is completely within existing range
                $q->where('min_score', '<=', $min_score)
                    ->where(function ($q2) use ($max_score) {
                        $q2->where('max_score', '>=', $max_score)
                            ->orWhereNull('max_score');
                    });
            })->orWhere(function ($q) use ($min_score, $max_score) {
                // Case 4: Existing range is null for max (above min)
                $q->whereNull('max_score')
                    ->where('min_score', '<', $max_score);
            })->exists();
        }

        if ($overlapping) {
            abort(422, 'The score range overlaps with an existing grade range. Please check your min and max scores.');
        }

        return true;
    }

    // API method to check for overlaps (for AJAX validation)
    public function checkPerformanceOverlap(Request $request)
    {
        $min_score = $request->min_score ?? 0;
        if ($min_score < 0) {
            $min_score = 0;
        }

        $max_score = $request->max_score;
        $excludeId = $request->id;

        try {
            $this->validateOverlappingRanges($min_score, $max_score, $excludeId);
            return response()->json(['valid' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    public function grade_performance_destroy($id)
    {
        try {
            $grade = RulePerformanceGrade::findOrFail($id);
            $gradeName = $grade->grade;

            // Delete the record
            $grade->delete();

            return redirect()->route('evaluation.rule.performance.grade.index')
                ->with('success', "Performance grade rule '{$gradeName}' was deleted successfully.");
        } catch (\Exception $e) {
            return redirect()->route('evaluation.rule.performance.grade.index')
                ->with('error', "Failed to delete performance grade rule. Error: {$e->getMessage()}");
        }
    }



    // Discipline Grade

    public function rule_discipline_grade_index()
    {
        $grades = RuleDisciplineGrade::orderBy('min_score', 'desc')->get();
        return view('evaluation.rule.discipline.grade.index', compact('grades'));
    }

    public function rule_discipline_grade_create()
    {
        return view('evaluation.rule.discipline.grade.create');
    }

    public function rule_discipline_grade_store(Request $request)
    {
        // Validate basic requirements
        $request->validate([
            'grade' => 'required|string|max:2|unique:rule_discipline_grades,grade',
            'min_score' => 'nullable|integer|min:0',
            'max_score' => 'nullable|integer|gte:min_score',
            'description' => 'nullable|string'
        ]);

        // Ensure min_score is never negative
        $min_score = $request->min_score ?? 0;
        if ($min_score < 0) {
            $min_score = 0;
        }

        // Check for overlapping ranges
        $this->validateDisciplineOverlappingRanges($min_score, $request->max_score);

        // Create the record
        RuleDisciplineGrade::create([
            'grade' => strtoupper($request->grade),
            'min_score' => $min_score,
            'max_score' => $request->max_score,
            'description' => $request->description
        ]);

        return redirect()->route('evaluation.rule.discipline.grade.index')
            ->with('success', 'Discipline grade rule created successfully.');
    }

    public function rule_discipline_grade_edit($id)
    {
        $grade = RuleDisciplineGrade::findOrFail($id);
        return view('evaluation.rule.discipline.grade.edit', compact('grade'));
    }

    public function rule_discipline_grade_update(Request $request, $id)
    {
        // Validate basic requirements
        $request->validate([
            'grade' => 'required|string|max:2|unique:rule_discipline_grades,grade,' . $id,
            'min_score' => 'nullable|integer|min:0',
            'max_score' => 'nullable|integer|gte:min_score',
            'description' => 'nullable|string'
        ]);

        $grade = RuleDisciplineGrade::findOrFail($id);

        // Ensure min_score is never negative
        $min_score = $request->min_score ?? 0;
        if ($min_score < 0) {
            $min_score = 0;
        }

        // Check for overlapping ranges, excluding the current record
        $this->validateDisciplineOverlappingRanges($min_score, $request->max_score, $id);

        // Update the record
        $grade->update([
            'grade' => strtoupper($request->grade),
            'min_score' => $min_score,
            'max_score' => $request->max_score,
            'description' => $request->description
        ]);

        return redirect()->route('evaluation.rule.discipline.grade.index')
            ->with('success', 'Discipline grade rule updated successfully.');
    }

    public function rule_discipline_grade_destroy($id)
    {
        try {
            $grade = RuleDisciplineGrade::findOrFail($id);
            $gradeName = $grade->grade;

            // Delete the record
            $grade->delete();

            return redirect()->route('evaluation.rule.discipline.grade.index')
                ->with('success', "Discipline grade rule '{$gradeName}' was deleted successfully.");
        } catch (\Exception $e) {
            return redirect()->route('evaluation.rule.discipline.grade.index')
                ->with('error', "Failed to delete discipline grade rule. Error: {$e->getMessage()}");
        }
    }

    /**
     * Check if the given discipline range overlaps with existing ranges
     */
    private function validateDisciplineOverlappingRanges($min_score, $max_score, $excludeId = null)
    {
        $query = RuleDisciplineGrade::query();

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        // If max_score is NULL, then it means "above min_score"
        if ($max_score === null) {
            // Check if there's any range where min_score is >= our min_score
            $overlapping = $query->where('min_score', '>=', $min_score)->exists();

            // Or check if there's any range where max_score is > our min_score
            $overlapping = $overlapping || $query->where('max_score', '>', $min_score)->exists();
        } else {
            // Check for any overlapping ranges
            $overlapping = $query->where(function ($q) use ($min_score, $max_score) {
                // Case 1: Existing range starts within our range
                $q->where('min_score', '>=', $min_score)
                    ->where('min_score', '<', $max_score);
            })->orWhere(function ($q) use ($min_score, $max_score) {
                // Case 2: Existing range ends within our range
                $q->where('max_score', '>', $min_score)
                    ->where('max_score', '<=', $max_score);
            })->orWhere(function ($q) use ($min_score, $max_score) {
                // Case 3: Our range is completely within existing range
                $q->where('min_score', '<=', $min_score)
                    ->where(function ($q2) use ($max_score) {
                        $q2->where('max_score', '>=', $max_score)
                            ->orWhereNull('max_score');
                    });
            })->orWhere(function ($q) use ($min_score, $max_score) {
                // Case 4: Existing range is null for max (above min)
                $q->whereNull('max_score')
                    ->where('min_score', '<', $max_score);
            })->exists();
        }

        if ($overlapping) {
            abort(422, 'The score range overlaps with an existing discipline grade range. Please check your min and max scores.');
        }

        return true;
    }

    // API method to check for discipline overlaps (for AJAX validation)
    public function checkDisciplineOverlap(Request $request)
    {
        $min_score = $request->min_score ?? 0;
        if ($min_score < 0) {
            $min_score = 0;
        }

        $max_score = $request->max_score;
        $excludeId = $request->id;

        try {
            $this->validateDisciplineOverlappingRanges($min_score, $max_score, $excludeId);
            return response()->json(['valid' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => $e->getMessage()
            ]);
        }
    }




    // Add these methods to your EvaluationController class

    public function rule_elearning_grade_index()
    {
        $grades = RuleElearningGrade::orderBy('min_score', 'desc')->get();
        return view('evaluation.rule.elearning.grade.index', compact('grades'));
    }

    public function rule_elearning_grade_create()
    {
        return view('evaluation.rule.elearning.grade.create');
    }

    public function rule_elearning_grade_store(Request $request)
    {
        $request->validate([
            'grade' => 'required|string|max:2',
            'min_score' => 'required|integer|min:0|max:100',
            'max_score' => 'nullable|integer|min:0|max:100|gt:min_score',
            'description' => 'nullable|string',
        ]);
        // Check for overlapping ranges
        $overlap = $this->checkForElearningOverlap(
            $request->min_score,
            $request->max_score,
            null
        );
        if ($overlap) {
            return back()->withInput()->withErrors(['overlap' => $overlap]);
        }
        // Create the new grade rule
        RuleElearningGrade::create([
            'grade' => $request->grade,
            'min_score' => $request->min_score,
            'max_score' => $request->max_score,
            'description' => $request->description,
        ]);
        return redirect()->route('evaluation.rule.elearning.grade.index')
            ->with('success', 'E-learning grade rule created successfully.');
    }

    public function rule_elearning_grade_edit($id)
    {
        $grade = RuleElearningGrade::findOrFail($id);
        return view('evaluation/rule/elearning/grade/update', compact('grade'));
    }

    public function rule_elearning_grade_update(Request $request, $id)
    {
        $request->validate([
            'grade' => 'required|string|max:2',
            'min_score' => 'required|integer|min:0|max:100',
            'max_score' => 'nullable|integer|min:0|max:100|gt:min_score',
            'description' => 'nullable|string',
        ]);

        $grade = RuleElearningGrade::findOrFail($id);

        // Check for overlapping ranges
        $overlap = $this->checkForElearningOverlap(
            $request->min_score,
            $request->max_score,
            $id
        );

        if ($overlap) {
            return back()->withInput()->withErrors(['overlap' => $overlap]);
        }

        // Update the grade rule
        $grade->update([
            'grade' => $request->grade,
            'min_score' => $request->min_score,
            'max_score' => $request->max_score,
            'description' => $request->description,
        ]);

        return redirect()->route('evaluation.rule.elearning.grade.index')->with('success', 'E-learning grade rule updated successfully.');
    }

    public function grade_elearning_destroy($id)
    {
        $grade = RuleElearningGrade::findOrFail($id);
        $grade->delete();

        return redirect()->route('evaluation.rule.elearning.grade.index')
            ->with('success', 'E-learning grade rule deleted successfully.');
    }

    private function checkForElearningOverlap($minScore, $maxScore = null, $excludeId = null)
    {
        $query = RuleElearningGrade::query();

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        // If max_score is null, set it to 100 for comparison
        $actualMaxScore = $maxScore ?? 100;

        $overlapping = $query->where(function ($query) use ($minScore, $actualMaxScore) {
            // Case 1: The start of our range is inside an existing range
            $query->orWhere(function ($q) use ($minScore) {
                $q->where('min_score', '<=', $minScore)
                    ->where(function ($sq) use ($minScore) {
                        $sq->whereNull('max_score')
                            ->orWhere('max_score', '>', $minScore);
                    });
            });

            // Case 2: The end of our range is inside an existing range
            $query->orWhere(function ($q) use ($actualMaxScore) {
                $q->where('min_score', '<', $actualMaxScore)
                    ->where(function ($sq) use ($actualMaxScore) {
                        $sq->whereNull('max_score')
                            ->orWhere('max_score', '>=', $actualMaxScore);
                    });
            });

            // Case 3: Our range completely contains an existing range
            $query->orWhere(function ($q) use ($minScore, $actualMaxScore) {
                $q->where('min_score', '>=', $minScore)
                    ->where(function ($sq) use ($actualMaxScore) {
                        $sq->whereNull('max_score')
                            ->orWhere('max_score', '<=', $actualMaxScore);
                    });
            });
        })->first();

        if ($overlapping) {
            $maxText = $overlapping->max_score ?? '100';
            return "The score range overlaps with existing grade '{$overlapping->grade}' ({$overlapping->min_score}-{$maxText}).";
        }

        return null;
    }

    public function checkElearningOverlap(Request $request)
    {
        $validatedData = $request->validate([
            'min_score' => 'required|integer|min:0|max:100',
            'max_score' => 'nullable|integer|min:0|max:100|gt:min_score',
            'id' => 'nullable|integer|exists:rule_elearning_grades,id',
        ]);

        $overlap = $this->checkForElearningOverlap(
            $validatedData['min_score'],
            $validatedData['max_score'] ?? null,
            $validatedData['id'] ?? null
        );

        return response()->json([
            'valid' => $overlap === null,
            'message' => $overlap ?: 'No overlap detected'
        ]);
    }



    //Report Elearning
    public function report_elearning_index(Request $request)
    {
        // Get available years from invitation data
        $years = elearning_invitation::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // If no years available, use current year
        if (empty($years)) {
            $years = [Carbon::now()->year];
        }

        // Get selected year, default to the most recent year
        $selectedYear = $request->input('year', $years[0]);

        // Get all departments and positions for filters
        $allDepartments = EmployeeDepartment::orderBy('department')->get();
        $allPositions = EmployeePosition::orderBy('position')->get();

        // Get grading rules for the info box
        $gradeRules = RuleElearningGrade::orderBy('min_score', 'desc')->get();

        // Get employees with e-learning invitations for the selected year
        $employeesWithElearning = User::select('users.id')
            ->join('elearning_invitation', 'users.id', '=', 'elearning_invitation.users_id')
            ->whereYear('elearning_invitation.created_at', $selectedYear)
            ->groupBy('users.id')
            ->get()
            ->pluck('id')
            ->toArray();

        // Get historical positions and departments for these employees
        $userHistoricalPositions = [];
        $userHistoricalDepartments = [];
        $historicalPositionIds = collect();
        $historicalDepartmentIds = collect();

        foreach ($employeesWithElearning as $userId) {
            // Find the earliest elearning invitation date for this user in the selected year
            $referenceDate = elearning_invitation::where('users_id', $userId)
                ->whereYear('created_at', $selectedYear)
                ->min('created_at');

            if ($referenceDate) {
                // Find the most recent transfer before the reference date
                $historicalTransfer = \App\Models\history_transfer_employee::where('users_id', $userId)
                    ->where('created_at', '<', $referenceDate)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $user = User::find($userId);

                if ($historicalTransfer) {
                    // Use the historical position and department
                    $userHistoricalPositions[$userId] = $historicalTransfer->new_position_id;
                    $userHistoricalDepartments[$userId] = $historicalTransfer->new_department_id;

                    $historicalPositionIds->push($historicalTransfer->new_position_id);
                    $historicalDepartmentIds->push($historicalTransfer->new_department_id);
                } else {
                    // No transfer history found, use current position and department
                    $userHistoricalPositions[$userId] = $user->position_id;
                    $userHistoricalDepartments[$userId] = $user->department_id;

                    $historicalPositionIds->push($user->position_id);
                    $historicalDepartmentIds->push($user->department_id);
                }
            }
        }

        // Apply filters
        $selectedDepartment = $request->input('department_id');
        $selectedPosition = $request->input('position_id');

        // Filter users by historical position and department if requested
        $filteredUserIds = collect($employeesWithElearning);

        if ($selectedPosition) {
            $filteredUserIds = $filteredUserIds->filter(function ($userId) use ($userHistoricalPositions, $selectedPosition) {
                return isset($userHistoricalPositions[$userId]) && $userHistoricalPositions[$userId] == $selectedPosition;
            });
        }

        if ($selectedDepartment) {
            $filteredUserIds = $filteredUserIds->filter(function ($userId) use ($userHistoricalDepartments, $selectedDepartment) {
                return isset($userHistoricalDepartments[$userId]) && $userHistoricalDepartments[$userId] == $selectedDepartment;
            });
        }

        // Get departments and positions from historical data for the filter dropdowns
        $positions = EmployeePosition::whereIn('id', $historicalPositionIds->unique())
            ->orderBy('position')
            ->get();

        $departments = EmployeeDepartment::whereIn('id', $historicalDepartmentIds->unique())
            ->orderBy('department')
            ->get();

        // Get the employees with their historical position and department
        $employees = collect();

        foreach ($filteredUserIds as $userId) {
            $user = User::find($userId);
            $historicalPositionId = $userHistoricalPositions[$userId] ?? $user->position_id;
            $historicalDepartmentId = $userHistoricalDepartments[$userId] ?? $user->department_id;

            $positionName = EmployeePosition::where('id', $historicalPositionId)
                ->value('position') ?? '';

            $departmentName = EmployeeDepartment::where('id', $historicalDepartmentId)
                ->value('department') ?? '';

            // Create a custom object with the required data
            $employeeData = (object)[
                'id' => $user->id,
                'name' => $user->name,
                'employee_id' => $user->employee_id,
                'position' => $positionName,
                'department' => $departmentName,
                'historical_position_id' => $historicalPositionId,
                'historical_department_id' => $historicalDepartmentId
            ];

            $employees->push($employeeData);
        }

        // Calculate final scores for each employee
        foreach ($employees as $employee) {
            $yearScores = $this->calculateYearScores($employee->id, $selectedYear);
            $employee->final_percentage = $yearScores['final_percentage'];
            $employee->final_grade = $yearScores['final_grade'];
            $employee->grade_description = $yearScores['grade_description'];
        }

        return view('evaluation.report.elearning.index', compact(
            'employees',
            'years',
            'departments',
            'positions',
            'selectedYear',
            'selectedDepartment',
            'selectedPosition',
            'gradeRules'
        ));
    }
    public function report_elearning_detail($employee_id, $year = null)
    {
        $employee = User::findOrFail($employee_id);

        if (!$year) {
            // Get the latest year with invitations for this employee
            $latestYear = elearning_invitation::where('users_id', $employee_id)
                ->orderBy('created_at', 'desc')
                ->first();

            $year = $latestYear ? Carbon::parse($latestYear->created_at)->year : Carbon::now()->year;
        }

        // Get monthly data
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyData[$month] = $this->getMonthData($employee_id, $year, $month);
        }

        // Calculate final scores
        $yearScores = $this->calculateYearScores($employee_id, $year);

        return view('evaluation.report.elearning.detail', compact(
            'employee',
            'year',
            'monthlyData',
            'yearScores'
        ));
    }

    private function getMonthData($employee_id, $year, $month)
    {
        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $invitations = elearning_invitation::select(
            'elearning_invitation.id as invitation_id',
            'elearning_invitation.lesson_id',
            'elearning_invitation.schedule_id',
            'elearning_lesson.name as lesson_name',
            'elearning_lesson.passing_grade',
            'elearning_schedule.start_date',
            'elearning_schedule.end_date'
        )
            ->join('elearning_lesson', 'elearning_invitation.lesson_id', '=', 'elearning_lesson.id')
            ->join('elearning_schedule', 'elearning_invitation.schedule_id', '=', 'elearning_schedule.id')
            ->where('elearning_invitation.users_id', $employee_id)
            ->whereBetween('elearning_schedule.start_date', [$start, $end])
            ->get();

        $now = Carbon::now();

        foreach ($invitations as $invitation) {
            // Check if there are answers for this invitation
            $answers = elearning_answer::where('invitation_id', $invitation->invitation_id)
                ->where('users_id', $employee_id)
                ->get();

            if ($answers->count() > 0) {
                // Get the total possible points for all questions
                $totalPossiblePoints = 0;
                $earnedPoints = 0;

                // Calculate total points and earned points
                foreach ($answers as $answer) {
                    // Get grade value for this question (from the grade column)
                    $questionValue = $answer->grade;
                    $totalPossiblePoints += $questionValue;

                    // If the answer matches the answer_key, add the points
                    if ($answer->answer == $answer->answer_key) {
                        $earnedPoints += $questionValue;
                    }
                }

                // Calculate raw score (earned points)
                $invitation->raw_score = $earnedPoints;

                // Store total possible points
                $invitation->total_possible = $totalPossiblePoints;

                // Calculate score percentage (out of possible points)
                $invitation->score_percentage = $totalPossiblePoints > 0 ?
                    round(($earnedPoints / $totalPossiblePoints) * 100, 2) : 0;

                // Calculate passing grade percentage relative to total possible points
                $invitation->passing_grade_percentage = $totalPossiblePoints > 0 ?
                    round(($invitation->passing_grade / $totalPossiblePoints) * 100, 2) : 0;

                $invitation->status = 'Completed';
            } else {
                // No answers found
                $endDate = Carbon::parse($invitation->end_date);

                if ($now->gt($endDate)) {
                    // Past deadline, not completed
                    $invitation->raw_score = 0;
                    $invitation->total_possible = 0;
                    $invitation->score_percentage = 0;
                    $invitation->passing_grade_percentage = 0;
                    $invitation->status = 'Not Completed';
                } else {
                    // Still has time to complete
                    $invitation->raw_score = '-';
                    $invitation->total_possible = '-';
                    $invitation->score_percentage = '-';
                    $invitation->passing_grade_percentage = '-';
                    $invitation->status = 'Not Yet Completed';
                }
            }

            // Get grade based on score percentage - use floor to round down decimal scores
            if (is_numeric($invitation->score_percentage)) {
                $scoreFloor = floor($invitation->score_percentage); // Round down decimal scores

                $grade = RuleElearningGrade::where('min_score', '<=', $scoreFloor)
                    ->where(function ($query) use ($scoreFloor) {
                        $query->where('max_score', '>=', $scoreFloor)
                            ->orWhereNull('max_score');
                    })
                    ->orderBy('min_score', 'desc')
                    ->first();

                $invitation->grade = $grade ? $grade->grade : '-';
                $invitation->grade_description = $grade ? $grade->description : '-';
            } else {
                $invitation->grade = '-';
                $invitation->grade_description = '-';
            }
        }

        return $invitations;
    }
    private function calculateYearScores($employee_id, $year)
    {
        $totalPercentage = 0;
        $totalLessons = 0;

        for ($month = 1; $month <= 12; $month++) {
            $monthData = $this->getMonthData($employee_id, $year, $month);

            foreach ($monthData as $invitation) {
                // Fix: Changed 'percentage' to 'score_percentage'
                if (is_numeric($invitation->score_percentage)) {
                    $totalPercentage += $invitation->score_percentage;
                    $totalLessons++;
                }
            }
        }

        // Calculate final percentage
        $finalPercentage = $totalLessons > 0 ? round($totalPercentage / $totalLessons, 2) : 0;

        // Floor the percentage for grade determination
        $finalPercentageFloor = floor($finalPercentage);

        // Get grade based on final percentage
        $grade = RuleElearningGrade::where('min_score', '<=', $finalPercentageFloor)
            ->where(function ($query) use ($finalPercentageFloor) {
                $query->where('max_score', '>=', $finalPercentageFloor)
                    ->orWhereNull('max_score');
            })
            ->orderBy('min_score', 'desc')
            ->first();

        return [
            'final_percentage' => $finalPercentage,
            'final_grade' => $grade ? $grade->grade : '-',
            'grade_description' => $grade ? $grade->description : '-'
        ];
    }
    public function report_elearning_detail_answers($invitation_id)
    {
        $invitation = elearning_invitation::findOrFail($invitation_id);

        $answers = elearning_answer::where('invitation_id', $invitation_id)
            ->orderBy('id')
            ->get();

        $lesson = elearning_lesson::find($invitation->lesson_id);

        return response()->json([
            'invitation' => $invitation,
            'answers' => $answers,
            'lesson' => $lesson
        ]);
    }


    public function report_elearning_export(Request $request)
    {
        // Get employee ID from request or session
        $employee_id = $request->input('employee_id');
        $year = $request->input('year');

        // Check if employee_id is missing but available in session/referer
        if (!$employee_id && session()->has('employee_id')) {
            $employee_id = session('employee_id');
        }

        // Make sure we have an employee ID
        if (!$employee_id) {
            return redirect()->back()->with('error', 'Employee ID is required for export');
        }


        // Find employee
        $employee = User::findOrFail($employee_id);

        if (!$year) {
            // Get the latest year with invitations for this employee
            $latestYear = elearning_invitation::where('users_id', $employee_id)
                ->orderBy('created_at', 'desc')
                ->first();

            $year = $latestYear ? Carbon::parse($latestYear->created_at)->year : Carbon::now()->year;
        }

        // Get yearly scores for the final sheet
        $yearScores = $this->calculateYearScores($employee_id, $year);

        // Create Excel file
        $spreadsheet = new Spreadsheet();

        // Remove the default worksheet
        $spreadsheet->removeSheetByIndex(0);

        // Month names
        $monthNames = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];

        // Add monthly sheets
        foreach ($monthNames as $monthNum => $monthName) {
            $monthData = $this->getMonthData($employee_id, $year, $monthNum);

            // Only create sheets for months with data
            if (count($monthData) > 0) {
                // Create a new sheet
                $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $monthName);
                $spreadsheet->addSheet($sheet);

                // Employee Information Header
                $sheet->setCellValue('A1', 'Employee Information');
                $sheet->setCellValue('A2', 'ID');
                $sheet->setCellValue('B2', $employee->employee_id);
                $sheet->setCellValue('D2', 'Name');
                $sheet->setCellValue('E2', $employee->name);

                $department = EmployeeDepartment::find($employee->department_id);
                $position = EmployeePosition::find($employee->position_id);

                $sheet->setCellValue('A3', 'Department');
                $sheet->setCellValue('B3', $department ? $department->department : '');
                $sheet->setCellValue('D3', 'Position');
                $sheet->setCellValue('E3', $position ? $position->position : '');

                $sheet->setCellValue('A4', 'Year');
                $sheet->setCellValue('B4', $year);

                // Style employee info header
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->mergeCells('A1:H1');
                $sheet->getStyle('A2:A4')->getFont()->setBold(true);
                $sheet->getStyle('D2:D4')->getFont()->setBold(true);

                // Lesson table headers
                $headers = [
                    'No',
                    'Lesson Name',
                    'Start Date',
                    'End Date',
                    'Passing Grade',
                    'Passing Grade (%)',
                    'Raw Score',
                    'Score (%)',
                    'Grade',
                    'Status'
                ];

                $col = 'A';
                $headerRow = 6; // Leave a blank row after employee info

                foreach ($headers as $header) {
                    $sheet->setCellValue($col . $headerRow, $header);
                    $col++;
                }

                // Style headers
                $headerRange = 'A' . $headerRow . ':' . 'J' . $headerRow;
                $sheet->getStyle($headerRange)->getFont()->setBold(true);
                $sheet->getStyle($headerRange)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('D1E7DD');
                $sheet->getStyle($headerRange)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Add lesson data
                $row = $headerRow + 1;
                $count = 1;

                foreach ($monthData as $lesson) {
                    $sheet->setCellValue('A' . $row, $count);
                    $sheet->setCellValue('B' . $row, $lesson->lesson_name);
                    $sheet->setCellValue('C' . $row, $lesson->start_date ? Carbon::parse($lesson->start_date)->format('d M Y') : '-');
                    $sheet->setCellValue('D' . $row, $lesson->end_date ? Carbon::parse($lesson->end_date)->format('d M Y') : '-');
                    $sheet->setCellValue('E' . $row, $lesson->passing_grade);
                    $sheet->setCellValue('F' . $row, is_numeric($lesson->passing_grade_percentage) ? $lesson->passing_grade_percentage . '%' : '-');
                    $sheet->setCellValue('G' . $row, is_numeric($lesson->raw_score) ? $lesson->raw_score : '-');
                    $sheet->setCellValue('H' . $row, is_numeric($lesson->score_percentage) ? $lesson->score_percentage . '%' : '-');
                    $sheet->setCellValue('I' . $row, $lesson->grade);
                    $sheet->setCellValue('J' . $row, $lesson->status);

                    // Style completed/not completed cells
                    if ($lesson->status == 'Completed') {
                        $sheet->getStyle('J' . $row)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('D1E7DD');
                    } else if ($lesson->status == 'Not Completed') {
                        $sheet->getStyle('J' . $row)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('F8D7DA');
                    }

                    $row++;
                    $count++;
                }

                // Auto size columns
                foreach (range('A', 'J') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }
            }
        }


        // Create Final sheet (always include this one)
        $finalSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Final');
        $spreadsheet->addSheet($finalSheet);

        // Employee information on Final sheet
        $finalSheet->setCellValue('A1', 'E-Learning Detail Report');
        $finalSheet->setCellValue('A2', 'ID');
        $finalSheet->setCellValue('B2', $employee->employee_id);
        $finalSheet->setCellValue('D2', 'Name');
        $finalSheet->setCellValue('E2', $employee->name);

        $department = EmployeeDepartment::find($employee->department_id);
        $position = EmployeePosition::find($employee->position_id);

        $finalSheet->setCellValue('A3', 'Department');
        $finalSheet->setCellValue('B3', $department ? $department->department : '');
        $finalSheet->setCellValue('D3', 'Position');
        $finalSheet->setCellValue('E3', $position ? $position->position : '');

        $finalSheet->setCellValue('A4', 'Year');
        $finalSheet->setCellValue('B4', $year);

        // Final score information
        $finalSheet->setCellValue('A6', 'Final Grade');
        $finalSheet->setCellValue('B6', $yearScores['final_grade']);
        $finalSheet->setCellValue('A7', 'Description');
        $finalSheet->setCellValue('B7', $yearScores['grade_description']);
        $finalSheet->setCellValue('A8', 'Final Score');
        $finalSheet->setCellValue('B8', $yearScores['final_percentage'] . '%');

        // Style final score section
        $finalSheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $finalSheet->mergeCells('A1:H1');
        $finalSheet->getStyle('A2:A4')->getFont()->setBold(true);
        $finalSheet->getStyle('D2:D4')->getFont()->setBold(true);
        $finalSheet->getStyle('A6:A8')->getFont()->setBold(true);
        $finalSheet->getStyle('B6')->getFont()->setSize(14);

        // Monthly summary on Final sheet
        $finalSheet->setCellValue('A10', 'Month');
        $finalSheet->setCellValue('B10', 'Total Lessons');
        $finalSheet->setCellValue('C10', 'Completed');
        $finalSheet->setCellValue('D10', 'Average Score');

        // Style monthly summary header
        $finalSheet->getStyle('A10:D10')->getFont()->setBold(true);
        $finalSheet->getStyle('A10:D10')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D1E7DD');
        $finalSheet->getStyle('A10:D10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add monthly summary data
        $summaryRow = 11;

        foreach ($monthNames as $monthNum => $monthName) {
            $monthData = $this->getMonthData($employee_id, $year, $monthNum);

            if (count($monthData) > 0) {
                $totalLessons = count($monthData);
                $completedLessons = 0;
                $totalScore = 0;
                $scoredLessons = 0;

                foreach ($monthData as $lesson) {
                    if ($lesson->status == 'Completed') {
                        $completedLessons++;
                    }

                    if (is_numeric($lesson->score_percentage)) {
                        $totalScore += $lesson->score_percentage;
                        $scoredLessons++;
                    }
                }

                $averageScore = $scoredLessons > 0 ? round($totalScore / $scoredLessons, 2) . '%' : '-';

                $finalSheet->setCellValue('A' . $summaryRow, $monthName);
                $finalSheet->setCellValue('B' . $summaryRow, $totalLessons);
                $finalSheet->setCellValue('C' . $summaryRow, $completedLessons);
                $finalSheet->setCellValue('D' . $summaryRow, $averageScore);

                $summaryRow++;
            }
        }

        // Auto size columns on final sheet
        foreach (range('A', 'D') as $columnID) {
            $finalSheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set the first sheet as active
        $spreadsheet->setActiveSheetIndex(0);

        // Create the Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'E-Learning_Report_' . $employee->employee_id . '_' . $employee->name . '_' . $year . '.xlsx';

        // Create a temporary file
        $temp_file = tempnam(sys_get_temp_dir(), 'elearning_report');
        $writer->save($temp_file);

        // Return the file as a download
        return response()->download($temp_file, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }



    /**
     * Show the final evaluation report index
     */
    public function reportFinalCalculateIndex(Request $request)
    {
        // Get current year for default filtering
        $currentYear = date('Y');
        $selectedYear = $request->input('year', $currentYear);

        // Get filter parameters
        $employeeFilter = $request->input('employee');
        $positionFilter = $request->input('position');
        $departmentFilter = $request->input('department');

        // Get available years from evaluations
        $availableYears = EvaluationPerformance::select(DB::raw('DISTINCT YEAR(date) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [$currentYear];
        }

        // Find users that have any evaluation data at all
        $usersWithData = DB::table('users')
            ->select('users.id')
            ->distinct()
            ->where(function ($query) use ($selectedYear) {
                // Has performance evaluation
                $query->whereExists(function ($q) use ($selectedYear) {
                    $q->select(DB::raw(1))
                        ->from('employee_evaluation_performance')
                        ->whereRaw('employee_evaluation_performance.user_id = users.id')
                        ->whereYear('employee_evaluation_performance.date', $selectedYear);
                })
                    // Or has discipline record (absence)
                    ->orWhereExists(function ($q) use ($selectedYear) {
                        $q->select(DB::raw(1))
                            ->from('employee_absent')
                            ->whereRaw('employee_absent.user_id = users.id')
                            ->whereYear('employee_absent.date', $selectedYear);
                    })
                    // Or has e-learning record
                    ->orWhereExists(function ($q) use ($selectedYear) {
                        $q->select(DB::raw(1))
                            ->from('elearning_invitation')
                            ->whereRaw('elearning_invitation.users_id = users.id')
                            ->whereYear('elearning_invitation.created_at', $selectedYear);
                    });
            })
            ->when($employeeFilter, function ($query) use ($employeeFilter) {
                return $query->where('users.id', $employeeFilter);
            })
            ->pluck('id')
            ->toArray();

        // Get basic user data
        $users = User::whereIn('id', $usersWithData)->get();

        // Get filter data (without assuming relationships)
        $employeesList = User::whereIn('id', $usersWithData)
            ->select('id', 'name', 'employee_id')
            ->orderBy('name')
            ->get();

        // Initialize collections for positions and departments
        $historicalPositionIds = collect();
        $historicalDepartmentIds = collect();
        $userHistoricalPositions = [];
        $userHistoricalDepartments = [];

        // For each user, find their historical position and department
        foreach ($users as $user) {
            // Get the reference date for this user (we'll use the earliest event date in the selected year)
            // Fixed the SQL query construction
            $minPerformanceDate = DB::table('employee_evaluation_performance')
                ->where('user_id', $user->id)
                ->whereYear('date', $selectedYear)
                ->min('date');

            $minAbsentDate = DB::table('employee_absent')
                ->where('user_id', $user->id)
                ->whereYear('date', $selectedYear)
                ->min('date');

            $minElearningDate = DB::table('elearning_invitation')
                ->where('users_id', $user->id)
                ->whereYear('created_at', $selectedYear)
                ->min('created_at');

            // Find the earliest date among all three types
            $dates = array_filter([$minPerformanceDate, $minAbsentDate, $minElearningDate]);
            $referenceDate = !empty($dates) ? min($dates) : null;

            if ($referenceDate) {
                // Find the most recent transfer before the reference date
                $historicalTransfer = \App\Models\history_transfer_employee::where('users_id', $user->id)
                    ->where('created_at', '<', $referenceDate)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($historicalTransfer) {
                    // Use the historical position and department
                    $userHistoricalPositions[$user->id] = $historicalTransfer->new_position_id;
                    $userHistoricalDepartments[$user->id] = $historicalTransfer->new_department_id;

                    $historicalPositionIds->push($historicalTransfer->new_position_id);
                    $historicalDepartmentIds->push($historicalTransfer->new_department_id);
                } else {
                    // No transfer history found, use current position and department
                    $userHistoricalPositions[$user->id] = $user->position_id;
                    $userHistoricalDepartments[$user->id] = $user->department_id;

                    $historicalPositionIds->push($user->position_id);
                    $historicalDepartmentIds->push($user->department_id);
                }
            } else {
                // No reference date found, use current values
                $userHistoricalPositions[$user->id] = $user->position_id;
                $userHistoricalDepartments[$user->id] = $user->department_id;

                $historicalPositionIds->push($user->position_id);
                $historicalDepartmentIds->push($user->department_id);
            }
        }

        // Filter users by historical position and department if requested
        $filteredUsers = collect($usersWithData);

        if ($positionFilter) {
            $filteredUsers = $filteredUsers->filter(function ($userId) use ($userHistoricalPositions, $positionFilter) {
                return isset($userHistoricalPositions[$userId]) && $userHistoricalPositions[$userId] == $positionFilter;
            });
        }

        if ($departmentFilter) {
            $filteredUsers = $filteredUsers->filter(function ($userId) use ($userHistoricalDepartments, $departmentFilter) {
                return isset($userHistoricalDepartments[$userId]) && $userHistoricalDepartments[$userId] == $departmentFilter;
            });
        }

        // Update users array with filtered list
        $users = User::whereIn('id', $filteredUsers->toArray())->get();

        // Get positions and departments for display and filtering
        $positionsList = EmployeePosition::whereIn('id', $historicalPositionIds->unique())
            ->select('id', 'position')
            ->orderBy('position')
            ->get();

        $departmentsList = EmployeeDepartment::whereIn('id', $historicalDepartmentIds->unique())
            ->select('id', 'department')
            ->orderBy('department')
            ->get();

        // Initialize empty data array with just user info and historical positions/departments
        $finalData = [];
        foreach ($users as $user) {
            $historicalPositionId = $userHistoricalPositions[$user->id] ?? $user->position_id;
            $historicalDepartmentId = $userHistoricalDepartments[$user->id] ?? $user->department_id;

            $finalData[] = [
                'user_id' => $user->id,
                'employee_id' => $user->employee_id ?? '',
                'name' => $user->name,
                'position' => $historicalPositionId ?
                    ($positionsList->firstWhere('id', $historicalPositionId)->position ?? '') : '',
                'department' => $historicalDepartmentId ?
                    ($departmentsList->firstWhere('id', $historicalDepartmentId)->department ?? '') : '',
                'performance' => null,
                'discipline' => null,
                'elearning' => null
            ];
        }

        return view('evaluation.report.final.calculate.index', compact(
            'finalData',
            'employeesList',
            'positionsList',
            'departmentsList',
            'availableYears',
            'selectedYear',
            'filteredUsers' // Pass filtered users instead of all usersWithData
        ));
    }

    public function getFinalCalculateReportData(Request $request)
    {
        set_time_limit(900);
        $userIds = $request->input('userIds', []);
        $year = $request->input('year');

        if (empty($userIds) || !$year) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        if (is_string($userIds)) {
            $userIds = json_decode($userIds, true);
        }

        // Limit to first 15 users only
        // $limitedUserIds = array_slice($userIds, 0, 15);
        // $performanceData = $this->getPerformanceData($limitedUserIds, $year);
        // $disciplineData = $this->getDisciplineData($limitedUserIds, $year);
        // $elearningData = $this->getElearningData($limitedUserIds, $year);

        // dd($userIds);
        $performanceData = $this->getPerformanceData($userIds, $year);
        $disciplineData = $this->getDisciplineData($userIds, $year);
        $elearningData = $this->getElearningData($userIds, $year);

        return response()->json([
            'performance' => $performanceData,
            'discipline' => $disciplineData,
            'elearning' => $elearningData
        ]);
    }

    /**
     * Get performance data for employees
     */
    private function getPerformanceData($employeeIds, $year)
    {
        set_time_limit(900);
        $data = [];

        // Get all evaluations for these users in the selected year
        $evaluations = EvaluationPerformance::whereIn('user_id', $employeeIds)
            ->whereYear('date', $year)
            ->get()
            ->groupBy('user_id');

        // Get performance grade rules
        $gradeRules = RulePerformanceGrade::orderBy('min_score', 'desc')->get();

        foreach ($evaluations as $userId => $userEvaluations) {
            $totalScore = $userEvaluations->sum('total_score');
            $totalReduction = $userEvaluations->sum('total_reduction');
            $monthCount = $userEvaluations->count();

            if ($monthCount > 0) {
                $averageScore = $totalScore / $monthCount;
                $finalScore = $averageScore - $totalReduction;

                $grade = $gradeRules->first(function ($rule) use ($finalScore) {
                    $belowMaxOrNoMax = ($rule->max_score === null || $finalScore <= $rule->max_score);
                    $aboveMinOrNoMin = ($rule->min_score === null || $finalScore >= $rule->min_score);
                    return $aboveMinOrNoMin && $belowMaxOrNoMax;
                });

                $data[$userId] = [
                    'score' => round($finalScore, 2),
                    'grade' => $grade ? $grade->grade : 'E',
                    'description' => $grade ? $grade->description : 'Poor'
                ];
            }
        }

        return $data;
    }

    /**
     * Get discipline data for employees
     */
    private function getDisciplineData($employeeIds, $year)
    {
        set_time_limit(900);
        $data = [];

        // Get grade rules from database
        $gradeRules = RuleDisciplineGrade::orderBy('min_score', 'desc')->get();

        // Get discipline data for each employee
        foreach ($employeeIds as $employeeId) {
            $employee = User::find($employeeId);
            if (!$employee) continue;

            $yearlyData = $this->calculateDisciplineYearlyData($employee, $year);

            if ($yearlyData && isset($yearlyData['total_score']) && $yearlyData['total_score'] !== '') {
                // Find grade based on score
                $grade = 'E'; // Default to lowest grade
                $description = 'Poor';

                foreach ($gradeRules as $rule) {
                    if (
                        $yearlyData['total_score'] >= $rule->min_score &&
                        ($rule->max_score === null || $yearlyData['total_score'] <= $rule->max_score)
                    ) {
                        $grade = $rule->grade;
                        $description = $rule->description;
                        break;
                    }
                }

                $data[$employeeId] = [
                    'score' => round($yearlyData['total_score'], 2),
                    'grade' => $grade,
                    'description' => $description
                ];
            }
        }

        return $data;
    }

    /**
     * Calculate discipline yearly data for an employee
     */
    private function calculateDisciplineYearlyData($employee, $year)
    {
        set_time_limit(900);

        // Get discipline rules once
        $attendanceRules = DisciplineRule::where('rule_type', 'attendance')->orderBy('min_value', 'desc')->get();
        $lateRules = DisciplineRule::where('rule_type', 'late')->orderBy('min_value', 'asc')->get();
        $afternoonShiftRule = DisciplineRule::where('rule_type', 'afternoon_shift')->first();
        $earlyLeaveRule = DisciplineRule::where('rule_type', 'early_leave')->first();
        $stRule = DisciplineRule::where('rule_type', 'st')->first();
        $spRule = DisciplineRule::where('rule_type', 'sp')->first();

        // Initialize total scores
        $totalAttendanceScore = 0;
        $totalLateScore = 0;
        $totalAfternoonShiftScore = 0;
        $totalEarlyDepartureScore = 0;
        $totalStScore = 0;
        $totalSpScore = 0;

        // Initialize yearly counters for display
        $yearlyData = [
            'working_days' => 0,
            'presence' => 0,
            'late_arrivals' => 0,
            'permission' => 0,
            'afternoon_shift_count' => 0,
            'early_departures' => 0,
            'sick_leave' => 0,
            'st_count' => 0,
            'sp_count' => 0
        ];

        for ($month = 1; $month <= 12; $month++) {
            $customHolidays = CustomHoliday::whereYear('date', $year)
                ->whereMonth('date', $month)
                ->pluck('date')
                ->map(function ($date) {
                    return Carbon::parse($date)->format('Y-m-d');
                })
                ->toArray();

            $workingDays = $this->calculateWorkingDays($year, $month, $customHolidays);
            $yearlyData['working_days'] += $workingDays;

            $absences = EmployeeAbsent::where('user_id', $employee->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();

            $startDate = Carbon::create($year, $month, 1)->format('Y-m-d');
            $endDate = Carbon::create($year, $month)->endOfMonth()->format('Y-m-d');

            $timeOffRequests = RequestTimeOff::where('user_id', $employee->id)
                ->where('status', 'Approved')
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($innerQ) use ($startDate, $endDate) {
                            $innerQ->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                })
                ->with('timeOffPolicy')
                ->get();

            $warningLetters = WarningLetter::where('user_id', $employee->id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->get();

            // Sum all monthly values
            $monthPresence = $absences->count();
            $yearlyData['presence'] += $monthPresence;
            $monthLateArrivals = $this->countLateArrivals($absences);
            $yearlyData['late_arrivals'] += $monthLateArrivals;
            $monthEarlyDepartures = $this->countEarlyDepartures($absences);
            $yearlyData['early_departures'] += $monthEarlyDepartures;
            $monthSickLeave = $this->countTimeOffByType($timeOffRequests, 'sick');
            $yearlyData['sick_leave'] += $monthSickLeave;
            $monthPermission = $this->countTimeOffByType($timeOffRequests, 'permission');
            $yearlyData['permission'] += $monthPermission;
            $monthAfternoonShift = $this->countAfternoonShifts($timeOffRequests);
            $yearlyData['afternoon_shift_count'] += $monthAfternoonShift;
            $monthStCount = $this->countWarningLetters($warningLetters, 'ST');
            $yearlyData['st_count'] += $monthStCount;
            $monthSpCount = $this->countWarningLetters($warningLetters, 'SP');
            $yearlyData['sp_count'] += $monthSpCount;

            // Calculate monthly attendance percentage
            $monthAttendancePercentage = $workingDays > 0
                ? round(($monthPresence / $workingDays) * 100)
                : 0;

            // Calculate monthly scores
            $monthAttendanceScore = $monthPresence === 0 ? 0 : $this->calculateAttendanceScore($monthAttendancePercentage, $attendanceRules, $monthPresence);
            $monthLateScore = $this->calculateLateScore($monthLateArrivals, $lateRules);
            $monthAfternoonShiftScore = $this->calculateOccurrenceScore($monthAfternoonShift, $afternoonShiftRule);
            $monthEarlyDepartureScore = $this->calculateOccurrenceScore($monthEarlyDepartures, $earlyLeaveRule);
            $monthStScore = $this->calculateOccurrenceScore($monthStCount, $stRule);
            $monthSpScore = $this->calculateOccurrenceScore($monthSpCount, $spRule);

            // Sum up all monthly scores
            $totalAttendanceScore += $monthAttendanceScore;
            $totalLateScore += $monthLateScore;
            $totalAfternoonShiftScore += $monthAfternoonShiftScore;
            $totalEarlyDepartureScore += $monthEarlyDepartureScore;
            $totalStScore += $monthStScore;
            $totalSpScore += $monthSpScore;
        }

        // Calculate total score from summed monthly scores
        $totalScore = $totalAttendanceScore - $totalLateScore - $totalAfternoonShiftScore - $totalEarlyDepartureScore - $totalStScore - $totalSpScore;

        $finalData = [
            'total_score' => $yearlyData['presence'] > 0 ? $totalScore : ''
        ];

        return $finalData;
    }

    /**
     * Get e-learning data for employees
     */
    private function getElearningData($employeeIds, $year)
    {
        set_time_limit(900);
        $data = [];

        // Get grade rules
        $gradeRules = RuleElearningGrade::orderBy('min_score', 'desc')->get();

        foreach ($employeeIds as $employeeId) {
            $yearScores = $this->calculateElearningYearScores($employeeId, $year);

            if ($yearScores && isset($yearScores['final_percentage'])) {
                $data[$employeeId] = [
                    'score' => round($yearScores['final_percentage'], 2),
                    'grade' => $yearScores['final_grade'] ?? 'E',
                    'description' => $yearScores['grade_description'] ?? 'Poor'
                ];
            }
        }

        return $data; // Removed dd() to return the data instead of dumping it
    }

    /**
     * Calculate e-learning year scores for an employee
     */
    private function calculateElearningYearScores($userId, $year)
    {
        set_time_limit(900);
        // This implementation follows the logic in your original calculateYearScores method
        $totalPercentage = 0;
        $totalLessons = 0;

        for ($month = 1; $month <= 12; $month++) {
            $monthData = $this->getMonthData($userId, $year, $month);

            foreach ($monthData as $invitation) {
                if (is_numeric($invitation->score_percentage)) {
                    $totalPercentage += $invitation->score_percentage;
                    $totalLessons++;
                }
            }
        }

        // Calculate final percentage
        $finalPercentage = $totalLessons > 0 ? round($totalPercentage / $totalLessons, 2) : 0;

        // Floor the percentage for grade determination
        $finalPercentageFloor = floor($finalPercentage);

        // Get grade based on final percentage
        $grade = RuleElearningGrade::where('min_score', '<=', $finalPercentageFloor)
            ->where(function ($query) use ($finalPercentageFloor) {
                $query->where('max_score', '>=', $finalPercentageFloor)
                    ->orWhereNull('max_score');
            })
            ->orderBy('min_score', 'desc')
            ->first();

        return [
            'final_percentage' => $finalPercentage,
            'final_grade' => $grade ? $grade->grade : 'E',
            'grade_description' => $grade ? $grade->description : 'Poor'
        ];
    }


    public function finalCalculateExportToExcel(Request $request)
    {
        set_time_limit(900);
        // Get parameters from the request
        $selectedYear = $request->input('year', date('Y'));
        $employeeFilter = $request->input('employee');
        $positionFilter = $request->input('position');
        $departmentFilter = $request->input('department');

        // Reuse the query logic from report_final_index to get the same data
        $usersWithData = DB::table('users')
            ->select('users.id')
            ->distinct()
            ->where(function ($query) use ($selectedYear) {
                // Has performance evaluation
                $query->whereExists(function ($q) use ($selectedYear) {
                    $q->select(DB::raw(1))
                        ->from('employee_evaluation_performance')
                        ->whereRaw('employee_evaluation_performance.user_id = users.id')
                        ->whereYear('employee_evaluation_performance.date', $selectedYear);
                })
                    // Or has discipline record (absence)
                    ->orWhereExists(function ($q) use ($selectedYear) {
                        $q->select(DB::raw(1))
                            ->from('employee_absent')
                            ->whereRaw('employee_absent.user_id = users.id')
                            ->whereYear('employee_absent.date', $selectedYear);
                    })
                    // Or has e-learning record
                    ->orWhereExists(function ($q) use ($selectedYear) {
                        $q->select(DB::raw(1))
                            ->from('elearning_invitation')
                            ->whereRaw('elearning_invitation.users_id = users.id')
                            ->whereYear('elearning_invitation.created_at', $selectedYear);
                    });
            })
            ->when($employeeFilter, function ($query) use ($employeeFilter) {
                return $query->where('users.id', $employeeFilter);
            })
            ->when($positionFilter, function ($query) use ($positionFilter) {
                return $query->where('users.position_id', $positionFilter);
            })
            ->when($departmentFilter, function ($query) use ($departmentFilter) {
                return $query->where('users.department_id', $departmentFilter);
            })
            ->pluck('id')
            ->toArray();

        // Get users and their basic info
        $users = User::whereIn('id', $usersWithData)->get();

        // Get performance, discipline, e-learning data
        $performanceData = $this->getPerformanceData($usersWithData, $selectedYear);
        $disciplineData = $this->getDisciplineData($usersWithData, $selectedYear);
        $elearningData = $this->getElearningData($usersWithData, $selectedYear);

        // Get positions and departments
        $positionsList = EmployeePosition::whereIn('id', User::whereIn('id', $usersWithData)
            ->whereNotNull('position_id')
            ->pluck('position_id')
            ->unique()
            ->toArray())
            ->select('id', 'position')
            ->get();

        $departmentsList = EmployeeDepartment::whereIn('id', User::whereIn('id', $usersWithData)
            ->whereNotNull('department_id')
            ->pluck('department_id')
            ->unique()
            ->toArray())
            ->select('id', 'department')
            ->get();

        // Prepare data for Excel
        $exportData = [];
        $counter = 1;

        // Calculate weights (default values if not provided)
        $performanceWeight = $request->input('performance_weight', 60) / 100;
        $disciplineWeight = $request->input('discipline_weight', 30) / 100;
        $elearningWeight = $request->input('elearning_weight', 10) / 100;

        // Grade value mapping
        $gradeValues = [
            'A' => 5.0,
            'A-' => 4.75,
            'B+' => 4.3,
            'B' => 4.0,
            'B-' => 3.75,
            'C+' => 3.3,
            'C' => 3.0,
            'C-' => 2.75,
            'D+' => 2.3,
            'D' => 2.0,
            'D-' => 1.75,
            'E+' => 1.3,
            'E' => 1.0,
            'F' => 0.5
        ];

        foreach ($users as $user) {
            $performanceGrade = $performanceData[$user->id]['grade'] ?? 'F';
            $disciplineGrade = $disciplineData[$user->id]['grade'] ?? 'F';
            $elearningGrade = $elearningData[$user->id]['grade'] ?? 'F';

            $performanceGradeValue = $gradeValues[$performanceGrade] ?? 0;
            $disciplineGradeValue = $gradeValues[$disciplineGrade] ?? 0;
            $elearningGradeValue = $gradeValues[$elearningGrade] ?? 0;

            // Calculate weighted scores
            $weightedPerformance = $performanceGradeValue * $performanceWeight;
            $weightedDiscipline = $disciplineGradeValue * $disciplineWeight;
            $weightedElearning = $elearningGradeValue * $elearningWeight;

            // Calculate final score
            $finalScore = $weightedPerformance + $weightedDiscipline + $weightedElearning;
            $finalGrade = $this->getGradeFromScore($finalScore);

            $exportData[] = [
                '#' => $counter++,
                'Employee ID' => $user->employee_id ?? '',
                'Name' => $user->name,
                'Department' => $user->department_id ?
                    ($departmentsList->firstWhere('id', $user->department_id)->department ?? '') : '',
                'Position' => $user->position_id ?
                    ($positionsList->firstWhere('id', $user->position_id)->position ?? '') : '',
                'Performance' => $performanceGrade,
                'Performance Score' => number_format($weightedPerformance, 2),
                'Discipline' => $disciplineGrade,
                'Discipline Score' => number_format($weightedDiscipline, 2),
                'E-Learning' => $elearningGrade,
                'E-Learning Score' => number_format($weightedElearning, 2),
                'Final Score' => number_format($finalScore, 2),
                'Final Grade' => $finalGrade
            ];
        }

        // Create Excel file
        $fileName = 'Final_Evaluation_Report_' . $selectedYear . '_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new class($exportData) implements FromArray, WithHeadings, WithStyles, ShouldAutoSize {
            private $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }

            public function headings(): array
            {
                // Return the first row's keys as headings if data exists
                return !empty($this->data) ? array_keys($this->data[0]) : [];
            }

            public function styles(Worksheet $sheet)
            {
                // Style for the header row
                $sheet->getStyle('A1:M1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => [
                            'rgb' => 'FFFFFF'
                        ]
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => '4472C4'
                        ]
                    ]
                ]);

                // Apply borders to all cells
                $lastRow = count($this->data) + 1;
                $sheet->getStyle('A1:M' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ]
                ]);

                // Center align specific columns
                $sheet->getStyle('A:A')->getAlignment()->setHorizontal('center'); // #
                $sheet->getStyle('F:F')->getAlignment()->setHorizontal('center'); // Performance
                $sheet->getStyle('G:G')->getAlignment()->setHorizontal('center'); // Performance Score
                $sheet->getStyle('H:H')->getAlignment()->setHorizontal('center'); // Discipline
                $sheet->getStyle('I:I')->getAlignment()->setHorizontal('center'); // Discipline Score
                $sheet->getStyle('J:J')->getAlignment()->setHorizontal('center'); // E-Learning
                $sheet->getStyle('K:K')->getAlignment()->setHorizontal('center'); // E-Learning Score
                $sheet->getStyle('L:L')->getAlignment()->setHorizontal('center'); // Final Score
                $sheet->getStyle('M:M')->getAlignment()->setHorizontal('center'); // Final Grade
            }
        }, $fileName);
    }

    /**
     * Helper function to get grade from score
     */
    private function getGradeFromScore($score)
    {

        if ($score >= 5.0) return 'A';
        if ($score >= 4.6) return 'A-';
        if ($score >= 4.1) return 'B+';
        if ($score >= 4.0) return 'B';
        if ($score >= 3.6) return 'B-';
        if ($score >= 3.1) return 'C+';
        if ($score >= 3.0) return 'C';
        if ($score >= 2.6) return 'C-';
        if ($score >= 2.1) return 'D+';
        if ($score >= 2.0) return 'D';
        if ($score >= 1.6) return 'D-';
        if ($score >= 1.1) return 'E+';
        if ($score >= 1.0) return 'E';
        return 'F';
    }


    /**
     * Save final evaluation results to the database
     */
    public function saveFinalCalculateResults(Request $request)
    {
        // dd($request->all());
        // Validate the request
        $request->validate([
            'evaluations' => 'required|array',
            'evaluations.*.user_id' => 'required|exists:users,id',
            'evaluations.*.year' => 'required|numeric',
            'evaluations.*.performance' => 'nullable|string|max:5',
            'evaluations.*.performance_score' => 'nullable|numeric',
            'evaluations.*.discipline' => 'nullable|string|max:5',
            'evaluations.*.discipline_score' => 'nullable|numeric',
            'evaluations.*.elearning' => 'nullable|string|max:5',
            'evaluations.*.elearning_score' => 'nullable|numeric',
            'evaluations.*.final_score' => 'nullable|numeric',
            'evaluations.*.final_grade' => 'nullable|string|max:5',
        ]);

        $savedCount = 0;
        $year = null;

        // Begin transaction
        DB::beginTransaction();

        try {
            foreach ($request->evaluations as $evaluation) {
                $year = $evaluation['year'];

                // Check if record already exists for this user and year
                $existingRecord = EmployeeFinalEvaluation::where('user_id', $evaluation['user_id'])
                    ->where('year', $evaluation['year'])
                    ->first();

                if ($existingRecord) {
                    // Update existing record
                    $existingRecord->update([
                        'performance' => $evaluation['performance'],
                        'performance_score' => $evaluation['performance_score'],
                        'discipline' => $evaluation['discipline'],
                        'discipline_score' => $evaluation['discipline_score'],
                        'elearning' => $evaluation['elearning'],
                        'elearning_score' => $evaluation['elearning_score'],
                        'final_score' => $evaluation['final_score'],
                        'final_grade' => $evaluation['final_grade'],
                        'updated_at' => now()
                    ]);
                } else {
                    // Insert new record
                    EmployeeFinalEvaluation::create([
                        'user_id' => $evaluation['user_id'],
                        'year' => $evaluation['year'],
                        'performance' => $evaluation['performance'],
                        'performance_score' => $evaluation['performance_score'],
                        'discipline' => $evaluation['discipline'],
                        'discipline_score' => $evaluation['discipline_score'],
                        'elearning' => $evaluation['elearning'],
                        'elearning_score' => $evaluation['elearning_score'],
                        'final_score' => $evaluation['final_score'],
                        'final_grade' => $evaluation['final_grade'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                $savedCount++;
            }

            // Commit transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Successfully saved ' . $savedCount . ' evaluation results.',
            ]);
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            // Log error
            Log::error('Error saving evaluation results: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to save evaluation results: ' . $e->getMessage()
            ], 500);
        }
    }













    public function rule_grade_salary_index()
    {
        $gradeSalaries = RuleEvaluationGradeSalaryFinal::all();

        return view('evaluation/rule/final/grade/salary/index', compact('gradeSalaries'));
    }

    /**
     * Show the form for creating a new grade salary rule
     */
    public function rule_grade_salary_create()
    {
        return view('evaluation/rule/final/grade/salary/create');
    }

    /**
     * Store a newly created grade salary rule
     */
    public function rule_grade_salary_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'grade' => 'required|string|max:3|unique:rule_evaluation_grade_salary_final,grade',
            'value_salary' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        RuleEvaluationGradeSalaryFinal::create([
            'grade' => $request->grade,
            'value_salary' => $request->value_salary,
        ]);

        return redirect()->route('evaluation.rule.grade.salary.index')
            ->with('success', 'Grade salary rule successfully created!');
    }

    /**
     * Show the form for editing the specified grade salary rule
     */
    public function rule_grade_salary_edit($id)
    {
        $gradeSalary = RuleEvaluationGradeSalaryFinal::findOrFail($id);

        return view('evaluation/rule/final/grade/salary/update', compact('gradeSalary'));
    }

    /**
     * Update the specified grade salary rule
     */
    public function rule_grade_salary_update(Request $request, $id)
    {
        $gradeSalary = RuleEvaluationGradeSalaryFinal::findOrFail($id);

        $rules = [
            'grade' => 'required|string|max:3',
            'value_salary' => 'required|numeric|min:0',
        ];

        // Only validate uniqueness if the grade has changed
        if ($request->grade != $gradeSalary->grade) {
            $rules['grade'] .= '|unique:rule_evaluation_grade_salary_final,grade';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $gradeSalary->update([
            'grade' => $request->grade,
            'value_salary' => $request->value_salary,
        ]);

        return redirect()->route('evaluation.rule.grade.salary.index')
            ->with('success', 'Grade salary rule successfully updated!');
    }

    /**
     * Remove the specified grade salary rule
     */
    public function rule_grade_salary_destroy($id)
    {
        $gradeSalary = RuleEvaluationGradeSalaryFinal::findOrFail($id);
        $gradeSalary->delete();

        return redirect()->route('evaluation.rule.grade.salary.index')
            ->with('success', 'Grade salary rule successfully deleted!');
    }

    /**
     * Check if a grade already exists (AJAX endpoint)
     */
    public function rule_grade_salary_check(Request $request)
    {
        $grade = $request->grade;
        $currentId = $request->current_id ?? null;

        $query = RuleEvaluationGradeSalaryFinal::where('grade', $grade);

        // Exclude the current record if editing
        if ($currentId) {
            $query->where('id', '!=', $currentId);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }


    /**
     * Show the evaluation final report graph index page
     */
    public function reportFinalGraphIndex()
    {
        return view('evaluation.report.final.graph.index');
    }

    /**
     * Get evaluation data for graphs
     */
    public function reportFinalGraphData(Request $request)
    {
        $years = $request->input('years', [date('Y')]);
        $employeeIds = $request->input('employees', []);

        $evaluations = EmployeeFinalEvaluation::select(
            'employee_final_evaluations.*',
            'users.name as employee_name'
        )
            ->leftJoin('users', 'users.id', '=', 'employee_final_evaluations.user_id')
            ->whereIn('year', $years)
            ->whereIn('user_id', $employeeIds)
            ->get();

        return response()->json($evaluations);
    }

    /**
     * API endpoint to get employees for select2
     */
    public function getEmployees(Request $request)
    {
        $search = $request->input('search', '');

        // Remove pagination limit
        $query = User::with(['position', 'department'])->select('id', 'name', 'position_id', 'department_id');

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Get all matching employees with no limit
        $employees = $query->get();

        return response()->json($employees);
    }

    /**
     * API endpoint to get available years for evaluation reports
     * This dynamically retrieves all years from the database
     */
    public function getAvailableYears()
    {
        $years = EmployeeFinalEvaluation::select(DB::raw('DISTINCT year'))
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // If no years are found, return current year
        if (empty($years)) {
            $years = [date('Y')];
        }

        return response()->json($years);
    }














    public function reportFinalResultIndex(Request $request)
    {
        // Get current user ID
        $id = Auth::id();


        // Build the base query - exclude current user
        $query = EmployeeFinalEvaluation::with(['user'])->where('user_id', '!=', $id);

        // Get distinct years for dropdown
        $years = EmployeeFinalEvaluation::distinct()->orderBy('year', 'desc')->pluck('year');

        // Get distinct names for dropdown
        $users = EmployeeFinalEvaluation::with('user')
            ->select('user_id')
            ->distinct()
            ->get()
            ->filter(function ($item) {
                return $item->user !== null; // pastikan user-nya ada
            })
            ->map(function ($item) {
                return [
                    'id' => $item->user_id,
                    'name' => $item->user->name,
                ];
            })
            ->sortBy('name')
            ->values(); // reset index biar rapih

        // Get distinct departments and positions
        $departments = EmployeeDepartment::orderBy('department')->get(['id', 'department']);
        $positions = EmployeePosition::orderBy('position')->get(['id', 'position']);

        // Apply filters
        if ($request->has('year') && !empty($request->year)) {
            $query->where('year', $request->year);
        }

        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('final_grade') && !empty($request->final_grade)) {
            $query->where('final_grade', $request->final_grade);
        }

        if ($request->has('performance') && !empty($request->performance)) {
            $query->where('performance', $request->performance);
        }

        if ($request->has('discipline') && !empty($request->discipline)) {
            $query->where('discipline', $request->discipline);
        }

        if ($request->has('elearning') && !empty($request->elearning)) {
            $query->where('elearning', $request->elearning);
        }

        if ($request->has('proposal_grade') && !empty($request->proposal_grade)) {
            $query->where('proposal_grade', $request->proposal_grade);
        }

        // Change from paginate(15) to get() to show all data
        $evaluations = $query->get();

        // Process each evaluation to add department, position, and salary data
        foreach ($evaluations as $evaluation) {
            if ($evaluation->user) {
                // Find the transfer history record closest to but before the evaluation date
                // Assuming there's a date field in EmployeeFinalEvaluation - if not, use created_at or a similar date field
                $evalDate = $evaluation->created_at ?? now();

                $history = history_transfer_employee::where('users_id', $evaluation->user_id)
                    ->where('created_at', '<', $evalDate)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($history) {
                    // Use historical position and department
                    $evaluation->department_id = $history->new_department_id;
                    $evaluation->position_id = $history->new_position_id;
                    $evaluation->department = EmployeeDepartment::find($history->new_department_id);
                    $evaluation->position = EmployeePosition::find($history->new_position_id);
                } else {
                    // No history found, use current position and department
                    $evaluation->department_id = $evaluation->user->department_id;
                    $evaluation->position_id = $evaluation->user->position_id;
                    $evaluation->department = $evaluation->user->department;
                    $evaluation->position = $evaluation->user->position;
                }

                // Get warning letter information for this employee within the evaluation year
                $yearStart = $evaluation->year . '-01-01';
                $yearEnd = $evaluation->year . '-12-31';

                // Get all warning letters for this user in the evaluation year
                $warningLetters = WarningLetter::with('rule')
                    ->where('user_id', $evaluation->user_id)
                    ->whereBetween('created_at', [$yearStart, $yearEnd])
                    ->get();

                // Group warning letters by type
                $warningTypes = [];
                foreach ($warningLetters as $letter) {
                    if ($letter->rule) {
                        // Get only the prefix (ST or SP) without the number
                        $type = preg_replace('/[0-9]+/', '', $letter->rule->name);
                        $warningTypes[$type] = true;
                    }
                }

                // Store warning letter types as a comma-separated string
                $evaluation->warning_letters = !empty($warningTypes) ? implode(', ', array_keys($warningTypes)) : '-';

                // Get current salary data
                $salary = EmployeeSalary::where('users_id', $evaluation->user_id)->first();
                $evaluation->current_salary = $salary ? $salary->basic_salary : 0;

                // Calculate projected new salary if increases are applied
                $evaluation->projected_salary = $evaluation->current_salary + ($evaluation->salary_increases ?? 0);
            } else {
                // Handle case where user might be null
                $evaluation->department_id = null;
                $evaluation->position_id = null;
                $evaluation->department = null;
                $evaluation->position = null;
                $evaluation->warning_letters = '-';
                $evaluation->current_salary = 0;
                $evaluation->projected_salary = 0;
            }
        }

        // Now filter by department and position using the processed data
        if ($request->has('department_id') && !empty($request->department_id)) {
            $departmentId = $request->department_id;
            $evaluations = $evaluations->filter(function ($evaluation) use ($departmentId) {
                return $evaluation->department_id == $departmentId;
            });
        }

        if ($request->has('position_id') && !empty($request->position_id)) {
            $positionId = $request->position_id;
            $evaluations = $evaluations->filter(function ($evaluation) use ($positionId) {
                return $evaluation->position_id == $positionId;
            });
        }

        // Get all possible grades for dropdowns
        $grades = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'D-', 'E+', 'E', 'F'];

        return view('evaluation.report.final.result.index', compact(
            'evaluations',
            'years',
            'users',
            'departments',
            'positions',
            'grades'
        ));
    }

    public function reportFinalResultIndex2(Request $request)
    {
        // Get current user ID
        $id = Auth::id();

        // Build the base query - only for current user
        $query = EmployeeFinalEvaluation::with(['user'])->where('user_id', $id);

        // Get all evaluations for the current user
        $evaluations = $query->get();
        // Get performance history for charts
        $performanceHistory = EmployeeFinalEvaluation::where('user_id', $id)
            ->orderBy('year', 'asc')
            ->get(['year', 'performance_score', 'discipline_score', 'elearning_score', 'final_score']);


        return view('evaluation.report.final.result.index2', compact(
            'evaluations',
            'performanceHistory',
        ));
    }

    public function reportFinalResultUpdate(Request $request, $id)
    {
        $request->validate([
            'proposal_grade' => 'nullable|string',
            'salary_increases' => 'nullable|numeric|min:0',
        ]);

        $evaluation = EmployeeFinalEvaluation::findOrFail($id);
        $hasChanges = false;
        $salaryUpdated = false;
        $newSalary = 0;

        $data = [];

        // Check if proposal grade changed
        if ($request->has('proposal_grade') && $evaluation->proposal_grade !== $request->proposal_grade) {
            $data['proposal_grade'] = $request->proposal_grade;
            $hasChanges = true;
        }


        // Check if salary increases changed
        if ($request->has('salary_increases') && $evaluation->salary_increases != $request->salary_increases) {
            $data['salary_increases'] = $request->salary_increases;
            $hasChanges = true;
        }

        // Auto-calculate salary increase if proposal grade is selected
        if ($request->has('proposal_grade') && !empty($request->proposal_grade) && empty($request->salary_increases)) {
            $grade = $request->proposal_grade;
            $salaryRule = RuleEvaluationGradeSalaryFinal::where('grade', $grade)->first();

            if ($salaryRule) {
                $data['salary_increases'] = $salaryRule->value_salary;
                $hasChanges = true;
            }
        } elseif (empty($evaluation->salary_increases) && !empty($evaluation->final_grade) && empty($request->salary_increases)) {
            // Use final_grade if proposal_grade is not set
            $grade = $evaluation->final_grade;
            $salaryRule = RuleEvaluationGradeSalaryFinal::where('grade', $grade)->first();

            if ($salaryRule) {
                $data['salary_increases'] = $salaryRule->value_salary;
                $hasChanges = true;
            }
        }

        $message = 'No changes detected';

        if ($hasChanges) {

            $evaluation->update($data);

            // Check if salary should be updated
            if (isset($data['salary_increases']) && $data['salary_increases'] > 0) {
                // Update employee's actual salary if there's an increase and it's confirmed
                $salary = EmployeeSalary::where('users_id', $evaluation->user_id)->first();

                if ($salary) {
                    DB::transaction(function () use ($salary, $data, &$salaryUpdated, &$newSalary) {
                        // Store salary history
                        SalaryHistory::create([
                            'users_id' => $salary->users_id,
                            'old_basic_salary' => $salary->basic_salary,
                            'old_overtime_rate_per_hour' => $salary->overtime_rate_per_hour,
                            'new_basic_salary' => $salary->basic_salary + $data['salary_increases'],
                            'new_overtime_rate_per_hour' => $salary->overtime_rate_per_hour
                        ]);

                        // Update salary
                        $newSalary = $salary->basic_salary + $data['salary_increases'];
                        $salary->basic_salary = $newSalary;
                        $salary->save();
                        $salaryUpdated = true;
                    });

                    $message = 'Changes saved successfully and salary updated';
                } else {
                    $message = 'Changes saved successfully but employee salary record not found';
                }
            } else {
                $message = 'Changes saved successfully';
            }
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'salary_updated' => $salaryUpdated,
            'new_salary' => $newSalary
        ]);
    }

    public function reportFinalResultSaveAll(Request $request)
    {
        $request->validate([
            'data' => 'required|array',
            'data.*.id' => 'required|integer|exists:employee_final_evaluations,id',
            'data.*.proposal_grade' => 'nullable|string',
            'data.*.salary_increases' => 'nullable|numeric|min:0',
            'confirm_salary_update' => 'nullable|boolean'
        ]);

        $updatedCount = 0;
        $salaryUpdatedCount = 0;

        DB::transaction(function () use ($request, &$updatedCount, &$salaryUpdatedCount) {
            foreach ($request->data as $item) {
                $evaluation = EmployeeFinalEvaluation::findOrFail($item['id']);
                $data = [];
                $hasChanges = false;

                if (isset($item['proposal_grade']) && $evaluation->proposal_grade !== $item['proposal_grade']) {
                    $data['proposal_grade'] = $item['proposal_grade'];
                    $hasChanges = true;
                }

                if (isset($item['salary_increases']) && $evaluation->salary_increases != $item['salary_increases']) {
                    $data['salary_increases'] = $item['salary_increases'];
                    $hasChanges = true;
                }

                if ($hasChanges) {
                    $evaluation->update($data);
                    $updatedCount++;


                    if (isset($data['salary_increases']) && $data['salary_increases'] > 0) {
                        $salary = EmployeeSalary::where('users_id', $evaluation->user_id)->first();

                        if ($salary) {
                            // Store salary history
                            SalaryHistory::create([
                                'users_id' => $salary->users_id,
                                'old_basic_salary' => $salary->basic_salary,
                                'old_overtime_rate_per_hour' => $salary->overtime_rate_per_hour,
                                'new_basic_salary' => $salary->basic_salary + $data['salary_increases'],
                                'new_overtime_rate_per_hour' => $salary->overtime_rate_per_hour
                            ]);

                            // Update salary
                            $salary->basic_salary = $salary->basic_salary + $data['salary_increases'];
                            $salary->save();
                            $salaryUpdatedCount++;
                        }
                    }
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => $updatedCount . ' record(s) updated successfully, ' . $salaryUpdatedCount . ' salary(s) updated'
        ]);
    }


    public function reportFinalResultGetSalaryValue(Request $request)
    {
        $request->validate([
            'grade' => 'required|string',
        ]);

        $salaryRule = RuleEvaluationGradeSalaryFinal::where('grade', $request->grade)->first();

        if ($salaryRule) {
            return response()->json(['value_salary' => $salaryRule->value_salary]);
        }

        return response()->json(['value_salary' => null]);
    }


    public function reportFinalResultUploadProposal(Request $request, $id)
    {
        $request->validate([
            'file_proposal' => 'required|file|mimes:pdf|max:10240',
        ]);

        $evaluation = EmployeeFinalEvaluation::with('user')->findOrFail($id);

        // Delete old file if exists
        if ($evaluation->file_proposal) {
            Storage::disk('public')->delete($evaluation->file_proposal);
        }

        // Create directory if it doesn't exist
        $directory = 'evaluation/report/final/result';
        Storage::disk('public')->makeDirectory($directory);

        // Create filename based on requirements
        $filename = 'proposal_salary_' . $evaluation->user->name . '_' . $evaluation->user_id . '_' . $evaluation->year . '.pdf';

        // Store new file
        $path = $request->file('file_proposal')->storeAs($directory, $filename, 'public');
        $evaluation->update(['file_proposal' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully',
            'file_url' => asset('storage/' . $path)
        ]);
    }
}
