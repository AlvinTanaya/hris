@extends('layouts.app')

@section('content')
<style>
    #requestsTable_wrapper {
        width: 100%;
        margin: 0;
        padding: 0;
    }

    .dataTables_wrapper .dataTables_length, 
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 15px;
    }

    .dataTables_wrapper .no-footer {
        width: 100% !important;
        margin: 0 !important;
    }

    .dataTable {
        width: 100% !important;
    }
    
    .badge {
        font-size: 0.85rem;
    }
    
    .filter-card {
        background-color: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .btn-icon {
        padding: 0.25rem 0.5rem;
    }
    
    .status-pending {
        background-color: #ffc107;
    }
    
    .status-approved {
        background-color: #28a745;
    }
    
    .status-declined {
        background-color: #dc3545;
    }
</style>

<div class="container-fluid px-4">
    <h1 class="text-center text-warning" style="margin-bottom: 35px; margin-top:25px">
        <i class="fas fa-user-clock"></i> Shift Change Request Management
    </h1>

    <!-- Filter Card -->
    <div class="card filter-card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i> Filter Options
        </div>
        <div class="card-body">
            <form id="filterForm" method="GET" action="{{ route('change_shift.index') }}">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select form-select-sm" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Declined" {{ request('status') == 'Declined' ? 'selected' : '' }}>Declined</option>
                        </select>
                    </div>
                    
                    @if($isSuperAdmin || $isManager)
                    <div class="col-md-3">
                        <label for="department_id" class="form-label">Department</label>
                        <select class="form-select form-select-sm" id="department_id" name="department_id">
                            <option value="">All Departments</option>
                            @foreach(\App\Models\EmployeeDepartment::all() as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->department }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="user_id" class="form-label">Employee</label>
                        <select class="form-select form-select-sm" id="user_id" name="user_id">
                            <option value="">All Employees</option>
                            @foreach(\App\Models\User::where('user_status', 'Active')->orderBy('name')->get() as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ $user->employee_id }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control form-control-sm" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control form-control-sm" id="date_to" name="date_to"
                               value="{{ request('date_to') }}">
                    </div>
                    
                    <div class="col-md-12 text-end mt-4">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search me-1"></i> Apply Filter
                        </button>
                        <a href="{{ route('change_shift.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-redo me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i> Shift Change Requests
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="requestsTable" class="table table-striped table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="15%">Employee</th>
                            <th width="10%">Department</th>
                            <th width="15%">Date Range</th>
                            <th width="10%">Current Shift</th>
                            <th width="10%">Requested Shift</th>
                            <th width="10%">Status</th>
                            <th width="10%">Approval Level</th>
                            <th width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shiftChanges as $request)
                        <tr>
                            <td>{{ $request->id }}</td>
                            <td>
                                <strong>{{ $request->user->name }}</strong><br>
                                <small class="text-muted">{{ $request->user->employee_id }}</small>
                            </td>
                            <td>{{ $request->user->department_name }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($request->date_change_start)->format('d M Y') }} 
                                <span class="text-muted">to</span> 
                                {{ \Carbon\Carbon::parse($request->date_change_end)->format('d M Y') }}
                            </td>
                            <td>{{ $request->ruleShiftBefore->name ?? 'N/A' }}</td>
                            <td>{{ $request->ruleShiftAfter->name ?? 'N/A' }}</td>
                            <td>
                                @if($request->status_change == 'Pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($request->status_change == 'Approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($request->status_change == 'Declined')
                                    <span class="badge bg-danger">Declined</span>
                                    @if($request->declined_reason)
                                        <button type="button" class="btn btn-link p-0 btn-sm view-reason" 
                                                data-reason="{{ $request->declined_reason }}">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <div class="mb-1">
                                        <span class="badge {{ $request->dept_approval_status == 'Approved' ? 'bg-success' : ($request->dept_approval_status == 'Declined' ? 'bg-danger' : 'bg-secondary') }}">
                                            Department: {{ $request->dept_approval_status }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="badge {{ $request->admin_approval_status == 'Approved' ? 'bg-success' : ($request->admin_approval_status == 'Declined' ? 'bg-danger' : 'bg-secondary') }}">
                                            Admin: {{ $request->admin_approval_status }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($request->status_change == 'Pending')
                                    @if($request->can_approve_dept)
                                        <button class="btn btn-success btn-sm approve-btn" 
                                                data-id="{{ $request->id }}" 
                                                data-level="dept">
                                            <i class="fas fa-check me-1"></i> Approve
                                        </button>
                                    @endif
                                    
                                    @if($request->can_approve_admin)
                                        <button class="btn btn-success btn-sm approve-btn" 
                                                data-id="{{ $request->id }}" 
                                                data-level="admin">
                                            <i class="fas fa-check me-1"></i> Approve
                                        </button>
                                    @endif
                                    
                                    @if($request->can_approve_dept || $request->can_approve_admin)
                                        <button class="btn btn-danger btn-sm decline-btn" 
                                                data-id="{{ $request->id }}">
                                            <i class="fas fa-times me-1"></i> Decline
                                        </button>
                                    @endif
                                @else
                                    <button class="btn btn-info btn-sm view-details-btn" 
                                            data-id="{{ $request->id }}">
                                        <i class="fas fa-eye me-1"></i> View
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

<!-- Decline Modal -->
<div class="modal fade" id="declineModal" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="declineModalLabel">
                    <i class="fas fa-times-circle me-2"></i> Decline Shift Change Request
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="declineForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="decline_reason" class="form-label">Please provide a reason for declining:</label>
                        <textarea class="form-control" id="decline_reason" name="decline_reason" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i> Confirm Decline
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewDetailsModalLabel">
                    <i class="fas fa-info-circle me-2"></i> Shift Change Request Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailsContent">
                <!-- Content will be loaded dynamically -->
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Reason Modal -->
<div class="modal fade" id="reasonModal" tabindex="-1" aria-labelledby="reasonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="reasonModalLabel">
                    <i class="fas fa-info-circle me-2"></i> Decline Reason
                </h5>
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
@endsection

@push('scripts')
<!-- Bootstrap 5 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables CSS & JS -->
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<!-- SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#requestsTable').DataTable({
            responsive: true,
            order: [[0, 'desc']], // Sort by ID descending
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search requests..."
            },
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
        });

        // Enhanced Select2 for dropdowns (optional - requires adding Select2 library)
        if ($.fn.select2) {
            $('#department_id, #user_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select option',
                allowClear: true
            });
        }

        // Handle approve button click
        $('.approve-btn').on('click', function() {
            const requestId = $(this).data('id');
            const level = $(this).data('level');
            const levelText = level === 'dept' ? 'Department' : 'Admin';
            
            Swal.fire({
                title: 'Confirm Approval',
                text: `Are you sure you want to approve this shift change request as ${levelText}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('/time_management/request_shift/approve') }}/${requestId}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Approved!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonColor: '#28a745'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'Something went wrong.',
                                    icon: 'error',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'There was an error processing your request.',
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                }
            });
        });

        // Handle decline button click
        $('.decline-btn').on('click', function() {
            const requestId = $(this).data('id');
            $('#declineForm').attr('action', `{{ url('/time_management/request_shift/decline') }}/${requestId}`);
            $('#declineModal').modal('show');
        });

        // Handle decline form submission
        $('#declineForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr('action');
            
            $.ajax({
                url: url,
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $('#declineModal').modal('hide');
                    
                    if (response.success) {
                        Swal.fire({
                            title: 'Declined!',
                            text: 'The shift change request has been declined.',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Something went wrong.',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function(xhr) {
                    $('#declineModal').modal('hide');
                    Swal.fire({
                        title: 'Error!',
                        text: 'There was an error processing your request.',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        });

        // Handle view reason click
        $('.view-reason').on('click', function() {
            const reason = $(this).data('reason');
            $('#reasonText').text(reason);
            $('#reasonModal').modal('show');
        });

        // Handle view details click
        $('.view-details-btn').on('click', function() {
            const requestId = $(this).data('id');
            $('#viewDetailsModal').modal('show');
            
            // You would implement an AJAX call here to get the details
            // For demo purposes, we'll just show a placeholder
            setTimeout(() => {
                $('#detailsContent').html(`
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Request Information</h6>
                            <p><strong>Request ID:</strong> ${requestId}</p>
                            <p><strong>Status:</strong> <span class="badge bg-success">Complete</span></p>
                            <p><strong>Created Date:</strong> [Created Date]</p>
                            <p><strong>Processed Date:</strong> [Processed Date]</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">Employee Information</h6>
                            <p><strong>Name:</strong> [Employee Name]</p>
                            <p><strong>ID:</strong> [Employee ID]</p>
                            <p><strong>Department:</strong> [Department]</p>
                            <p><strong>Position:</strong> [Position]</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="fw-bold">Shift Change Details</h6>
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Period</th>
                                        <th>Original Shift</th>
                                        <th>New Shift</th>
                                        <th>Working Hours</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>[Date Range]</td>
                                        <td>[Original Shift]</td>
                                        <td>[New Shift]</td>
                                        <td>[Working Hours]</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h6 class="fw-bold">Reason for Change</h6>
                            <p class="border p-2 rounded bg-light">[Reason Text]</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Department Approval</h6>
                            <p><strong>Status:</strong> [Approval Status]</p>
                            <p><strong>Approved By:</strong> [Approver Name]</p>
                            <p><strong>Date:</strong> [Approval Date]</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">Admin Approval</h6>
                            <p><strong>Status:</strong> [Approval Status]</p>
                            <p><strong>Approved By:</strong> [Approver Name]</p>
                            <p><strong>Date:</strong> [Approval Date]</p>
                        </div>
                    </div>
                `);
            }, 500);
        });
    });
</script>
@endpush