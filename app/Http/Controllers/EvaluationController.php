<?php

namespace App\Http\Controllers;

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

use App\Models\RuleEvaluationPerformance;
use App\Models\EmployeeEvaluationPerformance;
use App\Models\Notification;
use App\Models\User;
use App\Mail\TimeOffRequestDeclined;


class EvaluationController extends Controller
{
    /**
     * Display a listing of rule performance evaluations
     */
    public function rule_performance_index()
    {
        $rule_performances = RuleEvaluationPerformance::all();
        return view('/evaluation/rule/performance/index', compact('rule_performances'));
    }

    /**
     * Show the form for creating a new rule performance evaluation
     */
    public function rule_performance_create()
    {
        return view('evaluation.rule.performance.create');
    }

    /**
     * Store a newly created rule performance evaluation in storage
     */
    public function rule_performance_store(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|string|max:255',
            'weight' => 'required|numeric|between:0,100',
            'status' => 'required|in:Active,Inactive'
        ]);

        // Check for duplicate type
        if (RuleEvaluationPerformance::where('type', $validatedData['type'])->exists()) {
            return response()->json([
                'message' => 'The performance type you entered already exists. Please use a different type.'
            ], 409); // 409 Conflict status code
        }

        try {
            $performance = RuleEvaluationPerformance::create($validatedData);

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
    public function rule_performance_edit($id)
    {
        $performance = RuleEvaluationPerformance::findOrFail($id);
        return view('evaluation/rule/performance/update', compact('performance'));
    }

    /**
     * Update the specified rule performance evaluation in storage
     */
    public function rule_performance_update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'type' => 'required|string|max:255',
            'weight' => 'required|numeric|between:0,100',
            'status' => 'required|in:Active,Inactive'
        ]);

        // Check for duplicate type (excluding current record)
        if (RuleEvaluationPerformance::where('type', $validatedData['type'])
            ->where('id', '!=', $id)
            ->exists()
        ) {
            return response()->json([
                'message' => 'The performance type already exists. Please use a different type.'
            ], 409);
        }

        try {
            $performance = RuleEvaluationPerformance::findOrFail($id);
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


    /**
     * Display a listing of Assign performance evaluations
     */
    public function assign_performance_index($id)
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $evaluations = EmployeeEvaluationPerformance::select(
            'employee_evaluation_performance.user_id',
            'users.name as employee_name',
            'users.position',
            'users.department',
            DB::raw('SUM(employee_evaluation_performance.value * rule_evaluation_performance.weight) as total_score')
        )
            ->join('users', 'employee_evaluation_performance.user_id', '=', 'users.id')
            ->join('rule_evaluation_performance', 'employee_evaluation_performance.rule_evaluation_performance_id', '=', 'rule_evaluation_performance.id')
            ->where('employee_evaluation_performance.evaluator_id', $id)
            ->whereMonth('employee_evaluation_performance.created_at', $currentMonth)
            ->whereYear('employee_evaluation_performance.created_at', $currentYear)
            ->groupBy('employee_evaluation_performance.user_id', 'users.name', 'users.position', 'users.department')
            ->get();

        $departments = User::distinct()->pluck('department');
        $positions = User::distinct()->pluck('position');

        return view('evaluation.assign.performance.index', compact('evaluations', 'departments', 'positions', 'currentMonth', 'currentYear'));
    }

    public function getEvaluationDetails(Request $request)
    {
        $userId = $request->input('user_id');
        $month = $request->input('month');
        $year = $request->input('year');

        $details = EmployeeEvaluationPerformance::select(
            'rule_evaluation_performance.type as category',
            'rule_evaluation_performance.weight',
            'employee_evaluation_performance.value',
            DB::raw('employee_evaluation_performance.value * rule_evaluation_performance.weight as weighted_score')
        )
            ->join('rule_evaluation_performance', 'employee_evaluation_performance.rule_evaluation_performance_id', '=', 'rule_evaluation_performance.id')
            ->where('employee_evaluation_performance.user_id', $userId)
            ->whereMonth('employee_evaluation_performance.created_at', $month)
            ->whereYear('employee_evaluation_performance.created_at', $year)
            ->get();

        $html = '';
        foreach ($details as $detail) {
            $html .= '<tr>';
            $html .= '<td>' . $detail->category . '</td>';
            $html .= '<td>' . ($detail->weight * 100) . '%</td>';
            $html .= '<td>' . $detail->value . '</td>';
            $html .= '<td>' . number_format($detail->weighted_score, 2) . '</td>';
            $html .= '</tr>';
        }

        return $html;
    }
}
