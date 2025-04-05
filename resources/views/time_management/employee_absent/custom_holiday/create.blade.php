@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Add New Holiday</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('time.custom.holiday.store') }}" method="POST" id="holidayForm">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Holiday Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div id="nationalHolidayInfo" class="text-success mt-1 d-none">
                                <i class="fas fa-check-circle"></i> <span id="holidayText"></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                            <div id="dateFeedback" class="mt-1"></div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('time.custom.holiday.index') }}" class="btn btn-danger">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                                <i class="fas fa-save"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        const dateInput = $('#date');
        const nameInput = $('#name');
        const nationalHolidayInfo = $('#nationalHolidayInfo');
        const holidayText = $('#holidayText');
        const dateFeedback = $('#dateFeedback');
        const submitBtn = $('#submitBtn');
        let isDateValid = false;

        dateInput.on('change', async function() {
            const selectedDate = $(this).val();

            // Reset state
            nameInput.val('');
            nationalHolidayInfo.addClass('d-none');
            dateFeedback.empty();
            submitBtn.prop('disabled', true);
            isDateValid = false;

            if (!selectedDate) return;

            const dateObj = new Date(selectedDate);
            const year = dateObj.getFullYear();
            const month = (dateObj.getMonth() + 1).toString().padStart(2, '0');
            const day = dateObj.getDate().toString().padStart(2, '0');
            const formattedDate = `${year}-${month}-${day}`;

            try {
                // Reject Sundays
                if (dateObj.getDay() === 0) {
                    showFeedback('Error: Cannot add holiday on Sunday!', 'danger');
                    return;
                }

                // Check national holidays API
                const apiResponse = await $.ajax({
                    url: `https://api-harilibur.vercel.app/api?year=${year}`,
                    method: 'GET',
                    dataType: 'json'
                });

                // Find matching holiday
                const existingHoliday = apiResponse.find(item => {
                    const apiDate = new Date(item.holiday_date);
                    const apiFormatted = `${apiDate.getFullYear()}-${(apiDate.getMonth()+1).toString().padStart(2, '0')}-${apiDate.getDate().toString().padStart(2, '0')}`;
                    return apiFormatted === formattedDate;
                });

                if (existingHoliday) {
                    nameInput.val(existingHoliday.holiday_name);
                    
                    if (existingHoliday.is_national_holiday) {
                        // National holiday - disable saving
                        holidayText.text(`${existingHoliday.holiday_name} is a national holiday`);
                        nationalHolidayInfo.removeClass('d-none');
                        showFeedback('Error: Cannot add custom holiday on a national holiday!', 'danger');
                        return;
                    } else {
                        // Local holiday - allow but inform
                        holidayText.text(`${existingHoliday.holiday_name} is a local holiday`);
                        nationalHolidayInfo.removeClass('d-none');
                        showFeedback('Note: This date has a local holiday', 'info');
                    }
                }

                // Check database for existing holidays
                const dbCheck = await $.ajax({
                    url: "{{ route('time.custom.holiday.check-date') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        date: selectedDate
                    }
                });

                if (dbCheck.exists) {
                    showFeedback('A custom holiday already exists for this date!', 'danger');
                    return;
                }

                // If all checks passed
                showFeedback('Date is available', 'success');
                isDateValid = true;
                submitBtn.prop('disabled', false);

            } catch (error) {
                console.error('Error checking date:', error);
                showFeedback('Error validating date. Please try again.', 'danger');
            }
        });

        function showFeedback(message, type) {
            dateFeedback.html(`<div class="text-${type}"><i class="fas ${type === 'success' ? 'fa-check-circle' : 
                              type === 'info' ? 'fa-info-circle' : 'fa-exclamation-triangle'}"></i> ${message}</div>`);
        }
    });
</script>
@endpush