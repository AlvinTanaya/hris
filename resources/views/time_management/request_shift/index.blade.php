@extends('layouts.app')

@section('content')
<style>
    /* Modal and Card Styling */
    .modal-content {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }

    .modal-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    }

    .modal-header .btn-close {
        color: white;
        opacity: 0.8;
    }

    .modal-header .btn-close:hover {
        opacity: 1;
    }

    .modal-title {
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .modal-title i {
        margin-right: 0.5rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid rgba(0, 0, 0, 0.08);
    }

    .card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.375rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        padding: 1rem 1.25rem;
    }

    .card-header .card-title {
        font-weight: 600;
        font-size: 1rem;
        margin: 0;
        color: #343a40;
    }

    .card-body {
        padding: 1.25rem;
        background-color: white;
    }

    .card-body p {
        margin-bottom: 0.75rem;
    }

    .card-body p:last-child {
        margin-bottom: 0;
    }

    /* Badge Styling */
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
        border-radius: 0.25rem;
    }

    .badge-shift {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }

    /* Status Background Colors */
    .status-pending {
        background-color: rgba(255, 193, 7, 0.1);
        border: 1px solid rgba(255, 193, 7, 0.5);
        color: #856404;
    }

    .status-approved {
        background-color: rgba(25, 135, 84, 0.1);
        border: 1px solid rgba(25, 135, 84, 0.5);
        color: #155724;
    }

    .status-declined {
        background-color: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.5);
        color: #721c24;
    }

    /* Approval Badges */
    .approval-badge {
        display: inline-block;
        width: 100%;
        padding: 0.5rem;
        border-radius: 0.25rem;
        text-align: center;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .badge-pending {
        background-color: #ffc107;
        color: #212529;
    }

    .badge-approved {
        background-color: #198754;
        color: white;
    }

    .badge-declined {
        background-color: #dc3545;
        color: white;
    }

    /* Button Styling */
    .btn {
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        transition: all 0.2s;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }

    .btn-action {
        padding: 0.25rem 0.5rem;
        line-height: 1;
        font-size: 0.875rem;
    }

    /* Table Styling */
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #495057;
        padding: 0.75rem 1rem;
    }

    .table td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
    }

    .schedule-details {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }

    /* DataTables Styling */
    .dataTables_wrapper .dataTables_filter {
        margin: 1rem 0;
        padding-right: 1rem;
    }

    .dataTables_wrapper .dataTables_filter input {
        margin-left: 0.5rem;
        padding: 0.375rem 0.75rem;
        border-radius: 0.25rem;
        border: 1px solid #ced4da;
    }

    .dataTables_wrapper .dataTables_length {
        margin: 1rem 0;
        padding-left: 1rem;
    }

    .dataTables_wrapper .dataTables_length label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .dataTables_wrapper .dataTables_length select {
        margin: 0 0.5rem;
        padding: 0.375rem 0.5rem;
        border-radius: 0.25rem;
        border: 1px solid #ced4da;
    }

    .dataTables_info,
    .dataTables_paginate {
        margin-top: 1rem;
        padding: 0 1rem;
    }

    /* Navigation Tabs */
    .nav-tabs {
        border-bottom: none;
        border-radius: 0.5rem 0.5rem 0 0;
        overflow: hidden;
    }

    .nav-tabs .nav-link {
        color: rgba(255, 255, 255, 0.85);
        font-weight: 500;
        padding: 1rem 1.25rem;
        transition: all 0.2s ease;
        border: none;
        border-bottom: 3px solid transparent;
        width: 100%;
        display: inline-flex;
        justify-content: center;
        align-items: center;
    }

    .nav-tabs .nav-item {
        width: 33.333%;
        text-align: center;
    }

    .nav-tabs .nav-link:hover {
        color: white;
        background-color: #0d6efd;
        background-color: rgba(255, 255, 255, 0.1);
    }

    .nav-tabs .nav-link.active {
        color: white;
        border-bottom: 3px solid white;
        background-color: rgba(255, 255, 255, 0.2);
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .nav-tabs .nav-link {
            padding: 0.75rem 0.5rem;
            font-size: 0.875rem;
        }

        .modal-dialog {
            margin: 0.5rem;
        }

        .card-header .card-title {
            font-size: 0.875rem;
        }

        .modal-body {
            padding: 1rem;
        }
    }

    /* Icons in Card Headers */
    .card-header i {
        color: #0d6efd;
        margin-right: 0.5rem;
    }

    /* Additional Card Border for Declined Reason */
    .border-danger {
        border: 1px solid #dc3545 !important;
    }

    /* Shadow effect for cards */
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }

    /* Background for approval sections */
    .bg-light {
        background-color: #f8f9fa !important;
    }

    /* Spacing and Typography */
    .fs-6 {
        font-size: 1rem !important;
    }

    .border-bottom {
        border-bottom: 1px solid #dee2e6 !important;
    }

    .pb-2 {
        padding-bottom: 0.5rem !important;
    }
