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

class UserSeeder extends Seeder
{
    // Track employee counters per year-month
    private $employeeCounters = [];
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        // Create users for different departments except Director, GM, and HR
        $departments = [4, 5, 6, 7, 8, 9, 10, 11, 12, 13]; // Skip Director, GM, HR departments
        
        try {
            // Pre-initialize counters from existing database records
            $this->initializeCounters();
            
            DB::beginTransaction();
            
            foreach ($departments as $department_id) {
                $this->logger("Creating employees for department #{$department_id}");
                
                // Create Manager for each department
                $manager = $this->createEmployee($faker, $department_id, 3, 'Full Time');
                User::create($manager);
                
                // Create Supervisor for each department
                $supervisor = $this->createEmployee($faker, $department_id, 4, 'Full Time');
                User::create($supervisor);
                
                // Create multiple Staff for each department
                $staffCount = rand(3, 5);
                for ($i = 0; $i < $staffCount; $i++) {
                    $employeeStatus = $this->getRandomEmployeeStatus();
                    $staff = $this->createEmployee($faker, $department_id, 5, $employeeStatus);
                    User::create($staff);
                }
                
                $this->logger("Created {$staffCount} staff members for department #{$department_id}");
            }
            
            // Add a few inactive employees
            $this->logger("Creating inactive employees...");
            for ($i = 0; $i < 3; $i++) {
                $randomDept = $faker->randomElement($departments);
                $inactive = $this->createEmployee($faker, $randomDept, 5, 'Inactive');
                User::create($inactive);
            }
            
            DB::commit();
            $this->logger("Seeding completed successfully!");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logger("Error during seeding: " . $e->getMessage());
            throw $e; // Re-throw to show in console
        }
    }
    
    /**
     * Initialize the employee counters from database records
     */
    private function initializeCounters()
    {
        // Check if users table has any records
        if (DB::table('users')->count() == 0) {
            return; // No records, nothing to initialize
        }
        
        // Get all distinct year-month and find max numbers - handle MariaDB compatibility
        try {
            // Try first with simpler SQL that should work on MariaDB
            $existingEmployees = DB::select("
                SELECT 
                    SUBSTRING(employee_id, 1, 6) as year_month,
                    MAX(CAST(SUBSTRING(employee_id, 7, 3) AS UNSIGNED)) as max_number
                FROM users
                GROUP BY SUBSTRING(employee_id, 1, 6)
            ");
            
            foreach ($existingEmployees as $record) {
                $this->employeeCounters[$record->year_month] = $record->max_number;
            }
        } catch (\Exception $e) {
            // In case of error, fall back to processing records individually
            $this->logger("Failed to initialize counters in bulk: " . $e->getMessage());
            
            try {
                // Get all employee IDs 
                $employeeIds = DB::table('users')->select('employee_id')->get();
                
                // Process each ID individually
                foreach ($employeeIds as $record) {
                    if (preg_match('/^(\d{6})(\d{3})$/', $record->employee_id, $matches)) {
                        $yearMonth = $matches[1];
                        $number = intval($matches[2]);
                        
                        // Update the counter if this number is higher than what we've seen
                        if (!isset($this->employeeCounters[$yearMonth]) || $number > $this->employeeCounters[$yearMonth]) {
                            $this->employeeCounters[$yearMonth] = $number;
                        }
                    }
                }
            } catch (\Exception $e2) {
                // Last resort - just log the error and continue with empty counters
                $this->logger("Failed to process employee IDs individually: " . $e2->getMessage());
            }
        }
    }
    
    /**
     * Simple logger function
     */
    private function logger($message)
    {
        // Log to Laravel log or just output for seeder
        echo $message . PHP_EOL;
    }
    
    /**
     * Generate a unique employee ID for a given join date
     */
    private function generateEmployeeId($joinDate)
    {
        $yearMonth = $joinDate->format('Ym');
        
        // If counter doesn't exist for this year-month, initialize it
        if (!isset($this->employeeCounters[$yearMonth])) {
            $this->employeeCounters[$yearMonth] = 0;
        }
        
        // Increment the counter
        $this->employeeCounters[$yearMonth]++;
        
        // Double-check that this ID doesn't already exist
        $employeeId = $yearMonth . str_pad($this->employeeCounters[$yearMonth], 3, '0', STR_PAD_LEFT);
        
        // For extra safety, check if this ID already exists
        $maxAttempts = 10;
        $attempt = 0;
        
        while ($attempt < $maxAttempts) {
            if (!DB::table('users')->where('employee_id', $employeeId)->exists()) {
                // ID is unique, we can use it
                return $employeeId;
            }
            
            // ID already exists, increment counter and try again
            $this->employeeCounters[$yearMonth]++;
            $employeeId = $yearMonth . str_pad($this->employeeCounters[$yearMonth], 3, '0', STR_PAD_LEFT);
            $attempt++;
        }
        
        // If we get here, we've tried too many times - there's a bigger issue
        throw new \Exception("Failed to generate unique employee ID after {$maxAttempts} attempts for year/month {$yearMonth}");
    }
    
    /**
     * Create an employee record with the given parameters
     */
    private function createEmployee($faker, $department_id, $position_id, $employeeStatus)
    {
        // Generate a random join date in the past 5 years
        $joinDate = Carbon::now()->subMonths(rand(1, 60));
        
        // Generate unique employee ID
        $employeeId = $this->generateEmployeeId($joinDate);
        
        // Generate name and create password
        $name = $faker->name;
        $password = Hash::make(strtolower(str_replace(' ', '', $name)) . '12345');
        
        // Set contract dates if required
        $contractStartDate = null;
        $contractEndDate = null;
        if (in_array($employeeStatus, ['Contract', 'Part Time'])) {
            $contractStartDate = $joinDate->format('Y-m-d');
            $contractEndDate = $joinDate->copy()->addMonths(rand(6, 12))->format('Y-m-d');
        }
        
        // Set exit date if inactive
        $exitDate = null;
        if ($employeeStatus === 'Inactive') {
            $exitDate = $joinDate->copy()->addMonths(rand(1, 36))->format('Y-m-d');
        }
        
        // Generate phone number
        $phoneNumber = '08' . $faker->numberBetween(1, 9) . $faker->numerify('##########');
        
        // Generate sim data
        $simTypes = $faker->randomElements(['A', 'B', 'C'], $faker->numberBetween(1, 3));
        $simNumber = [];
        foreach ($simTypes as $type) {
            $simNumber[$type] = $faker->numerify('####-###-####');
        }
        
        // Generate bank data
        $bankCount = $faker->numberBetween(1, 2);
        $bankNames = [];
        $bankNumbers = [];
        
        $availableBanks = ['Bank Central Asia (BCA)', 'Bank Mandiri', 'Bank Rakyat Indonesia (BRI)', 'Bank Negara Indonesia (BNI)', 'CIMB Niaga'];
        
        for ($i = 0; $i < $bankCount; $i++) {
            $bankNames[] = $faker->randomElement($availableBanks);
            $bankNumbers[] = $faker->numerify('##############');
        }
        
        return [
            'employee_id' => $employeeId,
            'position_id' => $position_id,
            'department_id' => $department_id,
            'name' => $name,
            'email' => $faker->unique()->safeEmail,
            'phone_number' => $phoneNumber,
            'employee_status' => $employeeStatus,
            'contract_start_date' => $contractStartDate,
            'contract_end_date' => $contractEndDate,
            'user_status' => $faker->randomElement(['Unbanned', 'Unbanned', 'Unbanned', 'Unbanned', 'Banned']),
            'join_date' => $joinDate->format('Y-m-d'),
            'ID_number' => $faker->numerify('################'),
            'birth_date' => $faker->dateTimeBetween('-45 years', '-20 years')->format('Y-m-d'),
            'birth_place' => $faker->city,
            'religion' => $faker->randomElement(['Islam', 'Katholik', 'Kristen', 'Buddha', 'Hindu']),
            'gender' => $faker->randomElement(['Male', 'Female']),
            'ID_address' => $faker->address,
            'domicile_address' => $faker->address,
            'height' => $faker->numberBetween(150, 190),
            'weight' => $faker->numberBetween(45, 90),
            'blood_type' => $faker->randomElement(['A', 'B', 'AB', 'O']),
            'bpjs_employment' => $faker->numerify('##########'),
            'bpjs_health' => $faker->numerify('##########'),
            'sim' => implode(',', $simTypes),
            'sim_number' => json_encode($simNumber),
            'password' => $password,
            'NPWP' => $faker->numerify('###.###.###.#-###.###'),
            'bank_number' => json_encode($bankNumbers),
            'bank_name' => json_encode($bankNames),
            'emergency_contact' => '08' . $faker->numberBetween(1, 9) . $faker->numerify('#########'),
            'status' => $faker->randomElement(['TK/0', 'TK/1', 'TK/2', 'TK/3', 'K/1', 'K/2', 'K/3']),
            'distance' => $faker->randomFloat(2, 0, 20),
            'exit_date' => $exitDate,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
    
    /**
     * Get a random employee status with weighted distribution
     */
    private function getRandomEmployeeStatus()
    {
        $rand = rand(1, 100);
        
        if ($rand <= 60) {
            return 'Full Time';
        } elseif ($rand <= 90) {
            return 'Contract';
        } else {
            return 'Part Time';
        }
    }
}