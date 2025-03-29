@extends('layouts.app')

@section('content')
<a href="{{ route('elearning.index') }}" class="btn btn-danger ms-2 px-5"> <i class="fas fa-arrow-left me-2"></i>Back</a>
<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px"><i class="fas fa-plus"></i> Add Schedule</h1>

<div class="container mt-4 mx-auto">

    <div class="card shadow-lg">
        <div class="card-body">
            <form action="{{ route('elearning.store_schedule') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="lesson_id" class="form-label">Lesson Title</label>
                        <select name="lesson_id" class="form-control" required>
                            <option value="" disabled selected>Select a lesson</option>
                            @foreach($lessons as $lesson)
                            <option value="{{ $lesson->id }}">{{ $lesson->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" name="startDate" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="date" name="endDate" class="form-control" required>
                    </div>
                </div>

                <h4 class="text-primary mt-4 mb-4"><i class="fas fa-users me-2"></i>Invite Employees</h4>

                <div class="row mb-3">
                    <div class="col-md-6 border-end">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="positionFilter" class="form-label">Filter by Position</label>
                                <select id="positionFilter" class="form-control select2" multiple>
                                    <option value="">All Positions</option>
                                    @foreach($positions as $position)
                                    <option value="{{ $position->id }}">{{ $position->position }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="departmentFilter" class="form-label">Filter by Department</label>
                                <select id="departmentFilter" class="form-control select2" multiple>
                                    <option value="">All Departments</option>
                                    @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->department }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-warning w-100" id="addAllEmployees">
                                    <i class="fas fa-users me-2"></i> Add All Filtered Employees
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="employees" class="form-label">Select Employees</label>
                                <select id="employees" class="form-control select2" style="width: 100%;" multiple>
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" data-department="{{ $employee->department }}" data-position="{{ $employee->position }}">{{ $employee->employee_id }} - {{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary w-100" id="addEmployees">
                                    <i class="fas fa-user-plus me-2"></i> Add Selected Employees
                                </button>
                            </div>
                        </div>
                    </div>
                </div>





                <div class="card mt-3">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Invited Employees</span>
                            <button type="button" class="btn btn-danger btn-sm" id="removeAllEmployees">
                                <i class="fas fa-trash-alt me-1"></i> Remove All
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-bordered" id="employeeTable">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Department</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="invitedEmployees">
                                <!-- Data will be displayed here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <input type="hidden" name="invited_employees" id="invitedEmployeesInput">

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                            <td><button type="button" class="btn btn-danger btn-sm removeEmployee" data-id="${id}">Remove</button></td>
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
@endsection