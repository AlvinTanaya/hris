@extends('layouts.app')

@section('content')
<style>
    #shiftTab .nav-link {
        color: white;
        font-weight: 500;
        padding: 1rem;
        transition: all 0.3s ease;
    }

    #shiftTab .nav-link.active {
        color: #0d6efd;
        border-bottom: 3px solid #0d6efd;
    }

    #requestsTable_wrapper {
        width: 100%;
        margin: 0;
        padding: 0;
    }

    .dataTables_wrapper .no-footer {
        width: 100% !important;
        margin: 0 !important;
    }

    .dataTable {
        width: 100% !important;
    }
</style>

<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px"><i class="fas fa-user-clock"></i> Employee Shifts Management</h1>

<div class="container mt-4 mx-auto">
    <ul class="nav nav-tabs d-flex w-100" id="shiftTab" role="tablist">
        <li class="nav-item flex-grow-1 text-center" role="presentation">
            <a class="nav-link active" id="currentTab" data-bs-toggle="tab" href="#current" role="tab" aria-controls="current" aria-selected="true">
                <i class="fas fa-calendar-day"></i> Current Shifts
            </a>
        </li>
        <li class="nav-item flex-grow-1 text-center" role="presentation">
            <a class="nav-link" id="historyTab" data-bs-toggle="tab" href="#history" role="tab" aria-controls="history" aria-selected="false">
                <i class="fas fa-history"></i> Shift History
            </a>
        </li>

        <li class="nav-item flex-grow-1 text-center" role="presentation">
            <a class="nav-link" id="requestTab" data-bs-toggle="tab" href="#request" role="tab" aria-controls="request" aria-selected="false">
                <i class="fa-solid fa-hand-point-up"></i> Shift Request
            </a>
        </li>
    </ul>

    <div class="tab-content mt-4">
        <!-- Current Shifts Section -->
        <div class="tab-pane fade show active" id="current" role="tabpanel">
            <div id="currentFilter" class="card shadow-sm mb-4">
                <div class="card-header bg-primary">
                    <h5 class="text-white mt-2"><i class="fas fa-filter"></i> Filter Current Shifts</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('time.set.shift.index') }}" method="GET" class="row g-3">
                        <input type="hidden" name="tab" value="current">
                        <div class="col-md-4">
                            <label class="form-label">Employee</label>
                            <select class="form-select" name="employee">
                                <option value="">All Employees</option>
                                @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Position</label>
                            <select class="form-select" name="position">
                                <option value="">All Positions</option>
                                @foreach($positions as $position)
                                <option value="{{ $position }}" {{ request('position') == $position ? 'selected' : '' }}>{{ $position }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Department</label>
                            <select class="form-select" name="department">
                                <option value="">All Departments</option>
                                @foreach($departments as $department)
                                <option value="{{ $department }}" {{ request('department') == $department ? 'selected' : '' }}>{{ $department }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Rule</label>
                            <select class="form-select" name="type">
                                <option value="">All Rules</option>
                                @foreach($rules as $rule)
                                <option value="{{ $rule->id}}" {{ request('type') == $rule->id ? 'selected' : '' }}>{{ $rule->type }}</option>
                                @endforeach

                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('time.set.shift.index') }}?tab=current" class="btn btn-secondary">
                                <i class="fas fa-undo me-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>


            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-primary mt-2"><i class="fas fa-calendar-check"></i> Current Employee Shifts</h5>

                    <div>

                        <!-- Exchange Shift Button -->
                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exchangeShiftModal">
                            <i class="fas fa-exchange-alt me-2"></i> Exchange Shifts
                        </button>
                        <a href="{{ route('time.set.shift.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i>Add Employee Shift
                        </a>


                    </div>

                </div>
            </div>


            @foreach($employeeShifts as $ruleId => $shifts)
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="table-responsive">

                        <h3 class="text-primary">{{ $shifts->first()->ruleShift->type }}</h3>

                        <table id="currentShiftTable" class="table table-bordered table-hover mb-3 pt-3">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th>Employee</th>
                                    <th style="width: 20%">Rule</th>
                                    <th style="width: 10%">Start Date</th>
                                    <th style="width: 10%">End Date</th>
                                    <th style="width: 20%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shifts as $index => $shift)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $shift->user->name }}</td>
                                    <td>{{ $shift->ruleShift->type }}</td>
                                    <td>{{ $shift->start_date }}</td>
                                    <td>n/d</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm editShiftBtn"
                                            data-id="{{ $shift->id }}"
                                            data-user="{{ $shift->user_id }}"
                                            data-rule="{{ $shift->rule_id }}"
                                            data-start="{{ $shift->start_date }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>

                                        <button class="btn btn-danger btn-sm deleteShiftBtn"
                                            data-id="{{ $shift->id }}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>


            @endforeach



        </div>

        <!-- History Section -->
        <div class="tab-pane fade" id="history" role="tabpanel">
            <div id="historyFilter" class="card shadow-sm mb-4 d-none">
                <div class="card-header">
                    <h5 class="text-primary mt-2"><i class="fas fa-filter"></i> Filter Shift History</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('time.set.shift.index') }}" method="GET" class="row g-3">
                        <input type="hidden" name="tab" value="history">
                        <div class="col-md-4">
                            <label class="form-label">Employee</label>
                            <select class="form-select" name="employee_history">
                                <option value="">All Employees</option>
                                @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Position</label>
                            <select class="form-select" name="position_history">
                                <option value="">All Positions</option>
                                @foreach($positions as $position)
                                <option value="{{ $position }}" {{ request('position') == $position ? 'selected' : '' }}>{{ $position }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Department</label>
                            <select class="form-select" name="department_history">
                                <option value="">All Departments</option>
                                @foreach($departments as $department)
                                <option value="{{ $department }}" {{ request('department') == $department ? 'selected' : '' }}>{{ $department }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Rule</label>
                            <select class="form-select" name="type_history">
                                <option value="">All Rules</option>
                                @foreach($rules as $rule)
                                <option value="{{ $rule->id}}" {{ request('type') == $rule->id ? 'selected' : '' }}>{{ $rule->type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date_history" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date_history" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('time.set.shift.index') }}?tab=history" class="btn btn-secondary">
                                <i class="fas fa-undo me-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="text-primary mt-2"><i class="fas fa-history"></i> Employee Shift History</h5>
                </div>
            </div>
            @foreach($employeeShiftsHistory as $ruleId => $shifts)
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <h3 class="text-primary">{{ $shifts->first()->ruleShift->type }}</h3>
                        <table id="historyShiftTable" class="table table-bordered table-hover mb-3 pt-3">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th>Employee</th>
                                    <th style="width: 20%">Rule</th>
                                    <th style="width: 20%">Start Date</th>
                                    <th style="width: 20%">End Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shifts as $index => $shift)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $shift->user->name }}</td>
                                    <td>{{ $shift->ruleShift->type }}</td>
                                    <td>{{ $shift->start_date }}</td>
                                    <td>{{ $shift->end_date }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
            @endforeach
        </div>


        <!-- request Section -->
        <div class="tab-pane fade" id="request" role="tabpanel">
            <!-- Request Filter Card -->
            <div id="requestFilter" class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="text-primary mt-2"><i class="fas fa-filter"></i> Filter Shift Requests</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('time.set.shift.index') }}" method="GET" class="row g-3">
                        <input type="hidden" name="tab" value="request">
                        <div class="col-md-4">
                            <label class="form-label">Employee</label>
                            <select class="form-select" name="employee_request">
                                <option value="">All Employees</option>
                                @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee_request') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Position</label>
                            <select class="form-select" name="position_request">
                                <option value="">All Positions</option>
                                @foreach($positions as $position)
                                <option value="{{ $position }}" {{ request('position_request') == $position ? 'selected' : '' }}>{{ $position }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Department</label>
                            <select class="form-select" name="department_request">
                                <option value="">All Departments</option>
                                @foreach($departments as $department)
                                <option value="{{ $department }}" {{ request('department_request') == $department ? 'selected' : '' }}>{{ $department }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Current Shift</label>
                            <select class="form-select" name="current_shift_request">
                                <option value="">All Current Shifts</option>
                                @foreach($rules as $rule)
                                <option value="{{ $rule->id}}" {{ request('current_shift_request') == $rule->id ? 'selected' : '' }}>{{ $rule->type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Requested Shift</label>
                            <select class="form-select" name="requested_shift_request">
                                <option value="">All Requested Shifts</option>
                                @foreach($rules as $rule)
                                <option value="{{ $rule->id}}" {{ request('requested_shift_request') == $rule->id ? 'selected' : '' }}>{{ $rule->type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status_request">
                                <option value="">All Statuses</option>
                                <option value="Pending" {{ request('status_request') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Approved" {{ request('status_request') == 'Approved' ? 'selected' : '' }}>Approved</option>
                                <option value="Declined" {{ request('status_request') == 'Declined' ? 'selected' : '' }}>Declined</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date_request" value="{{ request('start_date_request') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date_request" value="{{ request('end_date_request') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('time.set.shift.index') }}?tab=request" class="btn btn-secondary">
                                <i class="fas fa-undo me-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Pending Shift Change Requests Section -->
            <div id="pendingRequestsCard" class="card shadow-sm mb-4">
                <div class="card-header bg-warning">
                    <h5 class="text-white mt-2"><i class="fas fa-clock"></i> Pending Shift Change Requests</h5>
                </div>
                <div class="card-body">
                    @if($pendingShiftRequests->where('status_change', 'Pending')->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped mb-3 pt-3" id="pendingRequestsTable">
                            <thead class="table-dark">


                                <tr>
                                    <th>Employee</th>
                                    <th>Request Date</th>
                                    <th>Current Shift</th>
                                    <th>Requested Shift</th>
                                    <th>Exchange With</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th width="200">Reason</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingShiftRequests->where('status_change', 'Pending') as $request)
                                <tr class="status-pending">
                                    <td>{{ $request->user->name }}</td>
                                    <td>{{ Carbon\Carbon::parse($request->created_at)->format('Y-m-d') }}</td>
                                    <td>
                                        @if($request->ruleShiftBefore)
                                        <span class="badge rounded-pill bg-{{ $request->ruleShiftBefore->type == 'Morning' ? 'primary' : ($request->ruleShiftBefore->type == 'Afternoon' ? 'warning' : 'secondary') }}">
                                            {{ $request->ruleShiftBefore->type }}
                                        </span><br>
                                        <small>
                                            @php
                                            $days = json_decode($request->ruleShiftBefore->days);
                                            $hourStart = json_decode($request->ruleShiftBefore->hour_start);
                                            $hourEnd = json_decode($request->ruleShiftBefore->hour_end);
                                            $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                            $schedule = [];

                                            // Get start and end dates
                                            $startDate = Carbon\Carbon::parse($request->date_change_start);
                                            $endDate = Carbon\Carbon::parse($request->date_change_end);

                                            // Loop through the range
                                            $currentDate = clone $startDate;
                                            while ($currentDate <= $endDate) {
                                                $dayOfWeek=$currentDate->dayOfWeek;
                                                // Adjust for Carbon's day numbering (0=Sunday, 1=Monday)
                                                $dayIndex = $dayOfWeek == 0 ? 6 : $dayOfWeek - 1;

                                                if(isset($days[$dayIndex]) && $days[$dayIndex]) {
                                                $dayName = substr($dayNames[$dayIndex], 0, 3);
                                                $schedule[] = $dayName . ': ' . $hourStart[$dayIndex] . '-' . $hourEnd[$dayIndex];
                                                }

                                                $currentDate->addDay();
                                                }

                                                echo implode('<br>', $schedule);
                                                @endphp
                                        </small>
                                        @else
                                        <span class="badge bg-secondary">Unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->ruleShiftAfter)
                                        <span class="badge rounded-pill bg-{{ $request->ruleShiftAfter->type == 'Morning' ? 'primary' : ($request->ruleShiftAfter->type == 'Afternoon' ? 'warning' : 'secondary') }}">
                                            {{ $request->ruleShiftAfter->type }}
                                        </span><br>
                                        <small>
                                            @php
                                            $days = json_decode($request->ruleShiftAfter->days);
                                            $hourStart = json_decode($request->ruleShiftAfter->hour_start);
                                            $hourEnd = json_decode($request->ruleShiftAfter->hour_end);
                                            $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                            $schedule = [];

                                            // Get start and end dates
                                            $startDate = Carbon\Carbon::parse($request->date_change_start);
                                            $endDate = Carbon\Carbon::parse($request->date_change_end);

                                            // Loop through the range
                                            $currentDate = clone $startDate;
                                            while ($currentDate <= $endDate) {
                                                $dayOfWeek=$currentDate->dayOfWeek;
                                                // Adjust for Carbon's day numbering (0=Sunday, 1=Monday)
                                                $dayIndex = $dayOfWeek == 0 ? 6 : $dayOfWeek - 1;

                                                if(isset($days[$dayIndex]) && $days[$dayIndex]) {
                                                $dayName = substr($dayNames[$dayIndex], 0, 3);
                                                $schedule[] = $dayName . ': ' . $hourStart[$dayIndex] . '-' . $hourEnd[$dayIndex];
                                                }

                                                $currentDate->addDay();
                                                }

                                                echo implode('<br>', $schedule);
                                                @endphp
                                        </small>
                                        @else
                                        <span class="badge bg-secondary">Unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->exchangeUser)
                                        {{ $request->exchangeUser->name }}
                                        @else
                                        <span class="badge bg-secondary">No Exchange</span>
                                        @endif
                                    </td>
                                    <td>{{ Carbon\Carbon::parse($request->date_change_start)->format('Y-m-d') }}</td>
                                    <td>{{ Carbon\Carbon::parse($request->date_change_end)->format('Y-m-d') }}</td>
                                    <td>
                                        {{ $request->reason_change }}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-success approve-request" data-id="{{ $request->id }}" data-bs-toggle="tooltip" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger decline-request" data-id="{{ $request->id }}" data-bs-toggle="tooltip" title="Decline">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <p class="text-center mb-0">No pending shift change requests found.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Approved Shift Change Requests Section -->
            <div id="approvedRequestsCard" class="card shadow-sm mb-4">
                <div class="card-header bg-success">
                    <h5 class="text-white mt-2"><i class="fas fa-check-circle"></i> Approved Shift Change Requests</h5>
                </div>
                <div class="card-body">
                    @if($pendingShiftRequests->where('status_change', 'Approved')->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped mb-3 pt-3" id="approvedRequestsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Employee</th>
                                    <th>Request Date</th>
                                    <th>Current Shift</th>
                                    <th>Requested Shift</th>
                                    <th>Exchange With</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Reason</th>
                                    <th>Approved By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingShiftRequests->where('status_change', 'Approved') as $request)
                                <tr class="status-approved">
                                    <td>{{ $request->user->name }}</td>
                                    <td>{{ Carbon\Carbon::parse($request->created_at)->format('Y-m-d') }}</td>
                                    <td>
                                        @if($request->ruleShiftBefore)
                                        <span class="badge rounded-pill bg-{{ $request->ruleShiftBefore->type == 'Morning' ? 'primary' : ($request->ruleShiftBefore->type == 'Afternoon' ? 'warning' : 'secondary') }}">
                                            {{ $request->ruleShiftBefore->type }}
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">Unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->ruleShiftAfter)
                                        <span class="badge rounded-pill bg-{{ $request->ruleShiftAfter->type == 'Morning' ? 'primary' : ($request->ruleShiftAfter->type == 'Afternoon' ? 'warning' : 'secondary') }}">
                                            {{ $request->ruleShiftAfter->type }}
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">Unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->exchangeUser)
                                        {{ $request->exchangeUser->name }}
                                        @else
                                        <span class="badge bg-secondary">No Exchange</span>
                                        @endif
                                    </td>
                                    <td>{{ Carbon\Carbon::parse($request->date_change_start)->format('Y-m-d') }}</td>
                                    <td>{{ Carbon\Carbon::parse($request->date_change_end)->format('Y-m-d') }}</td>
                                    <td>
                                        {{ $request->reason_change }}
                                    </td>
                                    <td>
                                        @if($request->answer_user_id && App\Models\User::find($request->answer_user_id))
                                        {{ App\Models\User::find($request->answer_user_id)->name }}
                                        @else
                                        <span class="badge bg-secondary">Unknown</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <p class="text-center mb-0">No approved shift change requests found.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Declined Shift Change Requests Section -->
            <div id="declinedRequestsCard" class="card shadow-sm mb-4">
                <div class="card-header bg-danger">
                    <h5 class="text-white mt-2"><i class="fas fa-times-circle"></i> Declined Shift Change Requests</h5>
                </div>
                <div class="card-body">
                    @if($pendingShiftRequests->where('status_change', 'Declined')->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped mb-3 pt-3" id="declinedRequestsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Employee</th>
                                    <th>Request Date</th>
                                    <th>Current Shift</th>
                                    <th>Requested Shift</th>
                                    <th>Exchange With</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Request Reason</th>
                                    <th>Declined By</th>
                                    <th>Decline Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingShiftRequests->where('status_change', 'Declined') as $request)
                                <tr class="status-declined">
                                    <td>{{ $request->user->name }}</td>
                                    <td>{{ Carbon\Carbon::parse($request->created_at)->format('Y-m-d') }}</td>
                                    <td>
                                        @if($request->ruleShiftBefore)
                                        <span class="badge rounded-pill bg-{{ $request->ruleShiftBefore->type == 'Morning' ? 'primary' : ($request->ruleShiftBefore->type == 'Afternoon' ? 'warning' : 'secondary') }}">
                                            {{ $request->ruleShiftBefore->type }}
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">Unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->ruleShiftAfter)
                                        <span class="badge rounded-pill bg-{{ $request->ruleShiftAfter->type == 'Morning' ? 'primary' : ($request->ruleShiftAfter->type == 'Afternoon' ? 'warning' : 'secondary') }}">
                                            {{ $request->ruleShiftAfter->type }}
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">Unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->exchangeUser)
                                        {{ $request->exchangeUser->name }}
                                        @else
                                        <span class="badge bg-secondary">No Exchange</span>
                                        @endif
                                    </td>
                                    <td>{{ Carbon\Carbon::parse($request->date_change_start)->format('Y-m-d') }}</td>
                                    <td>{{ Carbon\Carbon::parse($request->date_change_end)->format('Y-m-d') }}</td>
                                    <td>
                                        {{ $request->reason_change }}
                                    </td>
                                    <td>
                                        @if($request->answer_user_id && App\Models\User::find($request->answer_user_id))
                                        {{ App\Models\User::find($request->answer_user_id)->name }}
                                        @else
                                        <span class="badge bg-secondary">Unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $request->declined_reason }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <p class="text-center mb-0">No declined shift change requests found.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Modals -->
            <!-- Reason Modal -->
            <div class="modal fade" id="reasonModal" tabindex="-1" aria-labelledby="reasonModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="reasonModalLabel">Request Reason</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p id="reasonText"></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Decline Reason Modal -->
            <div class="modal fade" id="declineModal" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="declineModalLabel">Decline Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="declineForm" method="POST">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="request_id" id="declineRequestId">
                                <div class="mb-3">
                                    <label for="declined_reason" class="form-label">Reason for Declining</label>
                                    <textarea class="form-control" name="declined_reason" id="declined_reason" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Decline Request</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

         
        </div>





    </div>
</div>



<!-- Edit Shift Modal -->
<div class="modal fade" id="editShiftModal" tabindex="-1" aria-labelledby="editShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editShiftModalLabel">Edit Employee Shift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editShiftForm">
                    <input type="hidden" id="editShiftId">
                    <input type="hidden" id="editUserId">



                    <div class="mb-3">
                        <label class="form-label">Rule</label>
                        <select class="form-select" id="editRule">
                            @foreach($rules as $rule)
                            <option value="{{ $rule->id }}">{{ $rule->type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="editStartDate">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Shift</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Exchange Shift Modal -->
<div class="modal fade" id="exchangeShiftModal" tabindex="-1" aria-labelledby="exchangeShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exchange Employee Shifts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="exchangeShiftForm">
                    @csrf
                    <div class="mb-3">
                        <label for="start_date" class="form-label">New Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Exchange Shifts</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Decline Reason Modal -->
<div class="modal fade" id="declineReasonModal" tabindex="-1" aria-labelledby="declineReasonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="declineReasonModalLabel">Decline Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="declineReasonForm">
                    <input type="hidden" id="request_id" name="request_id">
                    <div class="mb-3">
                        <label for="decline_reason" class="form-label">Reason for Declining</label>
                        <textarea class="form-control" id="decline_reason" name="decline_reason" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="submitDecline">Submit</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Handle URL parameters for tab navigation
        var activeTab = new URLSearchParams(window.location.search).get('tab');
        if (activeTab) {
            $('.nav-tabs a[href="#' + activeTab + '"]').tab('show');
        }

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialize DataTables with common settings
        $('#currentShiftTable, #historyShiftTable').DataTable();
        $('.dataTables_filter input').addClass('ms-2');

        // Configure request tables with consistent options
        const tableConfig = {
            "order": [
                [1, "desc"]
            ],
            "pageLength": 10,
            "responsive": true,
            "language": {
                "search": ""
            }
        };

        const pendingTable = $('#pendingRequestsTable').DataTable(tableConfig);
        const approvedTable = $('#approvedRequestsTable').DataTable(tableConfig);
        const declinedTable = $('#declinedRequestsTable').DataTable(tableConfig);

        // Tab handling - show/hide appropriate filters
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            let target = $(e.target).attr("href");
            $("#currentFilter, #historyFilter, #requestFilter").addClass("d-none");

            if (target === "#current") {
                $("#currentFilter").removeClass("d-none");
            } else if (target === "#history") {
                $("#historyFilter").removeClass("d-none");
            } else if (target === "#request") {
                $("#requestFilter").removeClass("d-none");
            }
        });

        // Edit shift functionality
        $('.editShiftBtn').on('click', function() {
            let data = $(this).data();
            $('#editShiftId').val(data.id);
            $('#editUserId').val(data.user);
            $('#editRule').val(data.rule);
            $('#editStartDate').val(data.start);
            $('#editShiftModal').modal('show');
        });

        // Common AJAX response handler
        function handleAjaxResponse(response, successTitle, successMessage) {
            Swal.fire({
                icon: 'success',
                title: successTitle,
                text: successMessage,
                timer: 2000,
                showConfirmButton: false
            }).then(() => location.reload());
        }

        // Common AJAX error handler
        function handleAjaxError(xhr, errorTitle = "Error") {
            let errorMsg = xhr.responseJSON?.message || "An error occurred.";
            Swal.fire(errorTitle, errorMsg, "error");
        }

        // Submit Edit Form
        $('#editShiftForm').on('submit', function(e) {
            e.preventDefault();
            let shiftId = $('#editShiftId').val();

            $.ajax({
                url: `/time_management/set_shift/update/${shiftId}`,
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    rule_id: $('#editRule').val(),
                    user_id: $('#editUserId').val(),
                    start_date: $('#editStartDate').val()
                },
                success: function(response) {
                    handleAjaxResponse(response, 'Shift Updated', 'Employee shift has been updated successfully!');
                },
                error: handleAjaxError
            });
        });

        // Delete Shift with Confirmation
        $('.deleteShiftBtn').on('click', function() {
            let shiftId = $(this).data('id');

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/time_management/set_shift/delete/${shiftId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            handleAjaxResponse(response, 'Deleted!', 'Employee shift has been deleted.');
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong while deleting the shift.'
                            });
                        }
                    });
                }
            });
        });

        // Exchange Shift Form Submission
        $("#exchangeShiftForm").submit(function(event) {
            event.preventDefault();
            $.ajax({
                url: "{{ route('time.set.shift.exchange') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    handleAjaxResponse(response, 'Success!', response.message);
                },
                error: handleAjaxError
            });
        });

        // Handle approve request
        $('.approve-request').on('click', function() {
            const requestId = $(this).data('id');
            const row = $(this).closest('tr');

            Swal.fire({
                title: 'Approve Request',
                text: "Are you sure you want to approve this shift change request?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/time_management/set_shift/approve/' + requestId,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#pendingRequestsTable').DataTable().row(row).remove().draw();
                                handleAjaxResponse(response, 'Approved!', 'The shift change request has been approved.');
                            } else {
                                Swal.fire('Error!', response.message || 'Something went wrong.', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'There was a problem approving the request.', 'error');
                        }
                    });
                }
            });
        });

        // Handle decline request
        $('.decline-request').on('click', function() {
            $('#request_id').val($(this).data('id'));
            $('#declineReasonModal').modal('show');
        });

        // Handle decline form submission
        $('#submitDecline').on('click', function() {
            const requestId = $('#request_id').val();
            const declineReason = $('#decline_reason').val();
            const row = $('[data-id="' + requestId + '"]').closest('tr');

            if (!declineReason) {
                alert('Please provide a reason for declining.');
                return;
            }

            $.ajax({
                url: '/time_management/set_shift/decline/' + requestId,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    decline_reason: declineReason
                },
                success: function(response) {
                    if (response.success) {
                        $('#declineReasonModal').modal('hide');
                        $('#pendingRequestsTable').DataTable().row(row).remove().draw();
                        handleAjaxResponse(response, 'Declined!', 'The shift change request has been declined.');
                    } else {
                        Swal.fire('Error!', response.message || 'Something went wrong.', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'There was a problem declining the request.', 'error');
                }
            });
        });

        // Filter functionality
        function toggleResultCards() {
            const statusFilter = $('select[name="status_request"]').val();
            $('#pendingRequestsCard, #approvedRequestsCard, #declinedRequestsCard').hide();

            if (statusFilter === 'Pending') {
                $('#pendingRequestsCard').show();
            } else if (statusFilter === 'Approved') {
                $('#approvedRequestsCard').show();
            } else if (statusFilter === 'Declined') {
                $('#declinedRequestsCard').show();
            } else {
                $('#pendingRequestsCard, #approvedRequestsCard, #declinedRequestsCard').show();
            }
        }

        // Apply status filter on page load and change
        toggleResultCards();
        $('select[name="status_request"]').change(toggleResultCards);

        // Handle status filter in URL params
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('status_request')) {
            $('select[name="status_request"]').val(urlParams.get('status_request'));
            toggleResultCards();
        }
    });
</script>
@endpush