@extends('layouts.app')

@section('content')
<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px">
    <i class="fas fa-edit"></i> Edit Rule Performance
</h1>

<div class="container mt-4 mx-auto">
    <div class="card shadow-sm">
        <div class="card-header text-white bg-primary">
            <h5 class="mt-2"><i class="fas fa-edit"></i> Edit Performance Rule</h5>
        </div>
        <div class="card-body">
            <form id="editPerformanceForm">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="type" class="form-label">Type</label>
                    <input type="text" class="form-control" id="type" name="type" value="{{ $criteria_performances->type }}" required>
                    <div class="invalid-feedback" id="typeError"></div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('evaluation.rule.performance.criteria.index') }}" class="btn btn-danger">
                        <i class="fas fa-arrow-left me-2"></i> Back to List
                    </a>
                    <button type="submit" class="btn btn-primary" id="updateBtn">
                        <i class="fas fa-save me-2"></i> Update Performance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#editPerformanceForm').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();
            const url = "{{ route('evaluation.rule.performance.criteria.update', $criteria_performances->id) }}";

            // Show loading indicator
            Swal.fire({
                title: 'Updating...',
                html: 'Please wait while we update your data',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            // Submit form via AJAX
            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                success: function(response) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('evaluation.rule.performance.criteria.index') }}";
                        }
                    });
                },
                error: function(xhr) {
                    Swal.close();

                    if (xhr.status === 422) {
                        // Validation errors
                        const errors = xhr.responseJSON.errors;
                        for (const field in errors) {
                            $(`#${field}`).addClass('is-invalid');
                            $(`#${field}Error`).text(errors[field][0]);
                        }
                    } else if (xhr.status === 409) {
                        // Duplicate entry error
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        // Other errors
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON.message || 'An error occurred while updating the data',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });
    });
</script>
@endpush