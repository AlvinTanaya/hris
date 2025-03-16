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

use App\Models\rule_shift;
use App\Models\EmployeeShift;
use App\Models\RequestShiftChange;
use App\Models\RequestResign;
use App\Models\EmployeeAbsent;
use App\Models\User;
use App\Models\WarningLetter;
use App\Models\Notification;
use App\Models\EmployeeOvertime;
use App\Models\TimeOffPolicy;
use App\Models\TimeOffAssign;
use App\Models\RequestTimeOff;

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
        // Original code for employee shifts (unchanged)
        $query = EmployeeShift::with(['user', 'ruleShift']);

        // Filter berdasarkan tipe shift
        if ($request->has('type') && $request->type != '') {
            $query->where('rule_id', $request->type);
        }

        // Filter berdasarkan employee
        if ($request->has('employee') && $request->employee != '') {
            $query->where('user_id', $request->employee);
        }

        // Filter berdasarkan tanggal mulai
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('start_date', $request->start_date);
        }

        if ($request->has('position') && $request->position != '') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('position', $request->position);
            });
        }

        if ($request->has('department') && $request->department != '') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        $employeeShifts = $query->whereNull('end_date')->get()->groupBy('rule_id');

        // History filter code (unchanged)
        $query_history = EmployeeShift::with(['user', 'ruleShift']);

        if ($request->has('type_history') && $request->type_history != '') {
            $query_history->where('rule_id', $request->type_history);
        }

        if ($request->has('employee_history') && $request->employee_history != '') {
            $query_history->where('user_id', $request->employee_history);
        }

        if ($request->has('start_date_history') && $request->start_date_history != '') {
            $query_history->whereDate('start_date', $request->start_date_history);
        }

        if ($request->has('position_history') && $request->position_history != '') {
            $query_history->whereHas('user', function ($q) use ($request) {
                $q->where('position', $request->position_history);
            });
        }

        if ($request->has('department_history') && $request->department_history != '') {
            $query_history->whereHas('user', function ($q) use ($request) {
                $q->where('department', $request->department_history);
            });
        }

        if ($request->has('end_date_history') && $request->end_date_history != '') {
            $query_history->whereDate('end_date', $request->end_date_history);
        }

        $employeeShiftsHistory = $query_history->whereNotNull('end_date')->get()->groupBy('rule_id');

        // Also fetch user's own pending requests for the "requests" tab
        $pendingRequests = RequestShiftChange::where('user_id', Auth::user()->id)
            ->where('status_change', 'Pending')
            ->get();

        // Fetch all user's requests for the "requests" tab
        $allRequests = RequestShiftChange::where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Shift Request filter - For admin tab
        $requestQuery = RequestShiftChange::with(['user', 'ruleShiftBefore', 'ruleShiftAfter', 'exchangeUser', 'ruleExchangeBefore', 'ruleExchangeAfter']);

        // Apply filters for shift requests
        // if ($request->has('status_request') && $request->status_request != '') {
        //     $requestQuery->where('status_change', $request->status_request);
        // }

        if ($request->has('employee_request') && $request->employee_request != '') {
            $requestQuery->where('user_id', $request->employee_request);
        }

        if ($request->has('current_shift_request') && $request->current_shift_request != '') {
            $requestQuery->where('rule_user_id_before', $request->current_shift_request);
        }

        if ($request->has('requested_shift_request') && $request->requested_shift_request != '') {
            $requestQuery->where('rule_user_id_after', $request->requested_shift_request);
        }

        if ($request->has('start_date_request') && $request->start_date_request != '') {
            $requestQuery->whereDate('date_change_start', '>=', $request->start_date_request);
        }

        if ($request->has('end_date_request') && $request->end_date_request != '') {
            $requestQuery->whereDate('date_change_end', '<=', $request->end_date_request);
        }

        if ($request->has('position_request') && $request->position_request != '') {
            $requestQuery->whereHas('user', function ($q) use ($request) {
                $q->where('position', $request->position_request);
            });
        }

        if ($request->has('department_request') && $request->department_request != '') {
            $requestQuery->whereHas('user', function ($q) use ($request) {
                $q->where('department', $request->department_request);
            });
        }

        $pendingShiftRequests = $requestQuery->orderBy('created_at', 'desc')->get();

        $rules = rule_shift::all();
        $employees = User::where('employee_status', '!=', 'Inactive')->get();
        $departments = User::where('employee_status', '!=', 'Inactive')
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        $positions = User::where('employee_status', '!=', 'Inactive')
            ->whereNotNull('position')
            ->distinct()
            ->pluck('position');

        // Get active tab
        $activeTab = $request->tab ?? 'current';

        return view('/time_management/set_shift/index', compact(
            'employeeShifts',
            'employeeShiftsHistory',
            'rules',
            'employees',
            'positions',
            'departments',
            'pendingShiftRequests',
            'pendingRequests',
            'allRequests',
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
            ->get();

        // Mengambil daftar departemen unik dari pegawai yang aktif
        $departments = User::where('employee_status', '!=', 'Inactive')
            ->whereNotIn('id', $existShift)
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        // Mengambil daftar posisi unik dari pegawai yang aktif
        $positions = User::where('employee_status', '!=', 'Inactive')
            ->whereNotIn('id', $existShift)
            ->whereNotNull('position')
            ->distinct()
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

                // Get user
                $user = User::find($employeeId);
                if ($user) {
                    // Kirim email
                    Mail::to($user->email)->send(new EmployeeShiftAssigned($user, $shift));

                    // Buat notifikasi di database
                    Notification::create([
                        'users_id' => $user->id,
                        'message' => "You have been assigned to the {$shift->rule->type} shift from {$shift->start_date} to " . ($shift->end_date ?? 'indefinitely') . ".",
                        'type' => 'shift_assigned',
                        'maker_id' => Auth::user()->id,
                        'status' => 'Unread'
                    ]);
                }
            }
        }

        return redirect()->route('time.employee.shift.index')->with('success', 'Employee shift added successfully.');
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
                    'message' => "We sincerely apologize for the mistake. There has been a change in your shift assignment. You have now been reassigned to the {$shift->rule->type} shift from {$shift->start_date} onwards.",
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
                'message' => "We sincerely apologize for the mistake. There has been a change in your shift assignment. You have now been reassigned to the {$shift->rule->type} shift from {$shift->start_date} onwards.",
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
                EmployeeShift::where('id', $employee->id)->update([
                    'rule_id' => $newShiftRule->id,
                    'updated_at' => now(),
                ]);

                $updatedEmployees[] = $employee->employee_name;

                // Send email
                Mail::to($employee->user_email)->send(new EmployeeShiftExchanged($employee, $newShiftRule));

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


    public function approveShiftChange($id)
    {
        try {
            DB::beginTransaction();

            $request = RequestShiftChange::with(['user', 'ruleShiftAfter'])->findOrFail($id);

            $request->status_change = 'Approved';
            $request->answer_user_id = Auth::id();
            $request->save();

            $startDate = Carbon::parse($request->date_change_start);
            $endDate = Carbon::parse($request->date_change_end);

            $this->processUserShiftChange($request->user_id, $request->rule_user_id_after, $startDate, $endDate);

            if ($request->user_exchange_id) {
                $this->processUserShiftChange($request->user_exchange_id, $request->rule_user_exchange_id_after, $startDate, $endDate);
            } else if ($request->rule_user_exchange_id_after) {
                $this->processUserShiftChange($request->user_id, $request->rule_user_exchange_id_after, $startDate, $endDate, true);
            }

            DB::commit();

            // Send email
            Mail::to($request->user->email)->send(new ShiftChangeApprovedMail($request));

            // Create notification
            Notification::create([
                'users_id' => $request->user_id,
                'message' => "Your shift change request has been approved.",
                'type' => 'shift_change_approved',
                'maker_id' => Auth::id(),
                'status' => 'Unread'
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
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
        try {
            $shiftChangeRequest = RequestShiftChange::with('user')->findOrFail($id);

            $shiftChangeRequest->status_change = 'Declined';
            $shiftChangeRequest->declined_reason = $request->decline_reason;
            $shiftChangeRequest->answer_user_id = Auth::id();
            $shiftChangeRequest->save();

            // Send email
            Mail::to($shiftChangeRequest->user->email)->send(new ShiftChangeDeclinedMail($shiftChangeRequest));

            // Create notification
            Notification::create([
                'users_id' => $shiftChangeRequest->user_id,
                'message' => "Your shift change request has been declined.",
                'type' => 'shift_change_declined',
                'maker_id' => Auth::id(),
                'status' => 'Unread'
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Display attendance page
     */

    public function employee_absent_index()
    {
        $employeeAbsent = EmployeeAbsent::all();
        $years = EmployeeAbsent::selectRaw('YEAR(date) as year')->distinct()->pluck('year')->sortDesc();

        return view('time_management/employee_absent/index', compact('employeeAbsent', 'years'));
    }


    public function getAttendanceData(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        // Fetch all employees
        $employees = User::all();

        // Prepare attendance data
        $attendanceData = $employees->map(function ($employee) use ($month, $year) {
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

            // Get all days in the month
            $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

            // Process each day
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::createFromDate($year, $month, $day);
                $dayOfWeek = $date->format('l'); // Get day name (Monday, Tuesday, etc.)

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

                    if ($absence->hour_in && $shiftStartTime) {
                        $actualIn = Carbon::parse($absence->hour_in);
                        $expectedInTime = Carbon::parse($shiftStartTime);
                        if ($actualIn->gt($expectedInTime)) {
                            $lateMinutes = $actualIn->diffInMinutes($expectedInTime);
                        }
                    }

                    // Use rule_out for shift end time if available
                    $shiftEndTime = $ruleOut ?: $expectedOut;

                    // In getAttendanceData function where early_minutes is calculated:
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
                        $attendance[$day] = [
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
                            'rule_type' => $ruleType
                        ];
                    }
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
     * Display Warning letter
     */
    public function warning_letter_index()
    {
        $employees = User::where('employee_status', '!=', 'Inactive')->get();


        // Get unique departments and positions
        $departments = User::where('employee_status', '!=', 'Inactive')
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        $positions = User::where('employee_status', '!=', 'Inactive')
            ->whereNotNull('position')
            ->distinct()
            ->pluck('position');


        // Join employee_warning_letter with users table
        $warning_letter = DB::table('employee_warning_letter as ewl')
            ->join('users as u1', 'ewl.user_id', '=', 'u1.id')
            ->join('users as u2', 'ewl.maker_id', '=', 'u2.id')
            ->select(
                'ewl.*',
                'u1.name as employee_name',
                'u1.department as employee_department',
                'u1.position as employee_position',
                'u1.employee_id as employee_id',

                'u2.name as maker_name',
                'u2.department as maker_department',
                'u2.position as maker_position',
                'u2.employee_id as maker_id',
            )
            ->whereIn('ewl.user_id', $employees->pluck('id'))
            ->get();



        return view('time_management/warning_letter/index', compact('warning_letter', 'employees', 'departments', 'positions'));
    }

    public function warning_letter_index2($id)
    {


        $warning_letter = DB::table('employee_warning_letter as ewl')
            ->join('users', 'ewl.maker_id', '=', 'users.id')
            ->select('ewl.*', 'users.name', 'users.department', 'users.position', 'users.employee_id')
            ->where('ewl.user_id', $id)->get();



        return view('time_management/warning_letter/index2', compact('warning_letter'));
    }

    public function warning_letter_create()
    {
        $employees = User::where('employee_status', '!=', 'Inactive')->get();
        return view('time_management/warning_letter/create', compact('employees'));
    }

    public function warning_letter_edit($id)
    {
        $warning_letter = WarningLetter::where('id', $id)->first();

        $employee = User::where('id', $warning_letter->user_id)->first();
        return view('time_management/warning_letter/update', compact('warning_letter', 'employee'));
    }

    public function warning_letter_store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason_warning' => 'required|string',
            'maker_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);
        $maker = User::findOrFail($request->maker_id);

        // Hitung jumlah warning letter saat ini
        $count = WarningLetter::where('user_id', $request->user_id)->count() + 1;

        // Simpan Warning Letter
        WarningLetter::create([
            'user_id' => $request->user_id,
            'maker_id' => $request->maker_id,
            'reason_warning' => $request->reason_warning
        ]);

        // Jika warning letter sudah mencapai 3, ubah status employee menjadi Inactive
        if ($count >= 3) {
            $user->update(['employee_status' => 'Inactive']);
        }

        // Cek apakah notifikasi sudah ada
        $notification = Notification::where('users_id', $request->user_id)
            ->where('type', 'warning_letter')
            ->first();

        if ($notification) {
            // Update notifikasi yang sudah ada
            $notification->update([
                'message' => "You received warning letter #$count: " . $request->reason_warning,
                'status' => 'Unread'
            ]);
        } else {
            // Buat notifikasi baru jika belum ada
            Notification::create([
                'users_id' => $request->user_id,
                'message' => "You received warning letter #$count: " . $request->reason_warning,
                'type' => 'warning_letter',
                'maker_id' => $request->maker_id,
                'status' => 'Unread'
            ]);
        }

        // Kirim Email
        Mail::to($user->email)->send(new WarningLetterMail($user, $count, $request->reason_warning, $maker));

        return redirect()->route('warning.letter.index')->with('success', 'Warning letter created successfully');
    }

    public function warning_letter_update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason_warning' => 'required|string',
            'maker_id' => 'required|exists:users,id'
        ]);

        $warning_letter = WarningLetter::findOrFail($id);
        $user = User::findOrFail($request->user_id);
        $maker = User::findOrFail($request->maker_id);

        // Hitung jumlah warning letter yang sudah ada
        $count = WarningLetter::where('user_id', $request->user_id)->count();

        // Update warning letter
        $warning_letter->update([
            'user_id' => $request->user_id,
            'maker_id' => $request->maker_id,
            'reason_warning' => $request->reason_warning
        ]);

        // Update notifikasi jika sudah ada
        $notification = Notification::where('users_id', $request->user_id)
            ->where('type', 'warning_letter')
            ->first();

        if ($notification) {
            $notification->update([
                'message' => "Your warning letter #$count reason has been updated to: " . $request->reason_warning,
                'status' => 'Unread',
                'upadted_at' => now()

            ]);
        }

        // Kirim Email
        Mail::to($user->email)->send(new WarningLetterMail($user, $count, $request->reason_warning, $maker));

        return redirect()->route('warning.letter.index')->with('success', 'Warning letter updated successfully');
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
                    $query->where('name', 'Human Resources')->where('employee_status', '!=', 'Inactive');
                })->get();

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

            $request->delete();

            // Get HR users
            $hrUsers = User::whereHas('department', function ($query) {
                $query->where('name', 'Human Resources')->where('employee_status', '!=', 'Inactive');
            })->get();

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
        $employees = User::where('employee_status', '!=', 'Inactive')->get();

        // Get unique departments and positions
        $departments = User::where('employee_status', '!=', 'Inactive')
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        $positions = User::where('employee_status', '!=', 'Inactive')
            ->whereNotNull('position')
            ->distinct()
            ->pluck('position');

        // Ambil daftar unik resign_type
        $types = RequestResign::whereNotNull('resign_type')
            ->distinct()
            ->pluck('resign_type')
            ->toArray(); // Ubah ke array agar mudah dicek

        // Definisikan tipe yang wajib ada
        $requiredTypes = ['Voluntary', 'Retirement'];

        // Gabungkan tipe yang ada dengan yang wajib
        $finalTypes = array_unique(array_merge($types, $requiredTypes));

        // Base query with joins to get employee information
        $query = DB::table('request_resign')
            ->join('users as employee', 'request_resign.user_id', '=', 'employee.id')
            ->leftJoin('users as responder', 'request_resign.response_user_id', '=', 'responder.id')
            ->select(
                'request_resign.*',
                'employee.name as employee_name',
                'employee.position as employee_position',
                'employee.department as employee_department',
                'responder.name as response_name'
            );

        // Apply filters if provided
        if ($request->filled('user_id')) {
            $query->where('request_resign.user_id', $request->user_id);
        }

        if ($request->filled('position')) {
            $query->where('employee.position', $request->position);
        }

        if ($request->filled('department')) {
            $query->where('employee.department', $request->department);
        }

        if ($request->filled('resign_type')) {
            $query->where('request_resign.resign_type', $request->resign_type);
        }

        if ($request->filled('date')) {
            $query->where('request_resign.resign_date', $request->date);
        }

        // Whether to show specific status cards
        $show_pending = true;
        $show_approved = true;
        $show_declined = true;

        // Filter by response type if provided
        if ($request->filled('response_type')) {
            // Only include records with the matching status
            $query->where('request_resign.resign_status', $request->response_type);

            // Set visibility flags for cards
            $show_pending = $request->response_type == 'Pending';
            $show_approved = $request->response_type == 'Approved';
            $show_declined = $request->response_type == 'Declined';
        }

        // Get requests by status
        $pending_requests = (clone $query)->where('resign_status', 'Pending')->get();
        $approved_requests = (clone $query)->where('resign_status', 'Approved')->get();
        $declined_requests = (clone $query)->where('resign_status', 'Declined')->get();

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
        // Check if the request exists for this user
        $query = DB::table('request_resign')
            ->join('users as employee', 'request_resign.user_id', '=', 'employee.id')
            ->leftJoin('users as responder', 'request_resign.response_user_id', '=', 'responder.id')
            ->select(
                'request_resign.*',
                'employee.name as employee_name',
                'employee.position as employee_position',
                'employee.department as employee_department',
                'responder.name as response_name'
            )
            ->where('request_resign.user_id', $id);

        // Get requests by status for this specific user
        $pending_requests = (clone $query)->where('resign_status', 'Pending')->get();
        $approved_requests = (clone $query)->where('resign_status', 'Approved')->get();
        $declined_requests = (clone $query)->where('resign_status', 'Declined')->get();

        // Get user info for display
        $user = User::find($id);

        return view('time_management/request_resign/index2', compact(
            'pending_requests',
            'approved_requests',
            'declined_requests',
            'user'
        ));
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

        // Send email to user
        Mail::to(Auth::user()->email)->send(new ResignationRequestMail(Auth::user(), $validated['resign_date']));

        // Create notification
        Notification::create([
            'message' => 'You have submitted a resignation request. Please wait for management’s decision.',
            'type' => 'resign',
            'users_id' => $request->user_id,
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

        // Create notification
        Notification::create([
            'message' => 'Your resignation request has been updated. Please wait for management’s decision.',
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
        $department = $request->input('department_request'); // Fixed
        $position = $request->input('position_request'); // Fixed

        // Base query
        $query = EmployeeOvertime::query()
            ->join('users', 'employee_overtime.user_id', '=', 'users.id')
            ->leftJoin('users as responder', 'employee_overtime.answer_user_id', '=', 'responder.id')
            ->select(
                'employee_overtime.*',
                'users.name as employee_name',
                'users.position as employee_position',
                'users.department as employee_department',
                'responder.name as response_name'
            );

        // Apply filters
        if ($employee !== 'all' && is_numeric($employee)) {
            $query->where('employee_overtime.user_id', $employee);
        }

        if ($position) {
            $query->where('users.position', $position); // Fixed alias
        }

        if ($department) {
            $query->where('users.department', $department); // Fixed alias
        }

        if ($overtimeType !== 'all') {
            $query->where('employee_overtime.overtime_type', $overtimeType);
        }

        if ($date) {
            $query->whereDate('employee_overtime.date', $date);
        }

        // Separate queries for each tab
        $pendingRequests = (clone $query)->where('employee_overtime.approval_status', 'Pending')->get();
        $approvedRequests = (clone $query)->where('employee_overtime.approval_status', 'Approved')->get();
        $declinedRequests = (clone $query)->where('employee_overtime.approval_status', 'Declined')->get();

        // Get all employees for filter dropdown
        $employees = User::where('employee_status', '!=', 'Inactive')->get();

        // Get unique departments and positions
        $departments = User::where('employee_status', '!=', 'Inactive')
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        $positions = User::where('employee_status', '!=', 'Inactive')
            ->whereNotNull('position')
            ->distinct()
            ->pluck('position');

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


    // View overtime requests for a specific employee
    public function overtime_index2($id)
    {
        $query = EmployeeOvertime::query()
            ->join('users', 'employee_overtime.user_id', '=', 'users.id')
            ->leftJoin('users as responder', 'employee_overtime.answer_user_id', '=', 'responder.id')
            ->select('employee_overtime.*', 'users.name as employee_name', 'users.position as employee_position', 'users.department as employee_department',    'responder.name as response_name')
            ->where('employee_overtime.user_id', $id);


        // Separate queries for each tab (Pending, Approved, Declined)
        $pendingRequests = clone $query;
        $pendingRequests = $pendingRequests->where('approval_status', 'Pending')->get();

        $approvedRequests = clone $query;
        $approvedRequests = $approvedRequests->where('approval_status', 'Approved')->get();

        $declinedRequests = clone $query;
        $declinedRequests = $declinedRequests->where('approval_status', 'Declined')->get();

        $employee = User::where('id', $id)->first();

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
        $hr_personnel = User::where('department', 'Human Resources')
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
    public function overtime_approve(Request $request, $id)
    {
        $overtime = EmployeeOvertime::findOrFail($id);
        $overtime->update([
            'approval_status' => 'Approved',
            'answer_user_id' => Auth::id(),
            'updated_at' => now(),
        ]);

        // Get the employee who submitted the request
        $employee = User::find($overtime->user_id);

        // Create notification for the employee
        Notification::create([
            'users_id' => $overtime->user_id,
            'message' => "Your overtime request for {$overtime->date} has been approved.",
            'type' => 'overtime_approved',
            'maker_id' => Auth::id(),
            'status' => 'Unread'
        ]);

        // Send email notification to employee
        Mail::to($employee->email)->send(new OvertimeApprovedMail($overtime));

        return redirect()->back()->with('success', 'Overtime request approved successfully');
    }

    // Decline an overtime request
    public function overtime_decline(Request $request, $id)
    {
        $request->validate([
            'declined_reason' => 'required|string',
        ]);

        $overtime = EmployeeOvertime::findOrFail($id);
        $overtime->update([
            'approval_status' => 'Declined',
            'declined_reason' => $request->declined_reason,
            'answer_user_id' => Auth::id(),
            'updated_at' => now(),
        ]);

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
        Mail::to($employee->email)->send(new OvertimeDeclinedMail($overtime));

        return redirect()->back()->with('success', 'Overtime request declined successfully');
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
        $hr_personnel = User::where('department', 'Human Resources')
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Prepare data and handle nullable end_date
        $data = $request->only(['time_off_name', 'time_off_description', 'quota', 'start_date']);
        $data['end_date'] = $request->end_date ?: null;

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
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $policy = TimeOffPolicy::findOrFail($id);

        // Prepare data and handle nullable end_date
        $data = $request->only(['time_off_name', 'time_off_description', 'quota', 'start_date']);
        $data['end_date'] = $request->end_date ?: null;

        $policy->update($data);

        return redirect()->route('time.off.policy.index')
            ->with('success', 'Time off policy has been updated successfully.');
    }


    public function time_off_assign_index(Request $request)
    {
        $query = DB::table('time_off_assign')
            ->join('users', 'time_off_assign.user_id', '=', 'users.id')
            ->join('time_off_policy', 'time_off_assign.time_off_id', '=', 'time_off_policy.id')
            ->select(
                'time_off_assign.id',
                'users.name as employee_name',
                'users.employee_id',
                'users.department as department_name', // Ambil langsung dari users
                'users.position as position_name', // Ambil langsung dari users
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
            $query->where('users.department', $request->department);
        }

        if ($request->filled('position')) {
            $query->where('users.position', $request->position);
        }

        if ($request->filled('time_off_type')) {
            $query->where('time_off_policy.id', $request->time_off_type);
        }

        $timeOffAssignments = $query->orderBy('users.name')->paginate(10);

        // Get data for filter dropdowns
        $employees = DB::table('users')->select('id', 'name')->orderBy('name')->get();
        $departments = DB::table('users')->select('department')->distinct()->whereNotNull('department')->orderBy('department')->pluck('department');
        $positions = DB::table('users')->select('position')->distinct()->whereNotNull('position')->orderBy('position')->pluck('position');
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

        // Get all active employees
        $allEmployees = User::where('employee_status', '!=', 'Inactive')->get();

        // Get departments and positions for filtering
        $departments = User::where('employee_status', '!=', 'Inactive')
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        $positions = User::where('employee_status', '!=', 'Inactive')
            ->whereNotNull('position')
            ->distinct()
            ->pluck('position');

        // Get employees that already have this time off policy assigned
        $employees = $allEmployees;

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




    public function request_time_off_index(Request $request)
    {
        // Get all users, positions, departments, and time off policies for filters
        $users = User::orderBy('name')->get();

        // Get unique departments and positions
        $departments = User::where('employee_status', '!=', 'Inactive')
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        $positions = User::where('employee_status', '!=', 'Inactive')
            ->whereNotNull('position')
            ->distinct()
            ->pluck('position');

        $timeOffPolicies = TimeOffPolicy::orderBy('time_off_name')->get();

        // Build the query with joins
        $query = RequestTimeOff::query()
            ->join('users as u1', 'request_time_off.user_id', '=', 'u1.id') // User pemohon
            ->leftJoin('users as u2', 'request_time_off.answered_by', '=', 'u2.id') // User yang menjawab
            ->join('time_off_policy', 'request_time_off.time_off_id', '=', 'time_off_policy.id')
            ->leftJoin('time_off_assign', function ($join) {
                $join->on('request_time_off.user_id', '=', 'time_off_assign.user_id')
                    ->on('request_time_off.time_off_id', '=', 'time_off_assign.time_off_id');
            })
            ->select(
                'request_time_off.*',
                'u1.name as user_name', // Nama user pemohon
                'time_off_policy.time_off_name as time_off_name',
                'u1.department',
                'u1.position',
                'u2.name as answered_by_name' // Nama user yang menjawab
            );


        // Apply filters - FIXED parameter names to match the form
        if ($request->filled('employee')) {
            $query->where('request_time_off.user_id', $request->employee);
        }

        if ($request->filled('position_request')) {
            $query->where('users.position', $request->position_request);
        }

        if ($request->filled('department_request')) {
            $query->where('users.department', $request->department_request);
        }

        if ($request->filled('time_off_type')) {
            $query->where('request_time_off.time_off_id', $request->time_off_type);
        }

        if ($request->filled('date')) {
            $date = $request->date;
            $query->where(function ($q) use ($date) {
                $q->where('request_time_off.start_date', '<=', $date)
                    ->where('request_time_off.end_date', '>=', $date);
            });
        }

        // Count statuses for the tabs
        $pendingCount = (clone $query)->where('request_time_off.status', 'Pending')->count();
        $approvedCount = (clone $query)->where('request_time_off.status', 'Approved')->count();
        $declinedCount = (clone $query)->where('request_time_off.status', 'Declined')->count();

        // Get pending requests
        $pendingRequests = (clone $query)->where('request_time_off.status', 'Pending')
            ->orderBy('request_time_off.created_at', 'desc')
            ->get();

        // Get approved requests
        $approvedRequests = (clone $query)->where('request_time_off.status', 'Approved')
            ->orderBy('request_time_off.updated_at', 'desc')
            ->get();

        // Get declined requests
        $declinedRequests = (clone $query)->where('request_time_off.status', 'Declined')
            ->orderBy('request_time_off.updated_at', 'desc')
            ->get();

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


    public function request_time_off_index2($id)
    {
        $employee = User::findOrFail($id);

        // Get time off requests for this employee
        $pendingRequests = RequestTimeOff::where('user_id', $id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedRequests = RequestTimeOff::where('user_id', $id)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        $declinedRequests = RequestTimeOff::where('user_id', $id)
            ->where('status', 'declined')
            ->orderBy('created_at', 'desc')
            ->get();

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

        return view('time_management/time_off/request_time_off/index2', compact(
            'employee',
            'pendingRequests',
            'approvedRequests',
            'declinedRequests',
            'timeOffAssignments'
        ));
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

    public function request_time_off_store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'time_off_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:255',
            'file_reason' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Create the time off request
        $timeOffRequest = new RequestTimeOff();
        $timeOffRequest->user_id = $request->user_id;
        $timeOffRequest->time_off_id = $request->time_off_id;
        $timeOffRequest->start_date = $request->start_date;
        $timeOffRequest->end_date = $request->end_date;
        $timeOffRequest->reason = $request->reason;
        $timeOffRequest->status = 'pending';

        // Handle file upload if present
        if ($request->hasFile('file_reason')) {
            $file = $request->file('file_reason');
            $fileName = 'request_time_off_' . $request->time_off_id . '_' . $request->user_id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('time_management/time_off', $fileName, 'public');
            $timeOffRequest->file_reason_path = 'time_management/time_off/' . $fileName;
        }

        $timeOffRequest->save();

        // Get the user who made the request
        $user = User::find($request->user_id);

        // Get HR personnel
        $hr_personnel = User::where('department', 'Human Resources')
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

            // Send email notification to HR personnel
            Mail::to($hr->email)->send(new TimeOffRequestSubmitted($timeOffRequest, $user));
        }

        return redirect()->route('request.time.off.index2', $request->user_id)
            ->with('success', 'Time off request submitted successfully.');
    }

    public function request_time_off_destroy($id)
    {
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

            // Send email notification to HR personnel
            Mail::to($hr->email)->send(new TimeOffRequestCancelled($request, $user));
        }

        $request->delete();

        return redirect()->route('request.time.off.index2', $userId)
            ->with('success', 'Time off request deleted successfully.');
    }

    public function request_time_off_approve($id)
    {
        $request = RequestTimeOff::findOrFail($id);

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

        // Check if user has enough balance
        if ($timeOffAssignment && $timeOffAssignment->balance >= $daysRequested) {
            // Update the balance by subtracting the days requested
            DB::table('time_off_assign')
                ->where('user_id', $request->user_id)
                ->where('time_off_id', $request->time_off_id)
                ->update([
                    'balance' => $timeOffAssignment->balance - $daysRequested,
                    'updated_at' => now()
                ]);

            // Update the request status
            $request->status = 'approved';
            $request->answered_by = Auth::id();
            $request->updated_at = now();
            $request->save();

            // Get the user who made the request
            $user = User::find($request->user_id);

            // Create notification for the user
            Notification::create([
                'users_id' => $request->user_id,
                'message' => "Your time off request has been approved.",
                'type' => 'time_off_approved',
                'maker_id' => Auth::user()->id,
                'status' => 'Unread'
            ]);

            // Send email notification to the user
            Mail::to($user->email)->send(new TimeOffRequestApproved($request));

            return redirect()->back()->with('success', 'Time off request approved successfully.');
        } else {
            // User doesn't have enough balance
            return redirect()->back()->with('error', 'Cannot approve request. User does not have enough time off balance.');
        }
    }

    public function request_time_off_decline(Request $httpRequest, $id)
    {
        $timeOffRequest = RequestTimeOff::findOrFail($id);
        $timeOffRequest->status = 'Declined';
        $timeOffRequest->answered_by = Auth::id();
        $timeOffRequest->declined_reason = $httpRequest->declined_reason;
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

        // Send email notification to the user
        Mail::to($user->email)->send(new TimeOffRequestDeclined($timeOffRequest));

        return redirect()->back()->with('success', 'Time off request declined successfully.');
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
                ->where('status', 'pending')
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
}