</style>

<div class="container-fluid py-4">

    <div class="row">
        <div class="col-12 text-center mb-5">
            <h1 class="text-warning">
                <i class="fas fa-user-clock me-2"></i>Shift Change Request Management
            </h1>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">
                <i class="fas fa-filter me-2"></i>Filter Requests
            </h5>
        </div>

        <div class="collapse show" id="filterCollapse">
            <div class="card-body pb-3">
                <form action="{{ route('change_shift.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4 col-sm-6">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Declined" {{ request('status') == 'Declined' ? 'selected' : '' }}>Declined</option>
                        </select>
                    </div>

                    @if($isAdmin)
                    <div class="col-md-4 col-sm-6">
                        <label for="department_id" class="form-label">Department</label>
                        <select name="department_id" id="department_id" class="form-select">
                            <option value="">All Departments</option>
                            @foreach(\App\Models\EmployeeDepartment::all() as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->department }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="col-md-4 col-sm-6">
                        <label for="user_id" class="form-label">Employee</label>
                        <select name="user_id" id="user_id" class="form-select">
                            <option value="">All Employees</option>
                            @foreach(\App\Models\User::where('employee_status', '!=', 'Inactive')->get() as $employee)
                            <option value="{{ $employee->id }}" {{ request('user_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-4 col-sm-6 d-flex justify-content-end align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-2"></i>Apply Filters
                        </button>
                        <a href="{{ route('change_shift.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-undo me-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="requestTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button">
                <i class="fas fa-clock me-2"></i>Pending
                <span class="badge bg-warning ms-1">{{ $shiftChanges->where('status_change', 'Pending')->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button">
                <i class="fas fa-check-circle me-2"></i>Approved
                <span class="badge bg-success ms-1">{{ $shiftChanges->where('status_change', 'Approved')->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="declined-tab" data-bs-toggle="tab" data-bs-target="#declined" type="button">
                <i class="fas fa-times-circle me-2"></i>Declined
                <span class="badge bg-danger ms-1">{{ $shiftChanges->where('status_change', 'Declined')->count() }}</span>
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Pending Requests Tab -->
        <div class="tab-pane fade show active" id="pending" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="pendingTable">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Request Date</th>
                                    <th>Current Shift</th>
                                    <th>Requested Shift</th>
                                    <th>Exchange With</th>
                                    <th>Date Range</th>
                                    <th>Approvals</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shiftChanges->where('status_change', 'Pending') as $request)
                                <tr class="status-pending">
                                    <td>{{ $request->user->name }}</td>
                                    <td>{{ $request->user->department->department ?? 'N/A' }}</td>
                                    <td>{{ $request->user->position->position ?? 'N/A' }}</td>
                                    <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        @if($request->ruleShiftBefore)
                                        <span class="badge rounded-pill bg-{{ $request->ruleShiftBefore->type == 'Morning' ? 'primary' : ($request->ruleShiftBefore->type == 'Afternoon' ? 'warning' : 'secondary') }}">
                                            {{ $request->ruleShiftBefore->type }}
                                        </span>
                                        <div class="schedule-details mt-1">
                                            @php
                                            $days = json_decode($request->ruleShiftBefore->days);
                                            $hourStart = json_decode($request->ruleShiftBefore->hour_start);
                                            $hourEnd = json_decode($request->ruleShiftBefore->hour_end);
                                            $dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                                            $schedule = [];

                                            for ($i = 0; $i < 7; $i++) {
                                                if(isset($days[$i]) && $days[$i]) {
                                                $schedule[]=$dayNames[$i] . ': ' . $hourStart[$i] . '-' . $hourEnd[$i];
                                                }
                                                }

                                                echo implode('<br>', $schedule);
                                                @endphp
                                        </div>
                                        @else
                                        <span class="badge bg-secondary">Unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->ruleShiftAfter)
                                        <span class="badge rounded-pill bg-{{ $request->ruleShiftAfter->type == 'Morning' ? 'primary' : ($request->ruleShiftAfter->type == 'Afternoon' ? 'warning' : 'secondary') }}">
                                            {{ $request->ruleShiftAfter->type }}
                                        </span>
                                        <div class="schedule-details mt-1">
                                            @php
                                            $days = json_decode($request->ruleShiftAfter->days);
                                            $hourStart = json_decode($request->ruleShiftAfter->hour_start);
                                            $hourEnd = json_decode($request->ruleShiftAfter->hour_end);
                                            $dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                                            $schedule = [];

                                            for ($i = 0; $i < 7; $i++) {
                                                if(isset($days[$i]) && $days[$i]) {
                                                $schedule[]=$dayNames[$i] . ': ' . $hourStart[$i] . '-' . $hourEnd[$i];
                                                }
                                                }

                                                echo implode('<br>', $schedule);
                                                @endphp
                                        </div>
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
                                    <td>
                                        {{ \Carbon\Carbon::parse($request->date_change_start)->format('Y-m-d') }}
                                        <br>to<br>
                                        {{ \Carbon\Carbon::parse($request->date_change_end)->format('Y-m-d') }}
                                    </td>
                                    <td>
                                        <span class="approval-badge badge bg-{{ $request->dept_approval_status == 'Pending' ? 'warning' : ($request->dept_approval_status == 'Approved' ? 'success' : 'danger') }}">
                                            Department: {{ $request->dept_approval_status }}
                                        </span>
                                        <span class="approval-badge badge bg-{{ $request->admin_approval_status == 'Pending' ? 'warning' : ($request->admin_approval_status == 'Approved' ? 'success' : 'danger') }}">
                                            Admin: {{ $request->admin_approval_status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-info mb-1 btn-view"
                                                data-id="{{ $request->id }}"
                                                data-bs-toggle="tooltip"
                                                title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                            @if($request->can_approve_dept)
                                            <button type="button" class="btn btn-sm btn-outline-success mb-1 btn-approve-dept"
                                                data-id="{{ $request->id }}"
                                                data-bs-toggle="tooltip"
                                                title="Approve as Department Head">
                                                <i class="fas fa-check"></i> Dept
                                            </button>
                                            @endif

                                            @if($request->can_approve_admin)
                                            <button type="button" class="btn btn-sm btn-outline-success mb-1 btn-approve-admin"
                                                data-id="{{ $request->id }}"
                                                data-bs-toggle="tooltip"
                                                title="Approve as Admin">
                                                <i class="fas fa-check"></i> Admin
                                            </button>
                                            @endif



                                            @if($request->can_approve_dept || $request->can_approve_admin)
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-decline"
                                                data-id="{{ $request->id }}"
                                                data-bs-toggle="tooltip"
                                                title="Decline Request">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approved Requests Tab -->
        <div class="tab-pane fade" id="approved" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="approvedTable">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Request Date</th>
                                    <th>Current Shift</th>
                                    <th>Requested Shift</th>
                                    <th>Exchange With</th>
                                    <th>Date Range</th>
                                    <th>Approved By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shiftChanges->where('status_change', 'Approved') as $request)
                                <tr class="status-approved">
                                    <td>{{ $request->user->name }}</td>
                                    <td>{{ $request->user->department->department ?? 'N/A' }}</td>
                                    <td>{{ $request->user->position->position ?? 'N/A' }}</td>
                                    <td>{{ $request->created_at->format('Y-m-d') }}</td>
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
                                    <td>
                                        {{ \Carbon\Carbon::parse($request->date_change_start)->format('Y-m-d') }}
                                        <br>to<br>
                                        {{ \Carbon\Carbon::parse($request->date_change_end)->format('Y-m-d') }}
                                    </td>
                                    <td>
                                        <div>
                                            <small class="d-block">Dept:
                                                @if($request->dept_approval_user_id)
                                                {{ \App\Models\User::find($request->dept_approval_user_id)->name ?? 'Unknown' }}
                                                @else
                                                N/A
                                                @endif
                                            </small>
                                            <small class="d-block">Admin:
                                                @if($request->admin_approval_user_id)
                                                {{ \App\Models\User::find($request->admin_approval_user_id)->name ?? 'Unknown' }}
                                                @else
                                                N/A
                                                @endif
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-info btn-view"
                                            data-id="{{ $request->id }}"
                                            data-bs-toggle="tooltip"
                                            title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Declined Requests Tab -->
        <div class="tab-pane fade" id="declined" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="declinedTable">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Request Date</th>
                                    <th>Current Shift</th>
                                    <th>Requested Shift</th>
                                    <th>Exchange With</th>
                                    <th>Date Range</th>
                                    <th>Declined By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shiftChanges->where('status_change', 'Declined') as $request)
                                <tr class="status-declined">
                                    <td>{{ $request->user->name }}</td>
                                    <td>{{ $request->user->department->department ?? 'N/A' }}</td>
                                    <td>{{ $request->user->position->position ?? 'N/A' }}</td>
                                    <td>{{ $request->created_at->format('Y-m-d') }}</td>
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
                                    <td>
                                        {{ \Carbon\Carbon::parse($request->date_change_start)->format('Y-m-d') }}
                                        <br>to<br>
                                        {{ \Carbon\Carbon::parse($request->date_change_end)->format('Y-m-d') }}
                                    </td>
                                    <td>
                                        @if($request->dept_approval_status === 'Declined' && $request->deptApprovalUser)
                                        <span class="fw-bold">Dept:</span> {{ $request->deptApprovalUser->name }}<br>
                                        @endif

                                        @if($request->admin_approval_status === 'Declined' && $request->adminApprovalUser)
                                        <span class="fw-bold">Admin:</span> {{ $request->adminApprovalUser->name }}
                                        @endif

                                        @if($request->dept_approval_status !== 'Declined' && $request->admin_approval_status !== 'Declined')
                                        Unknown
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-info btn-view-declined"
                                            data-id="{{ $request->id }}"
                                            data-reason="{{ $request->declined_reason }}"
                                            data-bs-toggle="tooltip"
                                            title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Request Modal -->
<div class="modal fade" id="viewRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i>Shift Change Request Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- Employee and Request Info Section -->
                    <div class="col-12 mb-2">
                        <div class="row g-4">
                            <!-- Employee Information Card -->
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="card-title mb-0"><i class="fas fa-user me-2"></i>Employee Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <strong>Name:</strong> <span id="view-employee-name"></span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Department:</strong> <span id="view-department"></span>
                                        </div>
                                        <div>
                                            <strong>Position:</strong> <span id="view-position"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Request Information Card -->
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="card-title mb-0"><i class="fas fa-calendar-alt me-2"></i>Request Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <strong>Request Date:</strong> <span id="view-request-date"></span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Date Range:</strong> <span id="view-date-range"></span>
                                        </div>
                                        <div>
                                            <strong>Exchange With:</strong> <span id="view-exchange-with"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shift Information Section -->
                    <div class="col-12 mb-2">
                        <div class="row g-4">
                            <!-- Current Shift Card -->
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="card-title mb-0"><i class="fas fa-clock me-2"></i>Current Shift</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="view-current-shift"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Requested Shift Card -->
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="card-title mb-0"><i class="fas fa-exchange-alt me-2"></i>Requested Shift</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="view-requested-shift"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reason for Change Card -->
                    <div class="col-12 mb-2">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0"><i class="fas fa-comment-alt me-2"></i>Reason for Change</h6>
                            </div>
                            <div class="card-body">
                                <p id="view-reason" class="mb-0"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Approval Status Card -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0"><i class="fas fa-check-circle me-2"></i>Approval Status</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <div class="p-3 rounded bg-light">
                                            <h6 class="mb-3 border-bottom pb-2">Department Approval</h6>
                                            <p class="mb-2">
                                                <span id="view-dept-approval" class="badge fs-6 d-block p-2 mb-2"></span>
                                            </p>
                                            <p id="view-dept-approver-container" class="mb-0">
                                                <strong>Approved By:</strong>
                                                <span id="view-dept-approver"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded bg-light">
                                            <h6 class="mb-3 border-bottom pb-2">Admin Approval</h6>
                                            <p class="mb-2">
                                                <span id="view-admin-approval" class="badge fs-6 d-block p-2 mb-2"></span>
                                            </p>
                                            <p id="view-admin-approver-container" class="mb-0">
                                                <strong>Approved By:</strong>
                                                <span id="view-admin-approver"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Declined Request Modal -->
<div class="modal fade" id="viewDeclinedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle me-2"></i>Declined Shift Change Request
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- Employee and Request Info Section -->
                    <div class="col-12 mb-2">
                        <div class="row g-4">
                            <!-- Employee Information Card -->
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="card-title mb-0"><i class="fas fa-user me-2"></i>Employee Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <strong>Name:</strong> <span id="declined-employee-name"></span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Department:</strong> <span id="declined-department"></span>
                                        </div>
                                        <div>
                                            <strong>Position:</strong> <span id="declined-position"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Request Information Card -->
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="card-title mb-0"><i class="fas fa-calendar-alt me-2"></i>Request Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <strong>Request Date:</strong> <span id="declined-request-date"></span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Date Range:</strong> <span id="declined-date-range"></span>
                                        </div>
                                        <div>
                                            <strong>Exchange With:</strong> <span id="declined-exchange-with"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shift Information Section -->
                    <div class="col-12 mb-2">
                        <div class="row g-4">
                            <!-- Current Shift Card -->
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="card-title mb-0"><i class="fas fa-clock me-2"></i>Current Shift</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="declined-current-shift"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Requested Shift Card -->
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="card-title mb-0"><i class="fas fa-exchange-alt me-2"></i>Requested Shift</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="declined-requested-shift"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reason for Change Card -->
                    <div class="col-12 mb-2">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0"><i class="fas fa-comment-alt me-2"></i>Reason for Change</h6>
                            </div>
                            <div class="card-body">
                                <p id="declined-reason-change" class="mb-0"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Reason for Declining Card -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm border-danger">
                            <div class="card-header bg-danger text-white">
                                <h6 class="card-title mb-0"><i class="fas fa-ban me-2"></i>Reason for Declining</h6>
                            </div>
                            <div class="card-body">
                                <p id="declined-reason" class="mb-0"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Decline Modal -->
<div class="modal fade" id="declineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle me-2"></i>Decline Shift Change Request
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <span>This action cannot be undone. The requestor will be notified of your decision.</span>
                </div>

                <p>Are you sure you want to decline this shift change request?</p>

                <form id="declineForm" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="declined_reason" class="form-label">Reason for Declining <span class="text-danger">*</span></label>
                        <textarea
                            class="form-control"
                            id="declined_reason"
                            name="declined_reason"
                            rows="3"
                            required
                            placeholder="Please provide a clear reason for declining this request..."></textarea>
                        <div class="form-text">
                            This reason will be visible to the employee who requested the shift change.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDecline">
                    <i class="fas fa-ban me-2"></i>Decline Request
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')




<script>
    $(document).ready(function() {
        // Initialize DataTables for each table
        $('#pendingTable, #approvedTable, #declinedTable').DataTable({

        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });


        // Handler untuk tombol view
        $(document).on('click', '.btn-view', function() {
            const requestId = $(this).data('id');

            $.ajax({
                url: `/time_management/request_shift/${requestId}`,
                type: 'GET',
                success: function(response) {
                    // Isi data ke modal
                    $('#view-employee-name').text(response.user.name);
                    $('#view-department').text(response.user.department);
                    $('#view-position').text(response.user.position);
                    $('#view-request-date').text(new Date(response.created_at).toLocaleDateString());
                    $('#view-date-range').text(`${response.date_change_start} to ${response.date_change_end}`);
                    $('#view-exchange-with').text(response.exchangeUser ? response.exchangeUser.name : 'No Exchange');
                    $('#view-reason').text(response.reason);

                    // Format shift before
                    let currentShiftHtml = formatShift(response.ruleShiftBefore);
                    $('#view-current-shift').html(currentShiftHtml);

                    // Format shift after
                    let requestedShiftHtml = formatShift(response.ruleShiftAfter);
                    $('#view-requested-shift').html(requestedShiftHtml);

                    // Approval status
                    updateApprovalStatus('dept', response.dept_approval_status, response.deptApprovalUser);
                    updateApprovalStatus('admin', response.admin_approval_status, response.adminApprovalUser);

                    // Tampilkan modal
                    const modal = new bootstrap.Modal(document.getElementById('viewRequestModal'));
                    modal.show();
                },
                error: function(xhr) {
                    console.error(xhr);
                    alert('Failed to load request details');
                }
            });
        });

        function formatShift(shift) {
            if (!shift) return '<span class="badge bg-secondary">Unknown Shift</span>';

            const typeClass = shift.type === 'Morning' ? 'primary' :
                (shift.type === 'Afternoon' ? 'warning' : 'secondary');

            let html = `<span class="badge bg-${typeClass} mb-2">${shift.type}</span>`;

            if (shift.days && shift.hour_start && shift.hour_end) {
                html += '<ul class="list-group">';
                for (let i = 0; i < shift.days.length; i++) {
                    html += `<li class="list-group-item p-2">${shift.days[i]}: ${shift.hour_start[i]} - ${shift.hour_end[i]}</li>`;
                }
                html += '</ul>';
            }

            return html;
        }

        function updateApprovalStatus(type, status, approver) {
            const statusClass = status === 'Pending' ? 'warning' :
                (status === 'Approved' ? 'success' : 'danger');

            $(`#view-${type}-approval`)
                .removeClass()
                .addClass(`badge bg-${statusClass}`)
                .text(status);

            if (status === 'Approved' && approver) {
                $(`#view-${type}-approver-container`).show();
                $(`#view-${type}-approver`).text(approver.name || 'Unknown');
            } else {
                $(`#view-${type}-approver-container`).hide();
            }
        }

        // View declined request details
        $(document).on('click', '.btn-view-declined', function() {
            const requestId = $(this).data('id');
            const declinedReason = $(this).data('reason');

            $.ajax({
                url: `/time_management/request_shift/${requestId}`,
                type: 'GET',
                success: function(response) {
                    // Fill employee info
                    $('#declined-employee-name').text(response.user.name);
                    $('#declined-department').text(response.user.department || 'N/A');
                    $('#declined-position').text(response.user.position || 'N/A');

                    // Fill request info
                    $('#declined-request-date').text(new Date(response.created_at).toLocaleDateString());
                    $('#declined-date-range').text(`${response.date_change_start} to ${response.date_change_end}`);
                    $('#declined-exchange-with').text(response.exchangeUser ? response.exchangeUser.name : 'No Exchange');
                    $('#declined-reason-change').text(response.reason);
                    $('#declined-reason').text(declinedReason || 'No reason provided');

                    // Format shift data
                    const formatShift = (shift) => {
                        if (!shift) return '<span class="badge bg-secondary">Unknown Shift</span>';

                        const typeClass = shift.type === 'Morning' ? 'primary' :
                            (shift.type === 'Afternoon' ? 'warning' : 'secondary');

                        let html = `<span class="badge bg-${typeClass} mb-2">${shift.type}</span>`;

                        if (shift.days && shift.hour_start && shift.hour_end) {
                            html += '<ul class="list-group">';
                            for (let i = 0; i < shift.days.length; i++) {
                                html += `<li class="list-group-item p-2">${shift.days[i]}: ${shift.hour_start[i]} - ${shift.hour_end[i]}</li>`;
                            }
                            html += '</ul>';
                        }

                        return html;
                    };

                    // Fill shift info
                    $('#declined-current-shift').html(formatShift(response.ruleShiftBefore));
                    $('#declined-requested-shift').html(formatShift(response.ruleShiftAfter));

                    // Show modal - cara yang lebih reliable untuk Bootstrap 5
                    const declinedModal = new bootstrap.Modal(document.getElementById('viewDeclinedModal'));
                    declinedModal.show();
                },
                error: function(xhr) {
                    console.error(xhr);
                    Swal.fire('Error!', 'Failed to load request details', 'error');
                }
            });
        });


        // Department approval button handler
        $('.btn-approve-dept').on('click', function() {
            const requestId = $(this).data('id');

            Swal.fire({
                title: 'Approve Department Request?',
                text: "Are you sure you want to approve this shift change request as department head?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/time_management/request_shift/approve/${requestId}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            approval_type: 'dept' // Add this parameter
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Approved!', response.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Failed to approve the request.', 'error');
                        }
                    });
                }
            });
        });

        // Admin approval button handler
        $('.btn-approve-admin').on('click', function() {
            const requestId = $(this).data('id');

            Swal.fire({
                title: 'Approve Admin Request?',
                text: "Are you sure you want to approve this shift change request as admin?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/time_management/request_shift/approve/${requestId}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            approval_type: 'admin' // Add this parameter
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Approved!', response.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Failed to approve the request.', 'error');
                        }
                    });
                }
            });
        });




        // Decline button handler
        $('.btn-decline').on('click', function() {
            const requestId = $(this).data('id');
            const requestorName = $(this).data('requestor') || 'this employee';

            // Reset form
            $('#declined_reason').val('');
            $('.decline-validation-error').remove();

            // Update modal content
            $('#declineForm').attr('action', `/time_management/request_shift/decline/${requestId}`);
            $('#declineModal .modal-body p').html(`Are you sure you want to decline the shift change request from <strong>${requestorName}</strong>?`);

            // Show modal
            $('#declineModal').modal('show');
        });

        // Confirm decline button handler
        $('#confirmDecline').on('click', function() {
            // Show loading state
            const $btn = $(this);
            const originalText = $btn.html();
            $btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');
            $btn.prop('disabled', true);

            // Validate form input
            const declinedReason = $('#declined_reason').val().trim();

            if (declinedReason === '') {
                $('.decline-validation-error').remove();
                $('#declined_reason').after('<div class="text-danger mt-1 decline-validation-error">Please provide a reason for declining</div>');

                // Reset button state
                $btn.html(originalText);
                $btn.prop('disabled', false);
                return;
            }

            // Get request ID from form action URL
            const requestId = $('#declineForm').attr('action').split('/').pop();

            // Submit using AJAX
            $.ajax({
                url: `/time_management/request_shift/decline/${requestId}`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    declined_reason: declinedReason
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Close modal
                        $('#declineModal').modal('hide');

                        // Show success message
                        Swal.fire({
                            title: 'Success!',
                            text: 'The shift change request has been declined.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Reload page or update UI
                            location.reload();
                        });
                    } else {
                        // Show error message
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Failed to decline the request.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });

                        // Reset button state
                        $btn.html(originalText);
                        $btn.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Something went wrong. Please try again.';

                    // Try to parse error message from response
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    // If validation errors exist
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.declined_reason) {
                            $('.decline-validation-error').remove();
                            $('#declined_reason').after(`<div class="text-danger mt-1 decline-validation-error">${errors.declined_reason[0]}</div>`);
                        }
                    }

                    // Show error message if not validation error
                    if (xhr.status !== 422) {
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }

                    // Reset button state
                    $btn.html(originalText);
                    $btn.prop('disabled', false);
                }
            });
        });


    });
</script>
@endpush