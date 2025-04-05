<?php

namespace Database\Seeders;

use App\Models\DisciplineRule;
use Illuminate\Database\Seeder;

class DisciplineRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing rules
        DisciplineRule::truncate();
        
        // Attendance percentage rules
        DisciplineRule::create([
            'rule_type' => 'attendance',
            'category' => 'attendance_percentage',
            'min_value' => 100,
            'max_value' => 100,
            'score_value' => 8,
            'operation' => 'set',
        ]);

        DisciplineRule::create([
            'rule_type' => 'attendance',
            'category' => 'attendance_percentage',
            'min_value' => 95,
            'max_value' => 99,
            'score_value' => 7,
            'operation' => 'set',
        ]);

        DisciplineRule::create([
            'rule_type' => 'attendance',
            'category' => 'attendance_percentage',
            'min_value' => 80,
            'max_value' => 94,
            'score_value' => 6,
            'operation' => 'set',
        ]);

        DisciplineRule::create([
            'rule_type' => 'attendance',
            'category' => 'attendance_percentage',
            'min_value' => null,
            'max_value' => 79,
            'score_value' => 5,
            'operation' => 'set',
        ]);

        // Late rules
        DisciplineRule::create([
            'rule_type' => 'late',
            'category' => 'lateness',
            'occurrence' => 1,
            'score_value' => 1,
            'operation' => 'subtract',
        ]);

        DisciplineRule::create([
            'rule_type' => 'late',
            'category' => 'lateness',
            'occurrence' => 3,
            'score_value' => 3,
            'operation' => 'subtract',
        ]);

        // Afternoon shift rules
        DisciplineRule::create([
            'rule_type' => 'afternoon_shift',
            'category' => 'afternoon_shift',
            'occurrence' => 1,
            'score_value' => 0.5,
            'operation' => 'subtract',
        ]);

        // Early leave rules
        DisciplineRule::create([
            'rule_type' => 'early_leave',
            'category' => 'early_leave',
            'occurrence' => 1,
            'score_value' => 0.5,
            'operation' => 'subtract',
        ]);

        // ST rules
        DisciplineRule::create([
            'rule_type' => 'st',
            'category' => 'sick_time',
            'occurrence' => 1,
            'score_value' => 2,
            'operation' => 'subtract',
        ]);

        // SP rules
        DisciplineRule::create([
            'rule_type' => 'sp',
            'category' => 'warning_letter',
            'occurrence' => 1,
            'score_value' => 10,
            'operation' => 'subtract',
        ]);
    }
}
