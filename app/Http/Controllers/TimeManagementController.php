<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Validation\Rule;
use App\Imports\EmployeesImport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use DateInterval;
use DatePeriod;

use Illuminate\Bus\UpdatedBatchJobCounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\history_transfer_employee;
use App\Models\rule_shift;
use App\Models\EmployeeShift;
use App\Models\RequestShiftChange;
use App\Models\RequestResign;
use App\Models\EmployeeAbsent;
use App\Models\User;
use App\Models\WarningLetter;
use App\Models\WarningLetterRule;
use App\Models\Notification;
use App\Models\EmployeeOvertime;
use App\Models\TimeOffPolicy;
use App\Models\TimeOffAssign;
use App\Models\RequestTimeOff;
use App\Models\EmployeeDepartment;
use App\Models\EmployeePosition;
use App\Models\CustomHoliday;

use App\Mail\WarningLetterMail;
use App\Mail\EmployeeShiftAssigned;
use App\Mail\EmployeeShiftDeleted;
use App\Mail\EmployeeShiftUpdated;
use App\Mail\EmployeeShiftExchanged;
use App\Mail\ShiftChangeApprovedMail;
use App\Mail\ShiftChangeDeclinedMail;
use App\Mail\ShiftChangeRequestMail;
use App\Mail\ShiftChangeCancelledMail;
use App\Mail\ResignationRequestMail;
use App\Mail\ResignationUpdatedMail;
use App\Mail\ResignationDeletedMail;
use App\Mail\ResignationApprovedMail;
use App\Mail\ResignationDeclinedMail;
use App\Mail\OvertimeRequestMail;
use App\Mail\OvertimeApprovedMail;
use App\Mail\OvertimeDeclinedMail;
use App\Mail\OvertimeCancelledMail;
use App\Mail\TimeOffAssignUpdateMail;
use App\Mail\TimeOffAssignCreateMail;
use App\Mail\TimeOffAssignDestroyMail;
use App\Mail\TimeOffRequestSubmitted;
use App\Mail\TimeOffRequestCancelled;
use App\Mail\TimeOffRequestApproved;
use App\Mail\TimeOffRequestDeclined;


class TimeManagementController extends Controller
{
    /**
     * Display work shift page
     */
    public function rule_index()
    {
        $rule_shift = rule_shift::all()->map(function ($shift) {
            $shift->hour_start = implode('<br>', json_decode($shift->hour_start));
            $shift->hour_end = implode('<br>', json_decode($shift->hour_end));
            $shift->days = implode('<br>', json_decode($shift->days));
            return $shift;
        });


        return view('time_management/rule_shift/index', compact('rule_shift'));
    }

