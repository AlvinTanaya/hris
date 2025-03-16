@extends('layouts.app')

@section('content')
<style>
    #timeOffTabs .nav-link {
        color: #6c757d;
        font-weight: 500;
        padding: 1rem;
        transition: all 0.3s ease;
    }

    #timeOffTabs .nav-link.active {
        color: #0d6efd;
        border-bottom: 3px solid #ffc107;
    }

    /* DataTables styling */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_processing,
    .dataTables_wrapper .dataTables_paginate {
        margin-bottom: 10px;
        color: #333;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #0d6efd;
        color: white !important;
        border: none;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #e9ecef;
        color: #333 !important;
        border: none;
    }

    /* Badge improvements */
    .badge {
        font-size: 90%;
        padding: 0.4em 0.6em;
        border-radius: 4px;
    }

    .badge-info {
        background-color: #17a2b8;
        color: white;
    }

    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .badge-success {
        background-color: #28a745;
        color: white;
    }

    .badge-danger {
        background-color: #dc3545;
        color: white;
    }

    /* Calendar icon for date inputs */
    .date-picker-icon {
        position: relative;
    }

    .date-picker-icon i {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        pointer-events: none;
    }

    /* Improved card styling */
    .card {
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    /* Tab styling */
    #timeOffTabs {
        border-bottom: 1px solid #dee2e6;
        background-color: #f8f9fa;
        border-radius: 8px 8px 0 0;
    }

    #timeOffTabs .nav-link {
        border-radius: 8px 8px 0 0;
    }

    /* Table styling */
    .table th {
        background-color: #212529;
        color: white;
        font-weight: 500;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
</style>

<div class="container-fluid mt-4">
    <h1 class="text-center mb-4">
        <i class="fas fa-clock text-primary"></i> <span class="text-primary">Time Off Management</span>
    </h1>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i> Filter Options</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('request.time.off.index') }}" method="GET" id="filterForm">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="employee" class="form-label"><i class="fas fa-user me-1"></i> Employee</label>
                        <select class="form-select" name="employee" id="employee">
                            <option value="">All Employees</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('employee') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="position_request" class="form-label"><i class="fas fa-id-badge me-1"></i> Position</label>
                        <select class="form-select" name="position_request" id="position_request">
                            <option value="">All Positions</option>
                            @foreach($positions as $position)
                            <option value="{{ $position }}" {{ request('position_request') == $position ? 'selected' : '' }}>{{ $position }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="department_request" class="form-label"><i class="fas fa-building me-1"></i> Department</label>
                        <select class="form-select" name="department_request" id="department_request">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                            <option value="{{ $department }}" {{ request('department_request') == $department ? 'selected' : '' }}>{{ $department }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="time_off_type" class="form-label"><i class="fas fa-calendar-alt me-1"></i> Time Off Type</label>
                        <select class="form-select" name="time_off_type" id="time_off_type">
                            <option value="">All Types</option>
                            @foreach($timeOffPolicies as $policy)
                            <option value="{{ $policy->id }}" {{ request('time_off_type') == $policy->id ? 'selected' : '' }}>
                                {{ $policy->time_off_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="date" class="form-label"><i class="fas fa-calendar me-1"></i> Date</label>
                        <div class="date-picker-icon">
                            <input type="date" class="form-control" name="date" id="date" value="{{ request('date') }}">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i> Apply Filters
                        </button>
                        <a href="{{ route('request.time.off.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-1"></i> Reset Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="container-fluid p-0">
        <ul class="nav nav-tabs w-100" id="timeOffTabs" role="tablist">
            <li class="nav-item flex-grow-1 text-center" role="presentation">
                <a class="nav-link active" id="pending-tab" data-bs-toggle="tab" href="#pending" role="tab" aria-controls="pending" aria-selected="true">
                    <i class="fas fa-hourglass-half text-warning me-1"></i>
                    <span class="badge badge-warning">{{ $pendingCount }}</span> Pending Requests
                </a>
            </li>
            <li class="nav-item flex-grow-1 text-center" role="presentation">
                <a class="nav-link" id="approved-tab" data-bs-toggle="tab" href="#approved" role="tab" aria-controls="approved" aria-selected="false">
                    <i class="fas fa-check-circle text-success me-1"></i>
                    <span class="badge badge-success">{{ $approvedCount }}</span> Approved Requests
                </a>
            </li>
            <li class="nav-item flex-grow-1 text-center" role="presentation">
                <a class="nav-link" id="declined-tab" data-bs-toggle="tab" href="#declined" role="tab" aria-controls="declined" aria-selected="false">
                    <i class="fas fa-times-circle text-danger me-1"></i>
                    <span class="badge badge-danger">{{ $declinedCount }}</span> Declined Requests
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content p-0 mt-3" id="timeOffTabsContent">
            <!-- Pending Requests Tab -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <div class="card shadow-sm w-100">
                    <div class="card-header bg-warning text-dark d-flex align-items-center">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i> Pending Time Off Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="pendingTable" class="table table-bordered table-hover w-100">
                                <thead>
                                    <tr>
                                        <th width="5%">No.</th>
                                        <th width="15%">Employee</th>
                                        <th width="15%">Date</th>
                                        <th width="8%">Days</th>
                                        <th width="12%">Time Off Type</th>
                                        <th width="25%">Reason</th>
                                        <th width="20%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingRequests as $key => $request)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $request->user_name }}</td>
                                        <td>

                                            @if($request->start_date == $request->end_date)
                                            {{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }}
                                            @else
                                            {{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }} -
                                            {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($request->start_date)->diffInDays(\Carbon\Carbon::parse($request->end_date)) + 1 }} days
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $request->time_off_name }}</span>
                                        </td>
                                        <td>{{ $request->reason }}</td>
                                        <td>
                                            <div class="btn-group d-flex">
                                                <button type="button" class="btn btn-sm btn-success approve-btn flex-fill"
                                                    data-id="{{ $request->id }}"
                                                    data-employee="{{ $request->user_name }}">
                                                    <i class="fas fa-check me-1"></i> Approve
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger decline-btn flex-fill"
                                                    data-id="{{ $request->id }}"
                                                    data-employee="{{ $request->user_name }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#declineModal">
                                                    <i class="fas fa-times me-1"></i> Decline
                                                </button>
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
                <div class="card shadow-sm w-100">
                    <div class="card-header bg-success text-white d-flex align-items-center">
                        <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i> Approved Time Off Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="approvedTable" class="table table-bordered table-hover w-100">
                                <thead>
                                    <tr>
                                        <th width="5%">No.</th>
                                        <th width="15%">Employee</th>
                                        <th width="15%">Date</th>
                                        <th width="8%">Days</th>
                                        <th width="12%">Time Off Type</th>
                                        <th width="20%">Reason</th>
                                        <th width="12%">Approved By</th>
                                        <th width="13%">Approved On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($approvedRequests as $key => $request)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $request->user_name }}</td>
                                        <td>
                                            @if($request->start_date == $request->end_date)
                                            {{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }}
                                            @else
                                            {{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }} -
                                            {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($request->start_date)->diffInDays(\Carbon\Carbon::parse($request->end_date)) + 1 }} days
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $request->time_off_name }}</span>
                                        </td>
                                        <td>{{ $request->reason }}</td>
                                        <td>{{ $request->answered_by_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($request->approved_at)->format('d M Y, H:i') }}</td>
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
                <div class="card shadow-sm w-100">
                    <div class="card-header bg-danger text-white d-flex align-items-center">
                        <h5 class="mb-0"><i class="fas fa-calendar-times me-2"></i> Declined Time Off Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="declinedTable" class="table table-bordered table-hover w-100">
                                <thead>
                                    <tr>
                                        <th width="5%">No.</th>
                                        <th width="15%">Employee</th>
                                        <th width="15%">Date</th>
                                        <th width="8%">Days</th>
                                        <th width="12%">Time Off Type</th>
                                        <th width="15%">Reason</th>
                                        <th width="20%">Declined Reason</th>
                                        <th width="10%">Declined By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($declinedRequests as $key => $request)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $request->user_name }}</td>
                                        <td>
                                            @if($request->start_date == $request->end_date)
                                            {{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }}
                                            @else
                                            {{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }} -
                                            {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($request->start_date)->diffInDays(\Carbon\Carbon::parse($request->end_date)) + 1 }} days
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $request->time_off_name }}</span>
                                        </td>
                                        <td>{{ $request->reason }}</td>
                                        <td>{{ $request->declined_reason }}</td>
                                        <td>{{ $request->answered_by_name }}</td>
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
</div>

