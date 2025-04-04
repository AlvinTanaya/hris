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

    .delete-bank-row {
        height: 38px;
        /* Match height with other buttons */
        margin-bottom: 0;
        /* Remove bottom margin */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Remove extra space */
    .bank-row {
        display: flex;
        align-items: flex-end;
        /* Align all elements to bottom */
        margin-bottom: 1rem;
    }

    /* Ensure consistent sizing */
    .bank-row .col-md-2 {
        display: flex;
        align-items: flex-end;
    }

    /* Make sure button fills available space */
    .delete-bank-row {
        width: 100%;
    }
</style>
<a href="{{ route('user.employees.index') }}" class="btn btn-danger px-5 mb-3">
    <i class="fas fa-arrow-left me-2"></i>Back
</a>

<h1 class="mb-5 text-center text-warning"><i class="far fa-plus"></i> Add Employee</h1>

<div class="container mt-1">


    <!-- Tab Navigation -->
    <ul class="nav nav-tabs" id="employeeTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="userDataTab" data-bs-toggle="tab" href="#userData" role="tab" aria-controls="userData" aria-selected="true">Employee</a>
        </li>

        <li class="nav-item" role="presentation">
            <a class="nav-link" id="familyDataTab" data-bs-toggle="tab" href="#familyData" role="tab" aria-controls="familyData" aria-selected="false">Family</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="languageTab" data-bs-toggle="tab" href="#language" role="tab" aria-controls="language" aria-selected="false">Language</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="educationTab" data-bs-toggle="tab" href="#education" role="tab" aria-controls="education" aria-selected="false">Education</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="trainingTab" data-bs-toggle="tab" href="#training" role="tab" aria-controls="training" aria-selected="false">Training</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="organizationTab" data-bs-toggle="tab" href="#organization" role="tab" aria-controls="organization" aria-selected="false">organization</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="workExperienceTab" data-bs-toggle="tab" href="#workExperience" role="tab" aria-controls="workExperience" aria-selected="false">Work Experience</a>
        </li>

    </ul>
    <form action="{{ route('user.employees.store') }}" method="POST" enctype="multipart/form-data">
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
                                <small class="text-muted text-centers">Profile Picture</small>
                                <div class="mb-3">
                                    <img
                                        id="image-preview"
                                        src="#"
                                        alt="Preview"
                                        class="rounded-circle border d-none border-primary border-3"
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
                                <label for="name" class="form-label">
                                    <i class="fas fa-user"></i> Name
                                </label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
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
                                <label for="position_id" class="form-label">
                                    <i class="fas fa-briefcase"></i> Position
                                </label>
                                <select class="form-control" id="position_id" name="position_id" required>
                                    <option selected disabled value="">Choose position</option>
                                    @foreach($positions as $position)
                                    <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
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
                                    <option selected disabled value="">Choose Department</option>
                                    @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->department }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="row">

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

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">
                                    <i class="fa-solid fa-ring"></i> Status
                                </label>
                                <select class="form-control" id="status" name="status" required>
                                    <option selected disabled>Choose Status</option>
                                    <option value="TK/0">TK/0</option>
                                    <option value="TK/1">TK/1</option>
                                    <option value="TK/2">TK/2</option>
                                    <option value="TK/3">TK/3</option>
                                    <option value="K/1">K/1</option>
                                    <option value="K/2">K/2</option>
                                    <option value="K/3">K/3</option>
                                </select>
                            </div>
                        </div>


                        <div class="row">
                            <!-- Employee Status -->
                            <div class="col-md-6 mb-3">
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
                            <div class="col-md-6 mb-3">
                                <label for="npwp" class="form-label">
                                    <i class="fa-solid fa-credit-card"></i> NPWP
                                </label>
                                <input type="text" class="form-control" id="npwp" name="npwp">
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
                        <!-- Bank Information Card -->
                        <!-- Bank Information Card -->

                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fa-solid fa-university"></i> Bank Information</h5>
                            </div>
                            <div class="card-body">
                                <div id="bank-container">

                                    <div class="row bank-row mb-3">
                                        <div class="col-md-6">
                                            <label for="bank_name_0" class="form-label"><i class="fa-solid fa-piggy-bank"></i> Bank Name</label>
                                            <select class="form-control bank-name-select" id="bank_name_0" name="bank_name[]" required>
                                                <option value="" selected disabled>Select Bank</option>
                                                <option value="Bank Central Asia (BCA)">Bank Central Asia (BCA)</option>
                                                <option value="Bank Mandiri">Bank Mandiri</option>
                                                <option value="Bank Rakyat Indonesia (BRI)">Bank Rakyat Indonesia (BRI)</option>
                                                <option value="Bank Negara Indonesia (BNI)">Bank Negara Indonesia (BNI)</option>
                                                <option value="Bank CIMB Niaga">Bank CIMB Niaga</option>
                                                <option value="Bank Tabungan Negara (BTN)">Bank Tabungan Negara (BTN)</option>
                                                <option value="Bank Danamon">Bank Danamon</option>
                                                <option value="Bank Permata">Bank Permata</option>
                                                <option value="Bank Panin">Bank Panin</option>
                                                <option value="Bank OCBC NISP">Bank OCBC NISP</option>
                                                <option value="Bank Maybank Indonesia">Bank Maybank Indonesia</option>
                                                <option value="Bank Mega">Bank Mega</option>
                                                <option value="Bank Bukopin">Bank Bukopin</option>
                                                <option value="Bank Sinarmas">Bank Sinarmas</option>
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <label for="bank_number_0" class="form-label">
                                                <i class="fa-solid fa-credit-card"></i> Bank Number</label>
                                            <input type="number" class="form-control bank-number-input" id="bank_number_0" name="bank_number[]" required>
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="button" class="btn btn-danger btn-block delete-bank-row"><i class="fa-solid fa-trash"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Button to add new bank row -->
                                <div class="mt-5 d-flex justify-content-end">
                                    <button type="button" id="add-bank-btn" class="btn btn-success">
                                        <i class="fa-solid fa-plus"></i> Add Bank
                                    </button>
                                </div>
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
                                <label class="form-label"><i class="fa-solid fa-location-dot"></i> Distance Between Domicile Address to Company Location</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="distance" id="distance" min="0" max="30" step="0.01" required>
                                    <span class="input-group-text">KM</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone_number" class="form-label">
                                    <i class="fas fa-phone"></i> Phone Number
                                </label>
                                <input type="tel" class="form-control" id="phone_number" name="phone_number"
                                    pattern="08[0-9]{9,12}"
                                    maxlength="14"
                                    minlength="11"
                                    placeholder="Example: 081234567890"
                                    required>

                                <small class="text-muted">Enter a mobile number starting with 08 (11-14 digits)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact" class="form-label"><i class="fa-solid fa-phone"></i> Emergency Contact</label>
                                <input type="tel" class="form-control" id="emergency_contact" name="emergency_contact"
                                    pattern="08[0-9]{9,12}"
                                    maxlength="14"
                                    minlength="11"
                                    placeholder="Example: 081234567890"
                                    required>
                                <small class="text-muted">Enter a mobile number starting with 08 (11-14 digits)</small>
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
                                    @foreach(['A', 'B', 'AB', 'O',] as $blood)
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

                        <div class="form-section mt-2 mb-4">
                            <h3 class="section-title">Driving License</h3>
                            <div class="row">
                                @php
                                // Daftar SIM
                                $licenses = [
                                'A' => 'SIM A (Car)',
                                'B' => 'SIM B (Commercial Car)',
                                'C' => 'SIM C (Motorcycle)'
                                ];
                                @endphp

                                @foreach ($licenses as $key => $label)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input
                                            class="form-check-input license-checkbox"
                                            type="checkbox"
                                            id="hasLicense{{ $key }}"
                                            name="sim[]"
                                            value="{{ $key }}">
                                        <label class="form-check-label" for="hasLicense{{ $key }}">{{ $label }}</label>
                                    </div>
                                    <input
                                        type="text"
                                        class="form-control license-number mt-2"
                                        name="sim_number[{{ $key }}]"
                                        placeholder="License number (if applicable)"
                                        disabled>
                                </div>
                                @endforeach

                                <!-- No License Option -->
                                <div class="col-12 mt-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="noLicense" name="no_license">
                                        <label class="form-check-label fw-bold" for="noLicense">I do not have a driving license</label>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <!-- Upload ID Card -->
                            <div class="col-md-4 mb-3">
                                <label for="id_card" class="form-label">
                                    <i class="fas fa-id-card"></i> Upload ID Card (JPG/PNG)
                                </label>
                                <input type="file" class="form-control" id="id_card" name="id_card" accept="image/jpeg, image/png">
                            </div>

                            <!-- Upload CV -->
                            <div class="col-md-4 mb-3">
                                <label for="cv" class="form-label">
                                    <i class="fas fa-file-alt"></i> Upload CV (PDF)
                                </label>
                                <input type="file" class="form-control" id="cv" name="cv" accept=".pdf">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="achievement" class="form-label">
                                    <i class="fas fa-file-alt"></i> Upload Achievement (PDF)
                                </label>
                                <input type="file" class="form-control" id="achievement" name="achievement" accept=".pdf">
                            </div>
                        </div>

                    </div>

                    <!-- Family Data user -->
                    <div class="tab-pane fade" id="familyData" role="tabpanel" aria-labelledby="familyDataTab">
                        <h5 class="text-primary">
                            <i class="fas fa-users me-2"></i>Family Data
                        </h5>

                        <div id="familyMembersContainer" class="mt-4">



                        </div>

                        <button type="button" class="btn btn-primary" id="addFamilyMember">
                            <i class="fas fa-plus-circle me-2"></i> Add Family Member
                        </button>
                    </div>


                    <!-- Data Pendidikan -->
                    <div class="tab-pane fade" id="education" role="tabpanel" aria-labelledby="educationTab">
                        <h5 class="text-primary">
                            <i class="fas fa-graduation-cap me-2"></i>Education Data
                        </h5>

                        <div id="educationContainer" class="mt-4">



                        </div>

                        <button type="button" class="btn btn-primary" id="addRow">
                            <i class="fas fa-plus-circle me-2"></i> Add Education
                        </button>
                    </div>



                    <!-- Work Experience Data -->
                    <div class="tab-pane fade" id="workExperience" role="tabpanel" aria-labelledby="workExperienceTab">
                        <h5 class="text-primary">
                            <i class="fas fa-briefcase me-2"></i>Work Experience Data
                        </h5>

                        <div id="workExperienceContainer" class="mt-4">



                        </div>

                        <button type="button" class="btn btn-primary" id="addWorkRow">
                            <i class="fas fa-plus-circle me-2"></i> Add Work Experience
                        </button>
                    </div>

                    <!-- Language Tab -->
                    <div class="tab-pane fade" id="language" role="tabpanel" aria-labelledby="languageTab">
                        <h5 class="text-primary">
                            <i class="fas fa-language me-2"></i>Language Data
                        </h5>

                        <div id="languageContainer" class="mt-4">



                        </div>

                        <button type="button" class="btn btn-primary" id="addLanguage">
                            <i class="fas fa-plus-circle me-2"></i> Add Language
                        </button>
                    </div>

                    <!-- Training Tab -->
                    <div class="tab-pane fade" id="training" role="tabpanel" aria-labelledby="trainingTab">
                        <h5 class="text-primary">
                            <i class="fas fa-certificate me-2"></i>Training Data
                        </h5>

                        <div id="trainingContainer" class="mt-4">


                        </div>

                        <button type="button" class="btn btn-primary" id="addTraining">
                            <i class="fas fa-plus-circle me-2"></i> Add Training
                        </button>
                    </div>

                    <!-- Organization Tab -->
                    <div class="tab-pane fade" id="organization" role="tabpanel" aria-labelledby="organizationTab">
                        <h5 class="text-primary">
                            <i class="fas fa-users me-2"></i>Organization Data
                        </h5>

                        <div id="organizationContainer" class="mt-4">


                        </div>

                        <button type="button" class="btn btn-primary" id="addOrganization">
                            <i class="fas fa-plus-circle me-2"></i> Add Organization
                        </button>
                    </div>


                </div>
            </div>
        </div>


        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-success px-5"><i class="fas fa-plus"></i> Add Employee Data</button>
        </div>
    </form>
