<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application Form</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/IconTimurJayaIndosteel.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">


    <style>
        :root {
            --primary: #0a2463;
            --primary-light: #1d52d1;
            --secondary: #247ba0;
            --accent: #3da5d9;
            --light: #f5f9ff;
            --dark: #0a1128;
            --success: #06d6a0;
            --warning: #ffd166;
            --danger: #ef476f;
        }

        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary), var(--secondary), var(--primary-light));
            background-attachment: fixed;
            color: #ffffff;
        }

        .container {
            padding: 2rem;
        }

        .back-btn {
            position: relative;
            overflow: hidden;
            z-index: 1;
            margin-bottom: 2rem;
        }

        .back-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
            z-index: -1;
        }

        .back-btn:hover::before {
            left: 100%;
        }

        .form-section {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 2rem;
            margin: 2rem auto;
            max-width: 1200px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
        }

        .form-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(to bottom, var(--accent), transparent);
        }

        .profile-section {
            text-align: center;
            padding: 2rem 0;
        }

        .profile-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            overflow: hidden;
            border: 3px solid var(--warning);
            box-shadow: 0 0 20px rgba(255, 209, 102, 0.3);
        }

        .profile-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .section-title {
            color: var(--warning);
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid rgba(255, 209, 102, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-label {
            color: #ffffff;
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-control,
        .form-select {
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(61, 165, 217, 0.3);
            color: #ffffff;
            font-weight: 600;
            backdrop-filter: blur(5px);
            border-radius: 10px;
            padding: 0.7rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(13, 54, 145, 0.6);
            border-color: var(--accent);
            color: white;
            box-shadow: 0 0 0 0.25rem rgba(61, 165, 217, 0.25);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .input-group-text {
            background: rgba(61, 165, 217, 0.5);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-add-row {
            background: linear-gradient(45deg, var(--accent), #5fb8e6);
            color: var(--dark);
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(61, 165, 217, 0.4);
        }

        .btn-add-row:hover {
            background: linear-gradient(45deg, #5fb8e6, var(--accent));
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(61, 165, 217, 0.5);
        }

        .btn-remove {
            color: var(--danger);
            background: none;
            border: none;
            padding: 0.25rem 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-remove:hover {
            color: #ff0000;
            transform: scale(1.1);
        }

        .file-preview,
        .cv-preview {
            margin-top: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .cv-preview {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .cv-preview i {
            font-size: 2rem;
            color: var(--warning);
        }

        .experience-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: visible;
            /* Changed from overflow: hidden to show the card number */
        }

        .experience-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .experience-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(to bottom, var(--accent), transparent);
        }

        .card-number {
            position: absolute;
            top: -15px;
            left: -15px;
            background: var(--warning);
            color: var(--dark);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 10;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-left: 20px;
        }

        .card-header h4 {
            color: var(--warning);
            font-weight: 700;
            margin: 0;
        }

        .section-container {
            position: relative;
        }

        .documents-section .upload-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
            height: 100%;
        }

        .documents-section .upload-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .documents-section .upload-card i {
            font-size: 24px;
            color: var(--warning);
            margin-bottom: 10px;
        }

        .documents-section .upload-card h5 {
            color: var(--warning);
            font-weight: 600;
        }

        .select2-container .select2-selection--single {
            height: 100%;
            padding: 0.7rem 1rem;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background-color: rgba(61, 165, 217, 0.3);
            color: #ffffff;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: normal;
            color: #ffffff;
            font-weight: 600;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: auto;
            display: flex;
            align-items: center;
        }

        .select2-dropdown {
            background-color: rgba(10, 36, 99, 0.9);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .select2-results__option {
            color: #ffffff;
            padding: 0.5rem 1rem;
        }

        .select2-results__option--highlighted {
            background-color: var(--accent);
            color: var(--dark);
        }

        .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: rgba(61, 165, 217, 0.5);
        }

        .invalid-feedback {
            color: var(--danger);
            font-weight: 500;
        }

        .is-invalid {
            border-color: var(--danger) !important;
        }

        .is-invalid:focus {
            box-shadow: 0 0 0 0.25rem rgba(239, 71, 111, 0.25);
        }

        .list-textarea {
            background: rgba(61, 165, 217, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 10px;
        }

        .list-textarea:focus {
            background: rgba(61, 165, 217, 0.3);
            color: white;
        }

        @media (max-width: 768px) {
            .form-section {
                padding: 1.5rem;
            }

            .profile-preview {
                width: 120px;
                height: 120px;
            }
        }





        /* Header Styling to Match Your Existing Theme */
        .header-section {
            position: relative;
            padding: 2rem;
            margin: 2rem auto;
            overflow: hidden;
            background: linear-gradient(135deg, var(--primary-light), var(--accent));
            border-radius: 16px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            max-width: 1200px;
        }

        .header-content {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-left {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            background-color: var(--danger);
            color: white;
            padding: 0.7rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(239, 71, 111, 0.4);
            position: relative;
            overflow: hidden;
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 71, 111, 0.5);
            color: white;
        }

        .back-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
            z-index: -1;
        }

        .back-button:hover::before {
            left: 100%;
        }

        .header-title h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: white;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            letter-spacing: 1px;
        }

        .header-title p {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 400;
            max-width: 600px;
        }

        .header-graphic {
            position: relative;
            width: 200px;
            height: 200px;
        }

        .header-circle {
            position: absolute;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            top: 0;
            right: 0;
            animation: pulse 4s infinite alternate;
        }

        .header-circle-sm {
            position: absolute;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.3);
            bottom: 20px;
            left: 20px;
            animation: pulse 3s infinite alternate-reverse;
        }

        .header-line {
            position: absolute;
            height: 3px;
            width: 120px;
            background: linear-gradient(90deg, transparent, var(--warning), transparent);
            top: 50%;
            right: 30px;
            transform: rotate(-45deg);
        }

        .header-wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='0.05' d='M0,224L48,224C96,224,192,224,288,208C384,192,480,160,576,165.3C672,171,768,213,864,218.7C960,224,1056,192,1152,170.7C1248,149,1344,139,1392,133.3L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E") repeat-x;
            background-size: 1440px 50px;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.7;
            }

            100% {
                transform: scale(1.1);
                opacity: 0.5;
            }
        }

        /* Make sure the required documents title is properly aligned */
        .section-title {
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .header-section {
                padding: 2rem 1.5rem 3rem;
            }

            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .header-left {
                align-items: center;
            }

            .header-title h1 {
                font-size: 2rem;
            }

            .header-graphic {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="container py-5 mx-auto">
        <!-- Header Section - Matching Your Existing Style -->
        <div class="header-section">
            <div class="header-content">
                <div class="header-left">
                    <a href="{{ route('job_vacancy.index') }}" class="back-button">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                    <div class="header-title">
                        <h1>Career Application Portal</h1>
                        <p>Complete your profile and submit required documents</p>
                    </div>
                </div>
                <div class="header-graphic">
                    <div class="header-circle"></div>
                    <div class="header-circle-sm"></div>
                    <div class="header-line"></div>
                </div>
            </div>
            <div class="header-wave"></div>
        </div>

        <form id="jobApplicationForm" action="{{ route('job_vacancy.store', $demand->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Profile Section -->
            <div class="form-section documents-section">
                <h3 class="section-title">Required Documents</h3>
                <div class="profile-preview">
                    <img id="profilePreview" src="{{ asset('storage/default_profile.png') }}" alt="Profile Preview">
                </div>
                <div class="row g-3">
                    <div class="col-md-12 mx-auto">
                        <label class="form-label">Profile Photo</label>
                        <input type="file" class="form-control @error('photo_profile_path') is-invalid @enderror"
                            name="photo_profile_path" accept="image/*" onchange="previewImage(this)" required>
                        @error('photo_profile_path')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row g-3 mt-4">
                    <div class="col-md-4 mx-auto">
                        <div class="upload-card text-center">
                            <i class="fas fa-file-pdf"></i>
                            <h5 class="text-warning mb-3">CV (PDF)</h5>
                            <input type="file" class="form-control @error('cv_path') is-invalid @enderror"
                                name="cv_path" accept=".pdf" onchange="previewCV(this)" required>
                            @error('cv_path')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="cvPreview" class="mt-3" style="display: none;">
                                <span id="cvFileName" class="mt-3"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mx-auto">
                        <div class="upload-card text-center">
                            <i class="fas fa-id-card"></i>
                            <h5 class="text-warning mb-3">ID Card</h5>
                            <input type="file" class="form-control" name="ID_card_path" accept=".jpg,.jpeg,.png" onchange="previewIDCard(this)" required>
                            <div id="idCardPreview" class="mt-3" style="display: none;">
                                <span id="idCardFileName"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mx-auto">
                        <div class="upload-card text-center">
                            <i class="fas fa-trophy"></i>
                            <h5 class="text-warning mb-3">Achievements (PDF)</h5>
                            <input type="file" class="form-control" name="achievement_path" accept=".pdf" onchange="handleAchievementFiles(this)">
                            <div id="achievementPreview"></div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Expectations -->
            <div class="form-section">
                <h3 class="section-title">Salary & Benefits Expectations</h3>
                <div class="row g-3">
                    <div class="col-md-12 mx-auto">
                        <label class="form-label">Expected Salary</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" name="expected_salary" placeholder="5000000" required>
                        </div>
                        <small class="text-white">Masukkan gaji yang diharapkan tanpa "." atau ",".</small>
                    </div>


                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6 mx-auto">
                        <label class="form-label">Expected Benefits</label>
                        <textarea class="form-control list-textarea" name="expected_benefits" rows="3" placeholder="- List your expected benefits"></textarea>
                    </div>
                    <div class="col-md-6 mx-auto">
                        <label class="form-label">Expected Facilities</label>
                        <textarea class="form-control list-textarea" name="expected_facilities" rows="3" placeholder="- List your expected facilities"></textarea>
                    </div>
                </div>
            </div>


            <!-- Personal Information -->
            <div class="form-section">
                <h3 class="section-title">Personal Information</h3>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" placeholder="08XXXXXXXXXX" pattern="08[0-9]{10,12}" class="form-control" name="phone_number" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Emergency Contact</label>
                        <input type="tel" placeholder="08XXXXXXXXXX" pattern="08[0-9]{10,12}" class="form-control" name="emergency_contact" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NIK (KTP)</label>
                        <input type="text" class="form-control" name="ID_number" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Religion</label>
                        <select class="form-select" name="religion" required>
                            <option value="" disabled>Select Religion</option>
                            <option value="Islam">Islam</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Kristen">Kristen</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Konghucu">Konghucu</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gender</label>
                        <select class="form-select" name="gender" required>
                            <option value="" disabled>Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="blood_type" class="form-label">
                            <i class="fas fa-tint"></i> Blood Type
                        </label>
                        <select class="form-control" id="blood_type" name="blood_type">
                            <option selected disabled>Choose Blood Type</option>
                            @foreach(['-','A', 'B', 'AB', 'O'] as $blood)
                            <option value="{{ $blood }}">
                                {{ $blood }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Place of Birth</label>
                        <input type="text" class="form-control" name="birth_place" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" name="birth_date" required>
                    </div>



                    <div class="col-md-6">
                        <label class="form-label">ID Card Address</label>
                        <textarea class="form-control" name="ID_address" rows="3" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Current Address</label>
                        <textarea class="form-control" name="domicile_address" rows="3" required></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Distance Between Domicile Address to Company Location</label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="distance" id="distance" min="0" max="30" step="0.01" required>
                            <span class="input-group-text">KM</span>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <label class="form-label">Weight (kg)</label>
                        <input type="number" class="form-control" name="weight">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Height (cm)</label>
                        <input type="number" class="form-control" name="height">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">BPJS Health Insurance (Optional)</label>
                        <input type="text" class="form-control" name="bpjs_health">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">BPJS Employment Insurance (Optional)</label>
                        <input type="text" class="form-control" name="bpjs_employment">
                    </div>


                </div>



            </div>


            <!-- Driving License Section -->
            <div class="form-section">
                <h3 class="section-title">Driving License</h3>
                <div class="row g-3">
                    @php
                    $licenses = [
                    'A' => 'SIM A (Car)',
                    'B' => 'SIM B (Commercial Car)',
                    'C' => 'SIM C (Motorcycle)'
                    ];
                    @endphp

                    @foreach ($licenses as $key => $label)
                    <div class="col-md-4">
                        <div class="card bg-transparent border-light mb-3">
                            <div class="card-body">
                                <h5 class="card-title text-warning">{{ $label }}</h5>
                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input license-checkbox"
                                        type="checkbox"
                                        id="hasLicense{{ $key }}"
                                        name="sim[]"
                                        value="{{ $key }}"
                                        {{ in_array($key, old('sim', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label text-white" for="hasLicense{{ $key }}">
                                        I have a {{ strtolower($label) }}
                                    </label>
                                </div>
                                <input
                                    type="text"
                                    class="form-control license-number"
                                    name="sim_number[{{ $key }}]"
                                    placeholder="License number (if applicable)"
                                    value="{{ old('sim_number.' . $key) }}"
                                    {{ in_array($key, old('sim', [])) ? '' : 'disabled' }}>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <!-- No License Option -->
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="noLicense" name="no_license">
                            <label class="form-check-label fw-bold text-white" for="noLicense">I do not have a driving license</label>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Education -->
            <div class="form-section">
                <h3 class="section-title">Education History</h3>
                <div id="educationContainer" class="section-container">
                    <!-- Education cards will be added here -->
                </div>
                <button type="button" class="btn btn-add-row" onclick="addEducation()">
                    <i class="fas fa-plus"></i> Add Education
                </button>
            </div>

            <!-- traning -->
            <div class="form-section">
                <h3 class="section-title">Training History</h3>
                <div id="trainingContainer" class="section-container">
                    <!-- traning cards will be added here -->
                </div>
                <button type="button" class="btn btn-add-row" onclick="addTraning()">
                    <i class="fas fa-plus"></i> Add Traning
                </button>
            </div>

            <!-- Family Information -->
            <div class="form-section">
                <h3 class="section-title">Family Information</h3>
                <div id="familyContainer" class="section-container">
                    <!-- Family cards will be added here -->
                </div>
                <button type="button" class="btn btn-add-row" onclick="addFamily()">
                    <i class="fas fa-plus"></i> Add Family Member
                </button>
            </div>

            <!-- Language -->
            <div class="form-section">
                <h3 class="section-title">Language Proficiency</h3>
                <div id="languageContainer" class="section-container">
                    <!-- Language cards will be added here -->
                </div>
                <button type="button" class="btn btn-add-row" onclick="addLanguage()">
                    <i class="fas fa-plus"></i> Add Language
                </button>
            </div>

            <!-- Organization -->
            <div class="form-section">
                <h3 class="section-title">Organizational Experience</h3>
                <div id="organizationContainer" class="section-container">
                    <!-- Organization cards will be added here -->
                </div>
                <button type="button" class="btn btn-add-row" onclick="addOrganization()">
                    <i class="fas fa-plus"></i> Add Organization
                </button>
            </div>



            <!-- Work Experience -->
            <div class="form-section">
                <h3 class="section-title">Work Experience</h3>
                <div id="workExperienceContainer" class="section-container">
                    <!-- Work experience cards will be added here -->
                </div>
                <button type="button" class="btn btn-add-row" onclick="addWorkExperience()">
                    <i class="fas fa-plus"></i> Add Work Experience
                </button>
            </div>




            <div class="form-section text-center">
                <H1 class="text-center text-warning">
                    <i class="fa-solid fa-triangle-exclamation"></i> Attention!
                </H1>
                <small class="d-block mt-2 text-white">
                    After submitting your application, you will receive a notification via email.
                    Please make sure to enter your email correctly to avoid missing important information.
                </small>
                <small class="d-block mt-1 text-white">
                    You can only submit the form once.
                    Please double-check all the information before submitting to ensure accuracy.
                </small>

                <button type="submit" class="btn btn-add-row btn-lg mt-4">
                    Submit Application
                </button>
            </div>

        </form>
    </div>


</body>

</html>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Checkbox handling
        const $checkboxes = $(".license-checkbox");
        const $licenseNumbers = $(".license-number");
        const $noLicenseCheckbox = $("#noLicense");

        $checkboxes.on("change", function() {
            $(this).closest(".card-body").find(".license-number").prop("disabled", !$(this).is(":checked"));

            if ($(this).is(":checked")) {
                $noLicenseCheckbox.prop("checked", false);
            }
        });

        $noLicenseCheckbox.on("change", function() {
            if ($(this).is(":checked")) {
                $checkboxes.prop("checked", false);
                $licenseNumbers.prop("disabled", true);
            }
        });


        // Initial cards
        if ($('#educationContainer').children().length === 0) addEducation();
        if ($('#familyContainer').children().length === 0) addFamily();
        if ($('#languageContainer').children().length === 0) addLanguage();


        // Ensure province dropdowns are loaded for any existing cards
        $('.province-dropdown').each(function() {
            loadProvinces($(this));
        });
        // Set up province change handlers
        $(document).on('change', '.province-dropdown', function() {
            // Get the province ID from the data attribute of the selected option
            const provinceId = $(this).find('option:selected').data('id');

            // Find the associated city dropdown
            const $cityDropdown = $(this).closest('.row').find('.city-dropdown');

            if (provinceId) {
                loadCities(provinceId, $cityDropdown);
            }
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



        // Form submission
        $('#jobApplicationForm').on('submit', function(e) {
            e.preventDefault();

            // Validation checks
            if ($('#educationContainer').children().length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Education Record',
                    text: 'Please add at least one education record before submitting.'
                });
                return;
            }

            if ($('#familyContainer').children().length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Family Record',
                    text: 'Please add at least one family record before submitting.'
                });
                return;
            }

            if ($('#languageContainer').children().length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Language Record',
                    text: 'Please add at least one language record before submitting.'
                });
                return;
            }

            // Similar checks for family and language...

            Swal.fire({
                title: 'Are you sure?',
                text: 'Make sure all your information is correct before submitting.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, submit!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Submitted!',
                        text: 'Your application has been successfully submitted.',
                        showConfirmButton: false,
                        timer: 2000
                    });

                    setTimeout(() => {
                        $(this).unbind('submit').submit();
                    }, 2000);
                }
            });



            // Grade input validation based on education level
            $(document).on('change', '.education-level', function() {
                const level = $(this).val();
                const $gradeInput = $(this).closest('.row').find('.grade-input');

                if (['SMA', 'SMK'].includes(level)) {
                    $gradeInput.attr({
                        'min': '0',
                        'max': '100',
                        'step': '0.01',
                        'placeholder': 'Enter grade (0-100)'
                    });
                } else if (['D3', 'S1', 'S2'].includes(level)) {
                    $gradeInput.attr({
                        'min': '0',
                        'max': '4',
                        'step': '0.01',
                        'placeholder': 'Enter GPA (0-4)'
                    });
                }
            });

            // Validate grade input values
            $(document).on('input', '.grade-input', function() {
                const level = $(this).closest('.row').find('.education-level').val();
                const value = parseFloat($(this).val());

                if (['SMA', 'SMK'].includes(level)) {
                    if (value < 0 || value > 100) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Grade',
                            text: 'Grade must be between 0 and 100!',
                        });
                        $(this).val('');
                    }
                } else if (['D3', 'S1', 'S2'].includes(level)) {
                    if (value < 0 || value > 4) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid GPA',
                            text: 'GPA must be between 0 and 4!',
                        });
                        $(this).val('');
                    }
                }
            });

            // Initialize language dropdowns for any existing cards
            updateLanguageDropdowns();



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


        $("#cityDropdown").select2({

            allowClear: true,
            width: '100%',

        });




    });



    // File preview functions
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Image size should not exceed 2MB!',
                });
                $(input).val('');
                return;
            }

            if (!file.type.match('image.*')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Please select an image file!',
                });
                $(input).val('');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                $('#profilePreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    }

    function previewIDCard(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB

            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'ID Card size should not exceed 5MB!',
                });
                $(input).val('');
                return;
            }

            const allowedTypes = ['image/jpeg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Please select a JPG or PNG file!',
                });
                $(input).val('');
                return;
            }

            $('#idCardFileName').html('<i class="fas fa-file me-2"></i>' + file.name);
            $('#idCardPreview').show();
        }
    }

    function previewCV(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB

            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'CV size should not exceed 5MB!',
                });
                $(input).val('');
                return;
            }

            if (file.type !== 'application/pdf') {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Please select a PDF file!',
                });
                $(input).val('');
                return;
            }

            $('#cvFileName').html('<i class="fas fa-file me-2"></i>' + file.name);
            $('#cvPreview').show();
        }
    }

    function handleAchievementFiles(input) {
        const $preview = $('#achievementPreview');
        $preview.empty();

        if (input.files.length > 0) {
            const $fileList = $('<div>').addClass('mt-3');

            $.each(input.files, function(index, file) {
                const $fileItem = $('<div>')
                    .addClass('text-light')
                    .html('<i class="fas fa-file me-2"></i>' + file.name);
                $fileList.append($fileItem);
            });

            $preview.append($fileList);
        }
    }

    // Update card numbers
    function updateCardNumbers(containerId) {
        $(`#${containerId} .experience-card`).each(function(index) {
            $(this).find('.card-number').text(index + 1);
        });
    }

    // Remove card function
    function removeCard(button, containerId, callback) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $(button).closest('.experience-card').remove();
                updateCardNumbers(containerId);

                // Call the callback function if provided (for language dropdown updates)
                if (typeof callback === 'function') {
                    callback();
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'The item has been removed.',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    }

    // Modified addEducation function
    function addEducation() {
        const $container = $('#educationContainer');
        const cardCount = $container.children().length + 1;
        const $card = $('<div>').addClass('experience-card');
        const cardId = `education-${cardCount}`;

        $card.html(`
    <div class="card-number">${cardCount}</div>
    <div class="card-header">
        <h4 class="text-warning m-0">Education #${cardCount}</h4>
        <button type="button" class="btn-remove" onclick="removeCard(this, 'educationContainer')">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Level</label>
            <select class="form-select education-level" name="degree[]" required>
                <option value="" selected disabled>Select Level</option>
                <option value="SMA">SMA</option>
                <option value="SMK">SMK</option>
                <option value="D3">D3</option>
                <option value="S1">S1</option>
                <option value="S2">S2</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Institution Name</label>
            <input type="text" class="form-control" name="educational_place[]" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Province</label>
            <select class="form-select province-dropdown" id="edu-province-${cardCount}" name="education_province[]" required>
                <option value="" disabled selected>Select Province</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">City</label>
            <select class="form-select city-dropdown" id="edu-city-${cardCount}" name="education_city[]" required>
                <option value="" disabled selected>Select City</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Major/Specialization</label>
            <input type="text" class="form-control" name="major[]" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Grade/GPA</label>
            <input type="number" step="0.01" class="form-control grade-input" name="grade[]" min="0" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Start Date</label>
            <input type="date" class="form-control" name="start_education[]" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">End Date</label>
            <input type="date" class="form-control" name="end_education[]" required>
        </div>
  
        <div class="col-md-12">
            <label class="form-label">Certificate/Diploma</label>
            <input type="file" class="form-control" name="education_certificate[]" accept="image/*" required>
            <small class="text-white">Upload image file (max 2MB)</small>
        </div>
    </div>
`);

        // Add validation for dates
        $card.find('input[name="end_education[]"]').on('change', function() {
            const startDate = $(this).closest('.row').find('input[name="start_education[]"]').val();
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

        // Add validation for grade input based on education level
        $card.find('.education-level').on('change', function() {
            const level = $(this).val();
            const $gradeInput = $(this).closest('.row').find('.grade-input');

            if (['SMA', 'SMK'].includes(level)) {
                $gradeInput.attr({
                    'min': '0',
                    'max': '100',
                    'step': '0.01',
                    'placeholder': 'Enter grade (0-100)'
                });
            } else if (['D3', 'S1', 'S2'].includes(level)) {
                $gradeInput.attr({
                    'min': '0',
                    'max': '4',
                    'step': '0.01',
                    'placeholder': 'Enter GPA (0-4)'
                });
            }

            // Prevent values outside allowed range
            $gradeInput.off('input').on('input', function() {
                let min = parseFloat($(this).attr('min'));
                let max = parseFloat($(this).attr('max'));
                let value = parseFloat($(this).val());

                if (value < min) {
                    $(this).val(min);
                } else if (value > max) {
                    $(this).val(max);
                }
            });
        });

        // Add validation for file size
        $card.find('input[name="education_certificate[]"]').on('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const maxSize = 2 * 1024 * 1024; // 2MB

                if (file.size > maxSize) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Too Large',
                        text: 'Certificate image should not exceed 2MB!',
                    });
                    $(this).val('');
                    return;
                }

                if (!file.type.match('image.*')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid File Type',
                        text: 'Please select an image file!',
                    });
                    $(this).val('');
                    return;
                }
            }
        });

        $container.append($card);

        // Load provinces for this card
        loadProvinces($card.find('.province-dropdown'));
    }

    // Modified addTraining function
    function addTraning() {
        const $container = $('#trainingContainer');
        const cardCount = $container.children().length + 1;
        const $card = $('<div>').addClass('experience-card');

        $card.html(`
        <div class="card-number">${cardCount}</div>
        <div class="card-header">
            <h4 class="text-warning m-0">Training #${cardCount}</h4>
            <button type="button" class="btn-remove" onclick="removeCard(this, 'trainingContainer')">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Training Name</label>
                <input type="text" class="form-control" name="training_name[]" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Province</label>
                <select class="form-select province-dropdown" id="training-province-${cardCount}" name="training_province[]" required>
                    <option value="" disabled selected>Select Province</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">City</label>
                <select class="form-select city-dropdown" id="training-city-${cardCount}" name="training_city[]" required>
                    <option value="" disabled selected>Select City</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-control" name="start_training[]" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">End Date</label>
                <input type="date" class="form-control" name="end_training[]" required>
            </div>
        </div>
    `);

        $card.find('input[name="end_training[]"]').on('change', function() {
            const startDate = $(this).closest('.row').find('input[name="start_training[]"]').val();
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

        $container.append($card);

        // Load provinces for this card
        loadProvinces($card.find('.province-dropdown'));
    }

    // Modified addOrganization function
    function addOrganization() {
        const $container = $('#organizationContainer');
        const cardCount = $container.children().length + 1;
        const $card = $('<div>').addClass('experience-card');

        $card.html(`
    <div class="card-number">${cardCount}</div>
    <div class="card-header">
        <h4 class="text-warning m-0">Organization #${cardCount}</h4>
        <button type="button" class="btn-remove" onclick="removeCard(this, 'organizationContainer')">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Organization Name</label>
            <input type="text" class="form-control" name="org_name[]" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Position</label>
            <input type="text" class="form-control" name="org_position[]" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Province</label>
            <select class="form-select province-dropdown" id="org-province-${cardCount}" name="org_province[]" required>
                <option value="" disabled selected>Select Province</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">City</label>
            <select class="form-select city-dropdown" id="org-city-${cardCount}" name="org_city[]" required>
                <option value="" disabled selected>Select City</option>
            </select>
        </div>
      
        <div class="col-md-12">
            <label class="form-label">Activity Type</label>
            <textarea class="form-control list-textarea" name="activity_type[]" required rows="3" placeholder="- list your activity"></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Start Date</label>
            <input type="date" class="form-control" name="org_start_date[]" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">End Date</label>
            <input type="date" class="form-control" name="org_end_date[]" required>
        </div>
    
    </div>
`);
        $card.find('input[name="org_end_date[]"]').on('change', function() {
            const startDate = $(this).closest('.row').find('input[name="org_start_date[]"]').val();
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

        $container.append($card);

        // Load provinces for this card
        loadProvinces($card.find('.province-dropdown'));
    }
    // Family Cards
    function addFamily() {
        const $container = $('#familyContainer');
        const cardCount = $container.children().length + 1;
        const $card = $('<div>').addClass('experience-card');

        $card.html(`
                <div class="card-number">${cardCount}</div>
                <div class="card-header">
                    <h4 class="text-warning m-0">Family Member #${cardCount}</h4>
                    <button type="button" class="btn-remove" onclick="removeCard(this, 'familyContainer')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="row g-3">
                    <!-- Full Name -->
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="family_name[]" required>
                    </div>
                    <!-- Relationship -->
                    <div class="col-md-6">
                        <label class="form-label">Relationship</label>
                        <select class="form-select" name="relation[]" required>
                            <option value="" disabled>Select Relationship</option>
                            <option value="Father">Father</option>
                            <option value="Mother">Mother</option>
                            <option value="Husband">Husband</option>
                            <option value="Wife">Wife</option>
                            <option value="Child">Child</option>
                            <option value="Sibling">Sibling</option>
                        </select>
                    </div>
                    <!-- Phone Number -->
                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" placeholder="08XXXXXXXXXX" pattern="08[0-9]{10,12}" class="form-control" name="family_phone[]" required>
                    </div>
                    <!-- Gender -->
                    <div class="col-md-6">
                        <label class="form-label">Gender</label>
                        <select class="form-select" name="gender_family[]" required>
                            <option value="" disabled>Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    
                </div>
            `);

        $container.append($card);
    }


    function addLanguage() {
        const $container = $('#languageContainer');
        const cardCount = $container.children().length + 1;
        const $card = $('<div>').addClass('experience-card');

        $card.html(`
    <div class="card-number">${cardCount}</div>
    <div class="card-header">
        <h4 class="text-warning m-0">Language Proficiency #${cardCount}</h4>
        <button type="button" class="btn-remove" onclick="removeCard(this, 'languageContainer', updateLanguageDropdowns)">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    <div class="row g-3">
        <div class="col-md-12">
            <label class="form-label">Language</label>
            <select class="form-select language-select" name="language[]" required>
                <option value="" selected disabled>Select Language</option>
                <option value="Indonesian">Indonesian</option>
                <option value="English">English</option>
                <option value="Mandarin">Mandarin</option>
                <option value="Japanese">Japanese</option>
                <option value="Other">Other</option>
            </select>
            <input type="text" class="form-control mt-2 d-none other-language" name="other_language[]" placeholder="Specify language">
        </div>
        <div class="col-md-6">
            <label class="form-label">Verbal Proficiency</label>
            <select class="form-select" name="verbal_proficiency[]" required>
                <option value="" selected disabled>Select Level</option>
                <option value="Active">Active</option>
                <option value="Passive">Passive</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Written Proficiency</label>
            <select class="form-select" name="written_proficiency[]" required>
                <option value="" selected disabled>Select Level</option>
                <option value="Active">Active</option>
                <option value="Passive">Passive</option>
            </select>
        </div>
    </div>
`);

        // Add change event handler for language select
        $card.find('select[name="language[]"]').on('change', function() {
            const $otherInput = $(this).siblings('.other-language');
            $otherInput.toggleClass('d-none', $(this).val() !== 'other');
            $otherInput.prop('required', $(this).val() === 'other');

            // Update all language dropdowns after selection
            updateLanguageDropdowns();
        });

        $container.append($card);
        updateLanguageDropdowns();
    }



    function addWorkExperience() {
        const $container = $('#workExperienceContainer');
        const cardCount = $container.children().length + 1;
        const $card = $('<div>').addClass('experience-card');
        const cardId = `work-${cardCount}`;

        $card.html(`
    <div class="card-number">${cardCount}</div>
    <div class="card-header">
        <h4 class="text-warning m-0">Work Experience #${cardCount}</h4>
        <button type="button" class="btn-remove" onclick="removeCard(this, 'workExperienceContainer')">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Company Name</label>
            <input type="text" class="form-control" name="company_name[]" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Position</label>
            <input type="text" class="form-control" name="position[]" required>
        </div>
        
        <div class="col-md-12">
            <label class="form-label">Company Address</label>
            <textarea class="form-control" name="company_address[]" required rows="2"></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Company Phone</label>
            <input type="tel" placeholder="08XXXXXXXXXX" pattern="08[0-9]{10,12}" class="form-control" name="company_phone[]" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Previous Salary</label>
            <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" class="form-control" name="previous_salary[]" placeholder="5000000"  required>
            </div>
            <small class="text-white">Masukkan gaji tanpa "." atau ",".</small>
        </div>
        <div class="col-md-6">
            <label class="form-label">Supervisor Name</label>
            <input type="text" class="form-control" name="supervisor_name[]" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Supervisor Phone</label>
            <input type="tel" placeholder="08XXXXXXXXXX" pattern="08[0-9]{10,12}" class="form-control" name="supervisor_phone[]" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Start Date</label>
            <input type="date" class="form-control" name="working_start[]" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">End Date</label>
            <input type="date" class="form-control" name="working_end[]" required>
        </div>
        <div class="col-md-12">
            <label class="form-label">Job Description</label>
            <textarea class="form-control list-textarea" name="job_description[]" required rows="3" placeholder="- list your job description"></textarea>
        </div>
        <div class="col-md-12">
            <label class="form-label">Reason for Leaving</label>
            <textarea class="form-control list-textarea" name="leaving_reason[]" required rows="3" placeholder="- list your reason to leave"></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Benefits Received</label>
            <textarea class="form-control list-textarea" name="previous_benefits[]" rows="3" placeholder="- list your previous benefits"></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Facilities Provided</label>
            <textarea class="form-control list-textarea" name="previous_facilities[]" rows="3" placeholder="- list your previous facilities"></textarea>
        </div>
    </div>
`);

        // Add change event handler for end date validation
        $card.find('input[name="working_end[]"]').on('change', function() {
            const startDate = $(this).closest('.row').find('input[name="working_start[]"]').val();
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

        $container.append($card);

    }

    // Improved function to load provinces
    function loadProvinces($dropdown) {
        // Only load if not already populated
        if ($dropdown.find('option').length <= 1) {
            $.ajax({
                url: "https://alamat.thecloudalert.com/api/provinsi/get/",
                type: "GET",
                success: function(response) {
                    if (response.result) {
                        let provinces = response.result;
                        $dropdown.empty().append('<option value="" disabled selected>Select Province</option>');

                        provinces.forEach(function(province) {
                            // Store province text as value and ID as data attribute
                            $dropdown.append(`<option value="${province.text}" data-id="${province.id}">${province.text}</option>`);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching provinces:", error);
                }
            });
        }
    }

    // Modified function to load cities based on province ID
    function loadCities(provinceId, $cityDropdown) {
        if (!provinceId) return;

        console.log("Loading cities for province ID:", provinceId); // Debug log

        $.ajax({
            url: `https://alamat.thecloudalert.com/api/kabkota/get/?d_provinsi_id=${provinceId}`,
            type: "GET",
            success: function(response) {
                if (response.result) {
                    let cities = response.result;
                    $cityDropdown.empty().append('<option value="" disabled selected>Select City</option>');

                    cities.forEach(function(city) {
                        // Store city text as the value
                        $cityDropdown.append(`<option value="${city.text}">${city.text}</option>`);
                    });

                    console.log(`Loaded ${cities.length} cities`); // Debug log
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching cities:", error);
            }
        });
    }

    function toggleOtherLanguage(select) {
        const $otherInput = $(select).parent().find('.other-language');
        $otherInput.toggleClass('d-none', $(select).val() !== 'other');
        $otherInput.prop('required', $(select).val() === 'other');
    }

    function updateLanguageDropdowns() {
        // Get all selected language values
        const selectedLanguages = [];
        $('.language-select').each(function() {
            const value = $(this).val();
            if (value && value !== 'other') {
                selectedLanguages.push(value);
            }
        });

        // Update each dropdown
        $('.language-select').each(function() {
            const currentValue = $(this).val();

            // Store current selection
            const $select = $(this);
            $select.find('option').each(function() {
                const optionValue = $(this).val();

                // Skip the empty, 'other', or currently selected option
                if (!optionValue || optionValue === 'other' || optionValue === currentValue) {
                    return;
                }

                // Disable if already selected in another dropdown
                const isSelected = selectedLanguages.includes(optionValue);
                const isCurrentlySelected = optionValue === currentValue;

                $(this).prop('disabled', isSelected && !isCurrentlySelected);
            });
        });


        $('#distance').on('input', function() {
            let value = parseFloat($(this).val());

            // Pastikan nilai tetap dalam batas yang diizinkan
            if (value < 0) {
                $(this).val(0);
            } else if (value > 30) {
                $(this).val(30);
            }
        });
    }
</script>
@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: "{{ session('error') }}",
    });
</script>
@endif

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: "{{ session('success') }}",
    });
</script>
@endif

<!-- Hidden Fields -->
<!-- <div class="col-md-6 d-none">
    <label class="form-label">Date of Birth</label>
    <input type="date" class="form-control" name="birth_date_family[]">
</div>
<div class="col-md-6 d-none">
    <label class="form-label">Place of Birth</label>
    <input type="text" class="form-control" name="birth_place_family[]">
</div>
<div class="col-md-12 d-none">
    <label class="form-label">Address</label>
    <textarea class="form-control" name="address[]" rows="2"></textarea>
</div>
<div class="col-md-6 d-none">
    <label class="form-label">ID Number</label>
    <input type="number" class="form-control" name="ID_number_family[]">
</div>
<div class="col-md-6 d-none">
    <label class="form-label">Occupation</label>
    <input type="text" class="form-control" name="job[]">
</div> -->