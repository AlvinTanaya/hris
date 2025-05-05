@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Page Header -->

        <div class="col-12 text-center mb-5">
            <h1 class="text-warning">
                <i class="fas fa-money-check-alt me-2"></i>Assign Payroll
            </h1>
        </div>
        <!-- Period Selection Card -->
        <div class="col-md-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-alt fa-2x me-3"></i>
                            <h4 class="mb-0 fw-bold">Select Payment Period</h4>
                        </div>
                        <a href="{{ route('payroll.assign.index') }}" class="btn btn-danger btn-lg px-5">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form id="payrollForm">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="month_year" class="form-label fw-bold">Payment Period <span class="text-danger">*</span></label>

                                <input type="month" class="form-control form-control-lg shadow-sm" id="month_year" name="month_year" required>
                                <div class="form-text text-muted">Select the month and year for payroll processing</div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-12" id="employeeSelectionSection" style="display: none;">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-info text-white py-3 d-flex align-items-center">
                    <i class="fas fa-users fa-lg me-2"></i>
                    <h5 class="mb-0 fw-semibold">Employee Selection</h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info d-flex align-items-center shadow-sm mb-4">
                        <i class="fas fa-info-circle me-2 text-info"></i>
                        <small class="mb-0">Filter employees by department or position, or select specific employees to add to the payroll.</small>
                    </div>

                    <div class="row g-4">
                        <!-- Filters Section -->
                        <div class="col-md-6">
                            <h6 class="text-primary fw-bold mb-3"><i class="fas fa-filter me-2"></i> Filter Employees</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <select class="form-select form-select-sm shadow-sm" id="position_filter">
                                        <option value="">All Positions</option>
                                        @foreach ($positions as $position)
                                        <option value="{{ $position->id }}">{{ $position->position }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <select class="form-select form-select-sm shadow-sm" id="department_filter">
                                        <option value="">All Departments</option>
                                        @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->department }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="button" class="btn btn-warning w-100 shadow-sm" id="addAllFilteredEmployees">
                                        <i class="fas fa-users me-1"></i> Add All Filtered Employees
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Manual Selection Section -->
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2"><i class="fas fa-user-plus me-2 mb-2 text-primary"></i> Select Specific Employees</h6>
                            <select class="form-select shadow-sm mb-3" id="employee_select" multiple style="width: 100%; height: 120px;">
                                <!-- Options loaded via AJAX -->
                            </select>
                            <button type="button" class="btn btn-primary w-100 shadow-sm mt-3" id="addSelectedEmployees">
                                <i class="fas fa-plus-circle me-1"></i> Add Selected Employees
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Employee Payroll Table - Initially Hidden -->
        <div class="col-md-12" id="employeePayrollSection" style="display: none;">
            <div class="card shadow-sm border-0 mb-4">
                <!-- Update for Fix 3: Action buttons container with proper alignment -->
                <div class="card-header bg-success text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-table fa-2x me-3"></i>
                            <h4 class="mb-0 fw-bold">Employee Payroll Data</h4>
                        </div>
                        <div class="action-buttons-container">
                            <button type="button" class="btn btn-danger btn-sm" id="deleteAllBtn">
                                <i class="fas fa-trash me-1"></i> Delete All
                            </button>
                            <button type="button" class="btn btn-light btn-sm" id="selectAll">
                                <i class="fas fa-check-square me-1"></i> Select All
                            </button>
                            <button type="button" class="btn btn-dark btn-sm" id="deselectAll">
                                <i class="fas fa-square me-1"></i> Deselect All
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Mass Update Section -->
                    <div class="card bg-light border-0 shadow-sm mb-4">
                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold mb-3">
                                <i class="fas fa-edit me-2"></i>Mass Update
                            </h5>
                            <p class="text-muted mb-3">Apply values to all selected employees simultaneously</p>

                            <div class="row g-3 justify-content-center">
                                <div class="col-md-4 col-lg-2">
                                    <div class="form-floating mb-2">
                                        <input type="number" class="form-control" id="mass_basic_salary" min="0" step="0.01">
                                        <label for="mass_basic_salary">Basic Salary</label>
                                    </div>
                                    <button type="button" class="btn btn-secondary w-100 mass-update" data-field="basic_salary">
                                        <i class="fas fa-check me-1"></i> Apply
                                    </button>
                                </div>

                                <div class="col-md-4 col-lg-2">
                                    <div class="form-floating mb-2">
                                        <input type="number" class="form-control" id="mass_allowance" min="0" step="0.01">
                                        <label for="mass_allowance">Allowance</label>
                                    </div>
                                    <button type="button" class="btn btn-secondary w-100 mass-update" data-field="allowance">
                                        <i class="fas fa-check me-1"></i> Apply
                                    </button>
                                </div>

                                <div class="col-md-4 col-lg-2">
                                    <div class="form-floating mb-2">
                                        <input type="number" class="form-control" id="mass_bonus" min="0" step="0.01">
                                        <label for="mass_bonus">Bonus</label>
                                    </div>
                                    <button type="button" class="btn btn-secondary w-100 mass-update" data-field="bonus">
                                        <i class="fas fa-check me-1"></i> Apply
                                    </button>
                                </div>

                                <div class="col-md-4 col-lg-2">
                                    <div class="form-floating mb-2">
                                        <input type="number" class="form-control" id="mass_overtime_rate" min="0" step="0.01">
                                        <label for="mass_overtime_rate">OT Rate</label>
                                    </div>
                                    <button type="button" class="btn btn-secondary w-100 mass-update" data-field="overtime_rate">
                                        <i class="fas fa-check me-1"></i> Apply
                                    </button>
                                </div>

                                <div class="col-md-4 col-lg-2">
                                    <div class="form-floating mb-2">
                                        <input type="number" class="form-control" id="mass_reduction_salary" min="0" step="0.01">
                                        <label for="mass_reduction_salary">Reduction</label>
                                    </div>
                                    <button type="button" class="btn btn-secondary w-100 mass-update" data-field="reduction_salary">
                                        <i class="fas fa-check me-1"></i> Apply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Payroll Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped" id="employeesTable">
                            <thead class="table-dark">
                                <tr>
                                    <th width="3%" class="text-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll">
                                        </div>
                                    </th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Basic Salary</th>
                                    <th>OT Hours</th>
                                    <th>OT Rate</th>
                                    <th>Allowance</th>
                                    <th>Bonus</th>
                                    <th>Absences (Alpa)</th>
                                    <th>Reduction</th>
                                    <th>Total</th>
                                    <th width="5%">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="employeesTableBody">
                                <tr>
                                    <td colspan="13" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center text-muted">
                                            <i class="fas fa-users fa-3x mb-3"></i>
                                            <p>Add employees to the table using the controls above</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-success btn-lg px-5" id="submitBtn" disabled>
                            <i class="fas fa-save me-2"></i> Save Payroll
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    /* General Styles */
    body {
        background-color: #f8f9fa;
    }

    .card {
        border-radius: 0.8rem;
        overflow: hidden;
    }

    .card-header {
        border-bottom: 0;
    }



    .bg-info {
        background: linear-gradient(45deg, #00bcd4, #00acc1);
    }

    .bg-success {
        background: linear-gradient(45deg, #2e7d32, #43a047);
    }

    .form-control,
    .form-select,
    .btn {
        border-radius: 0.5rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #3d5afe;
        box-shadow: 0 0 0 0.25rem rgba(61, 90, 254, 0.25);
    }

    /* Select2 Customization */
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--multiple {
        border-radius: 0.5rem;
        border: 1px solid #ced4da;
        min-height: 38px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        padding-left: 12px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #3d5afe;
        border: 1px solid #2962ff;
        color: white;
        border-radius: 0.3rem;
        padding: 2px 8px;
        margin-top: 4px;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3d5afe;
    }

    /* Button Styling */
    .btn {
        font-weight: 500;
        padding: 0.5rem 1rem;
        transition: all 0.3s;
    }

    .btn-primary {
        background-color: #3d5afe;
        border-color: #3d5afe;
    }

    .btn-primary:hover {
        background-color: #2962ff;
        border-color: #2962ff;
    }

    .btn-warning {
        background-color: #ff9800;
        border-color: #ff9800;
        color: white;
    }

    .btn-warning:hover {
        background-color: #fb8c00;
        border-color: #fb8c00;
        color: white;
    }

    /* Table styling */
    .table {
        vertical-align: middle;
    }

    .table> :not(:first-child) {
        border-top: none;
    }

    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    /* Employee row specific */
    .absences-badge {
        font-size: 0.8rem;
        padding: 0.35rem 0.5rem;
        border-radius: 1rem;
        font-weight: 600;
    }

    .total-amount {
        font-weight: 700;
        color: #3d5afe;
        font-size: 1.1rem;
    }

    .btn-delete-employee {
        color: white;
        background-color: #f44336;
        border: none;
        border-radius: 50%;
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        transition: all 0.2s;
    }

    .btn-delete-employee:hover {
        background-color: #d32f2f;
        transform: scale(1.1);
    }

    /* Form Control Sizing */
    .salary-component {
        height: 38px;
        font-size: 0.9rem;
    }


    .action-buttons-container {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .action-buttons-container .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 100px;
    }

    /* Mass update apply buttons alignment */
    .mass-update-container {
        display: flex;
        justify-content: space-between;
        gap: 15px;
    }

    .mass-update-item {
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .mass-update-item .btn {
        margin-top: 8px;
    }

    .select2-results__option .text-muted {
        display: none !important;
    }

    .select2-selection__rendered .text-muted {
        display: none !important;
    }
</style>
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Add this CSS for button alignment
        $('.action-buttons-container').css({
            'display': 'flex',
            'justify-content': 'flex-end',
            'gap': '10px'
        });

        let dataTable;
        let employeesData = {}; // Store fetched employee data
        let addedEmployees = new Set(); // Track added employee IDs

        // Initialize Select2
        $('.select2').select2({
            width: '100%'
        });

        // Initialize employee multiple select with Select2
        $('#employee_select').select2({
            width: '100%',
            placeholder: 'Select employees to add to payroll',
            allowClear: true,
            closeOnSelect: false,
            templateResult: formatEmployeeSelection,
            templateSelection: formatEmployeeSelection
        });


        // Change event handler for month_year input
        $('#month_year').change(function() {
            const monthYear = $(this).val();
            if (!monthYear) {
                showSweetAlert('warning', 'Missing Information', 'Please select a month/year first!');
                return;
            }

            // Clear the employees set
            addedEmployees.clear();

            // Destroy DataTable if it exists
            if (dataTable) {
                dataTable.destroy();
                dataTable = null;
            }

            // Empty the table body and add the empty message
            $('#employeesTableBody').html(`
                <tr>
                <td colspan="13" class="text-center py-4">
                    <div class="d-flex flex-column align-items-center text-muted">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <p>Add employees to the table using the controls above</p>
                    </div>
                </td>
                </tr>
                `);

            // Reset any filter selections
            $('#department_filter').val('').trigger('change');
            $('#position_filter').val('').trigger('change');
            $('#employee_select').val(null).trigger('change');

            // Make sure the checkAll checkbox is unchecked
            $('#checkAll').prop('checked', false);

            // Update submit button state
            updateSubmitButtonState();

            // Show employee selection section
            $('#employeeSelectionSection').slideDown('fast');
            $('#employeePayrollSection').slideDown('fast');

            // Load available employees for the selected month
            loadAvailableEmployees();
        });

        // Improved Delete All button functionality
        $('#deleteAllBtn').click(function() {
            // Show confirmation dialog
            Swal.fire({
                title: 'Remove All Employees?',
                text: "Are you sure you want to remove all employees from the payroll assignment?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove all',
                cancelButtonText: 'Cancel',
                position: 'center' // Keep confirmation dialog centered
            }).then((result) => {
                if (result.isConfirmed) {
                    // Clear the employees set
                    addedEmployees.clear();

                    // Destroy DataTable if it exists
                    if (dataTable) {
                        dataTable.destroy();
                        dataTable = null;
                    }

                    // Empty the table body and add the empty message
                    $('#employeesTableBody').html(`
                <tr>
                    <td colspan="13" class="text-center py-4">
                        <div class="d-flex flex-column align-items-center text-muted">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <p>Add employees to the table using the controls above</p>
                        </div>
                    </td>
                </tr>
            `);

                    // Reset any filter selections
                    $('#department_filter').val('').trigger('change');
                    $('#position_filter').val('').trigger('change');
                    $('#employee_select').val(null).trigger('change');

                    // Make sure the checkAll checkbox is unchecked
                    $('#checkAll').prop('checked', false);

                    // Update submit button state
                    updateSubmitButtonState();

                    // Show success message using our toast-style SweetAlert at bottom-right
                    showSweetAlert('success', 'Employees Removed', 'All employees have been removed from the payroll table');
                }
            });
        });



        // Format employee selection items with department info
        function formatEmployeeSelection(employee) {
            if (!employee.id) return employee.text;
            // Just return the name, no department or position info
            return employee.text;
        }

        // Load available employees for both selection methods
        function loadAvailableEmployees() {
            const monthYear = $('#month_year').val();

            // Show loading in employee dropdown
            $('#employee_select').html('<option value="">Loading employees...</option>');

            $.ajax({
                url: "{{ route('payroll.assign.get-filtered-users') }}",
                type: "GET",
                data: {
                    month_year: monthYear
                },
                success: function(response) {
                    // Store employee data for quick access
                    employeesData = {};
                    response.forEach(function(user) {
                        employeesData[user.id] = user;
                    });

                    // Reset and rebuild employee dropdown for multiple select
                    $('#employee_select').empty();

                    if (response.length > 0) {
                        response.forEach(function(user) {
                            // Just add the name without position/department
                            $('#employee_select').append(`<option value="${user.id}">${user.name}</option>`);
                        });
                        showSweetAlert('success', 'Employees Loaded', `${response.length} employees available for payroll assignment`);
                    } else {
                        $('#employee_select').append('<option value="" disabled>No employees available</option>');
                        showSweetAlert('info', 'No Employees', 'No employees available for the selected month');
                    }

                    // Refresh Select2
                    $('#employee_select').trigger('change');
                },
                error: function() {
                    $('#employee_select').html('<option value="" disabled>Error loading employees</option>');
                    showSweetAlert('error', 'Error', 'Failed to load employees. Please try again.');
                }
            });
        }


        // Add individually selected employees to the table
        $('#addSelectedEmployees').click(function() {
            const selectedIds = $('#employee_select').val();

            if (!selectedIds || selectedIds.length === 0) {
                showSweetAlert('warning', 'Selection Empty', 'Please select at least one employee');
                return;
            }

            // Show loading indicator
            $(this).html('<i class="fas fa-spinner fa-spin me-2"></i> Adding...');
            $(this).prop('disabled', true);

            let anyAdded = false;
            let alreadyAssigned = [];

            // Process each selected employee
            selectedIds.forEach(function(employeeId) {
                // Skip if already added to table
                if (addedEmployees.has(parseInt(employeeId))) {
                    alreadyAssigned.push(employeesData[employeeId].name);
                    return;
                }

                // Add employee to table
                if (addEmployeeToTable(employeeId)) {
                    anyAdded = true;
                }
            });

            // Show notification about already assigned employees
            if (alreadyAssigned.length > 0) {
                showSweetAlert('info', 'Already Added', `The following employees are already in the table: ${alreadyAssigned.join(', ')}`);
            }

            // Reset select2 after adding
            $('#employee_select').val(null).trigger('change');

            // Reset button after a short delay
            setTimeout(() => {
                $(this).html('<i class="fas fa-plus me-2"></i> Add Selected Employees');
                $(this).prop('disabled', false);
            }, 500);
        });

        // Add all filtered employees to the table
        $('#addAllFilteredEmployees').click(function() {
            const departmentId = $('#department_filter').val();
            const positionId = $('#position_filter').val();
            const monthYear = $('#month_year').val();

            if (!monthYear) {
                showSweetAlert('warning', 'Missing Information', 'Please select a month/year first!');
                return;
            }

            // Show loading indicator
            $(this).html('<i class="fas fa-spinner fa-spin me-2"></i> Adding...');
            $(this).prop('disabled', true);

            $.ajax({
                url: "{{ route('payroll.assign.get-filtered-users') }}",
                type: "GET",
                data: {
                    month_year: monthYear,
                    department_id: departmentId,
                    position_id: positionId
                },
                success: (response) => {
                    if (response.length === 0) {
                        showSweetAlert('info', 'No Employees', 'No employees found matching the filter criteria.');
                    } else {
                        let addedCount = 0;
                        let alreadyAssigned = [];

                        // Store employee data and add to table
                        response.forEach((user) => {
                            employeesData[user.id] = user;

                            // Skip if already added to table
                            if (addedEmployees.has(parseInt(user.id))) {
                                alreadyAssigned.push(user.name);
                                return;
                            }

                            // Add employee to table
                            if (addEmployeeToTable(user.id)) {
                                addedCount++;
                            }
                        });

                        // Show result message
                        if (addedCount > 0) {
                            showSweetAlert('success', 'Employees Added', `Added ${addedCount} employees to the payroll table.`);
                        }

                        // Show notification about already assigned employees
                        if (alreadyAssigned.length > 0) {
                            showSweetAlert('info', 'Already Added', `${alreadyAssigned.length} employees were already in the table.`);
                        }
                    }
                },
                error: () => {
                    showSweetAlert('error', 'Error', 'Failed to load employees. Please try again.');
                },
                complete: () => {
                    // Reset button
                    $('#addAllFilteredEmployees').html('<i class="fas fa-user-plus me-2"></i> Add Filtered Employees');
                    $('#addAllFilteredEmployees').prop('disabled', false);
                }
            });
        });

        // Add an employee to the payroll table
        function addEmployeeToTable(employeeId) {
            // Get employee data from our cache
            const user = employeesData[employeeId];
            if (!user) {
                console.error('Employee data not found for ID:', employeeId);
                return false;
            }

            // Get values from employee data
            const basicSalary = user.salary ? parseFloat(user.salary.basic_salary) || 0 : 0;
            const allowance = user.salary ? parseFloat(user.salary.allowance) || 0 : 0;
            const overtimeRate = user.salary ? parseFloat(user.salary.overtime_rate_per_hour) || 0 : 0;
            const overtimeHours = parseFloat(user.overtime_hours) || 0;
            const absences = parseInt(user.absences) || 0;

            // Calculate overtime amount
            const overtimeAmount = overtimeHours * overtimeRate;

            // Calculate initial total (will be updated later with bonus and reduction)
            const totalAmount = basicSalary + allowance + overtimeAmount;

            // Absences badge class
            const absenceClass = absences > 0 ? 'bg-danger' : 'bg-success';

            // Determine which department/position to show
            const displayDepartment = user.historical_department ? user.historical_department.department :
                (user.department ? user.department.department : 'No Dept');
            const displayPosition = user.historical_position ? user.historical_position.position :
                (user.position ? user.position.position : 'No Position');

            // Check if table is empty
            if ($('#employeesTableBody tr:first').find('td').attr('colspan')) {
                // Clear empty message
                $('#employeesTableBody').empty();
            }

            // Add row to table
            const newRow = `
        <tr id="employee-row-${user.id}">
            <td class="text-center">
                <div class="form-check">
                    <input class="form-check-input employee-checkbox" type="checkbox" name="user_ids[]" value="${user.id}" id="user_${user.id}" checked>
                </div>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        ${user.name.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <div class="fw-medium">${user.name}</div>
                        <div class="small text-muted">${user.email || ''}</div>
                    </div>
                </div>
            </td>
            <td>${displayDepartment}</td>
            <td>${displayPosition}</td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control salary-component" name="basic_salary[${user.id}]" id="basic_salary_${user.id}" value="${basicSalary}" step="0.01" min="0" data-user-id="${user.id}">
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" class="form-control bg-light" value="${overtimeHours}" readonly>
                    <span class="input-group-text">hrs</span>
                    <input type="hidden" name="overtime_hours[${user.id}]" value="${overtimeHours}">
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control salary-component" name="overtime_rate[${user.id}]" id="overtime_rate_${user.id}" value="${overtimeRate}" step="0.01" min="0" data-user-id="${user.id}">
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
    
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control salary-component" name="allowance[${user.id}]" id="allowance_${user.id}" value="${allowance}" step="0.01" min="0" data-user-id="${user.id}">
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control salary-component" name="bonus[${user.id}]" id="bonus_${user.id}" value="0" step="0.01" min="0" data-user-id="${user.id}">
                </div>
            </td>
            <td>
                <span class="badge ${absenceClass} absences-badge">${absences}</span>
                <input type="hidden" name="absences[${user.id}]" value="${absences}">
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control salary-component reduction-salary-input" name="reduction_salary[${user.id}]" id="reduction_salary_${user.id}" value="0" step="0.01" min="0" data-user-id="${user.id}">
                </div>
            </td>
            <td>
                <div class="total-amount" id="total_amount_${user.id}">$${totalAmount.toFixed(2)}</div>
            </td>
            <td class="text-center">
    <button 
        type="button" 
        class="btn btn-sm btn-danger btn-delete-employee" 
        data-user-id="${user.id}" 
        data-bs-toggle="tooltip" 
        data-bs-placement="top" 
        title="Delete Employee"
    >
        <i class="fas fa-trash-alt"></i>
    </button>
</td>

        </tr>
    `;

            $('#employeesTableBody').append(newRow);

            // Add to set of added employees
            addedEmployees.add(parseInt(employeeId));

            // Initialize DataTable if not already
            initializeDataTable();

            // Enable submit button
            updateSubmitButtonState();

            // Update total for this row
            updateCalculatedTotal(user.id);

            return true;
        }


        // Handle "Check All" checkbox
        $('#checkAll').change(function() {
            $('.employee-checkbox').prop('checked', $(this).is(':checked'));
            updateSubmitButtonState();
        });

        // Select All button
        $('#selectAll').click(function() {
            $('.employee-checkbox').prop('checked', true);
            $('#checkAll').prop('checked', true);
            updateSubmitButtonState();
        });

        // Deselect All button
        $('#deselectAll').click(function() {
            $('.employee-checkbox').prop('checked', false);
            $('#checkAll').prop('checked', false);
            updateSubmitButtonState();
        });

        // Handle individual checkbox changes
        $(document).on('change', '.employee-checkbox', function() {
            updateSubmitButtonState();
            updateCheckAllState();
        });

        // Delete employee from table
        $(document).on('click', '.btn-delete-employee', function() {
            const userId = $(this).data('user-id');

            // Show confirmation dialog
            Swal.fire({
                title: 'Remove Employee?',
                text: "Remove this employee from the payroll assignment?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Remove from added employees set
                    addedEmployees.delete(parseInt(userId));

                    // Remove row from table
                    $(`#employee-row-${userId}`).remove();

                    // Update submit button state
                    updateSubmitButtonState();

                    // Check if table is now empty
                    if ($('#employeesTableBody tr').length === 0) {
                        $('#employeesTableBody').html(`
                            <tr>
                                <td colspan="13" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center text-muted">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <p>Add employees to the table using the controls above</p>
                                    </div>
                                </td>
                            </tr>
                        `);

                        // Destroy DataTable if it exists
                        if (dataTable) {
                            dataTable.destroy();
                            dataTable = null;
                        }
                    }

                    // Show success message
                    showSweetAlert('success', 'Employee Removed', 'Employee has been removed from the payroll table');
                }
            });
        });

        // Mass update functionality
        $('.mass-update').click(function() {
            const field = $(this).data('field');
            const value = $(`#mass_${field}`).val();

            if (value === '' && value !== '0') {
                showSweetAlert('warning', 'Missing Value', 'Please enter a value to apply');
                return;
            }

            const checkedEmployees = $('.employee-checkbox:checked');
            if (checkedEmployees.length === 0) {
                showSweetAlert('warning', 'No Selection', 'Please select at least one employee first');
                return;
            }

            // Apply to all checked rows
            checkedEmployees.each(function() {
                const userId = $(this).val();
                $(`#${field}_${userId}`).val(value);

                // Update the calculated total
                updateCalculatedTotal(userId);
            });

            showSweetAlert('success', 'Mass Update Complete', `Applied ${field.replace('_', ' ')} value to ${checkedEmployees.length} employee(s)`);

            // Clear the mass update field
            $(`#mass_${field}`).val('');
        });

        // Update salary components and recalculate total
        $(document).on('input', '.salary-component', function() {
            const userId = $(this).data('user-id');
            updateCalculatedTotal(userId);
        });

        // Update submit button state
        function updateSubmitButtonState() {
            const anyChecked = $('.employee-checkbox:checked').length > 0;
            $('#submitBtn').prop('disabled', !anyChecked);

            if (anyChecked) {
                $('#submitBtn').removeClass('btn-secondary').addClass('btn-primary');
            } else {
                $('#submitBtn').removeClass('btn-primary').addClass('btn-secondary');
            }
        }

        // Update "Check All" checkbox state
        function updateCheckAllState() {
            const totalCheckboxes = $('.employee-checkbox').length;
            const checkedCheckboxes = $('.employee-checkbox:checked').length;
            $('#checkAll').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
        }

        // Update calculated totals
        function updateCalculatedTotal(userId) {
            const basicSalary = parseFloat($(`#basic_salary_${userId}`).val()) || 0;
            const allowance = parseFloat($(`#allowance_${userId}`).val()) || 0;
            const bonus = parseFloat($(`#bonus_${userId}`).val()) || 0;
            const overtimeHours = parseFloat($(`input[name="overtime_hours[${userId}]"]`).val()) || 0;
            const overtimeRate = parseFloat($(`#overtime_rate_${userId}`).val()) || 0;
            const reductionSalary = parseFloat($(`#reduction_salary_${userId}`).val()) || 0;

            // Calculate overtime amount
            const overtimeAmount = overtimeHours * overtimeRate;

            // Calculate total
            const calculatedTotal = basicSalary + allowance + bonus + overtimeAmount - reductionSalary;

            // Update total amount display
            $(`#total_amount_${userId}`).text(`$${calculatedTotal.toFixed(2)}`);

            // Animate the change
            $(`#total_amount_${userId}`).fadeOut(100).fadeIn(100);
        }

        // Initialize DataTable
        function initializeDataTable() {
            if (!dataTable) {
                dataTable = $('#employeesTable').DataTable({
                    "pageLength": 25,
                    "columnDefs": [{
                        "orderable": false,
                        "targets": [0, 12]
                    }],
                    "language": {
                        "emptyTable": "No employees added to payroll yet",
                        "info": "Showing _START_ to _END_ of _TOTAL_ employees",
                        "infoEmpty": "Showing 0 to 0 of 0 employees",
                        "search": "Quick search:",
                        "paginate": {
                            "first": "<i class='fas fa-angle-double-left'></i>",
                            "last": "<i class='fas fa-angle-double-right'></i>",
                            "next": "<i class='fas fa-angle-right'></i>",
                            "previous": "<i class='fas fa-angle-left'></i>"
                        }
                    },
                    "dom": '<"top"fl>rt<"bottom"ip>',
                    "order": [
                        [1, "asc"]
                    ]
                });
            }
        }

        // Custom Sweet Alert function
        function showSweetAlert(type, title, message) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end', // Changed from 'top-end' to 'bottom-end'
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: type,
                title: title,
                text: message
            });
        }

        // Submit button handling with consistent SweetAlert positioning
        $('#submitBtn').click(function() {
            const selectedEmployees = $('.employee-checkbox:checked');
            if (selectedEmployees.length === 0) {
                showSweetAlert('warning', 'No Selection', 'Please select at least one employee!');
                return false;
            }

            const monthYear = $('#month_year').val();
            if (!monthYear) {
                showSweetAlert('warning', 'Missing Information', 'Please select month/year!');
                return false;
            }

            // Confirm before submitting - keeping this one centered as it's a primary action
            Swal.fire({
                title: 'Confirm Payroll Assignment',
                text: `Assign payroll to ${selectedEmployees.length} employee(s) for ${monthYear}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3d5afe',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, assign payroll',
                cancelButtonText: 'Cancel',
                position: 'center' // Keep confirmation dialog centered
            }).then((result) => {
                if (result.isConfirmed) {
                    // Get all form fields for selected employees
                    const formData = new FormData();
                    formData.append('month_year', monthYear);
                    formData.append('_token', $('input[name="_token"]').val());

                    selectedEmployees.each(function() {
                        const userId = $(this).val();
                        formData.append('user_ids[]', userId);
                        formData.append(`basic_salary[${userId}]`, $(`#basic_salary_${userId}`).val());
                        formData.append(`allowance[${userId}]`, $(`#allowance_${userId}`).val());
                        formData.append(`bonus[${userId}]`, $(`#bonus_${userId}`).val());
                        formData.append(`overtime_hours[${userId}]`, $(`input[name="overtime_hours[${userId}]"]`).val());
                        formData.append(`overtime_rate[${userId}]`, $(`#overtime_rate_${userId}`).val());
                        formData.append(`reduction_salary[${userId}]`, $(`#reduction_salary_${userId}`).val());
                        formData.append(`absences[${userId}]`, $(`input[name="absences[${userId}]"]`).val());
                    });

                    // Show loading on button
                    const originalBtnText = $(this).html();
                    $(this).html('<i class="fas fa-spinner fa-spin me-2"></i> Processing...');
                    $(this).prop('disabled', true);

                    // Send AJAX request to store payroll
                    $.ajax({
                        url: "{{ route('payroll.assign.store') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            // Use standard Swal for success (centered)
                            Swal.fire({
                                title: 'Success!',
                                text: 'Payroll assigned successfully!',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false,
                                position: 'center'
                            }).then(() => {
                                window.location.href = "{{ route('payroll.assign.index') }}";
                            });
                        },
                        error: function(xhr) {
                            // Reset button
                            $('#submitBtn').html(originalBtnText);
                            $('#submitBtn').prop('disabled', false);

                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                const errorMessages = Object.values(xhr.responseJSON.errors).flat();
                                // Use standard Swal for errors (centered)
                                Swal.fire({
                                    title: 'Error!',
                                    html: errorMessages.join('<br>'),
                                    icon: 'error',
                                    position: 'center'
                                });
                            } else {
                                // Use standard Swal for errors (centered)
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Failed to assign payroll. Please try again.',
                                    icon: 'error',
                                    position: 'center'
                                });
                            }
                        }
                    });
                }
            });
        });


        // Department filter change event
        $('#department_filter').change(function() {
            // Update the employee dropdown based on the filter
            filterEmployeesDropdown();
        });

        // Position filter change event
        $('#position_filter').change(function() {
            // Update the employee dropdown based on the filter
            filterEmployeesDropdown();
        });

        // Function to filter employees dropdown based on selections
        // Function to filter employees dropdown based on selections
        function filterEmployeesDropdown() {
            const departmentId = $('#department_filter').val();
            const positionId = $('#position_filter').val();
            const monthYear = $('#month_year').val();

            if (!monthYear) return;

            // Show loading in dropdown
            $('#employee_select').html('<option value="">Loading filtered employees...</option>');

            $.ajax({
                url: "{{ route('payroll.assign.get-filtered-users') }}",
                type: "GET",
                data: {
                    month_year: monthYear,
                    department_id: departmentId,
                    position_id: positionId
                },
                success: function(response) {
                    // Store employee data for quick access
                    response.forEach(function(user) {
                        employeesData[user.id] = user;
                    });

                    // Reset and rebuild employee dropdown for multiple select
                    $('#employee_select').empty();

                    if (response.length > 0) {
                        response.forEach(function(user) {
                            // Skip if already in the table
                            if (!addedEmployees.has(parseInt(user.id))) {
                                // Just add the name without position/department
                                $('#employee_select').append(`<option value="${user.id}">${user.name}</option>`);
                            }
                        });
                    } else {
                        $('#employee_select').append('<option value="" disabled>No employees match the criteria</option>');
                    }

                    // Refresh Select2
                    $('#employee_select').trigger('change');
                },
                error: function() {
                    $('#employee_select').html('<option value="" disabled>Error loading employees</option>');
                    showSweetAlert('error', 'Error', 'Failed to load filtered employees');
                }
            });
        }



        // Initial UI setup
        updateSubmitButtonState();
    });
</script>
@endpush