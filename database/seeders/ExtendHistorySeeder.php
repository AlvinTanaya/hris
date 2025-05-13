<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\users_work_experience;
use App\Models\users_education;
use App\Models\users_family;
use App\Models\users_language;
use App\Models\users_training;
use App\Models\users_organization;
use App\Models\history_extend_employee;
use App\Models\history_transfer_employee;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Faker\Factory as Faker;
class ExtendHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        // Get contract and part-time employees
        $contractUsers = User::whereIn('employee_status', ['Contract', 'Part Time'])->where('id' , '>', '51')->get();
        
        foreach ($contractUsers as $user) {
            // 70% chance of having an extension history
            if (rand(1, 100) <= 70) {
                // Original contract dates
                $startDate = Carbon::parse($user->contract_start_date);
                $endDate = Carbon::parse($user->contract_end_date);
                $contractDuration = $startDate->diffInMonths($endDate);
                
                // Create previous contract that ended before the current one started
                $prevEndDate = $startDate->copy()->subDay();
                $prevStartDate = $prevEndDate->copy()->subMonths(rand(3, 6));
                
                history_extend_employee::create([
                    'users_id' => $user->id,
                    'position_id' => $user->position_id,
                    'department_id' => $user->department_id,
                    'reason' => $faker->sentence,
                    'start_date' => $prevStartDate->format('Y-m-d'),
                    'end_date' => $prevEndDate->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // Some users might have multiple extensions
                if (rand(1, 100) <= 30) {
                    $earlierEndDate = $prevStartDate->copy()->subDay();
                    $earlierStartDate = $earlierEndDate->copy()->subMonths(rand(3, 6));
                    
                    history_extend_employee::create([
                        'users_id' => $user->id,
                        'position_id' => $user->position_id,
                        'department_id' => $user->department_id,
                        'reason' => $faker->sentence,
                        'start_date' => $earlierStartDate->format('Y-m-d'),
                        'end_date' => $earlierEndDate->format('Y-m-d'),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }
}