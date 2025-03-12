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
                    <form action="{{ route('request.resign.update', $request_resign->id) }}" method="POST" id="resignForm">
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

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Please note:
                            <ul class="mb-0 mt-2">
                                <li>Your resignation request will be reviewed by management</li>
                                <li>The standard notice period is typically 2-4 weeks</li>
                                <li>All company property must be returned before your last day</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('request.resign.index') }}" class="btn btn-danger">
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
        // Ambil tanggal created_at dari Blade dalam format YYYY-MM-DD
        let createdAt = new Date("{{ $request_resign->created_at->format('Y-m-d') }}");

        // Set batas minimal dan maksimal berdasarkan createdAt
        let twoWeeksFromCreated = new Date(createdAt);
        let sixMonthsFromCreated = new Date(createdAt);

        twoWeeksFromCreated.setDate(createdAt.getDate() + 14); // Minimal 2 minggu
        sixMonthsFromCreated.setMonth(createdAt.getMonth() + 6); // Maksimal 6 bulan

        let minDate = twoWeeksFromCreated.toISOString().split('T')[0];
        let maxDate = sixMonthsFromCreated.toISOString().split('T')[0];

        $('#resign_date').attr('min', minDate);
        $('#resign_date').attr('max', maxDate);

        // Validasi ketika user memilih tanggal
        $('#resign_date').on('change', function() {
            let selectedDate = new Date($(this).val());
            let selectedDateStr = selectedDate.toISOString().split('T')[0];
            let twoWeeksStr = minDate; // Sudah dalam format YYYY-MM-DD

            console.log("Selected:", selectedDateStr, "Min Allowed:", twoWeeksStr);

            if (selectedDateStr < twoWeeksStr) {
                $('#dateWarning').removeClass('d-none');
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