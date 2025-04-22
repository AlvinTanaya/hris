@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Back button with danger color -->
    <a href="{{ route('user.employees.index') }}" class="btn btn-danger px-4 mb-4">
        <i class="fas fa-arrow-left me-2"></i>Back
    </a>

    <!-- Keep the warning text color for the title -->
    <h1 class="text-center text-warning mb-4">
        <i class="fas fa-exchange-alt me-2"></i> Employee Transfer
    </h1>

    <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
        <!-- Blue header like in the screenshot -->
        <div class="card-header bg-primary text-white p-4">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <!-- Image with 50px size, rounded with 2px yellow border -->
                    <img src="{{ $user->photo_profile_path ? asset('storage/'. $user->photo_profile_path) : asset('storage/default_profile.png') }}"
                        alt="Profile Picture"
                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%; border: 2px solid #FFCC00;">
                </div>
                <div>
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <span class="text-white">ID: {{ $user->employee_id }}</span>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <!-- Display validation errors -->
            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                    <li><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form action="{{ route('user.employees.transfer_user', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-exchange-alt text-primary me-2"></i>
                            <label for="transfer_type" class="form-label fw-bold mb-0">Jenis Transfer</label>
                        </div>
                        <select class="form-select form-select-lg border" id="transfer_type" name="transfer_type" required>
                            <option selected disabled>Pilih Jenis Transfer</option>
                            @if ($user->employee_status !== "Tetap")
                            <option value="Penetapan">Penetapan</option>
                            @endif
                            <option value="Demosi">Demosi</option>
                            <option value="Promosi">Promosi</option>
                            <option value="Mutasi">Mutasi</option>
                            <option value="Resign">Resign</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-briefcase text-primary me-2"></i>
                            <p class="text-muted mb-0">Current Position</p>
                        </div>
                        <p class="text-primary fw-bold fs-5">{{ $user->position->position }}</p>
                        <input type="hidden" name="old_position_id" value="{{ $user->position_id }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-building text-primary me-2"></i>
                            <p class="text-muted mb-0">Current Department</p>
                        </div>
                        <p class="text-primary fw-bold fs-5">{{ $user->department->department }}</p>
                        <input type="hidden" name="old_department_id" value="{{ $user->department_id }}">
                    </div>

                </div>

                <div class="row mb-4">
                    <!-- Reason section with textarea (fixed closing tag) -->
                    <div class="col-md-12 mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-pencil-alt text-primary me-2"></i>
                            <label for="reason" class="form-label fw-bold mb-0">Reason</label>
                        </div>
                        <textarea class="form-control form-control-lg border" id="reason" name="reason" placeholder="Masukkan reason" required rows="3"></textarea>
                    </div>
                </div>

                <!-- Transfer Detail section -->
                <div id="transfer-fields" style="display: none;">
                    <div class="mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            <h5 class="text-primary fw-bold mb-0">Transfer Detail</h5>
                        </div>
                        <hr>
                    </div>

                    <!-- New Position/Department section with more visible inputs -->
                    <div class="row">
                        <div class="col-md-6 mb-3 form-group">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-briefcase text-primary me-2"></i>
                                <label for="new_position_id" class="form-label fw-bold mb-0">New Position</label>
                            </div>
                            <select class="form-select form-select-lg border" id="new_position_id" name="new_position_id">
                                <option value="" disabled selected>Select Position</option>
                                @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ old('new_position_id') == $position->id ? 'selected' : '' }}>{{ trim($position->position)}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3 form-group">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-building text-primary me-2"></i>
                                <label for="new_department_id" class="form-label fw-bold mb-0">New Department</label>
                            </div>
                            <select class="form-select form-select-lg border" id="new_department_id" name="new_department_id">
                                <option value="" selected disabled>Select Department</option>
                                @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('new_department_id') == $department->id ? 'selected' : '' }}>{{ trim($department->department) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <!-- Transfer Employee button (green like in the screenshot) -->
                <div class="d-flex justify-content-end mt-5">
                    <button type="submit" class="btn btn-success btn-lg px-5">
                        <i class="fas fa-exchange-alt me-2"></i>Transfer Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle transfer type change
        $('#transfer_type').on('change', function() {
            var transferType = $(this).val();
            if (transferType === 'Mutasi' || transferType === 'Demosi' || transferType === 'Promosi') {
                $('#transfer-fields').slideDown();
                $('#new_position_id, #new_department_id').prop('required', true);
            } else {
                $('#transfer-fields').slideUp();
                $('#new_position_id, #new_department_id').val('').prop('required', false);
            }
        });
        $('#new_position_id').change(function() {
            var positionId = $(this).val();
            var departmentSelect = $('#new_department_id');
            var departmentWrapper = departmentSelect.closest('.form-group');

            // Reset department select
            departmentSelect.css({
                'pointer-events': '',
                'background-color': ''
            }).val('');
            departmentSelect.off('mousedown.positionLock change.positionLock');
            departmentWrapper.find('.text-danger').remove();

            // Get selected position text and trim whitespace
            var selectedPosition = $(this).find('option:selected').text().trim();

            if (selectedPosition === 'Director') {
                // Find Director department by ID or text
                var directorDept = departmentSelect.find('option').filter(function() {
                    return $(this).text().trim() === 'Director';
                }).first();

                if (directorDept.length) {
                    // Set value
                    departmentSelect.val(directorDept.val());

                    // Make it appear unchangeable but still submittable
                    departmentSelect.css({
                        'pointer-events': 'none',
                        'background-color': '#e9ecef'
                    });

                    // Additional protection against JavaScript manipulation
                    departmentSelect.on('mousedown.positionLock change.positionLock', function(e) {
                        e.preventDefault();
                        return false;
                    });

                    departmentWrapper.append('<small class="text-danger">Department automatically set for Director</small>');
                }
            } else if (selectedPosition === 'General Manager') {
                // Find General Manager department by ID or text
                var gmDept = departmentSelect.find('option').filter(function() {
                    return $(this).text().trim() === 'General Manager';
                }).first();

                if (gmDept.length) {
                    // Set value
                    departmentSelect.val(gmDept.val());

                    // Make it appear unchangeable but still submittable
                    departmentSelect.css({
                        'pointer-events': 'none',
                        'background-color': '#e9ecef'
                    });

                    // Additional protection against JavaScript manipulation
                    departmentSelect.on('mousedown.positionLock change.positionLock', function(e) {
                        e.preventDefault();
                        return false;
                    });

                    departmentWrapper.append('<small class="text-danger">Department automatically set for General Manager</small>');
                }
            }
        });
    });
</script>
@endpush