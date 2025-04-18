@extends('layouts.app')

@section('content')


<div class="container mt-4 mx-auto">
    <!-- Page Heading -->


    <h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px">
        <i class="fas fa-plus-circle me-2"></i>Add Time Off Policy
    </h1>
    </h1>


    <!-- Form Card -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="text-primary mt-2"><i class="fas fa-clipboard-list"></i> Time Off Policy Form</h5>
            <a href="{{ route('time.off.policy.index') }}" class="btn btn-danger">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('time.off.policy.store') }}" method="POST" id="timeOffForm">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="time_off_name" class="form-label">Time Off Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="time_off_name" name="time_off_name" value="{{ old('time_off_name') }}" required>
                        <div class="invalid-feedback">Time Off Name is required.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="quota" class="form-label">Quota<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quota" name="quota" value="{{ old('quota') }}" min="0" required>
                        <div class="invalid-feedback">Quota is required and must be a non-negative number.</div>
                    </div>

                    <div class="col-md-12">
                        <label for="time_off_description" class="form-label">Description<span class="text-danger">*</span></label>
                        <textarea class="form-control" id="time_off_description" name="time_off_description" rows="3" required>{{ old('time_off_description') }}</textarea>
                        <div class="invalid-feedback">Description is required.</div>
                    </div>



                    <div class="col-md-6">
                        <label for="start_date" class="form-label">Start Date<span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                        <div class="invalid-feedback">Start Date is required.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="end_date" class="form-label">
                            End Date <small class="text-danger">(Please Check The Box for no expiration)</small>
                        </label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date') }}">
                        <div class="invalid-feedback">End Date must be after Start Date.</div>

                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="no_expiration">
                            <label class="form-check-label" for="no_expiration">No Expiration</label>
                        </div>
                    </div>




                    <div class="col-md-12 d-flex justify-content-between align-items-center">

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                id="requires_time_input" name="requires_time_input"
                                {{ old('requires_time_input') ? 'checked' : '' }}>
                            <label class="form-check-label" for="requires_time_input">
                                <i class="fas fa-clock me-1"></i> Requires Time Input (For Input Time in Time Off Request)
                            </label>
                        </div>

                        <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Save</button>
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
        $("#no_expiration").change(function() {
            if ($(this).is(":checked")) {
                $("#end_date").val("").prop("disabled", true).removeClass("is-invalid").next(".text-danger").remove();
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

        // Date validation
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