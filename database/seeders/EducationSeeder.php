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

class EducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $users = User::where('id' , '>', '51')->get();
        
        foreach ($users as $user) {
            // Everyone has at least high school
            $birthYear = Carbon::parse($user->birth_date)->year;
            
            // High school (SMA/SMK)
            $hsType = $faker->randomElement(['SMA', 'SMK']);
            $hsStart = $birthYear + 15;
            $hsEnd = $hsStart + 3;
            
            users_education::create([
                'users_id' => $user->id,
                'degree' => $hsType,
                'educational_place' => $hsType . ' Negeri ' . $faker->numberBetween(1, 20) . ' ' . $faker->city,
                'educational_city' => 'Kota Surabaya',
                'educational_province' => 'Jawa Timur',
                'start_education' => $hsStart . '-07-01',
                'end_education' => $hsEnd . '-06-30',
                'grade' => $faker->numberBetween(75, 100),
                'major' => $hsType === 'SMK' ? $faker->randomElement(['Teknik Komputer dan Jaringan', 'Akuntansi', 'Multimedia', 'Administrasi Perkantoran']) : $faker->randomElement(['IPA', 'IPS']),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Some have college degrees
            if (rand(1, 100) <= 70) {
                // S1 degree
                $s1Start = $hsEnd;
                $s1End = $s1Start + 4;
                
                users_education::create([
                    'users_id' => $user->id,
                    'degree' => 'S1',
                    'educational_place' => 'Universitas ' . $faker->city,
                    'educational_city' => 'Kota Surabaya',
                    'educational_province' => 'Jawa Timur',
                    'start_education' => $s1Start . '-09-01',
                    'end_education' => $s1End . '-08-30',
                    'grade' => $faker->randomFloat(2, 2.75, 4.0),
                    'major' => $faker->randomElement(['Teknik Informatika', 'Akuntansi', 'Manajemen', 'Hukum', 'Ilmu Komunikasi']),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // Few have masters degrees
                if (rand(1, 100) <= 20) {
                    // S2 degree
                    $s2Start = $s1End + rand(0, 3);
                    $s2End = $s2Start + 2;
                    
                    users_education::create([
                        'users_id' => $user->id,
                        'degree' => 'S2',
                        'educational_place' => 'Universitas ' . $faker->city,
                        'educational_city' => 'Kota Surabaya',
                        'educational_province' => 'Jawa Timur',
                        'start_education' => $s2Start . '-09-01',
                        'end_education' => $s2End . '-08-30',
                        'grade' => $faker->randomFloat(2, 3.0, 4.0),
                        'major' => $faker->randomElement(['Magister Manajemen', 'Magister Teknologi Informasi', 'Magister Akuntansi', 'Magister Hukum Bisnis']),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }
}