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
</style>

<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px"><i class="fas fa-user-clock"></i> Employee Shifts</h1>

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
                    <form action="{{ route('time.employee.shift.index') }}" method="GET" class="row g-3">
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
                            <a href="{{ route('time.employee.shift.index') }}?tab=current" class="btn btn-secondary">
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
                        <a href="{{ route('time.employee.shift.create') }}" class="btn btn-primary">
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
                    <form action="{{ route('time.employee.shift.index') }}" method="GET" class="row g-3">
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
                            <a href="{{ route('time.employee.shift.index') }}?tab=history" class="btn btn-secondary">
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
                    <input type="hidden" id="shift_id">
                    <div class="mb-3">
                        <label class="form-label">Rule</label>
                        <select class="form-select" id="edit_rule_id">
                            @foreach($rules as $rule)
                            <option value="{{ $rule->id }}">{{ $rule->type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="edit_start_date">
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
        $('#currentShiftTable, #historyShiftTable').DataTable();

        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            let target = $(e.target).attr("href");
            if (target === "#current") {
                $("#currentFilter").removeClass("d-none");
                $("#historyFilter").addClass("d-none");
            } else {
                $("#currentFilter").addClass("d-none");
                $("#historyFilter").removeClass("d-none");
            }
        });

        // Open Edit Modal
        $('.editShiftBtn').on('click', function() {
            let shiftId = $(this).data('id');
            let ruleId = $(this).data('rule');
            let userId = $(this).data('user');
            let startDate = $(this).data('start');

            $('#editShiftId').val(shiftId);
            $('#editUserId').val(userId);
            $('#editRule').val(ruleId);
            $('#editStartDate').val(startDate);

            $('#editShiftModal').modal('show');
        });

        // Submit Edit Form
        $('#editShiftForm').on('submit', function(e) {
            e.preventDefault();
            let shiftId = $('#editShiftId').val();
            let userId = $('#editUserId').val();
            let ruleId = $('#editRule').val();
            let startDate = $('#editStartDate').val();

            $.ajax({
                url: `/time_management/employee_shift/update/${shiftId}`,
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    rule_id: ruleId,
                    user_id: userId,
                    start_date: startDate
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Shift Updated',
                        text: 'Employee shift has been updated successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                },
                error: function(xhr) {
                    let errorMsg = xhr.responseJSON.message || "An error occurred.";
                    Swal.fire("Error", errorMsg, "error");
                }
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
                        url: `/time_management/employee_shift/delete/${shiftId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Employee shift has been deleted.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => location.reload());
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


        $("#exchangeShiftForm").submit(function(event) {
            event.preventDefault();

            $.ajax({
                url: "{{ route('time.employee.shift.exchange') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        text: response.message,
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: xhr.responseJSON.message,
                    });
                }
            });
        });


    });
</script>

@endpush