    public function rule_create()
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        return view('time_management/rule_shift/create', compact('days'));
    }



    // Store a newly created pegawai in the database
    public function rule_store(Request $request)
    {
        // Validate form input
        $request->validate([
            'type' => 'required|string',
            'days' => 'required|array',
            'start_time' => 'array',
            'end_time' => 'array',
        ]);

        // Tentukan type, termasuk custom type jika diisi
        $type = $request->type === 'Other' ? $request->custom_type : $request->type;

        // Menyusun jam kerja per hari
        $hourStart = [];
        $hourEnd = [];
        $selectedDays = [];

        foreach ($request->days as $day) {
            $start = $request->start_time[$day] ?? null;
            $end = $request->end_time[$day] ?? null;

            // Simpan hanya jika jam mulai dan akhir diisi
            if ($start && $end) {
                $hourStart[] = $start;
                $hourEnd[] = $end;
                $selectedDays[] = $day;
            }
        }

        // Simpan ke database dengan format JSON
        rule_shift::create([
            'type' => $type,
            'hour_start' => json_encode($hourStart),
            'hour_end' => json_encode($hourEnd),
            'days' => json_encode($selectedDays),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('time.rule.index')->with('success', 'Shift Rule added successfully');
    }


    public function rule_edit($id)
    {
        // Ambil data shift berdasarkan ID
        $rule_shift = rule_shift::findOrFail($id);

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        // Default types
        $defaultTypes = ['Morning', 'Afternoon', 'Normal'];

        // Ambil semua type unik dari database, tetapi tidak termasuk defaultTypes
        $types = rule_shift::select('type')
            ->whereNotIn('type', $defaultTypes)
            ->groupBy('type')
            ->pluck('type')
            ->toArray();

        return view('time_management/rule_shift/update', compact('rule_shift', 'types', 'defaultTypes', 'days'));
    }


    public function rule_update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string',
            'days' => 'required|array',
            'start_time' => 'array',
            'end_time' => 'array',
        ]);


        $type = $request->type === 'Other' ? $request->custom_type : $request->type;

        // Menyusun jam kerja per hari
        $hourStart = [];
        $hourEnd = [];
        $selectedDays = [];

        foreach ($request->days as $day) {
            $start = $request->start_time[$day] ?? null;
            $end = $request->end_time[$day] ?? null;

            // Simpan hanya jika jam mulai dan akhir diisi
            if ($start && $end) {
                $hourStart[] = $start;
                $hourEnd[] = $end;
                $selectedDays[] = $day;
            }
        }


        $rule_shift = rule_shift::findOrFail($id);
        $rule_shift->update([
            'type' => $type,
            'hour_start' => json_encode($hourStart),
            'hour_end' => json_encode($hourEnd),
            'days' => json_encode($selectedDays),
            'updated_at' => now(),
        ]);

        return redirect()->route('time.rule.index')->with('success', 'Shift Rule Successfully Updated.');
    }


    public function set_shift_index(Request $request)
    {
        $query = EmployeeShift::with(['user', 'ruleShift', 'user.position', 'user.department']);

        if ($request->has('type') && $request->type != '') {
            $query->where('rule_id', $request->type);
        }

        if ($request->has('employee') && $request->employee != '') {
            $query->where('user_id', $request->employee);
        }

        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('start_date', $request->start_date);
        }

        $employeeShifts = $query->whereNull('end_date')->get()->groupBy('rule_id');

        foreach ($employeeShifts as $ruleId => $shifts) {
            foreach ($shifts as $shift) {
                $latestTransfer = history_transfer_employee::where('users_id', $shift->user_id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $shift->historical_position = $latestTransfer->newPosition ?? $shift->user->position;
                $shift->historical_department = $latestTransfer->newDepartment ?? $shift->user->department;
            }
        }

        if ($request->has('position') && $request->position != '') {
            $employeeShifts = $employeeShifts->map(function ($shifts) use ($request) {
                return $shifts->filter(fn($shift) => $shift->historical_position == $request->position);
            })->filter(fn($shifts) => $shifts->count() > 0);
        }

        if ($request->has('department') && $request->department != '') {
            $employeeShifts = $employeeShifts->map(function ($shifts) use ($request) {
                return $shifts->filter(fn($shift) => $shift->historical_department == $request->department);
            })->filter(fn($shifts) => $shifts->count() > 0);
        }

        // History
        $query_history = EmployeeShift::with(['user', 'ruleShift', 'user.position', 'user.department']);

        if ($request->has('type_history') && $request->type_history != '') {
            $query_history->where('rule_id', $request->type_history);
        }

        if ($request->has('employee_history') && $request->employee_history != '') {
            $query_history->where('user_id', $request->employee_history);
        }

        if ($request->has('start_date_history') && $request->start_date_history != '') {
            $query_history->whereDate('start_date', $request->start_date_history);
        }

        if ($request->has('end_date_history') && $request->end_date_history != '') {
            $query_history->whereDate('end_date', $request->end_date_history);
        }

        $employeeShiftsHistory = $query_history->whereNotNull('end_date')->get()->groupBy('rule_id');

        foreach ($employeeShiftsHistory as $ruleId => $shifts) {
            foreach ($shifts as $shift) {
                $historicalTransfer = history_transfer_employee::where('users_id', $shift->user_id)
                    ->where('created_at', '<', $shift->start_date)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $shift->historical_position = $historicalTransfer->oldPosition ?? $shift->user->position;
                $shift->historical_department = $historicalTransfer->oldDepartment ?? $shift->user->department;
            }
        }

        if ($request->has('position_history') && $request->position_history != '') {
            $employeeShiftsHistory = $employeeShiftsHistory->map(function ($shifts) use ($request) {
                return $shifts->filter(fn($shift) => $shift->historical_position == $request->position_history);
            })->filter(fn($shifts) => $shifts->count() > 0);
        }

        if ($request->has('department_history') && $request->department_history != '') {
            $employeeShiftsHistory = $employeeShiftsHistory->map(function ($shifts) use ($request) {
                return $shifts->filter(fn($shift) => $shift->historical_department == $request->department_history);
            })->filter(fn($shifts) => $shifts->count() > 0);
        }

        $rules = rule_shift::all();
        $employees = User::where('employee_status', '!=', 'Inactive')->get();
        $departments = EmployeeDepartment::distinct()->pluck('department');
        $positions = EmployeePosition::distinct()->pluck('position');
        $activeTab = $request->tab ?? 'current';

        return view('/time_management/set_shift/index', compact(
            'employeeShifts',
            'employeeShiftsHistory',
            'rules',
            'employees',
            'positions',
            'departments',
            'activeTab'
        ));
    }


    public function set_shift_create()
    {
        $existShift = EmployeeShift::whereNull('end_date')
            ->distinct()
            ->pluck('user_id');

        $rules = rule_shift::all();

        $employees = User::where('employee_status', '!=', 'Inactive')
            ->whereNotIn('id', $existShift)
            ->with(['position', 'department']) // Eager load relationships
            ->get();

        // Mengambil daftar departemen unik dari model EmployeeDepartment
        $departments = EmployeeDepartment::distinct()
            ->pluck('department');

        // Mengambil daftar posisi unik dari model EmployeePosition
        $positions = EmployeePosition::distinct()
            ->pluck('position');

        return view('/time_management/set_shift/create', compact('employees', 'rules', 'departments', 'positions'));
    }

    public function set_shift_store(Request $request)
    {
        $request->validate([
            'rule_id' => 'required',
            'startDate' => 'required|date',
            'shift_employees' => 'nullable|string',
        ]);

        if ($request->shift_employees) {
            $employeeIds = explode(',', $request->shift_employees);

            foreach ($employeeIds as $employeeId) {
                $shift = EmployeeShift::create([
                    'rule_id' => $request->rule_id,
                    'start_date' => $request->startDate,
                    'user_id' => $employeeId,
                ]);

                // Eager load the ruleShift relationship
                $shift->load('ruleShift');

                // Get user
                $user = User::find($employeeId);
                if ($user) {
                    // Akses relasi dengan nama yang benar
                    $shiftType = $shift->ruleShift ? $shift->ruleShift->type : 'unknown';

                    // Hapus dd() yang tidak diperlukan
                    // dd($shift->ruleShift->type);

                    // Kirim email
                    Mail::to($user->email)->send(new EmployeeShiftAssigned($user, $shift));

                    // Buat notifikasi di database
                    Notification::create([
                        'users_id' => $user->id,
                        'message' => "You have been assigned to the {$shiftType} shift from {$shift->start_date} to " . ($shift->end_date ?? 'indefinitely') . ".",
                        'type' => 'shift_assigned',
                        'maker_id' => Auth::user()->id,
                        'status' => 'Unread'
                    ]);
                }
            }
        }

        return redirect()->route('time.set.shift.index')->with('success', 'Employee shift added successfully.');
    }

    public function set_shift_destroy($id)
    {
        $shift = EmployeeShift::findOrFail($id);
        $user = $shift->user;

        // Hapus shift
        $shift->delete();

        // Kirim notifikasi ke user
        Notification::create([
            'users_id' => $user->id,
            'message' => "We apologize for the inconvenience. There was an issue with your last shift assignment. Please wait for further information regarding your new shift, or contact the Human Resources department.",
            'type' => 'shift_deleted',
            'maker_id' => Auth::user()->id,
            'status' => 'Unread'
        ]);

        // Kirim email ke user
        Mail::to($user->email)->send(new EmployeeShiftDeleted($user));

        return response()->json(['message' => 'Shift deleted successfully.']);
    }

    public function set_shift_update(Request $request, $id)
    {
        $shift = EmployeeShift::findOrFail($id);
        $user = $shift->user;

        // Make sure the start date is not a Sunday
        $startDate = $this->getNextNonSundayDate($request->start_date);

        $lastShift = EmployeeShift::where('user_id', $request->user_id)
            ->whereNotNull('end_date')
            ->orderBy('id', 'desc')
            ->first();

        // Get rule_shift to access the type
        $ruleShift = rule_shift::find($request->rule_id);
        $shiftType = $ruleShift ? $ruleShift->type : 'unknown';

        if (!empty($lastShift)) {
            if ($startDate <= $lastShift->end_date) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Start date cannot be earlier than the previous shift end date.'
                ], 400);
            }

            if ($shift->start_date == $startDate) {
                $shift->update([
                    'rule_id' => $request->rule_id,
                    'updated_at' => now(),
                ]);

                Mail::to($user->email)->send(new EmployeeShiftUpdated($user, $shift));

                Notification::create([
                    'users_id' => $user->id,
                    'message' => "We sincerely apologize for the mistake. There has been a change in your shift assignment. You have now been reassigned to the {$shiftType} shift from {$shift->start_date} onwards.",
                    'type' => 'shift_updated',
                    'maker_id' => Auth::user()->id,
                    'status' => 'Unread'
                ]);
            } else if ($startDate > $lastShift->end_date) {
                $endDate = Carbon::parse($startDate)->subDay()->format('Y-m-d');
                $endDate = $this->getPreviousNonSundayDate($endDate);

                $shift->update([
                    'end_date' => $endDate,
                    'updated_at' => now(),
                ]);

                $newShift = EmployeeShift::create([
                    'rule_id' => $request->rule_id,
                    'start_date' => $startDate,
                    'user_id' => $request->user_id,
                ]);

                Mail::to($user->email)->send(new EmployeeShiftUpdated($user, $shift));

                Notification::create([
                    'users_id' => $user->id,
                    'message' => "We sincerely apologize for the mistake. There has been a change in your shift assignment. You have now been reassigned to the {$shiftType} shift from {$shift->start_date} onwards.",
                    'type' => 'shift_updated',
                    'maker_id' => Auth::user()->id,
                    'status' => 'Unread'
                ]);
            }
        } else {
            $shift->update([
                'rule_id' => $request->rule_id,
                'start_date' => $startDate
            ]);

            Mail::to($user->email)->send(new EmployeeShiftUpdated($user, $shift));

            Notification::create([
                'users_id' => $user->id,
                'message' => "We sincerely apologize for the mistake. There has been a change in your shift assignment. You have now been reassigned to the {$shiftType} shift from {$shift->start_date} onwards.",
                'type' => 'shift_updated',
                'maker_id' => Auth::user()->id,
                'status' => 'Unread'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Shift updated successfully.'
        ]);
    }

    public function exchangeShifts(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
        ]);

        $startDate = $this->getNextNonSundayDate($request->start_date);
        $startDateCarbon = Carbon::parse($startDate);

        $employees = EmployeeShift::whereNull('end_date')
            ->join('rule_shift', 'employee_shift.rule_id', '=', 'rule_shift.id')
            ->join('users', 'employee_shift.user_id', '=', 'users.id')
            ->whereIn('rule_shift.type', ['Morning', 'Afternoon'])
            ->select('employee_shift.*', 'rule_shift.type as shift_type', 'users.id as user_id', 'users.name as employee_name', 'users.email as user_email')
            ->get();

        if ($employees->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No employees found with active Morning or Afternoon shifts.'
            ], 400);
        }

        $updatedEmployees = [];

        foreach ($employees as $employee) {
            $lastShift = EmployeeShift::where('user_id', $employee->user_id)
                ->whereNotNull('end_date')
                ->orderBy('id', 'desc')
                ->first();

            $newShiftRule = rule_shift::where('type', $employee->shift_type === 'Morning' ? 'Afternoon' : 'Morning')->first();

            if (!$newShiftRule) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'New shift rule not found.'
                ], 400);
            }

            if ($startDateCarbon->format('Y-m-d') == $employee->start_date) {
                $updatedShift = tap(EmployeeShift::where('id', $employee->id))
                    ->update([
                        'rule_id' => $newShiftRule->id,
                        'updated_at' => now(),
                    ])
                    ->first(); // Ambil data setelah update

                $updatedEmployees[] = $employee->employee_name;

                // Send email
                Mail::to($employee->user_email)->send(new EmployeeShiftExchanged($employee, $updatedShift));


                // Send notification
                Notification::create([
                    'users_id' => $employee->user_id,
                    'message' => "You have been assigned to a new shift: {$newShiftRule->type}.",
                    'type' => 'shift_exchanged',
                    'maker_id' => Auth::user()->id,
                    'status' => 'Unread'
                ]);

                continue;
            }

            if ($lastShift && $startDateCarbon <= Carbon::parse($lastShift->end_date)) {
                continue;
            }

            if ($startDateCarbon < Carbon::parse($employee->start_date)) {
                continue;
            }

            if ($startDateCarbon > Carbon::parse($employee->start_date)) {
                $endDate = $startDateCarbon->copy()->subDay()->format('Y-m-d');
                $endDate = $this->getPreviousNonSundayDate($endDate);

                EmployeeShift::where('id', $employee->id)->update([
                    'end_date' => $endDate,
                    'updated_at' => now(),
                ]);

                $newShift = EmployeeShift::create([
                    'user_id' => $employee->user_id,
                    'rule_id' => $newShiftRule->id,
                    'start_date' => $startDate,
                ]);

                $updatedEmployees[] = $employee->employee_name;

                // Send email
                Mail::to($employee->user_email)->send(new EmployeeShiftExchanged($employee, $newShift));

                // Send notification
                Notification::create([
                    'users_id' => $employee->user_id,
                    'message' => "You have been assigned to a new shift: {$newShiftRule->type}.",
                    'type' => 'shift_exchanged',
                    'maker_id' => Auth::user()->id,
                    'status' => 'Unread'
                ]);
            }
        }

        if (empty($updatedEmployees)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No shifts could be updated due to date constraints.'
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Shifts exchanged successfully for: ' . implode(', ', $updatedEmployees)
        ]);
    }

    // Helper function to get the next non-Sunday date (for start dates)
    private function getNextNonSundayDate($date)
    {
        $date = Carbon::parse($date);
        if ($date->dayOfWeek === 0) { // If it's Sunday
            return $date->addDay()->format('Y-m-d'); // Move to Monday
        }
        return $date->format('Y-m-d');
    }

    // Helper function to get the previous non-Sunday date (for end dates)
    private function getPreviousNonSundayDate($date)
    {
        $date = Carbon::parse($date);
        if ($date->dayOfWeek === 0) { // If it's Sunday
            return $date->subDay()->format('Y-m-d'); // Move to Saturday
        }
        return $date->format('Y-m-d');
    }



    public function indexShiftChange(Request $request)
    {
        $currentUser = Auth::user();

        // Only allow Manager and above (position_id <= 3)
        if ($currentUser->position_id > 3) { // Supervisor (4) or Staff (5)
            abort(403, 'Access denied. Only managers and above can access this page.');
        }

        $query = RequestShiftChange::with([
            'user',
            'user.department',
            'user.position',
            'exchangeUser',
            'ruleShiftBefore',
            'ruleShiftAfter',
            'ruleExchangeBefore',
            'ruleExchangeAfter'
        ]);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status_change', $request->status);
        }

        if ($request->filled('department_id')) {
            $query->whereHas('user', fn($q) => $q->where('department_id', $request->department_id));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date_change_start', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date_change_end', '<=', $request->date_to);
        }

        // No user should see their own requests on this page
        $query->where('user_id', '!=', $currentUser->id);

        // Permission-based filtering
        if ($this->isDepartmentManager($currentUser)) {
            // Department Manager can only see requests from their own department's staff/supervisors
            $query->whereHas('user', function ($q) use ($currentUser) {
                $q->where('department_id', $currentUser->department_id)
                    ->where('position_id', '>', 3); // Only Staff (5) or Supervisor (4)
            });
        } elseif (!$this->isAdminUser($currentUser)) {
            // If not admin and not department manager, they shouldn't see any requests
            // (this is a safety check as we already have the position check at the top)
            abort(403, 'Access denied.');
        }
        // Admin can see all requests except their own (already filtered above)

        $perPage = $request->input('per_page', 15);
        $shiftChanges = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Add approval permissions
        $shiftChanges->getCollection()->transform(function ($item) use ($currentUser) {
            $item->can_approve_dept = $this->canApproveDept($currentUser, $item);
            $item->can_approve_admin = $this->canApproveAdmin($currentUser, $item);
            $item->can_decline = $this->canDecline($currentUser, $item);
            return $item;
        });

        return view('time_management.request_shift.index', [
            'shiftChanges' => $shiftChanges,
            'isManager' => $this->isDepartmentManager($currentUser),
            'isAdmin' => $this->isAdminUser($currentUser)
        ]);
    }

    // Helper methods
    private function isAdminUser($user)
    {
        return in_array($user->position_id, [1, 2]) || // Director or GM
            ($user->position_id == 3 && $user->department_id == 3); // HR Manager
    }

    private function isDepartmentManager($user)
    {
        return $user->position_id == 3 && $user->department_id != 3; // Manager non-HR
    }

    private function canApproveDept($currentUser, $request)
    {
        // Always check if not already approved or declined
        if ($request->dept_approval_status !== 'Pending') return false;

        // Can't approve own request
        if ($currentUser->id == $request->user_id) return false;

        // Only department manager can approve
        if (!$this->isDepartmentManager($currentUser)) return false;

        // Must be same department
        if ($currentUser->department_id != $request->user->department_id) return false;

        // Only for staff/supervisor requests (position_id 4 or 5)
        return $request->user->position_id > 3;
    }

    private function canApproveAdmin($currentUser, $request)
    {
        // Always check if not already approved or declined
        if ($request->admin_approval_status !== 'Pending') return false;

        // Can't approve own request
        if ($currentUser->id == $request->user_id) return false;

        // Only admin can approve
        if (!$this->isAdminUser($currentUser)) return false;

        // For manager requests or admin requests, only need to check admin approval status
        // (dept approval will be auto-approved)
        if ($request->user->position_id <= 3) {
            return $request->admin_approval_status === 'Pending';
        }

        // For staff/supervisor requests, must have department approval first
        return $request->dept_approval_status === 'Approved';
    }

    private function canDecline($currentUser, $request)
    {
        // Can't decline own request
        if ($currentUser->id == $request->user_id) return false;

        // If current user is department manager
        if ($this->isDepartmentManager($currentUser)) {
            // Can only decline requests from staff/supervisors in their department
            return $request->user->position_id > 3 &&
                $request->user->department_id == $currentUser->department_id &&
                $request->status_change === 'Pending';
        }

        // If current user is admin
        if ($this->isAdminUser($currentUser)) {
            // Can decline any pending request that's not their own
            return $request->status_change === 'Pending';
        }

        return false;
    }



    public function approveShiftChange($id)
    {

        $shiftChangeRequest = RequestShiftChange::with('user')->findOrFail($id);
        $currentUser = Auth::user();
        $approvalType = request('approval_type');

        // Validate approval type
        if (!in_array($approvalType, ['dept', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Invalid approval type']);
        }

        // Check permissions based on approval type
        if ($approvalType === 'dept' && !$this->canApproveDept($currentUser, $shiftChangeRequest)) {
            return response()->json(['success' => false, 'message' => 'You do not get permission to department approval']);
        }

        if ($approvalType === 'admin' && !$this->canApproveAdmin($currentUser, $shiftChangeRequest)) {
            return response()->json(['success' => false, 'message' => 'You do not get permission to admin approval']);
        }

        // Process approval
        if ($approvalType === 'dept') {
            $shiftChangeRequest->dept_approval_status = 'Approved';
            $shiftChangeRequest->dept_approval_user_id = $currentUser->id;
        } else {
            $shiftChangeRequest->admin_approval_status = 'Approved';
            $shiftChangeRequest->admin_approval_user_id = $currentUser->id;

            // Auto-approve dept level if requester is manager or admin
            if ($shiftChangeRequest->user->position_id <= 3) { // Manager or above
                $shiftChangeRequest->dept_approval_status = 'Approved';
            }
        }


        $shiftChangeRequest->save();
        $this->checkShiftChangeApprovalStatus($shiftChangeRequest);

        return response()->json(['success' => true, 'message' => 'Request approved successfully']);
    }


    private function checkShiftChangeApprovalStatus(RequestShiftChange $request)
    {

        // If both levels approved, update the main approval status and process the change
        if ($request->dept_approval_status === 'Approved' && $request->admin_approval_status === 'Approved') {
            DB::beginTransaction();
            try {
                $request->status_change = 'Approved';
                $request->save();



                $startDate = Carbon::parse($request->date_change_start);
                $endDate = Carbon::parse($request->date_change_end);

                // $this->processUserShiftChange($request->user_id, $request->rule_user_id_after, $startDate, $endDate);

                // if ($request->user_exchange_id) {
                //     $this->processUserShiftChange($request->user_exchange_id, $request->rule_user_exchange_id_after, $startDate, $endDate);
                // } else if ($request->rule_user_exchange_id_after) {
                //     $this->processUserShiftChange($request->user_id, $request->rule_user_exchange_id_after, $startDate, $endDate, true);
                // }

                // Send email
                Mail::to($request->user->email)->send(new ShiftChangeApprovedMail($request));

                // Create notification
                Notification::create([
                    'users_id' => $request->user_id,
                    'message' => "Your shift change request has been fully approved.",
                    'type' => 'shift_change_approved',
                    'maker_id' => Auth::id(),
                    'status' => 'Unread'
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }
    }

    private function processUserShiftChange($userId, $newRuleId, $startDate, $endDate, $isSecondaryShift = false)
    {
        // Find all affected shifts that overlap with the requested period
        $affectedShifts = EmployeeShift::where('user_id', $userId)
            ->where(function ($query) use ($startDate, $endDate) {
                // Find shifts that overlap with the request period
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<=', $endDate)
                        ->where(function ($q2) use ($startDate) {  // Changed from $endDate to $startDate
                            $q2->where('end_date', '>=', $startDate)
                                ->orWhereNull('end_date');
                        });
                });
            })
            ->orderBy('start_date')
            ->get();

        // If no affected shifts, create a new one for the requested period
        if ($affectedShifts->isEmpty()) {
            EmployeeShift::create([
                'user_id' => $userId,
                'rule_id' => $newRuleId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            return;
        }

        // Process each affected shift
        foreach ($affectedShifts as $shift) {
            $shiftStart = Carbon::parse($shift->start_date);
            $shiftEnd = $shift->end_date ? Carbon::parse($shift->end_date) : null;

            // CASE 1: If shift starts before request period
            if ($shiftStart->lt($startDate)) {
                // Create shift for period before request
                EmployeeShift::create([
                    'user_id' => $userId,
                    'rule_id' => $shift->rule_id,
                    'start_date' => $shift->start_date,
                    'end_date' => $startDate->copy()->subDay(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // CASE 2: If shift ends after request period
            if ($shiftEnd === null || $shiftEnd->gt($endDate)) {
                // Create shift for period after request
                EmployeeShift::create([
                    'user_id' => $userId,
                    'rule_id' => $shift->rule_id,
                    'start_date' => $endDate->copy()->addDay(),
                    'end_date' => $shift->end_date, // Might be NULL
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Mark the original shift as deleted
            $shift->delete();
        }

        // CASE 3: Always create the new shift for the requested period
        EmployeeShift::create([
            'user_id' => $userId,
            'rule_id' => $newRuleId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }



    public function declineShiftChange(Request $request, $id)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'declined_reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $shiftChangeRequest = RequestShiftChange::with('user', 'user.department', 'user.position')
                ->findOrFail($id);
            $currentUser = Auth::user();

            // Check decline permission
            if (!$this->canDecline($currentUser, $shiftChangeRequest)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to decline this request.'
                ], 403);
            }

            // Determine which level is declining
            $isDeptDecline = $this->isDepartmentManager($currentUser) &&
                $currentUser->department_id == $shiftChangeRequest->user->department_id;
            $isAdminDecline = $this->isAdminUser($currentUser);

            // Update appropriate approval statuses
            if ($isDeptDecline) {
                $shiftChangeRequest->dept_approval_status = 'Declined';
                $shiftChangeRequest->dept_approval_user_id = $currentUser->id;
            }

            if ($isAdminDecline) {
                $shiftChangeRequest->admin_approval_status = 'Declined';
                $shiftChangeRequest->admin_approval_user_id = $currentUser->id;


                // Auto set dept approval for manager/admin requests
                if (
                    $shiftChangeRequest->user->position_id <= 3 &&
                    $shiftChangeRequest->dept_approval_status === 'Pending'
                ) {
                    $shiftChangeRequest->dept_approval_status = 'Approved';
                    $shiftChangeRequest->dept_approval_user_id = $currentUser->id;
                }
            }

            // Update main status
            $shiftChangeRequest->status_change = 'Declined';
            $shiftChangeRequest->declined_reason = $request->declined_reason;
            $shiftChangeRequest->updated_at = now();
            $shiftChangeRequest->save();

            // Send notification (move to queue for better performance)
            $this->sendDeclineNotification($shiftChangeRequest, $currentUser);

            DB::commit();



            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error declining shift change request: ' . $e->getMessage(), [
                'request_id' => $id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    private function sendDeclineNotification($shiftChangeRequest, $decliner)
    {
        Mail::to($shiftChangeRequest->user->email)->send(new ShiftChangeDeclinedMail($shiftChangeRequest));

        Notification::create([
            'users_id' => $shiftChangeRequest->user_id,
            'message' => "Your shift change request has been declined by " . $decliner->name,
            'type' => 'shift_change_declined',
            'maker_id' => $decliner->id,
            'status' => 'Unread'
        ]);
    }






    public function showShiftChange($id)
    {
        $shiftChange = RequestShiftChange::with([
            'user',
            'exchangeUser',
            'ruleShiftBefore',
            'ruleShiftAfter',
            'ruleExchangeBefore',
            'ruleExchangeAfter',
            'deptApprovalUser',
            'adminApprovalUser'
        ])->findOrFail($id);

        return response()->json([
            'user' => [
                'name' => $shiftChange->user->name,
                'department' => $shiftChange->user->department->department ?? 'N/A',
                'position' => $shiftChange->user->position->position ?? 'N/A'
            ],
            'created_at' => $shiftChange->created_at,
            'date_change_start' => $shiftChange->date_change_start,
            'date_change_end' => $shiftChange->date_change_end,
            'exchange_user_id' => $shiftChange->exchange_user_id,
            'exchangeUser' => $shiftChange->exchangeUser ? ['name' => $shiftChange->exchangeUser->name] : null,
            'reason' => $shiftChange->reason_change,
            'ruleShiftBefore' => $this->formatShiftData($shiftChange->ruleShiftBefore),
            'ruleShiftAfter' => $this->formatShiftData($shiftChange->ruleShiftAfter),
            'dept_approval_status' => $shiftChange->dept_approval_status,
            'admin_approval_status' => $shiftChange->admin_approval_status,
            'deptApprovalUser' => $shiftChange->deptApprovalUser ? ['name' => $shiftChange->deptApprovalUser->name] : null,
            'adminApprovalUser' => $shiftChange->adminApprovalUser ? ['name' => $shiftChange->adminApprovalUser->name] : null
        ]);
    }

    private function formatShiftData($shift)
    {
        if (!$shift) return null;

        return [
            'type' => $shift->type,
            'days' => json_decode($shift->days),
            'hour_start' => json_decode($shift->hour_start),
            'hour_end' => json_decode($shift->hour_end)
        ];
    }







    /**
     * Display attendance page
     */

    public function employee_absent_index()
    {
        $employeeAbsent = EmployeeAbsent::all();
        $years = EmployeeAbsent::selectRaw('YEAR(date) as year')->distinct()->pluck('year')->sortDesc();

        return view('time_management/employee_absent/attendance/index', compact('employeeAbsent', 'years'));
    }

    public function getCustomHolidaysByYear(Request $request)
    {
        $year = $request->input('year', date('Y'));

        return CustomHoliday::whereYear('date', $year)
            ->select('name', 'date')
            ->get();
    }

    public function getAttendanceData(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        // Fetch all employees
        $employees = User::all();

        // Get all custom holidays for this month and year
        $customHolidays = CustomHoliday::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });

        // Prepare attendance data
        $attendanceData = $employees->map(function ($employee) use ($month, $year, $customHolidays) {

            // Initialize attendance array for all days
            $attendance = [];

            // Get all employee absences for the month
            $absences = EmployeeAbsent::where('user_id', $employee->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get()
                ->keyBy(function ($item) {
                    return Carbon::parse($item->date)->day;
                });

            // Get all approved time-off requests for the employee in this month
            $timeOffRequests = RequestTimeOff::where('user_id', $employee->id)
                ->where('status', 'Approved')
                ->where(function ($query) use ($year, $month) {
                    // Get requests that overlap with this month
                    $startDate = Carbon::createFromDate($year, $month, 1);
                    $endDate = $startDate->copy()->endOfMonth();

                    $query->where(function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('start_date', [$startDate, $endDate])
                            ->orWhereBetween('end_date', [$startDate, $endDate])
                            ->orWhere(function ($innerQ) use ($startDate, $endDate) {
                                $innerQ->where('start_date', '<=', $startDate)
                                    ->where('end_date', '>=', $endDate);
                            });
                    });
                })
                ->with('timeOffPolicy') // Assuming you have this relationship set up
                ->get();



            // Get all days in the month
            $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

            // Process each day
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::createFromDate($year, $month, $day);
                $dateYmd = $date->format('Y-m-d');
                $dayOfWeek = $date->format('l'); // Get day name (Monday, Tuesday, etc.)

                // Check if this is a custom holiday
                $isCustomHoliday = isset($customHolidays[$dateYmd]);
                $customHolidayName = $isCustomHoliday ? $customHolidays[$dateYmd]->name : null;

                // Get the employee's active shift for this day
                $employeeShift = DB::table('employee_shift')
                    ->where('user_id', $employee->id)
                    ->where('start_date', '<=', $date->format('Y-m-d'))
                    ->where(function ($query) use ($date) {
                        $query->whereNull('end_date')
                            ->orWhere('end_date', '>=', $date->format('Y-m-d'));
                    })
                    ->first();

                // Get the rule details if shift exists
                $ruleIn = null;
                $ruleOut = null;
                $ruleType = null;

                if ($employeeShift) {
                    $ruleShift = DB::table('rule_shift')
                        ->where('id', $employeeShift->rule_id)
                        ->first();

                    if ($ruleShift) {
                        // Parse JSON arrays
                        $hourStartArray = json_decode($ruleShift->hour_start);
                        $hourEndArray = json_decode($ruleShift->hour_end);
                        $daysArray = json_decode($ruleShift->days);

                        // Get the rule type (Morning, etc.)
                        $ruleType = $ruleShift->type;

                        // Find the index for the current day of week
                        $dayIndex = $this->getDayIndex($dayOfWeek, $daysArray);

                        if ($dayIndex !== false) {
                            $ruleIn = $hourStartArray[$dayIndex] ?? null;
                            $ruleOut = $hourEndArray[$dayIndex] ?? null;
                        }
                    }
                }

                // Find absence record for this day
                $absence = $absences->get($day);

                // Find time-off requests for this day
                $dateYmd = $date->format('Y-m-d');
                $timeOff = $this->getTimeOffForDate($timeOffRequests, $dateYmd);

                // Initialize attendance data for this day
                $dayAttendance = [];

                if ($absence) {
                    // Original expected hours calculation
                    $shift = $this->getEmployeeShiftForDate($employee->id, $date);
                    $expectedHours = $this->getExpectedHoursFromShift($shift, $date);
                    $expectedIn = $expectedHours ? $expectedHours['start'] : null;
                    $expectedOut = $expectedHours ? $expectedHours['end'] : null;

                    // Calculate minutes late/early
                    $lateMinutes = null;
                    $earlyMinutes = null;

                    // Use rule_in for shift start time if available
                    $shiftStartTime = $ruleIn ?: $expectedIn;

                    // Check if late arrival is planned
                    $hasLateArrivalRequest = $timeOff && strpos($timeOff->timeOffPolicy->time_off_name, 'include (Masuk Siang)') !== false;

                    if ($absence->hour_in && $shiftStartTime) {
                        $actualIn = Carbon::parse($absence->hour_in);
                        $expectedInTime = Carbon::parse($shiftStartTime);
                        if ($actualIn->gt($expectedInTime)) {
                            $lateMinutes = $actualIn->diffInMinutes($expectedInTime);
                        }
                    }

                    // Check if early departure is planned
                    $hasEarlyDepartureRequest = $timeOff && strpos($timeOff->timeOffPolicy->time_off_name, 'include (Pulang Awal)') !== false;

                    // Use rule_out for shift end time if available
                    $shiftEndTime = $ruleOut ?: $expectedOut;

                    if ($absence->hour_out && $shiftEndTime) {
                        $actualOut = Carbon::parse($absence->hour_out);
                        $expectedOutTime = Carbon::parse($shiftEndTime);
                        if ($actualOut->lt($expectedOutTime)) {
                            $earlyMinutes = $expectedOutTime->diffInMinutes($actualOut);
                        } else {
                            $earlyMinutes = null; // Not early
                        }
                    }

                    // Only add data if there's something to show
                    if ($absence->hour_in || $absence->hour_out) {
                        $dayAttendance = [
                            'id' => $absence->id, // Add this line to include the ID
                            'hour_in' => $absence->hour_in,
                            'hour_out' => $absence->hour_out,
                            'status_in' => $absence->status_in,
                            'status_out' => $absence->status_out,
                            'expected_in' => $expectedIn,
                            'expected_out' => $expectedOut,
                            'absent_place' => $absence->absent_place,
                            'late_minutes' => $lateMinutes,
                            'early_minutes' => $earlyMinutes,
                            'rule_in' => $ruleIn,
                            'rule_out' => $ruleOut,
                            'rule_type' => $ruleType,
                            'has_late_arrival_request' => $hasLateArrivalRequest,
                            'has_early_departure_request' => $hasEarlyDepartureRequest,

                            // New fields for enhanced display
                            'time_off' => $timeOff ? true : false,
                            'time_off_name' => $timeOff ? ($timeOff->timeOffPolicy->time_off_name ?? 'Time Off') : null,
                            'time_off_reason' => $timeOff ? ($timeOff->reason ?? '') : null,
                            'time_off_id' => $timeOff ? $timeOff->id : null,
                            'is_override' => ($timeOff && (!empty($absence->hour_in) || !empty($absence->hour_out))) ? true : false,
                            'time_off_type' => $timeOff ? $this->classifyLeaveType($timeOff) : null,

                            // Additional status flags
                            'is_sick_leave' => $timeOff ? str_contains(strtolower($timeOff->timeOffPolicy->time_off_name ?? ''), 'sakit') : false,
                            'is_annual_leave' => $timeOff ? str_contains(strtolower($timeOff->timeOffPolicy->time_off_name ?? ''), 'tahunan') : false,
                            'is_morning_leave' => $hasLateArrivalRequest,
                            'is_early_departure' => $hasEarlyDepartureRequest
                        ];
                    }
                }

                // If no attendance record but there's a time-off request
                if ((!$absence || (empty($absence->hour_in) && empty($absence->hour_out))) && $timeOff) {
                    $timeOffName = $timeOff->timeOffPolicy->time_off_name ?? 'Unknown';
                    $timeOffReason = $timeOff->reason ?? '';

                    $dayAttendance = [
                        'id' => null, // No attendance record yet
                        'time_off_id' => $timeOff->id,
                        'hour_in' => null,
                        'hour_out' => null,
                        'time_off' => true,
                        'time_off_name' => $timeOffName,
                        'time_off_reason' => $timeOffReason,
                        'rule_in' => $ruleIn,
                        'rule_out' => $ruleOut,
                        'rule_type' => $ruleType,
                        // Add leave type classification
                        'leave_type' => $this->classifyLeaveType($timeOff)
                    ];
                }

                // For cases with both time-off and attendance (override)
                elseif ($timeOff && ($absence->hour_in || $absence->hour_out)) {
                    $dayAttendance['is_override'] = true;
                    $dayAttendance['leave_type'] = $this->classifyLeaveType($timeOff);
                }

                // Only add to attendance if we have data
                if (!empty($dayAttendance)) {
                    $attendance[$day] = $dayAttendance;
                }
            }

            return [
                'employee_id' => $employee->employee_id,
                'name' => $employee->name,
                'attendance' => $attendance
            ];
        });

        return response()->json($attendanceData);
    }

    private function classifyLeaveType($timeOffRequest)
    {
        $policyName = strtolower($timeOffRequest->timeOffPolicy->time_off_name ?? '');

        // Full-day leave types
        if (str_contains($policyName, 'sakit')) {
            return 'sick_leave';
        }
        if (str_contains($policyName, 'tahunan')) {
            return 'annual_leave';
        }
        if (str_contains($policyName, 'ijin')) {
            return 'permission_leave'; // New type for "Ijin"
        }

        // Partial leave types
        if (str_contains($policyName, 'masuk siang')) {
            return 'approved_late_arrival';
        }
        if (str_contains($policyName, 'pulang awal')) {
            return 'approved_early_departure';
        }

        // Default to full-day leave for any other types
        return 'full_day_leave';
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


    private function getDayIndex($dayOfWeek, $daysArray)
    {
        if (!$daysArray) {
            return false;
        }

        foreach ($daysArray as $index => $day) {
            // Remove any trailing periods and compare
            $cleanDay = rtrim($day, '. ');
            if (strcasecmp($cleanDay, $dayOfWeek) === 0) {
                return $index;
            }
        }

        return false;
    }

    /**
     * Get the employee's shift for a specific date
     */
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


    public function importAttendance(Request $request)
    {
        $data = $request->json('data');
        $year = $request->json('year');
        $month = $request->json('month');

        $importCount = 0;
        $errors = [];
        $skipped = 0;

        DB::beginTransaction();

        // dd($data);

        try {
            foreach ($data as $record) {
                // Validate and extract necessary fields
                $nip = $record['NIP'] ?? null;
                $dateStr = $record['Date'] ?? null;
                $hourIn = $record['Hour In'] ?? null;
                $hourOut = $record['Hour Out'] ?? null;
                $absentPlace = $record['Place'] ?? null;



                if (!$nip || !$dateStr) {
                    $errors[] = "Missing NIP or Date in record: " . json_encode($record);
                    continue;
                }

                // Parse the date
                try {
                    $date = Carbon::createFromFormat('d/m/Y', $dateStr);
                } catch (\Exception $e) {
                    $errors[] = "Invalid date format in record: " . json_encode($record);
                    continue;
                }

                // Find the employee by NIP (employee_id)
                $employee = User::where('employee_id', $nip)->first();

                if (!$employee) {
                    $errors[] = "Employee with ID $nip not found. Skipping record.";
                    $skipped++;
                    continue;
                }

                // Get employee shift for this date
                $shift = $this->getEmployeeShiftForDate($employee->id, $date);
                $expectedHours = $this->getExpectedHoursFromShift($shift, $date);

                // Get rule times for comparison
                $ruleIn = $expectedHours ? $expectedHours['start'] : null;
                $ruleOut = $expectedHours ? $expectedHours['end'] : null;

                //dd($ruleIn);

                // Determine status for IN (comparing with rule_in)
                $statusIn = null;
                $lateMinutes = null;

                if ($hourIn && $ruleIn && $hourIn !== '00:00') {
                    $actualIn = Carbon::parse($hourIn);
                    $expectedIn = Carbon::parse($ruleIn);

                    // As per requirement: if hour_in <= rule_in then early, otherwise late
                    if ($actualIn->lte($expectedIn)) {
                        $statusIn = 'early';
                    } else {
                        $statusIn = 'late';
                        $lateMinutes = $actualIn->diffInMinutes($expectedIn);
                    }
                }

                // Determine status for OUT (comparing with rule_out)
                $statusOut = null;
                $earlyMinutes = null;

                if ($hourOut && $ruleOut && $hourOut !== '00:00') {
                    $actualOut = Carbon::parse($hourOut);
                    $expectedOut = Carbon::parse($ruleOut);

                    // As per requirement: similar logic for out time
                    if ($actualOut->gte($expectedOut)) {
                        $statusOut = 'late';
                    } else {
                        $statusOut = 'early';
                        $earlyMinutes = $expectedOut->diffInMinutes($actualOut);
                    }
                }

                // Create or update attendance record
                EmployeeAbsent::updateOrCreate(
                    [
                        'user_id' => $employee->id,
                        'date' => $date
                    ],
                    [
                        'hour_in' => $hourIn !== '00:00' ? $hourIn : null,
                        'hour_out' => $hourOut !== '00:00' ? $hourOut : null,
                        'absent_place' => $absentPlace,
                        'status_in' => $statusIn,
                        'status_out' => $statusOut,
                        'late_minutes' => $lateMinutes,
                        'early_minutes' => $earlyMinutes,
                        'rule_in' => $ruleIn,
                        'rule_out' => $ruleOut,
                        'rule_type' => $shift && $shift->ruleShift ? $shift->ruleShift->type : null
                    ]
                );

                $importCount++;
            }

            DB::commit();

            return response()->json([
                'message' => "Successfully imported $importCount attendance records. Skipped $skipped records due to missing employee information.",
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error importing attendance data: ' . $e->getMessage(),
                'errors' => $errors
            ], 500);
        }
    }



    /**
     * Get all employees for dropdown
     */
    public function getEmployees()
    {
        return User::select('id', 'name', 'employee_id')->orderBy('name')->get();
    }

    /**
     * Get expected hours for an employee on a specific date
     */
    public function getExpectedHours(Request $request)
    {
        $userId = $request->input('user_id');
        $date = Carbon::parse($request->input('date'));

        $shift = $this->getEmployeeShiftForDate($userId, $date);
        $expectedHours = $this->getExpectedHoursFromShift($shift, $date);

        return response()->json([
            'rule_in' => $expectedHours ? $expectedHours['start'] : null,
            'rule_out' => $expectedHours ? $expectedHours['end'] : null,
            'rule_type' => $shift && $shift->ruleShift ? $shift->ruleShift->type : null
        ]);
    }

    /**
     * Get a specific attendance record
     */
    public function getAttendance($id)
    {
        $attendance = EmployeeAbsent::findOrFail($id);

        return response()->json($attendance);
    }

    /**
     * Store a new attendance record
     */
    public function storeAttendance(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'hour_in' => 'nullable|date_format:H:i',
            'hour_out' => 'nullable|date_format:H:i',
            'absent_place' => 'nullable|string|max:255',
        ]);

        $date = Carbon::parse($validated['date']);

        // Get employee shift for this date
        $shift = $this->getEmployeeShiftForDate($validated['user_id'], $date);
        $expectedHours = $this->getExpectedHoursFromShift($shift, $date);

        // Calculate status_in and status_out
        $statusIn = null;
        $lateMinutes = null;
        $statusOut = null;
        $earlyMinutes = null;

        $ruleIn = $expectedHours ? $expectedHours['start'] : null;
        $ruleOut = $expectedHours ? $expectedHours['end'] : null;

        if (!empty($validated['hour_in']) && $ruleIn) {
            $actualIn = Carbon::parse($validated['hour_in']);
            $expectedIn = Carbon::parse($ruleIn);

            if ($actualIn->lte($expectedIn)) {
                $statusIn = 'early';
            } else {
                $statusIn = 'late';
                $lateMinutes = $actualIn->diffInMinutes($expectedIn);
            }
        }

        if (!empty($validated['hour_out']) && $ruleOut) {
            $actualOut = Carbon::parse($validated['hour_out']);
            $expectedOut = Carbon::parse($ruleOut);

            if ($actualOut->gte($expectedOut)) {
                $statusOut = 'late';
            } else {
                $statusOut = 'early';
                $earlyMinutes = $expectedOut->diffInMinutes($actualOut);
            }
        }

        // Create the attendance record
        $attendance = EmployeeAbsent::create([
            'user_id' => $validated['user_id'],
            'date' => $validated['date'],
            'hour_in' => $validated['hour_in'],
            'hour_out' => $validated['hour_out'],
            'absent_place' => $validated['absent_place'],
            'status_in' => $statusIn,
            'status_out' => $statusOut,
            'late_minutes' => $lateMinutes,
            'early_minutes' => $earlyMinutes,
            'rule_in' => $ruleIn,
            'rule_out' => $ruleOut,
            'rule_type' => $shift && $shift->ruleShift ? $shift->ruleShift->type : null
        ]);

        return response()->json([
            'message' => 'Attendance record created successfully',
            'data' => $attendance
        ]);
    }

    /**
     * Update an existing attendance record
     */
    public function updateAttendance(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'id' => 'required|exists:employee_absent,id',
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'hour_in' => 'required',
            'hour_out' => 'required',
            'absent_place' => 'nullable|string|max:255',
        ]);

        $attendance = EmployeeAbsent::findOrFail($validated['id']);
        $date = Carbon::parse($validated['date']);

        // Get employee shift for this date
        $shift = $this->getEmployeeShiftForDate($validated['user_id'], $date);
        $expectedHours = $this->getExpectedHoursFromShift($shift, $date);

        // Calculate status_in and status_out
        $statusIn = null;
        $lateMinutes = null;
        $statusOut = null;
        $earlyMinutes = null;

        $ruleIn = $expectedHours ? $expectedHours['start'] : null;
        $ruleOut = $expectedHours ? $expectedHours['end'] : null;

        if (!empty($validated['hour_in']) && $ruleIn) {
            $actualIn = Carbon::parse($validated['hour_in']);
            $expectedIn = Carbon::parse($ruleIn);

            if ($actualIn->lte($expectedIn)) {
                $statusIn = 'early';
            } else {
                $statusIn = 'late';
                $lateMinutes = $actualIn->diffInMinutes($expectedIn);
            }
        }

        if (!empty($validated['hour_out']) && $ruleOut) {
            $actualOut = Carbon::parse($validated['hour_out']);
            $expectedOut = Carbon::parse($ruleOut);

            if ($actualOut->gte($expectedOut)) {
                $statusOut = 'late';
            } else {
                $statusOut = 'early';
                $earlyMinutes = $expectedOut->diffInMinutes($actualOut);
            }
        }

        // Update the attendance record
        $attendance->update([
            'user_id' => $validated['user_id'],
            'date' => $validated['date'],
            'hour_in' => $validated['hour_in'],
            'hour_out' => $validated['hour_out'],
            'absent_place' => $validated['absent_place'],
            'status_in' => $statusIn,
            'status_out' => $statusOut,
            'late_minutes' => $lateMinutes,
            'early_minutes' => $earlyMinutes,
            'rule_in' => $ruleIn,
            'rule_out' => $ruleOut,
            'rule_type' => $shift && $shift->ruleShift ? $shift->ruleShift->type : null
        ]);




        return response()->json([
            'message' => 'Attendance record updated successfully',
            'data' => $attendance
        ]);
    }

    /**
     * Delete an attendance record
     */
    public function deleteAttendance($id)
    {
        $attendance = EmployeeAbsent::findOrFail($id);
        $attendance->delete();

        return response()->json([
            'message' => 'Attendance record deleted successfully'
        ]);
    }


    // Index - List all holidays
    public function custom_holiday_index()
    {
        $holidays = CustomHoliday::orderBy('date', 'desc')->get();
        return view('time_management/employee_absent/custom_holiday/index', compact('holidays'));
    }

    // Create form
    public function custom_holiday_create()
    {
        return view('time_management/employee_absent/custom_holiday/create');
    }

    // Store new holiday
    public function custom_holiday_store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date|unique:custom_holiday,date'
        ]);

        CustomHoliday::create($request->only(['name', 'description', 'date']));

        return redirect()->route('time.custom.holiday.index')
            ->with('success', 'Holiday created successfully.');
    }

    // Edit form
    public function custom_holiday_edit($id)
    {
        $holiday = CustomHoliday::findOrFail($id);
        return view('time_management/employee_absent/custom_holiday/update', compact('holiday'));
    }

    // Update holiday
    public function custom_holiday_update(Request $request, $id)
    {
        // dd( $request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date|unique:custom_holiday,date,' . $id
        ]);

        $holiday = CustomHoliday::findOrFail($id);
        $holiday->update($request->only(['name', 'description', 'date']));

        return redirect()->route('time.custom.holiday.index')
            ->with('success', 'Holiday updated successfully.');
    }

    public function checkDate(Request $request)
    {
        $query = CustomHoliday::where('date', $request->date);

        if ($request->has('exclude_id')) {
            $query->where('id', '!=', $request->exclude_id);
        }

        return response()->json([
            'exists' => $query->exists()
        ]);
    }



    /**
     * Display Warning letter
     */
    // Index - List all rules
    public function warning_letter_rule_index(Request $request)
    {
        $rules = WarningLetterRule::orderBy('name')->get();
        return view('time_management/warning_letter/rule/index', compact('rules'));
    }

    // Create - Show create form
    public function warning_letter_rule_create()
    {
        return view('time_management/warning_letter/rule/create');
    }

    // Store - Save new rule
    public function warning_letter_rule_store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'expired_length' => 'nullable|integer|min:1',
        ]);

        WarningLetterRule::create($validated);

        return redirect()->route('warning.letter.rule.index')
            ->with('success', 'Warning letter rule created successfully');
    }

    // Edit - Show edit form
    public function warning_letter_rule_edit($id)
    {
        $rule = WarningLetterRule::findOrFail($id);
        return view('time_management/warning_letter/rule/update', compact('rule'));
    }

    // Update - Update existing rule
    public function warning_letter_rule_update(Request $request, $id)
    {
        $rule = WarningLetterRule::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'expired_length' => 'nullable|integer|min:1',
        ]);

        $rule->update($validated);

        return redirect()->route('warning.letter.rule.index')
            ->with('success', 'Warning letter rule updated successfully');
    }




    public function warning_letter_index(Request $request)
    {
        // Get employees with their department and position relationships
        $employees = User::with(['department', 'position'])
            ->where('employee_status', '!=', 'Inactive')
            ->get();

        // Get unique departments and positions
        $departments = EmployeeDepartment::distinct()->pluck('department');
        $positions = EmployeePosition::distinct()->pluck('position');

        // Get all warning letter types for filter
        $types = WarningLetterRule::orderBy('name')->get();

        // Build base query with proper field names
        $query = DB::table('employee_warning_letter as ewl')
            ->join('users as employee', 'ewl.user_id', '=', 'employee.id')
            ->join('users as maker', 'ewl.maker_id', '=', 'maker.id')
            ->leftJoin('rule_warning_letter as rule', 'ewl.type_id', '=', 'rule.id')
            ->select(
                'ewl.*',
                'employee.name as employee_name',
                'employee.employee_id as employee_id',
                'employee.id as user_id',
                'maker.name as maker_name',
                'maker.employee_id as maker_id',
                'rule.name as type_name'
            );

        // Apply employee filter if specified
        if ($request->has('employee') && $request->employee != '') {
            $query->where('ewl.user_id', $request->employee);
        }

        // Apply type filter if specified
        if ($request->has('type') && $request->type != '') {
            $query->where('ewl.type_id', $request->type);
        }

        // Get all warning letters
        $warning_letters_raw = $query->get();

        // Process each warning letter to get historical position and department
        $warning_letters = [];
        foreach ($warning_letters_raw as $letter) {
            // Find the user's position and department at the time the warning letter was created
            $history = DB::table('users_transfer_history')
                ->where('users_id', $letter->user_id)
                ->where('created_at', '<', $letter->created_at)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($history) {
                // Find the old position and department names
                $oldPosition = EmployeePosition::find($history->new_position_id);
                $oldDepartment = EmployeeDepartment::find($history->new_department_id);

                $letter->employee_position = $oldPosition ? $oldPosition->position : null;
                $letter->employee_department = $oldDepartment ? $oldDepartment->department : null;
                $letter->position_id = $history->new_position_id;
                $letter->department_id = $history->new_department_id;
            } else {
                // No history record found, get current position and department
                $employee = User::with(['department', 'position'])->find($letter->user_id);
                $letter->employee_position = $employee->position ? $employee->position->position : null;
                $letter->employee_department = $employee->department ? $employee->department->department : null;
                $letter->position_id = $employee->position_id;
                $letter->department_id = $employee->department_id;
            }

            // Apply position filter
            if ($request->has('position') && $request->position != '') {
                if ($letter->employee_position == $request->position) {
                    $warning_letters[] = $letter;
                }
            }
            // Apply department filter
            else if ($request->has('department') && $request->department != '') {
                if ($letter->employee_department == $request->department) {
                    $warning_letters[] = $letter;
                }
            }
            // No filter, include all
            else {
                $warning_letters[] = $letter;
            }
        }


        return view(
            'time_management/warning_letter/assign/index',
            compact('warning_letters', 'employees', 'departments', 'positions', 'types')
        );
    }

    public function warning_letter_index2($id)
    {
        // Get all warning letter types for filter
        $types = WarningLetterRule::orderBy('name')->get();

        // Build query with proper field names
        $query = DB::table('employee_warning_letter as ewl')
            ->join('users as employee', 'ewl.user_id', '=', 'employee.id')
            ->join('users as maker', 'ewl.maker_id', '=', 'maker.id')
            ->leftJoin('rule_warning_letter as rule', 'ewl.type_id', '=', 'rule.id')
            ->select(
                'ewl.*',
                'employee.name as employee_name',
                'employee.employee_id as employee_id',
                'employee.id as user_id',
                'maker.name as maker_name',
                'maker.employee_id as maker_employee_id',
                'rule.name as type_name'
            )
            ->where('ewl.user_id', $id);

        // Apply type filter
        if (request()->has('type') && request()->type != '') {
            $query->where('ewl.type_id', request()->type);
        }

        // Apply created_at date range filters
        if (request()->has('created_from') && request()->created_from != '') {
            $query->whereDate('ewl.created_at', '>=', request()->created_from);
        }

        if (request()->has('created_to') && request()->created_to != '') {
            $query->whereDate('ewl.created_at', '<=', request()->created_to);
        }

        // If the filter is applied and the checkbox is unchecked, exclude records with null expiry dates
        if (
            request()->isMethod('get') && request()->filled('type') &&
            !request()->has('include_no_expiry')
        ) {
            $query->whereNotNull('ewl.expired_at');
        }

        $warning_letters_raw = $query->get();

        // Process each warning letter to get historical position and department
        $warning_letters = [];
        foreach ($warning_letters_raw as $letter) {
            // Find the user's position and department at the time the warning letter was created
            $history = DB::table('users_transfer_history')
                ->where('users_id', $letter->user_id)
                ->where('created_at', '<', $letter->created_at)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($history) {
                // Find the old position and department names
                $oldPosition = EmployeePosition::find($history->new_position_id);
                $oldDepartment = EmployeeDepartment::find($history->new_department_id);

                $letter->employee_position = $oldPosition ? $oldPosition->position : null;
                $letter->employee_department = $oldDepartment ? $oldDepartment->department : null;
            } else {
                // No history record found, get current position and department
                $employee = User::with(['department', 'position'])->find($letter->user_id);
                $letter->employee_position = $employee->position ? $employee->position->position : null;
                $letter->employee_department = $employee->department ? $employee->department->department : null;
            }

            $warning_letters[] = $letter;
        }

        // Set default checkbox state for the view (checked by default)
        $includeNoExpiry = !request()->isMethod('get') || !request()->filled('type') ||
            request()->has('include_no_expiry');

        return view(
            'time_management/warning_letter/assign/index2',
            compact('warning_letters', 'types', 'includeNoExpiry')
        );
    }


    public function warning_letter_create()
    {
        $employees = User::where('employee_status', '!=', 'Inactive')->get();
        return view('time_management/warning_letter/assign/create', compact('employees'));
    }

    public function warning_letter_store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason_warning' => 'required|string',
            'maker_id' => 'required|exists:users,id',
            'type_id' => 'required'
        ]);

        $user = User::findOrFail($request->user_id);
        $maker = User::findOrFail($request->maker_id);
        $warningType = WarningLetterRule::findOrFail($request->type_id);

        // Generate warning letter number
        $warningLetterNumber = $this->generateWarningLetterNumber($warningType->name, $request->type_id);

        // Calculate expired date
        $expiredAt = $this->calculateExpiredDate($warningType);

        // Create Warning Letter
        $warningLetter = WarningLetter::create([
            'user_id' => $request->user_id,
            'maker_id' => $request->maker_id,
            'type_id' => $request->type_id,
            'reason_warning' => $request->reason_warning,
            'warning_letter_number' => $warningLetterNumber,
            'expired_at' => $expiredAt
        ]);

        // Change employee status to Inactive if SP3
        if (str_contains($warningType->name, 'SP3')) {
            $user->update(['employee_status' => 'Inactive']);
        }

        // Prepare notification
        $this->createOrUpdateNotification($user->id, $maker->id, $warningType->name, $request->reason_warning);

        // Send email
        Mail::to($user->email)->send(new WarningLetterMail(
            $user,
            $warningType->name,
            $this->getWarningCountByType($request->user_id, $request->type_id),
            $request->reason_warning,
            $maker,
            false
        ));

        return redirect()->route('warning.letter.index')->with('success', 'Warning letter created successfully');
    }


    public function warning_letter_edit($id)
    {
        $warning_letter = WarningLetter::with(['rule', 'employee'])->findOrFail($id);
        $employee = $warning_letter->employee;
        $warningTypes = WarningLetterRule::orderBy('name')->get();

        return view('time_management/warning_letter/assign/update', compact('warning_letter', 'employee', 'warningTypes'));
    }

    public function warning_letter_update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason_warning' => 'required|string',
            'maker_id' => 'required|exists:users,id',
            'type_id' => 'required|exists:rule_warning_later,id'
        ]);

        $warning_letter = WarningLetter::findOrFail($id);
        $user = User::findOrFail($request->user_id);
        $maker = User::findOrFail($request->maker_id);
        $newType = WarningLetterRule::findOrFail($request->type_id);

        // Check if the type has changed
        $typeChanged = $warning_letter->type_id != $request->type_id;
        $oldType = $warning_letter->rule;

        // Calculate new expired date if type changed
        $expiredAt = $typeChanged ? $this->calculateExpiredDate($newType) : $warning_letter->expired_at;

        // Generate new warning letter number if type changed
        $warningLetterNumber = $typeChanged
            ? $this->generateWarningLetterNumber($newType->name, $request->type_id)
            : $warning_letter->warning_letter_number;

        // Update warning letter with all fields
        $warning_letter->update([
            'user_id' => $request->user_id,
            'maker_id' => $request->maker_id,
            'type_id' => $request->type_id,
            'reason_warning' => $request->reason_warning,
            'expired_at' => $expiredAt,
            'warning_letter_number' => $warningLetterNumber
        ]);

        // Handle employee status changes
        $this->handleEmployeeStatus($user, $newType->name, $oldType->name, $request->user_id, $id);

        // Send notification and email
        $this->sendNotifications($user, $maker, $newType, $oldType, $request->reason_warning, $id, $typeChanged);

        return redirect()->route('warning.letter.index')->with('success', 'Warning letter updated successfully');
    }

    protected function handleEmployeeStatus($user, $newTypeName, $oldTypeName, $userId, $warningId)
    {
        // If changed to SP3, update employee status
        if ($newTypeName === 'SP3') {
            $user->update(['employee_status' => 'Inactive']);
        }

        // If changed from SP3 to something else, check if we need to reactivate the employee
        if ($oldTypeName === 'SP3' && $newTypeName !== 'SP3') {
            $hasOtherSP3 = WarningLetter::where('user_id', $userId)
                ->whereHas('rule', function ($q) {
                    $q->where('name', 'SP3');
                })
                ->where('id', '!=', $warningId)
                ->exists();

            if (!$hasOtherSP3) {
                $user->update(['employee_status' => 'Active']);
            }
        }
    }

    protected function sendNotifications($user, $maker, $newType, $oldType, $reason, $warningId, $typeChanged)
    {
        // Get the warning count for this type
        $typeCount = WarningLetter::where('user_id', $user->id)
            ->where('type_id', $newType->id)
            ->where('id', '!=', $warningId)
            ->count() + 1;

        // Prepare notification message
        $notificationMessage = $typeChanged
            ? "Your warning letter has been updated from {$oldType->name} to {$newType->name} #{$typeCount}: " . $reason
            : "Your {$newType->name} warning letter #{$typeCount} has been updated: " . $reason;

        // Update or create notification
        Notification::updateOrCreate(
            [
                'users_id' => $user->id,
                'type' => 'warning_letter'
            ],
            [
                'message' => $notificationMessage,
                'status' => 'Unread',
                'maker_id' => $maker->id,
                'updated_at' => now()
            ]
        );

        // Send email
        Mail::to($user->email)->send(new WarningLetterMail(
            $user,
            $newType->name,
            $typeCount,
            $reason,
            $maker,
            true,
            $oldType->name
        ));
    }



    protected function generateWarningLetterNumber($typeName, $typeId)
    {
        $now = now();

        // Get count of this type for numbering
        $count = WarningLetter::where('type_id', $typeId)->count() + 1;

        // Format: no.{count}/TJI/{type}/{month}/{date}-{dailyCount}
        $monthRoman = $this->convertToRoman($now->month);
        $datePart = $now->format('dmy');

        // Get daily count for this type
        $dailyCount = WarningLetter::where('type_id', $typeId)
            ->whereDate('created_at', $now->toDateString())
            ->count() + 1;

        return "no.{$count}/TJI/{$typeName}/{$monthRoman}/{$datePart}-{$dailyCount}";
    }

    protected function calculateExpiredDate($warningType)
    {
        if ($warningType->expired_length) {
            return now()->addMonths($warningType->expired_length);
        }

        // Default fallback based on type name
        if (str_contains($warningType->name, 'ST')) {
            return now()->addMonths(3);
        } elseif (str_contains($warningType->name, 'SP')) {
            return now()->addMonths(6);
        }

        return null; // For Verbal or other types without expiration
    }

    protected function getWarningCountByType($userId, $typeId)
    {
        return WarningLetter::where('user_id', $userId)
            ->where('type_id', $typeId)
            ->count() + 1;
    }

    protected function createOrUpdateNotification($userId, $makerId, $typeName, $reason)
    {
        $count = $this->getWarningCountByType($userId, $typeName);
        $message = "You received warning letter {$typeName} #{$count}: " . $reason;

        Notification::updateOrCreate(
            [
                'users_id' => $userId,
                'type' => 'warning_letter'
            ],
            [
                'message' => $message,
                'status' => 'Unread',
                'maker_id' => $makerId
            ]
        );
    }

    protected function convertToRoman($number)
    {
        $map = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $map[$number] ?? '';
    }

    public function getAvailableWarningTypes(Request $request)
    {
        $userId = $request->user_id;
        $availableTypes = [];
        $description =  [];
        $message = '';

        if ($userId) {
            $currentDate = now();
            $warningTypes = WarningLetterRule::orderBy('name')->get();

            foreach ($warningTypes as $type) {
                $isAvailable = true;
                $currentMessage = '';

                // Skip expiration check for Verbal warnings
                if ($type->name !== 'Verbal') {
                    $checkMonths = str_contains($type->name, 'ST') ? 3 : 6;

                    // Check if this type has been given within the period
                    $hasRecentWarning = WarningLetter::where('user_id', $userId)
                        ->where('type_id', $type->id)
                        ->where('created_at', '>=', $currentDate->copy()->subMonths($checkMonths))
                        ->exists();

                    if ($hasRecentWarning) {
                        $isAvailable = false;
                        $currentMessage = "{$type->name} is not available - employee received within last {$checkMonths} months.";
                    }
                }

                // Check if this is Verbal and employee has any formal warnings
                if ($type->name === 'Verbal') {
                    $hasFormalWarning = WarningLetter::where('user_id', $userId)
                        ->whereHas('rule', function ($q) {
                            $q->where('name', '!=', 'Verbal');
                        })
                        ->exists();

                    if ($hasFormalWarning) {
                        $isAvailable = false;
                        $currentMessage = "Verbal is not available - employee already has formal warnings.";
                    }
                }

                if ($isAvailable) {
                    $availableTypes[$type->id] = $type->name;
                    $description[$type->id] = $type->description;
                } elseif (!empty($currentMessage)) {
                    // Add message with line break if it's not the first message
                    $message .= (empty($message) ? $currentMessage : '<br>' . $currentMessage);
                }
            }
        }

        return response()->json([
            'available_types' => $availableTypes,
            'description' => $description,
            'message' => $message
        ]);
    }

    /**
     * Display Change Shift
     */
    public function change_shift_index($id)
    {
        // Get employee's current shifts
        $employeeShifts = DB::table('employee_shift')
            ->join('rule_shift', 'employee_shift.rule_id', '=', 'rule_shift.id')
            ->where('employee_shift.user_id', $id)
            ->select('employee_shift.*', 'rule_shift.type', 'rule_shift.hour_start', 'rule_shift.hour_end', 'rule_shift.days')
            ->orderByRaw('CASE WHEN employee_shift.end_date IS NULL THEN 0 ELSE 1 END')
            ->orderBy('employee_shift.end_date', 'desc')
            ->get();

        // Get pending shift change requests
        $pendingRequests = RequestShiftChange::where('user_id', $id)
            ->where('status_change', 'Pending')
            ->with(['user', 'ruleShiftBefore', 'ruleShiftAfter', 'exchangeUser', 'ruleExchangeBefore', 'ruleExchangeAfter'])
            ->get();

        // Get all shift change requests
        $allRequests = RequestShiftChange::where('user_id', $id)
            ->with(['user', 'ruleShiftBefore', 'ruleShiftAfter', 'exchangeUser', 'ruleExchangeBefore', 'ruleExchangeAfter'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('time_management/change_shift/index', compact('employeeShifts', 'pendingRequests', 'allRequests', 'id'));
    }
    public function change_shift_create($id)
    {
        // Get all shift rules for selection
        $shiftRules = rule_shift::all();

        return view('time_management/change_shift/create', compact('shiftRules', 'id'));
    }

    public function change_shift_store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'date_change_start' => 'required|date',
            'date_change_end' => 'required|date|after_or_equal:date_change_start',
            'reason_change' => 'required',
        ]);

        // Get the user ID
        $userId = $request->user_id;

        // Check if valid_dates were provided
        if (!$request->has('valid_dates') || empty($request->valid_dates)) {
            return redirect()->back()->with('error', 'No valid dates provided for shift change.')->withInput();
        }

        // Get valid dates from the request
        $validDates = $request->valid_dates;
        sort($validDates); // Sort dates in ascending order

        // Process consecutive date ranges
        $dateRanges = [];
        $currentRange = ['start' => $validDates[0], 'end' => $validDates[0]];

        for ($i = 1; $i < count($validDates); $i++) {
            $prevDate = Carbon::parse($validDates[$i - 1]);
            $currentDate = Carbon::parse($validDates[$i]);

            if ($prevDate->addDay()->format('Y-m-d') === $currentDate->format('Y-m-d')) {
                // Consecutive date
                $currentRange['end'] = $validDates[$i];
            } else {
                // Non-consecutive, start new range
                $dateRanges[] = $currentRange;
                $currentRange = ['start' => $validDates[$i], 'end' => $validDates[$i]];
            }
        }

        // Add the last range
        $dateRanges[] = $currentRange;

        // Process each date range
        foreach ($dateRanges as $rangeIndex => $range) {
            $startDate = Carbon::parse($range['start']);
            $endDate = Carbon::parse($range['end']);
            $dateRange = CarbonPeriod::create($startDate, $endDate);

            $batchRequests = [];
            $currentBatch = null;
            $currentShiftType = null;

            // Group consecutive days with the same shift type
            foreach ($dateRange as $date) {
                // Get user's shift for this specific date
                $userShift = $this->getUserShiftForDate($userId, $date);

                if (!$userShift) {
                    continue; // Skip dates with no shift assignment
                }

                // Get shift type for this date
                $shiftType = $userShift->type;

                // If shift type changed or starting a new batch
                if ($shiftType !== $currentShiftType) {
                    // Save previous batch if exists
                    if ($currentBatch) {
                        $batchRequests[] = $currentBatch;
                    }

                    // Start new batch
                    $currentBatch = [
                        'start_date' => $date->format('Y-m-d'),
                        'end_date' => $date->format('Y-m-d'),
                        'shift_type' => $shiftType,
                        'rule_id' => $userShift->rule_id
                    ];

                    $currentShiftType = $shiftType;
                } else {
                    // Extend current batch end date
                    $currentBatch['end_date'] = $date->format('Y-m-d');
                }
            }

            // Add the last batch
            if ($currentBatch) {
                $batchRequests[] = $currentBatch;
            }

            // Process each batch and create request records
            foreach ($batchRequests as $index => $batch) {
                $targetShiftType = $batch['shift_type'] == 'Morning' ? 'Afternoon' : 'Morning';

                // Create the request
                $shiftChange = new RequestShiftChange();
                $shiftChange->user_id = $userId;
                $shiftChange->rule_user_id_before = $batch['rule_id'];

                // Set target shift rule (for now, use the first available rule of the target type)
                $targetRule = rule_shift::where('type', $targetShiftType)->first();
                if (!$targetRule) {
                    return redirect()->back()->with('error', "No {$targetShiftType} shift rule found in the system.")->withInput();
                }

                $shiftChange->rule_user_id_after = $targetRule->id;

                // Handle exchange selection if provided
                if ($request->request_type == 'exchange' && $request->has('batch_exchange_partners')) {
                    $batchExchangePartners = $request->batch_exchange_partners;
                    $batchKey = array_keys($batchRequests)[$index];

                    if (isset($batchExchangePartners[$batchKey])) {
                        $exchangeUserId = $batchExchangePartners[$batchKey];

                        // Get exchange user's shift for this period
                        $exchangeShift = $this->getUserShiftForDate($exchangeUserId, Carbon::parse($batch['start_date']));

                        if (!$exchangeShift) {
                            return redirect()->back()->with('error', 'Selected exchange user does not have a shift during this period.')->withInput();
                        }

                        $shiftChange->user_exchange_id = $exchangeUserId;
                        $shiftChange->rule_user_exchange_id_before = $exchangeShift->rule_id;
                        $shiftChange->rule_user_exchange_id_after = $batch['rule_id']; // They get the requestor's shift
                    } else {
                        return redirect()->back()->with('error', 'Missing exchange partner for one or more shifts.')->withInput();
                    }
                }

                $shiftChange->reason_change = $request->reason_change;
                $shiftChange->status_change = 'Pending';
                $shiftChange->date_change_start = $batch['start_date'];
                $shiftChange->date_change_end = $batch['end_date'];
                $shiftChange->save();

                // Get HR users
                $hrUsers = User::whereHas('department', function ($query) {
                    $query->where('department', 'Human Resources');
                })
                    ->where('employee_status', '!=', 'Inactive')
                    ->get();

                // Create notifications & send emails
                foreach ($hrUsers as $hr) {
                    Notification::create([
                        'users_id' => $hr->id,
                        'message' => "New shift change request from {$shiftChange->user->name}.",
                        'type' => 'shift_request',
                        'maker_id' => Auth::user()->id,
                        'status' => 'Unread'
                    ]);

                    Mail::to($hr->email)->send(new ShiftChangeRequestMail($shiftChange));
                }
            }
        }

        return redirect()->route('change.shift.index', $userId)->with('success', 'Shift change request(s) submitted successfully.');
    }

    /**
     * Get user's shift for a specific date
     */
    private function getUserShiftForDate($userId, $date)
    {
        return DB::table('employee_shift')
            ->join('rule_shift', 'employee_shift.rule_id', '=', 'rule_shift.id')
            ->where('employee_shift.user_id', $userId)
            ->where(function ($query) use ($date) {
                $dateStr = $date instanceof Carbon ? $date->format('Y-m-d') : $date;
                $query->where(function ($q) use ($dateStr) {
                    $q->where('employee_shift.start_date', '<=', $dateStr)
                        ->whereNull('employee_shift.end_date');
                })->orWhere(function ($q) use ($dateStr) {
                    $q->where('employee_shift.start_date', '<=', $dateStr)
                        ->where('employee_shift.end_date', '>=', $dateStr);
                });
            })
            ->select('employee_shift.*', 'rule_shift.type', 'rule_shift.id as rule_id')
            ->first();
    }

    /**
     * Find potential exchange users for a date range
     */
    private function findPotentialExchanges($userId, $targetShiftType, $startDate, $endDate)
    {
        return DB::table('employee_shift')
            ->join('users', 'employee_shift.user_id', '=', 'users.id')
            ->join('rule_shift', 'employee_shift.rule_id', '=', 'rule_shift.id')
            ->where('users.id', '!=', $userId)
            ->where('rule_shift.type', $targetShiftType)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->where('employee_shift.start_date', '<=', $startDate)
                        ->whereNull('employee_shift.end_date');
                })->orWhere(function ($q) use ($startDate, $endDate) {
                    $q->where('employee_shift.start_date', '<=', $startDate)
                        ->where('employee_shift.end_date', '>=', $endDate);
                });
            })
            ->select('users.id', 'users.name', 'rule_shift.type', 'rule_shift.id as rule_id')
            ->get();
    }

    /**
     * Get potential exchange partners for a date range via AJAX
     */
    public function getExchangePartners(Request $request)
    {
        $userId = $request->user_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Get the current user's shift type for this period
        $userShift = $this->getUserShiftForDate($userId, Carbon::parse($startDate));

        if (!$userShift) {
            return response()->json([
                'success' => false,
                'message' => 'No shift assignment found for you in this period',
                'partners' => []
            ]);
        }

        // Get the opposite shift type
        $targetShiftType = $userShift->type === 'Morning' ? 'Afternoon' : 'Morning';

        // Find potential exchange partners
        $partners = $this->findPotentialExchanges($userId, $targetShiftType, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'partners' => $partners
        ]);
    }

    /**
     * Get shift preview for selected date range via AJAX
     */
    public function getShiftPreview(Request $request)
    {
        $userId = $request->user_id;
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Validate maximum date range (7 days)
        $daysDifference = $startDate->diffInDays($endDate) + 1; // +1 to include both start and end dates

        if ($daysDifference > 7) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum date range allowed is 7 days',
                'shifts' => []
            ]);
        }

        $dateRange = CarbonPeriod::create($startDate, $endDate);

        $shifts = [];
        $currentBatch = null;
        $currentShiftType = null;

        // Group consecutive days with the same shift type
        foreach ($dateRange as $date) {
            // Get user's shift for this specific date
            $userShift = $this->getUserShiftForDate($userId, $date);

            if (!$userShift) {
                continue; // Skip dates with no shift assignment
            }

            // Get shift type for this date
            $shiftType = $userShift->type;

            // If shift type changed or starting a new batch
            if ($shiftType !== $currentShiftType) {
                // Save previous batch if exists
                if ($currentBatch) {
                    $shifts[] = $currentBatch;
                }

                // Start new batch
                $currentBatch = [
                    'start_date' => $date->format('Y-m-d'),
                    'end_date' => $date->format('Y-m-d'),
                    'current_shift_type' => $shiftType,
                    'rule_id' => $userShift->rule_id
                ];

                $currentShiftType = $shiftType;
            } else {
                // Extend current batch end date
                $currentBatch['end_date'] = $date->format('Y-m-d');
            }
        }

        // Add the last batch
        if ($currentBatch) {
            $shifts[] = $currentBatch;
        }

        return response()->json([
            'success' => true,
            'shifts' => $shifts
        ]);
    }
    public function destroy_request($id)
    {
        try {
            $request = RequestShiftChange::findOrFail($id);

            // Check if user is authorized to delete this request
            if ($request->user_id != Auth::id() || $request->status_change != 'Pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to delete this request or it is no longer pending.'
                ]);
            }



            // First get the HR department ID
            $hrDepartment = EmployeeDepartment::where('department', 'Human Resources')->first();

            // Then get users in that department
            $hrUsers = User::where('department_id', $hrDepartment->id)
                ->where('employee_status', '!=', 'Inactive')
                ->get();

            // Create notifications & send emails
            foreach ($hrUsers as $hr) {
                Notification::create([
                    'users_id' => $hr->id,
                    'message' => "Shift change request from {$request->user->name} has been cancelled.",
                    'type' => 'shift_cancelled',
                    'maker_id' => Auth::user()->id,
                    'status' => 'Unread'
                ]);

                Mail::to($hr->email)->send(new ShiftChangeCancelledMail($request));
            }


            // Get updated pending count
            $pendingCount = RequestShiftChange::where('user_id', Auth::id())
                ->where('status_change', 'Pending')
                ->count();


            $request->delete();
            return response()->json([
                'success' => true,
                'message' => 'Shift change request deleted successfully.',
                'pendingCount' => $pendingCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting shift change request: ' . $e->getMessage()
            ]);
        }
    }

    public function getExistingRequests(Request $request)
    {
        $userId = $request->user_id;

        $requests = RequestShiftChange::where('user_id', $userId)
            ->whereIn('status_change', ['Pending', 'Approved'])
            ->select('date_change_start', 'date_change_end', 'status_change')
            ->get();

        return response()->json([
            'success' => true,
            'requests' => $requests
        ]);
    }


    /**
     * Display Resign management page
     */


    public function request_resign_index(Request $request)
    {
        // Get all employees for filter dropdown
        $employees = User::with(['department', 'position'])
            ->where('employee_status', '!=', 'Inactive')
            ->get();

        // Get unique departments and positions from their respective tables
        $departments = EmployeeDepartment::distinct()->pluck('department');
        $positions = EmployeePosition::distinct()->pluck('position');

        // Get unique resign types
        $types = RequestResign::whereNotNull('resign_type')
            ->distinct()
            ->pluck('resign_type')
            ->toArray();

        $requiredTypes = ['Voluntary', 'Retirement'];
        $finalTypes = array_unique(array_merge($types, $requiredTypes));

        // Base query with joins - remove department and position joins since we'll handle filtering differently
        $query = DB::table('request_resign')
            ->join('users as employee', 'request_resign.user_id', '=', 'employee.id')
            ->leftJoin('users as responder', 'request_resign.response_user_id', '=', 'responder.id')
            ->select(
                'request_resign.*',
                'employee.name as employee_name',
                'employee.employee_id as employee_id',
                'employee.id as user_id',
                'responder.name as response_name'
            );

        // Apply basic filters
        if ($request->filled('user_id')) {
            $query->where('request_resign.user_id', $request->user_id);
        }

        if ($request->filled('resign_type')) {
            $query->where('request_resign.resign_type', $request->resign_type);
        }

        if ($request->filled('date')) {
            $query->where('request_resign.resign_date', $request->date);
        }

        $show_pending = true;
        $show_approved = true;
        $show_declined = true;

        if ($request->filled('response_type')) {
            $query->where('request_resign.resign_status', $request->response_type);
            $show_pending = $request->response_type == 'Pending';
            $show_approved = $request->response_type == 'Approved';
            $show_declined = $request->response_type == 'Declined';
        }

        // Get the raw requests separated by status
        $pending_requests_raw = (clone $query)->where('resign_status', 'Pending')->get();
        $approved_requests_raw = (clone $query)->where('resign_status', 'Approved')->get();
        $declined_requests_raw = (clone $query)->where('resign_status', 'Declined')->get();

        // Process each request to add historical position and department and apply filters
        $pending_requests = $this->processAndFilterRequests($pending_requests_raw, $request);
        $approved_requests = $this->processAndFilterRequests($approved_requests_raw, $request);
        $declined_requests = $this->processAndFilterRequests($declined_requests_raw, $request);

        return view('time_management/request_resign/index', compact(
            'employees',
            'positions',
            'departments',
            'pending_requests',
            'approved_requests',
            'declined_requests',
            'finalTypes',
            'show_pending',
            'show_approved',
            'show_declined'
        ));
    }

    public function request_resign_index2($id)
    {
        $query = DB::table('request_resign')
            ->join('users as employee', 'request_resign.user_id', '=', 'employee.id')
            ->leftJoin('users as responder', 'request_resign.response_user_id', '=', 'responder.id')
            ->select(
                'request_resign.*',
                'employee.name as employee_name',
                'employee.employee_id as employee_id',
                'employee.id as user_id',
                'responder.name as response_name'
            )
            ->where('request_resign.user_id', $id);

        // Get raw request data separated by status
        $pending_requests_raw = (clone $query)->where('resign_status', 'Pending')->get();
        $approved_requests_raw = (clone $query)->where('resign_status', 'Approved')->get();
        $declined_requests_raw = (clone $query)->where('resign_status', 'Declined')->get();

        // Process each request to add historical position and department
        $pending_requests = $this->processHistoricalPositionDepartment($pending_requests_raw);
        $approved_requests = $this->processHistoricalPositionDepartment($approved_requests_raw);
        $declined_requests = $this->processHistoricalPositionDepartment($declined_requests_raw);

        $user = User::with(['department', 'position'])->find($id);

        return view('time_management/request_resign/index2', compact(
            'pending_requests',
            'approved_requests',
            'declined_requests',
            'user'
        ));
    }


    private function processAndFilterRequests($requests, $request)
    {
        $filtered_requests = [];

        foreach ($requests as $item) {
            // Find the transfer history record closest to, but before the resign date
            $history = DB::table('users_transfer_history')
                ->where('users_id', $item->user_id)
                // ->where('created_at', '<', $item->resign_date)  // Use resign_date
                ->where('created_at', '<', $item->created_at)  // Use resign_date
                ->orderBy('created_at', 'desc')
                ->first();

            if ($history) {
                // Get the historical position and department
                $oldPosition = EmployeePosition::find($history->new_position_id);
                $oldDepartment = EmployeeDepartment::find($history->new_department_id);

                $item->employee_position = $oldPosition ? $oldPosition->position : null;
                $item->employee_department = $oldDepartment ? $oldDepartment->department : null;
            } else {
                // No transfer history found, get current position/department
                $employee = User::with(['department', 'position'])->find($item->user_id);
                $item->employee_position = $employee->position ? $employee->position->position : null;
                $item->employee_department = $employee->department ? $employee->department->department : null;
            }

            // Apply position filter
            if ($request->filled('position')) {
                if ($item->employee_position == $request->position) {
                    $filtered_requests[] = $item;
                }
            }
            // Apply department filter
            else if ($request->filled('department')) {
                if ($item->employee_department == $request->department) {
                    $filtered_requests[] = $item;
                }
            }
            // No position/department filter, include all
            else {
                $filtered_requests[] = $item;
            }
        }

        return $filtered_requests;
    }


    private function processHistoricalPositionDepartment($requests)
    {
        foreach ($requests as $item) {
            // Find the transfer history record closest to, but before the resign date
            $history = DB::table('users_transfer_history')
                ->where('users_id', $item->user_id)
                ->where('created_at', '<', $item->resign_date)  // Use resign_date
                ->orderBy('created_at', 'desc')
                ->first();

            if ($history) {
                // Get the historical position and department
                $oldPosition = EmployeePosition::find($history->new_position_id);
                $oldDepartment = EmployeeDepartment::find($history->new_department_id);

                $item->employee_position = $oldPosition ? $oldPosition->position : null;
                $item->employee_department = $oldDepartment ? $oldDepartment->department : null;
            } else {
                // No transfer history found, get current position/department
                $employee = User::with(['department', 'position'])->find($item->user_id);
                $item->employee_position = $employee->position ? $employee->position->position : null;
                $item->employee_department = $employee->department ? $employee->department->department : null;
            }
        }

        return $requests;
    }


    public function request_resign_create($id)
    {
        // Logic for create form
        return view('time_management/request_resign/create', compact('id'));
    }


    public function request_resign_edit($id)
    {
        $request_resign = RequestResign::findOrFail($id);
        // Authorize that current user can edit this request

        return view('time_management/request_resign/update', compact('request_resign'));
    }

    public function request_resign_store(Request $request)
    {

        // Validate request
        $validated = $request->validate([
            'resign_type' => 'required',
            'resign_date' => 'required|date',
            'resign_reason' => 'required',
            'file_reason' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
        ]);

        $temp = ($validated['resign_type'] == 'Other' && $request->other_reason != null)
            ? $request->other_reason
            : $validated['resign_type'];

        // Create new request resignation
        $resignation = RequestResign::create([
            'user_id' => $request->user_id,
            'resign_type' => $temp,
            'resign_date' => $validated['resign_date'],
            'resign_reason' => $validated['resign_reason'],
            'resign_status' => 'Pending',
        ]);

        // Handle file upload
        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');



            // Create filename using resignation ID
            $fileName = 'request_resign_' . $resignation->id . '_' . $request->user_id . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('time_management/resign', $fileName, 'public');

            // Update resignation record with file path
            $resignation->file_path = 'time_management/resign/' . $fileName;
            $resignation->save();
        }

        // Send email to user
        Mail::to(Auth::user()->email)->send(new ResignationRequestMail(Auth::user(), $validated['resign_date']));

        // Create notification
        Notification::create([
            'message' => 'You have submitted a resignation request. Please wait for management’s decision.',
            'type' => 'resign',
            'users_id' => $request->user_id,
            'maker_id' => $request->user_id,
            'status' => 'Unread',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('request.resign.index2', ['id' => $request->user_id])
            ->with('success', 'Resignation request submitted successfully.');
    }

    public function request_resign_update(Request $request, $id)
    {
        // Validate request

        $validated = $request->validate([
            'resign_type' => 'required',
            'resign_date' => 'required|date',
            'resign_reason' => 'required',
            'file_reason' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
        ]);



        $temp = ($validated['resign_type'] == 'Other' && $request->other_reason != null)
            ? $request->other_reason
            : $validated['resign_type'];

        // Find the resignation request
        $request_resign = RequestResign::findOrFail($id);

        // Update resignation request
        $request_resign->update([
            'resign_status' => 'Pending',
            'resign_type' => $temp,
            'resign_date' => $validated['resign_date'],
            'resign_reason' => $validated['resign_reason'],
            'updated_at' => now(),
        ]);

        // Handle file upload
        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');

            // Delete old file if exists
            if ($request_resign->file_path && Storage::disk('public')->exists($request_resign->file_path)) {
                Storage::disk('public')->delete($request_resign->file_path);
            }

            // Create filename using resignation ID
            $fileName = 'request_resign_' . $request_resign->id . '_' . $request_resign->user_id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('time_management/resign', $fileName, 'public');

            // Update resignation record with file path
            $request_resign->file_path = 'time_management/resign/' . $fileName;
            $request_resign->save();
        }

        // Create notification
        Notification::create([
            'message' => 'Your resignation request has been updated. Please wait for  management’s decision.',
            'type' => 'resign',
            'users_id' => $request_resign->user_id,
            'status' => 'Unread',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send email notification
        $user = User::findOrFail($request_resign->user_id);
        Mail::to($user->email)->send(new ResignationUpdatedMail($user, $request_resign));

        return redirect()->route('request.resign.index2', ['id' => $request_resign->user_id])
            ->with('success', 'Resignation request updated successfully.');
    }



    public function request_resign_destroy($id)
    {
        // Find resignation request
        $resignRequest = RequestResign::findOrFail($id);
        $userID = $resignRequest->user_id;

        // Send email notification before deletion
        $user = User::findOrFail($userID);
        Mail::to($user->email)->send(new ResignationDeletedMail($user));

        // Delete request
        $resignRequest->delete();

        return redirect()->route('request.resign.index2', ['id' => $userID])
            ->with('success', 'Resignation request has been cancelled successfully.');
    }



    // Approve Resignation Request
    public function request_resign_approve(Request $request, $id)
    {
        $resign_request = RequestResign::findOrFail($id);

        // Check if resignation date is today or in the past
        if (Carbon::now()->lt(Carbon::parse($resign_request->resign_date))) {
            return back()->with('error', 'Cannot approve requests with future resignation dates.');
        }

        // Get employee details
        $employee = User::findOrFail($resign_request->user_id);

        // Update request status
        $resign_request->update([
            'resign_status' => 'Approved',
            'response_user_id' => Auth::user()->id,
        ]);

        // Update employee status to Inactive
        $employee->update([
            'employee_status' => 'Inactive',
            'updated_at' => now()
        ]);

        // Create notification
        Notification::create([
            'message' => 'Your resignation request has been approved',
            'type' => 'resign',
            'users_id' => $resign_request->user_id,
            'status' => 'Unread',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send email notification
        if ($employee->email) {
            Mail::to($employee->email)->send(new ResignationApprovedMail($employee, $resign_request, Auth::user()->name));
        }

        return redirect()->route('request.resign.index')
            ->with('success', 'Resignation request approved successfully.');
    }

    // Decline Resignation Request
    public function request_resign_decline(Request $request, $id)
    {
        // Validate request
        $validated = $request->validate([
            'declined_reason' => 'required',
        ]);

        $resign_request = RequestResign::findOrFail($id);

        // Get employee details
        $employee = User::findOrFail($resign_request->user_id);

        // Update request status
        $resign_request->update([
            'resign_status' => 'Declined',
            'declined_reason' => $validated['declined_reason'],
            'response_user_id' => Auth::user()->id,

        ]);

        // Create notification
        Notification::create([
            'message' => 'Your resignation request has been declined',
            'type' => 'resign',
            'users_id' => $resign_request->user_id,
            'status' => 'Unread',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send email notification
        if ($employee->email) {
            Mail::to($employee->email)->send(new ResignationDeclinedMail($employee, $resign_request, Auth::user()->name, $validated['declined_reason']));
        }

        return redirect()->route('request.resign.index')
            ->with('success', 'Resignation request declined successfully.');
    }




    /**
     * Display Overtime
     */
    // Overtime Index - Main view with filters and tabs
    public function overtime_index(Request $request)
    {
        // Get filter parameters
        $employee = $request->input('employee', 'all');
        $overtimeType = $request->input('overtime_type', 'all');
        $date = $request->input('date');
        $department = $request->input('department_request');
        $position = $request->input('position_request');

        // Base query with proper joins
        $query = EmployeeOvertime::query()
            ->join('users', 'employee_overtime.user_id', '=', 'users.id')
            ->leftJoin('employee_departments as ed', function ($join) {
                $join->on('users.department_id', '=', 'ed.id');
            })
            ->leftJoin('employee_positions as ep', function ($join) {
                $join->on('users.position_id', '=', 'ep.id');
            })
            ->leftJoin('users as responder', 'employee_overtime.answer_user_id', '=', 'responder.id')
            ->select(
                'employee_overtime.*',
                'users.name as employee_name',
                'ep.position as current_position',
                'ed.department as current_department',
                'responder.name as response_name'
            );

        // Apply filters
        if ($employee !== 'all' && is_numeric($employee)) {
            $query->where('employee_overtime.user_id', $employee);
        }

        if ($overtimeType !== 'all') {
            $query->where('employee_overtime.overtime_type', $overtimeType);
        }

        if ($date) {
            $query->whereDate('employee_overtime.date', $date);
        }

        // Get pending, approved and declined requests
        $pendingQuery = clone $query;
        $approvedQuery = clone $query;
        $declinedQuery = clone $query;

        $pendingRequests = $pendingQuery->where('employee_overtime.approval_status', 'Pending')->get();
        $approvedRequests = $approvedQuery->where('employee_overtime.approval_status', 'Approved')->get();
        $declinedRequests = $declinedQuery->where('employee_overtime.approval_status', 'Declined')->get();

        // All requests combined for processing
        $allRequests = collect()->merge($pendingRequests)
            ->merge($approvedRequests)
            ->merge($declinedRequests);

        // Process each record to find historical position and department
        foreach ($allRequests as $record) {
            // Find the closest historical transfer record before the overtime request
            $historyRecord = history_transfer_employee::where('users_id', $record->user_id)
                ->where('created_at', '<', $record->created_at)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($historyRecord) {
                // Use the historical position and department
                $histPosition = EmployeePosition::find($historyRecord->new_position_id);
                $histDepartment = EmployeeDepartment::find($historyRecord->new_department_id);

                $record->employee_position = $histPosition ? $histPosition->position : $record->current_position;
                $record->employee_department = $histDepartment ? $histDepartment->department : $record->current_department;
            } else {
                // No history record found, use current position and department
                $record->employee_position = $record->current_position;
                $record->employee_department = $record->current_department;
            }
        }

        // Apply position and department filters
        if ($position) {
            $pendingRequests = $pendingRequests->filter(function ($record) use ($position) {
                return $record->employee_position == $position;
            })->values();

            $approvedRequests = $approvedRequests->filter(function ($record) use ($position) {
                return $record->employee_position == $position;
            })->values();

            $declinedRequests = $declinedRequests->filter(function ($record) use ($position) {
                return $record->employee_position == $position;
            })->values();
        }

        if ($department) {
            $pendingRequests = $pendingRequests->filter(function ($record) use ($department) {
                return $record->employee_department == $department;
            })->values();

            $approvedRequests = $approvedRequests->filter(function ($record) use ($department) {
                return $record->employee_department == $department;
            })->values();

            $declinedRequests = $declinedRequests->filter(function ($record) use ($department) {
                return $record->employee_department == $department;
            })->values();
        }

        // Get all employees for filter dropdown
        $employees = User::where('employee_status', '!=', 'Inactive')->get();

        // Get unique departments and positions from their tables
        $departments = EmployeeDepartment::distinct()->pluck('department');
        $positions = EmployeePosition::distinct()->pluck('position');

        return view('time_management.overtime.index', compact(
            'pendingRequests',
            'approvedRequests',
            'declinedRequests',
            'employees',
            'departments',
            'positions',
            'employee',
            'overtimeType',
            'date'
        ));
    }

    public function overtime_index2($id)
    {
        $query = EmployeeOvertime::query()
            ->join('users', 'employee_overtime.user_id', '=', 'users.id')
            ->leftJoin('employee_departments as ed', 'users.department_id', '=', 'ed.id')
            ->leftJoin('employee_positions as ep', 'users.position_id', '=', 'ep.id')
            ->leftJoin('users as responder', 'employee_overtime.answer_user_id', '=', 'responder.id')
            ->select(
                'employee_overtime.*',
                'users.name as employee_name',
                'ep.position as current_position',
                'ed.department as current_department',
                'responder.name as response_name'
            )
            ->where('employee_overtime.user_id', $id);

        // Get the results separately for each status
        $pendingRequests = (clone $query)->where('employee_overtime.approval_status', 'Pending')->get();
        $approvedRequests = (clone $query)->where('employee_overtime.approval_status', 'Approved')->get();
        $declinedRequests = (clone $query)->where('employee_overtime.approval_status', 'Declined')->get();

        // All requests combined for processing
        $allRequests = collect()->merge($pendingRequests)
            ->merge($approvedRequests)
            ->merge($declinedRequests);

        // Process each record to find historical position and department
        foreach ($allRequests as $record) {
            // Find the closest historical transfer record before the overtime request
            $historyRecord = history_transfer_employee::where('users_id', $record->user_id)
                ->where('created_at', '<', $record->created_at)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($historyRecord) {
                // Use the historical position and department
                $histPosition = EmployeePosition::find($historyRecord->new_position_id);
                $histDepartment = EmployeeDepartment::find($historyRecord->new_department_id);

                $record->employee_position = $histPosition ? $histPosition->position : $record->current_position;
                $record->employee_department = $histDepartment ? $histDepartment->department : $record->current_department;
            } else {
                // No history record found, use current position and department
                $record->employee_position = $record->current_position;
                $record->employee_department = $record->current_department;
            }
        }

        $employee = User::with(['department', 'position'])->find($id);

        return view('time_management.overtime.index2', compact(
            'pendingRequests',
            'approvedRequests',
            'declinedRequests',
            'employee'
        ));
    }


    // Show overtime request creation form
    public function overtime_create($id)
    {
        $employee = User::findOrFail($id);
        return view('time_management.overtime.create', compact('employee'));
    }

    public function checkEligibility(Request $request)
    {
        $userId = $request->user_id;
        $date = $request->date;

        // Convert to Carbon instance
        $selectedDate = Carbon::parse($date);

        // Check if the user already has an overtime request for the same date
        $existingOvertime = EmployeeOvertime::where('user_id', $userId)
            ->whereDate('date', $selectedDate)
            ->exists(); // Check if any record exists

        if ($existingOvertime) {
            return response()->json([
                'eligible' => false,
                'message' => 'You already have an overtime request for this date. Please cancel it first'
            ]);
        }




        // 1. Check if the date falls within an employee shift
        $employeeShift = EmployeeShift::where('user_id', $userId)
            ->whereDate('start_date', '<=', $selectedDate)
            ->where(function ($query) use ($selectedDate) {
                $query->whereDate('end_date', '>=', $selectedDate)
                    ->orWhereNull('end_date');
            })
            ->first();

        if (!$employeeShift) {
            return response()->json([
                'eligible' => false,
                'message' => 'No active shift found for this date.'
            ]);
        }

        // 2. Get the rule_id and find shift details
        $ruleId = $employeeShift->rule_id;


        $ruleShift = rule_shift::find($ruleId);

        if (!$ruleShift) {
            return response()->json([
                'eligible' => false,
                'message' => 'Shift rules not found.'
            ]);
        }

        // Extract shift information (from rule_shift JSON data)
        $shiftType = $ruleShift->type; // Morning, Afternoon, Normal

        // Parse hour_end JSON to get the end time for this day of week
        $dayOfWeek = ucfirst($selectedDate->format('l')); // Outputs "Tuesday"

        $hourEndData = json_decode($ruleShift->hour_end, true);
        $hourStartData = json_decode($ruleShift->hour_start, true);

        // Find the relevant time for this day of week
        $shiftEndTime = null;
        $shiftStartTime = null;

        foreach ($hourEndData as $index => $time) {
            // Check if this time corresponds to the current day
            if (in_array($dayOfWeek, json_decode($ruleShift->days, true))) {
                $shiftEndTime = $time;
                $shiftStartTime = $hourStartData[$index];
                break;
            }
        }



        if (!$shiftEndTime) {
            return response()->json([
                'eligible' => false,
                'message' => 'No shift end time found for this day of week.'
            ]);
        }

        // 3. Check existing overtime requests for this user

        // a. Check daily overtime (max 4 hours per day)
        $dailyOvertime = EmployeeOvertime::where('user_id', $userId)
            ->whereDate('date', $selectedDate)
            ->whereIn('approval_status', ['Pending', 'Approved'])
            ->sum('total_hours');

        $remainingDailyHours = 4 - $dailyOvertime;

        if ($remainingDailyHours <= 0) {
            return response()->json([
                'eligible' => false,
                'message' => 'You have already requested or been approved for the maximum daily overtime (4 hours).'
            ]);
        }

        // b. Check weekly overtime (max 18 hours per week)
        $weekStart = $selectedDate->copy()->startOfWeek();
        $weekEnd = $selectedDate->copy()->endOfWeek();

        $weeklyOvertime = EmployeeOvertime::where('user_id', $userId)
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->whereIn('approval_status', ['Pending', 'Approved'])
            ->sum('total_hours');

        $remainingWeeklyHours = 18 - $weeklyOvertime;

        if ($remainingWeeklyHours <= 0) {
            return response()->json([
                'eligible' => false,
                'message' => 'You have already reached the maximum weekly overtime (18 hours).'
            ]);
        }

        // c. Check monthly overtime (max 56 hours per month)
        $monthStart = $selectedDate->copy()->startOfMonth();
        $monthEnd = $selectedDate->copy()->endOfMonth();

        $monthlyOvertime = EmployeeOvertime::where('user_id', $userId)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->whereIn('approval_status', ['Pending', 'Approved'])
            ->sum('total_hours');

        $remainingMonthlyHours = 56 - $monthlyOvertime;

        if ($remainingMonthlyHours <= 0) {
            return response()->json([
                'eligible' => false,
                'message' => 'You have already reached the maximum monthly overtime (56 hours).'
            ]);
        }

        // 4. If all checks pass, return eligibility status and shift details
        return response()->json([
            'eligible' => true,
            'shift_end_time' => $shiftEndTime,
            'shift_info' => [
                'type' => $shiftType,
                'start_time' => $shiftStartTime,
                'end_time' => $shiftEndTime
            ],
            'remaining' => [
                'daily' => $remainingDailyHours,
                'weekly' => $remainingWeeklyHours,
                'monthly' => $remainingMonthlyHours
            ]
        ]);
    }

    // Store a new overtime request
    public function overtime_store(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'user_id' => 'required',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'total_hours' => 'required|numeric',
            'reason' => 'required|string',
            'overtime_type' => 'required|in:Paid_Overtime,Overtime_Leave',
        ]);

        // Create overtime request
        $overtime = EmployeeOvertime::create([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'total_hours' => $request->total_hours,
            'reason' => $request->reason,
            'overtime_type' => $request->overtime_type,
            'approval_status' => 'Pending',
        ]);

        // Get the employee who submitted the request
        $employee = User::find($request->user_id);

        // Find HR personnel that are active
        $hr_personnel = User::whereHas('department', function ($query) {
            $query->where('department', 'Human Resources');
        })
            ->where('employee_status', '!=', 'Inactive')
            ->get();

        // Create notification for each HR personnel
        foreach ($hr_personnel as $hr) {
            Notification::create([
                'users_id' => $hr->id,
                'message' => "New overtime request from {$employee->name}.",
                'type' => 'overtime_request',
                'maker_id' => $request->user_id,
                'status' => 'Unread'
            ]);

            // Send email notification to HR
            Mail::to($hr->email)->send(new OvertimeRequestMail($overtime, $employee));
        }

        return redirect()->route('overtime.index2', ['id' => $request->user_id])
            ->with('success', 'Overtime request submitted successfully');
    }

    // Approve an overtime request
    // Updated Overtime Approval Function with Two-level Approval
    public function overtime_approve(Request $request, $id)
    {
        $overtime = EmployeeOvertime::findOrFail($id);
        $currentUser = Auth::user();

        // Check if current user is a department head or super admin
        $isDeptHead = User::whereHas('position', function ($query) {
            $query->where('position', 'like', '%Manager%')->where('position', 'not like', '%HR Manager%')
                ->where('position', 'not like', '%General Manager%');
        })->where('id', $currentUser->id)->exists();

        $isSuperAdmin = User::whereHas('position', function ($query) {
            $query->where('position', 'like', '%HR Manager%')
                ->orWhere('position', 'like', '%General Manager%')
                ->orWhere('position', 'like', '%Director%');
        })->where('id', $currentUser->id)->exists();

        // Process approval based on user role
        if ($isDeptHead && $overtime->dept_approval_status === 'Pending') {
            // Department head approval
            $overtime->dept_approval_status = 'Approved';
            $overtime->dept_approval_user_id = $currentUser->id;
            $overtime->approval_level = 1;
            $overtime->save();

            // Check if request is now fully approved (both levels approved)
            $this->checkOvertimeApprovalStatus($overtime);

            return redirect()->back()->with('success', 'First level approval completed. Waiting for final approval.');
        } elseif ($isSuperAdmin && $overtime->admin_approval_status === 'Pending') {
            // Super admin approval
            $overtime->admin_approval_status = 'Approved';
            $overtime->admin_approval_user_id = $currentUser->id;
            $overtime->approval_level = max(1, $overtime->approval_level);
            $overtime->save();

            // Check if request is now fully approved (both levels approved)
            $this->checkOvertimeApprovalStatus($overtime);

            return redirect()->back()->with('success', 'Admin approval completed. ' .
                ($overtime->dept_approval_status === 'Approved' ? 'Request is now fully approved.' : 'Waiting for department approval.'));
        } else {
            return redirect()->back()->with('error', 'You do not have permission to approve this request or it was already approved by your level.');
        }
    }

    // Helper function to check overall approval status
    private function checkOvertimeApprovalStatus(EmployeeOvertime $overtime)
    {
        // If both levels approved, update the main approval status
        if ($overtime->dept_approval_status === 'Approved' && $overtime->admin_approval_status === 'Approved') {
            $overtime->approval_status = 'Approved';
            $overtime->answer_user_id = $overtime->admin_approval_user_id; // Use admin as final approver
            $overtime->updated_at = now();
            $overtime->save();

            // Get the employee who submitted the request
            $employee = User::find($overtime->user_id);

            // Create notification for the employee
            Notification::create([
                'users_id' => $overtime->user_id,
                'message' => "Your overtime request for {$overtime->date} has been fully approved.",
                'type' => 'overtime_approved',
                'maker_id' => Auth::id(),
                'status' => 'Unread'
            ]);

            // Send email notification to employee
            Mail::to($employee->email)->send(new OvertimeApprovedMail($overtime, $employee));
        }
    }

    // Updated Overtime Decline Function (either level can decline)
    public function overtime_decline(Request $request, $id)
    {
        $request->validate([
            'declined_reason' => 'required|string',
        ]);

        $overtime = EmployeeOvertime::findOrFail($id);
        $currentUser = Auth::user();

        // Check if current user is a department head or super admin
        $isDeptHead = User::whereHas('position', function ($query) {
            $query->where('position', 'like', '%Manager%')->where('position', 'not like', '%HR Manager%')
                ->where('position', 'not like', '%General Manager%');
        })->where('id', $currentUser->id)->exists();

        $isSuperAdmin = User::whereHas('position', function ($query) {
            $query->where('position', 'like', '%HR Manager%')
                ->orWhere('position', 'like', '%General Manager%')
                ->orWhere('position', 'like', '%Director%');
        })->where('id', $currentUser->id)->exists();

        // Only department heads or super admins can decline
        if ($isDeptHead || $isSuperAdmin) {
            $overtime->approval_status = 'Declined';
            $overtime->declined_reason = $request->declined_reason;
            $overtime->answer_user_id = Auth::id();
            $overtime->updated_at = now();

            // Also update the specific level that did the declining
            if ($isDeptHead) {
                $overtime->dept_approval_status = 'Declined';
                $overtime->dept_approval_user_id = $currentUser->id;
            } else {
                $overtime->admin_approval_status = 'Declined';
                $overtime->admin_approval_user_id = $currentUser->id;
            }

            $overtime->save();

            // Get the employee who submitted the request
            $employee = User::find($overtime->user_id);

            // Create notification for the employee
            Notification::create([
                'users_id' => $overtime->user_id,
                'message' => "Your overtime request for {$overtime->date} has been declined.",
                'type' => 'overtime_declined',
                'maker_id' => Auth::id(),
                'status' => 'Unread'
            ]);

            // Send email notification to employee
            Mail::to($employee->email)->send(new OvertimeDeclinedMail($overtime, $employee));

            return redirect()->back()->with('success', 'Overtime request declined successfully');
        }

        return redirect()->back()->with('error', 'You do not have permission to decline this request.');
    }




    // Delete an overtime request
    public function overtime_destroy($id)
    {
        $overtime = EmployeeOvertime::findOrFail($id);

        // Only allow deletion for pending requests
        if ($overtime->approval_status != 'Pending') {
            return redirect()->back()->with('error', 'Only pending requests can be deleted');
        }

        // Get the employee who submitted the request
        $employee = User::find($overtime->user_id);

        // Find HR personnel that are active
        $hr_personnel = User::whereHas('department', function ($query) {
            $query->where('department', 'Human Resources');
        })
            ->where('employee_status', '!=', 'Inactive')
            ->get();

        // Create notification for each HR personnel
        foreach ($hr_personnel as $hr) {
            Notification::create([
                'users_id' => $hr->id,
                'message' => "Overtime request from {$employee->name} has been cancelled.",
                'type' => 'overtime_cancelled',
                'maker_id' => $overtime->user_id,
                'status' => 'Unread'
            ]);

            // Send email notification to HR personnel about the cancellation
            Mail::to($hr->email)->send(new OvertimeCancelledMail($overtime, $employee));
        }

        $overtime->delete();
        return redirect()->back()->with('success', 'Overtime request deleted successfully');
    }
    /**
     * Time Off
     */

    public function time_off_policy_index(Request $request)
    {
        $policies = TimeOffPolicy::query();

        // Apply filtering if there is a search query
        if ($request->has('name') && $request->name != '') {
            $policies->where('time_off_name', $request->name);
        }

        $policies = $policies->get();

        // Get unique policy names for dropdown
        $policyNames = TimeOffPolicy::select('time_off_name')->distinct()->pluck('time_off_name');

        return view('time_management/time_off/policy/index', compact('policies', 'policyNames'));
    }


    public function time_off_policy_create()
    {
        return view('time_management/time_off/policy/create');
    }


    public function time_off_policy_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'time_off_name' => 'required|string|max:255',
            'time_off_description' => 'required|string',
            'quota' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'requires_time_input' => 'nullable'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Prepare data and handle nullable end_date
        $data = $request->only([
            'time_off_name',
            'time_off_description',
            'quota',
            'start_date',
            'requires_time_input' // Ambil nilai dari form
        ]);
        $data['end_date'] = $request->end_date ?: null;
        $data['requires_time_input'] = $request->has('requires_time_input'); // Konversi ke boolean

        TimeOffPolicy::create($data);



        return redirect()->route('time.off.policy.index')
            ->with('success', 'Time off policy has been created successfully.');
    }

    public function time_off_policy_edit($id)
    {
        $policy = TimeOffPolicy::findOrFail($id);
        return view('time_management/time_off/policy/update', compact('policy'));
    }

    public function time_off_policy_update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'time_off_name' => 'required|string|max:255',
            'time_off_description' => 'required|string',
            'quota' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'requires_time_input' => 'nullable'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $policy = TimeOffPolicy::findOrFail($id);

        $data = $request->only([
            'time_off_name',
            'time_off_description',
            'quota',
            'start_date',
            'requires_time_input' // Include this field
        ]);

        $data['end_date'] = $request->end_date ?: null;
        $data['requires_time_input'] = $request->has('requires_time_input'); // Convert checkbox to boolean

        $policy->update($data);

        return redirect()->route('time.off.policy.index')
            ->with('success', 'Time off policy has been updated successfully.');
    }


    public function time_off_assign_index(Request $request)
    {
        $query = DB::table('time_off_assign')
            ->join('users', 'time_off_assign.user_id', '=', 'users.id')
            ->leftJoin('employee_departments as ed', 'users.department_id', '=', 'ed.id')
            ->leftJoin('employee_positions as ep', 'users.position_id', '=', 'ep.id')
            ->join('time_off_policy', 'time_off_assign.time_off_id', '=', 'time_off_policy.id')
            ->select(
                'time_off_assign.id',
                'users.name as employee_name',
                'users.employee_id',
                'ed.department as department_name',
                'ep.position as position_name',
                'time_off_policy.time_off_name',
                'time_off_policy.quota',
                'time_off_assign.balance',
                'time_off_assign.created_at'
            );

        // Apply filters if provided
        if ($request->filled('employee')) {
            $query->where('users.id', $request->employee);
        }

        if ($request->filled('department')) {
            $query->where('ed.department', $request->department);
        }

        if ($request->filled('position')) {
            $query->where('ep.position', $request->position);
        }

        if ($request->filled('time_off_type')) {
            $query->where('time_off_policy.id', $request->time_off_type);
        }

        $timeOffAssignments = $query->orderBy('users.name')->paginate(10);

        // Get data for filter dropdowns
        $employees = User::select('id', 'name')->orderBy('name')->get();
        $departments = EmployeeDepartment::select('department')->distinct()->orderBy('department')->pluck('department');
        $positions = EmployeePosition::select('position')->distinct()->orderBy('position')->pluck('position');
        $timeOffPolicies = DB::table('time_off_policy')->select('id', 'time_off_name')->orderBy('time_off_name')->get();

        return view('time_management.time_off.assign.index', compact(
            'timeOffAssignments',
            'employees',
            'departments',
            'positions',
            'timeOffPolicies'
        ));
    }

    public function time_off_assign_create()
    {
        // Get time off policies with their quotas
        $timeOffPolicies = DB::table('time_off_policy')->get();

        // Get all active employees with their relationships
        $employees = User::with(['department', 'position'])
            ->where('employee_status', '!=', 'Inactive')
            ->get();

        // Get departments and positions for filtering from their respective tables
        $departments = EmployeeDepartment::select('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        $positions = EmployeePosition::select('position')
            ->distinct()
            ->orderBy('position')
            ->pluck('position');

        return view(
            'time_management.time_off.assign.create',
            compact('timeOffPolicies', 'employees', 'departments', 'positions')
        );
    }

    public function time_off_assign_store(Request $request)
    {
        // Validate request
        $request->validate([
            'time_off_id' => 'required|exists:time_off_policy,id',
            'balance' => 'required|numeric|min:0',
            'invited_employees' => 'required'
        ]);

        $timeOffId = $request->input('time_off_id');
        $balance = $request->input('balance');
        $employeeIds = explode(',', $request->input('invited_employees'));

        // Check policy quota limit
        $policy = DB::table('time_off_policy')->where('id', $timeOffId)->first();
        if ($policy && $balance > $policy->quota) {
            return redirect()->back()->with('error', 'Balance exceeds the maximum allowed for this policy')->withInput();
        }

        // Get existing assignments for this policy
        $existingAssignments = DB::table('time_off_assign')
            ->where('time_off_id', $timeOffId)
            ->pluck('user_id')
            ->toArray();

        $successCount = 0;
        $skippedCount = 0;

        foreach ($employeeIds as $employeeId) {
            // Skip if this employee already has this policy
            if (in_array($employeeId, $existingAssignments)) {
                $skippedCount++;
                continue;
            }

            // Insert new assignment
            DB::table('time_off_assign')->insert([
                'time_off_id' => $timeOffId,
                'user_id' => $employeeId,
                'balance' => $balance,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Get employee data
            $employee = User::find($employeeId);

            // Create notification
            Notification::create([
                'users_id' => $employeeId,
                'message' => "You have been assigned {$policy->time_off_name} with a balance of {$balance} days.",
                'type' => 'time_off_assign',
                'maker_id' => Auth::user()->id,
                'status' => 'Unread'
            ]);

            // Send email
            if ($employee && $employee->email) {
                $mailData = [
                    'employee_name' => $employee->name,
                    'time_off_name' => $policy->time_off_name,
                    'balance' => $balance
                ];

                Mail::to($employee->email)->send(new TimeOffAssignCreateMail($mailData));
            }

            $successCount++;
        }

        $message = "{$successCount} employees assigned successfully.";
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} employees were skipped because they already have this policy.";
        }

        return redirect()->route('time.off.assign.index')->with('success', $message);
    }

    public function time_off_assign_update(Request $request, $id)
    {
        $request->validate([
            'balance' => 'required|numeric|min:0'
        ]);

        // Get the time off assignment
        $assignment = DB::table('time_off_assign')
            ->join('time_off_policy', 'time_off_assign.time_off_id', '=', 'time_off_policy.id')
            ->join('users', 'time_off_assign.user_id', '=', 'users.id')
            ->where('time_off_assign.id', $id)
            ->select(
                'time_off_assign.*',
                'time_off_policy.quota',
                'time_off_policy.time_off_name',
                'users.name as employee_name',
                'users.email'
            )
            ->first();

        // Check if balance exceeds quota
        if ($request->balance > $assignment->quota) {
            return back()->with('error', 'Balance cannot exceed the maximum quota.');
        }

        $oldBalance = $assignment->balance;
        $newBalance = $request->balance;

        // Update the balance
        DB::table('time_off_assign')
            ->where('id', $id)
            ->update([
                'balance' => $newBalance,
                'updated_at' => now()
            ]);

        // Create notification
        Notification::create([
            'users_id' => $assignment->user_id,
            'message' => "Your {$assignment->time_off_name} balance has been updated from {$oldBalance} to {$newBalance} days.",
            'type' => 'time_off_update',
            'maker_id' => Auth::user()->id,
            'status' => 'Unread'
        ]);

        // Send email
        if ($assignment->email) {
            $mailData = [
                'employee_name' => $assignment->employee_name,
                'time_off_name' => $assignment->time_off_name,
                'old_balance' => $oldBalance,
                'new_balance' => $newBalance
            ];

            Mail::to($assignment->email)->send(new TimeOffAssignUpdateMail($mailData));
        }

        return redirect()->route('time.off.assign.index')
            ->with('success', 'Time off balance updated successfully.');
    }

    public function time_off_assign_destroy($id)
    {
        // Get the time off assignment details before deletion
        $assignment = DB::table('time_off_assign')
            ->join('time_off_policy', 'time_off_assign.time_off_id', '=', 'time_off_policy.id')
            ->join('users', 'time_off_assign.user_id', '=', 'users.id')
            ->where('time_off_assign.id', $id)
            ->select(
                'time_off_assign.*',
                'time_off_policy.time_off_name',
                'users.name as employee_name',
                'users.email'
            )
            ->first();

        if ($assignment) {
            // Create notification
            Notification::create([
                'users_id' => $assignment->user_id,
                'message' => "Your {$assignment->time_off_name} assignment has been removed.",
                'type' => 'time_off_delete',
                'maker_id' => Auth::user()->id,
                'status' => 'Unread'
            ]);

            // Send email
            if ($assignment->email) {
                $mailData = [
                    'employee_name' => $assignment->employee_name,
                    'time_off_name' => $assignment->time_off_name
                ];

                Mail::to($assignment->email)->send(new TimeOffAssignDestroyMail($mailData));
            }

            // Delete the time off assignment
            DB::table('time_off_assign')->where('id', $id)->delete();
        }

        return redirect()->route('time.off.assign.index')
            ->with('success', 'Time off assignment deleted successfully.');
    }



    public function getAssignedEmployees(Request $request)
    {
        $timeOffId = $request->input('time_off_id');

        if (!$timeOffId) {
            return response()->json(['employees' => []]);
        }

        // Get all user IDs who already have this time off policy
        $assignedEmployees = DB::table('time_off_assign')
            ->where('time_off_id', $timeOffId)
            ->pluck('user_id')
            ->toArray();

        return response()->json(['employees' => $assignedEmployees]);
    }


    public function getTimeOffPolicyQuota(Request $request)
    {
        $timeOffId = $request->input('time_off_id');

        if (!$timeOffId) {
            return response()->json(['quota' => 0]);
        }

        $policy = DB::table('time_off_policy')
            ->where('id', $timeOffId)
            ->first();

        return response()->json([
            'quota' => $policy ? $policy->quota : 0,
            'name' => $policy ? $policy->time_off_name : ''
        ]);
    }









    public function request_time_off_index2($id)
    {
        $employee = User::findOrFail($id);

        // Get time off requests for this employee
        $timeOffRequests = RequestTimeOff::with(['deptApprovalUser', 'adminApprovalUser'])
            ->where('user_id', $id)
            ->get();

        // Process each record to find historical position and department
        foreach ($timeOffRequests as $record) {
            // Find the closest historical transfer record before the time off request
            $historyRecord = history_transfer_employee::where('users_id', $record->user_id)
                ->where('created_at', '<', $record->created_at)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($historyRecord) {
                // Use the historical position and department
                $position = EmployeePosition::find($historyRecord->new_position_id);
                $department = EmployeeDepartment::find($historyRecord->new_department_id);

                $record->historical_position = $position ? $position->position : null;
                $record->historical_department = $department ? $department->department : null;
            } else {
                // No history record found, use current position and department
                $record->historical_position = $employee->position->position ?? null;
                $record->historical_department = $employee->department->department ?? null;
            }

            // Store approver names
            if ($record->deptApprovalUser) {
                $record->dept_approver_name = $record->deptApprovalUser->name;
            }

            if ($record->adminApprovalUser) {
                $record->admin_approver_name = $record->adminApprovalUser->name;
            }
        }

        // Split requests by status and format them
        $pendingRequests = $timeOffRequests->where('status', 'Pending')
            ->sortByDesc('created_at')
            ->values()
            ->map(function ($item) {
                return $this->formatTimeOffRequest($item);
            });

        $approvedRequests = $timeOffRequests->where('status', 'Approved')
            ->sortByDesc('created_at')
            ->values()
            ->map(function ($item) {
                return $this->formatTimeOffRequest($item);
            });

        $declinedRequests = $timeOffRequests->where('status', 'Declined')
            ->sortByDesc('created_at')
            ->values()
            ->map(function ($item) {
                return $this->formatTimeOffRequest($item);
            });

        // Get time off assignments with policy information
        $timeOffAssignments = DB::table('time_off_assign')
            ->join('time_off_policy', 'time_off_assign.time_off_id', '=', 'time_off_policy.id')
            ->where('time_off_assign.user_id', $id)
            ->select(
                'time_off_assign.id',
                'time_off_assign.time_off_id',
                'time_off_assign.balance',
                'time_off_policy.time_off_name',
                'time_off_policy.time_off_description',
                'time_off_policy.quota'
            )
            ->get();

        return view('time_management.time_off.request_time_off.index2', compact(
            'employee',
            'pendingRequests',
            'approvedRequests',
            'declinedRequests',
            'timeOffAssignments'
        ));
    }


    public function request_time_off_index(Request $request)
    {
        $currentUser = Auth::user();

        // Only allow Manager and above (position_id <= 3)
        if ($currentUser->position_id > 3) { // Supervisor (4) or Staff (5)
            abort(403, 'Access denied. Only managers and above can access this page.');
        }

        // Get all users with their relationships
        $users = User::with(['department', 'position'])
            ->orderBy('name')
            ->get();

        // Get unique departments and positions from their respective tables
        $departments = EmployeeDepartment::distinct()
            ->orderBy('department')
            ->pluck('department');

        $positions = EmployeePosition::distinct()
            ->orderBy('position')
            ->pluck('position');

        $timeOffPolicies = TimeOffPolicy::orderBy('time_off_name')->get();

        // Build the query with proper joins
        $query = RequestTimeOff::query()
            ->join('users as u1', 'request_time_off.user_id', '=', 'u1.id')
            ->leftJoin('users as dept_approver', 'request_time_off.dept_approval_user_id', '=', 'dept_approver.id')
            ->leftJoin('users as admin_approver', 'request_time_off.admin_approval_user_id', '=', 'admin_approver.id')
            ->join('time_off_policy', 'request_time_off.time_off_id', '=', 'time_off_policy.id')
            ->leftJoin('time_off_assign', function ($join) {
                $join->on('request_time_off.user_id', '=', 'time_off_assign.user_id')
                    ->on('request_time_off.time_off_id', '=', 'time_off_assign.time_off_id');
            })
            ->select(
                'request_time_off.*',
                'u1.name as user_name',
                'time_off_policy.time_off_name as time_off_name',
                DB::raw('NULL as department'), // Will be filled later
                DB::raw('NULL as position'), // Will be filled later
                'dept_approver.name as dept_approver_name',
                'admin_approver.name as admin_approver_name'
            );

        // No user should see their own requests
        $query->where('request_time_off.user_id', '!=', $currentUser->id);

        // Permission-based filtering
        if ($this->timeOffIsDepartmentManager($currentUser)) {
            // Department Manager can only see requests from their own department's staff/supervisors
            $query->whereHas('user', function ($q) use ($currentUser) {
                $q->where('department_id', $currentUser->department_id)
                    ->where('position_id', '>', 3); // Only Staff (5) or Supervisor (4)
            });
        } elseif (!$this->timeOffIsAdminUser($currentUser)) {
            // If not admin and not department manager, they shouldn't see any requests
            // (this is a safety check as we already have the position check at the top)
            abort(403, 'Access denied.');
        }
        // Admin can see all requests except their own (already filtered above)

        // Apply employee filter
        if ($request->filled('employee')) {
            $query->where('request_time_off.user_id', $request->employee);
        }

        // Apply date filter
        if ($request->filled('date')) {
            $date = $request->date;
            $query->where(function ($q) use ($date) {
                $q->where('request_time_off.start_date', '<=', $date)
                    ->where('request_time_off.end_date', '>=', $date);
            });
        }

        // Apply time off type filter
        if ($request->filled('time_off_type')) {
            $query->where('request_time_off.time_off_id', $request->time_off_type);
        }

        // Get results
        $results = $query->get();

        // Process each record to find historical position and department
        foreach ($results as $record) {
            // Find the closest historical transfer record before the time off request
            $historyRecord = history_transfer_employee::where('users_id', $record->user_id)
                ->where('created_at', '<', $record->created_at)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($historyRecord) {
                // Use the historical position and department
                $position = EmployeePosition::find($historyRecord->new_position_id);
                $department = EmployeeDepartment::find($historyRecord->new_department_id);

                $record->position = $position ? $position->position : null;
                $record->department = $department ? $department->department : null;
            } else {
                // No history record found, use current position and department
                $user = User::with(['department', 'position'])->find($record->user_id);
                $record->position = $user->position->position ?? null;
                $record->department = $user->department->department ?? null;
            }

            // Add approval permissions
            $record->can_approve_dept = $this->timeOffCanApproveDept($currentUser, $record);
            $record->can_approve_admin = $this->timeOffCanApproveAdmin($currentUser, $record);
            $record->can_decline = $this->timeOffCanDecline($currentUser, $record);
        }

        // Apply position and department filters after historical data is determined
        if ($request->filled('position_request')) {
            $results = $results->filter(function ($record) use ($request) {
                return $record->position == $request->position_request;
            });
        }

        if ($request->filled('department_request')) {
            $results = $results->filter(function ($record) use ($request) {
                return $record->department == $request->department_request;
            });
        }

        // Count statuses for the tabs
        $pendingCount = $results->where('status', 'Pending')->count();
        $approvedCount = $results->where('status', 'Approved')->count();
        $declinedCount = $results->where('status', 'Declined')->count();

        // Get requests by status and format them
        $pendingRequests = $results->where('status', 'Pending')
            ->sortByDesc('created_at')
            ->values()
            ->map(function ($item) {
                return $this->formatTimeOffRequest($item);
            });

        $approvedRequests = $results->where('status', 'Approved')
            ->sortByDesc('updated_at')
            ->values()
            ->map(function ($item) {
                return $this->formatTimeOffRequest($item);
            });

        $declinedRequests = $results->where('status', 'Declined')
            ->sortByDesc('updated_at')
            ->values()
            ->map(function ($item) {
                return $this->formatTimeOffRequest($item);
            });

        return view('time_management/time_off/request_time_off/index', compact(
            'pendingRequests',
            'approvedRequests',
            'declinedRequests',
            'users',
            'positions',
            'departments',
            'timeOffPolicies',
            'pendingCount',
            'approvedCount',
            'declinedCount'
        ));
    }




    protected function formatTimeOffRequest($request)
    {
        // First, process the historical department and position if not already set
        if (!isset($request->historical_department) && !isset($request->historical_position)) {
            // Find historical position and department at the time of request creation
            $historyRecord = history_transfer_employee::where('users_id', $request->user_id)
                ->where('created_at', '<', $request->created_at)
                ->orderBy('created_at', 'desc')
                ->first();
    
            if ($historyRecord) {
                // Use historical position and department
                $position = EmployeePosition::find($historyRecord->new_position_id);
                $department = EmployeeDepartment::find($historyRecord->new_department_id);
    
                $request->historical_position = $position ? $position->position : null;
                $request->historical_department = $department ? $department->department : null;
            } else {
                // No history found, so current values will be used
                // But we'll set these properties to null to show they weren't found
                $request->historical_position = null;
                $request->historical_department = null;
            }
        }
    
        // Your original time formatting logic
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
    
        // Check if this is a full day request using requires_time_input
        $timeOffPolicy = null;
        if (isset($request->time_off_id)) {
            $timeOffPolicy = TimeOffPolicy::find($request->time_off_id);
        }
        $isFullDay = $timeOffPolicy ? $timeOffPolicy->requires_time_input == 0 : false;
    
        $request->formatted_start_date = $isFullDay
            ? $start->format('d-m-Y')
            : $start->format('d-m-Y H:i');
    
        $request->formatted_end_date = $isFullDay
            ? $end->format('d-m-Y')
            : $end->format('d-m-Y H:i');
    
        // Calculate duration
        if ($isFullDay) {
            // Untuk memastikan hasil bulat, kita gunakan startOfDay dan endOfDay
            $startDay = $start->copy()->startOfDay();
            $endDay = $end->copy()->endOfDay();
            
            // Hitung selisih hari dan bulatkan ke bilangan bulat
            $days = $startDay->diffInDays($endDay->startOfDay()) + 1;
            $request->duration = $days . ' day' . ($days > 1 ? 's' : '');
        } else {
            $diff = $start->diff($end);
    
            $parts = [];
            if ($diff->d > 0) $parts[] = $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
            if ($diff->h > 0) $parts[] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
            if ($diff->i > 0) $parts[] = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
    
            $request->duration = implode(' ', $parts) ?: 'Less than 1 minute';
        }
    
        // For date range display with updated logic based on full day status
        if ($isFullDay) {
            // For full day requests, always show date range format
            $request->formatted_date = $start->format('d M Y') . ' - ' . $end->format('d M Y');
        } else {
            // For non-full day requests, include time in the format
            $request->formatted_date = $start->format('d M Y H:i') . ' - ' . $end->format('d M Y H:i');
        }
    
        // Set the department and position properties for display in views
        // Use historical values if available, otherwise use current values
        if (!isset($request->department)) {
            $request->department = $request->historical_department ??
                ($request->employee_department ??
                    (isset($request->user) && isset($request->user->department) ?
                        $request->user->department->department : null));
        }
    
        if (!isset($request->position)) {
            $request->position = $request->historical_position ??
                ($request->employee_position ??
                    (isset($request->user) && isset($request->user->position) ?
                        $request->user->position->position : null));
        }
    
        // Add information about declined/approved by
        if ($request->status === 'Declined') {
            // Determine who declined the request
            if ($request->admin_approval_status === 'Declined') {
                $request->declined_by = $request->admin_approver_name ?? 'Admin Approver';
            } elseif ($request->dept_approval_status === 'Declined') {
                $request->declined_by = $request->dept_approver_name ?? 'Department Manager';
            } else {
                $request->declined_by = 'Unknown';
            }
        }
    
        // Add formatted approval information
        if ($request->dept_approval_status === 'Approved') {
            $request->dept_approved_by = $request->dept_approver_name ?? 'Department Manager';
        }
    
        if ($request->admin_approval_status === 'Approved') {
            $request->admin_approved_by = $request->admin_approver_name ?? 'Admin';
        }
    
        return $request;
    }

    public function request_time_off_approve(Request $request, $id)
    {
        $timeOffRequest = RequestTimeOff::findOrFail($id);
        $currentUser = Auth::user();
        $approvalType = $request->approval_type ?? 'auto'; // Default to auto detection

        // Determine approval type based on permissions if not specified
        if ($approvalType === 'auto') {
            if ($this->timeOffCanApproveDept($currentUser, $timeOffRequest)) {
                $approvalType = 'dept';
            } elseif ($this->timeOffCanApproveAdmin($currentUser, $timeOffRequest)) {
                $approvalType = 'admin';
            } else {
                return redirect()->back()->with('error', 'You do not have permission to approve this request.');
            }
        } else {
            // Validate approval type if specified
            if (!in_array($approvalType, ['dept', 'admin'])) {
                return redirect()->back()->with('error', 'Invalid approval type.');
            }

            // Check permissions based on approval type
            if ($approvalType === 'dept' && !$this->timeOffCanApproveDept($currentUser, $timeOffRequest)) {
                return redirect()->back()->with('error', 'You do not have permission to department approval.');
            }

            if ($approvalType === 'admin' && !$this->timeOffCanApproveAdmin($currentUser, $timeOffRequest)) {
                return redirect()->back()->with('error', 'You do not have permission to admin approval.');
            }
        }

        // Process approval
        if ($approvalType === 'dept') {
            $timeOffRequest->dept_approval_status = 'Approved';
            $timeOffRequest->dept_approval_user_id = $currentUser->id;
            $message = 'First level approval completed. Waiting for final approval.';
        } else {
            $timeOffRequest->admin_approval_status = 'Approved';
            $timeOffRequest->admin_approval_user_id = $currentUser->id;

            // Auto-approve dept level if requester is manager or admin
            $user = User::find($timeOffRequest->user_id);
            if ($user && $user->position_id <= 3) { // Manager or above
                $timeOffRequest->dept_approval_status = 'Approved';
                $timeOffRequest->dept_approval_user_id = $timeOffRequest->admin_approval_user_id;
            }

            $deptStatus = $timeOffRequest->dept_approval_status === 'Approved'
                ? 'Request is now fully approved.'
                : 'Waiting for department approval.';

            $message = 'Admin approval completed. ' . $deptStatus;
        }

        $timeOffRequest->save();
        $this->checkTimeOffApprovalStatus($timeOffRequest);

        return redirect()->back()->with('success', $message);
    }

    // Helper function to check overall time off approval status
    private function checkTimeOffApprovalStatus(RequestTimeOff $request)
    {
        // If both levels approved, update the main approval status
        if ($request->dept_approval_status === 'Approved' && $request->admin_approval_status === 'Approved') {
            DB::beginTransaction();
            try {
                // Calculate the number of days requested
                $startDate = new DateTime($request->start_date);
                $endDate = new DateTime($request->end_date);
                $interval = $startDate->diff($endDate);
                $daysRequested = $interval->days + 1; // Including both start and end days

                // Find the time off assignment record for this user and time off type
                $timeOffAssignment = DB::table('time_off_assign')
                    ->where('user_id', $request->user_id)
                    ->where('time_off_id', $request->time_off_id)
                    ->first();

                // Make sure there's enough balance
                if (!$timeOffAssignment || $timeOffAssignment->balance < $daysRequested) {
                    DB::rollBack();
                    return;  // We'll let the UI show an error if needed
                }

                // Deduct balance
                DB::table('time_off_assign')
                    ->where('user_id', $request->user_id)
                    ->where('time_off_id', $request->time_off_id)
                    ->update([
                        'balance' => $timeOffAssignment->balance - $daysRequested,
                        'updated_at' => now()
                    ]);

                // Update the request status
                $request->status = 'Approved';
                $request->updated_at = now();
                $request->save();

                // Get the user who made the request
                $user = User::find($request->user_id);

                // Create notification for the user
                Notification::create([
                    'users_id' => $request->user_id,
                    'message' => "Your time off request has been fully approved.",
                    'type' => 'time_off_approved',
                    'maker_id' => Auth::user()->id,
                    'status' => 'Unread'
                ]);

                $timeOffPolicy = TimeOffPolicy::where('id', $request->time_off_id)->first();

                // Send email notification to the user
                if ($user) {
                    Mail::to($user->email)->send(new TimeOffRequestApproved($request, $user, $timeOffPolicy));
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }
    }

    // Updated Time Off Decline Function (either level can decline)
    public function request_time_off_decline(Request $httpRequest, $id)
    {
        $timeOffRequest = RequestTimeOff::findOrFail($id);
        $currentUser = Auth::user();
        if (!$this->timeOffCanDecline($currentUser, $timeOffRequest)) {
            return redirect()->back()->with('error', 'You do not have permission to decline this request.');
        }

        DB::beginTransaction();
        try {
            $timeOffRequest->status = 'Declined';
            $timeOffRequest->declined_reason = $httpRequest->declined_reason;

            // Also update the specific level that did the declining
            if ($this->timeOffIsDepartmentManager($currentUser)) {
                $timeOffRequest->dept_approval_status = 'Declined';
                $timeOffRequest->dept_approval_user_id = $currentUser->id;
            } else { // Admin user
                $timeOffRequest->admin_approval_status = 'Declined';
                $timeOffRequest->admin_approval_user_id = $currentUser->id;
            }

            $timeOffRequest->updated_at = now();
            $timeOffRequest->save();

            // Get the user who made the request
            $user = User::find($timeOffRequest->user_id);

            // Create notification for the user
            Notification::create([
                'users_id' => $timeOffRequest->user_id,
                'message' => "Your time off request has been declined.",
                'type' => 'time_off_declined',
                'maker_id' => Auth::user()->id,
                'status' => 'Unread'
            ]);

            $timeOffPolicy = TimeOffPolicy::where('id', $timeOffRequest->time_off_id)->first();

            // Send email notification to the user
            if ($user) {
                Mail::to($user->email)->send(new TimeOffRequestDeclined($timeOffRequest, $user, $timeOffPolicy));
            }

            DB::commit();
            return redirect()->back()->with('success', 'Time off request declined successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    // Helper methods (reused from ShiftChange functionality)
    private function timeOffIsAdminUser($user)
    {
        return in_array($user->position_id, [1, 2]) || // Director or GM
            ($user->position_id == 3 && $user->department_id == 3); // HR Manager
    }

    private function timeOffIsDepartmentManager($user)
    {
        return $user->position_id == 3 && $user->department_id != 3; // Manager non-HR
    }

    private function timeOffCanApproveDept($currentUser, $request)
    {
        // Always check if not already approved or declined
        if ($request->dept_approval_status !== 'Pending') return false;

        // Can't approve own request
        if ($currentUser->id == $request->user_id) return false;

        // Only department manager can approve
        if (!$this->timeOffIsDepartmentManager($currentUser)) return false;

        // Must be same department
        $userDepartment = $request->user->department_id ?? null;
        if ($currentUser->department_id != $userDepartment) return false;

        // Only for staff/supervisor requests (position_id 4 or 5)
        $userPosition = $request->user->position_id ?? null;
        return $userPosition > 3;
    }

    private function timeOffCanApproveAdmin($currentUser, $request)
    {
        // Always check if not already approved or declined
        if ($request->admin_approval_status !== 'Pending') return false;

        // Can't approve own request
        if ($currentUser->id == $request->user_id) return false;

        // Only admin can approve
        if (!$this->timeOffIsAdminUser($currentUser)) return false;
        // Get user information
        $user = User::find($request->user_id);

        if (!$user) return false;

        // For manager requests or admin requests, only need to check admin approval status
        // (dept approval will be auto-approved)
        if ($user->position_id <= 3) {
            return $request->admin_approval_status === 'Pending';
        }

        // For staff/supervisor requests, must have department approval first
        return $request->dept_approval_status === 'Approved';
    }

    private function timeOffCanDecline($currentUser, $request)
    {
        // Can't decline own request
        if ($currentUser->id == $request->user_id) return false;

        // Get user information
        $user = User::find($request->user_id);

        if (!$user) return false;

        // If current user is department manager
        if ($this->timeOffIsDepartmentManager($currentUser)) {
            // Can only decline requests from staff/supervisors in their department
            return $user->position_id > 3 &&
                $user->department_id == $currentUser->department_id &&
                $request->status === 'Pending';
        }

        // If current user is admin
        if ($this->timeOffIsAdminUser($currentUser)) {
            // Can decline any pending request that's not their own
            return $request->status === 'Pending';
        }

        return false;
    }















    public function request_time_off_create($id)
    {
        $employee = User::findOrFail($id);

        // Ambil data di mana end_date null atau masih berlaku
        $timeOffTypes = TimeOffPolicy::whereNull('end_date')
            ->orWhere('end_date', '>=', Carbon::today())
            ->get();

        return view('time_management.time_off.request_time_off.create', compact('employee', 'timeOffTypes'));
    }

    public function checkRequiresTimeInput(Request $request)
    {
        $timeOffId = $request->input('time_off_id');

        $policy = TimeOffPolicy::select('requires_time_input')
            ->where('id', $timeOffId)
            ->first();

        return response()->json([
            'requires_time_input' => $policy ? (bool) $policy->requires_time_input : false
        ]);
    }

    public function getEmployeeShift(Request $request)
    {
        $userId = $request->user_id;
        $date = $request->date;
        $dayName = $request->day_name;

        // Convert date to Carbon for easier comparison
        $requestDate = Carbon::parse($date);

        // Find applicable shift
        $employeeShift = EmployeeShift::where('user_id', $userId)
            ->where(function ($query) use ($requestDate) {
                $query->where(function ($q) use ($requestDate) {
                    // Case 1: Date is within start_date and end_date
                    $q->where('start_date', '<=', $requestDate)
                        ->where('end_date', '>=', $requestDate);
                })->orWhere(function ($q) use ($requestDate) {
                    // Case 2: Date is after start_date and end_date is null (no expiry)
                    $q->where('start_date', '<=', $requestDate)
                        ->whereNull('end_date');
                });
            })
            ->orderBy('start_date', 'desc') // Get the most recent applicable shift rule
            ->first();

        if (!$employeeShift) {
            return response()->json([
                'success' => false,
                'message' => 'No shift found for this date'
            ]);
        }

        // Get the rule details
        $ruleShift = rule_shift::find($employeeShift->rule_id);

        if (!$ruleShift) {
            return response()->json([
                'success' => false,
                'message' => 'Shift rule not found'
            ]);
        }

        // Parse days from JSON
        $days = json_decode($ruleShift->days, true);

   

        // Check if the requested day is included in the rule
        if (!in_array($dayName, $days)) {
            return response()->json([
                'success' => false,
                'message' => 'No shift scheduled for this day of the week'
            ]);
        }

        // Return shift data
        return response()->json([
            'success' => true,
            'data' => [
                'hour_start' => $ruleShift->hour_start,
                'hour_end' => $ruleShift->hour_end,
                'shift_type' => $ruleShift->type,
                'days' => $days
            ]
        ]);
    }

    public function checkBalance(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'time_off_id' => 'required',
        ]);

        try {
            // Find the time off assignment record
            $timeOffAssign = TimeOffAssign::where('user_id', $request->user_id)
                ->where('time_off_id', $request->time_off_id)
                ->first();



            // Check if the record exists and has balance
            if (!$timeOffAssign || $timeOffAssign->balance <= 0) {
                return response()->json([
                    'status' => 'success',
                    'hasBalance' => false,
                    'balance' => $timeOffAssign ? $timeOffAssign->balance : 0,
                    'message' => 'User does not have available balance for this time off type.'
                ]);
            }

            // Check for existing pending requests
            $existingRequests = RequestTimeOff::where('user_id', $request->user_id)
                ->where(function ($query) {
                    $query->where('status', 'pending')
                        ->orWhere('status', 'approved');
                })
                ->get();





            // If there are no existing requests, return success with the balance
            if ($existingRequests->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'hasBalance' => true,
                    'balance' => $timeOffAssign->balance,
                    'message' => 'User has available balance for this time off type.',
                    'hasConflicts' => false
                ]);
            }

            // Create array of dates that are already requested
            $unavailableDates = [];
            foreach ($existingRequests as $request) {
                $startDate = new DateTime($request->start_date);
                $endDate = new DateTime($request->end_date);

                // Create interval for one day
                $interval = new DateInterval('P1D');

                // Create date range
                $dateRange = new DatePeriod($startDate, $interval, $endDate->modify('+1 day'));

                // Add each date to the unavailable dates array
                foreach ($dateRange as $date) {
                    $unavailableDates[] = $date->format('Y-m-d');
                }
            }

            // Get the next available date (1 day after the last unavailable date)
            sort($unavailableDates);
            $firstUnavailableDate = $unavailableDates[0] ?? null;
            $lastUnavailableDate = end($unavailableDates) ?? null;

            // Reset pointer after using end()
            reset($unavailableDates);

            // Format the next available dates
            $nextAvailableDates = [];
            if ($firstUnavailableDate) {
                $beforeFirstDate = new DateTime($firstUnavailableDate);
                $beforeFirstDate->modify('-1 day');
                if ($beforeFirstDate >= new DateTime()) {
                    $nextAvailableDates[] = $beforeFirstDate->format('Y-m-d');
                }
            }

            if ($lastUnavailableDate) {
                $afterLastDate = new DateTime($lastUnavailableDate);
                $afterLastDate->modify('+1 day');
                $nextAvailableDates[] = $afterLastDate->format('Y-m-d');
            }

            return response()->json([
                'status' => 'success',
                'hasBalance' => true,
                'balance' => $timeOffAssign->balance,
                'message' => 'User has available balance for this time off type.',
                'hasConflicts' => !empty($unavailableDates),
                'unavailableDates' => $unavailableDates,
                'nextAvailableDates' => $nextAvailableDates,
                'suggestions' => [
                    'before' => !empty($firstUnavailableDate) ? 'Available before: ' . date('d M Y', strtotime($firstUnavailableDate . ' -1 day')) : null,
                    'after' => !empty($lastUnavailableDate) ? 'Available after: ' . date('d M Y', strtotime($lastUnavailableDate . ' +1 day')) : null,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking time off balance: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while checking time off balance.'
            ], 500);
        }
    }

    public function request_time_off_store(Request $request)
    {
        //dd($request->all());

        // Validate the request data
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'time_off_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'hour_in' => 'nullable|date_format:H:i',
            'hour_out' => 'nullable|date_format:H:i',
            'reason' => 'required|string|max:255',
            'file_reason' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Get the time off type
        $timeOffType = TimeOffPolicy::find($request->time_off_id);

        // Combine date and time if needed
        $startDateTime = $request->start_date;
        $endDateTime = $request->end_date;


        // Check if we have time inputs and if the time off type requires them
        if ($timeOffType->requires_time_input) {
            // Use hour_in and hour_out if provided, otherwise use default times
            $startTime = $request->hour_in ?? '00:00:00';
            $endTime = $request->hour_out ?? '23:59:59';

            $startDateTime = $request->start_date . ' ' . $startTime;
            $endDateTime = $request->end_date . ' ' . $endTime;
        } else {
            // If time input is not required, use the full day
            $startDateTime = $request->start_date . ' 00:00:00';
            $endDateTime = $request->end_date . ' 23:59:59';
        }

        // dd($startDateTime, $endDateTime);

        // Create the time off request
        $timeOffRequest = new RequestTimeOff();
        $timeOffRequest->user_id = $request->user_id;
        $timeOffRequest->time_off_id = $request->time_off_id;
        $timeOffRequest->start_date = $startDateTime;
        $timeOffRequest->end_date = $endDateTime;
        $timeOffRequest->reason = $request->reason;
        $timeOffRequest->status = 'Pending';

        // Save request first to get ID
        $timeOffRequest->save();

        // Handle file upload if present
        if ($request->hasFile('file_reason')) {
            $file = $request->file('file_reason');

            // Get ID after save
            $fileName = 'request_time_off_' . $timeOffRequest->id . '_' . $request->time_off_id . '_' . $request->user_id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('time_management/time_off', $fileName, 'public');

            // Save file path after upload
            $timeOffRequest->file_reason_path = 'time_management/time_off/' . $fileName;
            $timeOffRequest->save();
        }




        // Get the user who made the request
        $user = User::find($request->user_id);

        $hrDepartment = EmployeeDepartment::where('department', 'Human Resources')->first();

        $hr_personnel = User::where('department_id', $hrDepartment->id)
            ->where('employee_status', '!=', 'Inactive')
            ->get();



        // Create notifications for HR personnel
        foreach ($hr_personnel as $hr) {
            Notification::create([
                'users_id' => $hr->id,
                'message' => "New time off request from {$user->name}.",
                'type' => 'time_off_request',
                'maker_id' => Auth::user()->id,
                'status' => 'Unread'
            ]);

            $timeOffPolicy = TimeOffPolicy::where('id',  $timeOffRequest->time_off_id)->first();


            // Send email notification to HR personnel
            Mail::to($hr->email)->send(new TimeOffRequestSubmitted($timeOffRequest, $user,  $timeOffPolicy));
        }

        return redirect()->route('request.time.off.index2', $request->user_id)
            ->with('success', 'Time off request submitted successfully.');
    }

    public function request_time_off_destroy($id)
    {
        try {
            $request = RequestTimeOff::findOrFail($id);
            $userId = $request->user_id;
            $user = User::find($userId);

            // Store request details before deletion for notifications
            $requestDetails = [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'time_off_type' => $request->timeOffType->name ?? 'Time Off',
            ];

            // Get HR personnel
            $hr_personnel = User::where('department', 'Human Resources')
                ->where('employee_status', '!=', 'Inactive')
                ->get();

            // Create notifications for HR personnel about the cancelled request
            foreach ($hr_personnel as $hr) {
                Notification::create([
                    'users_id' => $hr->id,
                    'message' => "Time off request cancelled by {$user->name}.",
                    'type' => 'time_off_cancelled',
                    'maker_id' => Auth::user()->id,
                    'status' => 'Unread'
                ]);

                $timeOffPolicy = TimeOffPolicy::where('id',  $request->time_off_id)->first();

                // Send email notification to HR personnel
                Mail::to($hr->email)->send(new TimeOffRequestCancelled($request, $user,     $timeOffPolicy));
            }



            $request->delete();
            return response()->json(['success' => 'Time off request deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
