<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class RecruitmentApplicantSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        // Religions, blood types, etc.
        $religions = ['Islam', 'Kristen', 'Katholik', 'Buddha', 'Hindu'];
        $bloodTypes = ['A', 'B', 'AB', 'O'];
        $genders = ['Male', 'Female'];
        $simTypes = [null, 'A', 'C', 'A,C', 'A,B'];
        $degrees = ['SMA', 'SMK', 'S1', 'S2'];
        $languages = ['English', 'Mandarin', 'Indonesian'];
        $proficiency = ['Active', 'Passive'];
        $relations = ['Father', 'Mother', 'Wife', 'Husband', 'Child'];

        // Helper function to safely create date ranges
        $safeDate = function($from, $to) use ($faker) {
            $fromDate = $from instanceof \DateTime ? $from : new \DateTime($from);
            $toDate = $to instanceof \DateTime ? $to : new \DateTime($to);
            
            // Ensure the 'to' date is after the 'from' date
            if ($fromDate >= $toDate) {
                return $fromDate;
            }
            
            return $faker->dateTimeBetween($fromDate, $toDate);
        };

        for ($i = 0; $i < 50; $i++) {
            // Random SIM data
            $hasSim = $faker->boolean(70);
            $simType = $hasSim ? $faker->randomElement($simTypes) : null;
            $simNumber = null;
            
            if ($simType) {
                $simParts = explode(',', $simType);
                $simData = [];
                foreach ($simParts as $type) {
                    $simData[trim($type)] = $faker->numerify('####-###-####');
                }
                $simNumber = json_encode($simData);
            }

            // Generate birth date between 40 and 20 years ago
            $birthDate = $safeDate('-40 years', '-20 years');

            // Insert main applicant
            $applicantId = DB::table('recruitment_applicant')->insertGetId([
                'recruitment_demand_id' => 12,
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone_number' => '08' . $faker->numerify('##########'),
                'ID_number' => $faker->numerify('################'),
                'birth_date' => $birthDate->format('Y-m-d'),
                'birth_place' => $faker->city,
                'religion' => $faker->randomElement($religions),
                'gender' => $faker->randomElement($genders),
                'ID_address' => $faker->address,
                'domicile_address' => $faker->address,
                'height' => $faker->numberBetween(150, 190),
                'weight' => $faker->numberBetween(40, 90),
                'blood_type' => $faker->randomElement($bloodTypes),
                'bpjs_employment' => $faker->numerify('##########'),
                'bpjs_health' => $faker->numerify('##########'),
                'sim' => $simType,
                'sim_number' => $simNumber,
                'emergency_contact' => '08' . $faker->numerify('##########'),
                'expected_salary' => $faker->numberBetween(5000000, 15000000),
                'expected_facility' => $faker->randomElement(['Health insurance', 'Transport allowance', 'Meal allowance', 'Flexible hours']),
                'expected_benefit' => $faker->randomElement(['Annual bonus', 'Performance bonus', 'Training opportunities', 'Career development']),
                'status_applicant' => 'Pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Earliest possible start date (18 years after birth)
            $careerStartDate = (clone $birthDate)->modify('+18 years');
            $now = new \DateTime();

            // Ensure careerStartDate is not in the future
            if ($careerStartDate > $now) {
                $careerStartDate = (clone $now)->modify('-5 years');
            }

            // Work experiences (0-3 per applicant)
            $workCount = $faker->numberBetween(0, 3);
            $workStartDate = $careerStartDate;

            for ($j = 0; $j < $workCount; $j++) {
                // Safe date ranges
                $workEndDate = $safeDate($workStartDate, 'now');
                
                DB::table('recruitment_applicant_work_experience')->insert([
                    'applicant_id' => $applicantId,
                    'company_name' => $faker->company,
                    'position' => $faker->jobTitle,
                    'working_start' => $workStartDate->format('Y-m-d'),
                    'working_end' => $workEndDate->format('Y-m-d'),
                    'company_address' => $faker->address,
                    'company_phone' => '021' . $faker->numerify('#######'),
                    'salary' => $faker->numberBetween(3000000, 10000000),
                    'supervisor_name' => $faker->name,
                    'supervisor_phone' => '08' . $faker->numerify('##########'),
                    'job_desc' => $faker->paragraph,
                    'reason' => $faker->randomElement(['Career advancement', 'Better opportunity', 'Personal reasons', 'Company closure']),
                    'benefit' => $faker->randomElement(['Health insurance', 'Performance bonus', 'Training']),
                    'facility' => $faker->randomElement(['Laptop', 'Company car', 'Phone allowance']),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                
                // Next job starts after the previous one ended
                $workStartDate = (clone $workEndDate)->modify('+1 day');
                
                // Stop if we've reached current date
                if ($workStartDate >= $now) {
                    break;
                }
            }

            // Education (1-3 per applicant)
            $eduCount = min($faker->numberBetween(1, 3), count($degrees));
            
            // Start education at 6 years after birth
            $eduStartDate = (clone $birthDate)->modify('+6 years');
            
            for ($j = 0; $j < $eduCount; $j++) {
                $degree = $degrees[$j];
                
                $duration = match($degree) {
                    'SMA', 'SMK' => 3,
                    'S1' => 4,
                    'S2' => 2,
                    default => 4
                };
                
                // Calculate end date based on start date and duration
                $eduEndDate = (clone $eduStartDate)->modify("+$duration years");
                
                DB::table('recruitment_applicant_education')->insert([
                    'applicant_id' => $applicantId,
                    'degree' => $degree,
                    'educational_place' => $degree === 'SMA' || $degree === 'SMK' 
                        ? $faker->randomElement(['SMA Negeri 1', 'SMA Negeri 2', 'SMK Negeri 1']) . ' ' . $faker->city
                        : $faker->randomElement(['Universitas Indonesia', 'Universitas Gadjah Mada', 'Institut Teknologi Bandung', 'Universitas Airlangga']),
                    'educational_city' => 'Kota Surabaya',
                    'educational_province' => 'Jawa Timur',
                    'start_education' => $eduStartDate->format('Y-m-d'),
                    'end_education' => $eduEndDate->format('Y-m-d'),
                    'grade' => $degree === 'SMA' || $degree === 'SMK' 
                        ? $faker->numberBetween(70, 100)
                        : $faker->randomFloat(1, 2.0, 4.0),
                    'major' => $faker->randomElement([
                        'IPA', 'IPS', 'Akuntansi', 'Teknik Komputer', 
                        'Manajemen', 'Marketing', 'Teknik Informatika'
                    ]),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                
                // Next education starts after the previous one
                $eduStartDate = (clone $eduEndDate)->modify('+6 months');
                
                // Stop if we've reached current date
                if ($eduStartDate >= $now) {
                    break;
                }
            }

            // Family members (1-5 per applicant)
            $familyCount = min($faker->numberBetween(1, 5), count($relations));
            $shuffledRelations = $faker->randomElements($relations, $familyCount);
            
            foreach ($shuffledRelations as $relation) {
                DB::table('recruitment_applicant_family')->insert([
                    'applicant_id' => $applicantId,
                    'name' => $faker->name,
                    'relation' => $relation,
                    'phone_number' => $relation === 'Child' && $faker->boolean(50) ? null : '08' . $faker->numerify('##########'),
                    'gender' => $relation === 'Father' || $relation === 'Husband' ? 'Male' : 
                               ($relation === 'Mother' || $relation === 'Wife' ? 'Female' : 
                                $faker->randomElement($genders)),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            // Languages (1-3 per applicant)
            $languageCount = min($faker->numberBetween(1, 3), count($languages));
            $shuffledLanguages = $faker->randomElements($languages, $languageCount);
            
            foreach ($shuffledLanguages as $language) {
                DB::table('recruitment_applicant_language')->insert([
                    'applicant_id' => $applicantId,
                    'language' => $language,
                    'verbal' => $faker->randomElement($proficiency),
                    'written' => $faker->randomElement($proficiency),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            // Training (0-4 per applicant)
            $trainingCount = $faker->numberBetween(0, 4);
            $trainingStartDate = $careerStartDate;
            
            for ($j = 0; $j < $trainingCount; $j++) {
                // Training typically lasts 1-30 days
                $trainingEndDate = (clone $trainingStartDate)->modify('+' . $faker->numberBetween(1, 30) . ' days');
                
                // Ensure end date is not in the future
                if ($trainingEndDate > $now) {
                    $trainingEndDate = clone $now;
                }
                
                DB::table('recruitment_applicant_training')->insert([
                    'applicant_id' => $applicantId,
                    'training_name' => $faker->randomElement(['Digital Marketing', 'Leadership', 'HR Professional', 'IT Security']) . ' ' . $faker->randomElement(['Certification', 'Workshop', 'Training']),
                    'training_city' => 'Jawa Timur',
                    'training_province' => 'Kota Surabaya',
                    'start_date' => $trainingStartDate->format('Y-m-d'),
                    'end_date' => $trainingEndDate->format('Y-m-d'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                
                // Next training starts 1-6 months after the previous one
                $trainingStartDate = (clone $trainingEndDate)->modify('+' . $faker->numberBetween(1, 6) . ' months');
                
                // Stop if we've reached current date
                if ($trainingStartDate >= $now) {
                    break;
                }
            }

            // Organizations (0-3 per applicant)
            $orgCount = $faker->numberBetween(0, 3);
            $orgStartDate = $careerStartDate;
            
            for ($j = 0; $j < $orgCount; $j++) {
                // Some organizations might still be active (null end date)
                $hasEndDate = $faker->boolean(70);
                $orgEndDate = $hasEndDate ? (clone $orgStartDate)->modify('+' . $faker->numberBetween(1, 36) . ' months') : null;
                
                // Ensure end date is not in the future
                if ($orgEndDate !== null && $orgEndDate > $now) {
                    $orgEndDate = clone $now;
                }
                
                DB::table('recruitment_applicant_organization')->insert([
                    'applicant_id' => $applicantId,
                    'organization_name' => $faker->randomElement(['Ikatan Alumni', 'Himpunan Mahasiswa', 'Komunitas']) . ' ' . $faker->word,
                    'activity_type' => $faker->randomElement(['Professional', 'Student', 'Community']),
                    'position' => $faker->randomElement(['Member', 'Secretary', 'Treasurer', 'Chairman']),
                    'city' => 'Kota Surabaya',
                    'province' => 'Jawa Timur',
                    'start_date' => $orgStartDate->format('Y-m-d'),
                    'end_date' => $orgEndDate ? $orgEndDate->format('Y-m-d') : null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                
                // Next org starts 1-6 months after the previous one
                $nextOrgStart = $orgEndDate ?? $orgStartDate;
                $orgStartDate = (clone $nextOrgStart)->modify('+' . $faker->numberBetween(1, 6) . ' months');
                
                // Stop if we've reached current date
                if ($orgStartDate >= $now) {
                    break;
                }
            }
        }
    }
}