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
use Illuminate\Support\Facades\DB;

class FamilySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $users = User::where('id' , '>', '51')->get();
        
        foreach ($users as $user) {
            // Each user has parents
            $this->createFamilyMember($faker, $user->id, 'Father', 'Male');
            $this->createFamilyMember($faker, $user->id, 'Mother', 'Female');
            
            // Some users have spouse and children
            if (rand(1, 100) <= 60) {
                $spouseGender = $user->gender === 'Male' ? 'Female' : 'Male';
                $spouseRelation = $user->gender === 'Male' ? 'Wife' : 'Husband';
                $this->createFamilyMember($faker, $user->id, $spouseRelation, $spouseGender);
                
                // Some have children
                if (rand(1, 100) <= 70) {
                    $childrenCount = rand(1, 3);
                    for ($i = 0; $i < $childrenCount; $i++) {
                        $childGender = $faker->randomElement(['Male', 'Female']);
                        $this->createFamilyMember($faker, $user->id, 'Child', $childGender, true);
                    }
                }
            }
        }
    }
    
    /**
     * Create a family member for the given user
     */
    private function createFamilyMember($faker, $userId, $relation, $gender, $isChild = false)
    {
        $user = User::find($userId);
        $userBirthYear = Carbon::parse($user->birth_date)->year;
        
        // Determine birth year based on relationship
        $birthYear = match($relation) {
            'Father', 'Mother' => $userBirthYear - rand(25, 35),
            'Wife', 'Husband' => $userBirthYear - rand(-5, 5),
            'Child' => $userBirthYear + rand(20, 30),
            default => $userBirthYear
        };
        
        $birthDate = Carbon::createFromDate($birthYear, rand(1, 12), rand(1, 28))->format('Y-m-d');
        
        users_family::create([
            'users_id' => $userId,
            'name' => $faker->name($gender === 'Male' ? 'male' : 'female'),
            'relation' => $relation,
            'phone_number' => '08' . $faker->numberBetween(1, 9) . $faker->numerify('##########'),
            'gender' => $gender,
            'birth_date' => $birthDate,
            'birth_place' => $faker->city,
            'ID_number' => $faker->numerify('################'),
            'address' => $faker->address,
            'job' => $isChild && Carbon::parse($birthDate)->age < 18 ? 'Pelajar' : $faker->jobTitle,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}