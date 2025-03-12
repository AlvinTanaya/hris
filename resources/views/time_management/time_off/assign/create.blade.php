@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('time.off.assign.index') }}" class="btn btn-danger px-4">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
            <h1 class="text-center text-warning my-4">
                <i class="fas fa-plus me-2"></i>Assign Time Off
            </h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-10 col-md-12">
            <div class="card shadow-lg">
                <div class="card-body p-4">
                    <form action="{{ route('time.off.assign.store') }}" method="POST" id="timeOffForm">
                        @csrf

                        <!-- Time Off Policy Section -->
                        <div class="row mb-4">
                            <div class="col-md-12 mb-3">
                                <label for="time_off_id" class="form-label">Time Off Policy</label>
                                <select name="time_off_id" id="time_off_id" class="form-select form-select-lg" required>
                                    <option value="" disabled selected>Select a time off policy</option>
                                    @foreach($timeOffPolicies as $policy)
                                    <option value="{{ $policy->id }}">{{ $policy->time_off_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label for="balance" class="form-label">Balance</label>
                                <div class="input-group input-group-lg">
                                    <input type="number" name="balance" id="balance" class="form-control" min="0" required placeholder="Select a policy first">
                                    <span class="input-group-text">days</span>
                                </div>
                                <div id="balance-feedback" class="invalid-feedback">
                                    Balance cannot exceed the maximum allowed value.
                                </div>
                                <div id="balance-helper" class="form-text d-none">
                                    Maximum allowed balance: <span class="text-primary fw-bold">0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Employee Assignment Section -->
                        <div class="row mt-4 mb-3">
                            <div class="col-12">
                                <h4 class="text-primary d-flex align-items-center">
                                    <i class="fas fa-users me-2"></i>
                                    <span>Assign to Employees</span>
                                </h4>
                                <hr>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- Filter Section -->
                            <div class="col-md-6 border-end pe-4">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="positionFilter" class="form-label">Filter by Position</label>
                                        <select id="positionFilter" class="form-select">
                                            <option value="" selected>All Position</option>
                                            @foreach($positions as $position)
                                            <option value="{{ $position }}">{{ $position }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="departmentFilter" class="form-label">Filter by Department</label>
                                        <select id="departmentFilter" class="form-select">
                                            <option value="" selected>All Department</option>
                                            @foreach($departments as $department)
                                            <option value="{{ $department }}">{{ $department }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-warning w-100" id="addAllEmployees">
                                            <i class="fas fa-users me-2"></i> Add All Filtered Employees
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Employee Selection Section -->
                            <div class="col-md-6 ps-4">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="employees" class="form-label">Select Employees</label>
                                        <select id="employees" class="form-select" multiple style="height: 100px;">
                                            @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" data-department="{{ $employee->department }}" data-position="{{ $employee->position }}">
                                                {{ $employee->employee_id }} - {{ $employee->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-primary w-100" id="addEmployees">
                                            <i class="fas fa-user-plus me-2"></i> Add Selected Employees
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assigned Employees Table -->
                        <div class="card mt-4">
                            <div class="card-header bg-primary">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-white fs-5">Assigned Employees</span>
                                    <button type="button" class="btn btn-danger btn-sm" id="removeAllEmployees">
                                        <i class="fas fa-trash-alt me-1"></i> Remove All
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered mb-0" id="employeeTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Employee ID</th>
                                                <th>Name</th>
                                                <th>Position</th>
                                                <th>Department</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="assignedEmployees">
                                            <!-- Data will be displayed here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="invited_employees" id="assignedEmployeesInput">

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-success btn-lg" id="saveButton">
                                <i class="fas fa-save me-2"></i>Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<!-- Include Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<!-- SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
    }

    .select2-container--default .select2-selection--multiple,
    .select2-container--default .select2-selection--single {
        border: 1px solid #ced4da !important;
        border-radius: 0.375rem !important;
    }

    .select2-container {
        width: 100% !important;
    }

    /* Hide the clear button (X) in Select2 dropdowns */
    .select2-selection__clear {
        display: none !important;
    }

    /* Add this to your style section */
    .select2-results__option[aria-disabled=true] {
        color: #999 !important;
        background-color: #f8f8f8 !important;
        cursor: not-allowed !important;
    }

    /* Add this class to your style tags */
    .employee-already-assigned {
        color: #999;
        font-style: italic;
    }
</style>

<script>
    $(document).ready(function() {
        $('#departmentFilter, #positionFilter, #time_off_id').change(function() {
            filterEmployees();
        });

        // Initialize Select2
        $('#employees, #positionFilter, #departmentFilter, #time_off_id').select2({
            width: '100%',
            allowClear: false
        });

        let assignedEmployees = [];
        let employeeList = @json($employees);
        let maxBalance = 0;

        // Handle time off policy change
        $('#time_off_id').change(function() {
            const timeOffId = $(this).val();
            if (!timeOffId) {
                resetBalanceInput();
                return;
            }

            // Reset assigned employees and UI
            assignedEmployees = [];
            $('#assignedEmployees').empty();
            updateHiddenInput();
            filterEmployees();

            // Fetch the quota for this time off policy
            $.ajax({
                url: "{{ route('time.off.get.policy.quota') }}",
                type: 'GET',
                data: {
                    time_off_id: timeOffId
                },
                success: function(response) {
                    maxBalance = response.quota ? parseFloat(response.quota) : 0;
                    $('#balance').prop('max', maxBalance || '').attr('placeholder', maxBalance ? `Enter balance (max: ${maxBalance})` : 'Enter balance');
                    $('#balance-helper').toggleClass('d-none', maxBalance === 0);
                    $('#balance-helper span').text(maxBalance);
                }
            });
        });

        // Validate balance input
        $('#balance').on('input', function() {
            validateBalanceInput();
        });

        function validateBalanceInput() {
            let balance = parseFloat($('#balance').val()) || 0;
            if (maxBalance > 0 && balance > maxBalance) {
                $('#balance').addClass('is-invalid');
                $('#balance-feedback').text(`Balance cannot exceed ${maxBalance} days`);
                return false;
            } else {
                $('#balance').removeClass('is-invalid');
                return true;
            }
        }

        function resetBalanceInput() {
            $('#balance').prop('max', '').attr('placeholder', 'Select a policy first');
            $('#balance-helper').addClass('d-none');
        }

        function filterEmployees() {
            let selectedDepartment = $('#departmentFilter').val();
            let selectedPosition = $('#positionFilter').val();
            let timeOffId = $('#time_off_id').val();

            if (!timeOffId) return;

            $('#employees').html('<option>Loading...</option>');

            $.ajax({
                url: "{{ route('time.off.get.assigned.employees') }}",
                type: 'GET',
                data: {
                    time_off_id: timeOffId
                },
                success: function(response) {
                    const alreadyAssignedIds = response.employees.map(String) || [];

                    $('#employees').empty();

                    employeeList.forEach(function(emp) {
                        const matchDept = !selectedDepartment || selectedDepartment === emp.department;
                        const matchPos = !selectedPosition || selectedPosition === emp.position;
                        const isAlreadyAssigned = alreadyAssignedIds.includes(String(emp.id)) || assignedEmployees.includes(String(emp.id));

                        if (matchDept && matchPos) {
                            const option = new Option(`${emp.employee_id} - ${emp.name}`, emp.id, false, false);
                            $(option).data('department', emp.department);
                            $(option).data('position', emp.position);

                            if (isAlreadyAssigned) {
                                $(option).prop('disabled', true).text(`${emp.employee_id} - ${emp.name} (Already assigned)`);
                            }

                            $('#employees').append(option);
                        }
                    });

                    $('#employees').select2({
                        width: '100%',
                        placeholder: "Select employees",
                        allowClear: false
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load employee data. Please try again.'
                    });
                }
            });
        }

        $('#addEmployees').on('click', function() {
            if (!$('#time_off_id').val()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please select a Time Off Policy first!'
                });
                return;
            }

            let selectedEmployees = $('#employees').val();
            if (!selectedEmployees || selectedEmployees.length === 0) return;

            addEmployeesToTable(selectedEmployees);
            $('#employees').val(null).trigger('change');
        });

        $('#addAllEmployees').on('click', function() {
            if (!$('#time_off_id').val()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please select a Time Off Policy first!'
                });
                return;
            }

            let filteredEmployees = [];
            $('#employees option:not(:disabled):visible').each(function() {
                filteredEmployees.push($(this).val());
            });

            if (filteredEmployees.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'No Employees Available',
                    text: 'All employees in this filter are already assigned this time off policy.'
                });
                return;
            }

            addEmployeesToTable(filteredEmployees);
            $('#employees').val(null).trigger('change');
        });

        $('#removeAllEmployees').on('click', function() {
            if (assignedEmployees.length === 0) return;

            Swal.fire({
                title: 'Are you sure?',
                text: 'You are about to remove all assigned employees. This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove all!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    assignedEmployees = [];
                    $('#assignedEmployees').empty();
                    updateHiddenInput();
                    Swal.fire('Removed!', 'All assigned employees have been removed.', 'success');
                }
            });
        });

        function addEmployeesToTable(employeeIds) {
            const timeOffId = $('#time_off_id').val();

            $.ajax({
                url: "{{ route('time.off.get.assigned.employees') }}",
                type: 'GET',
                data: {
                    time_off_id: timeOffId
                },
                success: function(response) {
                    const alreadyAssignedIds = response.employees.map(String) || [];
                    let skippedEmployees = 0;

                    employeeIds.forEach(id => {
                        if (assignedEmployees.includes(id) || alreadyAssignedIds.includes(id)) {
                            skippedEmployees++;
                            return;
                        }

                        let emp = employeeList.find(e => e.id == id);
                        if (emp) {
                            assignedEmployees.push(id);
                            $('#assignedEmployees').append(`
                            <tr data-id="${id}">
                                <td>${emp.employee_id}</td>
                                <td>${emp.name}</td>
                                <td>${emp.position}</td>
                                <td>${emp.department}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm removeEmployee" data-id="${id}">Remove</button>
                                </td>
                            </tr>
                        `);
                        }
                    });

                    updateHiddenInput();

                    if (skippedEmployees > 0) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Some employees skipped',
                            text: `${skippedEmployees} employee(s) already have this time off policy assigned.`
                        });
                    }
                }
            });
        }

        $(document).on('click', '.removeEmployee', function() {
            let id = $(this).data('id');
            assignedEmployees = assignedEmployees.filter(empId => empId != id);
            $(`tr[data-id="${id}"]`).remove();
            updateHiddenInput();
        });

        function updateHiddenInput() {
            $('#assignedEmployeesInput').val(assignedEmployees.join(','));
        }
    });
</script>
@endpush