</div>

@endsection


@push('scripts')

<script>
    function updateFamilyNumbers() {
        $('.family-card').each(function(index) {
            $(this).find('.family-index').text(`Family #${index + 1}`);
        });
    }

    function updateEducationNumbers() {
        $('.education-card').each(function(index) {
            $(this).find('.education-index').text(`Education #${index + 1}`);
        });
    }

    function updateWorkNumbers() {
        $('.work-card').each(function(index) {
            $(this).find('.work-index').text(`Work Experience #${index + 1}`);
        });
    }

    // Function to update language card indices
    function updateLanguageIndices() {
        $('.language-card').each(function(index) {
            $(this).find('.language-index').text(`Language #${index + 1}`);
        });
    }

    // Function to update training card indices
    function updateTrainingIndices() {
        $('.training-card').each(function(index) {
            $(this).find('.training-index').text(`Training #${index + 1}`);
        });
    }

    // Function to update organization card indices
    function updateOrganizationIndices() {
        $('.organization-card').each(function(index) {
            $(this).find('.organization-index').text(`Organization #${index + 1}`);
        });
    }


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



    function checkNoLicense() {
        let anyChecked = $(".license-checkbox:checked").length > 0;
        $("#noLicense").prop("checked", !anyChecked);
    }






    function loadProvinces() {
        $.ajax({
            url: 'https://alamat.thecloudalert.com/api/provinsi/get/',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.result) {
                    var options = '<option value="" disabled selected>Select Province</option>';
                    $.each(response.result, function(i, province) {
                        options += `<option value="${province.text}" data-id="${province.id}">${province.text}</option>`; // Pakai text sebagai value & simpan ID di data-id
                    });

                    $('.province-dropdown').html(options);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading provinces:', error);
            }
        });
    }

    // Function to load cities based on selected province ID
    function loadCities(provinceId, cityDropdownId) {
        $.ajax({
            url: 'https://alamat.thecloudalert.com/api/kabkota/get/',
            type: 'GET',
            data: {
                d_provinsi_id: provinceId // Kirim ID ke API
            },
            dataType: 'json',
            success: function(response) {
                if (response.result) {
                    var options = '<option value="" disabled selected>Select City</option>';
                    $.each(response.result, function(i, city) {
                        options += `<option value="${city.text}">${city.text}</option>`; // Tetap pakai text sebagai value
                    });

                    $('#' + cityDropdownId).html(options);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading cities:', error);
            }
        });
    }


    // Update the language select options
    function updateLanguageOptions() {
        // Get all language dropdowns
        $('.language-select').each(function(index) {
            const currentValue = $(this).val();

            // Reset options
            $(this).find('option:not(:first)').remove();

            // Add available options that are not selected elsewhere
            const availableLanguages = [{
                    value: 'Indonesian',
                    text: 'Indonesian'
                },
                {
                    value: 'English',
                    text: 'English'
                },
                {
                    value: 'Mandarin',
                    text: 'Mandarin'
                },
                {
                    value: 'Japanese',
                    text: 'Japanese'
                },
                {
                    value: 'Other',
                    text: 'Other'
                }
            ];

            availableLanguages.forEach(function(lang) {
                // Add if it's the current selection or not selected elsewhere
                if (lang.value === currentValue || !selectedLanguages.includes(lang.value)) {
                    $(this).append(`<option value="${lang.value}">${lang.text}</option>`);
                }
            }, this);

            // Set back the current value
            if (currentValue) {
                $(this).val(currentValue);
            }

            // Toggle "Other" text input visibility when needed
            toggleOtherLanguageInput($(this));
        });
    }

    // Function to toggle the "Other" language input field
    function toggleOtherLanguageInput(selectElement) {
        const otherInput = selectElement.closest('.card-body').find('.other-language');
        if (selectElement.val() === 'Other') {
            otherInput.removeClass('d-none');
        } else {
            otherInput.addClass('d-none');
        }
    }

    // Counter untuk ID unik
    var rowCount = 1;
    let selectedLanguages = [];

    $(document).ready(function() {

        $('form').on('submit', function(e) {
            // Check for any incomplete bank rows
            let hasError = false;
            $('.bank-row').each(function() {
                const bankName = $(this).find('.bank-name-select').val();
                const bankNumber = $(this).find('.bank-number-input').val();

                // If one field is filled but the other is empty
                if ((bankName && !bankNumber) || (!bankName && bankNumber)) {
                    hasError = true;

                    // Mark empty fields
                    if (!bankName) $(this).find('.bank-name-select').addClass('is-invalid');
                    if (!bankNumber) $(this).find('.bank-number-input').addClass('is-invalid');
                }
            });

            // Prevent form submission if errors exist
            if (hasError) {
                e.preventDefault();
                alert('Please complete both Bank Name and Bank Number for all entries.');
                // Scroll to bank section
                $('html, body').animate({
                    scrollTop: $('#bank-container').offset().top - 50
                }, 300);
            }
        });

        // Add new bank row
        $('#add-bank-btn').on('click', function() {
            // Get the count for generating a new ID
            const newIndex = $('.bank-row').length;

            // Create a new row with proper IDs
            const bankOptions = `
                <option value="" selected disabled>Select Bank</option>
                <option value="Bank Central Asia (BCA)">Bank Central Asia (BCA)</option>
                <option value="Bank Mandiri">Bank Mandiri</option>
                <option value="Bank Rakyat Indonesia (BRI)">Bank Rakyat Indonesia (BRI)</option>
                <option value="Bank Negara Indonesia (BNI)">Bank Negara Indonesia (BNI)</option>
                <option value="Bank CIMB Niaga">Bank CIMB Niaga</option>
                <option value="Bank Tabungan Negara (BTN)">Bank Tabungan Negara (BTN)</option>
                <option value="Bank Danamon">Bank Danamon</option>
                <option value="Bank Permata">Bank Permata</option>
                <option value="Bank Panin">Bank Panin</option>
                <option value="Bank OCBC NISP">Bank OCBC NISP</option>
                <option value="Bank Maybank Indonesia">Bank Maybank Indonesia</option>
                <option value="Bank Mega">Bank Mega</option>
                <option value="Bank Bukopin">Bank Bukopin</option>
                <option value="Bank Sinarmas">Bank Sinarmas</option>
            `;

            const newRow = `
                <div class="row bank-row mb-3">
                    <div class="col-md-6">
                        <label for="bank_name_${newIndex}" class="form-label"><i class="fa-solid fa-piggy-bank"></i> Bank Name</label>
                        <select class="form-control bank-name-select" id="bank_name_${newIndex}" name="bank_name[]" required>
                            ${bankOptions}
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="bank_number_${newIndex}" class="form-label">
                            <i class="fa-solid fa-credit-card"></i> Bank Number</label>
                        <input type="number" class="form-control bank-number-input" id="bank_number_${newIndex}" name="bank_number[]" required>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-block delete-bank-row"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
            `;

            // Add the new row to the container
            $('#bank-container').append(newRow);
        });

        // Delete bank row
        $(document).on('click', '.delete-bank-row', function() {
            if ($('.bank-row').length > 1) {
                $(this).closest('.bank-row').remove();
            } else {
                // Clear fields if this is the last row
                $(this).closest('.bank-row').find('select, input').val('').removeClass('is-invalid');
            }
        });

        // Clear validation errors when input changes
        $(document).on('change input', '.bank-name-select, .bank-number-input', function() {
            $(this).removeClass('is-invalid');
        });




        $('.language-select').on('change', function() {
            toggleOtherLanguageInput($(this));
        });

        // Initialize existing fields
        $('.language-select').each(function() {
            toggleOtherLanguageInput($(this));
        });

        updateLanguageOptions();

        loadProvinces();


        // Function to load provinces for all dropdowns
        $(document).on('change', '.province-dropdown', function() {
            var provinceText = $(this).val(); // Ini tetap text
            var provinceId = $(this).find(':selected').data('id'); // Ambil ID dari data-id
            var cityDropdownId = $(this).closest('.row').find('.city-dropdown').attr('id');

            console.log("Selected Province:", provinceText, "| Province ID:", provinceId);

            if (provinceId) {
                loadCities(provinceId, cityDropdownId);
            }
        });


        $('#position_id').change(function() {
            var positionId = $(this).val();
            var positionText = $(this).find('option:selected').text().trim();
            var departmentSelect = $('#department_id');
            var departmentWrapper = departmentSelect.closest('.form-group');

            // Reset department select
            departmentWrapper.find('.text-danger').remove();
            departmentWrapper.find('input[type="hidden"][name="department_id"]').remove();
            departmentSelect.prop('disabled', false).find('option').show();

            if (positionText === 'Director') {
                // Find Director department
                var directorOption = departmentSelect.find('option').filter(function() {
                    return $(this).text().trim() === 'Director';
                }).first();

                if (directorOption.length) {
                    departmentSelect.val(directorOption.val()).prop('disabled', true);
                    departmentWrapper.append('<input type="hidden" name="department_id" value="' + directorOption.val() + '">');
                    departmentWrapper.append('<small class="text-danger">Department automatically set for Director</small>');
                }
            } else if (positionText === 'General Manager') {
                // Find General Manager department
                var gmOption = departmentSelect.find('option').filter(function() {
                    return $(this).text().trim() === 'General Manager';
                }).first();

                if (gmOption.length) {
                    departmentSelect.val(gmOption.val()).prop('disabled', true);
                    departmentWrapper.append('<input type="hidden" name="department_id" value="' + gmOption.val() + '">');
                    departmentWrapper.append('<small class="text-danger">Department automatically set for General Manager</small>');
                }
            } else {
                // For other positions, hide Director and General Manager options
                departmentSelect.find('option').each(function() {
                    var deptText = $(this).text().trim();
                    if (deptText === 'Director' || deptText === 'General Manager') {
                        $(this).hide();
                    }
                });
            }
        });

        // Ensure hidden field is submitted
        $('form').on('submit', function() {
            $(this).find('input[type="hidden"][name="department_id"]').prop('disabled', false);
        });


        $(document).on("input", ".list-textarea", function() {
            let lines = $(this).val().split("\n");
            for (let i = 0; i < lines.length; i++) {
                if (lines[i] && !lines[i].startsWith("- ")) {
                    lines[i] = "- " + lines[i].trim();
                }
            }
            $(this).val(lines.join("\n"));
        });

        $(document).on("keydown", ".list-textarea", function(event) {
            let cursorPos = this.selectionStart;
            let value = $(this).val();
            let lines = value.split("\n");
            let currentLineIndex = value.substr(0, cursorPos).split("\n").length - 1;
            let currentLine = lines[currentLineIndex] || "";

            // Backspace: Jika kursor ada di awal baris yang hanya berisi "- ", hapus barisnya
            if (event.key === "Backspace" && currentLine.trim() === "-") {
                event.preventDefault();
                lines.splice(currentLineIndex, 1); // Hapus baris kosong
                $(this).val(lines.join("\n"));
                this.setSelectionRange(cursorPos - 2, cursorPos - 2); // Pindah kursor mundur
            }

            // Enter: Tambah baris baru dengan "- "
            if (event.key === "Enter") {
                event.preventDefault();
                let newText = value + "\n- ";
                $(this).val(newText);
                this.setSelectionRange(newText.length, newText.length);
            }
        });


        // Saat checkbox SIM diubah
        $(".license-checkbox").change(function() {
            let inputField = $(this).closest('.col-md-4').find('.license-number');
            inputField.prop("disabled", !$(this).is(":checked"));
            checkNoLicense();
        });

        // Jika "I do not have a driving license" dicentang
        $("#noLicense").change(function() {
            if ($(this).is(":checked")) {
                $(".license-checkbox").prop("checked", false).trigger("change");
            }
        });


        // Default profile image
        var defaultImage = "{{ asset('storage/default_profile.png') }}";
        var currentImage = null;

        if (!currentImage) {
            $('#image-preview').attr('src', defaultImage).removeClass('d-none');
        }

        $('#image-input').on('change', function(event) {
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-preview')
                        .attr('src', e.target.result)
                        .removeClass('d-none'); // Tampilkan gambar yang diupload
                };
                reader.readAsDataURL(file);
            } else {
                $('#image-preview')
                    .attr('src', defaultImage)
                    .removeClass('d-none'); // Kembali ke gambar default jika tidak ada upload
            }
        });

        $("#employee_status").change(function() {
            toggleContractFields();
        });



        // Tambahkan Keluarga Baru
        $('#addFamilyMember').on('click', function() {
            const newFamilyCard = `
            <div class="card mb-3 family-card">
                    <div class="card-header bg-primary text-white">
                        <span class="family-index fw-bold">Family #1</span>
                    </div>
                    <div class="card-body">
                       
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" placeholder="Name" name="name_family[]" value="">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Relationship</label>
                                <select class="form-select" name="relation[]" required>
                                    <option disabled value="" disabled>Select Relationship</option>
                                    <option value="Father">Father</option>
                                    <option value="Mother">Mother</option>
                                    <option value="Husband">Husband</option>
                                    <option value="Wife">Wife</option>
                                    <option value="Child">Child</option>
                                    <option value="Sibling">Sibling</option>

                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Birth Date</label>
                                <input type="date" class="form-control" placeholder="Birth Date" name="birth_date_family[]" value="">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Birth Place</label>
                                <input type="text" class="form-control" placeholder="Birth Place" name="birth_place_family[]" value="">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">KTP Number</label>
                                <input type="text" class="form-control" placeholder="KTP Number" name="ID_number_family[]" value="">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" placeholder="Phone Number" name="phone_number_family[]" value="">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" placeholder="Address" name="address_family[]" value="">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Gender</label>
                                <select class="form-control" name="gender_family[]">
                                    <option selected disabled>Choose Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Job</label>
                                <input type="text" class="form-control" placeholder="Job" name="job[]" value="">
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button type="button" class="btn btn-danger btn-sm remove-family-card">
                                <i class="fas fa-trash me-1"></i>Remove
                            </button>
                        </div>
                    </div>
                </div>
            `;
            $('#familyMembersContainer').append(newFamilyCard);
            updateFamilyNumbers(); // Perbarui nomor urut setelah menambahkan kartu baru
        });

        // Hapus Kartu Keluarga
        $(document).on('click', '.remove-family-card', function() {
            $(this).closest('.family-card').remove();
            updateFamilyNumbers(); // Perbarui nomor urut setelah menghapus kartu
        });


        //education
        $('#addRow').on('click', function() {
            const newEducationCard = `
        <div class="card mb-3 education-card">
            <div class="card-header bg-primary text-white">
                <span class="education-index fw-bold">Education #1</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Degree</label>
                        <select class="form-select education-level" name="education_level[]">
                            <option selected disabled> -- Choose Degree --</option>
                    
                            <option value="SMA">SMA</option>
                            <option value="SMK">SMK</option>
                            <option value="D3">D3</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Educational Place</label>
                        <input type="text" class="form-control" name="education_place[]" placeholder="Educational Place">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Province</label>
                        <select class="form-select province-dropdown" name="education_province[]" required>
                            <option value="" disabled selected>Select Province</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City</label>
                        <select class="form-select city-dropdown" id="educationCity${$('.education-card').length + 1}" name="education_city[]" required>
                            <option value="" disabled selected>Select City</option>
                        </select>
                    </div>
                  
                    <div class="col-md-6">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control education-start-date" name="start_education[]" value="">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control education-end-date" name="end_education[]" value="">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Major</label>
                        <input type="text" class="form-control" name="educational_major[]" placeholder="Major">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Grade</label>
                        <input type="text" class="form-control education-grade" name="grade[]" placeholder="Grade" value="">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Certificate</label>
                        <input type="file" class="form-control" name="education_certificate[]" accept="image/*">
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button type="button" class="btn btn-danger btn-sm remove-education-card">
                        <i class="fas fa-trash me-1"></i>Remove
                    </button>
                </div>
            </div>
        </div>
    `;

            $('#educationContainer').append(newEducationCard);
            updateEducationNumbers();
            loadProvinces();
        });

        // Add validation for grade based on education level
        $(document).on('change', '.education-level', function() {
            const level = $(this).val();
            const gradeInput = $(this).closest('.row').find('.education-grade');

            if (level === 'SMP' || level === 'SMA' || level === 'SMK') {
                gradeInput.attr('placeholder', 'Grade (0-100)');
                gradeInput.data('type', 'score');
            } else {
                gradeInput.attr('placeholder', 'GPA (0-4)');
                gradeInput.data('type', 'gpa');
            }
        });

        // Validate grade input
        $(document).on('input', '.education-grade', function() {
            const value = parseFloat($(this).val());
            const type = $(this).data('type');

            if (type === 'score') {
                if (value < 0 || value > 100 || isNaN(value)) {
                    $(this).addClass('is-invalid');
                    if (!$(this).next('.invalid-feedback').length) {
                        $(this).after('<div class="invalid-feedback">Score must be between 0 and 100</div>');
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').remove();
                }
            } else if (type === 'gpa') {
                if (value < 0 || value > 4 || isNaN(value)) {
                    $(this).addClass('is-invalid');
                    if (!$(this).next('.invalid-feedback').length) {
                        $(this).after('<div class="invalid-feedback">GPA must be between 0 and 4</div>');
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').remove();
                }
            }
        });
        // Validasi tanggal saat pengguna mengubah End Date
        $(document).on('change', '.education-end-date', function() {
            const startDate = $(this).closest('.row').find('.education-start-date').val();
            const endDate = $(this).val();

            if (startDate && endDate && new Date(endDate) <= new Date(startDate)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Date',
                    text: 'End date must be after start date!',
                });
                $(this).val('');
            }
        });


        // Remove education card
        $(document).on('click', '.remove-education-card', function() {
            $(this).closest('.education-card').remove();
            updateEducationNumbers();

        });


        $('#addWorkRow').click(function() {
            console.log('asadas');
            const newRow = `
                        <div class="card mb-3 work-card">
                                <div class="card-header bg-primary text-white">
                                    <span class="work-index fw-bold">Work Experience #1</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Company Name</label>
                                            <input type="text" class="form-control" name="company_name[]" placeholder="Company Name" value="{{ old('company_name.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Position</label>
                                            <input type="text" class="form-control" name="position[]" placeholder="Position" value="{{ old('position.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Start Date</label>
                                            <input type="date" class="form-control work-start-date" name="start_work[]" value="{{ old('start_work.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">End Date</label>
                                            <input type="date" class="form-control work-end-date" name="end_work[]" value="{{ old('end_work.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Company Address</label>
                                            <input type="text" class="form-control" name="company_address[]" placeholder="Company Address" value="{{ old('company_address.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Company Phone</label>
                                            <input type="text" class="form-control" name="company_phone[]" placeholder="Company Phone" value="{{ old('company_phone.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Salary</label>
                                            <input type="text" class="form-control" name="salary[]" placeholder="Salary" value="{{ old('salary.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Supervisor Name</label>
                                            <input type="text" class="form-control" name="supervisor_name[]" placeholder="Supervisor Name" value="{{ old('supervisor_name.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Supervisor Phone</label>
                                            <input type="text" class="form-control" name="supervisor_phone[]" placeholder="Supervisor Phone" value="{{ old('supervisor_phone.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Job Description</label>
                                            <textarea class="form-control list-textarea" name="job_desc[]" placeholder="- Job Description" rows="2">{{ old('job_desc.0') }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Reason for Leaving</label>
                                            <textarea class="form-control list-textarea" name="reason[]" placeholder="- Reason for Leaving" rows="2">{{ old('reason.0') }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Benefits</label>
                                            <textarea class="form-control list-textarea" name="benefit[]" placeholder="- Benefits" rows="2">{{ old('benefit.0') }}</textarea>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">Facilities</label>
                                            <textarea class="form-control list-textarea" name="facility[]" placeholder="- Facilities" rows="2">{{ old('facility.0') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="text-end mt-3">
                                        <button type="button" class="btn btn-danger btn-sm remove-work-card">
                                            <i class="fas fa-trash me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
            `;
            $('#workExperienceContainer').append(newRow);

            updateWorkNumbers();
        });

        $(document).on('click', '.remove-work-card', function() {
            $(this).closest('.work-card').remove();

            updateWorkNumbers();
        });

        // Validasi tanggal untuk Work Experience
        $(document).on('change', '.work-end-date', function() {
            const startDate = $(this).closest('.row').find('.work-start-date').val();
            const endDate = $(this).val();

            if (startDate && endDate && new Date(endDate) <= new Date(startDate)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Date',
                    text: 'End date must be after start date!',
                });
                $(this).val('');
            }
        });




        // Update the language card template
        $('#addLanguage').on('click', function() {
            const languageCount = $('.language-card').length + 1;
            const newLanguageCard = `
                <div class="card mb-3 language-card">
                    <div class="card-header bg-primary text-white">
                        <span class="language-index fw-bold">Language #${languageCount}</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Language</label>
                                <select class="form-select language-select" name="language[]">
                                    <option selected disabled>-- Select Language --</option>
                                    <option value="Indonesian">Indonesian</option>
                                    <option value="English">English</option>
                                    <option value="Mandarin">Mandarin</option>
                                    <option value="Japanese">Japanese</option>
                                    <option value="Other">Other</option>
                                </select>
                                <input type="text" class="form-control mt-2 d-none other-language" name="other_language[]" placeholder="Specify language">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Verbal</label>
                                <select class="form-select" name="verbal[]">
                                    <option selected disabled>-- Select Level --</option>
                                    <option value="Passive">Passive</option>
                                    <option value="Active">Active</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Written</label>
                                <select class="form-select" name="written[]">
                                    <option selected disabled>-- Select Level --</option>
                                    <option value="Passive">Passive</option>
                                    <option value="Active">Active</option>
                                </select>
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button type="button" class="btn btn-danger btn-sm remove-language-card">
                                <i class="fas fa-trash me-1"></i>Remove
                            </button>
                        </div>
                    </div>
                </div>
                `;
            $('#languageContainer').append(newLanguageCard);
            updateLanguageIndices();
            updateLanguageOptions();

            // Add change event handler for the new dropdown
            $('.language-select').off('change').on('change', function() {
                toggleOtherLanguageInput($(this));
            });
        });


        // Handle language selection
        $(document).on('change', '.language-select', function() {
            // Get the selected value
            const selectedValue = $(this).val();

            // Update the selected languages array
            selectedLanguages = [];
            $('.language-select').each(function() {
                const value = $(this).val();
                if (value && value !== '' && value !== 'Other') {
                    selectedLanguages.push(value);
                }
            });

            // Update available options
            updateLanguageOptions();
        });

        // Handle removal of language card
        $(document).on('click', '.remove-language-card', function() {
            $(this).closest('.language-card').remove();
            updateLanguageIndices();

            // Update the selected languages array
            selectedLanguages = [];
            $('.language-select').each(function() {
                const value = $(this).val();
                if (value && value !== '' && value !== 'Other') {
                    selectedLanguages.push(value);
                }
            });

            // Update available options
            updateLanguageOptions();
        });


        // Training Card Management
        $('#addTraining').on('click', function() {
            const trainingCount = $('.training-card').length + 1;
            const newTrainingCard = `
                <div class="card mb-3 training-card">
                    <div class="card-header bg-primary text-white">
                        <span class="training-index fw-bold">Training #${trainingCount}</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Training Name</label>
                                <input type="text" class="form-control" name="training_name[]" placeholder="Training Name" value="">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Province</label>
                                <select class="form-select province-dropdown" name="training_province[]" required>
                                    <option value="" disabled selected>Select Province</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <select class="form-select city-dropdown" id="trainingCity${trainingCount}" name="training_city[]" required>
                                    <option value="" disabled selected>Select City</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control training-start-date" name="training_start_date[]" value="">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control traning-end-date" name="training_end_date[]" value="">
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button type="button" class="btn btn-danger btn-sm remove-training-card">
                                <i class="fas fa-trash me-1"></i>Remove
                            </button>
                        </div>
                    </div>
                </div>
                `;
            $('#trainingContainer').append(newTrainingCard);

            // Update training indices
            updateTrainingIndices();
            loadProvinces();
        });

        // Remove training card
        $(document).on('click', '.remove-training-card', function() {
            $(this).closest('.training-card').remove();

            // Update training indices
            updateTrainingIndices();
        });


        // Validasi tanggal untuk Training
        $(document).on('change', '.training-end-date', function() {
            const startDate = $(this).closest('.row').find('.training-start-date').val();
            const endDate = $(this).val();

            if (startDate && endDate && new Date(endDate) <= new Date(startDate)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Date',
                    text: 'End date must be after start date!',
                });
                $(this).val('');
            }
        });


        $('#addOrganization').on('click', function() {
            const organizationCount = $('.organization-card').length + 1;
            const newOrganizationCard = `
        <div class="card mb-3 organization-card">
            <div class="card-header bg-primary text-white">
                <span class="organization-index fw-bold">Organization #${organizationCount}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Organization Name</label>
                        <input type="text" class="form-control" name="organization_name[]" placeholder="Organization Name" value="">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Position</label>
                        <input type="text" class="form-control" name="organization_position[]" placeholder="Position" value="">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Activity Type</label>
                        <textarea class="form-control list-textarea" name="activity_type[]" placeholder=" -Activity Type" rows="2"></textarea>
                    </div>
                 
                    <div class="col-md-6">
                        <label class="form-label">Province</label>
                        <select class="form-select province-dropdown" name="organization_province[]" required>
                            <option value="" disabled selected>Select Province</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City</label>
                        <select class="form-select city-dropdown" id="organizationCity${organizationCount}" name="organization_city[]" required>
                            <option value="" disabled selected>Select City</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control organization-start-date" name="organization_start_date[]" value="">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control organization-end-date" name="organization_end_date[]" value="">
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button type="button" class="btn btn-danger btn-sm remove-organization-card">
                        <i class="fas fa-trash me-1"></i>Remove
                    </button>
                </div>
            </div>
        </div>
    `;
            $('#organizationContainer').append(newOrganizationCard);
            updateOrganizationIndices();
            loadProvinces();
        });


        // Remove organization card
        $(document).on('click', '.remove-organization-card', function() {
            $(this).closest('.organization-card').remove();

            // Update organization indices
            updateOrganizationIndices();
        });


        // Validasi tanggal untuk Organization
        $(document).on('change', '.organization-end-date', function() {
            const startDate = $(this).closest('.row').find('.organization-start-date').val();
            const endDate = $(this).val();

            if (startDate && endDate && new Date(endDate) <= new Date(startDate)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Date',
                    text: 'End date must be after start date!',
                });
                $(this).val('');
            }
        });


        $('#distance').on('input', function() {
            let value = $(this).val();

            // Cegah input negatif atau lebih dari 30
            if (value < 0) {
                $(this).val(0);
            } else if (value > 30) {
                $(this).val(30);
            }
        });

    });
</script>
@endpush