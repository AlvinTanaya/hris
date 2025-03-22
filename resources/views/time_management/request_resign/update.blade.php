@extends('layouts.app')

@section('content')
<div class="container mt-4 mx-auto">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary">
                    <h5 class="text-white mt-2">
                        <i class="fas fa-edit me-2"></i>Edit Resignation Request
                    </h5>
                </div>
                <div class="card-body">
                    @if($request_resign->resign_status !== 'Pending')
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>This request has already been {{ strtolower($request_resign->resign_status) }} and cannot be edited.
                    </div>
                    @else
                    <form action="{{ route('request.resign.update', $request_resign->id) }}" method="POST" id="resignForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="user_id" value="{{$request_resign->user_id}}">
                        <div class="mb-4">
                            <label for="resign_type" class="form-label">Resignation Type <span class="text-danger">*</span></label>
                            <select name="resign_type" id="resign_type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="Voluntary" {{ (old('resign_type', $request_resign->resign_type) == 'Voluntary') ? 'selected' : '' }}>Voluntary</option>
                                <option value="Retirement" {{ (old('resign_type', $request_resign->resign_type) == 'Retirement') ? 'selected' : '' }}>Retirement</option>
                                <option value="Other" {{ (old('resign_type', $request_resign->resign_type) == 'Other') ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="mb-4" id="other_reason_container" style="display: none;">
                            <label for="other_reason" class="form-label">Specify Other Reason <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="other_reason" name="other_reason" value="{{ old('other_reason', $request_resign->other_reason) }}">
                        </div>

                        <div class="mb-4">
                            <label for="resign_date" class="form-label">Resignation Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="resign_date" name="resign_date" value="{{ old('resign_date', $request_resign->resign_date) }}" required>
                            <small class="text-muted">Your last working day</small>
                            <div id="dateWarning" class="alert alert-warning mt-2 d-none">
                                <i class="fas fa-exclamation-triangle me-2"></i>The selected date is less than the recommended 2-week notice period.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="resign_reason" class="form-label">Reason for Resignation <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="resign_reason" name="resign_reason" rows="5" required>{{ old('resign_reason', $request_resign->resign_reason) }}</textarea>
                            <small class="text-muted">Please provide details about your decision to resign</small>
                        </div>

                        <div class="mb-4">
                            <label for="file_path" class="form-label">Documentary Evidence <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="file_path" name="file_path" accept=".jpeg,.jpg,.png">
                            <small class="text-muted">Please upload supporting documents (JPEG, PNG, or JPG format)</small>

                            @if($request_resign->file_path)
                            <div class="mt-2">
                                <p class="mb-1">Current file:</p>
                                <img src="{{ asset('storage/' . $request_resign->file_path) }}" alt="Current evidence" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                            @endif
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
                            <a href="{{ route('request.resign.index2', ['id' => $request_resign->user_id]) }}" class="btn btn-danger">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Update Request
                            </button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Get created_at date from Blade in YYYY-MM-DD format
        let createdAt = new Date("{{ $request_resign->created_at->format('Y-m-d') }}");

        // Set minimum date to 1 month from created date
        let oneMonthFromCreated = new Date(createdAt);
        oneMonthFromCreated.setMonth(createdAt.getMonth() + 1); // 1 month from created date

        let minDate = oneMonthFromCreated.toISOString().split('T')[0];

        $('#resign_date').attr('min', minDate);
        // Remove max date attribute to make it unlimited
        $('#resign_date').removeAttr('max');

        // Validate when user selects a date
        $('#resign_date').on('change', function() {
            let selectedDate = new Date($(this).val());
            let selectedDateStr = selectedDate.toISOString().split('T')[0];
            let oneMonthStr = minDate; // Already in YYYY-MM-DD format

            console.log("Selected:", selectedDateStr, "Min Allowed:", oneMonthStr);

            if (selectedDateStr < oneMonthStr) {
                $('#dateWarning').removeClass('d-none');
                $('#dateWarning').text('The selected date is less than the required 1-month notice period.');
            } else {
                $('#dateWarning').addClass('d-none');
            }
        });

        $('#resign_type').on('change', function() {
            if ($(this).val() === 'Other') {
                $('#other_reason_container').show();
            } else {
                $('#other_reason_container').hide();
            }
        }).trigger('change');
    });
</script>
@endpush