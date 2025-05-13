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
class TransferHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $users = User::where('id' , '>', '51')->get();
        
        // Types of transfers
        $transferTypes = ['Penetapan', 'Mutasi', 'Promosi', 'Demosi', 'Resign'];
        
        foreach ($users as $user) {
            // 40% chance of having a transfer history
            if (rand(1, 100) <= 40) {
                $joinDate = Carbon::parse($user->join_date);
                
                // Number of transfers (1-2)
                $transferCount = rand(1, 2);
                
                for ($i = 0; $i < $transferCount; $i++) {
                    // Transfer should happen after join date
                    $transferDate = $joinDate->copy()->addMonths(rand(6, 24));
                    
                    // If user is inactive, transfer should be before exit date
                    if ($user->employee_status === 'Inactive' && $user->exit_date) {
                        $exitDate = Carbon::parse($user->exit_date);
                        if ($transferDate->gt($exitDate)) {
                            $transferDate = $exitDate->copy()->subMonths(rand(1, 6));
                        }
                    }
                    
                    // Determine transfer type
                    $transferType = $faker->randomElement($transferTypes);
                    
                    // Special case for 'Penetapan' - change from contract to full time
                    if ($transferType === 'Penetapan' && $user->employee_status === 'Full Time') {
                        // For full-time employees, this represents their transition from contract
                        $oldPositionId = $user->position_id;
                        $oldDepartmentId = $user->department_id;
                        $newPositionId = $oldPositionId;
                        $newDepartmentId = $oldDepartmentId;
                    }
                    // Special case for 'Resign' - only for inactive employees
                    elseif ($transferType === 'Resign') {
                        if ($user->employee_status !== 'Inactive') {
                            // Skip this iteration if it's a resign but user is not inactive
                            continue;
                        }
                        $oldPositionId = $user->position_id;
                        $oldDepartmentId = $user->department_id;
                        $newPositionId = $oldPositionId;
                        $newDepartmentId = $oldDepartmentId;
                    }
                    // Regular transfers
                    else {
                        // Generate transfer details based on type
                        switch ($transferType) {
                            case 'Promosi':
                                // Promotion moves up a rank
                                $oldPositionId = $user->position_id < 5 ? $user->position_id + 1 : 5;
                                $newPositionId = $user->position_id;
                                $oldDepartmentId = $user->department_id;
                                $newDepartmentId = $oldDepartmentId;
                                break;
                                
                            case 'Demosi':
                                // Demotion moves down a rank
                                $oldPositionId = $user->position_id > 3 ? $user->position_id - 1 : 3;
                                $newPositionId = $user->position_id;
                                $oldDepartmentId = $user->department_id;
                                $newDepartmentId = $oldDepartmentId;
                                break;
                                
                            case 'Mutasi':
                                // Mutation changes department but keeps position
                                $oldPositionId = $user->position_id;
                                $newPositionId = $oldPositionId;
                                $oldDepartmentId = $user->department_id;
                                
                                // Pick a different department from the available list
                                $availableDepartments = [4, 5, 6, 7, 8, 9, 10, 11, 12, 13];
                                $availableDepartments = array_diff($availableDepartments, [$oldDepartmentId]);
                                $newDepartmentId = $faker->randomElement($availableDepartments);
                                break;
                                
                            default:
                                $oldPositionId = $user->position_id;
                                $newPositionId = $oldPositionId;
                                $oldDepartmentId = $user->department_id;
                                $newDepartmentId = $oldDepartmentId;
                                break;
                        }
                    }
                    
                    history_transfer_employee::create([
                        'users_id' => $user->id,
                        'old_position_id' => $oldPositionId,
                        'old_department_id' => $oldDepartmentId,
                        'new_position_id' => $newPositionId,
                        'new_department_id' => $newDepartmentId,
                        'transfer_type' => $transferType,
                        'reason' => match($transferType) {
                            'Penetapan' => 'Kontrak berakhir dan diangkat menjadi karyawan tetap',
                            'Mutasi' => 'Kebutuhan operasional di departemen baru',
                            'Promosi' => 'Kinerja memuaskan dan pengembangan karir',
                            'Demosi' => 'Penyesuaian struktur organisasi dan kinerja',
                            'Resign' => 'Mengundurkan diri atas permintaan sendiri',
                            default => $faker->sentence()
                        },
                        'created_at' => $transferDate,
                        'updated_at' => $transferDate
                    ]);
                    
                    // Update join date for next iteration (if any)
                    $joinDate = $transferDate;
                }
            }
        }
    }
}