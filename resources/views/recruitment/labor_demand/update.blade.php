@extends('layouts.app')

@section('content')
<a href="{{ route('recruitment.index') }}" class="btn btn-danger ms-2 px-5"> <i class="fas fa-arrow-left me-2"></i>Back</a>
<h1 class="mb-5 text-center text-warning">
    <i class="fas fa-pencil"></i> Edit Labor Demand
</h1>
<div class="container mt-1">



    <form action="{{ route('recruitment.labor.demand.update', $demand->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="maker_id" value="{{ Auth::user()->id }}">

        <!-- Display validation errors -->
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="card shadow-lg">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="position_id" class="form-label">
                            <i class="fas fa-briefcase"></i> Position
                        </label>
                        <select class="form-control" id="position_id" name="position_id" required>
                            <option value="" selected disabled>Select Position</option>
                            @foreach($positions as $position)
                            <option value="{{ $position->id }}"
                                {{ old('position_id') == $position->id ? 'selected' : '' }}
                                {{ isset($demand) && $demand->position_id == $position->id ? 'selected' : '' }}>
                                {{ $position->position }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="department_id" class="form-label">
                            <i class="fas fa-building"></i> Department
                        </label>
                        <select class="form-control" id="department_id" name="department_id" required>
                            <option value="" selected disabled>Choose Department</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}"
                                {{ old('department_id') == $department->id ? 'selected' : '' }}
                                {{ isset($demand) && $demand->department_id == $department->id ? 'selected' : '' }}>
                                {{ $department->department }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="opening_date" class="form-label">
                            <i class="far fa-calendar-alt"></i> Opening Date
                        </label>
                        <input type="date" name="opening_date" class="form-control" value="{{ $demand->opening_date }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="closing_date" class="form-label">
                            <i class="far fa-calendar-times"></i> Closing Date
                        </label>
                        <input type="date" name="closing_date" class="form-control" value="{{ $demand->closing_date }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status_job" class="form-label">
                            <i class="fas fa-info-circle"></i> Job Status
                        </label>
                        <select name="status_job" id="status_job" class="form-control" required>
                            <option value="">Select Status</option>
                            <option value="Full Time" {{ $demand->status_job == 'Full Time' ? 'selected' : '' }}>Full Time</option>
                            <option value="Part Time" {{ $demand->status_job == 'Part Time' ? 'selected' : '' }}>Part Time</option>
                            <option value="Contract" {{ $demand->status_job == 'Contract' ? 'selected' : '' }}>Contract</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="qty_needed" class="form-label">
                            <i class="fas fa-users"></i> Quantity Needed
                        </label>
                        <input type="number" name="qty_needed" class="form-control" value="{{ $demand->qty_needed }}" required>
                    </div>
                </div>

                <div class="row" id="length_of_working_container" x-show="['Part Time', 'Contract'].includes(statusJob)">
                    <div class="col-md-12 mb-3">
                        <label for="length_of_working" class="form-label">
                            <i class="fas fa-clock me-2"></i> {{ __('Working Period (months)') }}
                        </label>
                        <input
                            type="number"
                            name="length_of_working"
                            id="length_of_working"
                            class="form-control"
                            value="{{ old('length_of_working', $demand->length_of_working) }}"
                            x-bind:required="['Part Time', 'Contract'].includes(statusJob)"
                            min="1"
                            max="60">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="education" class="form-label">
                            <i class="fas fa-graduation-cap"></i> Education
                        </label>
                        <select name="education" class="form-control" required>
                            <option value="">Select Education</option>
                            <option value="SMA" {{ $demand->education == 'SMA' ? 'selected' : '' }}>SMA/SMK</option>
                            <option value="D3" {{ $demand->education == 'D3' ? 'selected' : '' }}>D3</option>
                            <option value="S1" {{ $demand->education == 'S1' ? 'selected' : '' }}>S1</option>
                            <option value="S2" {{ $demand->education == 'S2' ? 'selected' : '' }}>S2</option>
                            <option value="S3" {{ $demand->education == 'S3' ? 'selected' : '' }}>S3</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="major" class="form-label">
                            <i class="fas fa-book"></i> Major
                        </label>
                        <input type="text" name="major" class="form-control" value="{{ $demand->major }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="gender" class="form-label">
                            <i class="fas fa-venus-mars"></i> Gender
                        </label>
                        <select name="gender" class="form-control" required>
                            <option value="">Select Gender</option>
                            <option value="Male" {{ $demand->gender == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ $demand->gender == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Both" {{ $demand->gender == 'Both' ? 'selected' : '' }}>Both</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="time_work_experience" class="form-label">
                            <i class="fas fa-clock"></i> Work Experience
                        </label>
                        <select name="time_work_experience" class="form-control" required>
                            <option value="">Select Work Experience</option>
                            <option value="no_experience" {{ $demand->time_work_experience == 'no_experience' ? 'selected' : '' }}>No Work Experience</option>
                            <option value="1_12_months" {{ $demand->time_work_experience == '1_12_months' ? 'selected' : '' }}>1 to 12 Months</option>
                            <option value="1_3_years" {{ $demand->time_work_experience == '1_3_years' ? 'selected' : '' }}>1 to 3 Years</option>
                            <option value="3_5_years" {{ $demand->time_work_experience == '3_5_years' ? 'selected' : '' }}>3 to 5 Years</option>
                            <option value="5_plus_years" {{ $demand->time_work_experience == '5_plus_years' ? 'selected' : '' }}>5+ Years</option>
                        </select>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="reason" class="form-label">
                            <i class="fas fa-comment"></i> Reason to Recruit
                        </label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="Enter reasons, each starting with '-'. E.g.&#10;- Reason 1&#10;- Reason 2">{{ $demand->reason }}</textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="job_goal" class="form-label">
                            <i class="fas fa-bullseye"></i> Job Purpose
                        </label>
                        <textarea name="job_goal" class="form-control" rows="3" required placeholder="Enter job purposes, each starting with '-'. E.g.&#10;- Purpose 1&#10;- Purpose 2">{{ $demand->job_goal }}</textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="experience" class="form-label">
                            <i class="fas fa-star"></i> Experience
                        </label>
                        <textarea name="experience" class="form-control" rows="3" required placeholder="List experiences, each starting with '-'. E.g.&#10;- Experience 1&#10;- Experience 2">{{ $demand->experience}}</textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="skills" class="form-label">
                            <i class="fas fa-tools"></i> Skills
                        </label>
                        <textarea name="skills" class="form-control" rows="3" required placeholder="List skills, each starting with '-'. E.g.&#10;- Skill 1&#10;- Skill 2">{{ $demand->skills }}</textarea>
                    </div>
                </div>


            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-success px-5">
                <i class="fas fa-edit"></i> Update
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#position_id').change(function() {
            var positionId = $(this).val();
            var departmentSelect = $('#department_id');
            var departmentWrapper = departmentSelect.closest('.form-group');

            // Remove previous messages and hidden inputs
            departmentWrapper.find('.text-danger').remove();
            departmentWrapper.find('input[type="hidden"][name="department_id"]').remove();

            // Reset department select
            departmentSelect.prop('readonly', false).val('').find('option').show();

            // Get the selected position
            var selectedPosition = $(this).find('option:selected').text().trim();

            if (selectedPosition === 'Director') {
                // Find Director department
                var directorDept = departmentSelect.find('option').filter(function() {
                    return $(this).text().trim() === 'Director';
                });

                if (directorDept.length) {
                    departmentSelect.prop('readonly', true)
                        .val(directorDept.val())
                        .after('<input type="hidden" name="department_id" value="' + directorDept.val() + '">');
                    departmentSelect.find('option').not(directorDept).hide();
                    departmentWrapper.append('<small class="text-danger">Department dibatasi sesuai posisi</small>');
                }
            } else if (selectedPosition === 'General Manager') {
                // Find General Manager department
                var gmDept = departmentSelect.find('option').filter(function() {
                    return $(this).text().trim() === 'General Manager';
                });

                if (gmDept.length) {
                    departmentSelect.prop('readonly', true)
                        .val(gmDept.val())
                        .after('<input type="hidden" name="department_id" value="' + gmDept.val() + '">');
                    departmentSelect.find('option').not(gmDept).hide();
                    departmentWrapper.append('<small class="text-danger">Department dibatasi sesuai posisi</small>');
                }
            } else {
                // Hide Director and General Manager options for other positions
                departmentSelect.find('option').each(function() {
                    var deptName = $(this).text().trim();
                    if (deptName === 'Director' || deptName === 'General Manager') {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
            }
        });

        // Optional: Enable hidden input on form submit
        $('form').on('submit', function() {
            $(this).find('input[type="hidden"][name="department_id"]').prop('disabled', false);
        });
        // Show/hide working period based on job status
        $('#status_job').on('change', function() {
            var selectedStatus = $(this).val();

            if (selectedStatus === 'Part Time' || selectedStatus === 'Contract') {
                $('#length_of_working_container').show();
                $('#length_of_working').prop('required', true);
            } else {
                $('#length_of_working_container').hide();
                $('#length_of_working').prop('required', false).val('');
            }
        });
    });
</script>
@endpush