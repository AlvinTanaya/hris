@extends('layouts.app')

@section('content')

<a href="{{ route('user.employees.index') }}" class="btn btn-danger px-5 mb-3">
    <i class="fas fa-arrow-left me-2"></i>Back
</a>
@if ($user->id == Auth::user()->id)
<h1 class="add-employee-heading">
    <i class="fas fa-user"></i> Profile
</h1>
@else
<h1 class="add-employee-heading"><i class="far fa-user"></i> Employee Profile Information</h1>
@endif

<div class="container mt-1 test">
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
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="elearningTab" data-bs-toggle="tab" href="#elearning" role="tab" aria-controls="elearning" aria-selected="false">E-learning</a>
        </li>
    </ul>
    <form action="{{ route('user.employees.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card shadow-lg border-0 rounded mt-4">

            <div class="card-body pt-1 pb-1">
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

                <div class="tab-content" id="employeeTabContent">
                    <!-- Data user -->
                    <div class="tab-pane fade show active" id="userData" role="tabpanel" aria-labelledby="userDataTab">

                        <!-- Judul Data user -->
                        <h5 class="text-primary">
                            <i class="fas fa-user-tie"></i> User Data
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
                                        src="{{ $user->photo_profile_path ? asset('storage/' . $user->photo_profile_path) : '#' }}"
                                        alt="Preview"
                                        class="rounded-circle border border-primary border-3 {{ $user->photo_profile_path ? '' : 'd-none' }}"
                                        style="width: 150px; height: 150px; object-fit: cover;">


                                </div>

                                <!-- Input file untuk gambar -->
                                <input
                                    type="file"
                                    id="image-input"
                                    class="form-control"
                                    name="photo"
                                    accept="image/jpeg, image/png, image/jpg">
                                <small class="text-muted">Only file JPG, JPEG, atau PNG</small>
                            </div>

                            <div class="col-md-4 mb-3"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="employee_id" class="form-label">
                                    <i class="fas fa-id-badge"></i> Employee ID
                                </label>
                                <input type="text" class="form-control" id="employee_id" name="employee_id" value="{{ old('employee_id',$user->employee_id) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user"></i> Name
                                </label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name',$user->name) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="join_date" class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Join Date
                                </label>
                                <input type="date" class="form-control" id="join_date" name="join_date" value="{{ old('join_date', $user->join_date ? $user->join_date->format('Y-m-d') : '') }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="exit_date" class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Exit Date
                                </label>
                                <input type="date" class="form-control" id="exit_date" name="exit_date" value="{{ old('exit_date', $user->exit_date ? $user->exit_date->format('Y-m-d') : '') }}">
                            </div>
                        </div>







                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="position_id" class="form-label">
                                    <i class="fas fa-briefcase"></i> Position
                                </label>
                                <select class="form-control" id="position_id" name="position_id" required>
                                    <option value="" selected disabled>Choose position</option>
                                    @foreach($positions as $position)
                                    <option value="{{ $position->id }}"
                                        {{ (old('position_id', $user->position_id) == $position->id) ? 'selected' : '' }}>
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
                                        {{ (old('department_id', $user->department_id) == $department->id) ? 'selected' : '' }}>
                                        {{ $department->department }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="row">
                            <!-- Employee Status -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">
                                    <i class="fa-solid fa-ring"></i> Status
                                </label>
                                <select class="form-control" id="status" name="status" required>
                                    <option selected disabled>Choose Status</option>
                                    <option value="TK/0" {{ old('status', $user->status) == 'TK/0' ? 'selected' : '' }}>TK/0</option>
                                    <option value="TK/1" {{ old('status', $user->status) == 'TK/1' ? 'selected' : '' }}>TK/1</option>
                                    <option value="TK/2" {{ old('status', $user->status) == 'TK/2' ? 'selected' : '' }}>TK/2</option>
                                    <option value="TK/3" {{ old('status', $user->status) == 'TK/3' ? 'selected' : '' }}>TK/3</option>
                                    <option value="K/1" {{ old('status', $user->status) == 'K/1' ? 'selected' : '' }}>K/1</option>
                                    <option value="K/2" {{ old('status', $user->status) == 'K/2' ? 'selected' : '' }}>K/2</option>
                                    <option value="K/3" {{ old('status', $user->status) == 'K/3' ? 'selected' : '' }}>K/3</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="user_status" class="form-label">
                                    <i class="fas fa-ban"></i> User Status
                                </label>
                                <select class="form-control" id="user_status" name="user_status" required>
                                    <option selected disabled>Choose Status</option>
                                    <option value="Unbanned" {{ old('user_status', $user->user_status) == 'Unbanned' ? 'selected' : '' }}>Unbanned</option>
                                    <option value="Banned" {{ old('user_status', $user->user_status) == 'Banned' ? 'selected' : '' }}>Banned</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="employee_status" class="form-label">
                                    <i class="fas fa-user-check"></i> Employee Status
                                </label>
                                <select class="form-control" id="employee_status" name="employee_status" required>
                                    <option selected disabled>Choose Status</option>
                                    <option value="Full Time" {{ old('employee_status', $user->employee_status) == 'Full Time' ? 'selected' : '' }}>Full Time</option>
                                    <option value="Part Time" {{ old('employee_status', $user->employee_status) == 'Part Time' ? 'selected' : '' }}>Part Time</option>
                                    <option value="Contract" {{ old('employee_status', $user->employee_status) == 'Contract' ? 'selected' : '' }}>Contract</option>
                                    <option value="Inactive" {{ old('employee_status', $user->employee_status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="npwp" class="form-label">
                                    <i class="fas fa-user"></i> NPWP
                                </label>
                                <input type="text" class="form-control" id="npwp" name="npwp" value="{{ old('npwp',$user->NPWP) }}">
                            </div>
                        </div>


                        <!-- Contract Start & End Date (Hidden by Default) -->
                        <div class="row" id="contract_dates_wrapper" style="display: none;">
                            <!-- Contract Start Date -->
                            <div class="col-md-6 mb-3">
                                <label for="contract_start_date" class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Contract Start Date
                                </label>
                                <input type="date" value="{{ old('contract_start_date', $user->contract_start_date) }}" class="form-control" id="contract_start_date" name="contract_start_date">
                            </div>

                            <!-- Contract End Date -->
                            <div class="col-md-6 mb-3">
                                <label for="contract_end_date" class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Contract End Date
                                </label>
                                <input type="date" value="{{ old('contract_end_date', $user->contract_end_date) }}" class="form-control" id="contract_end_date" name="contract_end_date">
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-6 mb-3">

                                <label for="bpjs_employment" class="form-label">
                                    <i class="fas fa-id-card"></i> BPJS Ketenagakerjaan
                                </label>
                                <input type="text" class="form-control" id="bpjs_employment" name="bpjs_employment" value="{{ old('bpjs_employment', $user->bpjs_employment) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bpjs_health" class="form-label">
                                    <i class="fas fa-id-card"></i> BPJS Kesehatan
                                </label>
                                <input type="text" class="form-control" id="bpjs_health" name="bpjs_health" value="{{ old('bpjs_health',$user->bpjs_health) }}" required>
                            </div>
                        </div>

                        <!-- Bank Information Card -->

                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fa-solid fa-university"></i> Bank Information</h5>
                            </div>
                            <div class="card-body">
                                <!-- Container for all bank rows -->
                                <div id="bank-container">
                                    @if(!empty($bankData))
                                    @foreach($bankData as $index => $bank)
                                    <div class="row bank-row mb-3">
                                        <div class="col-md-6">
                                            <label for="bank_name_{{ $index }}" class="form-label"><i class="fa-solid fa-piggy-bank"></i> Bank Name</label>
                                            <select class="form-control bank-name-select" id="bank_name_{{ $index }}" name="bank_name[]" required>
                                                <option value="" disabled>Select Bank</option>
                                                @foreach(['Bank Central Asia (BCA)', 'Bank Mandiri', 'Bank Rakyat Indonesia (BRI)', 'Bank Negara Indonesia (BNI)', 'Bank CIMB Niaga', 'Bank Tabungan Negara (BTN)', 'Bank Danamon', 'Bank Permata', 'Bank Panin', 'Bank OCBC NISP', 'Bank Maybank Indonesia', 'Bank Mega', 'Bank Bukopin', 'Bank Sinarmas'] as $bankOption)
                                                <option value="{{ $bankOption }}" {{ $bank['name'] == $bankOption ? 'selected' : '' }}>{{ $bankOption }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <label for="bank_number_{{ $index }}" class="form-label">
                                                <i class="fa-solid fa-credit-card"></i> Bank Number</label>
                                            <input type="number" class="form-control bank-number-input" id="bank_number_{{ $index }}" name="bank_number[]" value="{{ $bank['number'] }}" required>
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="button" class="btn btn-danger btn-block delete-bank-row"><i class="fa-solid fa-trash"></i></button>
                                        </div>
                                    </div>
                                    @endforeach
                                    @else
                                    <!-- Default empty bank row if no data exists -->
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
                                    @endif
                                </div>

                                <!-- Button to add new bank row -->
                                <div class="mt-5 d-flex justify-content-end">
                                    <button type="button" id="add-bank-btn" class="btn btn-success">
                                        <i class="fa-solid fa-plus"></i> Add Bank
                                    </button>
                                </div>
                            </div>
                        </div>



                        @if ($user->id == Auth::user()->id)
                        <div class="row">
                            <div class="col-md-12">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> New Password
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password">
                                    <button class="btn btn-primary" type="button" onclick="togglePassword()">
                                        <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        @endif


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
                                <input type="number" class="form-control" id="ID_number" name="ID_number" value="{{ old('ID_number' , $user->ID_number) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="religion" class="form-label">
                                    <i class="fas fa-praying-hands"></i> Religion
                                </label>
                                <select class="form-control" id="religion" name="religion" required>
                                    <option selected disabled>Choose Religion</option>
                                    <option value="Islam" {{ old('religion', $user->religion) == 'Islam' ? 'selected' : '' }}>Islam</option>
                                    <option value="Katolik" {{ old('religion', $user->religion) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                    <option value="Kristen" {{ old('religion', $user->religion) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                    <option value="Buddha" {{ old('religion', $user->religion) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                    <option value="Hindu" {{ old('religion', $user->religion) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                    <option value="Konghucu" {{ old('religion', $user->religion) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>



                                </select>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="birth_date" class="form-label">
                                    <i class="fas fa-calendar-day"></i> Birth Date
                                </label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date" value="{{ old('birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="birth_place" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i> Birth Place
                                </label>
                                <input type="text" class="form-control" id="birth_place" name="birth_place" value="{{ old('birth_place', $user->birth_place) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ID_address" class="form-label">
                                    <i class="fas fa-map-pin"></i> KTP Address
                                </label>
                                <input type="text" class="form-control" id="ID_address" name="ID_address" value="{{ old('ID_address', $user->ID_address) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="domicile_address" class="form-label">
                                    <i class="fas fa-map-signs"></i> Domicile Address
                                </label>
                                <input type="text" class="form-control" id="domicile_address" name="domicile_address" value="{{ old('domicile_address', $user->domicile_address) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fa-solid fa-location-dot"></i> Distance Between Domicile Address to Company Location</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="distance" id="distance"
                                        value="{{ old('distance', $user->distance) }}" min="0" max="30" step="0.01" required>
                                    <span class="input-group-text">KM</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label for="phone_number" class="form-label">
                                    <i class="fas fa-phone"></i> Phone Number
                                </label>
                                <input type="tel" placeholder="Example: 081234567890" pattern="08[0-9]{10,12}" value="{{ old('phone_number', $user->phone_number) }}" class="form-control" id="phone_number" name="phone_number" required>
                                <small class="text-muted">Enter a mobile number starting with 08 (11-14 digits)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact" class="form-label"><i class="fa-solid fa-phone"></i> Emergency Contact</label>
                                <input type="tel" class="form-control" id="emergency_contact" name="emergency_contact"
                                    pattern="08[0-9]{9,12}"
                                    maxlength="14"
                                    minlength="11"
                                    placeholder="Example: 081234567890"
                                    value="{{ old('emergency_contact', $user->emergency_contact) }}"
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

                                    <option value="Male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <!-- Blood Type -->
                            <div class="col-md-6 mb-3">
                                <label for="blood_type" class="form-label">
                                    <i class="fas fa-tint"></i> Blood Type
                                </label>
                                <select class="form-control" id="blood_type" name="blood_type" required>
                                    <option selected disabled>Choose Blood Type</option>
                                    @foreach(['A', 'B', 'AB', 'O'] as $blood)
                                    <option value="{{ $blood }}" {{ old('blood_type', $user->blood_type) == $blood ? 'selected' : '' }}>
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
                                <input type="number" class="form-control" id="height" name="height" value="{{ old('height', $user->height) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="weight" class="form-label">
                                    <i class="fas fa-weight"></i> Weight (kg)
                                </label>
                                <input type="number" class="form-control" id="weight" name="weight" value="{{ old('weight', $user->weight) }}" required>
                            </div>
                        </div>


                        <!-- Driving License Section -->
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

                                // Decode JSON dari database agar jadi array
                                $user_sim = is_array($user->sim) ? $user->sim : explode(',', $user->sim ?? '');



                                $user_sim_numbers = json_decode($user->sim_number ?? '{}', true);
                                $has_sim = !empty($user_sim); // Cek apakah user punya SIM atau tidak
                                @endphp

                                @foreach ($licenses as $key => $label)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input
                                            class="form-check-input license-checkbox"
                                            type="checkbox"
                                            id="hasLicense{{ $key }}"
                                            name="sim[]"
                                            value="{{ $key }}"
                                            {{ !empty($user_sim) && in_array($key, (array) $user_sim) ? 'checked' : '' }}>

                                        <label class="form-check-label" for="hasLicense{{ $key }}">{{ $label }}</label>
                                    </div>
                                    <input
                                        type="text"
                                        class="form-control license-number mt-2"
                                        name="sim_number[{{ $key }}]"
                                        placeholder="License number (if applicable)"
                                        value="{{ $user_sim_numbers[$key] ?? '' }}"
                                        {{ $has_sim && in_array($key, $user_sim) ? '' : 'disabled' }}>
                                </div>
                                @endforeach

                                <!-- No License Option -->
                                <div class="col-12 mt-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="noLicense" name="no_license"
                                            {{ $has_sim ? '' : 'checked' }}>
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
                                <div class="input-group">
                                    <input type="file" class="form-control" id="id_card" name="id_card" accept="image/jpeg, image/png">
                                    @if(!empty($user->ID_card_path))
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#viewIDCard">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Upload CV -->
                            <div class="col-md-4 mb-3">
                                <label for="cv" class="form-label">
                                    <i class="fa-solid fa-file-pdf"></i> Upload CV (PDF)
                                </label>
                                <div class="input-group">
                                    <input type="file" class="form-control" id="cv" name="cv" accept=".pdf">
                                    @if(!empty($user->cv_path))
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#viewCV">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    @endif
                                </div>
                            </div>


                            <!-- Achievement -->
                            <div class="col-md-4 mb-3">
                                <label for="achievement" class="form-label">
                                    <i class="fa-solid fa-file-pdf"></i> Achievement (PDF)
                                </label>
                                <div class="input-group">
                                    <input type="file" class="form-control" id="achievement" name="achievement" accept=".pdf">
                                    @if(!empty($user->achievement_path))
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#viewAchievement">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Modal View ID Card -->
                        @if(!empty($user->ID_card_path))
                        <div class="modal fade" id="viewIDCard" tabindex="-1" aria-labelledby="viewIDCardLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="viewIDCardLabel"> <i class="fas fa-id-card"></i> View ID Card</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <img src="{{ asset('storage/' . $user->ID_card_path) }}" class="img-fluid rounded" alt="ID Card">

                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Modal View CV -->
                        @if(!empty($user->cv_path))

                        <div class="modal fade" id="viewCV" tabindex="-1" aria-labelledby="viewCVLabel" aria-hidden="true">

                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="viewCVLabel"><i class="fas fa-file-alt"></i> View CV </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <iframe src="{{ asset('storage/'. $user->cv_path) }}" width="100%" height="500px"></iframe>

                                    </div>

                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Modal Achievement -->
                        @if(!empty($user->achievement_path))
                        <div class="modal fade" id="viewAchievement" tabindex="-1" aria-labelledby="viewAchievementLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="viewAchievementLabel"><i class="fas fa-file-alt"></i> Achievement</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <iframe src="{{ asset('storage/'. $user->achievement_path) }}" width="100%" height="500px"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif


                    </div>

                    <!-- Family Data user -->
                    <div class="tab-pane fade" id="familyData" role="tabpanel" aria-labelledby="familyDataTab">
                        <h5 class="text-primary">
                            <i class="fas fa-users me-2"></i>Family Data
                        </h5>

                        <div id="familyMembersContainer" class="mt-4">
                            @if(!empty($userFamily))
                            @foreach($userFamily as $index => $family)
                            <div class="card mb-3 family-card">
                                <div class="card-header bg-primary text-white">
                                    <span class="family-index fw-bold">Family #{{ $index + 1 }}</span>
                                </div>

                                <div class="card-body">

                                    <div class="row g-3">
                                        <input type="text" class="form-control" name="id_family[]" value="{{ old('id_family', $family->id) }}" hidden>
                                        <div class="col-md-6">
                                            <label class="form-label">Name</label>
                                            <input type="text" class="form-control" placeholder="Name" name="name_family[]" value="{{ old('name_family',$family->name) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Relationship</label>
                                            <select class="form-select" name="relation[]" required>
                                                <option disabled value="">Select Relationship</option>
                                                <option value="Father" {{ old('gender_family', $family->relation) == 'Father' ? 'selected' : '' }}>Father</option>
                                                <option value="Mother" {{ old('gender_family', $family->relation) == 'Mother' ? 'selected' : '' }}>Mother</option>
                                                <option value="Husband" {{ old('gender_family', $family->relation) == 'Husband' ? 'selected' : '' }}>Husband</option>
                                                <option value="Wife" {{ old('gender_family', $family->relation) == 'Wife' ? 'selected' : '' }}>Wife</option>
                                                <option value="Child" {{ old('gender_family', $family->relation) == 'Child' ? 'selected' : '' }}>Child</option>
                                                <option value="Sibling" {{ old('gender_family', $family->relation) == 'Sibling' ? 'selected' : '' }}>Sibling</option>

                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone Number</label>
                                            <input type="tel" placeholder="08XXXXXXXXXX" pattern="08[0-9]{10,12}" value="{{ old('phone_number_family', $family->phone_number) }}" class="form-control" name="phone_number_family[]">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Gender</label>
                                            <select class="form-control" name="gender_family[]">
                                                <option selected disabled>Choose Gender</option>
                                                <option value="Male" {{ old('gender_family', $family->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ old('gender_family', $family->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Birth Date</label>
                                            <input type="date" class="form-control" placeholder="Birth Date" name="birth_date_family[]" value="{{ old('birth_date_family', $family->birth_date) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Birth Place</label>
                                            <input type="text" class="form-control" placeholder="Birth Place" name="birth_place_family[]" value="{{ old('birth_place_family', $family->birth_place) }}">
                                        </div>


                                        <div class="col-md-6">
                                            <label class="form-label">Address</label>
                                            <input type="text" class="form-control" placeholder="Address" name="address_family[]" value="{{ old('address_family', $family->address) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">KTP Number</label>
                                            <input type="text" class="form-control" placeholder="KTP Number" name="ID_number_family[]" value="{{ old('ID_number_family', $family->ID_number) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Job</label>
                                            <input type="text" class="form-control" placeholder="Job" name="job[]" value="{{ old('job', $family->job) }}">
                                        </div>
                                    </div>
                                    <div class="text-end mt-3">
                                        <button type="button" class="btn btn-danger btn-sm remove-family-card">
                                            <i class="fas fa-trash me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @endif
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
                            @if(!empty($userEducation))
                            @foreach($userEducation as $index => $education)
                            <div class="card mb-3 education-card">
                                <div class="card-header bg-primary text-white">
                                    <span class="education-index fw-bold">Education #{{ $index + 1 }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <input type="text" class="form-control" name="id_education[]" value="{{ old('id_education.'.$index, $education->id) }}" hidden>
                                        <div class="col-md-6">
                                            <label class="form-label">Degree</label>
                                            <select class="form-select education-level" name="education_level[]">
                                                <option disabled> -- Choose Degree --</option>
                                                <option value="SMA" {{ old('education_level.'.$index, $education->degree) == 'SMA' ? 'selected' : '' }}>SMA</option>
                                                <option value="SMK" {{ old('education_level.'.$index, $education->degree) == 'SMK' ? 'selected' : '' }}>SMK</option>
                                                <option value="D3" {{ old('education_level.'.$index, $education->degree) == 'D3' ? 'selected' : '' }}>D3</option>
                                                <option value="S1" {{ old('education_level.'.$index, $education->degree) == 'S1' ? 'selected' : '' }}>S1</option>
                                                <option value="S2" {{ old('education_level.'.$index, $education->degree) == 'S2' ? 'selected' : '' }}>S2</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Educational Place</label>
                                            <input type="text" class="form-control" name="education_place[]" placeholder="Educational Place" value="{{ old('education_place.'.$index, $education->educational_place) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Province</label>
                                            <select class="form-select province-dropdown" name="education_province[]" required>
                                                <option value="" disabled selected>Select Province</option>
                                                <!-- Add a default selected option for the saved value -->
                                                @if(!empty($education->educational_province))
                                                <option value="{{ $education->educational_province }}" selected>{{ $education->educational_province }}</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">City</label>
                                            <select class="form-select city-dropdown" id="educationCity{{ $index + 1 }}" name="education_city[]" required>
                                                <option value="" disabled selected>Select City</option>
                                                <!-- Add a default selected option for the saved value -->
                                                @if(!empty($education->educational_city))
                                                <option value="{{ $education->educational_city }}" selected>{{ $education->educational_city }}</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Major</label>
                                            <input type="text" class="form-control" name="major[]" placeholder="Major" value="{{ old('major.'.$index, $education->major) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Start Date</label>
                                            <input type="date" class="form-control education-start-date" name="start_education[]" value="{{ old('start_education.'.$index, $education->start_education) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">End Date</label>
                                            <input type="date" class="form-control education-end-date" name="end_education[]" value="{{ old('end_education.'.$index, $education->end_education) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Grade</label>
                                            <input type="text" class="form-control education-grade" name="grade[]" placeholder="Grade" value="{{ old('grade.'.$index, $education->grade) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Transcript</label>
                                            @if(!empty($education->transcript_file_path))
                                            <div class="input-group mt-1">
                                                <input type="file" class="form-control" name="education_transcript[]" accept="image/*">
                                                <button class="btn btn-outline-secondary view-transcript" type="button" data-bs-toggle="modal" data-bs-target="#transcriptModal" data-src="{{ asset('storage/' . $education->transcript_file_path) }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            @else
                                            <input type="file" class="form-control" name="education_transcript[]" accept="image/*">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-end mt-3">
                                        <button type="button" class="btn btn-danger btn-sm remove-education-card">
                                            <i class="fas fa-trash me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @endif
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
                            @if(!empty($userWork))
                            @foreach($userWork as $index => $work)
                            <div class="card mb-3 work-card">
                                <div class="card-header bg-primary text-white">
                                    <span class="work-index fw-bold">Work Experience #{{ $index + 1 }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <input type="text" class="form-control" name="id_work[]" value="{{ old('id_work', $work->id) }}" hidden>
                                        <div class="col-md-6">
                                            <label class="form-label">Company Name</label>
                                            <input type="text" class="form-control" name="company_name[]" placeholder="Company Name" value="{{ old('company_name.0', $work->company_name) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Position</label>
                                            <input type="text" class="form-control" name="position_work[]" placeholder="Position" value="{{ old('position.0', $work->position) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Start Date</label>
                                            <input type="date" class="form-control" name="start_work[]" value="{{ old('start_work.0', $work->start_working) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">End Date</label>
                                            <input type="date" class="form-control" name="end_work[]" value="{{ old('end_work.0', $work->end_working) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Company Address</label>
                                            <input type="text" class="form-control" name="company_address[]" placeholder="Company Address" value="{{ old('company_address.0', $work->company_address) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Company Phone</label>
                                            <input type="tel" placeholder="08XXXXXXXXXX" pattern="08[0-9]{10,12}" value="{{ old('company_phone.0', $work->company_phone) }}" class="form-control" name="company_phone[]">

                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Salary</label>
                                            <input type="text" class="form-control" name="salary[]" placeholder="Salary" value="{{ old('salary.0', $work->salary) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Supervisor Name</label>
                                            <input type="text" class="form-control" name="supervisor_name[]" placeholder="Supervisor Name" value="{{ old('supervisor_name.0', $work->supervisor_name) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Supervisor Phone</label>
                                            <input type="text" class="form-control" name="supervisor_phone[]" placeholder="Supervisor Phone" value="{{ old('supervisor_phone.0', $work->supervisor_phone) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Job Description</label>
                                            <textarea class="form-control list-textarea" name="job_desc[]" placeholder="- Job Description" rows="2">{{ old('job_desc.0', isset($work) ? "- " . str_replace(";", "\n- ", $work->job_desc) : '') }}</textarea>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Reason for Leaving</label>
                                            <textarea class="form-control list-textarea" name="reason[]" placeholder="- Reason for Leaving" rows="2">{{ old('reason.0', isset($work) ? "- " . str_replace(";", "\n- ", $work->reason) : '') }}</textarea>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Benefits</label>
                                            <textarea class="form-control list-textarea" name="benefit[]" placeholder="- Benefits" rows="2">{{ old('benefit.0', isset($work) ? "- " . str_replace(";", "\n- ", $work->benefit) : '') }}</textarea>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label">Facilities</label>
                                            <textarea class="form-control list-textarea" name="facility[]" placeholder="- Facilities" rows="2">{{ old('facility.0', isset($work) ? "- " . str_replace(";", "\n- ", $work->facility) : '') }}</textarea>
                                        </div>

                                    </div>
                                    <div class="text-end mt-3">
                                        <button type="button" class="btn btn-danger btn-sm remove-work-card">
                                            <i class="fas fa-trash me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @endif
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
                            @if(!empty($userLanguage))

                            @foreach($userLanguage as $index => $language)
                            <div class="card mb-3 language-card">
                                <div class="card-header bg-primary text-white">
                                    <span class="language-index fw-bold">Language #{{ $index + 1 }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <input type="text" class="form-control" name="id_language[]" value="{{ old('id_language', $language->id) }}" hidden>
                                        <div class="col-md-6">
                                            <label class="form-label">Language</label>
                                            <select class="form-select language-select" name="language[]">
                                                <option disabled>-- Select Language --</option>
                                                <option value="Indonesian" {{ old('language.0', $language->language) == 'Indonesian' ? 'selected' : '' }}>Indonesian</option>
                                                <option value="English" {{ old('language.0', $language->language) == 'English' ? 'selected' : '' }}>English</option>
                                                <option value="Mandarin" {{ old('language.0', $language->language) == 'Mandarin' ? 'selected' : '' }}>Mandarin</option>
                                                <option value="Japanese" {{ old('language.0', $language->language) == 'Japanese' ? 'selected' : '' }}>Japanese</option>
                                                <option value="Other" {{ !in_array(old('language.0', $language->language), ['Indonesian', 'English', 'Mandarin', 'Japanese']) ? 'selected' : '' }}>Other</option>
                                            </select>
                                            <input type="text" class="form-control mt-2 {{ !in_array(old('language.0', $language->language), ['Indonesian', 'English', 'Mandarin', 'Japanese']) ? '' : 'd-none' }} other-language"
                                                name="other_language[]"
                                                placeholder="Specify language"
                                                value="{{ !in_array(old('language.0', $language->language), ['Indonesian', 'English', 'Mandarin', 'Japanese']) ? old('language.0', $language->language) : '' }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Verbal</label>
                                            <select class="form-select" name="verbal[]">
                                                <option disabled>-- Select Level --</option>
                                                <option value="Passive" {{ old('verbal.0', $language->verbal) == 'Passive' ? 'selected' : '' }}>Passive</option>
                                                <option value="Active" {{ old('verbal.0', $language->verbal) == 'Active' ? 'selected' : '' }}>Active</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Written</label>
                                            <select class="form-select" name="written[]">
                                                <option disabled>-- Select Level --</option>
                                                <option value="Passive" {{ old('written.0', $language->written) == 'Passive' ? 'selected' : '' }}>Passive</option>
                                                <option value="Active" {{ old('written.0', $language->written) == 'Active' ? 'selected' : '' }}>Active</option>
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
                            @endforeach
                            @endif
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
                            @if(!empty($userTraining))
                            @foreach($userTraining as $index => $training)
                            <div class="card mb-3 training-card">
                                <div class="card-header bg-primary text-white">
                                    <span class="training-index fw-bold">Training #{{ $index + 1 }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <input type="text" class="form-control" name="id_training[]" value="{{ old('id_training.'.$index, $training->id) }}" hidden>
                                        <div class="col-md-12">
                                            <label class="form-label">Training Name</label>
                                            <input type="text" class="form-control" name="training_name[]" placeholder="Training Name" value="{{ old('training_name.'.$index, $training->training_name) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Province</label>
                                            <select class="form-select province-dropdown" name="training_province[]" required>
                                                <option value="" disabled selected>Select Province</option>
                                                <!-- Add a default selected option for the saved value -->
                                                @if(!empty($training->training_province))
                                                <option value="{{ $training->training_province }}" selected>{{ $training->training_province }}</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">City</label>
                                            <select class="form-select city-dropdown" id="trainingCity{{ $index + 1 }}" name="training_city[]" required>
                                                <option value="" disabled selected>Select City</option>
                                                <!-- Add a default selected option for the saved value -->
                                                @if(!empty($training->training_city))
                                                <option value="{{ $training->training_city }}" selected>{{ $training->training_city }}</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Start Date</label>
                                            <input type="date" class="form-control training-start-date" name="training_start_date[]" value="{{ old('training_start_date.'.$index, $training->start_date) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">End Date</label>
                                            <input type="date" class="form-control training-end-date" name="training_end_date[]" value="{{ old('training_end_date.'.$index, $training->end_date) }}">
                                        </div>
                                    </div>
                                    <div class="text-end mt-3">
                                        <button type="button" class="btn btn-danger btn-sm remove-training-card">
                                            <i class="fas fa-trash me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @endif
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
                            @if(!empty($userOrganization))
                            @foreach($userOrganization as $index => $organization)
                            <div class="card mb-3 organization-card">
                                <div class="card-header bg-primary text-white">
                                    <span class="organization-index fw-bold">Organization #{{ $index + 1 }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <input type="text" class="form-control" name="id_organization[]" value="{{ old('id_organization.'.$index, $organization->id) }}" hidden>
                                        <div class="col-md-6">
                                            <label class="form-label">Organization Name</label>
                                            <input type="text" class="form-control" name="organization_name[]" placeholder="Organization Name" value="{{ old('organization_name.'.$index, $organization->organization_name) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Position</label>
                                            <input type="text" class="form-control" name="organization_position[]" placeholder="Position" value="{{ old('position.'.$index, $organization->position) }}">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">Activity Type</label>
                                            <textarea class="form-control list-textarea" name="activity_type[]" placeholder="- Activity Type" rows="2">{{ old('activity_type.'.$index, isset($organization) ? "- " . str_replace(";", " ", $organization->activity_type) : '') }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Province</label>
                                            <select class="form-select province-dropdown" name="organization_province[]" required>
                                                <option value="" disabled selected>Select Province</option>
                                                <!-- Add a default selected option for the saved value -->
                                                @if(!empty($organization->province))
                                                <option value="{{ $organization->province }}" selected>{{ $organization->province }}</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">City</label>
                                            <select class="form-select city-dropdown" id="organizationCity{{ $index + 1 }}" name="organization_city[]" required>
                                                <option value="" disabled selected>Select City</option>
                                                <!-- Add a default selected option for the saved value -->
                                                @if(!empty($organization->city))
                                                <option value="{{ $organization->city }}" selected>{{ $organization->city }}</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Start Date</label>
                                            <input type="date" class="form-control organization-start-date" name="organization_start_date[]" value="{{ old('organization_start_date.'.$index, $organization->start_date) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">End Date</label>
                                            <input type="date" class="form-control organization-end-date" name="organization_end_date[]" value="{{ old('organization_end_date.'.$index, $organization->end_date) }}">
                                        </div>
                                    </div>
                                    <div class="text-end mt-3">
                                        <button type="button" class="btn btn-danger btn-sm remove-organization-card">
                                            <i class="fas fa-trash me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @endif
                        </div>

                        <button type="button" class="btn btn-primary" id="addOrganization">
                            <i class="fas fa-plus-circle me-2"></i> Add Organization
                        </button>
                    </div>


                    <!-- E-learning Data -->
                    <div class="tab-pane fade" id="elearning" role="tabpanel" aria-labelledby="elearningTab">
                        <h5 class="text-primary mb-4"><i class="fas fa-book me-2"></i> E-learning Data</h5>
                        <div class="table-responsive" style="padding-right: 1%;">
                            <table id="elearningTable" class="table table-bordered table-striped mb-3 pt-3 align-middle align-items-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Event Name</th>
                                        <th>Duration (minutes)</th>
                                        <th>Date Start</th>
                                        <th>Date End</th>
                                        <th>Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($duty as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->duration }} minutes</td>
                                        <td>{{ $item->start_date }}</td>
                                        <td>{{ $item->end_date }}</td>
                                        <td>{{ $item->grade }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="d-flex justify-content-end mt-4">
            @if ($user->id == Auth::user()->id)
            <button type="submit" class="btn btn-success px-5"><i class="fas fa-edit"></i> Update Profile Information</button>
            @else
            <button type="submit" class="btn btn-success px-5"><i class="fas fa-edit"></i> Update Employee Profile Information</button>
            @endif
        </div>


    </form>
</div>

<!-- Transcript Modal -->
<div class="modal fade" id="transcriptModal" tabindex="-1" aria-labelledby="transcriptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transcriptModalLabel">Transcript</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img id="transcriptImage" src="" class="img-fluid" alt="Transcript">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

<style>
    .test {
        --primary-color: #4e73df;
        --secondary-color: #f8f9fc;
        --accent-color: #2e59d9;
        --text-color: #5a5c69;
        --light-gray: #f8f9fa;
        --border-radius: 0.5rem;
        --box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        max-width: 1280px;
        margin: 0 auto;
    }

    body {
        background-color: #f8f9fc;
        color: var(--text-color);
        font-family: 'Nunito', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    h1.text-center.text-warning {
        font-weight: 700;
        margin-bottom: 2rem;
        background: linear-gradient(to right, #f6c23e, #e0a800);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        display: inline-block;
        padding: 0.5rem 0;
    }

    .card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        transition: all 0.3s ease;
        margin-bottom: 2rem;
    }

    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2);
    }

    .card-header {
        background-color: var(--secondary-color);
        border-bottom: 1px solid #e3e6f0;
        padding: 1.25rem 1.5rem;
        border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
        font-weight: 600;
    }

    .form-label {
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-control,
    .form-select {
        border-radius: var(--border-radius);
        padding: 0.75rem 1rem;
        border: 1px solid #d1d3e2;
        transition: all 0.3s;
        font-size: 0.9rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    .input-group-text {
        background-color: #eaecf4;
        border: 1px solid #d1d3e2;
        color: #6e707e;
        border-radius: 0 var(--border-radius) var(--border-radius) 0;
    }

    .btn {
        border-radius: var(--border-radius);
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-primary:hover {
        background-color: var(--accent-color);
        border-color: var(--accent-color);
    }

    .btn-danger {
        background-color: #e74a3b;
        border-color: #e74a3b;
    }

    .btn-danger:hover {
        background-color: #d52a1a;
        border-color: #d52a1a;
    }

    .btn-success {
        background-color: #1cc88a;
        border-color: #1cc88a;
    }

    .btn-success:hover {
        background-color: #169b6b;
        border-color: #169b6b;
    }

    /* Center the Add Employee heading */
    h1.add-employee-heading {
        text-align: center;
        margin: 1.5rem auto 2rem;
        font-weight: 700;
        color: #FFD700;
        /* Gold color for visibility */
        font-size: 2.5rem;
    }

    /* Fix the nav tabs to be evenly distributed */
    .nav-tabs {
        display: flex;
        width: 100%;
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        background-color: rgba(16, 48, 108, 0.8);
        /* Darker blue background */
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .nav-tabs .nav-item {
        flex: 1;
        text-align: center;
    }

    .nav-tabs .nav-link {
        color: rgba(255, 255, 255, 0.7);
        font-weight: 600;
        padding: 1rem 0;
        border: none;
        border-radius: 0;
        transition: all 0.3s;
        width: 100%;
        text-transform: uppercase;
        font-size: 0.9rem;
        letter-spacing: 0.5px;
    }

    .nav-tabs .nav-link:hover:not(.active) {
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
    }

    .nav-tabs .nav-link.active {
        color: #fff;
        background-color: #1E88E5;
        /* Bright blue for active tab */
        border-bottom: 3px solid #FFD700;
        /* Gold underline */
        font-weight: 700;
    }

    /* Make Add Employee text more prominent */
    .add-employee-title {
        color: #FFD700;
        font-size: 2.5rem;
        text-align: center;
        margin-bottom: 2rem;
        position: relative;
        display: inline-block;
    }

    .add-employee-title:after {
        content: '';
        position: absolute;
        width: 100%;
        height: 3px;
        background-color: #FFD700;
        bottom: -10px;
        left: 0;
    }

    #image-preview {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border: 3px solid white;
        box-shadow: var(--box-shadow);
        transition: all 0.3s;
        background-color: #f1f5fe;
    }

    #image-preview:hover {
        transform: scale(1.05);
    }

    .section-title {
        font-size: 1.25rem;
        color: var(--primary-color);
        border-bottom: 1px solid #e3e6f0;
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
    }

    hr {
        border-top: 1px solid #e3e6f0;
        margin: 1.5rem 0;
    }

    .text-primary {
        color: var(--primary-color) !important;
    }

    .form-check-input {
        width: 1.25rem;
        height: 1.25rem;
        margin-top: 0.1rem;
    }

    .form-check-label {
        padding-left: 0.25rem;
    }

    .bank-row {
        background-color: rgba(248, 249, 252, 0.8);
        padding: 1rem;
        border-radius: var(--border-radius);
        border: 1px solid #e3e6f0;
        margin-bottom: 1rem;
        transition: all 0.3s;
    }

    .bank-row:hover {
        background-color: #f1f5fe;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    }

    .small,
    small {
        font-size: 80%;
        font-weight: 400;
        color: #858796;
    }

    .tab-content {
        padding: 1.5rem 0;
    }

    .alert {
        border-radius: var(--border-radius);
        border: none;
        box-shadow: var(--box-shadow);
    }

    .alert-danger {
        background-color: #fff5f5;
        border-left: 4px solid #e74a3b;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .nav-tabs .nav-link {
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }

        .btn {
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
        }

        .form-control,
        .form-select {
            padding: 0.6rem 0.75rem;
        }

        .card-body {
            padding: 1rem;
        }
    }

    @media (max-width: 576px) {
        .nav-tabs {
            display: flex;
            overflow-x: auto;
            flex-wrap: nowrap;
            padding-bottom: 5px;
        }

        .nav-tabs .nav-link {
            white-space: nowrap;
        }

        h1.text-center.text-warning {
            font-size: 1.75rem;
        }
    }
</style>


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


    function togglePassword() {
        var passwordField = $('#password');


        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');

        } else {
            passwordField.attr('type', 'password');

        }
    }


    // Load provinces but preserve existing selected values
    function loadProvinces(context = document) {
        $(context).find('.province-dropdown').each(function() {
            const dropdownElement = $(this);
            const selectedValue = dropdownElement.find('option:selected').val();

            // Only load provinces if not already populated with options beyond the default and selected option
            if (dropdownElement.find('option').length <= 2) {
                $.ajax({
                    url: 'https://alamat.thecloudalert.com/api/provinsi/get/',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.result) {
                            var options = '<option value="" disabled>Select Province</option>';

                            // Keep the currently selected option if it exists
                            if (selectedValue && selectedValue !== '') {
                                const existingOption = dropdownElement.find('option:selected');
                                if (existingOption.length) {
                                    options += `<option value="${selectedValue}" selected>${selectedValue}</option>`;
                                }
                            }

                            // Add all provinces from API
                            $.each(response.result, function(i, province) {
                                // Avoid duplicating the selected option
                                if (province.text !== selectedValue) {
                                    options += `<option value="${province.text}" data-id="${province.id}">${province.text}</option>`;
                                }
                            });

                            dropdownElement.html(options);

                            // If there's a selected value, load the corresponding cities
                            if (selectedValue) {
                                // Find the city dropdown ID
                                const card = dropdownElement.closest('.education-card, .training-card, .organization-card');
                                let cityDropdownId;

                                if (card.hasClass('education-card')) {
                                    const index = $('.education-card').index(card);
                                    cityDropdownId = 'educationCity' + (index + 1);
                                } else if (card.hasClass('training-card')) {
                                    const index = $('.training-card').index(card);
                                    cityDropdownId = 'trainingCity' + (index + 1);
                                } else if (card.hasClass('organization-card')) {
                                    const index = $('.organization-card').index(card);
                                    cityDropdownId = 'organizationCity' + (index + 1);
                                }

                                // Get the currently selected city
                                const cityDropdown = $('#' + cityDropdownId);
                                const selectedCity = cityDropdown.find('option:selected').val();

                                // Load cities for this province
                                if (cityDropdownId) {
                                    loadCities(selectedValue, cityDropdownId, selectedCity);
                                }
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading provinces:', error);
                    }
                });
            }
        });
    }

    // Function to set selected provinces in dropdowns
    function setSelectedProvinces() {
        // For education section
        @if(!empty($userEducation))
        @foreach($userEducation as $index => $education)
        // Langsung menggunakan text provinsi tanpa mapping
        var provinceDropdown = $('.education-card:eq({{ $index }}) .province-dropdown');
        provinceDropdown.val('{{ $education->educational_province }}');
        // Memanggil loadCities dengan text provinsi
        loadCities('{{ $education->educational_province }}', 'educationCity{{ $index + 1 }}', '{{ $education->educational_city }}');
        @endforeach
        @endif

        // For training section
        @if(!empty($userTraining))
        @foreach($userTraining as $index => $training)
        // Langsung menggunakan text provinsi tanpa mapping
        var provinceDropdown = $('.training-card:eq({{ $index }}) .province-dropdown');
        provinceDropdown.val('{{ $training->training_province }}');
        // Memanggil loadCities dengan text provinsi
        loadCities('{{ $training->training_province }}', 'trainingCity{{ $index + 1 }}', '{{ $training->training_city }}');
        @endforeach
        @endif

        // For organization section
        @if(!empty($userOrganization))
        @foreach($userOrganization as $index => $organization)
        // Langsung menggunakan text provinsi tanpa mapping
        var provinceDropdown = $('.organization-card:eq({{ $index }}) .province-dropdown');
        provinceDropdown.val('{{ $organization->province }}');
        // Memanggil loadCities dengan text provinsi
        loadCities('{{ $organization->province }}', 'organizationCity{{ $index + 1 }}', '{{ $organization->city }}');
        @endforeach
        @endif
    }

    // Load cities for a province and preserve any selected city
    function loadCities(provinceName, cityDropdownId, selectedCityText = null) {
        const cityDropdown = $('#' + cityDropdownId);

        // Store the currently selected city
        if (!selectedCityText) {
            selectedCityText = cityDropdown.find('option:selected').val();
        }

        // Find the province ID from the dropdown
        let provinceId = null;
        $('select.province-dropdown option').each(function() {
            if ($(this).val() === provinceName && $(this).data('id')) {
                provinceId = $(this).data('id');
                return false; // Break the loop once found
            }
        });

        // If we couldn't find the province ID in the options, we need to fetch all provinces
        if (!provinceId) {
            $.ajax({
                url: 'https://alamat.thecloudalert.com/api/provinsi/get/',
                type: 'GET',
                dataType: 'json',
                success: function(provinceResponse) {
                    if (provinceResponse.result) {
                        // Find province ID by name
                        $.each(provinceResponse.result, function(i, province) {
                            if (province.text === provinceName) {
                                provinceId = province.id;
                                return false; // Break the loop once found
                            }
                        });

                        // Now fetch cities with the found ID
                        if (provinceId) {
                            fetchCitiesWithProvinceId(provinceId, cityDropdownId, selectedCityText);
                        } else {
                            console.error('Could not find province ID for:', provinceName);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error finding province ID:', error);
                }
            });
        } else {
            // We already have the province ID, so fetch cities
            fetchCitiesWithProvinceId(provinceId, cityDropdownId, selectedCityText);
        }
    }

    // Helper function to fetch cities once we have the province ID
    function fetchCitiesWithProvinceId(provinceId, cityDropdownId, selectedCityText) {
        $.ajax({
            url: 'https://alamat.thecloudalert.com/api/kabkota/get/',
            type: 'GET',
            data: {
                d_provinsi_id: provinceId
            },
            dataType: 'json',
            success: function(response) {
                if (response.result) {
                    const cityDropdown = $('#' + cityDropdownId);
                    var options = '<option value="" disabled>Select City</option>';

                    // Keep the currently selected option if it exists
                    if (selectedCityText && selectedCityText !== '') {
                        const existingOption = cityDropdown.find('option:selected');
                        if (existingOption.length && existingOption.val() === selectedCityText) {
                            options += `<option value="${selectedCityText}" selected>${selectedCityText}</option>`;
                        } else {
                            options += `<option value="${selectedCityText}" selected>${selectedCityText}</option>`;
                        }
                    }

                    // Add all cities from API
                    $.each(response.result, function(i, city) {
                        // Avoid duplicating the selected option
                        if (city.text !== selectedCityText) {
                            options += `<option value="${city.text}">${city.text}</option>`;
                        }
                    });

                    cityDropdown.html(options);
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
    let rowCount = "{{ count($user_sim) }}";
    let selectedLanguages = [];





    $(document).ready(function() {
        // Validasi sebelum submit form

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




        loadProvinces();

        // Set up event listener for province selection
        $(document).on('change', '.province-dropdown', function() {
            var provinceId = $(this).find(':selected').data('id');
            var cityDropdown = $(this).closest('.row').find('.city-dropdown');

            // Kosongkan dropdown city terlebih dahulu
            cityDropdown.html('<option value="" disabled selected>Select City</option>');

            if (provinceId) {
                loadCities(provinceId, cityDropdown.attr('id'));
            }
        });


        // Handle transcript modal
        $(document).on('click', '.view-transcript', function(e) {
            e.preventDefault();
            const imgSrc = $(this).data("src");

            $('#transcriptImage').attr('src', imgSrc);
        });


        $('#elearningTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true
        });

        updateFamilyNumbers();
        updateEducationNumbers();
        updateWorkNumbers();
        toggleContractFields();
        updateTrainingIndices();
        updateOrganizationIndices();
        updateLanguageIndices();
        checkNoLicense();



        $('.language-select').on('change', function() {
            toggleOtherLanguageInput($(this));
        });

        // Initialize existing fields
        $('.language-select').each(function() {
            toggleOtherLanguageInput($(this));
        });

        updateLanguageOptions();

        loadProvinces();

        // Event listeners for province changes
        $(document).on('change', '.province-dropdown', function() {
            var provinceId = $(this).val();
            var cityDropdownId = $(this).closest('.row').find('.city-dropdown').attr('id');
            loadCities(provinceId, cityDropdownId);
        });


        // Inisialisasi berdasarkan nilai awal
        var initialPositionId = $('#position_id').val();
        updateDepartmentOptions(initialPositionId);

        // Event listener untuk perubahan position
        $('#position_id').change(function() {
            var positionId = $(this).val();
            updateDepartmentOptions(positionId);
        });


        function updateDepartmentOptions(positionId) {
            var positionText = $('#position_id option:selected').text().trim();
            var departmentSelect = $('#department_id');
            var departmentWrapper = departmentSelect.closest('.form-group');

            // Reset
            departmentWrapper.find('.text-danger').remove();
            departmentSelect.prop('disabled', false).find('option').show();

            // Remove any previous event handlers to prevent stacking
            departmentSelect.off('change.positionRestriction mousedown.positionRestriction');

            if (positionText === 'Director') {
                // Find Director department
                var directorDept = departmentSelect.find('option').filter(function() {
                    return $(this).text().trim() === 'Director';
                }).first();

                if (directorDept.length) {
                    // Set the value BUT DON'T DISABLE IT
                    departmentSelect.val(directorDept.val());

                    // Prevent changes with event handlers instead of disabled attribute
                    departmentSelect.on('change.positionRestriction mousedown.positionRestriction', function(e) {
                        e.preventDefault();
                        return false;
                    });

                    // Optional: Style to look disabled but actually still enabled
                    departmentSelect.css({
                        'background-color': '#e9ecef',
                        'pointer-events': 'none' // Makes it appear unclickable
                    });

                    departmentWrapper.append('<small class="text-danger">Department automatically set for Director</small>');
                }
            } else if (positionText === 'General Manager') {
                // Find General Manager department
                var gmDept = departmentSelect.find('option').filter(function() {
                    return $(this).text().trim() === 'General Manager';
                }).first();

                if (gmDept.length) {
                    // Set the value BUT DON'T DISABLE IT
                    departmentSelect.val(gmDept.val());

                    // Prevent changes with event handlers instead of disabled attribute
                    departmentSelect.on('change.positionRestriction mousedown.positionRestriction', function(e) {
                        e.preventDefault();
                        return false;
                    });

                    // Optional: Style to look disabled but actually still enabled
                    departmentSelect.css({
                        'background-color': '#e9ecef',
                        'pointer-events': 'none' // Makes it appear unclickable
                    });

                    departmentWrapper.append('<small class="text-danger">Department automatically set for General Manager</small>');
                }
            } else {
                // Reset the styling
                departmentSelect.css({
                    'background-color': '',
                    'pointer-events': ''
                });

                // For other positions, hide Director and General Manager options
                departmentSelect.find('option').each(function() {
                    var deptText = $(this).text().trim();
                    if (deptText === 'Director' || deptText === 'General Manager') {
                        $(this).hide();
                    }
                });

                // If current department is Director/GM, reset to empty
                var currentDeptText = departmentSelect.find('option:selected').text().trim();
                if (currentDeptText === 'Director' || currentDeptText === 'General Manager') {
                    departmentSelect.val('');
                }
            }
        }

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

        // Aktifkan input nomor SIM jika checkbox sudah dicentang saat halaman dimuat
        $(".license-checkbox").change(function() {
            let inputField = $(this).closest('.col-md-4').find('.license-number');
            inputField.prop("disabled", !$(this).is(":checked"));
            checkNoLicense();
        });

        // Jika "I do not have a driving license" dicentang, hapus centang dari semua SIM
        $("#noLicense").change(function() {
            if ($(this).is(":checked")) {
                $(".license-checkbox").prop("checked", false).trigger("change");
            }
        });




        $('#togglePasswordCheckbox').click(function() {
            togglePassword();
        });









        // Default profile image
        var defaultImage = "{{ asset('storage/default_profile.png') }}";
        var currentImage = "{{ $user->photo_profile_path ? asset('storage/' . $user->photo_profile_path) : '' }}";

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
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" placeholder="Phone Number" name="phone_number_family[]" value="">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender</label>
                                <select class="form-control" name="gender_family[]">
                                    <option selected disabled>Choose Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
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
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" placeholder="Address" name="address_family[]" value="">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">KTP Number</label>
                                <input type="text" class="form-control" placeholder="KTP Number" name="ID_number_family[]" value="">
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
                        <label class="form-label">Major</label>
                        <input type="text" class="form-control" name="major[]" placeholder="Major">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control education-start-date" name="start_education[]" value="">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control education-end-date" name="end_education[]" value="">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Grade</label>
                        <input type="text" class="form-control education-grade" name="grade[]" placeholder="Grade" value="">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Transcript</label>
                        <input type="file" class="form-control" name="education_transcript[]" accept="image/*">
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

            // Add your new card HTML here
            $('#educationContainer').append(newEducationCard);
            updateEducationNumbers();

            // This is the important part - only load provinces for the new card
            loadProvinces($('#educationContainer .education-card:last'));
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
                                            <input type="text" class="form-control" name="position_work[]" placeholder="Position" value="{{ old('position.0') }}">
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
            // existing code...
            $('#trainingContainer').append(newTrainingCard);
            updateTrainingIndices();
            loadProvinces($('#trainingContainer .training-card:last'));
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
            // existing code...
            $('#organizationContainer').append(newOrganizationCard);
            updateOrganizationIndices();
            loadProvinces($('#organizationContainer .organization-card:last'));
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





        // Show loading state on buttons
        $(document).on('submit', 'form', function() {
            $(this).find('button[type="submit"]').html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...'
            ).prop('disabled', true);
        });

        // Handle form submission with AJAX
        $('form').on('submit', function(e) {
            e.preventDefault();

            // Show processing Swal
            Swal.fire({
                title: 'Processing',
                html: 'Please wait while we save employee data...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Prepare form data
            let formData = new FormData(this);

            // Submit via AJAX
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = response.redirect;
                        }
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = 'Please check the form for errors';
                        // Highlight error fields
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            $(`[name="${key}"]`).addClass('is-invalid');
                            $(`[name="${key}"]`).after(`<div class="invalid-feedback">${value[0]}</div>`);
                        });
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage
                    });
                }
            });
        });





    });
</script>
@endpush