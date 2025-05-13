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

class EmployeeEvaluationSystemSeederV2 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedEvaluationPerformances();
    }
    
    /**
     * Seed evaluation performances and related details
     * This will add missing data for 2023-2025
     */
    private function seedEvaluationPerformances()
    {
        // Get employees who can be evaluated (non-inactive)
        $employees = User::where('employee_status', '!=', 'Inactive')
                     ->select('id', 'position_id', 'department_id')
                     ->get();
        
        // Get existing evaluations to avoid duplicates
        $existingEvaluations = EvaluationPerformance::select('user_id', 'date')->get()
            ->mapToGroups(function ($item) {
                $yearMonth = Carbon::parse($item->date)->format('Y-m');
                return [$item->user_id.'-'.$yearMonth => true];
            })->toArray();
        
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
        
        // Get all warning letters for potential reductions
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
        
        // Generate evaluations focusing on 2023-06 to 2025-08
        // Changed the start month from 2023-01 to 2023-06 as requested
        $startMonth = Carbon::create(2023, 6, 1);
        $endMonth = Carbon::create(2025, 8, 1);
        
        $currentMonth = clone $startMonth;
        
        $totalCreated = 0;
        
        while ($currentMonth->lte($endMonth)) {
            $created = $this->generateMonthlyEvaluations(
                $currentMonth, 
                $usersByRanking, 
                $weightsByPosition, 
                $warningLetters,
                $reductionsByTypeId,
                $existingEvaluations
            );
            
            $totalCreated += $created;
            $this->command->info("Added {$created} evaluations for {$currentMonth->format('Y-m')}");
            
            $currentMonth->addMonth();
        }
        
        $this->command->info("Total evaluation performances seeded: {$totalCreated}");
    }
    
    /**
     * Generate evaluations for a specific month
     * Returns number of evaluations created
     */
    private function generateMonthlyEvaluations(
        $month, 
        $usersByRanking, 
        $weightsByPosition, 
        $warningLetters,
        $reductionsByTypeId,
        &$existingEvaluations
    ) {
        $createdCount = 0;
        
        // Process each employee by ranking, starting from lowest (staff)
        for ($ranking = 5; $ranking >= 1; $ranking--) {
            // These employees will be evaluated
            $employeesToEvaluate = $usersByRanking[$ranking];
            
            foreach ($employeesToEvaluate as $employee) {
                // Check if evaluation already exists for this employee in this month
                $yearMonth = $month->format('Y-m');
                $evalKey = $employee->id.'-'.$yearMonth;
                
                if (isset($existingEvaluations[$evalKey])) {
                    continue; // Skip if evaluation already exists
                }
                
                // Find appropriate evaluator
                $evaluator = null;
                
                if ($ranking == 5) {
                    // Staff can be evaluated by same department supervisor or above
                    $evaluator = $this->findEvaluator($usersByRanking, $employee, 4);
                } else if ($ranking == 4) {
                    // Supervisors can be evaluated by same department manager or above
                    $evaluator = $this->findEvaluator($usersByRanking, $employee, 3);
                } else if ($ranking == 3) {
                    // Managers can be evaluated by GMs or Directors
                    $evaluator = $this->findEvaluator($usersByRanking, $employee, 2);
                } else if ($ranking == 2) {
                    // GMs can be evaluated by Directors
                    $evaluator = $this->findEvaluator($usersByRanking, $employee, 1);
                } else if ($ranking == 1) {
                    // Directors can't be evaluated
                    continue;
                }
                
                if (!$evaluator) {
                    continue; // Skip if no suitable evaluator found
                }
                
                // Create evaluation with random date in the month (avoiding weekends)
                $attempts = 0;
                $evaluationDate = null;
                
                do {
                    $day = rand(1, min(28, $month->daysInMonth));
                    $evaluationDate = (clone $month)->setDay($day);
                    $attempts++;
                    
                    // Avoid weekends, keep trying up to 10 times
                    if ($attempts > 10) {
                        break;
                    }
                } while ($evaluationDate->isWeekend());
                
                // If we couldn't find a good date after 10 attempts, just use a weekday
                if ($evaluationDate === null || $evaluationDate->isWeekend()) {
                    $evaluationDate = (clone $month)->setDay(rand(1, min(28, $month->daysInMonth)));
                    // Ensure it's a weekday
                    while ($evaluationDate->isWeekend()) {
                        $evaluationDate->addDay();
                    }
                }
                
                // Add business hours
                $evaluationDate->setHour(rand(9, 16))->setMinute(rand(0, 59))->setSecond(rand(0, 59));
                
                // Check for warning letters in the given month
                $relevantWarningLetters = $warningLetters->filter(function ($letter) use ($employee, $evaluationDate) {
                    $letterDate = Carbon::parse($letter->created_at);
                    $letterExpired = $letter->expired_at ? Carbon::parse($letter->expired_at) : null;
                    
                    // Consider the letter if it's for this employee and either:
                    // 1. Created in the same month as the evaluation
                    // 2. Created before and not expired by evaluation date
                    return $letter->user_id == $employee->id && 
                          ($letterDate->format('Y-m') == $evaluationDate->format('Y-m') ||
                          ($letterDate->lt($evaluationDate) && 
                           (!$letterExpired || $letterExpired->gte($evaluationDate))));
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
                
                // Calculate evaluation scores
                $totalScore = 0;
                $details = [];
                
                // Use performance trend to make evaluations more realistic over time
                $employeePerformanceBase = $this->getEmployeeBasePerformance($employee->id);
                $monthInfluence = $this->getMonthInfluence($evaluationDate);
                
                foreach ($applicableWeights as $weight) {
                    $value = $this->getPerformanceValueWithTrend(
                        $employeePerformanceBase, 
                        $monthInfluence, 
                        $evaluationDate
                    );
                    
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
                
                // Create evaluation (REMOVED final_score field since it's generated)
                $evaluation = EvaluationPerformance::create([
                    'user_id' => $employee->id,
                    'evaluator_id' => $evaluator->id,
                    'date' => $evaluationDate,
                    'total_score' => $finalScore,
                    'total_reduction' => $totalReduction,
                    // final_score removed as it's a generated column
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
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
                
                // Create reductions if any
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
                
                // Mark as created
                $existingEvaluations[$evalKey] = true;
                $createdCount++;
            }
        }
        
        return $createdCount;
    }
    
    /**
     * Find an appropriate evaluator for an employee
     */
    private function findEvaluator($usersByRanking, $employee, $minRanking)
    {
        // Try to find someone in the same department first
        for ($rank = $minRanking; $rank >= 1; $rank--) {
            $potentialEvaluators = array_filter($usersByRanking[$rank], function ($user) use ($employee, $rank) {
                // Directors and GMs can evaluate anyone
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
     * Get a base performance value for an employee
     * This ensures some consistency in an employee's evaluations
     */
    private function getEmployeeBasePerformance($employeeId)
    {
        // Use employee ID as seed for consistent results
        srand($employeeId);
        
        // Generate a base between 2.5 and 4.5
        $base = 2.5 + (rand(0, 200) / 100);
        
        // Reset random seed
        srand();
        
        return $base;
    }
    
    /**
     * Get month-specific influence (some months might have better/worse performance)
     */
    private function getMonthInfluence($date)
    {
        $month = (int)$date->format('m');
        
        // Year-end and mid-year usually have higher performance
        if (in_array($month, [6, 12])) {
            return 0.3; // Positive influence
        }
        
        // Beginning of year and vacation seasons might have lower performance
        if (in_array($month, [1, 7, 8])) {
            return -0.2; // Negative influence
        }
        
        return 0; // Neutral
    }
    
    /**
     * Get performance value with trend consideration
     * Makes the scores more realistic with gradual improvement/decline
     */
    private function getPerformanceValueWithTrend($basePerformance, $monthInfluence, $date)
    {
        // Start with base performance
        $baseValue = $basePerformance;
        
        // Add time-based trend (slight improvement over years)
        $yearsSince2023 = $date->year - 2023;
        $yearTrend = $yearsSince2023 * 0.1; // Small improvement each year
        
        // Add monthly influence
        $value = $baseValue + $yearTrend + $monthInfluence;
        
        // Add some randomness (-0.5 to +0.5)
        $randomness = (rand(-50, 50) / 100);
        $value += $randomness;
        
        // Ensure value is between 1 and 5
        $value = max(1, min(5, $value));
        
        // Round to appropriate precision (some values might be whole numbers, others might have decimals)
        $precision = rand(0, 1) ? 0 : 1; // Either whole number or one decimal place
        $value = round($value, $precision);
        
        return $value;
    }
    
    /**
     * Get multiple random evaluation messages based on score
     * 
     * @param float $score The evaluation score
     * @param int $count Number of messages to generate
     * @return array Array of message strings
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
            'Employee needs help with task prioritization.',
            'Difficulty meeting project deadlines consistently.',
            'Technical knowledge gaps are affecting performance.',
            'Needs to improve cooperation with team members.',
            'Shows resistance to implementing new processes.',
            'Documentation quality needs significant improvement.',
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
            'Demonstrates adequate problem-solving skills but struggles with complex issues.',
            'Communication is clear but could be more proactive.',
            'Meets deadlines but often finishes tasks at the last minute.',
            'Documentation is complete but could be more thorough.',
            'Follows instructions well but rarely suggests improvements.',
            'Handles routine tasks effectively but needs support with new challenges.',
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
            'Innovative approach to problem-solving yields excellent results.',
            'Proactively identifies and addresses potential issues before they escalate.',
            'Documentation is thorough, clear, and consistently excellent.',
            'Effectively trains and supports other team members.',
            'Takes ownership of projects and drives them to successful completion.',
            'Client feedback is consistently positive regarding interactions.',
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
}