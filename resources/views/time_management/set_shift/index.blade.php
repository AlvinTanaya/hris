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
    </ul>

    <div class="tab-content mt-4">
        <!-- Current Shifts Section -->
        <div class="tab-pane fade show active" id="current" role="tabpanel">
            <div id="currentFilter" class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="text-primary mt-2"><i class="fas fa-filter"></i> Filter Current Shifts</h5>
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
                                    <th>Position</th>
                                    <th>Department</th>
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
                                    <td>{{ $shift->historical_position ? $shift->historical_position->position : 'N/A' }}</td>
                                    <td>{{ $shift->historical_department ? $shift->historical_department->department : 'N/A' }}</td>
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
            <div id="historyFilter" class="card shadow-sm mb-4">
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
                                    <th>Position</th>
                                    <th>Department</th>
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
                                    <td>{{ $shift->historical_position ? $shift->historical_position->position : 'N/A' }}</td>
                                    <td>{{ $shift->historical_department ? $shift->historical_department->department : 'N/A' }}</td>
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

        // Initialize DataTables with common settings
        $('#currentShiftTable, #historyShiftTable').DataTable();
        $('.dataTables_filter input').addClass('ms-2');

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

            const submitButton = $(this).find('button[type="submit"]');
            const originalButtonText = submitButton.html();
            submitButton.prop('disabled', true);
            submitButton.html('<i class="fas fa-spinner fa-spin"></i> Processing...');

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
                    submitButton.html(originalButtonText);
                    submitButton.prop('disabled', false);
                    handleAjaxResponse(response, 'Shift Updated', 'Employee shift has been updated successfully!');
                },
                error: function(xhr, status, error) {
                    submitButton.html(originalButtonText);
                    submitButton.prop('disabled', false);
                    handleAjaxError(xhr, status, error);
                }
            });
        });

        // Delete Shift with Confirmation
        $('.deleteShiftBtn').on('click', function() {
            let shiftId = $(this).data('id');
            const deleteButton = $(this);
            const originalButtonText = deleteButton.html();

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
                    deleteButton.prop('disabled', true);
                    deleteButton.html('<i class="fas fa-spinner fa-spin"></i> Deleting...');

                    $.ajax({
                        url: `/time_management/set_shift/delete/${shiftId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            deleteButton.html(originalButtonText);
                            deleteButton.prop('disabled', false);
                            handleAjaxResponse(response, 'Deleted!', 'Employee shift has been deleted.');
                        },
                        error: function() {
                            deleteButton.html(originalButtonText);
                            deleteButton.prop('disabled', false);
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
            const submitButton = $(this).find('button[type="submit"]');
            const originalButtonText = submitButton.html();
            submitButton.prop('disabled', true);
            submitButton.html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: "{{ route('time.set.shift.exchange') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    submitButton.html(originalButtonText);
                    submitButton.prop('disabled', false);
                    handleAjaxResponse(response, 'Success!', response.message);
                },
                error: function(xhr, status, error) {
                    submitButton.html(originalButtonText);
                    submitButton.prop('disabled', false);
                    handleAjaxError(xhr, status, error);
                }
            });
        });
    });
</script>
@endpush