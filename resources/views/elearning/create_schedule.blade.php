@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('elearning.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to E-Learning
                </a>
                <h1 class="text-warning fw-bold mb-0">Add New Schedule</h1>
                <div></div> <!-- Empty div for flex spacing -->
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-lg rounded-3">
        <div class="card-header bg-primary bg-gradient text-white py-3">
            <h4 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Schedule Details</h4>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('elearning.store_schedule') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-4">
                    <!-- Lesson Selection -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-book me-2"></i>Lesson Information</h5>
                            </div>
                            <div class="card-body p-3">
                                <div class="mb-3">
                                    <label for="lesson_id" class="form-label fw-bold">Lesson Title</label>
                                    <select name="lesson_id" class="form-select form-select-lg" required>
                                        <option value="" disabled selected>Select a lesson</option>
                                        @foreach($lessons as $lesson)
                                        <option value="{{ $lesson->id }}">{{ $lesson->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="startDate" class="form-label fw-bold">Start Date</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-primary text-white">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </span>
                                                <input type="date" name="startDate" class="form-control form-control-lg" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="endDate" class="form-label fw-bold">End Date</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-primary text-white">
                                                    <i class="fas fa-calendar-check"></i>
                                                </span>
                                                <input type="date" name="endDate" class="form-control form-control-lg" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employee Selection -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Employee Enrollment</h5>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <!-- Filter Section -->
                                    <div class="col-md-6 border-end">
                                        <h6 class="text-primary fw-bold mb-3">Filter Options</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="positionFilter" class="form-label">Position Filter</label>
                                                <select id="positionFilter" class="form-select select2" multiple>
                                                    <option value="">All Positions</option>
                                                    @foreach($positions as $position)
                                                    <option value="{{ $position->id }}">{{ $position->position }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="departmentFilter" class="form-label">Department Filter</label>
                                                <select id="departmentFilter" class="form-select select2" multiple>
                                                    <option value="">All Departments</option>
                                                    @foreach($departments as $department)
                                                    <option value="{{ $department->id }}">{{ $department->department }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-warning btn-lg w-100" id="addAllEmployees">
                                                <i class="fas fa-users-cog me-2"></i> Add All Filtered Employees
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Employee Selection -->
                                    <div class="col-md-6">
                                        <h6 class="text-primary fw-bold mb-3">Employee Selection</h6>
                                        <div class="mb-3">
                                            <label for="employees" class="form-label">Select Employees</label>
                                            <select id="employees" class="form-select select2" style="width: 100%;" multiple>
                                                @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}" data-department="{{ $employee->department }}" data-position="{{ $employee->position }}">
                                                    {{ $employee->employee_id }} - {{ $employee->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-primary btn-lg w-100" id="addEmployees">
                                                <i class="fas fa-user-plus me-2"></i> Add Selected Employees
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invited Employees Table -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-header bg-success bg-gradient text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-user-check me-2"></i>Invited Employees</h5>
                                    <button type="button" class="btn btn-danger btn-sm" id="removeAllEmployees">
                                        <i class="fas fa-trash-alt me-1"></i> Remove All
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped mb-0" id="employeeTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Employee ID</th>
                                                <th>Name</th>
                                                <th>Position</th>
                                                <th>Department</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="invitedEmployees">
                                            <!-- Data will be displayed here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="invited_employees" id="invitedEmployeesInput">

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-success btn-lg px-5">
                        <i class="fas fa-save me-2"></i>Save Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection
<style>
    /* Custom Styling */
    .select2-container .select2-selection--multiple {
        min-height: 38px;
        border-color: #ced4da;
    }
    
    .select2-container .select2-selection--single {
        height: 38px;
        border-color: #ced4da;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    
    /* Table styling */
    #employeeTable th {
        font-weight: 600;
    }
    
    #employeeTable td {
        vertical-align: middle;
    }
    
    /* Card styling */
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    /* Button styling */
    .btn {
        border-radius: 5px;
        font-weight: 500;
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem;
    }
    
    /* Form controls */
    .form-control, .form-select {
        border-radius: 5px;
        padding: 0.5rem 0.75rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    /* Header styling */
    h1, h4, h5, h6 {
        font-weight: 600;
    }
    
    /* Input groups */
    .input-group-text {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
</style>


@push('scripts')
<!-- Include Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<!-- SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('#employees').select2({
            width: '100%',
            placeholder: "Select employees",
            allowClear: true
        });

        $('#positionFilter').select2({
            width: '100%',
            placeholder: "All Position",
            allowClear: true
        });
        $('#departmentFilter').select2({
            width: '100%',
            placeholder: "All Department",
            allowClear: true
        });

        let invitedEmployees = [];
        let employeeList = @json($employees);

        // Function to filter employees based on department and position
        function filterEmployees() {
            let selectedDepartments = $('#departmentFilter').val() || [];
            let selectedPositions = $('#positionFilter').val() || [];

            $('#employees option').each(function() {
                let empDept = $(this).data('department');
                let empPos = $(this).data('position');

                // Cek apakah employee sesuai dengan salah satu filter yang dipilih
                let matchDept = selectedDepartments.length === 0 || selectedDepartments.includes(empDept);
                let matchPos = selectedPositions.length === 0 || selectedPositions.includes(empPos);

                if (matchDept && matchPos) {
                    $(this).prop('disabled', false).show();
                } else {
                    $(this).prop('disabled', true).hide();
                }
            });

            $('#employees').select2('destroy').select2({
                width: '100%',
                placeholder: "Select employees",
                allowClear: true
            });
        }

        // Trigger filter setiap kali ada perubahan di dropdown
        $('#departmentFilter, #positionFilter').change(function() {
            filterEmployees();
        });


        // Add selected employees
        $('#addEmployees').on('click', function() {
            let selectedEmployees = $('#employees').val();
            if (!selectedEmployees) return;

            addEmployeesToTable(selectedEmployees);
            $('#employees').val(null).trigger('change'); // Reset dropdown selection
        });

        // Add all filtered employees
        $('#addAllEmployees').on('click', function() {
            let filteredEmployees = [];

            $('#employees option:not(:disabled)').each(function() {
                filteredEmployees.push($(this).val());
            });

            if (filteredEmployees.length === 0) return;

            addEmployeesToTable(filteredEmployees);

            // Reset filters
            $('#departmentFilter').val(null).trigger('change');
            $('#positionFilter').val(null).trigger('change');

            // Reset employee selection
            $('#employees').val(null).trigger('change');

            // Reset all options to be selectable again
            $('#employees option').each(function() {
                $(this).prop('disabled', false).show();
            });

            // Refresh Select2
            $('#employees').select2('destroy').select2({
                width: '100%',
                placeholder: "Select employees",
                allowClear: true
            });
        });


        // Remove all employees
        $('#removeAllEmployees').on('click', function() {
            if (invitedEmployees.length === 0) return;

            // SweetAlert confirmation
            Swal.fire({
                title: 'Are you sure?',
                text: 'You are about to remove all invited employees. This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove all!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    invitedEmployees = [];
                    $('#invitedEmployees').empty();
                    updateHiddenInput();
                    Swal.fire(
                        'Removed!',
                        'All invited employees have been removed.',
                        'success'
                    );
                }
            });
        });

        // Function to add employees to the table
        function addEmployeesToTable(employeeIds) {
            employeeIds.forEach(id => {
                if (!invitedEmployees.includes(id)) {
                    let emp = employeeList.find(e => e.id == id);
                    if (emp) {
                        invitedEmployees.push(id);
                        $('#invitedEmployees').append(
                            `<tr data-id="${id}">
                            <td>${emp.employee_id}</td>
                            <td>${emp.name}</td>
                            <td>${emp.position.position}</td>
                            <td>${emp.department.department}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm removeEmployee" data-id="${id}">
                                    <i class="fas fa-user-minus me-1"></i> Remove
                                </button>
                            </td>
                        </tr>`
                        );
                    }
                }
            });
            updateHiddenInput();
        }

        // Remove employee from invited list
        $(document).on('click', '.removeEmployee', function() {
            let id = $(this).data('id');
            invitedEmployees = invitedEmployees.filter(empId => empId != id);
            $(`tr[data-id="${id}"]`).remove();
            updateHiddenInput();
        });

        // Update hidden input with the list of invited employees
        function updateHiddenInput() {
            $('#invitedEmployeesInput').val(invitedEmployees.join(','));
        }
    });
</script>
@endpush