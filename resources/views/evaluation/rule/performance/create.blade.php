@extends('layouts.app')

@section('content')
<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px">
    <i class="fas fa-chart-line"></i> Create Rule Performance
</h1>

<div class="container mt-4 mx-auto">
    <div class="card shadow-sm">
        <div class="card-header text-white bg-primary">
            <h5 class="mt-2"><i class="fas fa-plus-circle"></i> Add New Rule Performance</h5>
        </div>
        <div class="card-body">
            <form id="performanceForm">
                @csrf
                <div class="mb-3">
                    <label for="type" class="form-label">Type</label>
                    <input type="text" class="form-control" id="type" name="type" required>
                    <div class="invalid-feedback" id="typeError"></div>
                </div>
                <div class="mb-3">
                    <label for="weight" class="form-label">Weight</label>
                    <input type="number" class="form-control" id="weight" name="weight" min="0" max="100" step="0.01" required>
                    <div class="invalid-feedback" id="weightError"></div>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between mt-5">
                    <a href="{{ route('evaluation.rule.performance.index') }}" class="btn btn-danger">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save me-2"></i>Save Performance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#performanceForm').on('submit', function(e) {
            e.preventDefault();

            // Show loading indicator
            Swal.fire({
                title: 'Processing...',
                html: 'Please wait while we save your data',
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
                url: "{{ route('evaluation.rule.performance.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('evaluation.rule.performance.index') }}";
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
                            text: 'An error occurred while saving the data',
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