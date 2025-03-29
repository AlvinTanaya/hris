@extends('layouts.app')

@section('content')
<style>
    .form-label {
        font-weight: 600;
    }

    .card {
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: #0d6efd;
        color: white;
        font-weight: 600;
        padding: 15px 20px;
    }

    .btn-primary {
        padding: 10px 20px;
        font-weight: 600;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
    }

    .required-field::after {
        content: " *";
        color: red;
    }

    .balance-info {
        font-size: 14px;
        margin-top: 5px;
        font-weight: 500;
    }

    .step-container {
        margin-bottom: 20px;
        border-radius: 8px;
        padding: 15px;
        background-color: #f8f9fa;
        border-left: 4px solid #0d6efd;
        transition: all 0.3s ease;
    }

    .step-container.active {
        background-color: #f0f7ff;
        border-left: 4px solid #0d6efd;
    }

    .step-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #0d6efd;
        color: white;
        margin-right: 10px;
        font-weight: bold;
    }

    .step-title {
        font-size: 18px;
        font-weight: 600;
        color: #0d6efd;
    }

    .date-field-container {
        transition: all 0.3s ease;
    }

    .employee-info {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        border-left: 4px solid #28a745;
    }

    .employee-name {
        font-weight: 600;
        font-size: 16px;
        color: #28a745;
    }
</style>

