@extends('layouts.app')

@section('content')
<style>
    #overtimeTabs .nav-link {
        color: white;
        font-weight: 500;
        padding: 1rem;
        transition: all 0.3s ease;
    }

    #overtimeTabs .nav-link.active {
        color: #0d6efd;
        border-bottom: 3px solid #0d6efd;
    }

    #overtimeTabs .nav-link.active span {
        color: #0d6efd;
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

    .badge {
        font-size: 90%;
        padding: 0.4em 0.6em;
    }

    /* Badge improvements */
    .badge-info {
        background-color: #17a2b8;
        color: white;
    }

    .badge-primary {
        background-color: #007bff;
        color: white;
    }
</style>

<h1 class="text-center text-warning" style="margin-bottom: 60px; margin-top:25px">
    <i class="fas fa-clock"></i> Overtime Management
</h1>
<div class="container mt-4 mx-auto">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mt-2"><i class="fas fa-filter"></i> Filter</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('overtime.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="employee">Employee</label>
                        <select name="employee" id="employee" class="form-select">
                            <option value="all" {{ $employee == 'all' ? 'selected' : '' }}>All Employees</option>
                            @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $employee == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="position">Position</label>
                        <select class="form-select" name="position_request">
                            <option value="">All Positions</option>
                            @foreach($positions as $position)
                            <option value="{{ $position }}" {{ request('position_request') == $position ? 'selected' : '' }}>
                                {{ $position }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="department">Department</label>
                        <select class="form-select" name="department_request">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                            <option value="{{ $department }}" {{ request('department_request') == $department ? 'selected' : '' }}>
                                {{ $department }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="overtime_type">Overtime Type</label>
                        <select name="overtime_type" id="overtime_type" class="form-select">
                            <option value="all" {{ $overtimeType == 'all' ? 'selected' : '' }}>All Types</option>
                            <option value="Paid_Overtime" {{ $overtimeType == 'Paid_Overtime' ? 'selected' : '' }}>Paid Overtime</option>
                            <!-- <option value="Overtime_Leave" {{ $overtimeType == 'Overtime_Leave' ? 'selected' : '' }}>Overtime Leave</option> -->
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="date">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ $date }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <a href="{{ route('overtime.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- Tabs Navigation - Improved Styling -->
    <div class="container-fluid p-0">
        <ul class="nav nav-tabs w-100" id="overtimeTabs" role="tablist">
            <li class="nav-item flex-grow-1 text-center" role="presentation">
                <a class="nav-link active" id="pending-tab" data-bs-toggle="tab" href="#pending" role="tab" aria-controls="pending" aria-selected="true">
                    <i class="fas fa-hourglass-half text-warning"></i>
                    <span class="badge badge-warning mr-1">{{ count($pendingRequests) }}</span> Pending Requests
                </a>
            </li>
            <li class="nav-item flex-grow-1 text-center" role="presentation">
                <a class="nav-link" id="approved-tab" data-bs-toggle="tab" href="#approved" role="tab" aria-controls="approved" aria-selected="false">
                    <i class="fas fa-check-circle text-success"></i>
                    <span class="badge badge-success mr-1">{{ count($approvedRequests) }}</span> Approved Requests
                </a>
            </li>
            <li class="nav-item flex-grow-1 text-center" role="presentation">
                <a class="nav-link" id="declined-tab" data-bs-toggle="tab" href="#declined" role="tab" aria-controls="declined" aria-selected="false">
                    <i class="fas fa-times-circle text-danger"></i>
                    <span class="badge badge-danger mr-1">{{ count($declinedRequests) }}</span> Declined Requests
                </a>
            </li>
        </ul>


        <!-- Tab Content -->
        <div class="tab-content p-0 mt-3" id="overtimeTabsContent">
            <!-- Pending Requests Tab -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <div class="card shadow-sm w-100">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mt-2"><i class="fas fa-calendar"></i> Pending Overtime Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="pendingTable" class="table table-bordered table-hover w-100">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">Employee</th>
                                        <th width="12%">Date</th>
                                        <th width="10%">Hours</th>
                                        <th width="13%">Overtime Type</th>
                                        <th width="25%">Reason</th>
                                        <th width="20%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($pendingRequests as $index => $overtime)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $overtime->employee_name }}</td>
                                        <td>{{ date('d M Y', strtotime($overtime->date)) }}</td>
                                        <td>{{ $overtime->total_hours }} hrs</td>
                                        <td>
                                            @if($overtime->overtime_type == 'Paid_Overtime')
                                            <span class="badge badge-info">Paid Overtime</span>
                                            @else
                                            <span class="badge badge-primary">Overtime Leave</span>
                                            @endif
                                        </td>
                                        <td>{{ $overtime->reason }}</td>
                                        <td>
                                            <div class="btn-group d-flex">
                                                <button type="button" class="btn btn-sm btn-success approve-btn flex-fill"
                                                    data-id="{{ $overtime->id }}"
                                                    data-employee="{{ $overtime->employee_name }}">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger decline-btn flex-fill"
                                                    data-id="{{ $overtime->id }}"
                                                    data-employee="{{ $overtime->employee_name }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#declineModal">
                                                    <i class="fas fa-times"></i> Decline
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
                    <div class="card-header bg-success text-white">
                        <h5 class="mt-2"><i class="fas fa-calendar-check"></i> Approved Overtime Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="approvedTable" class="table table-bordered table-hover w-100">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">Employee</th>
                                        <th width="12%">Date</th>
                                        <th width="10%">Hours</th>
                                        <th width="13%">Overtime Type</th>
                                        <th width="20%">Reason</th>
                                        <th width="12%">Approved By</th>
                                        <th width="13%">Approved On</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($approvedRequests as $index => $overtime)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $overtime->employee_name }}</td>
                                        <td>{{ date('d M Y', strtotime($overtime->date)) }}</td>
                                        <td>{{ $overtime->total_hours }} hrs</td>
                                        <td>
                                            @if($overtime->overtime_type == 'Paid_Overtime')
                                            <span class="badge badge-info">Paid Overtime</span>
                                            @else
                                            <span class="badge badge-primary">Overtime Leave</span>
                                            @endif
                                        </td>
                                        <td>{{ $overtime->reason }}</td>
                                        <td>
                                            @php
                                            $approver = \App\Models\User::find($overtime->answer_user_id);
                                            @endphp
                                            {{ $approver ? $approver->name : 'Unknown' }}
                                        </td>
                                        <td>{{ date('d M Y', strtotime($overtime->updated_at)) }}</td>
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
                    <div class="card-header bg-danger text-white">
                        <h5 class="mt-2"><i class="fas fa-calendar-times"></i> Declined Overtime Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="declinedTable" class="table table-bordered table-hover w-100">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">Employee</th>
                                        <th width="12%">Date</th>
                                        <th width="10%">Hours</th>
                                        <th width="13%">Overtime Type</th>
                                        <th width="15%">Reason</th>
                                        <th width="20%">Declined Reason</th>
                                        <th width="10%">Declined By</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($declinedRequests as $index => $overtime)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $overtime->employee_name }}</td>
                                        <td>{{ date('d M Y', strtotime($overtime->date)) }}</td>
                                        <td>{{ $overtime->total_hours }} hrs</td>
                                        <td>
                                            @if($overtime->overtime_type == 'Paid_Overtime')
                                            <span class="badge badge-info">Paid Overtime</span>
                                            @else
                                            <span class="badge badge-primary">Overtime Leave</span>
                                            @endif
                                        </td>
                                        <td>{{ $overtime->reason }}</td>
                                        <td>{{ $overtime->declined_reason }}</td>
                                        <td>
                                            @php
                                            $decliner = \App\Models\User::find($overtime->answer_user_id);
                                            @endphp
                                            {{ $decliner ? $decliner->name : 'Unknown' }}
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
</div>


