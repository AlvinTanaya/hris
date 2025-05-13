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
class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $users = User::where('id' , '>', '51')->get();
        
        foreach ($users as $user) {
            // Every user knows Indonesian
            users_language::create([
                'users_id' => $user->id,
                'language' => 'Indonesian',
                'verbal' => 'Active',
                'written' => 'Active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Most know some English
            if (rand(1, 100) <= 90) {
                users_language::create([
                    'users_id' => $user->id,
                    'language' => 'English',
                    'verbal' => $faker->randomElement(['Active', 'Passive']),
                    'written' => $faker->randomElement(['Active', 'Passive']),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            // Few know Mandarin
            if (rand(1, 100) <= 15) {
                users_language::create([
                    'users_id' => $user->id,
                    'language' => 'Mandarin',
                    'verbal' => $faker->randomElement(['Active', 'Passive']),
                    'written' => $faker->randomElement(['Active', 'Passive']),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
