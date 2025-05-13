<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\EvaluationPerformance;
use App\Models\WarningLetter;
use App\Models\EvaluationPerformanceReduction;
use App\Models\RuleEvaluationReductionPerformance;

class FixEvaluationRelationsSeeder extends Seeder
{
    /**
     * Fix evaluation relations without deleting existing data.
     *
     * @return void
     */
    public function run()
    {
        // 0. Handle warning letters with type_id = 1 (Verbal): remove relations and nullify warning_letter_numbers
        $this->handleVerbalWarningLetters();

        // 1. Fix inconsistent relations between EvaluationPerformance and WarningLetter
        $this->fixInconsistentRelations();

        // 2. Add missing relations between evaluations and warning letters in the same month
        $this->addMissingRelations();

        // 3. Update total_reduction values based on actual relations
        $this->updateTotalReductions();

        // 4. Fix warning letters without numbers (except type_id = 1)
        $this->fixWarningLettersWithoutNumbers();

        // 5. Apply 3-month restriction rule for ST warning letters
        $this->applySTRestrictionPeriod();
    }

    /**
     * Handle verbal warning letters (type_id = 1):
     * - Remove relations in EvaluationPerformanceReduction for these warning letters
     * - Nullify warning_letter_number for these warning letters
     */
    private function handleVerbalWarningLetters()
    {
        // Find all verbal warning letters (type_id = 1)
        $verbalWarningLetters = WarningLetter::where('type_id', 1)->get();

        foreach ($verbalWarningLetters as $warningLetter) {
            // Remove relations for this warning letter
            EvaluationPerformanceReduction::where('warning_letter_id', $warningLetter->id)->delete();

            // Nullify warning_letter_number
            $warningLetter->warning_letter_number = null;
            $warningLetter->save();
        }
    }

    /**
     * Fix inconsistent relations where the months don't match
     */
    private function fixInconsistentRelations()
    {
        // Get all evaluations that have reductions
        $evaluationsWithReductions = EvaluationPerformance::where('total_reduction', '>', 0)->get();

        foreach ($evaluationsWithReductions as $evaluation) {
            // Get all reduction relations for this evaluation
            $reductions = EvaluationPerformanceReduction::where('evaluation_id', $evaluation->id)->get();

            foreach ($reductions as $reduction) {
                // Get the related warning letter
                $warningLetter = WarningLetter::find($reduction->warning_letter_id);

                if (!$warningLetter) {
                    // Warning letter doesn't exist anymore, remove the relation
                    $reduction->delete();
                    continue;
                }

                // Skip verbal warning letters (type_id = 1)
                if ($warningLetter->type_id == 1) {
                    $reduction->delete();
                    continue;
                }

                // Skip SP warning letters (should only use ST types)
                if (strpos($warningLetter->name, 'SP') === 0) {
                    $reduction->delete();
                    continue;
                }

                // Check if the warning letter's month matches the evaluation's month
                $evalDate = Carbon::parse($evaluation->date);
                $wlDate = Carbon::parse($warningLetter->created_at);

                if ($evalDate->year != $wlDate->year || $evalDate->month != $wlDate->month) {
                    // Dates don't match - find a correct warning letter in the same month
                    $correctWarningLetter = WarningLetter::where('user_id', $evaluation->user_id)
                        ->whereYear('created_at', $evalDate->year)
                        ->whereMonth('created_at', $evalDate->month)
                        ->whereRaw("name LIKE 'ST%'") // Only use ST types
                        ->first();

                    if ($correctWarningLetter) {
                        // Update the relation to point to the correct warning letter
                        $reduction->warning_letter_id = $correctWarningLetter->id;
                        $reduction->save();
                    } else {
                        // No correct warning letter found, create one if we're allowed (considering the 3-month rule)
                        $canCreateNewST = $this->canCreateSTWarningLetter($evaluation->user_id, $evalDate);

                        if ($canCreateNewST) {
                            $newWarningLetter = $this->createWarningLetter(
                                $evaluation->user_id,
                                $evalDate,
                                $reduction->reduction_amount
                            );

                            // Update the relation
                            $reduction->warning_letter_id = $newWarningLetter->id;
                            $reduction->save();
                        } else {
                            // Cannot create new ST due to restriction period, remove the relation
                            $reduction->delete();
                        }
                    }
                }
            }
        }
    }

    /**
     * Add missing relations between evaluations and warning letters in the same month
     */
    private function addMissingRelations()
    {
        // Get all evaluations
        $evaluations = EvaluationPerformance::all();

        foreach ($evaluations as $evaluation) {
            $evalDate = Carbon::parse($evaluation->date);

            // Find warning letters for the same user in the same month (only ST types, not Verbal or SP)
            $warningLetters = WarningLetter::where('user_id', $evaluation->user_id)
                ->whereNotNull('warning_letter_number')
                ->whereIn('type_id', [2, 3])
                ->whereYear('created_at', $evalDate->year)
                ->whereMonth('created_at', $evalDate->month)
                ->get();

            // For each warning letter, check if we already have a relation
            foreach ($warningLetters as $warningLetter) {
                $existingRelation = EvaluationPerformanceReduction::where([
                    'evaluation_id' => $evaluation->id,
                    'warning_letter_id' => $warningLetter->id
                ])->first();

                if (!$existingRelation) {
                    // Determine reduction amount based on warning letter type
                    $rule = RuleEvaluationReductionPerformance::where('type_id', $warningLetter->type_id)->first();
                    $reductionAmount = $rule ? $rule->weight : 3; // Default to 3 for ST letters

                    // Create the missing relation
                    EvaluationPerformanceReduction::create([
                        'evaluation_id' => $evaluation->id,
                        'warning_letter_id' => $warningLetter->id,
                        'reduction_amount' => $reductionAmount
                    ]);
                }
            }
        }
    }

