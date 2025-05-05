<?php

namespace App\Http\Controllers;

use App\Models\EmployeeDepartment;
use App\Models\EmployeeOvertime;
use App\Models\EmployeePayroll;
use App\Models\EmployeePosition;
use App\Models\EmployeeSalary;
use App\Models\EmployeeShift;
use App\Models\SalaryHistory;
use App\Models\User;
use App\Models\history_transfer_employee;
use App\Models\EmployeeAbsent;
use App\Models\RequestTimeOff;
use App\Models\CustomHoliday;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;


class PayrollController extends Controller
{

    public function masterSalaryIndex(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $employeeSalaries = EmployeeSalary::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Get employees that don't have salary records yet and are active
        $salaryUserIds = EmployeeSalary::pluck('users_id')->toArray();
        $availableEmployees = User::where('employee_status', '!=', 'Inactive')
            ->whereNotIn('id', $salaryUserIds)
            ->get();

        return view('payroll.master_salary.index', compact('employeeSalaries', 'availableEmployees'));
    }

    public function masterSalaryStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|exists:users,id',
            'basic_salary' => 'required|numeric|min:0',
            'overtime_rate_per_hour' => 'required|numeric|min:0',
            'allowance' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validation failed.');
        }

        // Check if employee already has a salary record
        $exists = EmployeeSalary::where('users_id', $request->users_id)->exists();
        if ($exists) {
            return redirect()->back()
                ->with('error', 'This employee already has a salary record.');
        }

        EmployeeSalary::create([
            'users_id' => $request->users_id,
            'basic_salary' => $request->basic_salary,
            'overtime_rate_per_hour' => $request->overtime_rate_per_hour,
            'allowance' => $request->allowance
        ]);

        return redirect()->route('payroll.master.salary.index')
            ->with('success', 'Employee salary has been added successfully.');
    }

    public function masterSalaryUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:employee_salaries,id',
            'basic_salary' => 'required|numeric|min:0',
            'overtime_rate_per_hour' => 'required|numeric|min:0',
            'allowance' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validation failed.');
        }

        $salary = EmployeeSalary::findOrFail($request->id);

        // Create salary history record before updating
        SalaryHistory::create([
            'users_id' => $salary->users_id,
            'old_basic_salary' => $salary->basic_salary,
            'old_overtime_rate_per_hour' => $salary->overtime_rate_per_hour,
            'old_allowance' => $salary->allowance,
            'new_basic_salary' => $request->basic_salary,
            'new_overtime_rate_per_hour' => $request->overtime_rate_per_hour,
            'new_allowance' => $request->allowance
        ]);

        // Update the salary record
        $salary->update([
            'basic_salary' => $request->basic_salary,
            'overtime_rate_per_hour' => $request->overtime_rate_per_hour,
            'allowance' => $request->allowance
        ]);

        return redirect()->route('payroll.master.salary.index')
            ->with('success', 'Employee salary has been updated successfully.');
    }


    public function masterSalaryDestroy(Request $request)
    {
        $salary = EmployeeSalary::findOrFail($request->id);
        $salary->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('payroll.master.salary.index')
            ->with('success', 'Employee salary has been deleted successfully.');
    }






    public function salaryAssignIndex(Request $request)
    {
        // Get all users with their relationships
        $users = User::with(['department', 'position'])
            ->orderBy('name')
            ->get();

        // Get unique departments and positions
        $departments = EmployeeDepartment::orderBy('department')->get();
        $positions = EmployeePosition::orderBy('position')->get();
        $employees = User::where('employee_status', '!=', 'Inactive')->orderBy('name')->get();

        // Build the query
        $query = EmployeePayroll::with(['user']);

        // Apply month/year filter only if provided
        if ($request->filled('month_year')) {
            $monthYear = $request->month_year;
            $month = Carbon::parse($monthYear)->format('m');
            $year = Carbon::parse($monthYear)->format('Y');
            $query->whereMonth('created_at', $month)
                ->whereYear('created_at', $year);
        }

        // Apply employee filter
        if ($request->filled('employee_ids') && is_array($request->employee_ids)) {
            $query->whereIn('users_id', $request->employee_ids);
        }

        // Get results - sort by period (month and year) chronologically
        $results = $query->get();

        // Sort the results by period (created_at) chronologically
        $results = $results->sortBy(function ($payroll) {
            return $payroll->created_at->format('Y-m');
        });

        // Process historical data
        foreach ($results as $payroll) {
            $payrollDate = $payroll->created_at;

            $historyRecord = history_transfer_employee::where('users_id', $payroll->users_id)
                ->where('created_at', '<=', $payrollDate)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($historyRecord) {
                $position = EmployeePosition::find($historyRecord->new_position_id);
                $department = EmployeeDepartment::find($historyRecord->new_department_id);

                $payroll->historical_position_id = $historyRecord->new_position_id;
                $payroll->historical_position = $position ? $position->position : null;
                $payroll->historical_department_id = $historyRecord->new_department_id;
                $payroll->historical_department = $department ? $department->department : null;
            } else {
                $user = User::with(['department', 'position'])->find($payroll->users_id);
                $payroll->historical_position_id = $user->position_id ?? null;
                $payroll->historical_position = $user->position->position ?? null;
                $payroll->historical_department_id = $user->department_id ?? null;
                $payroll->historical_department = $user->department->department ?? null;
            }
        }

        // Apply department filter
        if ($request->filled('department_id')) {
            $results = $results->filter(function ($payroll) use ($request) {
                return $payroll->historical_department_id == $request->department_id;
            });
        }

        // Apply position filter
        if ($request->filled('position_id')) {
            $results = $results->filter(function ($payroll) use ($request) {
                return $payroll->historical_position_id == $request->position_id;
            });
        }

        // Add sequential numbering after all filtering and sorting
        $counter = 1;
        foreach ($results as $payroll) {
            $payroll->display_number = $counter++;
        }

        // Paginate results
        $page = request()->get('page', 1);
        $perPage = 10;
        $payrolls = new \Illuminate\Pagination\LengthAwarePaginator(
            $results->forPage($page, $perPage),
            $results->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('payroll.assign.index', compact(
            'payrolls',
            'departments',
            'positions',
            'employees',
            'users'
        ));
    }

    // Upload payroll attachment
    public function uploadAttachment(Request $request)
    {
        $request->validate([
            'payroll_id' => 'required|exists:employee_payroll,id',
            'attachment' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            $payroll = EmployeePayroll::with('user')->findOrFail($request->payroll_id);
            $user = $payroll->user;

            // Delete old file if exists
            if ($payroll->file_path && Storage::disk('public')->exists($payroll->file_path)) {
                Storage::disk('public')->delete($payroll->file_path);
            }

            // Generate a more organized filename
            $period = $payroll->created_at->format('Y_m');
            $sanitizedName = str_replace(' ', '_', $user->name);
            $employeeId = $user->employee_id ?? $user->id;
            $extension = $request->file('attachment')->getClientOriginalExtension();

            $fileName = "payroll_{$period}_{$sanitizedName}_{$employeeId}." . $extension;

            // Store file in organized folder structure (payroll/assign/year/month)
            $folder = 'payroll/assign/' . $payroll->created_at->format('Y') . '/' . $payroll->created_at->format('m');
            $path = $request->file('attachment')->storeAs($folder, $fileName, 'public');

            // Update payroll record
            $payroll->file_path = $path;
            $payroll->save();

            return redirect()->back()->with('success', 'Payroll attachment for ' . $user->name . ' has been uploaded successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error uploading attachment: ' . $e->getMessage());
        }
    }


    // Delete payroll record
    public function salaryAssignDestroy($id)
    {
        try {
            $payroll = EmployeePayroll::findOrFail($id);

            // Delete file if exists
            if ($payroll->file_path) {
                $filePath = 'public/' . $payroll->file_path;
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                }
            }

            $payroll->delete();

            return redirect()->route('payroll.assign.index')
                ->with('success', 'Payroll record deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('payroll.assign.index')
                ->with('error', 'Error deleting payroll record: ' . $e->getMessage());
        }
    }








    // Create - Show form to create new payroll
    public function salaryAssignCreate()
    {
        $departments = EmployeeDepartment::all();
        $positions = EmployeePosition::all();
        $employees = User::select('id', 'name')->orderBy('name')->get(); // Get all employees

        return view('payroll.assign.create', compact('departments', 'positions', 'employees'));
    }

    // Add this method to your controller to count absences for each employee
    private function getAbsencesForMonth($userId, $month, $year)
    {
        // Get all days in the month
        $date = Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $date->daysInMonth;
        $workingDays = 0;
        $workingDatesArray = [];

        // Get all attendance records for this user in this month
        $attendanceRecords = EmployeeAbsent::where('user_id', $userId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->pluck('date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        // Get all time-off requests for this user
        $timeOffRequests = RequestTimeOff::where('user_id', $userId)
            ->where('status', 'Approved')
            ->get();


        // Calculate working days in the month (excluding weekends and holidays)
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $checkDate = Carbon::createFromDate($year, $month, $day);
            $dateStr = $checkDate->format('Y-m-d');

            // Skip weekends
            if ($checkDate->isSunday()) {
                continue;
            }

            // Skip holidays
            if ($this->isHoliday($checkDate)) {
                continue;
            }


            // // Check if employee had a shift for this date
            // $shift = $this->getEmployeeShiftForDate($userId, $checkDate);
            // $expectedHours = $this->getExpectedHoursFromShift($shift, $checkDate);

            // // Skip if no shift was expected
            // if (!$expectedHours) {
            //     continue;
            // }



            // This is a working day
            $workingDays++;
            $workingDatesArray[] = $dateStr;
        }



        // Count how many working days the employee was present
        $presentDays = 0;
        foreach ($workingDatesArray as $workingDate) {
            // Check if date is in attendance records
            if (in_array($workingDate, $attendanceRecords)) {
                $presentDays++;
                continue;
            }

            // Check if employee had an approved time-off request for this date
            if ($this->getTimeOffForDate($timeOffRequests, $workingDate)) {
                $presentDays++;
            }
        }

        // Absences = workingDays - presentDays
        return max(0, $workingDays - $presentDays);
    }


    /**
     * Check if a given date is a holiday
     */
    private function isHoliday($date)
    {

        $dateStr = $date->format('Y-m-d');
        $year = $date->year;
        $month = $date->month;

        // Try to get holidays from cache
        $cacheKey = 'holidays_' . $year . '_' . $month;
        $holidays = Cache::remember($cacheKey, 86400, function () use ($year, $month) {
            // Fetch national holidays from API
            try {
                // Use month-specific API for more targeted results
                $apiUrl = "https://api-harilibur.vercel.app/api?month={$month}&year={$year}";
                $response = Http::get($apiUrl);

                if ($response->successful()) {
                    $apiHolidays = $response->json();

                    // Filter only national holidays
                    $nationalHolidays = collect($apiHolidays)
                        ->filter(function ($item) {
                            return $item['is_national_holiday'] === true;
                        })
                        ->map(function ($item) {
                            return Carbon::parse($item['holiday_date'])->format('Y-m-d');
                        })
                        ->toArray();

                    // Add custom holidays if any
                    $customHolidays = CustomHoliday::whereYear('date', $year)
                        ->whereMonth('date', $month)
                        ->pluck('date')
                        ->map(function ($date) {
                            return Carbon::parse($date)->format('Y-m-d');
                        })
                        ->toArray();

                    return array_merge($nationalHolidays, $customHolidays);
                }
            } catch (\Exception $e) {
                Log::error('Error fetching holidays: ' . $e->getMessage());
            }

            // Fallback to annual API if monthly fails
            try {
                $apiUrl = "https://api-harilibur.vercel.app/api?year={$year}";
                $response = Http::get($apiUrl);

                if ($response->successful()) {
                    $apiHolidays = $response->json();

                    // Filter only national holidays and for the specific month
                    $nationalHolidays = collect($apiHolidays)
                        ->filter(function ($item) use ($month) {
                            $holidayDate = Carbon::parse($item['holiday_date']);
                            return ($item['is_national_holiday'] === true) &&
                                ($holidayDate->month === $month);
                        })
                        ->map(function ($item) {
                            return Carbon::parse($item['holiday_date'])->format('Y-m-d');
                        })
                        ->toArray();

                    // Add custom holidays
                    $customHolidays = CustomHoliday::whereYear('date', $year)
                        ->whereMonth('date', $month)
                        ->pluck('date')
                        ->map(function ($date) {
                            return Carbon::parse($date)->format('Y-m-d');
                        })
                        ->toArray();

                    return array_merge($nationalHolidays, $customHolidays);
                }
            } catch (\Exception $e) {
                Log::error('Error fetching holidays from year API: ' . $e->getMessage());
            }

            return [];
        });



        return in_array($dateStr, $holidays);
    }

    public function getFilteredUsers(Request $request)
    {
        $departmentId = $request->department_id;
        $positionId = $request->position_id;
        $monthYear = $request->month_year;
        $employeeIds = $request->employee_ids ? explode(',', $request->employee_ids) : [];


        if (!$monthYear) {
            return response()->json([]);
        }

        $month = Carbon::parse($monthYear)->format('m');
        $year = Carbon::parse($monthYear)->format('Y');
        $date = Carbon::parse($monthYear)->endOfMonth();

        // Get existing payroll records for this month
        $existingPayrollUsers = EmployeePayroll::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->pluck('users_id')
            ->toArray();

        // Get all users first
        $query = User::query()
            ->with(['department', 'position'])
            ->where('employee_status', '!=', 'Inactive')
            ->whereNotIn('id', $existingPayrollUsers);



        // Apply employee filter if provided
        if (!empty($employeeIds)) {
            $query->whereIn('id', $employeeIds);
        }

        // Get users based on basic query
        $users = $query->get();
        $filteredUserIds = [];

        // Process each user to determine their department/position as of the selected month
        foreach ($users as $user) {
            // Get latest transfer record before the end of the selected month
            $latestTransfer = history_transfer_employee::where('users_id', $user->id)
                ->whereDate('created_at', '<=', $date)
                ->latest('created_at')
                ->first();

            // Determine department and position for the selected month
            $userDepartmentId = $latestTransfer ? $latestTransfer->new_department_id : $user->department_id;
            $userPositionId = $latestTransfer ? $latestTransfer->new_position_id : $user->position_id;

            // Apply department filter
            if ($departmentId && $userDepartmentId != $departmentId) {
                continue; // Skip this user if department doesn't match
            }

            // Apply position filter
            if ($positionId && $userPositionId != $positionId) {
                continue; // Skip this user if position doesn't match
            }

            // This user passes all filters
            $filteredUserIds[] = $user->id;

            // Store historical department and position for display
            if ($latestTransfer) {
                $user->historical_department = EmployeeDepartment::find($latestTransfer->new_department_id);
                $user->historical_position = EmployeePosition::find($latestTransfer->new_position_id);
            }

            // Get salary and overtime information
            $this->addSalaryAndOvertimeInfo($user, $date, $month, $year);

            // Calculate absences for this month
            $user->absences = $this->getAbsencesForMonth($user->id, $month, $year);
        }

        // Return only users that passed all filters
        return response()->json($users->whereIn('id', $filteredUserIds)->values());
    }

    /**
     * Check if a date falls within a time-off request period
     */
    private function getTimeOffForDate($timeOffRequests, $date)
    {
        $checkDate = Carbon::parse($date);

        foreach ($timeOffRequests as $request) {
            // Ensure dates are parsed consistently
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();

            if ($checkDate->between($startDate, $endDate)) {
                return $request;
            }
        }
        return null;
    }

    public function salaryAssignStore(Request $request)
    {
        $request->validate([
            'month_year' => 'required|date_format:Y-m',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'basic_salary' => 'required|array',
            'basic_salary.*' => 'numeric|min:0',
            'overtime_rate' => 'required|array',
            'overtime_rate.*' => 'numeric|min:0',
            'allowance' => 'required|array',
            'allowance.*' => 'numeric|min:0',
            'bonus' => 'required|array',
            'bonus.*' => 'numeric|min:0',
            'reduction_salary' => 'required|array',
            'reduction_salary.*' => 'numeric|min:0',
        ]);

        $monthYear = $request->month_year;
        $month = Carbon::parse($monthYear)->format('m');
        $year = Carbon::parse($monthYear)->format('Y');
        $payrollDate = Carbon::parse($monthYear);
        $successCount = 0;
        $skippedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($request->user_ids as $userId) {
                // Check if payroll already exists for this user and month/year
                $existingPayroll = EmployeePayroll::where('users_id', $userId)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->first();

                if ($existingPayroll) {
                    $skippedCount++;
                    continue; // Skip this user as payroll already exists
                }

                // Get overtime hours
                $overtimeHours = $request->overtime_hours[$userId] ?? 0;
                $overtimeRate = $request->overtime_rate[$userId];
                $overtimeSalary = $overtimeHours * $overtimeRate;

                // Get reduction salary
                $reductionSalary = $request->reduction_salary[$userId] ?? 0;

                // Create payroll record
                EmployeePayroll::create([
                    'users_id' => $userId,
                    'basic_salary' => $request->basic_salary[$userId],
                    'overtime_hours' => $overtimeHours,
                    'overtime_salary' => $overtimeSalary,
                    'allowance' => $request->allowance[$userId],
                    'bonus' => $request->bonus[$userId],
                    'reduction_salary' => $reductionSalary,
                    'created_at' => $payrollDate,
                ]);

                $successCount++;
            }

            DB::commit();

            $message = "Payroll assigned successfully for $successCount employee(s)";
            if ($skippedCount > 0) {
                $message .= ". $skippedCount employee(s) skipped as they already have payroll for this month.";
            }

            return redirect()->route('payroll.assign.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to assign payroll: ' . $e->getMessage())->withInput();
        }
    }

    // Helper method to add salary and overtime info to user
    private function addSalaryAndOvertimeInfo($user, $date, $month, $year)
    {
        // Ensure we have a Carbon instance for comparison
        $endOfMonth = Carbon::createFromDate($year, $month)->endOfMonth();
        
        // Get salary history for this user
        $salaryHistory = SalaryHistory::where('users_id', $user->id)
 
            ->latest('created_at')
            ->first();
        
        // Get regular salary information if no history exists
        $employeeSalary = EmployeeSalary::where('users_id', $user->id)->first();
        
        // Set salary information based on history or regular salary
        if ($salaryHistory) {
            // Check if the date is before the salary history created_at date
            if ($endOfMonth->lt(Carbon::parse($salaryHistory->created_at))) {
                // If the date is before the salary history, use old values
                $user->salary = [
                    'basic_salary' => $salaryHistory->old_basic_salary,
                    'allowance' => $salaryHistory->old_allowance,
                    'overtime_rate_per_hour' => $salaryHistory->old_overtime_rate_per_hour,
                ];
            } else {
                // If the date is on or after the salary history, use new values
                $user->salary = [
                    'basic_salary' => $salaryHistory->new_basic_salary,
                    'allowance' => $salaryHistory->new_allowance,
                    'overtime_rate_per_hour' => $salaryHistory->new_overtime_rate_per_hour,
                ];
            }
        } else {
            // If no history found, use current salary settings
            $user->salary = [
                'basic_salary' => $employeeSalary ? $employeeSalary->basic_salary : 0,
                'allowance' => $employeeSalary ? $employeeSalary->allowance : 0,
                'overtime_rate_per_hour' => $employeeSalary ? $employeeSalary->overtime_rate_per_hour : 0,
            ];
        }
        
        // Get overtime hours for this month
        $user->overtime_hours = EmployeeOvertime::where('user_id', $user->id)
            ->where('approval_status', 'Approved')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('total_hours');
    }
    private function getEmployeeShiftForDate($userId, $date)
    {
        $shift = EmployeeShift::where('user_id', $userId)
            ->where(function ($query) use ($date) {
                $query->where('start_date', '<=', $date->format('Y-m-d'))
                    ->where(function ($q) use ($date) {
                        $q->where('end_date', '>=', $date->format('Y-m-d'))
                            ->orWhereNull('end_date');
                    });
            })
            ->with('ruleShift')
            ->first();

        return $shift;
    }

    /**
     * Get the expected working hours from a shift rule for a specific date
     */
    private function getExpectedHoursFromShift($shift, $date)
    {

        if (!$shift || !$shift->ruleShift) {
            return null;
        }

        $dayOfWeek = $date->format('l'); // Monday, Tuesday, etc.

        $hourStart = json_decode($shift->ruleShift->hour_start, true);
        $hourEnd = json_decode($shift->ruleShift->hour_end, true);
        $days = json_decode($shift->ruleShift->days, true);

        // Find the index of the current day in the days array
        $dayIndex = array_search($dayOfWeek, $days);

        if ($dayIndex === false) {
            return null; // Not a working day
        }

        return [
            'start' => $hourStart[$dayIndex],
            'end' => $hourEnd[$dayIndex]
        ];
    }













    // Edit - Show form to edit payroll
    public function salaryAssignEdit($id)
    {
        $payroll = EmployeePayroll::with(['user.department', 'user.position'])
            ->findOrFail($id);

        // Get historical department/position if available
        $history = history_transfer_employee::where('users_id', $payroll->users_id)
            ->where('created_at', '<=', $payroll->created_at)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($history) {
            $payroll->historical_department = EmployeeDepartment::find($history->new_department_id);
            $payroll->historical_position = EmployeePosition::find($history->new_position_id);
        }

        // Calculate absences for this employee in the payroll month
        $monthYear = $payroll->created_at->format('Y-m');
        $month = $payroll->created_at->format('m');
        $year = $payroll->created_at->format('Y');
        $payroll->absences = $this->getAbsencesForMonth($payroll->users_id, $month, $year);

        return view('payroll.assign.edit', compact('payroll'));
    }

    // Update - Update existing payroll
    public function salaryAssignUpdate(Request $request, $id)
    {
        $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'overtime_hours' => 'required|numeric|min:0',
            'overtime_rate' => 'required|numeric|min:0',
            'allowance' => 'required|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'reduction_salary' => 'required|numeric|min:0',
            'attachment' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        DB::beginTransaction();
        try {
            $payroll = EmployeePayroll::with('user')->findOrFail($id);
            $user = $payroll->user;

            // Calculate overtime salary
            $overtimeSalary = $request->overtime_hours * $request->overtime_rate;

            $data = [
                'basic_salary' => $request->basic_salary,
                'overtime_hours' => $request->overtime_hours,
                'overtime_rate' => $request->overtime_rate,
                'overtime_salary' => $overtimeSalary,
                'allowance' => $request->allowance,
                'bonus' => $request->bonus ?? 0,
                'reduction_salary' => $request->reduction_salary
            ];

            // Handle file upload if present
            if ($request->hasFile('attachment')) {
                // Delete old file if exists
                if ($payroll->file_path && Storage::disk('public')->exists($payroll->file_path)) {
                    Storage::disk('public')->delete($payroll->file_path);
                }

                // Generate a more organized filename
                $period = $payroll->created_at->format('Y_m');
                $sanitizedName = str_replace(' ', '_', $user->name);
                $employeeId = $user->employee_id ?? $user->id;
                $extension = $request->file('attachment')->getClientOriginalExtension();

                $fileName = "payroll_{$period}_{$sanitizedName}_{$employeeId}." . $extension;

                // Store file in organized folder structure (payroll/assign/year/month)
                $folder = 'payroll/assign/' . $payroll->created_at->format('Y') . '/' . $payroll->created_at->format('m');
                $path = $request->file('attachment')->storeAs($folder, $fileName, 'public');

                $data['file_path'] = $path;
            }

            $payroll->update($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payroll updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payroll: ' . $e->getMessage()
            ], 500);
        }
    }
}