<div class="container-fluid mt-4">
    <div class="text-center mb-4">
        <h1 class="display-5 text-warning">
            <i class="fas fa-calendar-plus"></i> Request Time Off
        </h1>
        <p class="text-white">Complete the form below to submit your time off request</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Employee Info Card -->
            <div class="employee-info mb-4 d-flex justify-content-between ">
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-circle text-success fa-2x me-3"></i>
                    <div>
                        <div class="employee-name">{{ $employee->name }}</div>
                        <div class="text-muted small">Employee ID: {{ $employee->employee_id }}</div>
                    </div>
                </div>
                <a href="{{ route('request.time.off.index2', $employee->id) }}" class="btn btn-danger" style="padding-top: 10px;">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>



            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-file-alt"></i> New Time Off Request</h5>
                    <span class="badge bg-warning text-white">Pending</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('request.time.off.store') }}" method="POST" id="timeOffRequestForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $employee->id }}">

                        <!-- Step 1: Select Time Off Type -->
                        <div class="step-container active" id="step-1-container">
                            <div class="d-flex align-items-center mb-3">
                                <span class="step-number">1</span>
                                <span class="step-title">Select Time Off Type</span>
                            </div>

                            <div class="mb-3">
                                <label for="time_off_id" class="form-label required-field">Time Off Type</label>
                                <select name="time_off_id" id="time_off_id" class="form-select @error('time_off_id') is-invalid @enderror" required>
                                    <option value="">-- Select Time Off Type --</option>
                                    @foreach($timeOffTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->time_off_name }}</option>
                                    @endforeach
                                </select>
                                <div id="balance-info" class="balance-info text-primary mt-2 d-none">
                                    <i class="fas fa-info-circle"></i>
                                    <span id="balance-text"></span>
                                </div>
                                <div id="no-balance-warning" class="balance-info text-danger mt-2 d-none">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>You don't have any balance remaining for this time off type.</span>
                                </div>
                                @error('time_off_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Step 2: Select Dates -->
                        <div class="step-container d-none" id="step-2-container">
                            <div class="d-flex align-items-center mb-3">
                                <span class="step-number">2</span>
                                <span class="step-title">Select Dates</span>
                            </div>

                            <div class="row">
                                <!-- Start Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="start_date" class="form-label required-field">Start Date</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                        id="start_date" name="start_date" required disabled>
                                    <small class="form-text text-muted">First day of time off</small>
                                    @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- End Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label required-field">End Date</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                        id="end_date" name="end_date" required disabled>
                                    <small class="form-text text-muted">Last day of time off</small>
                                    @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                            <!-- Start Time -->
                            <div class="row" id="time-inputs-container" style="display: none;">
                                <!-- Start Time -->
                                <div class="col-md-6 mb-3" id="hour_in_container">
                                    <label for="hour_in" class="form-label required-field">Start Time</label>
                                    <input type="time" class="form-control @error('hour_in') is-invalid @enderror"
                                        id="hour_in" name="hour_in" disabled>
                                    <small class="form-text text-muted">Time when starting work</small>
                                    @error('hour_in')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- End Time -->
                                <div class="col-md-6 mb-3" id="hour_out_container">
                                    <label for="hour_out" class="form-label required-field">End Time</label>
                                    <input type="time" class="form-control @error('hour_out') is-invalid @enderror"
                                        id="hour_out" name="hour_out" disabled>
                                    <small class="form-text text-muted">Time when leaving work</small>
                                    @error('hour_out')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Duration Info -->
                            <div class="mb-3">
                                <div class="alert alert-info" id="duration-info">
                                    <i class="fas fa-info-circle"></i> Select dates to calculate duration
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Provide Reason -->
                        <div class="step-container d-none" id="step-3-container">
                            <div class="d-flex align-items-center mb-3">
                                <span class="step-number">3</span>
                                <span class="step-title">Provide Reason</span>
                            </div>

                            <!-- Reason -->
                            <div class="mb-3">
                                <label for="reason" class="form-label required-field">Reason</label>
                                <textarea class="form-control @error('reason') is-invalid @enderror"
                                    id="reason" name="reason" rows="3" placeholder="Please provide a reason for your time off request..." required disabled></textarea>
                                <small class="form-text text-muted">Your reason will be reviewed by management</small>
                                @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3" id="file-upload-container">
                                <label for="file_reason" class="form-label" id="file-reason-label">Supporting Document (Optional)</label>
                                <input type="file" class="form-control @error('file_reason') is-invalid @enderror"
                                    id="file_reason" name="file_reason" accept="image/*" disabled>
                                <small class="form-text text-muted" id="file-reason-help">Upload supporting document if needed</small>
                                <div id="sick-leave-warning" class="text-danger mt-2 d-none">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>For sick leave, a doctor's note or medical certificate is required</span>
                                </div>
                                @error('file_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                                <i class="fas fa-paper-plane"></i> Submit Request
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <!-- Help Text -->
            <div class="card mt-4">
                <div class="card-header bg-light text-dark">
                    <h5 class="mb-0"><i class="fas fa-question-circle"></i> Need Help?</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><i class="fas fa-info-circle text-primary me-2"></i> Your request will need approval from your supervisor.</p>
                    <p class="mb-2"><i class="fas fa-info-circle text-primary me-2"></i> The time off duration will be deducted from your balance upon approval.</p>
                    <p class="mb-0"><i class="fas fa-info-circle text-primary me-2"></i> You can track the status of your request in the "Request Time Off" section.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let userBalance = 0;
        const today = new Date().toISOString().split('T')[0];

        // When time off type is selected

        // When time off type is selected
        $('#time_off_id').change(function() {
            const timeOffId = $(this).val();
            const timeOffName = $(this).find('option:selected').text().toLowerCase();
            const userId = $('input[name="user_id"]').val();

            // Reset fields when time off type changes
            $('#start_date').val('');
            $('#end_date').val('');
            $('#hour_in').val('');
            $('#hour_out').val('');

            // Reset layout - tetap mempertahankan col-md-6 untuk semua elemen
            $('#start_date').closest('.col-md-6').show();
            $('#end_date').closest('.col-md-6').show();
            $('#hour_in_container').show();
            $('#hour_out_container').show();

            // Reset semua properti disabled dan css
            $('#start_date, #end_date, #hour_in, #hour_out').prop('disabled', false).prop('readonly', false);
            $('#start_date, #end_date, #hour_in, #hour_out').css({
                'background-color': '',
                'cursor': ''
            });
            $('#start_date, #end_date, #hour_in, #hour_out').attr('data-required', 'true');

            $.ajax({
                url: "{{ url('time_management/time_off/request_time_off/check-requires-time') }}",
                type: 'GET',
                data: {
                    time_off_id: timeOffId
                },
                success: function(response) {
                    // Reset time input fields
                    $('#time-inputs-container').hide();
                    // CHANGED: Using readonly instead of disabled for hour_in and hour_out
                    $('#hour_in').prop('disabled', false).prop('readonly', true);
                    $('#hour_out').prop('disabled', false).prop('readonly', true);
                    // Add styling for readonly fields
                    $('#hour_in, #hour_out').css({
                        'background-color': '#f5f5f5',
                        'cursor': 'not-allowed'
                    });

                    // Show time inputs if required
                    if (response.requires_time_input) {
                        $('#time-inputs-container').show();

                        // Reset semua field ke enabled state terlebih dahulu
                        $('#start_date').prop('disabled', false).prop('readonly', false);
                        $('#end_date').prop('disabled', false).prop('readonly', false);
                        $('#hour_in').prop('disabled', false).prop('readonly', false);
                        $('#hour_out').prop('disabled', false).prop('readonly', false);

                        // Reset CSS
                        $('#start_date, #end_date, #hour_in, #hour_out').css({
                            'background-color': '',
                            'cursor': ''
                        });

                        // Handle special cases
                        const timeOffName = $('#time_off_id option:selected').text().toLowerCase();

                        // For "masuk siang" case
                        if (timeOffName.includes('masuk siang')) {
                            // CHANGED: Using readonly instead of disabled for end_date and hour_out
                            $('#end_date').prop('disabled', false).prop('readonly', true);
                            $('#hour_out').prop('disabled', false).prop('readonly', true);

                            // Add CSS styling
                            $('#end_date, #hour_out').css({
                                'background-color': '#f5f5f5',
                                'cursor': 'not-allowed'
                            });

                            // Set data-required attributes
                            $('#end_date, #hour_out').attr('data-required', 'false');

                            // Remove previous event handlers to prevent multiple bindings
                            $('#start_date').off('change');

                            // Add immediate auto-copy functionality
                            $('#start_date').on('change', function() {
                                const selectedDate = $(this).val();
                                // Immediately set the end_date value
                                $('#end_date').val(selectedDate);
                                // console.log("Auto-copied to end_date:", selectedDate);

                                // Get employee shift for this date
                                if (selectedDate) {
                                    fetchEmployeeShift(userId, selectedDate, 'siang');
                                }

                                // Force duration calculation
                                calculateDuration();
                            });

                            // If start_date already has a value, trigger the change event
                            if ($('#start_date').val()) {
                                $('#start_date').trigger('change');
                            }
                        }
                        // For "pulang awal" case
                        else if (timeOffName.includes('pulang awal')) {
                            // CHANGED: Using readonly instead of disabled for start_date and hour_in
                            $('#start_date').prop('disabled', false).prop('readonly', true);
                            $('#hour_in').prop('disabled', false).prop('readonly', true);

                            // Add CSS styling
                            $('#start_date, #hour_in').css({
                                'background-color': '#f5f5f5',
                                'cursor': 'not-allowed'
                            });

                            // Set data-required attributes
                            $('#start_date, #hour_in').attr('data-required', 'false');

                            // Remove previous event handlers to prevent multiple bindings
                            $('#end_date').off('change');

                            // Add immediate auto-copy functionality
                            $('#end_date').on('change', function() {
                                const selectedDate = $(this).val();
                                // Immediately set the start_date value
                                $('#start_date').val(selectedDate);
                                // console.log("Auto-copied to start_date:", selectedDate);

                                // Get employee shift for this date
                                if (selectedDate) {
                                    fetchEmployeeShift(userId, selectedDate, 'awal');
                                }

                                // Force duration calculation
                                calculateDuration();
                            });

                            // If end_date already has a value, trigger the change event
                            if ($('#end_date').val()) {
                                $('#end_date').trigger('change');
                            }
                        } else {
                            // Regular case - semua field bisa diisi
                            $('#start_date').prop('disabled', false).prop('readonly', false);
                            $('#end_date').prop('disabled', false).prop('readonly', false);
                            $('#hour_in').prop('disabled', false).prop('readonly', false);
                            $('#hour_out').prop('disabled', false).prop('readonly', false);

                            // Reset CSS
                            $('#start_date, #end_date, #hour_in, #hour_out').css({
                                'background-color': '',
                                'cursor': ''
                            });

                            // Reset penanda data
                            $('#start_date, #end_date, #hour_in, #hour_out').attr('data-required', 'true');

                            // Get employee shift when either date changes
                            $('#start_date, #end_date').on('change', function() {
                                const startDate = $('#start_date').val();
                                const endDate = $('#end_date').val();

                                if (startDate && endDate) {
                                    fetchEmployeeShift(userId, startDate, 'regular');
                                }
                            });
                        }
                    } else {
                        // Hide time inputs but show both date fields
                        $('#time-inputs-container').hide();

                        // Enable date fields, make time fields readonly instead of disabled
                        $('#start_date').prop('disabled', false).prop('readonly', false);
                        $('#end_date').prop('disabled', false).prop('readonly', false);
                        $('#hour_in').prop('disabled', false).prop('readonly', true);
                        $('#hour_out').prop('disabled', false).prop('readonly', true);

                        // Add styling for readonly fields
                        $('#hour_in, #hour_out').css({
                            'background-color': '#f5f5f5',
                            'cursor': 'not-allowed'
                        });

                        // Reset time values
                        $('#hour_in').val('');
                        $('#hour_out').val('');

                        // Reset CSS for date fields
                        $('#start_date, #end_date').css({
                            'background-color': '',
                            'cursor': ''
                        });

                        // Reset penanda data
                        $('#start_date, #end_date').attr('data-required', 'true');
                    }
                }
            });


            // Tambahkan juga validasi pada form submission
            $('#time-off-form').on('submit', function(e) {
                const timeOffName = $('#time_off_id option:selected').text().toLowerCase();

                // Skip validasi untuk field yang memang tidak perlu diisi berdasarkan tipe
                if (timeOffName.includes('masuk siang')) {
                    // Untuk masuk siang, end date dan end time tidak perlu divalidasi
                    $('#end_date').removeClass('is-invalid');
                    $('#hour_out').removeClass('is-invalid');
                } else if (timeOffName.includes('pulang awal')) {
                    // Untuk pulang awal, start date dan start time tidak perlu divalidasi
                    $('#start_date').removeClass('is-invalid');
                    $('#hour_in').removeClass('is-invalid');
                }

                // Tambahkan validasi jam sesuai dengan shift
                const shiftHourStart = $('#shift_hour_start').val();
                const shiftHourEnd = $('#shift_hour_end').val();

                if (shiftHourStart && shiftHourEnd) {
                    // Validasi untuk masuk siang - jam masuk tidak boleh lebih awal dari jam shift
                    if (timeOffName.includes('masuk siang')) {
                        const hourIn = $('#hour_in').val();
                        if (hourIn && hourIn < shiftHourStart) {
                            e.preventDefault();
                            showAlert('Jam masuk siang tidak boleh lebih awal dari jam shift (' + shiftHourStart + ')');
                            $('#hour_in').addClass('is-invalid');
                        }
                    }

                    // Validasi untuk pulang awal - jam pulang tidak boleh lebih telat dari jam shift
                    else if (timeOffName.includes('pulang awal')) {
                        const hourOut = $('#hour_out').val();
                        if (hourOut && hourOut > shiftHourEnd) {
                            e.preventDefault();
                            showAlert('Jam pulang awal tidak boleh lebih telat dari jam shift (' + shiftHourEnd + ')');
                            $('#hour_out').addClass('is-invalid');
                        }
                    }
                }
            });

            $('#file_reason').prop('disabled', $('#step-3-container').hasClass('d-none'));

            if (timeOffName.includes('sick') || timeOffName.includes('sakit')) {
                $('#file-reason-label').addClass('required-field');
                $('#file-reason-help').html('Upload doctor\'s note or medical certificate');
                $('#sick-leave-warning').removeClass('d-none');
            } else {
                $('#file-reason-label').removeClass('required-field');
                $('#file-reason-help').html('Upload supporting document if needed');
                $('#sick-leave-warning').addClass('d-none');
            }

            if (timeOffId) {
                // Show loading indicator
                $('#balance-info').removeClass('d-none');
                $('#balance-text').html('<i class="fas fa-spinner fa-spin"></i> Checking available balance...');

                // Check available balance via AJAX
                $.ajax({
                    url: "{{ url('time_management/time_off/request_time_off/check-time-off-balance') }}",
                    type: 'GET',
                    data: {
                        user_id: userId,
                        time_off_id: timeOffId
                    },
                    success: function(response) {
                        if (response.hasBalance) {
                            userBalance = response.balance;

                            // Show balance info
                            $('#balance-info').removeClass('d-none');
                            $('#balance-text').html(`<i class="fas fa-check-circle"></i> Available balance: ${userBalance} days`);
                            $('#no-balance-warning').addClass('d-none');

                            // Enable date fields
                            $('#step-2-container').removeClass('d-none').addClass('active');
                            $('#start_date').prop('disabled', false);
                            $('#end_date').prop('disabled', false);

                            // Set min date for start date (today)
                            const today = new Date().toISOString().split('T')[0];
                            $('#start_date').attr('min', today);
                            $('#start_date').val('');
                            $('#end_date').val('');

                            // Reset duration info
                            $('#duration-info').removeClass('alert-success alert-danger').addClass('alert-info')
                                .html('<i class="fas fa-info-circle"></i> Select dates to calculate duration');

                            // Handle date conflicts
                            if (response.hasConflicts) {
                                // Create a datepicker with disabled dates
                                window.unavailableDates = response.unavailableDates || [];

                                $('#duration-info').nextAll('.alert').remove();

                                if (window.unavailableDates && window.unavailableDates.length > 0) {
                                    // Format the dates in a readable format
                                    const formattedDates = window.unavailableDates.map(date => {
                                        return new Date(date).toLocaleDateString('en-US', {
                                            year: 'numeric',
                                            month: 'short',
                                            day: 'numeric'
                                        });
                                    });

                                    // Add warning about unavailable dates
                                    let warningMessage = `<div class="alert alert-warning mt-3">
                                                        <i class="fas fa-exclamation-triangle"></i> You already have pending or Approved requests for these dates:<br>
                                                        <ul style="margin-bottom: 0;">` +
                                        formattedDates.map(date => `<li>${date}</li>`).join('') +
                                        `</ul></div>`;
                                    $('#duration-info').after(warningMessage);
                                } else {
                                    // No conflicts
                                    let infoMessage = '<div class="alert alert-success mt-3"><i class="fas fa-check-circle"></i> You have no other pending or approved requests.</div>';
                                    $('#duration-info').after(infoMessage);
                                }

                                // Create a function to check if a date is unavailable
                                window.isDateUnavailable = function(dateStr) {
                                    return window.unavailableDates.includes(dateStr);
                                };

                                // Override the default date validation to check for unavailable dates
                                $('#start_date, #end_date').on('input change', function() {
                                    const selectedDate = $(this).val();
                                    if (window.isDateUnavailable(selectedDate)) {
                                        $(this).addClass('is-invalid');
                                        if (!$(this).next('.invalid-feedback').length) {
                                            $(this).after('<div class="invalid-feedback">This date is unavailable due to an existing request.</div>');
                                        }
                                    } else {
                                        $(this).removeClass('is-invalid');
                                        $(this).next('.invalid-feedback').remove();
                                    }
                                });
                            }

                            // Hide and disable step 3
                            $('#step-3-container').addClass('d-none').removeClass('active');
                            $('#reason').prop('disabled', true);
                            $('#submit-btn').prop('disabled', true);
                        } else {
                            // Show no balance warning
                            $('#balance-info').addClass('d-none');
                            $('#no-balance-warning').removeClass('d-none');
                            $('#no-balance-warning span').text(
                                response.balance === 0 ?
                                'You don\'t have any balance remaining for this time off type.' :
                                'You don\'t have any assigned balance for this time off type.'
                            );

                            // Disable date fields
                            $('#step-2-container').addClass('d-none').removeClass('active');
                            $('#start_date').prop('disabled', true);
                            $('#end_date').prop('disabled', true);

                            // Hide and disable step 3
                            $('#step-3-container').addClass('d-none').removeClass('active');
                            $('#reason').prop('disabled', true);
                            $('#submit-btn').prop('disabled', true);
                        }
                    },
                    error: function(xhr) {
                        // Handle error
                        let errorMessage = 'Error checking time off balance. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        $('#balance-info').addClass('d-none');
                        $('#no-balance-warning').removeClass('d-none');
                        $('#no-balance-warning span').text(errorMessage);

                        // Disable fields
                        $('#step-2-container').addClass('d-none').removeClass('active');
                        $('#step-3-container').addClass('d-none').removeClass('active');
                    }
                });
            } else {
                // Reset all fields when no type is selected
                $('#balance-info').addClass('d-none');
                $('#no-balance-warning').addClass('d-none');
                $('#step-2-container').addClass('d-none').removeClass('active');
                $('#step-3-container').addClass('d-none').removeClass('active');
                $('#start_date').prop('disabled', true);
                $('#end_date').prop('disabled', true);
                $('#reason').prop('disabled', true);
                $('#submit-btn').prop('disabled', true);
            }
        });
        // Function to calculate date difference
        function calculateDuration() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();

            // console.log(startDate, endDate);

            if (startDate && endDate) {
                // Convert to Date objects
                const start = new Date(startDate);
                const end = new Date(endDate);

                // Check if end date is before start date
                if (end < start) {
                    $('#duration-info').removeClass('alert-info alert-success').addClass('alert-danger')
                        .html('<i class="fas fa-exclamation-triangle"></i> End date cannot be before start date');
                    $('#step-3-container').addClass('d-none').removeClass('active');
                    $('#reason').prop('disabled', true);
                    $('#submit-btn').prop('disabled', true);
                    return;
                }

                // Calculate difference in days
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // Include both start and end days

                // Check if requested days exceed available balance
                if (diffDays > userBalance) {
                    $('#duration-info').removeClass('alert-info alert-success').addClass('alert-danger')
                        .html(`<i class="fas fa-exclamation-triangle"></i> Requested duration (${diffDays} days) exceeds your available balance (${userBalance} days)`);
                    $('#step-3-container').addClass('d-none').removeClass('active');
                    $('#reason').prop('disabled', true);
                    $('#submit-btn').prop('disabled', true);
                    return;
                }

                // Check if any date in the range is unavailable
                let hasConflict = false;
                if (window.unavailableDates && window.unavailableDates.length > 0) {
                    let currentDate = new Date(startDate);
                    while (currentDate <= end) {
                        const dateStr = currentDate.toISOString().split('T')[0];
                        if (window.isDateUnavailable(dateStr)) {
                            hasConflict = true;
                            break;
                        }
                        currentDate.setDate(currentDate.getDate() + 1);
                    }
                }

                if (hasConflict) {
                    $('#duration-info').removeClass('alert-info alert-success').addClass('alert-danger')
                        .html('<i class="fas fa-exclamation-triangle"></i> Selected date range overlaps with an existing request');
                    $('#step-3-container').addClass('d-none').removeClass('active');
                    $('#reason').prop('disabled', true);
                    $('#submit-btn').prop('disabled', true);
                    return;
                }

                // Show success message with duration
                if (diffDays === 1) {
                    $('#duration-info').removeClass('alert-info alert-danger').addClass('alert-success')
                        .html('<i class="fas fa-info-circle"></i> Duration: 1 day');
                } else {
                    $('#duration-info').removeClass('alert-info alert-danger').addClass('alert-success')
                        .html('<i class="fas fa-info-circle"></i> Duration: ' + diffDays + ' days');
                }

                // Enable step 3
                $('#step-3-container').removeClass('d-none').addClass('active');
                $('#reason').prop('disabled', false);
            }
        }


        // Set maximum date for end date based on start date and available balance
        $('#start_date').change(function() {
            const startDateVal = $(this).val();

            if (startDateVal) {
                const startDate = new Date(startDateVal);

                // Calculate max end date based on available balance
                const maxDate = new Date(startDate);
                maxDate.setDate(startDate.getDate() + userBalance - 1); // -1 because we include the start date

                const maxDateStr = maxDate.toISOString().split('T')[0];
                $('#end_date').attr('min', startDateVal);
                $('#end_date').attr('max', maxDateStr);
            }
        });

        // Enable submit button when reason is provided
        $('#reason').on('input', function() {
            if ($(this).val().trim() !== '') {
                $('#submit-btn').prop('disabled', false);
                $('#file_reason').prop('disabled', false);
            } else {
                $('#submit-btn').prop('disabled', true);
            }
        });

        // Enable Start Time when step 2 is active
        $('#start_date, #end_date').change(function() {
            if (!$('#step-2-container').hasClass('d-none')) {
                // Check if the containers are hidden, not if they're disabled
                $('#hour_in').prop('readonly', $('#hour_in_container').is(':hidden'));
                $('#hour_out').prop('readonly', $('#hour_out_container').is(':hidden'));
            }
        });

        // Attach event listeners to date fields
        $('#start_date, #end_date').on('change', function() {
            // console.log("Date changed:", $('#start_date').val(), $('#end_date').val());
            calculateDuration();
        });


        // Validate form on submit
        $('#timeOffRequestForm').submit(function(e) {
            const startDate = new Date($('#start_date').val());
            const endDate = new Date($('#end_date').val());
            const diffTime = Math.abs(endDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            const timeOffName = $('#time_off_id option:selected').text().toLowerCase();
            const isSickLeave = timeOffName.includes('sick') || timeOffName.includes('sakit');
            const requiresTimeInput = $('#time-inputs-container').is(':visible');
            const isMasukSiang = timeOffName.includes('masuk siang');
            const isPulangAwal = timeOffName.includes('pulang awal');

            // Validate time fields if required
            if (requiresTimeInput) {
                if (isMasukSiang && !$('#hour_in').val()) {
                    e.preventDefault();
                    $('#hour_in').addClass('is-invalid');
                    $('<div class="invalid-feedback">Start Time is required.</div>').insertAfter($('#hour_in'));
                    return false;
                }

                if (isPulangAwal && !$('#hour_out').val()) {
                    e.preventDefault();
                    $('#hour_out').addClass('is-invalid');
                    $('<div class="invalid-feedback">End Time is required.</div>').insertAfter($('#hour_out'));
                    return false;
                }

                if (!isMasukSiang && !isPulangAwal) {
                    if (!$('#hour_in').val() || !$('#hour_out').val()) {
                        e.preventDefault();
                        if (!$('#hour_in').val()) {
                            $('#hour_in').addClass('is-invalid');
                            $('<div class="invalid-feedback">Start Time is required.</div>').insertAfter($('#hour_in'));
                        }
                        if (!$('#hour_out').val()) {
                            $('#hour_out').addClass('is-invalid');
                            $('<div class="invalid-feedback">End Time is required.</div>').insertAfter($('#hour_out'));
                        }
                        return false;
                    }
                }
            }


            if (endDate < startDate) {
                e.preventDefault();
                $('#duration-info').removeClass('alert-info alert-success').addClass('alert-danger')
                    .html('<i class="fas fa-exclamation-triangle"></i> End date cannot be before start date');
                return false;
            }

            if (diffDays > userBalance) {
                e.preventDefault();
                $('#duration-info').removeClass('alert-info alert-success').addClass('alert-danger')
                    .html(`<i class="fas fa-exclamation-triangle"></i> Requested duration (${diffDays} days) exceeds your available balance (${userBalance} days)`);
                return false;
            }

            if (isSickLeave && !$('#file_reason').val()) {
                e.preventDefault();
                $('#sick-leave-warning').removeClass('d-none');
                $('#file_reason').addClass('is-invalid');
                $('<div class="invalid-feedback">Medical certificate is required for sick leave.</div>').insertAfter($('#file_reason'));
                return false;
            }

            return true;
        });
    });


    // Function to fetch employee shift data
    function fetchEmployeeShift(userId, date, type) {
        // Add loading indicator
        showLoading(true);

        // Convert date to get day name
        const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const selectedDate = new Date(date);
        const dayName = dayNames[selectedDate.getDay()];
        const dayIndex = selectedDate.getDay(); // 0 for Sunday, 1 for Monday, etc.

        $.ajax({
            url: "{{ url('time_management/time_off/request_time_off/get-employee-shift') }}",
            type: 'GET',
            data: {
                user_id: userId,
                date: date,
                day_name: dayName
            },
            success: function(response) {
                showLoading(false);

                if (response.success) {
                    const shiftData = response.data;
                    console.log(shiftData);

                    // Parse the hour_start and hour_end as JSON if they're strings
                    let hourStart, hourEnd;

                    try {
                        // If they're JSON strings, parse them and get the value for the specific day
                        if (typeof shiftData.hour_start === 'string' && shiftData.hour_start.includes('[')) {
                            const hourStartArray = JSON.parse(shiftData.hour_start);
                            hourStart = hourStartArray[dayIndex];
                        } else {
                            hourStart = shiftData.hour_start;
                        }

                        if (typeof shiftData.hour_end === 'string' && shiftData.hour_end.includes('[')) {
                            const hourEndArray = JSON.parse(shiftData.hour_end);
                            hourEnd = hourEndArray[dayIndex];
                        } else {
                            hourEnd = shiftData.hour_end;
                        }
                    } catch (e) {
                        console.error('Error parsing shift hours:', e);
                        hourStart = shiftData.hour_start;
                        hourEnd = shiftData.hour_end;
                    }

                    // Add hidden fields to store shift hours for validation
                    if (!$('#shift_hour_start').length) {
                        $('<input>').attr({
                            type: 'hidden',
                            id: 'shift_hour_start',
                            name: 'shift_hour_start'
                        }).appendTo('#time-off-form');

                        $('<input>').attr({
                            type: 'hidden',
                            id: 'shift_hour_end',
                            name: 'shift_hour_end'
                        }).appendTo('#time-off-form');
                    }

                    // Store shift hours for validation
                    $('#shift_hour_start').val(hourStart);
                    $('#shift_hour_end').val(hourEnd);

                    // Display shift info message
                    const shiftInfoMessage = `<div class="alert alert-info">
                    Jadwal shift Anda: ${hourStart} - ${hourEnd}
                </div>`;

                    // Remove existing alert if any
                    $('#shift-info-alert').remove();

                    // Add new alert
                    $(shiftInfoMessage).attr('id', 'shift-info-alert')
                        .insertBefore('#time-inputs-container');

                    // Auto-fill fields based on type
                    // Auto-fill fields based on type
                    if (type === 'siang') {
                        // For "masuk siang", fill hour_in with shift start time
                        $('#hour_in').val(hourStart);
                        // Also fill end time even though it's readonly
                        $('#hour_out').val(hourEnd);
                    } else if (type === 'awal') {
                        // For "pulang awal", fill hour_out with shift end time
                        $('#hour_out').val(hourEnd);
                        // Also fill start time even though it's readonly
                        $('#hour_in').val(hourStart);
                    } else {
                        // Regular case, show both times for reference
                        // Let user fill in the specific times they want
                    }

                    // Set min/max constraints for time inputs
                    if (type === 'siang') {
                        $('#hour_in').attr('min', hourStart);
                        $('#hour_in').attr('max', hourEnd);
                    } else if (type === 'awal') {
                        $('#hour_out').attr('min', hourStart);
                        $('#hour_out').attr('max', hourEnd);
                    } else {
                        $('#hour_in').attr('min', hourStart);
                        $('#hour_in').attr('max', hourEnd);
                        $('#hour_out').attr('min', hourStart);
                        $('#hour_out').attr('max', hourEnd);
                    }
                } else {
                    // Show error if no shift found
                    showAlert('Tidak ditemukan jadwal shift untuk tanggal yang dipilih');
                    // Reset time fields
                    $('#hour_in').val('');
                    $('#hour_out').val('');
                }
            },
            error: function(xhr) {
                showLoading(false);
                showAlert('Terjadi kesalahan saat mengambil data shift');
                console.error('Error fetching employee shift:', xhr.responseText);
            }
        });
    }


    // Helper function to show loading state
    function showLoading(isLoading) {
        if (isLoading) {
            // Add loading spinner or change button state
            $('#time-inputs-container').append('<div id="loading-spinner" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat jadwal shift...</div>');
        } else {
            // Remove loading spinner
            $('#loading-spinner').remove();
        }
    }

    // Helper function to show alerts
    function showAlert(message, type = 'danger') {
        const alertHtml = `<div class="alert alert-${type} alert-dismissible fade show">
        ${message}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>`;

        // Remove existing alerts
        $('.alert-dismissible').remove();

        // Add new alert before form
        $(alertHtml).insertBefore('#time-off-form');

        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.alert-dismissible').alert('close');
        }, 5000);
    }
</script>
@endpush