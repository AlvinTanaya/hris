<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\rule_shift;
use App\Models\EmployeeShift;
use App\Models\EmployeeAbsent;
use App\Models\TimeOffPolicy;
use App\Models\TimeOffAssign;
use App\Models\RequestTimeOff;
use App\Models\EmployeeOvertime;
use App\Models\CustomHoliday;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class AttendanceSystemSeeder extends Seeder
{
    protected $nationalHolidays = [];
    protected $users = [];
    protected $managers = [];
    protected $directorGmHrManagers = [];
    protected $shiftRules = [];
    protected $timeOffPolicies = [];
    protected $weekends = ['Sunday']; // Sunday is always a holiday
    protected $customHolidays = [];
    protected $existingEmployeeShifts = [];
    protected $existingAbsents = [];
    protected $existingTimeOffAssigns = [];
    protected $existingTimeOffRequests = [];
    protected $existingOvertimes = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->fetchHolidays();
        $this->loadExistingData();
        $this->organizeUsers();
        
        // Seed in proper order to maintain data integrity
        $this->seedEmployeeShifts();
        $this->seedTimeOffAssigns();
        $this->seedRequestTimeOffs();
        $this->seedEmployeeOvertimes();
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
        $this->timeOffPolicies = TimeOffPolicy::all();
        
        // Load existing data to prevent duplicates
        $this->existingEmployeeShifts = EmployeeShift::all();
        $this->existingAbsents = EmployeeAbsent::all();
        $this->existingTimeOffAssigns = TimeOffAssign::all();
        $this->existingTimeOffRequests = RequestTimeOff::all();
        $this->existingOvertimes = EmployeeOvertime::all();
    }

    /**
     * Organize users by position for approval workflows
     */
    private function organizeUsers(): void
    {
        // Group managers by department (position_id = 3)
        foreach ($this->users as $user) {
            // Directors, General Managers, and HR Managers are final approvers
            if ($user->position_id == 1 || $user->position_id == 2 || 
                ($user->position_id == 3 && $user->department_id == 3)) {
                $this->directorGmHrManagers[] = $user->id;
            }
            
            // Department managers for first-level approval
            if ($user->position_id == 3) {
                if (!isset($this->managers[$user->department_id])) {
                    $this->managers[$user->department_id] = [];
                }
                $this->managers[$user->department_id][] = $user->id;
            }
        }
    }

    /**
     * Seed employee shifts
     */
    private function seedEmployeeShifts(): void
    {
        $seedCount = 0;
        $maxSeeds = 150;
        $shiftRuleIds = $this->shiftRules->pluck('id')->toArray();
        
        foreach ($this->users as $user) {
            // Skip if user already has an active shift (end_date is null)
            $existingActiveShift = $this->existingEmployeeShifts
                ->where('user_id', $user->id)
                ->where('end_date', null)
                ->first();
                
            if ($existingActiveShift) {
                continue;
            }
            
            // Create 1-3 shift history records for each user
            $shiftsPerUser = rand(1, 3);
            $startDate = Carbon::parse($user->join_date);
            
            for ($i = 0; $i < $shiftsPerUser && $seedCount < $maxSeeds; $i++) {
                // Last shift should be active (null end_date)
                $endDate = ($i == $shiftsPerUser - 1) ? null : $startDate->copy()->addMonths(rand(3, 8));
                
                EmployeeShift::create([
                    'user_id' => $user->id,
                    'rule_id' => Arr::random($shiftRuleIds),
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate ? $endDate->format('Y-m-d') : null,
                    'created_at' => $startDate->copy()->subDays(rand(1, 5)),
                    'updated_at' => $startDate->copy()->subDays(rand(0, 3))
                ]);
                
                $seedCount++;
                
                // Set next start date after the previous end date
                if ($endDate) {
                    $startDate = $endDate->addDays(1);
                }
            }
        }
        
        // Reload with newly created records
        $this->existingEmployeeShifts = EmployeeShift::all();
    }

    /**
     * Seed time off assignments
     */
    private function seedTimeOffAssigns(): void
    {
        $seedCount = 0;
        $maxSeeds = 200;
        
        foreach ($this->users as $user) {
            foreach ($this->timeOffPolicies as $policy) {
                // Skip if assignment already exists
                $exists = $this->existingTimeOffAssigns
                    ->where('user_id', $user->id)
                    ->where('time_off_id', $policy->id)
                    ->first();
                    
                if ($exists || $seedCount >= $maxSeeds) {
                    continue;
                }
                
                // Determine balance based on policy quota
                $balance = rand(0, $policy->quota);
                
                TimeOffAssign::create([
                    'user_id' => $user->id,
                    'time_off_id' => $policy->id,
                    'balance' => $balance,
                    'created_at' => Carbon::parse($user->join_date)->addDays(rand(1, 30)),
                    'updated_at' => Carbon::now()
                ]);
                
                $seedCount++;
            }
        }
        
        // Reload with newly created records
        $this->existingTimeOffAssigns = TimeOffAssign::all();
    }

    /**
     * Seed time off requests
     */
    private function seedRequestTimeOffs(): void
    {
        $seedCount = 0;
        $maxSeeds = 300;
        $statuses = ['Pending', 'Approved', 'Declined'];
        
        foreach ($this->existingTimeOffAssigns as $assign) {
            // Generate 0-5 requests per assignment
            $requestCount = rand(0, 5);
            
            for ($i = 0; $i < $requestCount && $seedCount < $maxSeeds; $i++) {
                $user = $this->users->where('id', $assign->user_id)->first();
                $policy = $this->timeOffPolicies->where('id', $assign->time_off_id)->first();
                
                if (!$user || !$policy) {
                    continue;
                }
                
                // Skip if there's not enough balance
                if ($assign->balance <= 0) {
                    continue;
                }
                
                // Generate valid dates (after join date)
                $joinDate = Carbon::parse($user->join_date);
                $requestDate = $joinDate->copy()->addDays(rand(30, 730)); // Between 1 month and 2 years after join
                
                // Skip weekends and holidays
                while ($this->isHoliday($requestDate)) {
                    $requestDate->addDay();
                }
                
                // Format dates based on whether time input is required
                $startDate = $requestDate->copy();
                $endDate = null;
                
                if ($policy->requires_time_input) {
                    // For time-specific requests (like early departure)
                    // Find the employee's shift for this date
                    $shift = $this->findShiftForDate($user->id, $startDate);
                    
                    if (!$shift) {
                        continue;
                    }
                    
                    $ruleShift = $this->shiftRules->where('id', $shift->rule_id)->first();
                    if (!$ruleShift) {
                        continue;
                    }
                    
                    // Parse hour_start and hour_end
                    $dayIndex = min(5, $startDate->dayOfWeek); // 0 = Sunday, 6 = Saturday
                    if ($dayIndex == 0) { // Skip Sundays
                        continue;
                    }
                    
                    // Adjust for arrays that are 0-based (Monday = 0, Saturday = 5)
                    $shiftIndex = $dayIndex - 1;
                    if ($shiftIndex < 0) {
                        continue;
                    }
                    
                    $hourStartArray = json_decode($ruleShift->hour_start);
                    $hourEndArray = json_decode($ruleShift->hour_end);
                    
                    if (!isset($hourStartArray[$shiftIndex]) || !isset($hourEndArray[$shiftIndex])) {
                        continue;
                    }
                    
                    $hourStart = $hourStartArray[$shiftIndex];
                    $hourEnd = $hourEndArray[$shiftIndex];
                    
                    // For early departure, set start time during the shift and end time as the regular end
                    if (str_contains(strtolower($policy->time_off_name), 'pulang awal')) {
                        $startTimeHour = Carbon::parse($hourStart)->addHours(rand(1, 3))->format('H:i');
                        $startDate = Carbon::parse($startDate->format('Y-m-d') . ' ' . $startTimeHour);
                        $endDate = Carbon::parse($startDate->format('Y-m-d') . ' ' . $hourEnd);
                    } 
                    // For late arrival, set start time as the regular start and end time during the shift
                    else if (str_contains(strtolower($policy->time_off_name), 'masuk siang')) {
                        $endTimeHour = Carbon::parse($hourEnd)->subHours(rand(1, 3))->format('H:i');
                        $startDate = Carbon::parse($startDate->format('Y-m-d') . ' ' . $hourStart);
                        $endDate = Carbon::parse($startDate->format('Y-m-d') . ' ' . $endTimeHour);
                    }
                    // For regular time off within the day
                    else {
                        $startDate = Carbon::parse($startDate->format('Y-m-d') . ' ' . $hourStart);
                        $endDate = Carbon::parse($startDate->format('Y-m-d') . ' ' . $hourEnd);
                    }
                } else {
                    // For full-day time off
                    $durationDays = min($assign->balance, rand(1, 3)); // Use up to 3 days or available balance
                    $startDate = Carbon::parse($startDate->format('Y-m-d') . ' 00:00:00');
                    $endDate = Carbon::parse($startDate->copy()->addDays($durationDays - 1)->format('Y-m-d') . ' 23:59:59');
                    
                    // Ensure we don't create time-offs on holidays
                    for ($day = 0; $day < $durationDays; $day++) {
                        $checkDate = $startDate->copy()->addDays($day);
                        if ($this->isHoliday($checkDate)) {
                            // Skip this iteration if we hit a holiday
                            continue 2;
                        }
                    }
                }
                
                // Determine the status
                $status = Arr::random($statuses);
                $deptApprovalStatus = $status;
                $adminApprovalStatus = 'Pending';
                $reasonDeclined = null;
                
                // For managers, skip dept approval
                $needsDeptApproval = $user->position_id > 3; // Supervisors and staff need dept approval
                
                // Setup approvers
                $deptApproverId = null;
                $adminApproverId = null;
                
                // Set dept approver if needed
                if ($needsDeptApproval && isset($this->managers[$user->department_id])) {
                    $deptApproverId = Arr::random($this->managers[$user->department_id]);
                    
                    // If declined at dept level, admin stays pending
                    if ($deptApprovalStatus === 'Declined') {
                        $adminApprovalStatus = 'Pending';
                        $reasonDeclined = 'Request does not meet department policy requirements.';
                    }
                } else {
                    // No dept approval needed, so it's automatically approved
                    $deptApprovalStatus = 'Approved';
                }
                
                // Set admin approver if dept approved
                if ($deptApprovalStatus === 'Approved') {
                    // For admin approval, exclude the requesting user from approvers
                    $possibleAdminApprovers = array_diff($this->directorGmHrManagers, [$user->id]);
                    
                    if (!empty($possibleAdminApprovers)) {
                        $adminApproverId = Arr::random($possibleAdminApprovers);
                        $adminApprovalStatus = $status;
                        
                        if ($adminApprovalStatus === 'Declined') {
                            $reasonDeclined = 'Request does not align with company leave policies.';
                        }
                    }
                }
                
                // Overall status follows the most negative status
                if ($deptApprovalStatus === 'Declined' || $adminApprovalStatus === 'Declined') {
                    $status = 'Declined';
                } elseif ($deptApprovalStatus === 'Pending' || $adminApprovalStatus === 'Pending') {
                    $status = 'Pending';
                } else {
                    $status = 'Approved';
                }
                
                // Create the request
                RequestTimeOff::create([
                    'user_id' => $user->id,
                    'time_off_id' => $policy->id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'reason' => $this->generateTimeOffReason($policy->time_off_name),
                    'status' => $status,
                    'declined_reason' => $reasonDeclined,
                    'dept_approval_status' => $deptApprovalStatus,
                    'dept_approval_user_id' => $deptApproverId,
                    'admin_approval_status' => $adminApprovalStatus,
                    'admin_approval_user_id' => $adminApproverId,
                    'created_at' => $startDate->copy()->subDays(rand(1, 14)),
                    'updated_at' => $startDate->copy()->subDays(rand(0, 7))
                ]);
                
                // Reduce the balance if approved
                if ($status === 'Approved') {
                    $deduction = $policy->requires_time_input ? 1 : $durationDays;
                    $assign->balance -= $deduction;
                    $assign->save();
                }
                
                $seedCount++;
            }
        }
        
        // Reload with newly created records
        $this->existingTimeOffRequests = RequestTimeOff::all();
    }

    /**
     * Seed employee overtime records
     */
    private function seedEmployeeOvertimes(): void
    {
        $seedCount = 0;
        $maxSeeds = 200;
        $statuses = ['Pending', 'Approved', 'Declined'];
        
        foreach ($this->users as $user) {
            // Generate 0-4 overtime requests per user
            $overtimeCount = rand(0, 4);
            
            for ($i = 0; $i < $overtimeCount && $seedCount < $maxSeeds; $i++) {
                // Pick a random workday after join date
                $joinDate = Carbon::parse($user->join_date);
                $requestDate = $joinDate->copy()->addDays(rand(30, 730));
                
                // Skip weekends and holidays
                while ($this->isHoliday($requestDate)) {
                    $requestDate->addDay();
                }
                
                // Find the employee's shift for this date
                $shift = $this->findShiftForDate($user->id, $requestDate);
                
                if (!$shift) {
                    continue;
                }
                
                $ruleShift = $this->shiftRules->where('id', $shift->rule_id)->first();
                if (!$ruleShift) {
                    continue;
                }
                
                // Parse hour_start and hour_end
                $dayIndex = min(5, $requestDate->dayOfWeek); // 0 = Sunday, 6 = Saturday
                if ($dayIndex == 0) { // Skip Sundays
                    continue;
                }
                
                // Adjust for arrays that are 0-based (Monday = 0, Saturday = 5)
                $shiftIndex = $dayIndex - 1;
                if ($shiftIndex < 0) {
                    continue;
                }
                
                $hourEndArray = json_decode($ruleShift->hour_end);
                
                if (!isset($hourEndArray[$shiftIndex])) {
                    continue;
                }
                
                $hourEnd = $hourEndArray[$shiftIndex];
                
                // Set overtime start time to shift end
                $startTime = Carbon::parse($hourEnd);
                // Overtime for 1-3 hours
                $hoursOvertime = rand(1, 3);
                $endTime = $startTime->copy()->addHours($hoursOvertime);
                
                // Format times for database
                $formattedStartTime = $startTime->format('H:i:s');
                $formattedEndTime = $endTime->format('H:i:s');
                
                // Determine the status
                $status = Arr::random($statuses);
                $deptApprovalStatus = $status;
                $adminApprovalStatus = 'Pending';
                $declinedReason = null;
                
                // For managers, skip dept approval
                $needsDeptApproval = $user->position_id > 3; // Supervisors and staff need dept approval
                
                // Setup approvers
                $deptApproverId = null;
                $adminApproverId = null;
                
                // Set dept approver if needed
                if ($needsDeptApproval && isset($this->managers[$user->department_id])) {
                    $deptApproverId = Arr::random($this->managers[$user->department_id]);
                    
                    // If declined at dept level, admin stays pending
                    if ($deptApprovalStatus === 'Declined') {
                        $adminApprovalStatus = 'Pending';
                        $declinedReason = 'Overtime request not justified for current workload.';
                    }
                } else {
                    // No dept approval needed, so it's automatically approved
                    $deptApprovalStatus = 'Approved';
                }
                
                // Set admin approver if dept approved
                if ($deptApprovalStatus === 'Approved') {
                    // For admin approval, exclude the requesting user from approvers
                    $possibleAdminApprovers = array_diff($this->directorGmHrManagers, [$user->id]);
                    
                    if (!empty($possibleAdminApprovers)) {
                        $adminApproverId = Arr::random($possibleAdminApprovers);
                        $adminApprovalStatus = $status;
                        
                        if ($adminApprovalStatus === 'Declined') {
                            $declinedReason = 'Budget constraints do not allow for overtime at this time.';
                        }
                    }
                }
                
                // Overall status follows the most negative status
                if ($deptApprovalStatus === 'Declined' || $adminApprovalStatus === 'Declined') {
                    $status = 'Declined';
                } elseif ($deptApprovalStatus === 'Pending' || $adminApprovalStatus === 'Pending') {
                    $status = 'Pending';
                } else {
                    $status = 'Approved';
                }
                
                EmployeeOvertime::create([
                    'user_id' => $user->id,
                    'date' => $requestDate->format('Y-m-d'),
                    'start_time' => $formattedStartTime,
                    'end_time' => $formattedEndTime,
                    'total_hours' => $hoursOvertime,
                    'reason' => $this->generateOvertimeReason(),
                    'approval_status' => $status,
                    'declined_reason' => $declinedReason,
                    'dept_approval_status' => $deptApprovalStatus,
                    'dept_approval_user_id' => $deptApproverId,
                    'admin_approval_status' => $adminApprovalStatus,
                    'admin_approval_user_id' => $adminApproverId,
                    'created_at' => $requestDate->copy()->subDays(rand(1, 7)),
                    'updated_at' => $requestDate->copy()->subDays(rand(0, 3))
                ]);
                
                $seedCount++;
            }
        }
        
        // Reload with newly created records
        $this->existingOvertimes = EmployeeOvertime::all();
    }

    /**
     * Seed employee absence records
     */
    private function seedEmployeeAbsents(): void
    {
        $seedCount = 0;
        $maxSeeds = 1000;
        $places = ['Office', 'Branch Office', 'Home Office', 'Client Site'];
        
        foreach ($this->users as $user) {
            // Generate attendance records starting from join date
            $currentDate = Carbon::parse($user->join_date);
            $endDate = Carbon::now()->subDays(1); // Up to yesterday
            
            while ($currentDate->lt($endDate) && $seedCount < $maxSeeds) {
                // Skip if record already exists for this user and date
                $existingRecord = $this->existingAbsents
                    ->where('user_id', $user->id)
                    ->where('date', $currentDate->format('Y-m-d'))
                    ->first();
                
                if ($existingRecord) {
                    $currentDate->addDay();
                    continue;
                }
                
                // Skip weekends and holidays
                if ($this->isHoliday($currentDate)) {
                    $currentDate->addDay();
                    continue;
                }
                
                // Check if user has a time-off request for this date
                $timeOffRequest = $this->existingTimeOffRequests
                    ->where('user_id', $user->id)
                    ->where('status', 'Approved')
                    ->filter(function ($request) use ($currentDate) {
                        $startDate = Carbon::parse($request->start_date)->startOfDay();
                        $endDate = Carbon::parse($request->end_date)->endOfDay();
                        return $currentDate->between($startDate, $endDate);
                    })
                    ->first();
                
                // If user has approved time-off, skip this day for attendance
                if ($timeOffRequest && !str_contains(strtolower($timeOffRequest->timeOffPolicy->time_off_name), 'masuk siang') && 
                    !str_contains(strtolower($timeOffRequest->timeOffPolicy->time_off_name), 'pulang awal')) {
                    $currentDate->addDay();
                    continue;
                }
                
                // Find the employee's shift for this date
                $shift = $this->findShiftForDate($user->id, $currentDate);
                
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
                    ->where('user_id', $user->id)
                    ->where('date', $currentDate->format('Y-m-d'))
                    ->where('approval_status', 'Approved')
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
                
                EmployeeAbsent::create([
                    'user_id' => $user->id,
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
                $currentDate->addDay();
            }
        }
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

    /**
     * Generate a realistic reason for time off
     */
    private function generateTimeOffReason(string $timeOffType): string
    {
        $reasons = [
            'Cuti Tahunan' => [
                'Keluarga besar berkumpul di kampung halaman',
                'Perlu waktu untuk istirahat dan me-refresh pikiran',
                'Menghadiri pernikahan keluarga',
                'Liburan keluarga yang sudah direncanakan',
                'Mengurus dokumen-dokumen penting',
                'Renovasi rumah yang membutuhkan pengawasan',
                'Urusan keluarga yang memerlukan kehadiran saya'
            ],
            'Cuti Sakit' => [
                'Demam tinggi dan butuh istirahat',
                'Rawat inap di rumah sakit',
                'Sedang dalam pemulihan setelah operasi',
                'Cedera yang membutuhkan istirahat',
                'Sakit perut yang parah',
                'Masalah kesehatan yang membutuhkan istirahat total'
            ],
            'Masuk Siang' => [
                'Perlu mengurus administrasi di bank pagi hari',
                'Mengantar anak ke dokter',
                'Urusan dengan pihak sekolah anak',
                'Antar keluarga ke bandara',
                'Harus ke kantor pelayanan publik di pagi hari'
            ],
            'Pulang Awal' => [
                'Perlu menghadiri acara sekolah anak',
                'Kedatangan tamu penting di rumah',
                'Janji dengan dokter untuk pemeriksaan kesehatan',
                'Keperluan keluarga mendadak',
                'Antar keluarga berobat'
            ],
            'Ijin' => [
                'Menghadiri pemakaman kerabat',
                'Anak sakit dan perlu diantar ke dokter',
                'Kerusakan di rumah yang perlu segera ditangani',
                'Mengurus dokumen kependudukan',
                'Panggilan dari sekolah anak'
            ]
        ];

        // Find the appropriate category by checking if the time off name contains any of the keys
        $category = 'Ijin'; // Default
        foreach ($reasons as $key => $values) {
            if (str_contains(strtolower($timeOffType), strtolower($key))) {
                $category = $key;
                break;
            }
        }

        return Arr::random($reasons[$category]);
    }

    /**
     * Generate a realistic reason for overtime
     */
    private function generateOvertimeReason(): string
    {
        $reasons = [
            'Menyelesaikan laporan bulanan yang deadline',
            'Mempersiapkan presentasi untuk meeting besok',
            'Menyelesaikan project yang mendekati deadline',
            'Menutup buku di akhir bulan',
            'Melakukan maintenance sistem yang tidak bisa dilakukan saat jam kerja',
            'Training sistem baru untuk tim',
            'Stock opname akhir bulan',
            'Persiapan audit internal',
            'Perbaikan sistem yang error',
            'Backup data bulanan dan verifikasi',
            'Menyelesaikan permintaan revisi dari klien'
        ];

        return Arr::random($reasons);
    }
}