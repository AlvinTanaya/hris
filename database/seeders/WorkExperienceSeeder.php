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


class WorkExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $users = User::all();
        
        foreach ($users as $user) {
            // Generate 1-3 work experiences for each user
            $workExperienceCount = rand(1, 3);
            
            $lastEndDate = Carbon::parse($user->join_date)->subMonth(1);
            
            for ($i = 0; $i < $workExperienceCount; $i++) {
                $startWorking = Carbon::parse($lastEndDate)->subYears(rand(1, 3));
                $endWorking = $lastEndDate;
                
                users_work_experience::create([
                    'users_id' => $user->id,
                    'company_name' => $faker->company,
                    'position' => $faker->jobTitle,
                    'start_working' => $startWorking->format('Y-m-d'),
                    'end_working' => $endWorking->format('Y-m-d'),
                    'company_address' => $faker->address,
                    'company_phone' => $faker->phoneNumber,
                    'salary' => $faker->numberBetween(3000000, 15000000),
                    'supervisor_name' => $faker->name,
                    'supervisor_phone' => $faker->phoneNumber,
                    'job_desc' => $faker->paragraph,
                    'reason' => $faker->sentence,
                    'benefit' => $faker->sentence,
                    'facility' => $faker->sentence,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $lastEndDate = $startWorking->subMonth(1);
            }
        }
    }
}
