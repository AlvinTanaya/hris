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
class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $users = User::where('id' , '>', '51')->get();
        
        $availableOrganizations = [
            'Himpunan Mahasiswa Jurusan',
            'Badan Eksekutif Mahasiswa',
            'Unit Kegiatan Mahasiswa',
            'Komunitas Pecinta Alam',
            'Ikatan Alumni',
            'Persatuan Olah Raga',
            'Organisasi Sosial Kemasyarakatan',
            'Komunitas Seni dan Budaya',
            'Karang Taruna',
            'Palang Merah Indonesia',
            'Perkumpulan Profesi',
            'Rotary Club',
            'Lions Club',
            'Komunitas Hobi'
        ];
        
        $availablePositions = [
            'Ketua',
            'Wakil Ketua',
            'Sekretaris',
            'Bendahara',
            'Koordinator Divisi',
            'Anggota',
            'Pengurus',
            'Relawan'
        ];
        
        $availableActivities = [
            'Kegiatan Sosial',
            'Pengembangan Profesional',
            'Kegiatan Keagamaan',
            'Kegiatan Mahasiswa',
            'Kegiatan Olahraga',
            'Kegiatan Kesenian',
            'Kegiatan Pendidikan',
            'Kegiatan Kemanusiaan',
            'Kegiatan Kepemudaan'
        ];
        
        foreach ($users as $user) {
            // 60% chance to have organizational experience
            if (rand(1, 100) <= 60) {
                // Generate 1-2 organizations for each user
                $orgCount = rand(1, 2);
                
                // Calculate user age from birth date
                $birthYear = Carbon::parse($user->birth_date)->year;
                
                for ($i = 0; $i < $orgCount; $i++) {
                    // Organization could be from high school or college age
                    $startYear = $birthYear + rand(16, 23);
                    $startDate = Carbon::createFromDate($startYear, rand(1, 12), rand(1, 28));
                    $endDate = $startDate->copy()->addYears(rand(1, 3));
                    
                    users_organization::create([
                        'users_id' => $user->id,
                        'organization_name' => $faker->randomElement($availableOrganizations),
                        'activity_type' => $faker->randomElement($availableActivities),
                        'position' => $faker->randomElement($availablePositions),
                        'city' => 'Kota Surabaya',
                        'province' => 'Jawa Timur',
                        'start_date' => $startDate->format('Y-m-d'),
                        'end_date' => $endDate->format('Y-m-d'),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }
}