<!-- Decline Modal -->
<div class="modal fade" id="declineModal" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="declineModalLabel">Decline Overtime Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="declined_reason" class="form-label">Reason for Declining</label>
                    <textarea class="form-control" id="declined_reason" name="declined_reason" rows="3" required></textarea>
                    <input type="hidden" id="decline_overtime_id">
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
        // Initialize DataTables
        $('#pendingTable').DataTable({
            responsive: true,
            "ordering": true,
            "info": true,
            "paging": true,
            "lengthChange": true,
            "language": {
                "search": "Search:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "Showing 0 to 0 of 0 entries",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            }
        });

        $('#approvedTable').DataTable({
            responsive: true,
            "ordering": true
        });

        $('#declinedTable').DataTable({
            responsive: true,
            "ordering": true
        });

        // Fix tab functionality
        $('#overtimeTabs a').on('click', function(e) {
            e.preventDefault();
            $(this).tab('show');
        });

        // Approve button with SweetAlert2
        $('.approve-btn').click(function() {
            const id = $(this).data('id');
            const employee = $(this).data('employee');

            Swal.fire({
                title: 'Approve Overtime Request',
                text: `Are you sure you want to approve ${employee}'s overtime request?`,
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
                        url: `{{ url('time_management/overtime/approve') }}/${id}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Approved!',
                                text: 'Overtime request has been approved successfully.',
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
            $('#decline_overtime_id').val(id);
            $('#decline_employee_name').val(employee);
            $('#declined_reason').val(''); // Clear previous values
        });

        // Handle decline submission with SweetAlert2 after modal
        $('#submitDeclineBtn').click(function() {
            const id = $('#decline_overtime_id').val();
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
                text: `Are you sure you want to decline ${employee}'s overtime request?`,
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
                        url: `{{ url('time_management/overtime/decline') }}/${id}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            declined_reason: reason
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Declined!',
                                text: 'Overtime request has been declined successfully.',
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
    });
</script>
@endpush