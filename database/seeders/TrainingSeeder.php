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
class TrainingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $users = User::where('id' , '>', '51')->get();
        
        $availableTrainings = [
            'Microsoft Office Specialist',
            'Digital Marketing Workshop',
            'Leadership Training',
            'Project Management Professional',
            'Customer Service Excellence',
            'Business Communication Skills',
            'Time Management Workshop',
            'Teamwork and Collaboration',
            'Problem Solving and Decision Making',
            'Financial Literacy for Non-Finance Professionals',
            'ISO 9001 Internal Auditor Training',
            'Workplace Safety Training',
            'Data Analysis with Excel',
            'Effective Presentation Skills',
            'Emotional Intelligence in the Workplace'
        ];
        
        foreach ($users as $user) {
            // Generate 0-3 trainings for each user
            $trainingCount = rand(0, 3);
            
            // Get user join date
            $joinDate = Carbon::parse($user->join_date);
            
            for ($i = 0; $i < $trainingCount; $i++) {
                // Training should be before join date or during employment
                $randomMonths = rand(-36, 24); // Training could be up to 3 years before joining or 2 years after
                $startDate = $joinDate->copy()->addMonths($randomMonths);
                
                // If user is inactive, training should be before exit date
                if ($user->employee_status === 'Inactive' && $user->exit_date) {
                    $exitDate = Carbon::parse($user->exit_date);
                    if ($startDate->gt($exitDate)) {
                        $startDate = $exitDate->copy()->subMonths(rand(1, 6));
                    }
                }
                
                $endDate = $startDate->copy()->addDays(rand(1, 5));
                
                users_training::create([
                    'users_id' => $user->id,
                    'training_name' => $faker->randomElement($availableTrainings),
                    'training_city' => 'Kota Surabaya',
                    'training_province' => 'Jawa Timur',
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