<!-- Decline Modal -->
<div class="modal fade" id="declineModal" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="declineModalLabel"><i class="fas fa-times-circle me-2"></i> Decline Time Off Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="declined_reason" class="form-label">Reason for Declining</label>
                    <textarea class="form-control" id="declined_reason" name="declined_reason" rows="3" required></textarea>
                    <input type="hidden" id="decline_request_id">
                    <input type="hidden" id="decline_employee_name">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="submitDeclineBtn" class="btn btn-danger">Submit Reason</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTables with improved settings
        $('#pendingTable').DataTable({
            responsive: true,
            ordering: true,
            info: true,
            paging: true,
            lengthChange: true,
            language: {
                search: "<i class='fas fa-search'></i> Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                paginate: {
                    first: "<i class='fas fa-angle-double-left'></i>",
                    last: "<i class='fas fa-angle-double-right'></i>",
                    next: "<i class='fas fa-angle-right'></i>",
                    previous: "<i class='fas fa-angle-left'></i>"
                }
            }
        });

        $('#approvedTable').DataTable({
            responsive: true,
            ordering: true,
            info: true,
            paging: true,
            lengthChange: true,
            language: {
                search: "<i class='fas fa-search'></i> Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                paginate: {
                    first: "<i class='fas fa-angle-double-left'></i>",
                    last: "<i class='fas fa-angle-double-right'></i>",
                    next: "<i class='fas fa-angle-right'></i>",
                    previous: "<i class='fas fa-angle-left'></i>"
                }
            }
        });

        $('#declinedTable').DataTable({
            responsive: true,
            ordering: true,
            info: true,
            paging: true,
            lengthChange: true,
            language: {
                search: "<i class='fas fa-search'></i> Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                paginate: {
                    first: "<i class='fas fa-angle-double-left'></i>",
                    last: "<i class='fas fa-angle-double-right'></i>",
                    next: "<i class='fas fa-angle-right'></i>",
                    previous: "<i class='fas fa-angle-left'></i>"
                }
            }
        });

        // Fix tab functionality
        $('#timeOffTabs a').on('click', function(e) {
            e.preventDefault();
            $(this).tab('show');
        });

        // Approve button with SweetAlert2
        $('.approve-btn').click(function() {
            const id = $(this).data('id');
            const employee = $(this).data('employee');

            Swal.fire({
                title: 'Approve Time Off Request',
                text: `Are you sure you want to approve ${employee}'s time off request?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, approve it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Processing',
                        text: 'Approving request...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Send AJAX request
                    $.ajax({
                        url: `{{ url('time_management/time_off/request_time_off/approve') }}/${id}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Approved!',
                                text: 'Time off request has been approved successfully.',
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'There was an error approving the request.',
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                }
            });
        });

        // Set up decline button action
        $('.decline-btn').click(function() {
            const id = $(this).data('id');
            const employee = $(this).data('employee');
            $('#decline_request_id').val(id);
            $('#decline_employee_name').val(employee);
            $('#declined_reason').val(''); // Clear previous values
        });

        // Handle decline submission with SweetAlert2 after modal
        $('#submitDeclineBtn').click(function() {
            const id = $('#decline_request_id').val();
            const employee = $('#decline_employee_name').val();
            const reason = $('#declined_reason').val();

            if (!reason.trim()) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please provide a reason for declining.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }

            // Close the modal
            $('#declineModal').modal('hide');

            // Show SweetAlert2 confirmation
            Swal.fire({
                title: 'Confirm Decline',
                text: `Are you sure you want to decline ${employee}'s time off request?`,
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
                        text: 'Declining request...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Send AJAX request
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
                            Swal.fire({
                                title: 'Error!',
                                text: 'There was an error declining the request.',
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                }
            });
        });

        // Select2 initialization for better dropdown experience
        if ($.fn.select2) {
            $('#employee, #position_request, #department_request, #time_off_type').select2({
                placeholder: "Select an option",
                allowClear: true,
                width: '100%'
            });
        }

        // Auto-submit filter on date change
        $('#date').change(function() {
            if ($(this).val()) {
                $('#filterForm').submit();
            }
        });

        // Add tooltip to better explain the date filter
        if ($.fn.tooltip) {
            $('.date-picker-icon').tooltip({
                title: "Filter requests that include this date",
                placement: "top"
            });
        }
    });
</script>
@endpush