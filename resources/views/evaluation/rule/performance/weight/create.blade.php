@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Create Weight Performance</h4>
                </div>
                <div class="card-body">
                    <form id="createWeightForm">
                        @csrf

                        <div class="mb-3">
                            <label for="position_id" class="form-label fw-bold">Positions <span class="text-danger">*</span></label>
                            <select name="position_id[]" id="position_id" class="form-select select2 @error('position_id') is-invalid @enderror" multiple required>
                                @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ (is_array(old('position_id')) && in_array($position->id, old('position_id'))) ? 'selected' : '' }}>
                                    {{ $position->position }}
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">You can select multiple positions</small>
                            <div class="invalid-feedback position_id-error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="criteria_id" class="form-label fw-bold">Criteria <span class="text-danger">*</span></label>
                            <select name="criteria_id" id="criteria_id" class="form-select @error('criteria_id') is-invalid @enderror" required>
                                <option value="">-- Select Criteria --</option>
                                @foreach($criterias as $criteria)
                                <option value="{{ $criteria->id }}" {{ old('criteria_id') == $criteria->id ? 'selected' : '' }}>
                                    {{ $criteria->type }}
                                </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback criteria_id-error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="weight" class="form-label fw-bold">Weight <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" max="100" name="weight" id="weight" class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight') }}" required>
                            <small class="text-muted">Enter decimal value (e.g., 25.5)</small>
                            <div class="invalid-feedback weight-error"></div>
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">-- Select Status --</option>
                                <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <div class="invalid-feedback status-error"></div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('evaluation.rule.performance.weight.index') }}" class="btn btn-danger">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                            <button type="submit" class="btn btn-success" id="submitBtn">
                                <i class="fas fa-save me-1"></i> Save
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            placeholder: "Select positions",
            allowClear: true,
            width: '100%'
        });

        // AJAX form submission
        $('#createWeightForm').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            let form = $(this);
            let formData = form.serialize();
            let url = "{{ route('evaluation.rule.performance.weight.store') }}";

            // Show loading state
            let submitBtn = $('#submitBtn');
            let originalBtnText = submitBtn.html();
            submitBtn.prop('disabled', true);
            submitBtn.html('<i class="fas fa-spinner fa-spin me-1"></i> Processing...');

            // Clear previous errors
            $('.invalid-feedback').text('');
            $('.form-select, .form-control').removeClass('is-invalid');

            // AJAX request
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    submitBtn.html(originalBtnText);
                    submitBtn.prop('disabled', false);

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        html: response.message,
                        showConfirmButton: true,
                        confirmButtonColor: '#3085d6',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('evaluation.rule.performance.weight.index') }}";
                        }
                    });
                },
                error: function(xhr) {
                    submitBtn.html(originalBtnText);
                    submitBtn.prop('disabled', false);

                    let errorMessage = 'An error occurred';

                    // Jika ada response JSON dan ada message
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;

                        // Jika ingin menampilkan detail error untuk development
                        if (xhr.responseJSON.error_details) {
                            console.error(xhr.responseJSON.error_details);
                        }
                    }
                    // Jika error validasi Laravel
                    else if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            if (field.includes('position_id')) {
                                $('#position_id').addClass('is-invalid');
                                $('.position_id-error').text(errors[field][0]);
                            } else {
                                $(`#${field}`).addClass('is-invalid');
                                $(`.${field}-error`).text(errors[field][0]);
                            }
                        }
                        errorMessage = 'Please check the form for errors';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage,
                        showConfirmButton: true,
                        confirmButtonColor: '#d33',
                    });
                }
            });
        });
    });
</script>
@endpush