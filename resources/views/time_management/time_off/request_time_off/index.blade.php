@extends('layouts.app')

@section('content')
<style>
    /* Improved tab styling */
    #timeOffTabs .nav-link {
        color: #6c757d;
        font-weight: 500;
        padding: 1rem 1.5rem;
        transition: all 0.3s ease;
        border: none;
        background-color: #f8f9fa;
        border-radius: 8px 8px 0 0;
        margin-right: 3px;
    }

    #timeOffTabs .nav-link:hover {
        background-color: #e9ecef;
        color: #495057;
    }

    #timeOffTabs .nav-link.active {
        color: #fff;
        background-color: #0d6efd;
        border-bottom: 3px solid #ffc107;
    }

    /* Improved card styling */
    .card {
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .card-header {
        padding: 1rem 1.5rem;
    }

    /* Improved button styling */
    .btn {
        border-radius: 4px;
        font-weight: 500;
        padding: 0.375rem 1rem;
        transition: all 0.3s ease;
    }

    .btn-sm {
        padding: 0.25rem 0.75rem;
    }

    .btn-group .btn {
        margin-right: 2px;
    }

    /* Improved table styling */
    .table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }

    .table th {
        font-weight: 600;
        background-color: #343a40;
        color: white;
        padding: 0.75rem;
    }

    .table td {
        padding: 0.75rem;
        vertical-align: middle;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }

    /* Improved badge styling */
    .badge {
        font-size: 85%;
        padding: 0.4em 0.6em;
        font-weight: 600;
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

    /* Improved modal styling */
    .modal-content {
        border: none;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .modal-header {
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        padding: 1rem 1.5rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
        padding: 1rem 1.5rem;
    }

    /* Improve search box spacing */
    .dataTables_filter {
        margin-bottom: 15px;
    }

    /* If you want to specifically target the search input */
    .dataTables_filter input[type="search"] {
        margin-left: 10px;
        /* Add space after the search label */
        padding: 6px 10px;
        /* Increase padding inside the search box */
        border-radius: 4px;
        /* Round the corners of the search box */
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
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
        }

        .btn-group .btn {
            margin-bottom: 0.25rem;
            width: 100%;
        }
    }
</style>

<h1 class="text-center text-warning" style="margin-bottom: 60px; margin-top:25px">
    <i class="fas fa-clock"></i> Time Off Management
</h1>

<div class="container mt-4 mx-auto">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mt-2"><i class="fas fa-filter"></i> Filter</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('request.time.off.index') }}" method="GET" id="filterForm">
                <div class="row mb-2">
                    <div class="col-md-4 mb-3">
                        <label for="employee">Employee</label>
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
                        <label for="position">Position</label>
                        <select class="form-select" name="position_request">
                            <option value="">All Positions</option>
                            @foreach($positions as $position)
                            <option value="{{ $position }}" {{ request('position_request') == $position ? 'selected' : '' }}>{{ $position }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="department">Department</label>
                        <select class="form-select" name="department_request">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                            <option value="{{ $department }}" {{ request('department_request') == $department ? 'selected' : '' }}>{{ $department }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-4 mb-3">
                        <label for="time_off_type">Time Off Type</label>
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
                        <label for="date">Date</label>
                        <input type="date" class="form-control" name="date" id="date" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-2"></i> Apply Filters
                        </button>
                        <a href="{{ route('request.time.off.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-2"></i> Reset
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
                    <i class="fas fa-hourglass-half text-warning"></i>
                    <span class="badge badge-warning mr-1">{{ $pendingCount }}</span> Pending Requests
                </a>
            </li>
            <li class="nav-item flex-grow-1 text-center" role="presentation">
                <a class="nav-link" id="approved-tab" data-bs-toggle="tab" href="#approved" role="tab" aria-controls="approved" aria-selected="false">
                    <i class="fas fa-check-circle text-success"></i>
                    <span class="badge badge-success mr-1">{{ $approvedCount }}</span> Approved Requests
                </a>
            </li>
            <li class="nav-item flex-grow-1 text-center" role="presentation">
                <a class="nav-link" id="declined-tab" data-bs-toggle="tab" href="#declined" role="tab" aria-controls="declined" aria-selected="false">
                    <i class="fas fa-times-circle text-danger"></i>
                    <span class="badge badge-danger mr-1">{{ $declinedCount }}</span> Declined Requests
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content p-0 mt-3" id="timeOffTabsContent">
            <!-- Pending Requests Tab -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mt-2"><i class="fas fa-calendar-alt"></i> Pending Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="pendingTable" class="table table-bordered table-hover w-100">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="3%">No.</th>
                                        <th>Employee</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th width="12%">Start Date</th>
                                        <th width="12%">End Date</th>
                                        <th width="8%">Duration</th>
                                        <th width="12%">Type</th>
                                        <th width="22%">Reason</th>
                                        <th width="8%">Proof</th>
                                        <th width="8%">Actions</th>
                                    </tr>

                                </thead>
                                <tbody>
                                    @foreach($pendingRequests as $key => $request)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $request->user_name }}</td>
                                        <td>{{ $request->department ?? 'N/A' }}</td>
                                        <td>{{ $request->position ?? 'N/A' }}</td>
                                        <td>{{ $request->formatted_start_date }}</td>
                                        <td>{{ $request->formatted_end_date }}</td>
                                        <td>{{ $request->duration }}</td>
                                        <td><span class="badge bg-info">{{ $request->time_off_name }}</span></td>
                                        <td>{{ $request->reason }}</td>
                                        <td>
                                            @if($request->file_reason_path)
                                            <button class="btn btn-sm btn-info view-file-btn" data-file="{{ $request->file_reason_path }}">
                                                <i class="fas fa-file-image"></i> View
                                            </button>
                                            @else
                                            <span class="text-muted">No file</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group d-flex">
                                                <button class="btn btn-sm btn-success approve-btn flex-fill" data-id="{{ $request->id }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger decline-btn flex-fill" data-id="{{ $request->id }}">
                                                    <i class="fas fa-times"></i>
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
            <div class="tab-pane fade" id="approved" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mt-2"><i class="fas fa-calendar-check"></i> Approved Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="approvedTable" class="table table-bordered table-hover w-100">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="3%">No.</th>
                                        <th>Employee</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th width="12%">Start Date</th>
                                        <th width="12%">End Date</th>
                                        <th width="8%">Duration</th>
                                        <th width="12%">Type</th>
                                        <th width="22%">Reason</th>
                                        <th width="8%">Proof</th>
                                        <th width="8%">Approved By</th>
                                    </tr>

                                </thead>
                                <tbody>
                                    @foreach($approvedRequests as $key => $request)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $request->user_name }}</td>
                                        <td>{{ $request->department ?? 'N/A' }}</td>
                                        <td>{{ $request->position ?? 'N/A' }}</td>
                                        <td>{{ $request->formatted_start_date }}</td>
                                        <td>{{ $request->formatted_end_date }}</td>
                                        <td>{{ $request->duration }}</td>
                                        <td><span class="badge bg-info">{{ $request->time_off_name }}</span></td>
                                        <td>{{ $request->reason }}</td>
                                        <td>
                                            @if($request->file_reason_path)
                                            <button class="btn btn-sm btn-info view-file-btn" data-file="{{ $request->file_reason_path }}">
                                                <i class="fas fa-file-image"></i> View
                                            </button>
                                            @else
                                            <span class="text-muted">No file</span>
                                            @endif
                                        </td>
                                        <td>{{ $request->answered_by_name }}</td>
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
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mt-2"><i class="fas fa-calendar-times"></i> Declined Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="declinedTable" class="table table-bordered table-hover w-100">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="3%">No.</th>
                                        <th>Employee</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th width="12%">Start Date</th>
                                        <th width="12%">End Date</th>
                                        <th width="8%">Duration</th>
                                        <th width="12%">Type</th>
                                        <th width="15%">Reason</th>
                                        <th width="8%">Proof</th>
                                        <th width="12%">Decline Reason</th>
                                        <th width="8%">By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($declinedRequests as $key => $request)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $request->user_name }}</td>
                                        <td>{{ $request->department ?? 'N/A' }}</td>
                                        <td>{{ $request->position ?? 'N/A' }}</td>
                                        <td>{{ $request->formatted_start_date }}</td>
                                        <td>{{ $request->formatted_end_date }}</td>
                                        <td>{{ $request->duration }}</td>
                                        <td><span class="badge bg-info">{{ $request->time_off_name }}</span></td>
                                        <td>{{ $request->reason }}</td>
                                        <td>
                                            @if($request->file_reason_path)
                                            <button class="btn btn-sm btn-info view-file-btn" data-file="{{ $request->file_reason_path }}">
                                                <i class="fas fa-file-image"></i> View
                                            </button>
                                            @else
                                            <span class="text-muted">No file</span>
                                            @endif
                                        </td>
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
                <h5 class="modal-title" id="declineModalLabel">Decline Time Off Request</h5>
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

