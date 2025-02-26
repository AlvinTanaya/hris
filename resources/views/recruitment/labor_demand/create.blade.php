@extends('layouts.app')

@section('content')
<a href="{{ route('recruitment.index') }}" class="btn btn-danger ms-2 px-5"> <i class="fas fa-arrow-left me-2"></i>Back</a>
<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px"><i class="fas fa-plus"></i> Add Labor Demand</h1>

<div class="container mt-4 mx-auto">


    <div class="card shadow-lg">
        <div class="card-body">
            <form action="{{ route('recruitment.labor.demand.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="maker_id" value="{{ Auth::user()->id }}">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="position" class="form-label">
                            <i class="fas fa-briefcase"></i> Position
                        </label>
                        <select class="form-control" id="position" name="position" required>
                            <option value="" selected disabled>Select Position</option>
                            <option value="Director" {{ old('position') == 'Director' ? 'selected' : '' }}>Director</option>
                            <option value="General Manager" {{ old('position') == 'General Manager' ? 'selected' : '' }}>General Manager</option>
                            <option value="Manager" {{ old('position') == 'Manager' ? 'selected' : '' }}>Manager</option>
                            <option value="Supervisor" {{ old('position') == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                            <option value="Staff" {{ old('position') == 'Staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="department" class="form-label">
                            <i class="fas fa-building"></i> Department
                        </label>
                        <select class="form-control" id="department" name="department" required>
                            <option value="" selected disabled>Select Department</option>
                            <option value="Director" {{ old('department') == 'Director' ? 'selected' : '' }}>
                                Director
                            </option>
                            <option value="General Manager" {{ old('department') == 'General Manager' ? 'selected' : '' }}>
                                General Manager
                            </option>
                            <option value="Human Resources" {{ old('department') == 'Human Resources' ? 'selected' : '' }}>
                                Human Resources
                            </option>
                            <option value="Finance and Accounting" {{ old('department') == 'Finance and Accounting' ? 'selected' : '' }}>
                                Finance and Accounting
                            </option>

                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="opening_date" class="form-label">
                            <i class="far fa-calendar-alt"></i> Opening Date
                        </label>
                        <input type="date" name="opening_date" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="closing_date" class="form-label">
                            <i class="far fa-calendar-times"></i> Closing Date
                        </label>
                        <input type="date" name="closing_date" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status_job" class="form-label">
                            <i class="fas fa-info-circle"></i> Job Status
                        </label>
                        <select name="status_job" id="status_job" class="form-control" required>
                            <option value="">Select Status</option>
                            <option value="Full Time">Full Time</option>
                            <option value="Part Time">Part Time</option>
                            <option value="Contract">Contract</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="qty_needed" class="form-label">
                            <i class="fas fa-users"></i> Quantity Needed
                        </label>
                        <input type="number" name="qty_needed" class="form-control" required>
                    </div>
                </div>

                <div class="row" id="length_of_working_container" style="display: none;">
                    <div class="col-md-12 mb-3">
                        <label for="length_of_working" class="form-label">
                            <i class="fas fa-clock"></i> Working Period (months)
                        </label>
                        <input type="number" name="length_of_working" id="length_of_working" class="form-control">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="education" class="form-label">
                            <i class="fas fa-graduation-cap"></i> Education
                        </label>
                        <select name="education" class="form-control" required>
                            <option value="">Select Education</option>
                            <option value="SMA">SMA/SMK</option>
                            <option value="D3">D3</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="major" class="form-label">
                            <i class="fas fa-book"></i> Major
                        </label>
                        <input type="text" name="major" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="gender" class="form-label">
                            <i class="fas fa-venus-mars"></i> Gender
                        </label>
                        <select name="gender" class="form-control" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Both">Both</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="time_work_experience" class="form-label">
                            <i class="fas fa-clock"></i> Work Experience
                        </label>
                        <select name="time_work_experience" class="form-control" required>
                            <option value="">Select Work Experience</option>
                            <option value="no_experience">No Work Experience</option>
                            <option value="1_12_months">1 to 12 Months</option>
                            <option value="1_3_years">1 to 3 Years</option>
                            <option value="3_5_years">3 to 5 Years</option>
                            <option value="5_plus_years">5+ Years</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="reason" class="form-label">
                            <i class="fas fa-comment"></i> Reason to Recruit
                        </label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="Enter reasons, each starting with '-'. E.g.&#10;- Reason 1&#10;- Reason 2"></textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="job_goal" class="form-label">
                            <i class="fas fa-bullseye"></i> Job Purpose
                        </label>
                        <textarea name="job_goal" class="form-control" rows="3" required placeholder="Enter job purposes, each starting with '-'. E.g.&#10;- Purpose 1&#10;- Purpose 2"></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="experience" class="form-label">
                            <i class="fas fa-star"></i> Experience
                        </label>
                        <textarea name="experience" class="form-control" rows="3" required placeholder="List experiences, each starting with '-'. E.g.&#10;- Experience 1&#10;- Experience 2"></textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="skills" class="form-label">
                            <i class="fas fa-tools"></i> Skills
                        </label>
                        <textarea name="skills" class="form-control" rows="3" required placeholder="List skills, each starting with '-'. E.g.&#10;- Skill 1&#10;- Skill 2"></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-success px-5">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script>
    $(document).ready(function() {
        $('#position').change(function() {
            var position = $(this).val();
            var department = $('#department');
            var departmentWrapper = department.closest('.form-group');

            // Hapus pesan sebelumnya  
            departmentWrapper.find('.text-danger').remove();

            // Reset semua opsi dan status  
            department.prop('readonly', false).val('').find('option').show();

            if (position === 'Director') {
                // Tambahkan atribut readonly dan hidden input untuk mengirim value  
                department.prop('readonly', true)
                    .val('Director')
                    .after('<input type="hidden" name="department" value="Director">');
                department.find('option:not([value="Director"])').hide();
                departmentWrapper.append('<small class="text-danger">Department dibatasi sesuai posisi</small>');
            } else if (position === 'General Manager') {
                department.prop('readonly', true)
                    .val('General Manager')
                    .after('<input type="hidden" name="department" value="General Manager">');
                department.find('option:not([value="General Manager"])').hide();
                departmentWrapper.append('<small class="text-danger">Department dibatasi sesuai posisi</small>');
            } else {
                // Hapus hidden input jika ada  
                departmentWrapper.find('input[type="hidden"][name="department"]').remove();
                department.find('option[value="Director"], option[value="General Manager"]').hide();
            }
        });

        // Optional: Tambahkan event listener untuk menghapus hidden input saat form disubmit  
        $('form').on('submit', function() {
            $(this).find('input[type="hidden"][name="department"]').prop('disabled', false);
        });


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