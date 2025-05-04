@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fa-solid fa-clock me-2"></i> Create Overtime Request</h3>
            <a href="{{ url('/time_management/overtime/management/index2/' . Auth::user()->id) }}" class="btn btn-light">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
        <div class="card-body bg-light">
            <form action="{{ route('overtime.store') }}" method="POST" id="overtimeForm">
                @csrf

                <input type="hidden" name="user_id" value="{{ $employee->id }}">

                <!-- Employee info card -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employee_name" class="form-label fw-bold"><i class="fas fa-user me-2"></i>Employee</label>
                                    <input type="text" class="form-control bg-white" id="employee_name" value="{{ $employee->name }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date" class="form-label fw-bold"><i class="fas fa-calendar-alt me-2"></i>Date</label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                                    @error('date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="text-muted">Select any date except Sundays</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info container with improved styling -->
                <div id="overtime-info-container"></div>

                <!-- Overtime details section (initially hidden) -->
                <div class="overtime-details card shadow-sm border-0 mt-4" style="display: none;">
                    <div class="card-body">
                        <h5 class="card-title mb-4 border-bottom pb-2"><i class="fas fa-info-circle me-2"></i>Overtime Details</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_time" class="form-label fw-bold"><i class="fas fa-hourglass-start me-2"></i>Start Time</label>
                                    <select class="form-select @error('start_time') is-invalid @enderror" id="start_time" name="start_time" required>
                                        <option value="">-- Select Start Time --</option>
                                    </select>
                                    @error('start_time')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_time" class="form-label fw-bold"><i class="fas fa-hourglass-end me-2"></i>End Time</label>
                                    <select class="form-select @error('end_time') is-invalid @enderror" id="end_time" name="end_time" required>
                                        <option value="">-- Select End Time --</option>
                                    </select>
                                    @error('end_time')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="total_hours" class="form-label fw-bold"><i class="fas fa-clock me-2"></i>Total Hours</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('total_hours') is-invalid @enderror" id="total_hours" name="total_hours" step="0.01" value="{{ old('total_hours') }}" readonly required>
                                        <span class="input-group-text">hours</span>
                                    </div>
                                    @error('total_hours')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="overtime_rate" class="form-label fw-bold"><i class="fas fa-money-bill-wave me-2"></i>Overtime Rate</label>
                                    <input type="text" class="form-control bg-white" id="overtime_rate" readonly>
                                    <input type="hidden" name="overtime_rate_value" id="overtime_rate_value">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="total_amount" class="form-label fw-bold"><i class="fas fa-hand-holding-usd me-2"></i>Total Amount</label>
                                    <input type="text" class="form-control bg-white" id="total_amount" readonly>
                                </div>
                            </div>
                        </div>


                        <div class="mb-4">
                            <label for="reason" class="form-label fw-bold"><i class="fas fa-comment-alt me-2"></i>Reason</label>
                            <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                            @error('reason')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitButton">
                                <i class="fas fa-save me-2"></i> Submit Overtime Request
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Include the enhanced JavaScript -->
<script>
    $(document).ready(function() {
        let overtimeRate = 0;

        // Function to show/hide form sections based on state
        function toggleFormSections(show) {
            if (show) {
                $('.overtime-details').slideDown(300);
            } else {
                $('.overtime-details').slideUp(300);
                // Reset values when hiding
                $('#start_time').empty().append('<option value="">-- Select Start Time --</option>');
                $('#end_time').empty().append('<option value="">-- Select End Time --</option>');
                $('#total_hours').val('');
                $('#reason').val('');
                $('#overtime_rate').val('');
                $('#total_amount').val('');
            }
        }

        // Initially hide the overtime details
        toggleFormSections(false);

        // Function to format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }

        // Function to get overtime rate based on date
        function getOvertimeRate(date, userId) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: "{{ url('time_management/overtime/management/get-overtime-rate') }}",
                    type: 'POST',
                    data: {
                        user_id: userId,
                        date: date,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        resolve(response.overtime_rate);
                    },
                    error: function(xhr) {
                        console.error('Error fetching overtime rate:', xhr);
                        reject(0);
                    }
                });
            });
        }

        // Function to check if date is Sunday
        function isSunday(dateString) {
            const date = new Date(dateString);
            return date.getDay() === 0; // 0 is Sunday in JavaScript
        }

        // Function to check overtime eligibility
        function checkOvertimeEligibility() {
            const selectedDate = $('#date').val();
            const userId = $('input[name="user_id"]').val();

            // Clear previous messages
            $('#overtime-info-container').empty();

            // Check if date is selected
            if (!selectedDate) {
                toggleFormSections(false);
                return;
            }

            // Check if selected date is Sunday
            if (isSunday(selectedDate)) {
                toggleFormSections(false);
                $('#overtime-info-container').html(`
                    <div class="alert alert-danger mt-3 shadow-sm">
                        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Not Available</h5>
                        <p>Overtime is not available on Sundays. Please select another date.</p>
                    </div>`);
                return;
            }

            // Check if selected date is valid and get shift information
            $.ajax({
                url: "{{ url('time_management/overtime/management/check-overtime-eligibility') }}",
                type: 'POST',
                data: {
                    user_id: userId,
                    date: selectedDate,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: async function(response) {
                    if (response.eligible) {
                        // Get overtime rate for the selected date
                        try {
                            overtimeRate = await getOvertimeRate(selectedDate, userId);
                            $('#overtime_rate').val(formatCurrency(overtimeRate));
                            $('#overtime_rate_value').val(overtimeRate);

                            // If total hours already exists, update the total amount
                            if ($('#total_hours').val()) {
                                updateTotalAmount();
                            }
                        } catch (error) {
                            console.error('Error getting overtime rate:', error);
                            overtimeRate = 0;
                            $('#overtime_rate').val('Error fetching rate');
                        }

                        // Employee is eligible for overtime
                        toggleFormSections(true);

                        // Store shift info in global variable for later use
                        window.shiftInfo = response.shift_info;

                        // Populate the start time dropdown
                        populateStartTimeDropdown(response.shift_end_time);

                        // Show remaining overtime limits
                        let infoMessage = `
                            <div class="card border-0 shadow-sm mt-3 mb-4">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Overtime Availability</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card h-100 border-info">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title"><i class="fas fa-calendar-day me-2"></i>Today</h5>
                                                    <h3 class="text-info mb-0">${response.remaining.daily} hours</h3>
                                                    <p class="text-muted mb-0">remaining (max 4 hours/day)</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card h-100 border-info">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title"><i class="fas fa-calendar-week me-2"></i>This Week</h5>
                                                    <h3 class="text-info mb-0">${response.remaining.weekly} hours</h3>
                                                    <p class="text-muted mb-0">remaining (max 18 hours/week)</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card h-100 border-info">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title"><i class="fas fa-calendar-alt me-2"></i>This Month</h5>
                                                    <h3 class="text-info mb-0">${response.remaining.monthly} hours</h3>
                                                    <p class="text-muted mb-0">remaining (max 56 hours/month)</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;

                        // Show shift information
                        if (response.shift_info) {
                            infoMessage += `
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="fas fa-business-time me-2"></i>Shift Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="fw-bold">Shift Type:</label>
                                                    <p class="mb-0">${response.shift_info.type}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="fw-bold">Working Hours:</label>
                                                    <p class="mb-0">${response.shift_info.start_time} - ${response.shift_info.end_time}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="fw-bold">Overtime Rate:</label>
                                                    <p class="mb-0">${formatCurrency(overtimeRate)} per hour</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                        }

                        // Insert message into the dedicated container
                        $('#overtime-info-container').html(infoMessage);

                        // Enable submit button
                        $('#submitButton').prop('disabled', false);
                    } else {
                        // Employee is not eligible for overtime
                        toggleFormSections(false);

                        let errorMessage = `
                            <div class="alert alert-danger mt-3 shadow-sm">
                                <h5 class="alert-heading"><i class="fas fa-exclamation-circle me-2"></i>Overtime Not Available</h5>
                                <p class="mb-0">${response.message}</p>
                            </div>`;

                        $('#overtime-info-container').html(errorMessage);

                        // Disable submit button
                        $('#submitButton').prop('disabled', true);
                    }
                },
                error: function(xhr) {
                    console.error('Error checking overtime eligibility:', xhr);
                    toggleFormSections(false);

                    $('#overtime-info-container').html(`
                        <div class="alert alert-danger mt-3 shadow-sm">
                            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Error</h5>
                            <p class="mb-0">Failed to check overtime eligibility. Please try again.</p>
                        </div>`);
                }
            });
        }

        // Function to populate start time dropdown
        function populateStartTimeDropdown(shiftEndTime) {
            // Clear current options
            $('#start_time').empty();

            // Add an empty default option
            $('#start_time').append('<option value="">-- Select Start Time --</option>');

            // Add shift end time as an option (the typical overtime start)
            $('#start_time').append(`<option value="${shiftEndTime}">${shiftEndTime}</option>`);

            // Reset end time and total hours
            $('#end_time').empty().append('<option value="">-- Select End Time --</option>');
            $('#total_hours').val('');
            $('#total_amount').val('');
        }

        // Function to update end time options based on start time
        function updateEndTimeOptions() {
            const startTime = $('#start_time').val();
            if (!startTime) return;

            // Get start time as Date object
            const startDate = new Date(`2000-01-01T${startTime}`);

            // Clear current options
            $('#end_time').empty();

            // Add an empty default option
            $('#end_time').append('<option value="">-- Select End Time --</option>');

            // Define maximum end time (either midnight or start time + 4 hours, whichever comes first)
            const midnightDate = new Date(`2000-01-01T23:59`);

            // Calculate maximum 4 hours after start time
            const maxFourHoursDate = new Date(startDate);
            maxFourHoursDate.setHours(maxFourHoursDate.getHours() + 4);

            // Use the earlier of midnight or start+4hours as the cutoff
            const cutoffTime = (midnightDate < maxFourHoursDate) ? midnightDate : maxFourHoursDate;

            // Generate hourly options from start time to cutoff time
            let currentOption = new Date(startDate);
            currentOption.setHours(currentOption.getHours() + 1); // Start with 1 hour after

            while (currentOption <= cutoffTime) {
                const timeString = currentOption.toTimeString().substring(0, 5);
                $('#end_time').append(`<option value="${timeString}">${timeString}</option>`);
                currentOption.setHours(currentOption.getHours() + 1);
            }

            // Reset total hours when start time changes
            $('#total_hours').val('');
            $('#total_amount').val('');
        }

        // Function to calculate total hours and amount
        function calculateTotalHours() {
            const startTime = $('#start_time').val();
            const endTime = $('#end_time').val();

            $('.time-warning').remove();

            if (startTime && endTime) {
                const start = new Date(`2000-01-01T${startTime}`);
                const end = new Date(`2000-01-01T${endTime}`);

                let diff = (end - start) / 1000 / 60 / 60;

                // Set the calculated hours with 2 decimal precision
                $('#total_hours').val(diff.toFixed(2));

                // Check for negative or zero hours (shouldn't happen with dropdown but just in case)
                if (diff <= 0) {
                    $('#total_hours').after(`
                        <div class="text-danger mt-1 time-warning">
                            <i class="fas fa-exclamation-triangle me-1"></i> Warning: End time must be after start time.
                        </div>`);
                    $('#submitButton').prop('disabled', true);
                    $('#total_amount').val('');
                } else {
                    $('#submitButton').prop('disabled', false);
                    updateTotalAmount();
                }
            }
        }

        // Function to update total amount based on hours and rate
        function updateTotalAmount() {
            const hours = parseFloat($('#total_hours').val()) || 0;
            if (hours > 0 && overtimeRate > 0) {
                const totalAmount = hours * overtimeRate;
                $('#total_amount').val(formatCurrency(totalAmount));
            } else {
                $('#total_amount').val('');
            }
        }

        // Initialize date check on page load
        checkOvertimeEligibility();

        // When date input changes
        $('#date').change(function() {
            checkOvertimeEligibility();
        });

        // When start time changes, update end time options
        $('#start_time').change(function() {
            updateEndTimeOptions();
            // Reset end time when start time changes
            $('#end_time').val('');
            $('#total_hours').val('');
            $('#total_amount').val('');
        });

        // Calculate total hours when end_time changes
        $('#end_time').change(function() {
            calculateTotalHours();
        });

        // Form validation before submit
        $('#overtimeForm').submit(function(e) {
            const startTime = $('#start_time').val();
            const endTime = $('#end_time').val();
            const totalHours = parseFloat($('#total_hours').val());
            const reason = $('#reason').val();

            let hasErrors = false;

            // Clear previous validation messages
            $('.validation-error').remove();

            // Validate all fields are filled
            if (!startTime || !endTime || isNaN(totalHours) || totalHours <= 0 || !reason.trim()) {
                $('#submitButton').before(`
                    <div class="alert alert-danger validation-error mb-3">
                        <i class="fas fa-exclamation-circle me-2"></i> Please fill in all required fields correctly.
                    </div>`);
                hasErrors = true;
            }

            if (hasErrors) {
                e.preventDefault();
                return false;
            }

            // Confirm submission if hours exceed limit (shouldn't happen with dropdown but just in case)
            if (totalHours > 4) {
                e.preventDefault();
                Swal.fire({
                    title: 'Confirm Submission',
                    text: 'The requested overtime exceeds 4 hours. Are you sure you want to continue?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, submit it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#overtimeForm').off('submit').submit();
                    }
                });
            }
        });
    });
</script>
@endpush