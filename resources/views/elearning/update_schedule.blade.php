@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('elearning.index') }}" class="btn btn-danger rounded-pill shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="display-5 fw-bold text-warning">
                <i class="fas fa-book me-3"></i>Edit Schedule
            </h1>
            <div class="border-bottom border-warning w-25 mx-auto mt-2 mb-4"></div>
        </div>
    </div>

    <div class="container">
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header bg-gradient-primary text-white py-3">
                <h4 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Schedule Details</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('elearning.update_schedule', $schedule->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <select name="lesson_id" class="form-select" id="lesson_id" required>
                                    <option value="" disabled>Select a lesson</option>
                                    @foreach($lessons as $lesson)
                                    <option value="{{ $lesson->id }}" {{ $schedule->lesson_id == $lesson->id ? 'selected' : '' }}>
                                        {{ $lesson->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <label for="lesson_id">Lesson Title</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="date" name="startDate" class="form-control" id="startDate" value="{{ $schedule->start_date }}" required>
                                <label for="startDate">Start Date</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="date" name="endDate" class="form-control" id="endDate" value="{{ $schedule->end_date }}" required>
                                <label for="endDate">End Date</label>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4 border-0 bg-light">
                        <div class="card-header bg-gradient-info text-white">
                            <h4 class="mb-0"><i class="fas fa-users me-2"></i>Invite Employees</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Filters Section -->
                                <div class="col-md-6 border-end">
                                    <h5 class="text-primary mb-3">Filter Options</h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="positionFilter" class="form-label text-muted small text-uppercase fw-bold">Filter by Position</label>
                                            <select id="positionFilter" class="form-select form-select-sm select2" multiple>
                                                <option value="">All Positions</option>
                                                @foreach($positions as $position)
                                                <option value="{{ $position->id }}">{{ $position->position }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="departmentFilter" class="form-label text-muted small text-uppercase fw-bold">Filter by Department</label>
                                            <select id="departmentFilter" class="form-select form-select-sm select2" multiple>
                                                <option value="">All Departments</option>
                                                @foreach($departments as $department)
                                                <option value="{{ $department->id }}">{{ $department->department }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 mt-4">
                                            <button type="button" class="btn btn-warning w-100 rounded-pill" id="addAllEmployees">
                                                <i class="fas fa-users me-2"></i> Add All Filtered Employees
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Employee Selection -->
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">Employee Selection</h5>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="employees" class="form-label text-muted small text-uppercase fw-bold">Select Employees</label>
                                            <select id="employees" class="form-select select2" style="width: 100%;" multiple>
                                                @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}" data-department="{{ $employee->department }}" data-position="{{ $employee->position }}"
                                                    @if(in_array($employee->id, $invitedEmployeesPluck)) selected @endif>
                                                    {{ $employee->employee_id }} - {{ $employee->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 mt-4">
                                            <button type="button" class="btn btn-primary w-100 rounded-pill" id="addEmployees">
                                                <i class="fas fa-user-plus me-2"></i> Add Selected Employees
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invited Employees Table -->
                    <div class="card mt-4 border-0 shadow">
                        <div class="card-header bg-gradient-primary text-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Invited Employees</h5>
                                <button type="button" class="btn btn-danger btn-sm rounded-pill" id="removeAllEmployees">
                                    <i class="fas fa-trash-alt me-1"></i> Remove All
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0" id="employeeTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="px-4 py-3">Employee ID</th>
                                            <th class="px-4 py-3">Name</th>
                                            <th class="px-4 py-3">Position</th>
                                            <th class="px-4 py-3">Department</th>
                                            <th class="px-4 py-3 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="invitedEmployees">
                                        @foreach($invitedEmployees as $employee)
                                        <tr data-id="{{ $employee->id }}">
                                            <td class="px-4 py-3">{{ $employee->employee_id }}</td>
                                            <td class="px-4 py-3">{{ $employee->name }}</td>
                                            <td class="px-4 py-3">{{ $employee->position->position}}</td>
                                            <td class="px-4 py-3">{{ $employee->department->department }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <button type="button" class="btn btn-danger btn-sm rounded-pill removeEmployee" data-id="{{ $employee->id }}">
                                                    <i class="fas fa-times me-1"></i> Remove
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="invited_employees" id="invitedEmployeesInput" value="{{ implode(',', $invitedUserIds) }}">

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-success btn-lg rounded-pill shadow-sm px-5">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
<style>
    /* Custom styling */
    .card {
        transition: all 0.3s ease;
        border-radius: 10px;
    }
    
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    }
    
    .bg-gradient-info {
        background: linear-gradient(135deg, #17a2b8 0%, #0f7986 100%);
    }
    
    .btn {
        transition: all 0.3s ease;
    }
    
    .btn-primary, .btn-success, .btn-danger, .btn-warning {
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    
    .btn-primary:hover, .btn-success:hover, .btn-danger:hover, .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .select2-container .select2-selection--multiple {
        min-height: 38px;
        border-color: #ced4da;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
        padding: 2px 8px;
        border-radius: 15px;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 5px;
    }
    
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }
    
    .form-floating > .form-select {
        padding-top: 1.625rem;
        padding-bottom: 0.625rem;
    }
    
    .border-warning {
        border-width: 3px !important;
    }
</style>

@push('scripts')
<!-- Include Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('#employees').select2({
            width: '100%',
            placeholder: "Select employees",
            allowClear: true,
            theme: "classic"
        });

        $('#positionFilter').select2({
            width: '100%',
            placeholder: "All Position",
            allowClear: true,
            theme: "classic"
        });
        $('#departmentFilter').select2({
            width: '100%',
            placeholder: "All Department",
            allowClear: true,
            theme: "classic"
        });

        let invitedEmployees = @json($invitedUserIds) || [];
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
                allowClear: true,
                theme: "classic"
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
                allowClear: true,
                theme: "classic"
            });
        });

        // Remove all employees
        $('#removeAllEmployees').on('click', function() {
            console.log('asdad');
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
                            <td class="px-4 py-3">${emp.employee_id}</td>
                            <td class="px-4 py-3">${emp.name}</td>
                            <td class="px-4 py-3">${emp.position.position}</td>
                            <td class="px-4 py-3">${emp.department.department}</td>
                            <td class="px-4 py-3 text-center">
                                <button type="button" class="btn btn-danger btn-sm rounded-pill removeEmployee" data-id="${id}">
                                    <i class="fas fa-times me-1"></i> Remove
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
            $('#invitedEmployeesInput').val(invitedEmployees.length > 0 ? invitedEmployees.join(',') : '');
        }

    });
</script>
@endpush
