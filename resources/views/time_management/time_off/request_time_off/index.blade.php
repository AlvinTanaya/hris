@extends('layouts.app')

@section('content')


<div class="container-fluid py-4 time-off-container">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header d-flex align-items-center justify-content-between text-warning">
                <h1 class="mb-0">
                    <i class="fas fa-calendar-alt me-2 "></i> Time Off Management
                </h1>
                <div class="d-flex align-items-center">
                    <span class="badge bg-light text-warning me-2" style="font-size: 1.2rem;">
                        <i class="fas fa-user-clock me-1"></i> Total Requests: {{ $pendingCount + $approvedCount + $declinedCount }}
                    </span>
                </div>
            </div>
            <hr class="mt-2" style="border-top: 2px solid rgba(67, 97, 238, 0.1);">
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card filter-card mb-4">
        <div class="card-header">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="fas fa-sliders-h me-2"></i> Filter Time Off Requests
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('request.time.off.index') }}" method="GET" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="employee" class="form-label">Employee</label>
                        <select class="form-select" name="employee" id="employee">
                            <option value="">All Employees</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('employee') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="position_request" class="form-label">Position</label>
                        <select class="form-select" name="position_request" id="position_request">
                            <option value="">All Positions</option>
                            @foreach($positions as $position)
                            <option value="{{ $position }}" {{ request('position_request') == $position ? 'selected' : '' }}>
                                {{ $position }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="department_request" class="form-label">Department</label>
                        <select class="form-select" name="department_request" id="department_request">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                            <option value="{{ $department }}" {{ request('department_request') == $department ? 'selected' : '' }}>
                                {{ $department }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="time_off_type" class="form-label">Time Off Type</label>
                        <select class="form-select" name="time_off_type" id="time_off_type">
                            <option value="">All Types</option>
                            @foreach($timeOffPolicies as $policy)
                            <option value="{{ $policy->id }}" {{ request('time_off_type') == $policy->id ? 'selected' : '' }}>
                                {{ $policy->time_off_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" name="date" id="date" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="d-flex w-100 justify-content-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i> Apply Filters
                            </button>
                            <a href="{{ route('request.time.off.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4" id="timeOffTabs" role="tablist">
        <li class="nav-item flex-grow-1 text-center" role="presentation">
            <a class="nav-link active" id="pending-tab" data-bs-toggle="tab" href="#pending" role="tab" aria-controls="pending" aria-selected="true">
                <i class="fas fa-hourglass-half"></i>
                <span class="badge bg-warning ms-2">{{ $pendingCount }}</span> Pending
            </a>
        </li>
        <li class="nav-item flex-grow-1 text-center" role="presentation">
            <a class="nav-link" id="approved-tab" data-bs-toggle="tab" href="#approved" role="tab" aria-controls="approved" aria-selected="false">
                <i class="fas fa-check-circle"></i>
                <span class="badge bg-success ms-2">{{ $approvedCount }}</span> Approved
            </a>
        </li>
        <li class="nav-item flex-grow-1 text-center" role="presentation">
            <a class="nav-link" id="declined-tab" data-bs-toggle="tab" href="#declined" role="tab" aria-controls="declined" aria-selected="false">
                <i class="fas fa-times-circle"></i>
                <span class="badge bg-danger ms-2">{{ $declinedCount }}</span> Declined
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="timeOffTabsContent">
        <!-- Pending Requests Tab -->
        <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-hourglass-half text-warning me-2"></i> Pending Time Off Requests
                    </h5>
                    <span class="badge bg-light text-dark">
                        Showing {{ $pendingRequests->count() }} of {{ $pendingCount }} requests
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="pendingTable" class="table table-hover w-100">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Time Off Type</th>
                                    <th>Date Range</th>
                                    <th>Duration</th>
                                    <th>Approval Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingRequests as $request)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-2">
                                                <span class="avatar-initial p-2 rounded-circle bg-primary text-white">
                                                    {{ substr($request->user_name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>{{ $request->user_name }}</div>
                                        </div>
                                    </td>
                                    <td>{{ $request->department }}</td>
                                    <td>{{ $request->position }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $request->time_off_name }}
                                        </span>
                                    </td>
                                    <td>{{ $request->formatted_date }}</td>
                                    <td>{{ $request->duration }}</td>
                                    <td>
                                        <div class="d-flex flex-column gap-2">
                                            <span class="badge bg-{{ $request->dept_approval_status === 'Approved' ? 'success' : ($request->dept_approval_status === 'Declined' ? 'danger' : 'warning') }}">
                                                <i class="fas fa-{{ $request->dept_approval_status === 'Approved' ? 'check' : ($request->dept_approval_status === 'Declined' ? 'times' : 'clock') }} me-1"></i>
                                                Dept: {{ $request->dept_approval_status ?? 'Pending' }}
                                            </span>
                                            <span class="badge bg-{{ $request->admin_approval_status === 'Approved' ? 'success' : ($request->admin_approval_status === 'Declined' ? 'danger' : 'warning') }}">
                                                <i class="fas fa-{{ $request->admin_approval_status === 'Approved' ? 'check' : ($request->admin_approval_status === 'Declined' ? 'times' : 'clock') }} me-1"></i>
                                                Admin: {{ $request->admin_approval_status ?? 'Pending' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if(isset($request->file_reason_path) && !empty($request->file_reason_path))
                                            <button type="button" class="btn btn-info btn-sm view-file-btn"
                                                data-bs-toggle="tooltip" title="View File"
                                                data-file="{{ $request->file_reason_path }}">
                                                <i class="fas fa-file-alt"></i>
                                            </button>
                                            @endif

                                            @if($request->can_approve_dept)
                                            <button type="button" class="btn btn-success btn-sm approve-btn"
                                                data-bs-toggle="tooltip" title="Approve (Department)"
                                                data-id="{{ $request->id }}"
                                                data-employee="{{ $request->user_name }}"
                                                data-approval-type="dept">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            @endif

                                            @if($request->can_approve_admin)
                                            <button type="button" class="btn btn-primary btn-sm approve-btn"
                                                data-bs-toggle="tooltip" title="Approve (Admin)"
                                                data-id="{{ $request->id }}"
                                                data-employee="{{ $request->user_name }}"
                                                data-approval-type="admin">
                                                <i class="fas fa-check-double"></i>
                                            </button>
                                            @endif

                                            @if($request->can_decline)
                                            <button type="button" class="btn btn-danger btn-sm decline-btn"
                                                data-bs-toggle="tooltip" title="Decline Request"
                                                data-id="{{ $request->id }}"
                                                data-employee="{{ $request->user_name }}"
                                                data-bs-target="#declineModal"
                                                data-bs-toggle="modal">
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
        <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-check-circle text-success me-2"></i> Approved Time Off Requests
                    </h5>
                    <span class="badge bg-light text-dark">
                        Showing {{ $approvedRequests->count() }} of {{ $approvedCount }} requests
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="approvedTable" class="table table-hover w-100">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Time Off Type</th>
                                    <th>Date Range</th>
                                    <th>Duration</th>
                                    <th>Approved By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($approvedRequests as $request)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-2">
                                                <span class="avatar-initial p-2 rounded-circle bg-success text-white">
                                                    {{ substr($request->user_name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>{{ $request->user_name }}</div>
                                        </div>
                                    </td>
                                    <td>{{ $request->department }}</td>
                                    <td>{{ $request->position }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $request->time_off_name }}
                                        </span>
                                    </td>
                                    <td>{{ $request->formatted_date }}</td>
                                    <td>{{ $request->duration }}</td>
                                    <td>
                                        <div class="d-flex flex-column gap-2">
                                            <span class="badge bg-light text-dark">
                                                <i class="fas fa-user-tie me-1"></i>
                                                Dept: {{ $request->dept_approver_name ?? 'N/A' }}
                                            </span>
                                            <span class="badge bg-light text-dark">
                                                <i class="fas fa-user-shield me-1"></i>
                                                Admin: {{ $request->admin_approver_name ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        @if(isset($request->file_reason_path) && !empty($request->file_reason_path))
                                        <button type="button" class="btn btn-info btn-sm view-file-btn"
                                            data-bs-toggle="tooltip" title="View File"
                                            data-file="{{ $request->file_reason_path }}">
                                            <i class="fas fa-file-alt"></i>
                                        </button>
                                        @endif
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
        <div class="tab-pane fade" id="declined" role="tabpanel" aria-labelledby="declined-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-times-circle text-danger me-2"></i> Declined Time Off Requests
                    </h5>
                    <span class="badge bg-light text-dark">
                        Showing {{ $declinedRequests->count() }} of {{ $declinedCount }} requests
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="declinedTable" class="table table-hover w-100">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Time Off Type</th>
                                    <th>Date Range</th>
                                    <th>Duration</th>
                                    <th>Declined By</th>
                                    <th>Reason</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($declinedRequests as $request)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-2">
                                                <span class="avatar-initial p-2 rounded-circle bg-danger text-white">
                                                    {{ substr($request->user_name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>{{ $request->user_name }}</div>
                                        </div>
                                    </td>
                                    <td>{{ $request->department }}</td>
                                    <td>{{ $request->position }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $request->time_off_name }}
                                        </span>
                                    </td>
                                    <td>{{ $request->formatted_date }}</td>
                                    <td>{{ $request->duration }}</td>
                                    <td>{{ $request->declined_by ?? 'Unknown' }}</td>
                                    <td>
                                        <span class="text-danger">
                                            {{ Str::limit($request->declined_reason, 50) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(isset($request->file_reason_path) && !empty($request->file_reason_path))
                                        <button type="button" class="btn btn-info btn-sm view-file-btn"
                                            data-bs-toggle="tooltip" title="View File"
                                            data-file="{{ $request->file_reason_path }}">
                                            <i class="fas fa-file-alt"></i>
                                        </button>
                                        @endif
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


    <!-- Decline Modal -->
    <div class="modal fade" id="declineModal" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="declineModalLabel">
                        <i class="fas fa-times-circle me-2"></i> Decline Time Off Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="declineForm">
                        <div class="mb-3">
                            <label for="declined_reason" class="form-label">Reason for Declining <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="declined_reason" name="declined_reason" rows="4" required></textarea>
                            <div class="invalid-feedback">Please provide a reason for declining.</div>
                        </div>
                        <input type="hidden" id="decline_request_id">
                        <input type="hidden" id="decline_employee_name">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="submitDeclineBtn" class="btn btn-danger">
                        <i class="fas fa-paper-plane me-1"></i> Submit
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Proof File Modal -->
    <div class="modal fade" id="proofFileModal" tabindex="-1" aria-labelledby="proofFileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="proofFileModalLabel">
                        <i class="fas fa-file-alt me-2"></i> Proof Document
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <div class="ratio ratio-16x9">
                        <iframe id="proofFileIframe" src="" frameborder="0" class="w-100 h-100"></iframe>
                        <img id="proofFileImage" src="" alt="Proof Document" class="img-fluid w-100 h-100 object-fit-contain d-none">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a id="downloadProofBtn" href="#" class="btn btn-primary" download>
                        <i class="fas fa-download me-1"></i> Download
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
    /* Modern UI Improvements - Scoped to time-off page */
    .time-off-container {
        /* Color Variables */
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --success-color: #4cc9f0;
        --warning-color: #f8961e;
        --danger-color: #f72585;
        --light-bg: #f8f9fa;
        --dark-bg: #212529;
        --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    /* Enhanced Tab Styling */
    #timeOffTabs {
        border-bottom: none;
        gap: 8px;
        padding: 0 8px;
    }

    #timeOffTabs .nav-link {
        color: #495057;
        font-weight: 600;
        padding: 12px 20px;
        transition: var(--transition);
        border: none;
        background-color: #f1f3f5;
        border-radius: 8px;
        margin-right: 0;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    #timeOffTabs .nav-link:hover {
        background-color: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    #timeOffTabs .nav-link.active {
        color: white;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
    }

    #timeOffTabs .nav-link.active .badge {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
    }

    #timeOffTabs .nav-link i {
        margin-right: 8px;
        font-size: 1.1rem;
    }

    /* Card Styling */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.25rem 1.5rem;
        background-color: white;
    }

    .card-header h5 {
        font-weight: 700;
        display: flex;
        align-items: center;
    }

    .card-header h5 i {
        margin-right: 10px;
    }

    /* Button Styling */
    .btn {
        border-radius: 8px;
        font-weight: 600;
        padding: 0.5rem 1.25rem;
        transition: var(--transition);
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .btn-sm {
        padding: 0.35rem 0.75rem;
        font-size: 0.85rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    }

    .btn-success {
        background: linear-gradient(135deg, #4cc9f0, #4895ef);
    }

    .btn-warning {
        background: linear-gradient(135deg, #f8961e, #f3722c);
    }

    .btn-danger {
        background: linear-gradient(135deg, #f72585, #b5179e);
    }

    .btn-info {
        background: linear-gradient(135deg, #3a86ff, #4361ee);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #6c757d, #495057);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Table Styling */
    .table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        font-size: 0.95rem;
    }

    .table th {
        font-weight: 700;
        background-color: #f8f9fa;
        color: #495057;
        padding: 1rem;
        border-bottom: 2px solid #e9ecef;
        position: sticky;
        top: 0;
    }

    .table td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(67, 97, 238, 0.03);
    }

    /* Badge Styling */
    .badge {
        font-size: 85%;
        padding: 0.4em 0.75em;
        font-weight: 600;
        border-radius: 8px;
        letter-spacing: 0.5px;
    }

    .bg-primary {
        background-color: var(--primary-color) !important;
    }

    .bg-success {
        background-color: var(--success-color) !important;
    }

    .bg-warning {
        background-color: var(--warning-color) !important;
        color: #212529 !important;
    }

    .bg-danger {
        background-color: var(--danger-color) !important;
    }

    /* Status indicators */
    .status-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
        vertical-align: middle;
    }

    .status-pending {
        background-color: var(--warning-color);
    }

    .status-approved {
        background-color: var(--success-color);
    }

    .status-declined {
        background-color: var(--danger-color);
    }

    /* Filter Card */
    .filter-card {
        border: none;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        background-color: white;
    }

    .filter-card .card-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border-bottom: none;
    }

    /* Form Elements */
    .form-select,
    .form-control {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        transition: var(--transition);
    }

    .form-select:focus,
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
    }

    /* Modal Styling */
    .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }

    .modal-header {
        border-bottom: none;
        padding: 1.5rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        border-top: none;
        padding: 1.25rem 1.5rem;
    }

    /* Responsive Improvements */
    @media (max-width: 992px) {
        #timeOffTabs .nav-link {
            padding: 0.75rem 0.5rem;
            font-size: 0.9rem;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .btn-group .btn {
            margin-bottom: 0;
            width: 100%;
        }
    }

    /* Animation for tab switching */
    .tab-pane.fade.show.active {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Custom scrollbar for tables */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTables with modern configuration
        const dataTableConfig = {
            responsive: true,
            processing: true,
            lengthMenu: [10, 25, 50, 100],
            dom: '<"top"<"row align-items-center"<"col-md-6"l><"col-md-6"f>>>rt<"bottom"<"row align-items-center"<"col-md-6"i><"col-md-6"p>><"clear">>',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    first: '<i class="fas fa-angle-double-left"></i>',
                    last: '<i class="fas fa-angle-double-right"></i>',
                    next: '<i class="fas fa-angle-right"></i>',
                    previous: '<i class="fas fa-angle-left"></i>'
                }
            },
            initComplete: function() {},
            drawCallback: function() {
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        };

        // Initialize the tables with the shared config
        $('#pendingTable').DataTable(dataTableConfig);
        $('#approvedTable').DataTable(dataTableConfig);
        $('#declinedTable').DataTable(dataTableConfig);

        // Add tooltips to action buttons
        $('[data-bs-toggle="tooltip"]').tooltip();

        // "View File" button handler with support for PDF and images
        $(document).on('click', '.view-file-btn', function() {
            const filePath = $(this).data('file');
            const fileUrl = `{{ asset('storage/') }}/${filePath}`;
            const fileExtension = filePath.split('.').pop().toLowerCase();

            // Set the download link
            $('#downloadProofBtn').attr('href', fileUrl);

            // Hide both iframe and image initially
            $('#proofFileIframe').addClass('d-none');
            $('#proofFileImage').addClass('d-none');

            if (fileExtension === 'pdf') {
                // Show PDF in iframe
                $('#proofFileIframe').attr('src', fileUrl).removeClass('d-none');
            } else {
                // Show image
                $('#proofFileImage').attr('src', fileUrl).removeClass('d-none');
            }

            // Show the modal
            $('#proofFileModal').modal('show');
        });

        // "Approve" button handler with enhanced UI
        $(document).on('click', '.approve-btn', function() {
            const id = $(this).data('id');
            const employee = $(this).data('employee');
            const approvalType = $(this).data('approval-type') || 'auto';
            const approvalTypeText = approvalType === 'dept' ? 'Department' :
                (approvalType === 'admin' ? 'Admin' : 'Department and Admin');

            Swal.fire({
                title: `Approve Time Off Request`,
                html: `<div class="text-start">
                         <p>Are you sure you want to approve <strong>${employee}'s</strong> time off request?</p>
                         <div class="alert alert-info mt-3">
                             <i class="fas fa-info-circle me-2"></i>
                             <strong>Approving as:</strong> ${approvalTypeText}
                         </div>
                       </div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check me-1"></i> Yes, approve it!',
                cancelButtonText: '<i class="fas fa-times me-1"></i> Cancel',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state with animation
                    Swal.fire({
                        title: 'Processing Approval',
                        html: `<div class="text-center py-4">
                                 <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                   <span class="visually-hidden">Loading...</span>
                                 </div>
                                 <p class="mt-2">Approving request for ${employee}...</p>
                               </div>`,
                        allowOutsideClick: false,
                        showConfirmButton: false
                    });

                    // Send AJAX request
                    $.ajax({
                        url: `{{ url('time_management/time_off/request_time_off/approve') }}/${id}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            approval_type: approvalType
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                html: `<div class="text-center text-success">
                                         <i class="fas fa-check-circle fa-4x mb-3"></i>
                                         <p>Time off request for <strong>${employee}</strong> has been approved successfully.</p>
                                       </div>`,
                                confirmButtonColor: '#28a745',
                                confirmButtonText: '<i class="fas fa-sync me-1"></i> Refresh Page',
                                allowOutsideClick: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            let errorMessage = 'There was an error approving the request.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                title: 'Error!',
                                html: `<div class="text-center text-danger">
                                         <i class="fas fa-exclamation-circle fa-4x mb-3"></i>
                                         <p>${errorMessage}</p>
                                       </div>`,
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });

        // Set up decline button action
        $(document).on('click', '.decline-btn', function() {
            const id = $(this).data('id');
            const employee = $(this).data('employee');

            $('#decline_request_id').val(id);
            $('#decline_employee_name').val(employee);
            $('#declined_reason').val(''); // Clear previous values

            // Update modal title with employee name
            $('#declineModalLabel').html(`<i class="fas fa-times-circle me-2"></i> Decline ${employee}'s Request`);

            // Show the modal with animation
            $('#declineModal').modal('show');
        });

        // Handle decline submission
        $('#submitDeclineBtn').click(function() {
            const id = $('#decline_request_id').val();
            const employee = $('#decline_employee_name').val();
            const reason = $('#declined_reason').val();

            // Validate reason
            if (!reason.trim()) {
                $('#declined_reason').addClass('is-invalid');
                return;
            }

            // Remove validation error if present
            $('#declined_reason').removeClass('is-invalid');

            // Hide the modal
            $('#declineModal').modal('hide');

            // Show SweetAlert2 confirmation
            Swal.fire({
                title: 'Confirm Decline',
                html: `Are you sure you want to decline <strong>${employee}'s</strong> time off request?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, decline it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Processing',
                        html: `<div class="d-flex justify-content-center">
                     <div class="spinner-border text-primary" role="status">
                       <span class="visually-hidden">Loading...</span>
                     </div>
                   </div>
                   <p class="mt-3">Declining request...</p>`,
                        allowOutsideClick: false,
                        showConfirmButton: false
                    });

                    // Send AJAX request
                    $.ajax({
                        url: `{{ url('time_management/time_off/request_time_off/decline') }}/${id}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            declined_reason: reason
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Declined!',
                                text: 'Time off request has been declined successfully.',
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            let errorMessage = 'There was an error declining the request.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                }
            });
        });

        // Make sure tab navigation works properly
        $('a[data-bs-toggle="tab"]').on('click', function(e) {
            e.preventDefault();
            $(this).tab('show');
        });

        // Hide any flash messages after 5 seconds
        setTimeout(function() {
            $('.alert-dismissible').fadeOut('slow');
        }, 5000);
    });
</script>
@endpush