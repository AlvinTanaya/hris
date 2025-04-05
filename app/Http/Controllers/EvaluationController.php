<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Color;

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
        // Get current month and year for default filtering
        $currentMonth = $request->input('month', date('n'));
        $currentYear = $request->input('year', date('Y'));

        // Get available years from evaluations
        $availableYears = EvaluationPerformance::where('evaluator_id', $id)
            ->selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [$currentYear];
        }

        // Query evaluasi utama
        $query = EvaluationPerformance::with([
            'user.position',
            'user.department',
            'details.weightPerformance.criteria'
        ])
            ->where('evaluator_id', $id)
            ->orderBy('date', 'desc');

        // Apply filters
        if ($request->filled('month')) {
            $query->whereMonth('date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }

        if ($request->filled('employee')) {
            $query->where('user_id', $request->employee);
        }

        $evaluations = $query->get();

        // Post-query filtering for position and department
        if ($request->filled('position')) {
            $evaluations = $evaluations->filter(function ($eval) use ($request) {
                return $eval->user && $eval->user->position_id == $request->position;
            });
        }

        if ($request->filled('department')) {
            $evaluations = $evaluations->filter(function ($eval) use ($request) {
                return $eval->user && $eval->user->department_id == $request->department;
            });
        }

        // Get filter dropdown options
        $employeesList = User::whereIn(
            'id',
            EvaluationPerformance::where('evaluator_id', $id)
                ->distinct('user_id')
                ->pluck('user_id')
        )->orderBy('name')->get();

        $positionsList = EmployeePosition::orderBy('position')->get();
        $departmentsList = EmployeeDepartment::orderBy('department')->get();

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
    public function assign_performance_create($id)
    {

        // Get the current user's position
        $user = User::findOrFail($id);
        $userPosition = $user->position_id;

        // Find subordinate positions (positions with rank below the current user's position)
        $currentPosition = EmployeePosition::findOrFail($userPosition);
        $currentRank = $currentPosition->ranking;

        // Get positions with rank one level below the current user's rank
        $subordinatePositions = EmployeePosition::where('ranking', '>', $currentRank)->pluck('id')->toArray();

        // Get users with those positions
        $subordinates = User::whereIn('position_id', $subordinatePositions)
            ->with('position')
            ->get();

        return view('evaluation.assign.performance.create', compact('subordinates'));
    }





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
        // Get current month and year for default filtering
        $currentMonth = date('n');
        $currentYear = date('Y');

        // Get filter parameters
        $employeeFilter = $request->input('employee');
        $positionFilter = $request->input('position');
        $departmentFilter = $request->input('department');
        $yearFilter = $request->input('year', $currentYear);

        // Remove this line as we don't need it anymore
        // $showGrades = $request->has('show_grades') ? (bool)$request->input('show_grades') : false;

        // Get available years from evaluations (unique years)
        $availableYears = EvaluationPerformance::select(DB::raw('DISTINCT YEAR(date) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // If no years found, use current year
        if (empty($availableYears)) {
            $availableYears = [$currentYear];
        }

        // Build base query for evaluations
        $evaluationsQuery = EvaluationPerformance::with(['user.position', 'user.department', 'evaluator']);

        // Apply year filter if it exists
        if ($yearFilter) {
            $evaluationsQuery->whereYear('date', $yearFilter);
        }

        // Apply employee filter if set
        if ($employeeFilter) {
            $evaluationsQuery->where('user_id', $employeeFilter);
        }

        // Apply position filter through join if set
        if ($positionFilter) {
            $evaluationsQuery->whereHas('user', function ($query) use ($positionFilter) {
                $query->where('position_id', $positionFilter);
            });
        }

        // Apply department filter through join if set
        if ($departmentFilter) {
            $evaluationsQuery->whereHas('user', function ($query) use ($departmentFilter) {
                $query->where('department_id', $departmentFilter);
            });
        }

        // Get evaluations ordered by date
        $evaluations = $evaluationsQuery->orderBy('date', 'desc')->get();

        // Get performance grade rules
        $gradeRules = RulePerformanceGrade::orderBy('min_score', 'desc')->get();

        // Calculate grades for each evaluation
        foreach ($evaluations as $evaluation) {
            $finalScore = $evaluation->total_score - $evaluation->total_reduction;

            // Find applicable grade
            $grade = $gradeRules->first(function ($rule) use ($finalScore) {
                // Handle NULL in max_score (meaning there's no upper limit)
                $belowMaxOrNoMax = ($rule->max_score === null || $finalScore <= $rule->max_score);

                // Handle NULL in min_score (meaning there's no lower limit)
                $aboveMinOrNoMin = ($rule->min_score === null || $finalScore >= $rule->min_score);

                return $aboveMinOrNoMin && $belowMaxOrNoMax;
            });

            $evaluation->grade = $grade ? $grade->grade : 'N/A';
            $evaluation->grade_description = $grade ? $grade->description : 'Not Available';
        }

        // Get lists for filters
        $employeesList = User::select('id', 'name')
            ->orderBy('name')
            ->get();

        $positionsList = EmployeePosition::select('id', 'position')
            ->orderBy('position')
            ->get();

        $departmentsList = EmployeeDepartment::select('id', 'department')
            ->orderBy('department')
            ->get();

        // Remove showGrades from the compact() function
        return view('evaluation.report.performance.index', compact(
            'evaluations',
            'employeesList',
            'positionsList',
            'departmentsList',
            'currentYear',
            'availableYears',
            'gradeRules'
        ));
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

                        $monthlyData[$monthIndex]['scores'][] = [
                            'name' => $criterion['name'],
                            'value' => $avgValue,
                            'score' => $avgScore
                        ];

                        $monthlyData[$monthIndex]['rawScore'] += $avgScore;
                    }
                }

                // Calculate deductions for this month (only count reductions that exist)
                $monthlyData[$monthIndex]['deductions'] = $monthEvaluations->sum(function ($eval) {
                    return $eval->reductions->sum('reduction_amount');
                });

                $monthlyData[$monthIndex]['finalScore'] = max(
                    0,
                    $monthlyData[$monthIndex]['rawScore'] - $monthlyData[$monthIndex]['deductions']
                );
            }
        }

        // Calculate overall averages
        $validRawScores = array_filter(array_column($monthlyData, 'rawScore'));
        $validFinalScores = array_filter(array_column($monthlyData, 'finalScore'));

        $overallRawAverage = $validRawScores ? array_sum($validRawScores) / count($validRawScores) : 0;
        $overallAverage = $validFinalScores ? array_sum($validFinalScores) / count($validFinalScores) : 0;

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

        // Process yearly reductions (only count those actually applied to evaluations)
        $reductionRules = RuleEvaluationReductionPerformance::where('Status', "Active")->get();
        $maxPossibleDeductions = $reductionRules->sum('weight');
        $yearlyReductions = [];
        $totalDeductions = 0;

        foreach ($reductionRules as $rule) {
            // Get warning letters that have actually been applied to evaluations
            $warningLetters = WarningLetter::where('user_id', $user->id)
                ->where('type_id', $rule->type_id)
                ->whereYear('created_at', $year)
                ->whereHas('evaluationReductions')
                ->get();

            $ruleData = [
                'id' => $rule->id,
                'name' => $rule->warningLetterRule->name ?? $rule->name,
                'weight' => $rule->weight,
                'monthly' => array_fill(1, 12, ['count' => 0, 'reduction' => 0]),
                'total_count' => 0,
                'total_reduction' => 0
            ];

            foreach ($warningLetters as $letter) {
                $monthNumber = $letter->date ? $letter->date->month : now()->month;

                // Sum only the actual reductions applied
                $reductionAmount = $letter->evaluationReductions->sum('reduction_amount');

                $ruleData['monthly'][$monthNumber]['count']++;
                $ruleData['monthly'][$monthNumber]['reduction'] += $reductionAmount;
                $ruleData['total_count']++;
                $ruleData['total_reduction'] += $reductionAmount;
            }

            $yearlyReductions[$rule->id] = $ruleData;
            $totalDeductions += $ruleData['total_reduction'];
        }

        // Calculate total possible score
        $totalPossible = $criteria->sum('weight') * 3; // Assuming max score per criterion is 3

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


    public function exportExcel()
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

                    // Calculate deductions for this month
                    $monthlyData[$monthIndex]['deductions'] = $monthEvaluations->sum(function ($eval) {
                        return $eval->reductions->sum('reduction_amount');
                    });

                    $monthlyData[$monthIndex]['finalScore'] = max(
                        0,
                        $monthlyData[$monthIndex]['rawScore'] - $monthlyData[$monthIndex]['deductions']
                    );
                }
            }

            // Calculate overall averages
            $validRawScores = array_filter(array_column($monthlyData, 'rawScore'));
            $validFinalScores = array_filter(array_column($monthlyData, 'finalScore'));

            $overallRawAverage = $validRawScores ? array_sum($validRawScores) / count($validRawScores) : 0;
            $overallAverage = $validFinalScores ? array_sum($validFinalScores) / count($validFinalScores) : 0;

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

            // Process yearly reductions
            $reductionRules = RuleEvaluationReductionPerformance::where('Status', "Active")->get();
            $maxPossibleDeductions = $reductionRules->sum('weight');
            $yearlyReductions = [];
            $totalDeductions = 0;
            foreach ($reductionRules as $rule) {
                // Get warning letters that have been applied to evaluations
                $warningLetters = WarningLetter::where('user_id', $user->id)
                    ->where('type_id', $rule->type_id)
                    ->whereYear('created_at', $year)
                    ->whereHas('evaluationReductions')
                    ->get();
                $ruleData = [
                    'id' => $rule->id,
                    'name' => $rule->warningLetterRule->name ?? $rule->name,
                    'weight' => $rule->weight,
                    'monthly' => array_fill(1, 12, ['count' => 0, 'reduction' => 0]),
                    'total_count' => 0,
                    'total_reduction' => 0
                ];
                foreach ($warningLetters as $letter) {
                    $monthNumber = $letter->date ? $letter->date->month : now()->month;
                    // Sum only the actual reductions applied
                    $reductionAmount = $letter->evaluationReductions->sum('reduction_amount');
                    $ruleData['monthly'][$monthNumber]['count']++;
                    $ruleData['monthly'][$monthNumber]['reduction'] += $reductionAmount;
                    $ruleData['total_count']++;
                    $ruleData['total_reduction'] += $reductionAmount;
                }
                $yearlyReductions[$rule->id] = $ruleData;
                $totalDeductions += $ruleData['total_reduction'];
            }

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
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $employeeId = $request->input('employee_id');
        $departmentId = $request->input('department_id');
        $positionId = $request->input('position_id');

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

        $employees = $query->get();

        if ($month === 'final') {
            return $this->getFinalYearlyData($employees, $year);
        }

        // Get discipline rules
        $attendanceRules = DisciplineRule::where('rule_type', 'attendance')->orderBy('min_value', 'desc')->get();
        $lateRules = DisciplineRule::where('rule_type', 'late')->orderBy('min_value', 'asc')->get();
        $afternoonShiftRule = DisciplineRule::where('rule_type', 'afternoon_shift')->first();
        $earlyLeaveRule = DisciplineRule::where('rule_type', 'early_leave')->first();
        $stRule = DisciplineRule::where('rule_type', 'st')->first();
        $spRule = DisciplineRule::where('rule_type', 'sp')->first();

        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
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
    private function getFinalYearlyData($employees, $year)
    {
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
                'sp_count' => 0
            ];

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
                $yearlyData['presence'] += $absences->count();
                $yearlyData['late_arrivals'] += $this->countLateArrivals($absences);
                $yearlyData['early_departures'] += $this->countEarlyDepartures($absences);
                $yearlyData['sick_leave'] += $this->countTimeOffByType($timeOffRequests, 'sick');
                $yearlyData['permission'] += $this->countTimeOffByType($timeOffRequests, 'permission');
                $yearlyData['afternoon_shift_count'] += $this->countAfternoonShifts($timeOffRequests);
                $yearlyData['st_count'] += $this->countWarningLetters($warningLetters, 'ST');
                $yearlyData['sp_count'] += $this->countWarningLetters($warningLetters, 'SP');
            }

            // Calculate attendance percentage
            $attendancePercentage = $yearlyData['working_days'] > 0
                ? round(($yearlyData['presence'] / $yearlyData['working_days']) * 100)
                : 0;

            // Calculate scores based on yearly totals
            $attendanceScore = $yearlyData['presence'] === 0 ? 0 : $this->calculateAttendanceScore($attendancePercentage, $attendanceRules, $yearlyData['presence']);
            $lateScore = $this->calculateLateScore($yearlyData['late_arrivals'], $lateRules);
            $afternoonShiftScore = $this->calculateOccurrenceScore($yearlyData['afternoon_shift_count'], $afternoonShiftRule);
            $earlyDepartureScore = $this->calculateOccurrenceScore($yearlyData['early_departures'], $earlyLeaveRule);
            $stScore = $this->calculateOccurrenceScore($yearlyData['st_count'], $stRule);
            $spScore = $this->calculateOccurrenceScore($yearlyData['sp_count'], $spRule);

            // Calculate total score
            $totalScore = $attendanceScore - $lateScore - $afternoonShiftScore - $earlyDepartureScore - $stScore - $spScore;

            // Calculate final grade based on database rules
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
                'attendance_score' => $yearlyData['presence'] > 0 ? $attendanceScore : '',
                'late_score' => $yearlyData['late_arrivals'] > 0 ? -$lateScore : '',
                'afternoon_shift_score' => $yearlyData['afternoon_shift_count'] > 0 ? -$afternoonShiftScore : '',
                'early_departure_score' => $yearlyData['early_departures'] > 0 ? -$earlyDepartureScore : '',
                'st_score' => $yearlyData['st_count'] > 0 ? -$stScore : '',
                'sp_score' => $yearlyData['sp_count'] > 0 ? -$spScore : '',
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

        // Get employees based on filters
        $employees = $this->getFilteredEmployees($employeeId, $departmentId, $positionId);

        // Use caching to improve performance
        $cacheKey = "discipline_report_{$year}_{$employeeId}_{$departmentId}_{$positionId}_{$exportType}";
        $expiresAt = now()->addMinutes(60);

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
            $finalData = $this->getFinalYearlyData($employees, $year)->original;
            $this->createFinalYearlySheet($spreadsheet, $finalData, $year);
            $fileName = 'Discipline_Report_Final_' . $year . '.xlsx';
        } else {
            // Export everything (all monthly + yearly sheets)
            $this->createMonthlySheets($spreadsheet, $request, $year);

            // Add Final sheet as the last sheet
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex(12);
            $finalData = $this->getFinalYearlyData($employees, $year)->original;
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
    private function createFinalYearlySheet($spreadsheet, $data, $year)
    {
        // Set active sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('FINAL');

        // Set company information
        $sheet->setCellValue('A1', 'LAPORAN KEDISIPLINAN TAHUNAN');
        $sheet->setCellValue('A2', 'Tahun: ' . $year);
        $sheet->mergeCells('A1:P1');
        $sheet->mergeCells('A2:P2');

        // Create "FINAL" header
        $sheet->setCellValue('A3', 'FINAL');
        $sheet->mergeCells('A3:P3');
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

        // Set headers based on the screenshot shared
        $headers = [
            'A4' => 'NIK',
            'B4' => 'NAMA',
            'C4' => 'TTL KERJA/BL',
            'D4' => 'KEHADIRAN',
            'E4' => '%',
            'F4' => 'TERLAMBAT',
            'G4' => 'MASUK SIANG',
            'H4' => 'PULANG AWAL',
            'I4' => 'SAKIT',
            'J4' => 'ST',
            'K4' => 'SP',
            'L4' => 'KEHADIRAN',
            'M4' => 'TERLAMBAT',
            'N4' => 'MASUK SIANG',
            'O4' => 'PULANG AWAL',
            'P4' => 'ST',
            'Q4' => 'SP',
            'R4' => 'TOTAL',
            'S4' => 'GRADE',
        ];

        // Add SCORE header above score columns
        $sheet->setCellValue('L3', 'SCORE');
        $sheet->mergeCells('L3:S3');
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
            $sheet->setCellValue('G' . $row, $item['afternoon_shift_count']);
            $sheet->setCellValue('H' . $row, $item['early_departures']);
            $sheet->setCellValue('I' . $row, $item['sick_leave']);
            $sheet->setCellValue('J' . $row, $item['st_count']);
            $sheet->setCellValue('K' . $row, $item['sp_count']);

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






    // Discipline Grade CRUD

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





    public function getPerformanceGrade($score)
    {
        return RulePerformanceGrade::where(function ($query) use ($score) {
            $query->where('min_score', '<=', $score)
                ->where(function ($q) use ($score) {
                    $q->where('max_score', '>=', $score)
                        ->orWhereNull('max_score');
                });
        })
            ->orderBy('min_score', 'desc')
            ->first();
    }

    public function getDisciplineGrade($score)
    {
        return RuleDisciplineGrade::where(function ($query) use ($score) {
            $query->where('min_score', '<=', $score)
                ->where(function ($q) use ($score) {
                    $q->where('max_score', '>=', $score)
                        ->orWhereNull('max_score');
                });
        })
            ->orderBy('min_score', 'desc')
            ->first();
    }
}
