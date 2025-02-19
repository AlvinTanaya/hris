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
    </style>

</head>

<body>
    <div class="container py-5">

        <form id="jobApplicationForm" action="{{ route('job_vacancy.store', $demand->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Profile Section -->
            <div class="form-section profile-section">
                <div class="profile-preview">
                    <img id="profilePreview" src="{{ asset('storage/default_profile.png') }}" alt="Profile Preview">
                </div>
                <div class="row" style="padding-left: 30px; padding-right: 30px;">
                    <div class="col-md-12 mx-auto">
                        <label class="form-label">Profile Photo</label>
                        <input type="file" class="form-control @error('photo_profile_path') is-invalid @enderror"
                            name="photo_profile_path" accept="image/*" onchange="previewImage(this)" required>
                        @error('photo_profile_path')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row" style="padding-left: 30px; padding-right: 30px;">
                    <div class="col-md-6 mx-auto mt-3">
                        <label class="form-label">CV (PDF)</label>
                        <input type="file" class="form-control @error('cv_path') is-invalid @enderror"
                            name="cv_path" accept=".pdf" onchange="previewCV(this)" required>
                        @error('cv_path')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="cvPreview" class="cv-preview" style="display: none;">
                            <i class="fas fa-file-pdf"></i>
                            <span id="cvFileName"></span>
                        </div>
                    </div>
                    <div class="col-md-6 mx-auto mt-3">
                        <label class="form-label">ID Card (JPG/PNG)</label>
                        <input type="file" class="form-control @error('ID_card_path') is-invalid @enderror"
                            name="ID_card_path" accept=".jpg,.jpeg,.png" onchange="previewIDCard(this)" required>
                        @error('ID_card_path')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="idCardPreview" class="id-card-preview" style="display: none; align-items: center;">
                            <i class="fas fa-id-card" style="font-size: 24px; color: #f39c12;"></i>
                            <span id="idCardFileName" style="margin-left: 10px;"></span>
                        </div>
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
                        <input type="tel" class="form-control" name="phone_number" required>
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
                        <select class="form-control" id="blood_type" name="blood_type" required>
                            <option selected disabled>Choose Blood Type</option>
                            @foreach(['A', 'B', 'AB', 'O', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $blood)
                            <option value="{{ $blood }}">
                                {{ $blood }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Religion</label>
                        <select class="form-select" name="religion" required>
                            <option value="">Select Religion</option>
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
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ID Card Address</label>
                        <textarea class="form-control" name="ID_addres" rows="3" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Current Address</label>
                        <textarea class="form-control" name="domicile_address" rows="3" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Weight (kg)</label>
                        <input type="number" class="form-control" name="weight" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Height (cm)</label>
                        <input type="number" class="form-control" name="height" required>
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

            <!-- Education Information -->
            <div class="form-section">
                <h3 class="section-title">Education History</h3>
                <div class="table-responsive">
                    <table class="table" id="educationTable">
                        <thead>
                            <tr>
                                <th>Level</th>
                                <th>Institution</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Grade</th>
                                <th>Major</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-add-row" onclick="addEducationRow()">
                    <i class="fas fa-plus"></i> Add Education
                </button>
            </div>

            <!-- Family Information -->
            <div class="form-section">
                <h3 class="section-title">Family Information</h3>
                <div class="table-responsive">
                    <table class="table" id="familyTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Relationship</th>
                                <th>Date of Birth</th>
                                <th>Place of Birth</th>
                                <th>ID Number</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Gender</th>
                                <th>Occupation</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-add-row" onclick="addFamilyRow()">
                    <i class="fas fa-plus"></i> Add Family Member
                </button>
            </div>

            <!-- Work Experience -->
            <div class="form-section">
                <h3 class="section-title">Work Experience</h3>
                <div class="table-responsive">
                    <table class="table" id="workTable">
                        <thead>
                            <tr>
                                <th>Company Name</th>
                                <th>Position</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-add-row" onclick="addWorkRow()">
                    <i class="fas fa-plus"></i> Add Work Experience
                </button>
            </div>

            <div class="form-section text-center">
                <button type="submit" class="btn btn-add-row btn-lg">
                    Submit Application
                </button>
            </div>
        </form>
    </div>

    <script>
        // Improved file preview functions
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const maxSize = 2 * 1024 * 1024; // 2MB

                if (file.size > maxSize) {
                    alert('Image size should not exceed 2MB');
                    input.value = '';
                    return;
                }

                if (!file.type.match('image.*')) {
                    alert('Please select an image file');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePreview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }

        function previewIDCard(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const maxSize = 5 * 1024 * 1024; // 5MB

                if (file.size > maxSize) {
                    alert('ID Card size should not exceed 5MB');
                    input.value = '';
                    return;
                }

                const allowedTypes = ['image/jpeg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Please select a JPG or PNG file');
                    input.value = '';
                    return;
                }

                const idCardPreview = document.getElementById('idCardPreview');
                const idCardFileName = document.getElementById('idCardFileName');

                idCardFileName.textContent = file.name;
                idCardPreview.style.display = 'flex';
            }
        }



        function previewCV(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const maxSize = 5 * 1024 * 1024; // 5MB

                if (file.size > maxSize) {
                    alert('CV size should not exceed 5MB');
                    input.value = '';
                    return;
                }

                if (file.type !== 'application/pdf') {
                    alert('Please select a PDF file');
                    input.value = '';
                    return;
                }

                const cvPreview = document.getElementById('cvPreview');
                const cvFileName = document.getElementById('cvFileName');
                cvFileName.textContent = file.name;
                cvPreview.style.display = 'flex';
            }
        }

        // Form validation before submission
        document.getElementById('jobApplicationForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Check if at least one education row exists
            if (document.querySelector('#educationTable tbody').children.length === 0) {
                alert('Please add at least one education record');
                return;
            }

            // Check if at least one family member exists
            if (document.querySelector('#familyTable tbody').children.length === 0) {
                alert('Please add at least one family member');
                return;
            }

            // Optional: Check work experience (if required)
            // if (document.querySelector('#workTable tbody').children.length === 0) {
            //     alert('Please add at least one work experience');
            //     return;
            // }

            // All validations passed, submit the form
            this.submit();
        });

        // Enhanced row addition functions with better validation
        function addEducationRow() {
            const tbody = document.querySelector('#educationTable tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
            <td>
                <select class="form-select" name="degree[]" required>
                    <option value="">Select Level</option>
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
            <td><input type="text" class="form-control" name="educational_place[]" required></td>
            <td><input type="date" class="form-control" name="start_education[]" required></td>
            <td><input type="date" class="form-control" name="end_education[]" required onchange="validateDates(this)"></td>
            <td><input type="number" class="form-control" name="grade[]" required pattern="[0-9.]+" title="Please enter a valid grade (numbers and decimal point only)"></td>
            <td><input type="text" class="form-control" name="major[]" required></td>
            <td>
                <button type="button" class="btn-remove" onclick="removeRow(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
            tbody.appendChild(row);
        }

        // Add date validation
        function validateDates(endDateInput) {
            const startDate = endDateInput.parentElement.previousElementSibling.querySelector('input').value;
            const endDate = endDateInput.value;

            if (startDate && endDate && new Date(endDate) <= new Date(startDate)) {
                alert('End date must be after start date');
                endDateInput.value = '';
            }
        }

        // Modify family row addition with better structure
        function addFamilyRow() {
            const tbody = document.querySelector('#familyTable tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
            <td><input type="text" class="form-control" name="family_name[]" required></td>
            <td>
                <select class="form-select" name="relation[]" required>
                    <option value="">Select Relationship</option>
                    <option value="Ayah">Ayah</option>
                    <option value="Ibu">Ibu</option>
                    <option value="Suami">Suami</option>
                    <option value="Istri">Istri</option>
                    <option value="Anak">Anak</option>
                    <option value="Saudara">Saudara</option>
                </select>
            </td>
            <td><input type="date" class="form-control" name="birth_date_family[]" required max="${new Date().toISOString().split('T')[0]}"></td>
            <td><input type="text" class="form-control" name="birth_place_family[]" required></td>
            <td><input type="text" class="form-control" name="ID_number_family[]" required pattern="[0-9]{16}" title="Please enter a valid 16-digit ID number"></td>
            <td><input type="tel" class="form-control" name="family_phone[]" required pattern="[0-9]{10,13}" title="Please enter a valid phone number (10-13 digits)"></td>
            <td><input type="text" class="form-control" name="address[]" required></td>
            <td>
                <select class="form-select" name="family_gender[]" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </td>
            <td><input type="text" class="form-control" name="job[]" required></td>
            <td>
                <button type="button" class="btn-remove" onclick="removeRow(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
            tbody.appendChild(row);
        }

        // Work experience row with validation
        function addWorkRow() {
            const tbody = document.querySelector('#workTable tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
            <td><input type="text" class="form-control" name="company_name[]" required></td>
            <td><input type="text" class="form-control" name="position[]" required></td>
            <td><input type="date" class="form-control" name="working_start[]" required></td>
            <td><input type="date" class="form-control" name="working_end[]" required onchange="validateWorkDates(this)"></td>
            <td>
                <button type="button" class="btn-remove" onclick="removeRow(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
            tbody.appendChild(row);
        }

        function validateWorkDates(endDateInput) {
            const startDate = endDateInput.parentElement.previousElementSibling.querySelector('input').value;
            const endDate = endDateInput.value;

            if (startDate && endDate && new Date(endDate) <= new Date(startDate)) {
                alert('End date must be after start date');
                endDateInput.value = '';
            }
        }

        function removeRow(button) {
            if (confirm('Are you sure you want to remove this row?')) {
                button.closest('tr').remove();
            }
        }
    </script>
</body>

</html>