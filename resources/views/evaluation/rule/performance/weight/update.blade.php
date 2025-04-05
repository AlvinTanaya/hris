@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Weight Performance</h4>
                </div>
                <div class="card-body">
                    <form id="editWeightForm" data-id="{{ $weight->id }}">

                        @csrf
                        @method('PUT')

                        <input type="hidden" value="{{ $weight->id }}" id="id" name="id">

                        <div class="mb-3">
                            <label for="position_id" class="form-label fw-bold">Position <span class="text-danger">*</span></label>
                            <select name="position_id" id="position_id" class="form-select @error('position_id') is-invalid @enderror" required>
                                <option value="">-- Select Position --</option>
                                @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ old('position_id', $weight->position_id) == $position->id ? 'selected' : '' }}>
                                    {{ $position->position }}
                                </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback position_id-error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="criteria_id" class="form-label fw-bold">Criteria <span class="text-danger">*</span></label>
                            <select name="criteria_id" id="criteria_id" class="form-select @error('criteria_id') is-invalid @enderror" required>
                                <option value="">-- Select Criteria --</option>
                                @foreach($criterias as $criteria)
                                <option value="{{ $criteria->id }}" {{ old('criteria_id', $weight->criteria_id) == $criteria->id ? 'selected' : '' }}>
                                    {{ $criteria->type }}
                                </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback criteria_id-error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="weight" class="form-label fw-bold">Weight <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" max="100" name="weight" id="weight" class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight', $weight->weight) }}" required>
                            <small class="text-muted">Enter decimal value (e.g., 25.5)</small>
                            <div class="invalid-feedback weight-error"></div>
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">-- Select Status --</option>
                                <option value="Active" {{ old('status', $weight->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ old('status', $weight->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <div class="invalid-feedback status-error"></div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('evaluation.rule.performance.weight.index') }}" class="btn btn-danger">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                            <button type="submit" class="btn btn-success" id="submitBtn">
                                <i class="fas fa-save me-1"></i> Update
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        // AJAX form submission
        $('#editWeightForm').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation(); // Tambahkan ini

            let form = $(this);
            let formData = form.serialize();
            let weightId = form.data('id');
            let url = "{{ route('evaluation.rule.performance.weight.update', ':id') }}".replace(':id', weightId);

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
                type: 'POST', // Gunakan POST karena Laravel tidak bisa handle PUT langsung
                data: formData,
                headers: {
                    'X-HTTP-Method-Override': 'PUT' // Override method untuk Laravel
                },
                success: function(response) {
                    submitBtn.html(originalBtnText);
                    submitBtn.prop('disabled', false);

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'Update successful',
                        showConfirmButton: true,
                        confirmButtonColor: '#3085d6',
                    });

                    // Tidak redirect otomatis, biarkan user tetap di form
                },
                error: function(xhr) {
                    submitBtn.html(originalBtnText);
                    submitBtn.prop('disabled', false);

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            $(`#${field}`).addClass('is-invalid');
                            $(`.${field}-error`).text(errors[field][0]);
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error!',
                            text: 'Please check the form for errors',
                            showConfirmButton: true,
                            confirmButtonColor: '#d33',
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON.message || 'An error occurred',
                            showConfirmButton: true,
                            confirmButtonColor: '#d33',
                        });
                    }
                }
            });

            return false; // Tambahkan ini untuk pastikan tidak ada refresh
        });
    });
</script>
@endpush