    /**
     * Update total_reduction values for all evaluations based on actual relations
     */
    private function updateTotalReductions()
    {
        $evaluations = EvaluationPerformance::all();

        foreach ($evaluations as $evaluation) {
            // Calculate total reduction amount from relations
            $totalReduction = DB::table('employee_evaluation_performance_reductions')
                ->where('evaluation_id', $evaluation->id)
                ->sum('reduction_amount');

            // Update evaluation - only update total_reduction, final_score is auto-generated
            $evaluation->total_reduction = $totalReduction;
            $evaluation->save();
        }
    }

    /**
     * Fix warning letters without numbers (except type_id = 1)
     */
    private function fixWarningLettersWithoutNumbers()
    {
        $warningLettersWithoutNumbers = WarningLetter::whereNull('warning_letter_number')
            ->orWhere('warning_letter_number', '')
            ->where('type_id', '!=', 1) // Exclude verbal warnings
            ->whereIn('type_id', [2, 3])
            ->get();

        foreach ($warningLettersWithoutNumbers as $warningLetter) {
            $date = Carbon::parse($warningLetter->created_at);
            $warningLetter->warning_letter_number = 'ST/' . $date->format('Y/m') . '/' . $warningLetter->user_id;
            $warningLetter->save();
        }
    }

    /**
     * Apply 3-month restriction period for ST warning letters
     * Remove any ST warning letters that violate the 3-month restriction rule
     */
    private function applySTRestrictionPeriod()
    {
        // Get all users with warning letters
        $userIds = WarningLetter::distinct('user_id')->pluck('user_id')->toArray();

        foreach ($userIds as $userId) {
            // Get all ST warning letters for this user, ordered by date
            $stWarningLetters = WarningLetter::where('user_id', $userId)
            ->whereIn('type_id', [2, 3])
                ->orderBy('created_at')
                ->get();

            $lastSTDate = null;

            foreach ($stWarningLetters as $warningLetter) {
                $wlDate = Carbon::parse($warningLetter->created_at);

                if ($lastSTDate && $wlDate->diffInMonths($lastSTDate) < 3) {
                    // This ST warning letter violates the 3-month restriction rule
                    // Remove relations for this warning letter
                    EvaluationPerformanceReduction::where('warning_letter_id', $warningLetter->id)->delete();

                    // Delete the warning letter or mark it as invalid
                    // $warningLetter->delete(); // Or alternatively, mark it as invalid:
                    $warningLetter->reason_warning = 'INVALID: Violates 3-month restriction rule';
                    $warningLetter->save();
                } else {
                    // This warning letter is valid, remember its date
                    $lastSTDate = $wlDate;
                }
            }
        }
    }

    /**
     * Check if a new ST warning letter can be created for a user at a given date
     * (considering the 3-month restriction rule)
     */
    private function canCreateSTWarningLetter($userId, $date)
    {
        // Find the most recent ST warning letter before the given date
        $previousST = WarningLetter::where('user_id', $userId)
        ->whereIn('type_id', [2, 3])
            ->where('created_at', '<', $date)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$previousST) {
            // No previous ST warning letters, so we can create a new one
            return true;
        }

        // Check if the 3-month restriction period has passed
        $previousDate = Carbon::parse($previousST->created_at);
        return $date->diffInMonths($previousDate) >= 3;
    }

    /**
     * Create a new warning letter (ST type only)
     */
    private function createWarningLetter($userId, $date, $reductionAmount)
    {
        // Determine appropriate ST type (ST1, ST2, etc.) based on user's warning letter history
        $stType = $this->determineSTType($userId, $date);

        // Get a maker_id (randomly or first available)
        $makerId = DB::table('employee_warning_letter')
            ->select('maker_id')
            ->whereNotNull('maker_id')
            ->first();

        $makerId = $makerId ? $makerId->maker_id : 1;

        // Find the type_id for this ST type from rule_warning_letter table
        $typeId = DB::table('rule_warning_letter')
            ->where('name', $stType)
            ->value('id');

        if (!$typeId) {
            // Default to ST1 type_id if not found
            $typeId = DB::table('rule_warning_letter')
                ->where('name', 'ST1')
                ->value('id');
        }

        // Create the warning letter
        return WarningLetter::create([
            'user_id' => $userId,
            'maker_id' => $makerId,
            'type_id' => $typeId,
            'warning_letter_number' => 'new/' . $date->format('Y/m') . '/' . $userId,
            'reason_warning' => 'Auto-generated ST warning letter',
            'created_at' => $date,
            'updated_at' => $date,
            'expired_at' => (clone $date)->addMonths(3) // 3-month expiration
        ]);
    }

    /**
     * Determine the appropriate ST type (ST1, ST2) based on user's warning letter history
     */
    private function determineSTType($userId, $date)
    {
        // Count how many ST warning letters (type_id 2 or 3) this user has received before the given date
        $stCount = WarningLetter::where('user_id', $userId)
            ->whereIn('type_id', [2, 3])
            ->where('created_at', '<', $date)
            ->count();

        // Cycle between ST1 (type_id = 2) and ST2 (type_id = 3)
        return $stCount % 2 === 0 ? 'ST1' : 'ST2';
    }
}
