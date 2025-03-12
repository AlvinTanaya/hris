@extends('layouts.app')

@section('content')
<h1 class="text-center text-warning" style="margin-bottom: 45px; margin-top:25px">
    <i class="fas fa-calendar-plus"></i> Request Shift Change
</h1>

<div class="container mt-4 mx-auto">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="text-primary"><i class="fas fa-plus-circle me-2"></i>New Shift Change Request</h5>
            <a href="{{ route('change.shift.index', auth()->user()->id) }}" class="btn btn-danger">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('change.shift.store') }}" method="POST" id="shiftChangeForm">
                @csrf
                <input type="hidden" name="user_id" value="{{ $id }}">

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Select a date range for your shift change request.
                            The system will automatically check your assigned shifts for these dates and create appropriate requests.
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Request Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Request Type</label>
                                    <select name="request_type" id="requestType" class="form-select @error('request_type') is-invalid @enderror">
                                        <option value="exchange">Exchange with another employee</option>
                                        <option value="change">Change shift (no exchange)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Date Range</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="date_change_start" class="form-label">Start Date</label>
                                            <input type="date" name="date_change_start" id="date_change_start" class="form-control @error('date_change_start') is-invalid @enderror" required>
                                            @error('date_change_start')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="date_change_end" class="form-label">End Date</label>
                                            <input type="date" name="date_change_end" id="date_change_end" class="form-control @error('date_change_end') is-invalid @enderror" required>
                                            <div id="date-range-error" class="text-danger" style="display: none;">
                                                Maximum date range is 6 days
                                            </div>
                                            @error('date_change_end')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>


                                </div>
                                <div class="row">
                                    <div class="col-md-12" id="cekExistanceData">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Reason Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="reason_change" class="form-label">Reason for Change</label>
                                    <textarea name="reason_change" id="reason_change" rows="3" class="form-control @error('reason_change') is-invalid @enderror" required></textarea>
                                    @error('reason_change')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="exchangeSection">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">Exchange With</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning mb-3">
                                        <i class="fas fa-exclamation-triangle me-2"></i> Please select your date range first, then click "Find Exchange Partners" to see available employees.
                                    </div>

                                    <div class="mb-3">
                                        <button type="button" id="findExchangeBtn" class="btn btn-primary mb-3">
                                            <i class="fas fa-search me-2"></i>Find Exchange Partners
                                        </button>

                                        <div id="exchangePartnersContainer" style="display: none;">
                                            <!-- Exchange partners will be loaded here per shift batch -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3" id="shiftPreviewSection" style="display: none;">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Shift Change Preview</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Date Range</th>
                                                <th>Current Shift</th>
                                                <th>New Shift</th>
                                                <th>Exchange Partner</th>
                                            </tr>
                                        </thead>
                                        <tbody id="shiftPreviewTable">
                                            <!-- Will be populated via JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" id="submitRequest" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Submit Request
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
        // Set minimum date to today
        let today = new Date().toISOString().split('T')[0];
        $('#date_change_start').attr('min', today);
        $('#date_change_end').attr('min', today);

        // Arrays to store holidays and existing requests
        let holidays = [];
        let existingRequests = [];

        // Fetch holidays for current year
        const currentYear = new Date().getFullYear();
        $.ajax({
            url: `https://api-harilibur.vercel.app/api?year=${currentYear}`,
            method: 'GET',
            success: function(response) {
                holidays = response;
                console.log("Holidays loaded:", holidays);
            },
            error: function(error) {
                console.error("Failed to load holidays:", error);
            }
        });

        // Get existing requests
        $.ajax({
            url: '{{ route("change.shift.get-existing-requests") }}',
            method: 'GET',
            data: {
                user_id: '{{ auth()->user()->id }}'
            },
            success: function(response) {
                existingRequests = response.requests || [];
                console.log("Existing requests loaded:", existingRequests);
            },
            error: function(error) {
                console.error("Failed to load existing requests:", error);
            }
        });

        $('#date_change_start').on('change', function() {
            let startDate = $(this).val();
            if (startDate) {
                let maxEndDate = new Date(startDate);
                maxEndDate.setDate(maxEndDate.getDate() + 6); // Add 6 days

                let formattedMaxEndDate = maxEndDate.toISOString().split("T")[0];

                // Set min and max bounds for End Date
                $('#date_change_end').attr('min', startDate);
                $('#date_change_end').attr('max', formattedMaxEndDate);

                // Reset endDate if not within new bounds
                if ($('#date_change_end').val() < startDate || $('#date_change_end').val() > formattedMaxEndDate) {
                    $('#date_change_end').val('');
                }

                $('#date-range-error').hide();
                $('#date-warnings').hide();
            }
        });

        $('#date_change_end').on('change', function() {
            let startDate = $('#date_change_start').val();
            let endDate = $(this).val();

            if (startDate && endDate) {
                let diffDays = (new Date(endDate) - new Date(startDate)) / (1000 * 60 * 60 * 24);

                if (diffDays > 6) {
                    $('#date-range-error').show();
                    $(this).val(''); // Reset endDate if more than 6 days
                } else {
                    $('#date-range-error').hide();
                    validateDateRange();
                }
            }
        });

        // Store shift batches for processing
        let shiftBatches = [];
        let selectedExchangePartners = {};
        let validDates = [];

        // Handle request type change
        $('#requestType').change(function() {
            if ($(this).val() === 'exchange') {
                $('#exchangeSection').show();
            } else {
                $('#exchangeSection').hide();
                $('#exchangePartnersContainer').empty();
            }
            updatePreview();
        });

        // Validate date range and check for holidays/existing requests
        function validateDateRange() {
            const startDate = $('#date_change_start').val();
            const endDate = $('#date_change_end').val();

            if (!startDate || !endDate) {
                return false;
            }

            // Calculate the difference in days
            const diffTime = Math.abs(new Date(endDate) - new Date(startDate));
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 to include both start and end dates

            if (diffDays > 7) {
                $('#date-range-error').text('Maximum date range allowed is 6 days').show();
                return false;
            }

            // Check if date range includes holidays/Sundays/existing requests
            let warnings = [];
            validDates = [];
            let invalidDates = [];
            let dateMap = {}; // For displaying user-friendly message

            for (let d = new Date(startDate); d <= new Date(endDate); d.setDate(d.getDate() + 1)) {
                const dateStr = d.toISOString().split('T')[0];
                const friendlyDate = d.toLocaleDateString('en-US', {
                    weekday: 'short',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
                dateMap[dateStr] = friendlyDate;

                // Check if Sunday
                if (d.getDay() === 0) {
                    invalidDates.push({
                        date: dateStr,
                        reason: 'Sunday'
                    });
                    continue;
                }

                // Check if holiday
                const holiday = holidays.find(h => h.holiday_date === dateStr);
                if (holiday) {
                    invalidDates.push({
                        date: dateStr,
                        reason: `Holiday: ${holiday.holiday_name}`
                    });
                    continue;
                }

                // Check if already requested
                const existingRequest = existingRequests.find(r =>
                    (r.status_change === 'Pending' || r.status_change === 'Approved') &&
                    new Date(dateStr) >= new Date(r.date_change_start) &&
                    new Date(dateStr) <= new Date(r.date_change_end)
                );

                if (existingRequest) {
                    invalidDates.push({
                        date: dateStr,
                        reason: `Already has a ${existingRequest.status_change.toLowerCase()} request`
                    });
                    continue;
                }

                validDates.push(dateStr);
            }

            // Create warning messages for invalid dates
            if (invalidDates.length > 0) {
                let warningHTML = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>The following dates will be excluded:</div><ul>';

                invalidDates.forEach(item => {
                    warningHTML += `<li>${dateMap[item.date]}: ${item.reason}</li>`;
                });

                warningHTML += '</ul>';

                if (validDates.length > 0) {
                    warningHTML += '<div class="alert alert-info mt-2"><i class="fas fa-info-circle me-2"></i>Your request will only include the valid dates.</div>';
                }

                $('#date-warnings').html(warningHTML).show();
            } else {
                $('#date-warnings').hide();
            }

            if (validDates.length === 0) {
                $('#date-warnings').append('<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i>No valid dates in selected range. Please select different dates.</div>');
                return false;
            }

            // Update valid date range for preview
            const minValidDate = validDates.reduce((a, b) => a < b ? a : b);
            const maxValidDate = validDates.reduce((a, b) => a > b ? a : b);

            checkAndLoadPreview(minValidDate, maxValidDate);
            return true;
        }

        // Find exchange partners button
        $('#findExchangeBtn').click(function() {
            const startDate = $('#date_change_start').val();
            const endDate = $('#date_change_end').val();

            if (!startDate || !endDate) {
                alert('Please select both start and end dates first.');
                return;
            }

            if (!validateDateRange()) {
                alert('Please check the date warnings and select valid dates.');
                return;
            }

            // Clear previous exchange partners
            $('#exchangePartnersContainer').empty();

            // For each shift batch, load potential exchange partners
            if (shiftBatches.length > 0) {
                loadExchangePartnersForBatches();
            } else {
                alert('No shift assignments found for the selected valid dates.');
            }
        });

        function loadExchangePartnersForBatches() {
            let batchPromises = [];

            shiftBatches.forEach((batch, index) => {
                let promise = new Promise((resolve) => {
                    $.ajax({
                        url: '{{ route("change.shift.get-exchange-partners") }}',
                        method: 'GET',
                        data: {
                            user_id: '{{ auth()->user()->id }}',
                            start_date: batch.start_date,
                            end_date: batch.end_date
                        },
                        success: function(response) {
                            resolve({
                                batch: batch,
                                index: index,
                                partners: response.partners || [],
                                success: response.success
                            });
                        },
                        error: function() {
                            resolve({
                                batch: batch,
                                index: index,
                                partners: [],
                                success: false
                            });
                        }
                    });
                });

                batchPromises.push(promise);
            });

            Promise.all(batchPromises).then(results => {
                $('#exchangePartnersContainer').empty();

                results.forEach(result => {
                    const {
                        batch,
                        index,
                        partners,
                        success
                    } = result;

                    // Create a card for each shift batch
                    const batchCard = $(`
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                ${batch.start_date} to ${batch.end_date} - ${batch.current_shift_type} Shift
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="exchange_partner_${index}" class="form-label">
                                    Select Exchange Partner for ${batch.current_shift_type} to ${batch.current_shift_type === 'Morning' ? 'Afternoon' : 'Morning'} Shift
                                </label>
                                <select name="exchange_partner[${index}]" id="exchange_partner_${index}" 
                                        class="form-select exchange-partner-select" 
                                        data-batch-index="${index}">
                                    <option value="">-- Select Employee --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                `);

                    const selectElement = batchCard.find(`#exchange_partner_${index}`);

                    if (partners.length > 0) {
                        partners.forEach(partner => {
                            selectElement.append(`
                            <option value="${partner.id}" data-shift="${partner.type}">${partner.name} (${partner.type} Shift)</option>
                        `);
                        });
                    } else {
                        selectElement.append('<option value="" disabled>No eligible exchange partners found</option>');
                    }

                    $('#exchangePartnersContainer').append(batchCard);
                });

                // Add change event to all exchange partner selects
                $('.exchange-partner-select').change(function() {
                    const batchIndex = $(this).data('batch-index');
                    const partnerId = $(this).val();

                    if (partnerId) {
                        selectedExchangePartners[batchIndex] = {
                            id: partnerId,
                            name: $(this).find('option:selected').text()
                        };
                    } else {
                        delete selectedExchangePartners[batchIndex];
                    }

                    updatePreview();
                });

                $('#exchangePartnersContainer').show();
            });
        }

        function checkAndLoadPreview(startDate, endDate) {
            if (startDate && endDate) {
                // Load shift preview
                $.ajax({
                    url: '{{ route("change.shift.get-shift-preview") }}',
                    method: 'GET',
                    data: {
                        user_id: '{{ auth()->user()->id }}',
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(response) {
                        if (response.shifts && response.shifts.length > 0) {
                            shiftBatches = response.shifts;
                            updatePreview();
                        } else {
                            $('#shiftPreviewTable').empty().append(`
                            <tr>
                                <td colspan="4" class="text-center">No shift assignments found for selected dates</td>
                            </tr>
                        `);
                            $('#shiftPreviewSection').show();
                            shiftBatches = [];
                        }
                    },
                    error: function() {
                        alert('Error loading shift preview. Please try again.');
                    }
                });
            }
        }

        function updatePreview() {
            $('#shiftPreviewTable').empty();

            if (shiftBatches.length > 0) {
                shiftBatches.forEach((batch, index) => {
                    let newShiftType = batch.current_shift_type === 'Morning' ? 'Afternoon' : 'Morning';
                    let exchangePartner = '';

                    if ($('#requestType').val() === 'exchange' && selectedExchangePartners[index]) {
                        exchangePartner = selectedExchangePartners[index].name;
                    } else {
                        exchangePartner = 'N/A';
                    }

                    $('#shiftPreviewTable').append(`
                    <tr>
                        <td>(${batch.start_date}) - (${batch.end_date})</td>
                        <td>${batch.current_shift_type}</td>
                        <td>${newShiftType}</td>
                        <td>${exchangePartner}</td>
                    </tr>
                `);
                });
                $('#shiftPreviewSection').show();
            }
        }

        // Form submission
        $('#shiftChangeForm').submit(function(e) {
            // Validate date range
            if (!validateDateRange()) {
                e.preventDefault();
                alert('Please review the date warnings and select valid dates.');
                return false;
            }

            // Add valid dates as hidden fields
            validDates.forEach((date, index) => {
                $('<input>').attr({
                    type: 'hidden',
                    name: `valid_dates[${index}]`,
                    value: date
                }).appendTo('#shiftChangeForm');
            });

            // If exchange is selected, validate that all batches have a partner selected
            if ($('#requestType').val() === 'exchange') {
                if (shiftBatches.length === 0) {
                    e.preventDefault();
                    alert('No shift assignments found for the selected dates.');
                    return false;
                }

                // Check if all batches have exchange partners
                let missingPartners = false;
                let requiredPartnersCount = shiftBatches.length;
                let selectedPartnersCount = Object.keys(selectedExchangePartners).length;

                if (selectedPartnersCount < requiredPartnersCount) {
                    missingPartners = true;
                }

                if (missingPartners) {
                    e.preventDefault();
                    alert('Please select an exchange partner for each shift period.');
                    return false;
                }

                // Add hidden fields for exchange partners
                Object.entries(selectedExchangePartners).forEach(([batchIndex, partner]) => {
                    $('<input>').attr({
                        type: 'hidden',
                        name: `batch_exchange_partners[${batchIndex}]`,
                        value: partner.id
                    }).appendTo('#shiftChangeForm');
                });
            }

            return true;
        });

        // Add this to UI elements
        $('<div id="date-warnings" class="mt-3" style="display: none;"></div>').insertAfter('#cekExistanceData').parent();
    });
</script>
@endpush