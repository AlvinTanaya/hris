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
@if ($user->id == Auth::user()->id)
<h1 class="mb-5 text-center text-warning">
    <i class="fas fa-user"></i> Profile
</h1>
@else
<h1 class="mb-5 text-center text-warning"><i class="far fa-user"></i> Employee Profile Information</h1>
@endif

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
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="elearningTab" data-bs-toggle="tab" href="#elearning" role="tab" aria-controls="elearning" aria-selected="false">E-learning</a>
        </li>
    </ul>
    <form action="{{ route('user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
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
                            <i class="fas fa-user-tie"></i> User Data
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
                                        src="{{ $user->photo_profile_path ? asset('storage/' . $user->photo_profile_path) : '#' }}"
                                        alt="Preview"
                                        class="rounded-circle border {{ $user->photo_profile_path ? '' : 'd-none' }}"
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
                                <label for="join_date" class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Join Date
                                </label>
                                <input type="date" class="form-control" id="join_date" name="join_date" value="{{ old('join_date',$user->join_date) }}" required>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user"></i> Name
                                </label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name',$user->name) }}" required>
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
                                <label for="position" class="form-label">
                                    <i class="fas fa-briefcase"></i> Position
                                </label>
                                <select class="form-control" id="position" name="position" required>
                                    <option selected disabled>Choose position</option>
                                    <option value="Director" {{ old('position',$user->position) == 'Director' ? 'selected' : '' }}>Director</option>
                                    <option value="General Manager" {{ old('position',$user->position) == 'General Manager' ? 'selected' : '' }}>General Manager</option>
                                    <option value="Manager" {{ old('position',$user->position) == 'Manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="Supervisor" {{ old('position',$user->position) == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>

                                    <option value="Staff" {{ old('position',$user->position) == 'Staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="department" class="form-label">
                                    <i class="fas fa-building"></i> Department
                                </label>
                                <select class="form-control" id="department" name="department" required>
                                    <option selected disabled>Choose Department</option>
                                    <option value="Director" {{ old('department', $user->department) == 'Director' ? 'selected' : '' }}>Director</option>
                                    <option value="General manager" {{ old('department') == 'General manager' ? 'selected' : '' }}>General manager</option>
                                    <option value="Human Resources" {{ old('department', $user->department) == 'Human Resources' ? 'selected' : '' }}>Human Resources</option>
                                    <option value="Finnance and Accounting" {{ old('department', $user->department) == 'Finnance and Accounting' ? 'selected' : '' }}>Finnance and Accounting</option>

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
                                    <option value="Full Time" {{ old('employee_status', $user->employee_status) == 'Full Time' ? 'selected' : '' }}>Full Time</option>
                                    <option value="Part Time" {{ old('employee_status', $user->employee_status) == 'Part Time' ? 'selected' : '' }}>Part Time</option>
                                    <option value="Contract" {{ old('employee_status', $user->employee_status) == 'Contract' ? 'selected' : '' }}>Contract</option>
                                    <option value="Inactive" {{ old('employee_status', $user->employee_status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
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
                                <input type="date" class="form-control" id="birth_date" name="birth_date" value="{{ old('birth_date', $user->birth_date) }}" required>
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
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone_number" class="form-label">
                                    <i class="fas fa-phone"></i> Phone Number
                                </label>
                                <input type="tel" placeholder="08XXXXXXXXXX" pattern="08[0-9]{10,12}" value="{{ old('phone_number', $user->phone_number) }}" class="form-control" id="phone_number" name="phone_number" required>

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
                            @if(empty($userFamily))
                            <!-- Initial empty card -->
                            <!-- <div class="card mb-3 family-card">
                                <div class="card-header bg-primary text-white">
                                    <span class="family-index fw-bold">Family #1</span>
                                </div>
                                <div class="card-body">

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Name</label>
                                            <input type="text" class="form-control" placeholder="Name" name="name_family[]" value="{{ old('name_family') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Relationship</label>
                                            <select class="form-select" name="relation[]" required>
                                                <option disabled value="">Select Relationship</option>
                                                <option value="Ayah">Ayah</option>
                                                <option value="Ibu">Ibu</option>
                                                <option value="Suami">Suami</option>
                                                <option value="Istri">Istri</option>
                                                <option value="Anak">Anak</option>
                                                <option value="Saudara">Saudara</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Birth Date</label>
                                            <input type="date" class="form-control" placeholder="Birth Date" name="birth_date_family[]" value="{{ old('birth_date_family') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Birth Place</label>
                                            <input type="text" class="form-control" placeholder="Birth Place" name="birth_place_family[]" value="{{ old('birth_place_family') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">KTP Number</label>
                                            <input type="text" class="form-control" placeholder="KTP Number" name="ID_number_family[]" value="{{ old('ID_number_family') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone Number</label>
                                            <input type="tel" placeholder="08XXXXXXXXXX" pattern="08[0-9]{10,12}" value"" class="form-control" name="phone_number_family[]">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Address</label>
                                            <input type="text" class="form-control" placeholder="Address" name="address_family[]" value="{{ old('address_family') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Gender</label>
                                            <select class="form-control" name="gender_family[]">
                                                <option selected disabled>Choose Gender</option>
                                                <option value="Male" {{ old('gender_family') == 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ old('gender_family') == 'Female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Job</label>
                                            <input type="text" class="form-control" placeholder="Job" name="job[]" value="{{ old('job') }}">
                                        </div>
                                    </div>
                                    <div class="text-end mt-3">
                                        <button type="button" class="btn btn-danger btn-sm remove-family-card">
                                            <i class="fas fa-trash me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            </div> -->
                            @else
                            <!-- Display existing family members -->
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
                                                <option value="Ayah" {{ old('gender_family', $family->relation)== 'Ayah' ? 'selected' : '' }}>Ayah</option>
                                                <option value="Ibu" {{ old('gender_family', $family->relation)== 'Ibu' ? 'selected' : '' }}>Ibu</option>
                                                <option value="Suami" {{ old('gender_family', $family->relation)== 'Suami' ? 'selected' : '' }}>Suami</option>
                                                <option value="Istri" {{ old('gender_family', $family->relation)== 'Istri' ? 'selected' : '' }}>Istri</option>
                                                <option value="Anak" {{ old('gender_family', $family->relation)== 'Anak' ? 'selected' : '' }}>Anak</option>
                                                <option value="Saudara" {{ old('gender_family', $family->relation)== 'Saudara' ? 'selected' : '' }}>Saudara</option>
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
                                            <label class="form-label">KTP Number</label>
                                            <input type="text" class="form-control" placeholder="KTP Number" name="ID_number_family[]" value="{{ old('ID_number_family', $family->ID_number) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone Number</label>
                                            <input type="tel" placeholder="08XXXXXXXXXX" pattern="08[0-9]{10,12}" value="{{ old('phone_number_family', $family->phone_number) }}" class="form-control" name="phone_number_family[]">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Address</label>
                                            <input type="text" class="form-control" placeholder="Address" name="address_family[]" value="{{ old('address_family', $family->address) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Gender</label>
                                            <select class="form-control" name="gender_family[]">
                                                <option selected disabled>Choose Gender</option>
                                                <option value="Male" {{ old('gender_family', $family->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ old('gender_family', $family->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                            </select>
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
                            @if(empty($userEducation))
                            <!-- Initial empty card -->
                            <!-- <div class="card mb-3 education-card">
                                <div class="card-header bg-primary text-white">
                                    <span class="education-index fw-bold">Education #1</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Degree</label>
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
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Educational Place</label>
                                            <input type="text" class="form-control" name="education_place[]" placeholder="Educational Place" value="{{ old('education_place.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">City</label>
                                            <input type="text" class="form-control" name="educational_city[]" placeholder="City" value="{{ old('educational_city.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Major</label>
                                            <input type="text" class="form-control" name="major[]" placeholder="Major" value="{{ old('major.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Start Date</label>
                                            <input type="date" class="form-control" name="start_education[]" value="{{ old('start_education.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">End Date</label>
                                            <input type="date" class="form-control" name="end_education[]" value="{{ old('end_education.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Grade</label>
                                            <input type="text" class="form-control" name="grade[]" placeholder="Grade" value="{{ old('grade.0') }}">
                                        </div>
                                    </div>
                                    <div class="text-end mt-3">
                                        <button type="button" class="btn btn-danger btn-sm remove-education-card">
                                            <i class="fas fa-trash me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            </div> -->
                            @else
                            <!-- Display existing education -->
                            @foreach($userEducation as $index => $education)
                            <div class="card mb-3 education-card">
                                <div class="card-header bg-primary text-white">
                                    <span class="education-index fw-bold">Education #{{ $index + 1 }}</span>
                                </div>

                                <div class="card-body">
                                    <div class="row g-3">
                                        <input type="text" class="form-control" name="id_education[]" value="{{ old('id_education', $education->id) }}" hidden>
                                        <div class="col-md-6">
                                            <label class="form-label">Degree</label>
                                            <select class="form-select" name="education_level[]">
                                                <option selected disabled> -- Choose Degree --</option>
                                                <option value="SD" {{ old('education_level', $education->degree) == 'SD' ? 'selected' : '' }}>SD</option>
                                                <option value="SMP" {{ old('education_level', $education->degree) == 'SMP' ? 'selected' : '' }}>SMP</option>
                                                <option value="SMA" {{ old('education_level', $education->degree) == 'SMA' ? 'selected' : '' }}>SMA</option>
                                                <option value="SMK" {{ old('education_level', $education->degree) == 'SMK' ? 'selected' : '' }}>SMK</option>
                                                <option value="D3" {{ old('education_level', $education->degree) == 'D3' ? 'selected' : '' }}>D3</option>
                                                <option value="S1" {{ old('education_level', $education->degree) == 'S1' ? 'selected' : '' }}>S1</option>
                                                <option value="S2" {{ old('education_level', $education->degree) == 'S2' ? 'selected' : '' }}>S2</option>
                                                <option value="S3" {{ old('education_level', $education->degree) == 'S3' ? 'selected' : '' }}>S3</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Educational Place</label>
                                            <input type="text" class="form-control" name="education_place[]" placeholder="Educational Place" value="{{ old('education_place.0', $education->educational_place) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">City</label>
                                            <input type="text" class="form-control" name="educational_city[]" placeholder="City" value="{{ old('educational_city.0', $education->educational_city) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Major</label>
                                            <input type="text" class="form-control" name="major[]" placeholder="Major" value="{{ old('major.0', $education->major) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Start Date</label>
                                            <input type="date" class="form-control" name="start_education[]" value="{{ old('start_education.0', $education->start_education) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">End Date</label>
                                            <input type="date" class="form-control" name="end_education[]" value="{{ old('end_education.0', $education->end_education) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Grade</label>
                                            <input type="text" class="form-control" name="grade[]" placeholder="Grade" value="{{ old('grade.0', $education->grade) }}">
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
                            @if(empty($userWork))
                            <!-- Initial empty card -->
                            <!-- <div class="card mb-3 work-card">
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
                                            <input type="date" class="form-control" name="start_work[]" value="{{ old('start_work.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">End Date</label>
                                            <input type="date" class="form-control" name="end_work[]" value="{{ old('end_work.0') }}">
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
                                            <textarea class="form-control" name="job_desc[]" placeholder="Job Description" rows="2">{{ old('job_desc.0') }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Reason for Leaving</label>
                                            <textarea class="form-control" name="reason[]" placeholder="Reason for Leaving" rows="2">{{ old('reason.0') }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Benefits</label>
                                            <textarea class="form-control" name="benefit[]" placeholder="Benefits" rows="2">{{ old('benefit.0') }}</textarea>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">Facilities</label>
                                            <textarea class="form-control" name="facility[]" placeholder="Facilities" rows="2">{{ old('facility.0') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="text-end mt-3">
                                        <button type="button" class="btn btn-danger btn-sm remove-work-card">
                                            <i class="fas fa-trash me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            </div> -->
                            @else
                            <!-- Display existing work experience -->
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
                            @if(empty($userLanguage))
                            <!-- Initial empty card -->
                            <!-- <div class="card mb-3 language-card">
                                <div class="card-header bg-primary text-white">
                                    <span class="language-index fw-bold">Language #1</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Language</label>
                                            <input type="text" class="form-control" name="language[]" placeholder="Language" value="{{ old('language.0') }}">
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
                            </div> -->
                            @else
                            <!-- Display existing language -->
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
                                            <input type="text" class="form-control" name="language[]" placeholder="Language" value="{{ old('language.0', $language->language) }}">
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
                            @if(empty($userTraining))
                            <!-- Initial empty card -->
                            <!-- <div class="card mb-3 training-card">
                                <div class="card-header bg-primary text-white">
                                    <span class="training-index fw-bold">Training #1</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Training Name</label>
                                            <input type="text" class="form-control" name="training_name[]" placeholder="Training Name" value="{{ old('training_name.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">City</label>
                                            <input type="text" class="form-control" name="training_city[]" placeholder="City" value="{{ old('training_city.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Start Date</label>
                                            <input type="date" class="form-control" name="training_start_date[]" value="{{ old('training_start_date.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">End Date</label>
                                            <input type="date" class="form-control" name="training_end_date[]" value="{{ old('training_end_date.0') }}">
                                        </div>
                                    </div>
                                    <div class="text-end mt-3">
                                        <button type="button" class="btn btn-danger btn-sm remove-training-card">
                                            <i class="fas fa-trash me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            </div> -->
                            @else
                            <!-- Display existing training -->
                            @foreach($userTraining as $index => $training)
                            <div class="card mb-3 training-card">
                                <div class="card-header bg-primary text-white">
                                    <span class="training-index fw-bold">Training #{{ $index + 1 }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <input type="text" class="form-control" name="id_training[]" value="{{ old('id_training', $training->id) }}" hidden>
                                        <div class="col-md-6">
                                            <label class="form-label">Training Name</label>
                                            <input type="text" class="form-control" name="training_name[]" placeholder="Training Name" value="{{ old('training_name.0', $training->training_name) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">City</label>
                                            <input type="text" class="form-control" name="training_city[]" placeholder="City" value="{{ old('training_city.0', $training->training_city) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Start Date</label>
                                            <input type="date" class="form-control" name="training_start_date[]" value="{{ old('training_start_date.0', $training->start_date) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">End Date</label>
                                            <input type="date" class="form-control" name="training_end_date[]" value="{{ old('training_end_date.0', $training->end_date) }}">
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
                            @if(empty($userOrganization))
                            <!-- Initial empty card -->
                            <!-- <div class="card mb-3 organization-card">
                                <div class="card-header bg-primary text-white">
                                    <span class="organization-index fw-bold">Organization #1</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Organization Name</label>
                                            <input type="text" class="form-control" name="organization_name[]" placeholder="Organization Name" value="{{ old('organization_name.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Activity Type</label>
                                            <input type="text" class="form-control" name="activity_type[]" placeholder="Activity Type" value="{{ old('activity_type.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Position</label>
                                            <input type="text" class="form-control" name="position_work[]" placeholder="Position" value="{{ old('position.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">City</label>
                                            <input type="text" class="form-control" name="organization_city[]" placeholder="City" value="{{ old('organization_city.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Start Date</label>
                                            <input type="date" class="form-control" name="organization_start_date[]" value="{{ old('organization_start_date.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">End Date</label>
                                            <input type="date" class="form-control" name="organization_end_date[]" value="{{ old('organization_end_date.0') }}">
                                        </div>
                                    </div>
                                    <div class="text-end mt-3">
                                        <button type="button" class="btn btn-danger btn-sm remove-organization-card">
                                            <i class="fas fa-trash me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            </div> -->
                            @else
                            <!-- Display existing organization -->
                            @foreach($userOrganization as $index => $organization)
                            <div class="card mb-3 organization-card">
                                <div class="card-header bg-primary text-white">
                                    <span class="organization-index fw-bold">Organization #{{ $index + 1 }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <input type="text" class="form-control" name="id_organization[]" value="{{ old('id_organization', $organization->id) }}" hidden>
                                        <div class="col-md-6">
                                            <label class="form-label">Organization Name</label>
                                            <input type="text" class="form-control" name="organization_name[]" placeholder="Organization Name" value="{{ old('organization_name.0', $organization->organization_name) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Activity Type</label>
                                            <textarea class="form-control list-textarea" name="activity_type[]" placeholder="- Activity Type" rows="2">{{ old('activity_type.0', isset($organization) ? "- " . str_replace(";", " ", $organization->activity_type) : '') }}</textarea>
                                        </div>


                                        <div class="col-md-6">
                                            <label class="form-label">Position</label>
                                            <input type="text" class="form-control" name="organization_position[]" placeholder="Position" value="{{ old('position.0', $organization->position) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">City</label>
                                            <input type="text" class="form-control" name="organization_city[]" placeholder="City" value="{{ old('organization_city.0', $organization->city) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Start Date</label>
                                            <input type="date" class="form-control" name="organization_start_date[]" value="{{ old('organization_start_date.0', $organization->start_date) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">End Date</label>
                                            <input type="date" class="form-control" name="organization_end_date[]" value="{{ old('organization_end_date.0', $organization->end_date) }}">
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



    $(document).ready(function() {

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



        function checkNoLicense() {
            let anyChecked = $(".license-checkbox:checked").length > 0;
            $("#noLicense").prop("checked", !anyChecked);
        }

        // Aktifkan input nomor SIM jika checkbox sudah dicentang saat halaman dimuat
        $(".license-checkbox").each(function() {
            let inputField = $(this).closest('.col-md-4').find('.license-number');
            inputField.prop("disabled", !$(this).is(":checked"));
        });

        // Cek "I do not have a driving license" pada awal
        checkNoLicense();

        // Event saat checkbox SIM diklik
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
                                    <option disabled value="">Select Relationship</option>
                                    <option value="Ayah">Ayah</option>
                                    <option value="Ibu">Ibu</option>
                                    <option value="Suami">Suami</option>
                                    <option value="Istri">Istri</option>
                                    <option value="Anak">Anak</option>
                                    <option value="Saudara">Saudara</option>
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
                                <input type="tel" placeholder="08XXXXXXXXXX" pattern="08[0-9]{10,12}" value"" class="form-control" name="phone_number_family[]">
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

        // Education Card Management
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
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Educational Place</label>
                                <input type="text" class="form-control" name="education_place[]" placeholder="Educational Place">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" name="educational_city[]" placeholder="City">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Major</label>
                                <input type="text" class="form-control" name="educational_major[]" placeholder="Major">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_education[]" value="">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_education[]" value="">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Grade</label>
                                <input type="text" class="form-control" name="grade[]" placeholder="Grade" value="">
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

        });

        // Remove education card
        $(document).on('click', '.remove-education-card', function() {
            $(this).closest('.education-card').remove();
            updateEducationNumbers();

        });


        $(document).on('input', '.graduation-year', function() {
            let year = parseInt($(this).val());
            let currentYear = new Date().getFullYear();
            let minYear = 1900;

            if (year < minYear || year > currentYear) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Graduation Year',
                    text: `Graduation year must be between ${minYear} and ${currentYear}.`,
                });
                $(this).val('');
            }
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
                                            <input type="date" class="form-control" name="start_work[]" value="{{ old('start_work.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">End Date</label>
                                            <input type="date" class="form-control" name="end_work[]" value="{{ old('end_work.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Company Address</label>
                                            <input type="text" class="form-control" name="company_address[]" placeholder="Company Address" value="{{ old('company_address.0') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Company Phone</label>
                                            <input type="tel" placeholder="08XXXXXXXXXX" pattern="08[0-9]{10,12}" value="{{ old('company_phone.0') }}" class="form-control" name="company_phone[]">
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
                                <input type="text" class="form-control" name="language[]" placeholder="Language" value="">
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

            // Update language indices
            updateLanguageIndices();
        });

        // Remove language card
        $(document).on('click', '.remove-language-card', function() {
            $(this).closest('.language-card').remove();

            // Update language indices
            updateLanguageIndices();
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
                            <div class="col-md-6">
                                <label class="form-label">Training Name</label>
                                <input type="text" class="form-control" name="training_name[]" placeholder="Training Name" value="">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" name="training_city[]" placeholder="City" value="">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="training_start_date[]" value="">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" name="training_end_date[]" value="">
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
        });

        // Remove training card
        $(document).on('click', '.remove-training-card', function() {
            $(this).closest('.training-card').remove();

            // Update training indices
            updateTrainingIndices();
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
                    <label class="form-label">Activity Type</label>
                    <textarea class="form-control list-textarea" name="activity_type[]" placeholder="- Activity Type" rows="2"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Position</label>
                    <input type="text" class="form-control" name="organization_position[]" placeholder="Position" value="">
                </div>
                <div class="col-md-6">
                    <label class="form-label">City</label>
                    <input type="text" class="form-control" name="organization_city[]" placeholder="City" value="">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" name="organization_start_date[]" value="">
                </div>
                <div class="col-md-6">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" name="organization_end_date[]" value="">
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

            // Update organization indices
            updateOrganizationIndices();
        });

        // Remove organization card
        $(document).on('click', '.remove-organization-card', function() {
            $(this).closest('.organization-card').remove();

            // Update organization indices
            updateOrganizationIndices();
        });













        $('#togglePasswordCheckbox').click(function() {
            togglePassword();
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

    function togglePassword() {
        var passwordField = $('#password');


        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');

        } else {
            passwordField.attr('type', 'password');

        }
    }
</script>
@endpush