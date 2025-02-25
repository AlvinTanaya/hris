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
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3c72, #2a5298, #2c3e50);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: white;
        }

        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .form-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem auto;
            max-width: 1200px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
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
            border: 3px solid #ffc107;
            box-shadow: 0 0 20px rgba(255, 193, 7, 0.3);
        }

        .profile-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .form-control,
        .form-select {
            background: rgba(42, 82, 152, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            backdrop-filter: blur(5px);
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(42, 82, 152, 0.9);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.1);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .section-title {
            color: #ffc107;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid rgba(255, 193, 7, 0.3);
        }

        .table {
            color: white;
        }

        .table thead th {
            background: rgba(42, 82, 152, 0.9) !important;
            color: white !important;
            border-color: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }

        .table td {
            border-color: rgba(255, 255, 255, 0.1);
        }

        .btn-add-row {
            background: #ffc107;
            color: #1e3c72;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-add-row:hover {
            background: #ffca2c;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
        }

        .btn-remove {
            color: #ff4444;
            background: none;
            border: none;
            padding: 0.25rem 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-remove:hover {
            color: #ff0000;
            transform: scale(1.1);
        }

        .file-preview {
            margin-top: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .cv-preview {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            margin-top: 1rem;
        }

        .cv-preview i {
            font-size: 2rem;
            color: #ffc107;
        }

        .work-experience-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .work-experience-card .row {
            margin-bottom: 15px;
        }

        .documents-section .upload-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .documents-section .upload-card i {
            font-size: 24px;
            color: #ffc107;
            margin-bottom: 10px;
        }

        .experience-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
        }

        .card-number {
            position: absolute;
            top: -10px;
            left: -10px;
            background: #ffc107;
            color: #1e3c72;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-left: 20px;
        }

        .section-container {
            position: relative;
        }
    </style>

</head>

<body>

    <div class="container py-5 mx-auto">
        <a href="{{ route('job_vacancy.index') }}" class="btn btn-danger px-5 ms-5 mb-3">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>

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
                            <h5 class="text-warning mb-3">Achievements</h5>
                            <input type="file" class="form-control" name="achievement_path" accept=".pdf" onchange="handleAchievementFiles(this)">
                            <div id="achievementPreview" class="mt-3"></div>
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
                            <input type="number" class="form-control" name="expected_salary" required>
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
                        <input type="tel" placeholder="08X-XXX-XXX-XXXX" pattern="08[0-9]{1}-[0-9]{3}-[0-9]{3}-[0-9]{1,4}" class="form-control" name="phone_number" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NIK</label>
                        <input type="text" class="form-control" name="ID_number" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Place of Birth</label>
                        <input type="text" class="form-control" name="birth_place" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" name="birth_date" required>
                    </div>
                    <div class="col-md-4">
                        <label for="blood_type" class="form-label">
                            <i class="fas fa-tint"></i> Blood Type
                        </label>
                        <select class="form-control" id="blood_type" name="blood_type">
                            <option selected disabled>Choose Blood Type</option>
                            @foreach(['A', 'B', 'AB', 'O'] as $blood)
                            <option value="{{ $blood }}">
                                {{ $blood }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <label class="form-label">Gender</label>
                        <select class="form-select" name="gender" required>
                            <option value="" disabled>Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
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
                        <label class="form-label">Weight (kg)</label>
                        <input type="number" class="form-control" name="weight">
                    </div>
                    <div class="col-md-6">
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
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

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
                const $fileList = $('<div>').addClass('mt-2');

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
        function removeCard(button, containerId) {
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

        // Add card functions
        function addEducation() {
            const $container = $('#educationContainer');
            const cardCount = $container.children().length + 1;
            const $card = $('<div>').addClass('experience-card');

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
                <select class="form-select" name="degree[]" onchange="toggleOtherDegree(this)" required>
                    <option value="" disabled>Select Level</option>
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
                <label class="form-label">Institution Name</label>
                <input type="text" class="form-control" name="educational_place[]" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">City</label>
                <input type="text" class="form-control" name="education_city[]" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Major/Specialization</label>
                <input type="text" class="form-control" name="major[]" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-control" name="start_education[]" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">End Date</label>
                <input type="date" class="form-control" name="end_education[]" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Grade/GPA</label>
                <input type="number" step="0.01" class="form-control" name="grade[]" required>
            </div>
        </div>
    `);
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


            $container.append($card);
        }

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
                    <div class="col-md-6">
                        <label class="form-label">Training Name</label>
                        <input type="text" class="form-control" name="training_name[]" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City</label>
                        <input type="text" class="form-control" name="training_city[]" required>
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
            <div class="col-md-6">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="family_name[]" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Relationship</label>
                <select class="form-select" name="relation[]" required>
                    <option value="" disabled>Select Relationship</option>
                    <option value="Ayah">Ayah</option>
                    <option value="Ibu">Ibu</option>
                    <option value="Suami">Suami</option>
                    <option value="Istri">Istri</option>
                    <option value="Anak">Anak</option>
                    <option value="Saudara">Saudara</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Date of Birth</label>
                <input type="date" class="form-control" name="birth_date_family[]" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Place of Birth</label>
                <input type="text" class="form-control" name="birth_place_family[]" required>
            </div>
            <div class="col-md-12">
                <label class="form-label">Address</label>
                <textarea class="form-control" name="address[]" required rows="2"></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Gender</label>
                <select class="form-select" name="gender_family[]" required>
                    <option value="" disabled>Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">ID Number</label>
                <input type="number" class="form-control" name="ID_number_family[]" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone Number</label>
                <input type="tel" placeholder="08X-XXX-XXX-XXXX" pattern="08[0-9]{1}-[0-9]{3}-[0-9]{3}-[0-9]{1,4}" class="form-control" name="family_phone[]" required>

    
            </div>
            <div class="col-md-6">
                <label class="form-label">Occupation</label>
                <input type="text" class="form-control" name="job[]" required>
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
            <button type="button" class="btn-remove" onclick="removeCard(this, 'languageContainer')">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Language</label>
                <select class="form-select" name="language[]" required>
                    <option value="" disabled>Select Language</option>
                    <option value="Indonesian">Indonesian</option>
                    <option value="English">English</option>
                    <option value="Mandarin">Mandarin</option>
                    <option value="Japanese">Japanese</option>
                    <option value="Arabic">Arabic</option>
                    <option value="other">Other</option>
                </select>
                <input type="text" class="form-control mt-2 d-none other-language" name="other_language[]" placeholder="Specify language">
            </div>
            <div class="col-md-6">
                <label class="form-label">Verbal Proficiency</label>
                <select class="form-select" name="verbal_proficiency[]" required>
                    <option value="" disabled>Select Level</option>
                    <option value="Active">Active</option>
                    <option value="Passive">Passive</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Written Proficiency</label>
                <select class="form-select" name="written_proficiency[]" required>
                    <option value="" disabled>Select Level</option>
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
            });

            $container.append($card);
        }


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
            <div class="col-md-4">
                <label class="form-label">Organization Name</label>
                <input type="text" class="form-control" name="org_name[]" required>
            </div>
                <div class="col-md-4">
                <label class="form-label">City</label>
                <input type="text" class="form-control" name="org_city[]" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Position</label>
                <input type="text" class="form-control" name="org_position[]" required>
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
        }

        function addWorkExperience() {
            const $container = $('#workExperienceContainer');
            const cardCount = $container.children().length + 1;
            const $card = $('<div>').addClass('experience-card');

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
                <input type="tel" placeholder="08X-XXX-XXX-XXXX" pattern="08[0-9]{1}-[0-9]{3}-[0-9]{3}-[0-9]{1,4}" class="form-control" name="company_phone[]" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Previous Salary</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" class="form-control" name="previous_salary[]" required>
                </div>
                <small class="text-white">Masukkan gaji tanpa "." atau ",".</small>
            </div>

            
            <div class="col-md-6">
                <label class="form-label">Supervisor Name</label>
                <input type="text" class="form-control" name="supervisor_name[]" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Supervisor Phone</label>
                <input type="tel" placeholder="08X-XXX-XXX-XXXX" pattern="08[0-9]{1}-[0-9]{3}-[0-9]{3}-[0-9]{1,4}" class="form-control" name="supervisor_phone[]" required>
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
            // Validasi End Date untuk Working Experience
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
        // Toggle other language

        function toggleOtherLanguage(select) {
            const $otherInput = $(select).parent().find('.other-language');
            $otherInput.toggleClass('d-none', $(select).val() !== 'other');
            $otherInput.prop('required', $(select).val() === 'other');
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

</body>

</html>