<!-- Proof File Modal -->
<div class="modal fade" id="proofFileModal" tabindex="-1" aria-labelledby="proofFileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="proofFileModalLabel">Proof Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="proofFileImage" src="" alt="Proof Document" class="img-fluid">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {



        // Initialize DataTables
        // Updated DataTables configuration
        $('#pendingTable, #approvedTable, #declinedTable').DataTable({
            responsive: true,
            ordering: true,
            info: true,
            paging: true,
            lengthChange: true,
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            initComplete: function() {
                // Add tooltips to buttons after DataTable initialization
                $('.view-file-btn').tooltip({
                    title: 'View attached file',
                    placement: 'top'
                });
                $('.approve-btn').tooltip({
                    title: 'Approve this request',
                    placement: 'top'
                });
                $('.decline-btn').tooltip({
                    title: 'Decline this request',
                    placement: 'top'
                });
            }
        });

        // Fix tab functionality
        $('#timeOffTabs a').on('click', function(e) {
            e.preventDefault();
            $(this).tab('show');
        });

        // This will handle the "View File" button clicks
        $('.view-file-btn').click(function() {
            const filePath = $(this).data('file');
            const fileUrl = "{{ asset('storage/') }}/" + filePath;

            // Set the image source
            $('#proofFileImage').attr('src', fileUrl);

            // Show the modal
            $('#proofFileModal').modal('show');
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

            console.log(reason);

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

        // Handle per page and search functionality
        $('#entriesPerPage').change(function() {
            let url = new URL(window.location.href);
            url.searchParams.set('per_page', $(this).val());
            window.location.href = url.toString();
        });

        let searchTimer;
        $('#searchInput').keyup(function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                let url = new URL(window.location.href);
                url.searchParams.set('search', $(this).val());
                window.location.href = url.toString();
            }, 500);
        });
    });
</script>
@endpush