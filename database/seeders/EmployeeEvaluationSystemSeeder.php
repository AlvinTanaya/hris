<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\WarningLetter;
use App\Models\EvaluationPerformance;
use App\Models\EvaluationPerformanceDetail;
use App\Models\EvaluationPerformanceMessage;
use App\Models\EvaluationPerformanceReduction;
use App\Models\User;
use Carbon\Carbon;

class EmployeeEvaluationSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // First truncate existing data for users with ID > 51
        // $this->truncateExistingData();
        
        $this->seedWarningLetters();
        $this->seedEvaluationPerformances();
    }

    /**
     * Truncate existing data for users with ID > 51
     */
    // private function truncateExistingData()
    // {
    //     // Delete warning letters for users with ID > 51
    //     WarningLetter::where('user_id', '>', 51)->delete();
        
    //     // Get evaluation IDs for users with ID > 51
    //     $evaluationIds = EvaluationPerformance::where('user_id', '>', 51)
    //         ->pluck('id')
    //         ->toArray();
            
    //     // Delete related data
    //     EvaluationPerformanceDetail::whereIn('evaluation_id', $evaluationIds)->delete();
    //     EvaluationPerformanceMessage::whereIn('evaluation_id', $evaluationIds)->delete();
    //     EvaluationPerformanceReduction::whereIn('evaluation_id', $evaluationIds)->delete();
        
    //     // Finally delete the evaluations
    //     EvaluationPerformance::where('user_id', '>', 51)->delete();
    // }

    /**
     * Seed warning letters
     */
    private function seedWarningLetters()
    {
        // Get ALL users (including those with ID > 51)
        $users = User::where('employee_status', '!=', 'Inactive')
                    ->select('id', 'position_id')
                    ->get();
        
        // Get warning letter rules
        $warningLetterRules = DB::table('rule_warning_letter')->get();
        
        // Get reduction rules
        $reductionRules = DB::table('rule_evaluation_reduction_performance')
                            ->where('status', 'Active')
                            ->get();
        
        // Create admins who will create warning letters
        $adminIds = [11, 21];

        // Start date for generating warning letters
        $startDate = Carbon::create(2023, 1, 1);
        $endDate = Carbon::create(2025, 8, 1);
        
        // Generate warning letters with modified distribution
        $warningLetters = [];
        
        // We'll create more Verbal and ST1, medium ST2 and SP1, few SP2 and SP3
        $letterTypeCounts = [
            1 => 35,  // Verbal - many
            2 => 25,  // ST1 - many
            3 => 15,  // ST2 - medium
            4 => 15,  // SP1 - medium
            5 => 5,   // SP2 - few
            6 => 5    // SP3 - few
        ];
        
        foreach ($letterTypeCounts as $typeId => $count) {
            for ($i = 0; $i < $count; $i++) {
                // Select random user
                $user = $users->random();
                
                // Select rule by type
                $rule = $warningLetterRules->where('id', $typeId)->first();
                
                // Random date between start and end
                $createdAt = Carbon::create(
                    rand($startDate->year, $endDate->year),
                    rand(1, $endDate->month),
                    rand(1, 28),
                    rand(8, 17),
                    rand(0, 59),
                    rand(0, 59)
                );
                
                // Calculate expired_at based on rule's expired_length
                $expiredAt = null;
                if ($rule->expired_length) {
                    $expiredAt = (clone $createdAt)->addMonths($rule->expired_length);
                }
                
                // Generate warning letter number (except for Verbal warnings)
                $warningLetterNumber = null;
                if ($rule->id != 1) {
                    $letterCount = count(array_filter($warningLetters, function($letter) use ($rule) {
                        return $letter['type_id'] == $rule->id;
                    })) + 1;
                    
                    $monthRoman = $this->convertToRoman($createdAt->month);
                    $datePart = $createdAt->format('dmy');
                    
                    $warningLetterNumber = "no.{$letterCount}/TJI/{$rule->name}/{$monthRoman}/{$datePart}-1";
                }
                
                $warningLetters[] = [
                    'user_id' => $user->id,
                    'maker_id' => $adminIds[array_rand($adminIds)],
                    'type_id' => $rule->id,
                    'warning_letter_number' => $warningLetterNumber,
                    'reason_warning' => $this->getRandomWarningReason($rule->id),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                    'expired_at' => $expiredAt,
                ];
            }
        }
        
        // Insert warning letters
        foreach ($warningLetters as $letter) {
            WarningLetter::create($letter);
        }

        $this->command->info('Warning letters seeded successfully with modified distribution!');
    }
    
    /**
     * Generate random warning reason based on type
     */
    private function getRandomWarningReason($typeId)
    {
        $reasons = [
            1 => [ // Verbal
                'Poor time management resulting in missed deadlines',
                'Frequently late to work without proper notification',
                'Failure to follow proper communication protocols',
                'Disruptive behavior in team meetings',
                'Minor violations of company policy'
            ],
            2 => [ // ST1
                'Repeated instances of tardiness despite verbal warnings',
                'Underperformance on key performance indicators',
                'Failure to complete assigned tasks by deadlines',
                'Inappropriate communication with colleagues',
                'Violation of department policies'
            ],
            3 => [ // ST2
                'Consistent pattern of underperformance',
                'Multiple incidents of unexcused absences',
                'Failure to follow supervisor instructions',
                'Repeated violations of company policy',
                'Negligence resulting in minor operational issues'
            ],
            4 => [ // SP1
                'Serious breach of company protocols',
                'Negligence resulting in financial loss',
                'Insubordination to management',
                'Major customer complaint due to employee conduct',
                'Unauthorized disclosure of sensitive information'
            ],
            5 => [ // SP2
                'Severe violation of company code of conduct',
                'Major breach of safety protocols endangering colleagues',
                'Gross negligence resulting in significant operational disruption',
                'Serious misconduct affecting company reputation',
                'Pattern of behavior showing disregard for previous warnings'
            ],
            6 => [ // SP3
                'Severe misconduct warranting immediate disciplinary action',
                'Critical breach of security protocols',
                'Gross negligence resulting in major financial loss',
                'Serious ethical violation',
                'Actions severely damaging company reputation or operations'
            ]
        ];
        
        return $reasons[$typeId][array_rand($reasons[$typeId])];
    }
    
    /**
     * Seed evaluation performances and related details
     */
    private function seedEvaluationPerformances()
    {
        // Get ALL employees (including those with ID > 51)
        $employees = User::where('employee_status', '!=', 'Inactive')
                     ->select('id', 'position_id', 'department_id')
                     ->get();
        
        // Organize users by ranking for proper evaluation hierarchy
        $usersByRanking = [
            1 => $employees->where('position_id', 1)->all(), // Director
            2 => $employees->where('position_id', 2)->all(), // General Manager
            3 => $employees->where('position_id', 3)->all(), // Manager
            4 => $employees->where('position_id', 4)->all(), // Supervisor
            5 => $employees->where('position_id', 5)->all(), // Staff
        ];
        
        // Get criteria weights
        $weightPerformances = DB::table('rule_evaluation_weight_performance')
                                ->where('status', 'Active')
                                ->get();
        
        // Group weights by position
        $weightsByPosition = [];
        foreach ($weightPerformances as $weight) {
            if (!isset($weightsByPosition[$weight->position_id])) {
                $weightsByPosition[$weight->position_id] = [];
            }
            $weightsByPosition[$weight->position_id][] = $weight;
        }
        
        // Get warning letters for potential reductions
        $warningLetters = WarningLetter::all();
        
        // Get reduction rules
        $reductionRules = DB::table('rule_evaluation_reduction_performance')
                            ->where('status', 'Active')
                            ->get();
                            
        // Map rule types to reduction rules
        $reductionsByTypeId = [];
        foreach ($reductionRules as $rule) {
            $reductionsByTypeId[$rule->type_id] = $rule;
        }
        
        // Generate evaluations from 2023-01 to 2025-08
        $startMonth = Carbon::create(2023, 1, 1);
        $endMonth = Carbon::create(2025, 8, 1);
        
        $currentMonth = clone $startMonth;
        
        while ($currentMonth->lte($endMonth)) {
            $this->generateMonthlyEvaluations(
                $currentMonth, 
                $usersByRanking, 
                $weightsByPosition, 
                $warningLetters,
                $reductionsByTypeId
            );
            
            $currentMonth->addMonth();
        }
        
        $this->command->info('Evaluation performances seeded successfully for all users!');
    }
    
    /**
     * Generate evaluations for a specific month
     */
    private function generateMonthlyEvaluations(
        $month, 
        $usersByRanking, 
        $weightsByPosition, 
        $warningLetters,
        $reductionsByTypeId
    ) {
        // Process each employee by ranking, starting from lowest (staff)
        for ($ranking = 5; $ranking >= 1; $ranking--) {
            $employeesToEvaluate = $usersByRanking[$ranking];
            
            foreach ($employeesToEvaluate as $employee) {
                $evaluator = $this->findEvaluator($usersByRanking, $employee, $ranking);
                
                if (!$evaluator) {
                    continue;
                }
                
                // Create evaluation with random date in the month
                $evaluationDate = (clone $month)->setDay(rand(1, 28));
                
                // Check for warning letters in the given month
                $relevantWarningLetters = $warningLetters->filter(function ($letter) use ($employee, $evaluationDate) {
                    $letterDate = Carbon::parse($letter->created_at);
                    return $letter->user_id == $employee->id && 
                           $letterDate->year == $evaluationDate->year && 
                           $letterDate->month == $evaluationDate->month;
                });
                
                // Calculate total reduction based on warning letters
                $totalReduction = 0;
                $reductions = [];
                
                foreach ($relevantWarningLetters as $letter) {
                    if (isset($reductionsByTypeId[$letter->type_id])) {
                        $reductionRule = $reductionsByTypeId[$letter->type_id];
                        $reductionAmount = $reductionRule->weight;
                        $totalReduction += $reductionAmount;
                        
                        $reductions[] = [
                            'warning_letter_id' => $letter->id,
                            'reduction_amount' => $reductionAmount,
                        ];
                    }
                }
                
                // Get applicable performance weights for employee's position
                $applicableWeights = isset($weightsByPosition[$employee->position_id]) ? 
                                      $weightsByPosition[$employee->position_id] : [];
                
                // Calculate evaluation scores with modified distribution (1, 1.5, 2, 2.5, 3)
                $totalScore = 0;
                $details = [];
                
                foreach ($applicableWeights as $weight) {
                    $value = $this->getModifiedPerformanceValue();
                    $weightedScore = $value * $weight->weight;
                    $totalScore += $weightedScore;
                    
                    $details[] = [
                        'weight_performance_id' => $weight->id,
                        'weight' => $weight->weight,
                        'value' => $value,
                    ];
                }
                
                // Adjust total score accounting for reductions
                $finalScore = max(0, $totalScore - $totalReduction);
                
                // Create evaluation
                $evaluation = EvaluationPerformance::create([
                    'user_id' => $employee->id,
                    'evaluator_id' => $evaluator->id,
                    'date' => $evaluationDate,
                    'total_score' => $finalScore,
                    'total_reduction' => $totalReduction,
                ]);
                
                // Create details
                foreach ($details as $detail) {
                    EvaluationPerformanceDetail::create([
                        'evaluation_id' => $evaluation->id,
                        'weight_performance_id' => $detail['weight_performance_id'],
                        'weight' => $detail['weight'],
                        'value' => $detail['value'],
                    ]);
                }
                
                // Create reductions
                foreach ($reductions as $reduction) {
                    EvaluationPerformanceReduction::create([
                        'evaluation_id' => $evaluation->id,
                        'warning_letter_id' => $reduction['warning_letter_id'],
                        'reduction_amount' => $reduction['reduction_amount'],
                    ]);
                }
                
                // Add multiple random messages (1-3 messages per evaluation)
                $messageCount = rand(1, 3);
                $messages = $this->getRandomEvaluationMessages($finalScore, $messageCount);
                
                foreach ($messages as $message) {
                    EvaluationPerformanceMessage::create([
                        'evaluation_id' => $evaluation->id,
                        'message' => $message,
                    ]);
                }
            }
        }
    }
    
    /**
     * Find an appropriate evaluator for an employee
     */
    private function findEvaluator($usersByRanking, $employee, $employeeRanking)
    {
        // Determine minimum evaluator ranking based on employee's ranking
        $minEvaluatorRanking = 1; // Default (director can evaluate anyone)
        
        if ($employeeRanking == 5) { // Staff
            $minEvaluatorRanking = 4; // Can be evaluated by supervisor or above
        } elseif ($employeeRanking == 4) { // Supervisor
            $minEvaluatorRanking = 3; // Can be evaluated by manager or above
        } elseif ($employeeRanking == 3) { // Manager
            $minEvaluatorRanking = 2; // Can be evaluated by GM or above
        } elseif ($employeeRanking == 2) { // GM
            $minEvaluatorRanking = 1; // Can be evaluated by director
        } elseif ($employeeRanking == 1) { // Director
            return null; // Directors can't be evaluated
        }
        
        // Try to find someone in the same department first
        for ($rank = $minEvaluatorRanking; $rank >= 1; $rank--) {
            $potentialEvaluators = array_filter($usersByRanking[$rank], function ($user) use ($employee, $rank) {
                // Directors and GMs can evaluate anyone regardless of department
                if ($rank <= 2) {
                    return true;
                }
                // Others can only evaluate within their department
                return $user->department_id == $employee->department_id;
            });
            
            if (!empty($potentialEvaluators)) {
                return $potentialEvaluators[array_rand($potentialEvaluators)];
            }
        }
        
        return null;
    }
    
    /**
     * Get modified performance value (1, 1.5, 2, 2.5, 3) with more 2.5 and 3, medium 2, few 1 and 1.5
     */
    private function getModifiedPerformanceValue()
    {
        // Distribution weighted toward higher scores (2.5 and 3)
        $distribution = [
            1.0, 1.0,  // Few 1.0
            1.5, 1.5,  // Few 1.5
            2.0, 2.0, 2.0, 2.0,  // Medium 2.0
            2.5, 2.5, 2.5, 2.5, 2.5,  // Many 2.5
            3.0, 3.0, 3.0, 3.0, 3.0, 3.0  // Many 3.0
        ];
        
        return $distribution[array_rand($distribution)];
    }
    
    /**
     * Get multiple random evaluation messages based on score
     */
    private function getRandomEvaluationMessages($score, $count)
    {
        $lowMessages = [
            'Significant improvement needed in key areas. Please schedule a follow-up meeting to discuss a performance improvement plan.',
            'Performance falls below expected standards. Additional training and closer supervision recommended.',
            'The employee is struggling to meet basic job requirements. Immediate intervention needed.',
            'Attendance and punctuality issues need immediate attention.',
            'Communication skills require significant improvement.',
            'Time management skills need development.',
            'Work quality is inconsistent and often below standards.',
            'Customer service approach needs substantial improvement.',
        ];
        
        $mediumMessages = [
            'Meets basic job requirements but has room for improvement in several areas.',
            'Performance is acceptable but inconsistent. Would benefit from more structure and regular feedback.',
            'Shows potential but needs guidance to fully meet expectations in role.',
            'Technical skills are adequate but could be improved with additional training.',
            'Works effectively in a team but could take more initiative.',
            'Generally reliable but needs occasional reminders about deadlines.',
            'Good customer service but could improve follow-up procedures.',
            'Acceptable work quality with occasional lapses.',
        ];
        
        $highMessages = [
            'Consistently performs above expectations. Consider for additional responsibilities.',
            'Excellent work quality and attitude. A valuable team member who leads by example.',
            'Outstanding performance across all evaluation criteria. Potential for advancement.',
            'Exceeds expectations in all areas. Demonstrates leadership qualities and initiative.',
            'Highly skilled professional who mentors others effectively.',
            'Exceptional problem-solving abilities that benefit the entire team.',
            'Superior customer service skills that generate positive feedback.',
            'Consistently delivers high-quality work before deadlines.',
            'Demonstrates excellent initiative and doesn\'t require close supervision.',
        ];
        
        $messagePool = [];
        if ($score < 100) {
            $messagePool = $lowMessages;
        } else if ($score < 200) {
            $messagePool = $mediumMessages;
        } else {
            $messagePool = $highMessages;
        }
        
        // Make sure we don't request more messages than available
        $count = min($count, count($messagePool));
        
        // Shuffle the array to get random messages
        shuffle($messagePool);
        
        // Return the requested number of messages
        return array_slice($messagePool, 0, $count);
    }
    
    /**
     * Convert month number to Roman numeral
     */
    private function convertToRoman($number)
    {
        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 
            6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X',
            11 => 'XI', 12 => 'XII'
        ];
        
        return $romans[$number] ?? $number;
    }
}