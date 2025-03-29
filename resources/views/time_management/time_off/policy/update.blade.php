@extends('layouts.app')

@section('title', 'Edit Time Off Policy')

@section('content')
<div class="container mt-4 mx-auto">
    <h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px">
        <i class="fas fa-edit me-2"></i>Edit Time Off Policy
    </h1>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="text-primary mt-2"><i class="fas fa-clipboard-list"></i> Time Off Policy Form</h5>
            <a href="{{ route('time.off.policy.index') }}" class="btn btn-danger">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('time.off.policy.update', $policy->id) }}" method="POST" id="timeOffForm">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="time_off_name" class="form-label">Time Off Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="time_off_name" name="time_off_name" value="{{ old('time_off_name', $policy->time_off_name) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="quota" class="form-label">Quota<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quota" name="quota" value="{{ old('quota', $policy->quota) }}" min="0" required>
                    </div>

                    <div class="col-md-12">
                        <label for="time_off_description" class="form-label">Description<span class="text-danger">*</span></label>
                        <textarea class="form-control" id="time_off_description" name="time_off_description" rows="3" required>{{ old('time_off_description', $policy->time_off_description) }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label for="start_date" class="form-label">Start Date<span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                            value="{{ old('start_date', $policy->start_date ? \Carbon\Carbon::parse($policy->start_date)->format('Y-m-d') : '') }}" required>

                    </div>

                    <div class="col-md-6">
                        <label for="end_date" class="form-label">End Date <small class="text-danger">(leave empty for no expiration)</small></label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                            value="{{ old('end_date', $policy->end_date ? \Carbon\Carbon::parse($policy->end_date)->format('Y-m-d') : '') }}">

                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="no_expiration" {{ $policy->end_date ? '' : 'checked' }}>
                            <label class="form-check-label" for="no_expiration">No Expiration</label>
                        </div>
                    </div>


                    <div class="col-md-12 d-flex justify-content-between align-items-center">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                id="requires_time_input" name="requires_time_input"
                                {{ old('requires_time_input', $policy->requires_time_input) ? 'checked' : '' }}>
                            <label class="form-check-label" for="requires_time_input">
                                <i class="fas fa-clock me-1"></i> Requires Time Input
                            </label>
                        </div>
                        <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($("#no_expiration").is(":checked")) {
            $("#end_date").val("").prop("disabled", true);
        }

        $("#no_expiration").change(function() {
            if ($(this).is(":checked")) {
                $("#end_date").val("").prop("disabled", true);
            } else {
                $("#end_date").prop("disabled", false);
            }
        });

        $("#timeOffForm").on("submit", function(event) {
            let isValid = true;
            $(this).find("input, textarea").each(function() {
                if (!$(this).val() && !$(this).prop("disabled")) {
                    $(this).addClass("is-invalid");
                    isValid = false;
                } else {
                    $(this).removeClass("is-invalid");
                }
            });
            if (!isValid) {
                event.preventDefault();
            }
        });

        $("#end_date").on("change", function() {
            let startDate = new Date($("#start_date").val());
            let endDate = new Date($(this).val());

            if ($("#start_date").val() && $(this).val() && endDate < startDate) {
                $(this).addClass("is-invalid").next(".text-danger").remove();
                $(this).after('<div class="text-danger mt-1">End date must be after Start Date.</div>');
            } else {
                $(this).removeClass("is-invalid").next(".text-danger").remove();
            }
        });

        $("#start_date").on("change", function() {
            $("#end_date").trigger("change");
        });
    });
</script>
@endpush