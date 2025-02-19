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
                        <label for="lesson" class="form-label">Lesson Title</label>
                        <select name="lesson" class="form-control" required>
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

                <h4 class="text-primary mt-4"><i class="fas fa-users me-2"></i>Invite Employees</h4>
                <label for="employees" class="form-label">Select Employees</label>
                <div class="row">
                    <div class="col-md-11">
                        <select id="employees" class="form-control select2" style="flex-grow: 1;" multiple>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->employee_id }} - {{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1 ps-0">
                        <button type="button" class="btn btn-primary w-100" id="addEmployees">
                            <i class="fas fa-user-plus"></i>
                        </button>
                    </div>
                </div>




                <div class="card mt-3">
                    <div class="card-header bg-primary text-white">Invited Employees</div>
                    <div class="card-body">
                        <table class="table table-striped table-bordered" id="employeeTable">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Name</th>
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
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            placeholder: "Select employees",
            allowClear: true
        });

        let invitedEmployees = [];
        let employeeList = @json($employees);

        $('#addEmployees').on('click', function() {
            let selectedEmployees = $('#employees').val();
            if (!selectedEmployees) return;

            selectedEmployees.forEach(id => {
                if (!invitedEmployees.includes(id)) {
                    let emp = employeeList.find(e => e.id == id);
                    invitedEmployees.push(id);
                    $('#invitedEmployees').append(
                        `<tr data-id="${id}">
                            <td>${emp.employee_id}</td>
                            <td>${emp.name}</td>
                            <td><button type="button" class="btn btn-danger btn-sm removeEmployee" data-id="${id}">Remove</button></td>
                        </tr>`
                    );
                }
            });
            $('#employees').val(null).trigger('change'); // Reset dropdown selection
            updateHiddenInput();
        });

        $(document).on('click', '.removeEmployee', function() {
            let id = $(this).data('id');
            invitedEmployees = invitedEmployees.filter(empId => empId != id);
            $(`tr[data-id="${id}"]`).remove();
            updateHiddenInput();
        });

        function updateHiddenInput() {
            $('#invitedEmployeesInput').val(invitedEmployees.join(','));
        }
    });
</script>
@endpush
@endsection