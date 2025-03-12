@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <h3 class="mb-0"><i class="fa-solid fa-clock"></i> Create Overtime Request</h3>
            <a href="{{ url('/time_management/overtime/index2/' . Auth::user()->id) }}" class="btn btn-danger">
                <i class="fas fa-arrow-left"></i> Cancel
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('overtime.store') }}" method="POST" id="overtimeForm">
                @csrf

                <input type="hidden" name="user_id" value="{{ $employee->id }}">

                <!-- Always visible section -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="employee_name" class="form-label">Employee</label>
                            <input type="text" class="form-control" id="employee_name" value="{{ $employee->name }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                            @error('date')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div id="overtime-info-container"></div>

                <!-- Overtime details section (initially hidden) -->
                <div class="overtime-details" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Start Time</label>
                                <select class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" required>
                                    <option value="">-- Select Start Time --</option>
                                </select>
                                @error('start_time')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_time" class="form-label">End Time</label>
                                <select class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" required>
                                    <option value="">-- Select End Time --</option>
                                </select>
                                @error('end_time')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="total_hours" class="form-label">Total Hours</label>
                                <input type="number" class="form-control @error('total_hours') is-invalid @enderror" id="total_hours" name="total_hours" step="0.01" value="{{ old('total_hours') }}" readonly required>
                                @error('total_hours')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="overtime_type" class="form-label">Overtime Type</label>
                                <select class="form-control @error('overtime_type') is-invalid @enderror" id="overtime_type" name="overtime_type" required>
                                    <option value="">-- Select Type --</option>
                                    <option value="Paid_Overtime" {{ old('overtime_type') == 'Paid_Overtime' ? 'selected' : '' }}>Paid Overtime</option>
                                    <option value="Overtime_Leave" {{ old('overtime_type') == 'Overtime_Leave' ? 'selected' : '' }}>Overtime Leave</option>
                                </select>
                                @error('overtime_type')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                        @error('reason')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary" id="submitButton">
                            <i class="fas fa-save"></i> Submit Overtime Request
                        </button>

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
        const today = new Date().toISOString().split('T')[0];
        $('#date').attr('min', today);

        // Function to show/hide form sections based on state
        function toggleFormSections(show) {
            if (show) {
                $('.overtime-details').show();
            } else {
                $('.overtime-details').hide();
                // Reset values when hiding
                $('#start_time').empty().append('<option value="">-- Select Start Time --</option>');
                $('#end_time').empty().append('<option value="">-- Select End Time --</option>');
                $('#total_hours').val('');
                $('#reason').val('');
                $('#overtime_type').val('');
            }
        }

        // Initially hide the overtime details
        toggleFormSections(false);

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

            // Check if selected date is valid and get shift information
            $.ajax({
                url: "{{ url('time_management/overtime/check-overtime-eligibility') }}",
                type: 'POST',
                data: {
                    user_id: userId,
                    date: selectedDate,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.eligible) {
                        // Employee is eligible for overtime
                        toggleFormSections(true);

                        // Store shift info in global variable for later use
                        window.shiftInfo = response.shift_info;

                        // Populate the start time dropdown
                        populateStartTimeDropdown(response.shift_end_time);

                        // Show remaining overtime limits
                        let infoMessage = `
                    <div class="alert alert-info mt-3">
                        <h5>Overtime Availability:</h5>
                        <ul>
                            <li>Today: ${response.remaining.daily} hours remaining (max 4 hours/day)</li>
                            <li>This week: ${response.remaining.weekly} hours remaining (max 18 hours/week)</li>
                            <li>This month: ${response.remaining.monthly} hours remaining (max 56 hours/month)</li>
                        </ul>
                    </div>`;

                        // Show shift information
                        if (response.shift_info) {
                            infoMessage += `
                        <div class="alert alert-info mt-2">
                            <h5>Shift Information:</h5>
                            <p>Type: ${response.shift_info.type}</p>
                            <p>Working hours: ${response.shift_info.start_time} - ${response.shift_info.end_time}</p>
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
                    <div class="alert alert-danger mt-3">
                        <h5>Overtime not available:</h5>
                        <p>${response.message}</p>
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
                <div class="alert alert-danger mt-3">
                    <h5>Error:</h5>
                    <p>Failed to check overtime eligibility. Please try again.</p>
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
        }

        // Function to calculate total hours
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
                    Warning: End time must be after start time.
                </div>`);
                    $('#submitButton').prop('disabled', true);
                } else {
                    $('#submitButton').prop('disabled', false);
                }
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
            const overtimeType = $('#overtime_type').val();
            const reason = $('#reason').val();

            let hasErrors = false;

            // Clear previous validation messages
            $('.validation-error').remove();

            // Validate all fields are filled
            if (!startTime || !endTime || isNaN(totalHours) || totalHours <= 0 || !overtimeType || !reason.trim()) {
                $('#submitButton').before(`
            <div class="alert alert-danger validation-error">
                Please fill in all required fields correctly.
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