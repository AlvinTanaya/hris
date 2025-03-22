@extends('layouts.app')

@section('content')
<div class="container mt-4 mx-auto">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary">
                    <h5 class="text-white mt-2">
                        <i class="fas fa-plus-circle me-2"></i> Create Resignation Request
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('request.resign.store') }}" method="POST" id="resignForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $id }}">
                        <div class="mb-4">
                            <label for="resign_type" class="form-label">Resignation Type <span class="text-danger">*</span></label>
                            <select name="resign_type" id="resign_type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="Voluntary">Voluntary</option>
                                <option value="Retirement">Retirement</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="mb-4 d-none" id="other_reason_container">
                            <label for="other_reason" class="form-label">Please specify <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="other_reason" name="other_reason">
                        </div>

                        <div class="mb-4">
                            <label for="resign_date" class="form-label">Resignation Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="resign_date" name="resign_date" required>
                            <small class="text-muted">Your last working day</small>
                            <div id="dateWarning" class="alert alert-warning mt-2 d-none">
                                <i class="fas fa-exclamation-triangle me-2"></i>The selected date is less than the recommended 2-week notice period.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="resign_reason" class="form-label">Reason for Resignation <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="resign_reason" name="resign_reason" rows="5" required></textarea>
                            <small class="text-muted">Please provide details about your decision to resign</small>
                        </div>

                        <div class="mb-4">
                            <label for="file_path" class="form-label">Documentary Evidence <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="file_path" name="file_path" accept="image/*">
                            <small class="text-muted">Please upload supporting documents (JPEG, PNG, or JPG format)</small>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Please note:
                            <ul class="mb-0 mt-2">
                                <li>Your resignation request will be reviewed by management</li>
                                <li>The standard notice period is a minimum of 1 month.</li>

                                <li>All company property must be returned before your last day</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('request.resign.index2', ['id' => $id]) }}" class="btn btn-danger">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Submit Request
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
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Set minimum date to 1 month from today
        let today = new Date();
        let oneMonthFromNow = new Date();

        oneMonthFromNow.setMonth(today.getMonth() + 1); // 1 month from today

        let minDate = oneMonthFromNow.toISOString().split('T')[0];

        $('#resign_date').attr('min', minDate);
        // Remove max date attribute to make it unlimited

        // Check if selected date is at least 1 month from today
        $('#resign_date').on('change', function() {
            let selectedDate = new Date($(this).val());
            let selectedDateStr = selectedDate.toISOString().split('T')[0];
            let oneMonthStr = minDate;
            if (selectedDateStr < oneMonthStr) {
                $('#dateWarning').removeClass('d-none');
                $('#dateWarning').text('The selected date is less than the required 1-month notice period.');
            } else {
                $('#dateWarning').addClass('d-none');
            }
        });

        // Show/hide 'Other' reason input
        $('#resign_type').on('change', function() {
            if ($(this).val() === 'Other') {
                $('#other_reason_container').removeClass('d-none');
                $('#other_reason').attr('required', true);
            } else {
                $('#other_reason_container').addClass('d-none');
                $('#other_reason').removeAttr('required');
            }
        });
    });
</script>


@endpush