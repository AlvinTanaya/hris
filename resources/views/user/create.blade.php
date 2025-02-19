@extends('layouts.app')

@section('content')
<style>
    #employeeTab .nav-link {
        color: white;
        font-weight: 500;
        padding: 1rem;
        transition: all 0.3s ease;
    }

    #employeeTab .nav-link.active {
        color: #0d6efd;
        border-bottom: 3px solid #0d6efd;
    }

    #employeeTab .nav-item {
        flex: 1;
        text-align: center;
        max-width: 20%;
    }
</style>
<a href="{{ route('user.index') }}" class="btn btn-danger px-5 mb-3">
    <i class="fas fa-arrow-left me-2"></i>Back
</a>

<h1 class="mb-5 text-center text-warning"><i class="far fa-plus"></i>Add Employee</h1>

<div class="container mt-1">


    <!-- Tab Navigation -->
    <ul class="nav nav-tabs" id="employeeTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="userDataTab" data-bs-toggle="tab" href="#userData" role="tab" aria-controls="userData" aria-selected="true">Employee Data</a>
        </li>

        <li class="nav-item" role="presentation">
            <a class="nav-link" id="familyDataTab" data-bs-toggle="tab" href="#familyData" role="tab" aria-controls="familyData" aria-selected="false">Family Data</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="educationTab" data-bs-toggle="tab" href="#education" role="tab" aria-controls="education" aria-selected="false">Education Data</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="workExperienceTab" data-bs-toggle="tab" href="#workExperience" role="tab" aria-controls="workExperience" aria-selected="false">Work Experience Data</a>
        </li>

    </ul>
    <form action="{{ route('user.create') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card shadow-lg border-0 rounded mt-4">

            <div class="card-body pt-1">
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



                <div class="tab-content mt-4" id="employeeTabContent">
                    <!-- Data user -->
                    <div class="tab-pane fade show active" id="userData" role="tabpanel" aria-labelledby="userDataTab">

                        <!-- Judul Data user -->
                        <h5 class="text-primary">
                            <i class="fas fa-user-tie"></i> Data user
                        </h5>
                        <!-- Garis Pemisah -->
                        <hr class="mt-4">

                        <div class="row">
                            <div class="col-md-4 mb-3"></div>

                            <div class="col-md-4 mb-3 text-center">
                                <!-- Pratinjau gambar berbentuk bulat -->
                                <div class="mb-3">
                                    <img
                                        id="image-preview"
                                        src="#"
                                        alt="Preview"
                                        class="rounded-circle border d-none"
                                        style="width: 150px; height: 150px; object-fit: cover;">
                                </div>

                                <!-- Input file untuk gambar -->
                                <input
                                    type="file"
                                    id="image-input"
                                    class="form-control"
                                    name="photo"
                                    accept="image/jpeg, image/png, image/jpg">
                                <small class="text-muted">Hanya file JPG, JPEG, atau PNG</small>
                            </div>

                            <div class="col-md-4 mb-3"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="employee_id" class="form-label">
                                    <i class="fas fa-id-badge"></i> Employee ID
                                </label>
                                <input type="text" class="form-control" id="employee_id" name="employee_id" value="{{ old('employee_id') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="join_date" class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Join Date
                                </label>
                                <input type="date" class="form-control" id="join_date" name="join_date" value="{{ old('join_date') }}" required>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user"></i> Name
                                </label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="user_status" class="form-label">
                                    <i class="fas fa-ban"></i> User Status
                                </label>
                                <select class="form-control" id="user_status" name="user_status" required>
                                    <option selected disabled>Choose Status</option>
                                    <option value="Unbanned" {{ old('user_status') == 'Unbanned' ? 'selected' : '' }}>Unbanned</option>
                                    <option value="Banned" {{ old('user_status') == 'Banned' ? 'selected' : '' }}>Banned</option>
                                </select>
                            </div>


                        </div>




                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="position" class="form-label">
                                    <i class="fas fa-briefcase"></i> Position
                                </label>
                                <select class="form-control" id="position" name="position" required>
                                    <option selected disabled>Choose position</option>
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
                                    <option selected disabled>Choose Department</option>
                                    <option value="Director" {{ old('department') == 'Director' ? 'selected' : '' }}>Director</option>
                                    <option value="General manager" {{ old('department') == 'General manager' ? 'selected' : '' }}>General manager</option>
                                    <option value="Human Resources" {{ old('department') == 'Human Resources' ? 'selected' : '' }}>Human Resources</option>
                                    <option value="Finnance and Accounting" {{ old('department') == 'Finnance and Accounting' ? 'selected' : '' }}>Finnance and Accounting</option>
                                    
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Employee Status -->
                            <div class="col-md-12 mb-3">
                                <label for="employee_status" class="form-label">
                                    <i class="fas fa-user-check"></i> Employee Status
                                </label>
                                <select class="form-control" id="employee_status" name="employee_status" required>
                                    <option selected disabled>Choose Status</option>
                                    <option value="Full Time" {{ old('employee_status') == 'Full Time' ? 'selected' : '' }}>Full Time</option>
                                    <option value="Part Time" {{ old('employee_status') == 'Part Time' ? 'selected' : '' }}>Part Time</option>
                                    <option value="Contract" {{ old('employee_status' ) == 'Contract' ? 'selected' : '' }}>Contract</option>
                                    <option value="Inactive" {{ old('employee_status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Contract Start & End Date (Hidden by Default) -->
                        <div class="row" id="contract_dates_wrapper" style="display: none;">
                            <!-- Contract Start Date -->
                            <div class="col-md-6 mb-3">
                                <label for="contract_start_date" class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Contract Start Date
                                </label>
                                <input type="date" class="form-control" id="contract_start_date" name="contract_start_date">
                            </div>

                            <!-- Contract End Date -->
                            <div class="col-md-6 mb-3">
                                <label for="contract_end_date" class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Contract End Date
                                </label>
                                <input type="date" class="form-control" id="contract_end_date" name="contract_end_date">
                            </div>
                        </div>



                        <div class="row">
                            <div class="col-md-6 mb-3">

                                <label for="bpjs_employment" class="form-label">
                                    <i class="fas fa-id-card"></i> BPJS Ketenagakerjaan
                                </label>
                                <input type="text" class="form-control" id="bpjs_employment" name="bpjs_employment" value="{{ old('bpjs_employment') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bpjs_health" class="form-label">
                                    <i class="fas fa-id-card"></i> BPJS Kesehatan
                                </label>
                                <input type="text" class="form-control" id="bpjs_health" name="bpjs_health" value="{{ old('bpjs_health') }}" required>
                            </div>
                        </div>



                        <!-- Data Personal user -->
                        <h5 class="text-primary mt-4">
                            <i class="fas fa-user-circle"></i> Personal Data
                        </h5>
                        <!-- Garis Pemisah -->
                        <hr class="mt-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ID_number" class="form-label">
                                    <i class="fas fa-id-card"></i> KTP Number
                                </label>
                                <input type="number" class="form-control" id="ID_number" name="ID_number" value="{{ old('ID_number') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="religion" class="form-label">
                                    <i class="fas fa-praying-hands"></i> Religion
                                </label>
                                <select class="form-control" id="religion" name="religion" required>
                                    <option selected disabled>Choose Religion</option>
                                    <option value="Islam" {{ old('religion') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                    <option value="Katolik" {{ old('religion') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                    <option value="Kristen" {{ old('religion') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                    <option value="Buddha" {{ old('religion') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                    <option value="Hindu" {{ old('religion') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                    <option value="Konghucu" {{ old('religion') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>



                                </select>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="birth_date" class="form-label">
                                    <i class="fas fa-calendar-day"></i> Birth Date
                                </label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="birth_place" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i> Birth Place
                                </label>
                                <input type="text" class="form-control" id="birth_place" name="birth_place" value="{{ old('birth_place') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ID_address" class="form-label">
                                    <i class="fas fa-map-pin"></i> KTP Address
                                </label>
                                <input type="text" class="form-control" id="ID_address" name="ID_address" value="{{ old('ID_address') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="domicile_address" class="form-label">
                                    <i class="fas fa-map-signs"></i> Domicile Address
                                </label>
                                <input type="text" class="form-control" id="domicile_address" name="domicile_address" value="{{ old('domicile_address') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone_number" class="form-label">
                                    <i class="fas fa-phone"></i> Phone Number
                                </label>
                                <input type="number" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">
                                    <i class="fas fa-venus-mars"></i> Gender
                                </label>
                                <select class="form-control" id="gender" name="gender" required>
                                    <option selected disabled>Choose Gender</option>

                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <!-- Blood Type -->
                            <div class="col-md-6 mb-3">
                                <label for="blood_type" class="form-label">
                                    <i class="fas fa-tint"></i> Blood Type
                                </label>
                                <select class="form-control" id="blood_type" name="blood_type" required>
                                    <option selected disabled>Choose Blood Type</option>
                                    @foreach(['A', 'B', 'AB', 'O', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $blood)
                                    <option value="{{ $blood }}">
                                        {{ $blood }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="height" class="form-label">
                                    <i class="fas fa-arrows-alt-v"></i> Height (cm)
                                </label>
                                <input type="number" class="form-control" id="height" name="height" value="{{ old('height') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="weight" class="form-label">
                                    <i class="fas fa-weight"></i> Weight (kg)
                                </label>
                                <input type="number" class="form-control" id="weight" name="weight" value="{{ old('weight') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Upload ID Card -->
                            <div class="col-md-6 mb-3">
                                <label for="id_card" class="form-label">
                                    <i class="fas fa-id-card"></i> Upload ID Card (JPG/PNG)
                                </label>
                                <input type="file" class="form-control" id="id_card" name="id_card" accept="image/jpeg, image/png" required>
                            </div>

                            <!-- Upload CV -->
                            <div class="col-md-6 mb-3">
                                <label for="cv" class="form-label">
                                    <i class="fas fa-file-alt"></i> Upload CV (PDF)
                                </label>
                                <input type="file" class="form-control" id="cv" name="cv" accept=".pdf" required>
                            </div>
                        </div>

                    </div>

                    <!-- Family Data user -->
                    <div class="tab-pane fade" id="familyData" role="tabpanel" aria-labelledby="familyDataTab">
                        <h5 class="text-primary">
                            <i class="fas fa-users me-2"></i>Family Data
                        </h5>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Relation</th>
                                    <th>Birth Date</th>
                                    <th>Birth Place</th>
                                    <th>KTP Number</th>
                                    <th>Phone Number</th>
                                    <th>Address</th>
                                    <th>Gender</th>
                                    <th>Job</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="familyMembersTable">


                                <tr>
                                    <td><input type="text" class="form-control" placeholder="Nama" name="name_family[]" value="{{ old('name_family') }}"></td>
                                    <td>
                                        <select class="form-select" name="relation[]" required>
                                            <option disabled value="">Select Relationship</option>
                                            <option value="Ayah">Ayah</option>
                                            <option value="Ibu">Ibu</option>
                                            <option value="Suami">Suami</option>
                                            <option value="Istri">Istri</option>
                                            <option value="Anak">Anak</option>
                                            <option value="Saudara">Saudara</option>
                                        </select>
                                    </td>
                                    <td><input type="date" class="form-control" placeholder="Birth Date" name="birth_date_family[]" value="{{ old('birth_date_family') }}"></td>
                                    <td><input type="text" class="form-control" placeholder="Birth Place" name="birth_place_family[]" value="{{ old('birth_place_family') }}"></td>
                                    <td><input type="text" class="form-control" placeholder="KTP Number" name="ID_number_family[]" value="{{ old('ID_number_family') }}"></td>
                                    <td><input type="text" class="form-control" placeholder="Phone Number" name="phone_number_family[]" value="{{ old('phone_number_family') }}"></td>
                                    <td><input type="text" class="form-control" placeholder="Address" name="address_family[]" value="{{ old('address_family') }}"></td>
                                    <td>
                                        <select class="form-control" name="gender_family[]">
                                            <option selected disabled>Choose Gender</option>
                                            <option value="Male" {{ old('gender_family') == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('gender_family') == 'Female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control" placeholder="Job" name="job[]" value="{{ old('job') }}"></td>
                                    <td><button type="button" class="btn btn-danger btn-sm remove-family-row">Erase</button></td>
                                </tr>


                            </tbody>
                        </table>

                        <button type="button" class="btn btn-primary" id="addFamilyMember"><i class="fas fa-plus-circle me-2"></i> Add Family Member</button>
                    </div>


                    <!-- Data Pendidikan -->
                    <div class="tab-pane fade" id="education" role="tabpanel" aria-labelledby="educationTab">
                        <h5 class="text-primary"><i class="fas fa-graduation-cap me-2"></i>Education Data</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="educationTable">
                                <thead>
                                    <tr>
                                        <th>Degree</th>
                                        <th>Educational Place</th>
                                        <th>Major</th>
                                        <th>Start Education</th>
                                        <th>End Education</th>
                                        <th>grade</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="familyMembersTable">


                                    <tr>
                                        <td>
                                            <select class="form-select" name="education_level[]">
                                                <option selected disabled> -- Choose Degree --</option>
                                                <option value="SD">SD</option>
                                                <option value="SMP">SMP</option>
                                                <option value="SMA">SMA</option>
                                                <option value="SMK">SMK</option>
                                                <option value="D3">D3</option>
                                                <option value="S1">S1</option>
                                                <option value="S2">S2</option>
                                                <option value="S3">S3</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="education_place[]" placeholder="Educational Place" value="{{ old('education_place.0') }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="major[]" placeholder="major" value="{{ old('major.0') }}">
                                        </td>
                                        <td>
                                            <input type="date" class="form-control" name="start_education[]" value="{{ old('start_education.0') }}">
                                        </td>
                                        <td>
                                            <input type="date" class="form-control" name="end_education[]" value="{{ old('end_education.0') }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="grade[]" placeholder="grade" value="{{ old('grade.0') }}">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger remove-row">Erase</button>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary mt-3" id="addRow"><i class="fas fa-plus-circle me-2"></i>Add Education Row</button>
                        </div>
                    </div>

                    <!-- Work Experience Data -->
                    <div class="tab-pane fade" id="workExperience" role="tabpanel" aria-labelledby="workExperienceTab">
                        <h5 class="text-primary"><i class="fas fa-briefcase me-2"></i>Work Experience Data</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="workExperienceTable">
                                <thead>
                                    <tr>
                                        <th>Company Name</th>
                                        <th>position</th>
                                        <th>Start Working</th>
                                        <th>End Working/th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>


                                    <tr>
                                        <td>
                                            <input type="text" class="form-control" name="company_name[]" placeholder="Company Name" value="{{ old('company_name.0') }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="position[]" placeholder="position" value="{{ old('position.0') }}">
                                        </td>
                                        <td>
                                            <input type="date" class="form-control" name="start_work[]" value="{{ old('start_work.0') }}">
                                        </td>
                                        <td>
                                            <input type="date" class="form-control" name="end_work[]" value="{{ old('end_work.0') }}">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger remove-row">Erase</button>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary mt-3" id="addWorkRow"><i class="fas fa-plus-circle me-2"></i>Add Work Experience</button>
                        </div>
                    </div>



                </div>
            </div>
        </div>


        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-success px-5"><i class="fas fa-plus"></i>Update Employee Data</button>
        </div>
    </form>
</div>

@endsection


@push('scripts')

<script>
    $(document).ready(function() {
        //photo profile
        $('#image-input').on('change', function(event) {
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-preview')
                        .attr('src', e.target.result)
                        .removeClass('d-none'); // Tampilkan gambar
                };
                reader.readAsDataURL(file);
            } else {
                $('#image-preview')
                    .attr('src', '#')
                    .addClass('d-none'); // Sembunyikan gambar
            }
        });

        // Add new family member row
        $('#addFamilyMember').on('click', function() {
            const newRow = `
                <tr>
                   
                    <td><input type="text" class="form-control" placeholder="Nama" name="name_family[]" value="{{ old('name_family') }}"></td>
                    <td>
                        <select class="form-select" name="relation[]" required>
                            <option disabled value="">Select Relationship</option>
                            <option value="Ayah">Ayah</option>
                            <option value="Ibu">Ibu</option>
                            <option value="Suami">Suami</option>
                            <option value="Istri">Istri</option>
                            <option value="Anak">Anak</option>
                            <option value="Saudara">Saudara</option>
                        </select>
                    </td>
                    <td><input type="date" class="form-control" placeholder="Birth Date" name="birth_date_family[]" value="{{ old('birth_date_family') }}"></td>
                    <td><input type="text" class="form-control" placeholder="Birth Place" name="birth_place_family[]" value="{{ old('birth_place_family') }}"></td>
                    <td><input type="text" class="form-control" placeholder="KTP Number" name="ID_number_family[]" value="{{ old('ID_number_family') }}"></td>
                    <td><input type="text" class="form-control" placeholder="Phone Number" name="phone_number_family[]" value="{{ old('phone_number_family') }}"></td>
                    <td><input type="text" class="form-control" placeholder="address" name="address_family[]" value="{{ old('address_family') }}"></td>
                    <td>
                        <select class="form-control" name="gender_family[]">
                            <option selected disabled>Choose Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </td>
                    <td><input type="text" class="form-control" placeholder="job" name="job[]" value="{{ old('job') }}"></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-family-row">Erase</button></td>
                </tr>
            `;
            $('#familyMembersTable').append(newRow);
        });

        // Remove family member row
        $('#familyMembersTable').on('click', '.remove-family-row', function() {
            $(this).closest('tr').remove();
        });


        $('#addRow').click(function() {
            const newRow = `
                <tr>
                    <td>
                        <select class="form-select" name="education_level[]">
                            <option selected disabled>-- Choose Degree --</option>
                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                            <option value="SMA">SMA</option>
                            <option value="SMK">SMK</option>
                            <option value="D3">D3</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                           
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="education_place[]" placeholder="Educational Place">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="major[]" placeholder="Major">
                    </td>
                    <td>
                        <input type="date" class="form-control" name="start_education[]">
                    </td>
                    <td>
                        <input type="date" class="form-control" name="end_education[]">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="grade[]" placeholder="Grade">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger remove-row">Erase</button>
                    </td>
                </tr>
            `;
            $('#educationTable tbody').append(newRow);
        });

        // Event delegation to handle dynamically added rows
        $('#educationTable').on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
        });


        $('#addWorkRow').click(function() {
            const newRow = `
                <tr>
                    <td>
                        <input type="text" class="form-control" name="company_name[]" placeholder="Company Name">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="position[]" placeholder="position">
                    </td>
                    <td>
                        <input type="date" class="form-control" name="start_work[]">
                    </td>
                    <td>
                        <input type="date" class="form-control" name="end_work[]">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger remove-row">Erase</button>
                    </td>
                </tr>
            `;
            $('#workExperienceTable tbody').append(newRow);
        });

        // Event delegation to handle dynamically added rows
        $('#workExperienceTable').on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
        });

        $("#employee_status").change(function() {
            toggleContractFields();
        });


    });

    function toggleContractFields() {
        let status = $("#employee_status").val();

        if (status === "Part Time" || status === "Contract") {
            $("#contract_dates_wrapper").show();
            $("#contract_start_date, #contract_end_date").attr("required", true);
        } else {
            $("#contract_dates_wrapper").hide();
            $("#contract_start_date, #contract_end_date").removeAttr("required");
        }
    }
</script>
@endpush