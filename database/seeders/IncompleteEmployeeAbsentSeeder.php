<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\rule_shift;
use App\Models\EmployeeShift;
use App\Models\EmployeeAbsent;
use App\Models\RequestTimeOff;
use App\Models\EmployeeOvertime;
use App\Models\CustomHoliday;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class IncompleteEmployeeAbsentSeeder extends Seeder
{
    protected $nationalHolidays = [];
    protected $users = [];
    protected $usersWithAbsent = [];
    protected $usersWithoutAbsent = [];
    protected $shiftRules = [];
    protected $weekends = ['Sunday']; // Sunday is always a holiday
    protected $customHolidays = [];
    protected $existingEmployeeShifts = [];
    protected $existingTimeOffRequests = [];
    protected $existingOvertimes = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->fetchHolidays();
        $this->loadExistingData();
        $this->identifyUsersWithoutAbsent();
        
        // Only seed employee absents for users without absent records
        $this->seedEmployeeAbsents();
    }

    /**
     * Fetch holiday data from API and custom holidays
     */
    private function fetchHolidays(): void
    {
        // In a real scenario, you'd call the API 
        // For this seeder, we'll use sample holiday data
        $this->nationalHolidays = [
            '2023-01-01', '2023-01-22', '2023-02-18', '2023-03-22', '2023-04-07', 
            '2023-04-23', '2023-05-01', '2023-05-18', '2023-06-01', '2023-06-04',
            '2023-08-17', '2023-09-28', '2023-12-25',
            '2024-01-01', '2024-01-10', '2024-02-08', '2024-03-11', '2024-03-29', 
            '2024-04-10', '2024-04-11', '2024-05-01', '2024-05-23', '2024-06-01',
            '2024-08-17', '2024-09-16', '2024-12-25',
            '2025-01-01', '2025-01-29', '2025-02-27', '2025-03-30', '2025-04-18', 
            '2025-04-30', '2025-05-01', '2025-05-13', '2025-06-01', '2025-08-17',
            '2025-10-05', '2025-12-25'
        ];

        // Load custom holidays
        $this->customHolidays = CustomHoliday::pluck('date')->toArray();
    }

    /**
     * Load existing data to avoid conflicts
     */
    private function loadExistingData(): void
    {
        $this->users = User::all();
        $this->shiftRules = rule_shift::all();
        
        // Load existing data
        $this->existingEmployeeShifts = EmployeeShift::all();
        $this->existingTimeOffRequests = RequestTimeOff::where('status', 'Approved')->get();
        $this->existingOvertimes = EmployeeOvertime::where('approval_status', 'Approved')->get();
        
        // Get users who already have records in EmployeeAbsent
        $this->usersWithAbsent = EmployeeAbsent::select('user_id')
            ->distinct()
            ->pluck('user_id')
            ->toArray();
    }

    /**
     * Identify users without absent records
     */
    private function identifyUsersWithoutAbsent(): void
    {
        $this->usersWithoutAbsent = $this->users
            ->whereNotIn('id', $this->usersWithAbsent)
            ->pluck('id')
            ->toArray();
        
        // Output for logging purposes
        $this->command->info('Total users: ' . count($this->users));
        $this->command->info('Users with absent records: ' . count($this->usersWithAbsent));
        $this->command->info('Users without absent records: ' . count($this->usersWithoutAbsent));
    }

    /**
     * Seed employee absence records
     */
    private function seedEmployeeAbsents(): void
    {
        $seedCount = 0;
        $maxSeeds = 10000; // A high limit to ensure all needed records are created
        $places = ['Office', 'Branch Office', 'Home Office', 'Client Site'];
        
        $progressBar = $this->command->getOutput()->createProgressBar(count($this->usersWithoutAbsent));
        $progressBar->start();
        
        foreach ($this->usersWithoutAbsent as $userId) {
            $user = $this->users->firstWhere('id', $userId);
            
            if (!$user) {
                continue;
            }
            
            // Generate attendance records starting from join date
            $currentDate = Carbon::parse($user->join_date);
            $endDate = Carbon::now()->subDays(1); // Up to yesterday
            $userSeedCount = 0;
            
            while ($currentDate->lt($endDate) && $seedCount < $maxSeeds) {
                // Skip weekends and holidays
                if ($this->isHoliday($currentDate)) {
                    $currentDate->addDay();
                    continue;
                }
                
                // Check if user has a time-off request for this date
                $timeOffRequest = $this->existingTimeOffRequests
                    ->where('user_id', $userId)
                    ->filter(function ($request) use ($currentDate) {
                        $startDate = Carbon::parse($request->start_date)->startOfDay();
                        $endDate = Carbon::parse($request->end_date)->endOfDay();
                        return $currentDate->between($startDate, $endDate);
                    })
                    ->first();
                
                // If user has approved time-off (excluding late arrival/early departure), skip this day
                if ($timeOffRequest && !str_contains(strtolower($timeOffRequest->timeOffPolicy->time_off_name), 'masuk siang') && 
                    !str_contains(strtolower($timeOffRequest->timeOffPolicy->time_off_name), 'pulang awal')) {
                    $currentDate->addDay();
                    continue;
                }
                
                // Find the employee's shift for this date
                $shift = $this->findShiftForDate($userId, $currentDate);
                
                if (!$shift) {
                    $currentDate->addDay();
                    continue;
                }
                
                $ruleShift = $this->shiftRules->where('id', $shift->rule_id)->first();
                if (!$ruleShift) {
                    $currentDate->addDay();
                    continue;
                }
                
                // Parse hour_start and hour_end
                $dayIndex = min(5, $currentDate->dayOfWeek); // 0 = Sunday, 6 = Saturday
                if ($dayIndex == 0) { // Skip Sundays
                    $currentDate->addDay();
                    continue;
                }
                
                // Adjust for arrays that are 0-based (Monday = 0, Saturday = 5)
                $shiftIndex = $dayIndex - 1;
                if ($shiftIndex < 0) {
                    $currentDate->addDay();
                    continue;
                }
                
                $hourStartArray = json_decode($ruleShift->hour_start);
                $hourEndArray = json_decode($ruleShift->hour_end);
                
                if (!isset($hourStartArray[$shiftIndex]) || !isset($hourEndArray[$shiftIndex])) {
                    $currentDate->addDay();
                    continue;
                }
                
                $hourStart = $hourStartArray[$shiftIndex];
                $hourEnd = $hourEndArray[$shiftIndex];
                
                // Calculate check-in time (with some randomness)
                $checkInVariation = rand(-15, 30); // -15 minutes early to 30 minutes late
                $checkInTime = Carbon::parse($currentDate->format('Y-m-d') . ' ' . $hourStart)->addMinutes($checkInVariation);
                
                // Handle late arrival time-off request
                if ($timeOffRequest && str_contains(strtolower($timeOffRequest->timeOffPolicy->time_off_name), 'masuk siang')) {
                    $checkInTime = Carbon::parse($timeOffRequest->end_date);
                }
                
                // Calculate check-out time (with some randomness)
                $checkOutVariation = rand(-30, 15); // -30 minutes early to 15 minutes late
                $checkOutTime = Carbon::parse($currentDate->format('Y-m-d') . ' ' . $hourEnd)->addMinutes($checkOutVariation);
                
                // Handle early departure time-off request
                if ($timeOffRequest && str_contains(strtolower($timeOffRequest->timeOffPolicy->time_off_name), 'pulang awal')) {
                    $checkOutTime = Carbon::parse($timeOffRequest->start_date);
                }
                
                // Handle approved overtime requests
                $overtimeRequest = $this->existingOvertimes
                    ->where('user_id', $userId)
                    ->where('date', $currentDate->format('Y-m-d'))
                    ->first();
                
                if ($overtimeRequest) {
                    $checkOutTime = Carbon::parse($currentDate->format('Y-m-d') . ' ' . $overtimeRequest->end_time);
                    // Add a few minutes of randomness
                    $checkOutTime->addMinutes(rand(0, 15));
                }
                
                // Determine status
                $statusIn = ($checkInTime->format('H:i:s') <= $hourStart) ? 'early' : 'late';
                $statusOut = ($checkOutTime->format('H:i:s') >= $hourEnd) ? 'late' : 'early';
                
                // Calculate late/early minutes
                $lateMinutes = 0;
                $earlyMinutes = 0;
                
                if ($statusIn === 'late') {
                    $lateMinutes = Carbon::parse($hourStart)->diffInMinutes($checkInTime);
                }
                
                if ($statusOut === 'early') {
                    $earlyMinutes = Carbon::parse($hourEnd)->diffInMinutes($checkOutTime);
                }
                
                try {
                    EmployeeAbsent::create([
                        'user_id' => $userId,
                        'absent_place' => Arr::random($places),
                        'date' => $currentDate->format('Y-m-d'),
                        'hour_in' => $checkInTime->format('H:i:s'),
                        'hour_out' => $checkOutTime->format('H:i:s'),
                        'status_in' => $statusIn,
                        'status_out' => $statusOut,
                        'rule_in' => $hourStart,
                        'rule_out' => $hourEnd,
                        'rule_type' => $ruleShift->type,
                        'late_minutes' => $lateMinutes,
                        'early_minutes' => $earlyMinutes,
                        'created_at' => $currentDate->copy()->addHours(23),
                        'updated_at' => $currentDate->copy()->addHours(23)
                    ]);
                    
                    $seedCount++;
                    $userSeedCount++;
                } catch (\Exception $e) {
                    // Log any errors
                    $this->command->error("Error creating absent record for user $userId on {$currentDate->format('Y-m-d')}: " . $e->getMessage());
                }
                
                $currentDate->addDay();
            }
            
            // Add a variation in the number of records per user (some might have gaps in their attendance)
            if ($userSeedCount > 0 && rand(0, 100) > 95) {
                $this->command->info("Created $userSeedCount records for user $userId - skipping some days for realism");
                break;
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->command->info("\nTotal absence records created: $seedCount");
    }

    /**
     * Check if a date is a holiday (weekend or national/custom holiday)
     */
    private function isHoliday(Carbon $date): bool
    {
        // Check if it's a weekend (Sunday)
        if ($date->dayOfWeek === 0) {
            return true;
        }
        
        // Check if it's a national holiday
        $dateString = $date->format('Y-m-d');
        if (in_array($dateString, $this->nationalHolidays)) {
            return true;
        }
        
        // Check if it's a custom holiday
        if (in_array($dateString, $this->customHolidays)) {
            return true;
        }
        
        return false;
    }

    /**
     * Find the appropriate shift for a user on a specific date
     */
    private function findShiftForDate($userId, Carbon $date): ?EmployeeShift
    {
        return $this->existingEmployeeShifts
            ->where('user_id', $userId)
            ->filter(function ($shift) use ($date) {
                $startDate = Carbon::parse($shift->start_date);
                $endDate = $shift->end_date ? Carbon::parse($shift->end_date) : Carbon::now()->addYear();
                return $date->between($startDate, $endDate);
            })
            ->sortByDesc('start_date')
            ->first();
    }
}