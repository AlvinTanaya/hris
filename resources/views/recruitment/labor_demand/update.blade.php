@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('recruitment.index') }}" class="btn btn-outline-danger rounded-pill shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Back to Recruitment
            </a>
            <h2 class="text-center fw-bold my-4">
                <span class="badge bg-warning rounded-pill px-4 py-2 shadow">
                    <i class="fas fa-pencil-alt me-2"></i>Edit Labor Demand
                </span>
            </h2>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Display validation errors -->
            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h5>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form action="{{ route('recruitment.labor.demand.update', $demand->id) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" name="maker_id" value="{{ Auth::user()->id }}">

                <div class="card border-0 shadow-lg rounded-3">
                    <div class="card-header bg-gradient-warning text-white p-3">
                        <h4 class="mb-0"><i class="fas fa-file-edit me-2"></i>Edit Labor Demand Details</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Position and Department Selection -->
                            <div class="col-md-6 col-lg-6">
                                <div class="form-floating">

                                    <select class="form-select" id="position_id" name="position_id" required>
                                        <option value="" disabled>Select Position</option>
                                        @foreach($positions as $position)
                                        <option value="{{ $position->id }}"
                                            {{ old('position_id', $demand->position_id) == $position->id ? 'selected' : '' }}>
                                            {{ $position->position }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="position_id">
                                        <i class="fas fa-briefcase text-warning me-1"></i> Position
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-6">
                                <div class="form-floating">
                                    <select class="form-select" id="department_id" name="department_id" required>
                                        <option value="" disabled>Choose Department</option>
                                        @foreach($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ (string)old('department_id', $demand->department_id) === (string)$department->id ? 'selected' : '' }}>
                                            {{ $department->department }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="department_id">
                                        <i class="fas fa-building text-warning me-1"></i> Department
                                    </label>
                                </div>
                            </div>
                            <!-- Date Selection -->
                            <div class="col-md-6 col-lg-6">
                                <div class="form-floating">
                                    <input type="date" name="opening_date" id="opening_date" class="form-control" value="{{ $demand->opening_date }}" required>
                                    <label for="opening_date">
                                        <i class="far fa-calendar-alt text-warning me-1"></i> Opening Date
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-6">
                                <div class="form-floating">
                                    <input type="date" name="closing_date" id="closing_date" class="form-control" value="{{ $demand->closing_date }}" required>
                                    <label for="closing_date">
                                        <i class="far fa-calendar-times text-warning me-1"></i> Closing Date
                                    </label>
                                </div>
                            </div>

                            <!-- Job Status and Quantity -->
                            <div class="col-md-6 col-lg-6">
                                <div class="form-floating">
                                    <select name="status_job" id="status_job" class="form-select" required>
                                        <option value="">Select Status</option>
                                        <option value="Full Time" {{ $demand->status_job == 'Full Time' ? 'selected' : '' }}>Full Time</option>
                                        <option value="Part Time" {{ $demand->status_job == 'Part Time' ? 'selected' : '' }}>Part Time</option>
                                        <option value="Contract" {{ $demand->status_job == 'Contract' ? 'selected' : '' }}>Contract</option>
                                    </select>
                                    <label for="status_job">
                                        <i class="fas fa-info-circle text-warning me-1"></i> Job Status
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-6">
                                <div class="form-floating">
                                    <input type="number" name="qty_needed" id="qty_needed" class="form-control" value="{{ $demand->qty_needed }}" required>
                                    <label for="qty_needed">
                                        <i class="fas fa-users text-warning me-1"></i> Quantity Needed
                                    </label>
                                </div>
                            </div>

                            <!-- Working Period (Conditional) -->
                            <div class="col-12" id="length_of_working_container" style="{{ in_array($demand->status_job, ['Part Time', 'Contract']) ? '' : 'display: none;' }}">
                                <div class="form-floating">
                                    <input type="number" name="length_of_working" id="length_of_working" class="form-control"
                                        value="{{ old('length_of_working', $demand->length_of_working) }}" min="1" max="60"
                                        {{ in_array($demand->status_job, ['Part Time', 'Contract']) ? 'required' : '' }}>
                                    <label for="length_of_working">
                                        <i class="fas fa-clock text-warning me-1"></i> Working Period (months)
                                    </label>
                                </div>
                            </div>

                            <!-- Education and Major -->
                            <div class="col-md-6 col-lg-6">
                                <div class="form-floating">
                                    <select name="education" id="education" class="form-select" required>
                                        <option value="">Select Education</option>
                                        <option value="SMA" {{ $demand->education == 'SMA' ? 'selected' : '' }}>SMA/SMK</option>
                                        <option value="D3" {{ $demand->education == 'D3' ? 'selected' : '' }}>D3</option>
                                        <option value="S1" {{ $demand->education == 'S1' ? 'selected' : '' }}>S1</option>
                                        <option value="S2" {{ $demand->education == 'S2' ? 'selected' : '' }}>S2</option>
                                        <option value="S3" {{ $demand->education == 'S3' ? 'selected' : '' }}>S3</option>
                                    </select>
                                    <label for="education">
                                        <i class="fas fa-graduation-cap text-warning me-1"></i> Education
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-6">
                                <div class="form-floating">
                                    <input type="text" name="major" id="major" class="form-control" value="{{ $demand->major }}" required>
                                    <label for="major">
                                        <i class="fas fa-book text-warning me-1"></i> Major
                                    </label>
                                </div>
                            </div>

                            <!-- Gender and Work Experience -->
                            <div class="col-md-6 col-lg-6">
                                <div class="form-floating">
                                    <select name="gender" id="gender" class="form-select" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male" {{ $demand->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ $demand->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Both" {{ $demand->gender == 'Both' ? 'selected' : '' }}>Both</option>
                                    </select>
                                    <label for="gender">
                                        <i class="fas fa-venus-mars text-warning me-1"></i> Gender
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-6">
                                <div class="form-floating">
                                    <select name="time_work_experience" id="time_work_experience" class="form-select" required>
                                        <option value="">Select Work Experience</option>
                                        <option value="no_experience" {{ $demand->time_work_experience == 'no_experience' ? 'selected' : '' }}>No Work Experience</option>
                                        <option value="1_12_months" {{ $demand->time_work_experience == '1_12_months' ? 'selected' : '' }}>1 to 12 Months</option>
                                        <option value="1_3_years" {{ $demand->time_work_experience == '1_3_years' ? 'selected' : '' }}>1 to 3 Years</option>
                                        <option value="3_5_years" {{ $demand->time_work_experience == '3_5_years' ? 'selected' : '' }}>3 to 5 Years</option>
                                        <option value="5_plus_years" {{ $demand->time_work_experience == '5_plus_years' ? 'selected' : '' }}>5+ Years</option>
                                    </select>
                                    <label for="time_work_experience">
                                        <i class="fas fa-clock text-warning me-1"></i> Work Experience
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Textarea Fields -->
                        <div class="row g-4 mt-3">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-clipboard-list text-warning me-2"></i>Job Requirements & Details</h5>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <textarea maxlength="255" name="reason" id="reason" class="form-control" style="height: 120px" required placeholder="Enter reasons, each starting with '-'. E.g.&#10;- Reason 1&#10;- Reason 2">{{ $demand->reason }}</textarea>
                                    <label for="reason">
                                        <i class="fas fa-comment text-warning me-1"></i> Reason to Recruit
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <textarea maxlength="255" name="job_goal" id="job_goal" class="form-control" style="height: 120px" required placeholder="Enter job purposes, each starting with '-'. E.g.&#10;- Purpose 1&#10;- Purpose 2">{{ $demand->job_goal }}</textarea>
                                    <label for="job_goal">
                                        <i class="fas fa-bullseye text-warning me-1"></i> Job Purpose
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <textarea maxlength="255" name="experience" id="experience" class="form-control" style="height: 120px" required placeholder="List experiences, each starting with '-'. E.g.&#10;- Experience 1&#10;- Experience 2">{{ $demand->experience }}</textarea>
                                    <label for="experience">
                                        <i class="fas fa-star text-warning me-1"></i> Experience
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <textarea maxlength="255" name="skills" id="skills" class="form-control" style="height: 120px" required placeholder="List skills, each starting with '-'. E.g.&#10;- Skill 1&#10;- Skill 2">{{ $demand->skills }}</textarea>
                                    <label for="skills">
                                        <i class="fas fa-tools text-warning me-1"></i> Skills
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    
                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <a href="{{ route('recruitment.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-warning rounded-pill px-5 py-2 shadow">
                        <i class="fas fa-save me-2"></i> Update Labor Demand
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<style>
    .form-floating>label {
        opacity: 0.8;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
    }

    .bg-gradient-warning {
        background: linear-gradient(45deg, #ffc107, #fd7e14);
    }

    textarea.form-control {
        padding-top: 1.625rem;
    }

    .form-floating textarea.form-control {
        height: 120px;
    }
</style>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Track if this is initial load vs user-triggered change
        var isInitialLoad = true;

        $('#position_id').change(function() {
            // Skip the reset logic on the initial load
            if (isInitialLoad) {
                isInitialLoad = false;
                return;
            }

            var positionId = $(this).val();
            var departmentSelect = $('#department_id');
            var departmentWrapper = departmentSelect.closest('.form-floating');

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
                    departmentWrapper.append('<small class="text-danger position-absolute bottom-0 mb-2">Department dibatasi sesuai posisi</small>');
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
                    departmentWrapper.append('<small class="text-danger position-absolute bottom-0 mb-2">Department dibatasi sesuai posisi</small>');
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

        // Apply position-specific department restrictions only when the user changes the position
        // We're NOT triggering the position change event on initial load anymore

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

        // Form validation
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    });
</script>

@endpush