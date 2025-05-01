@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card bg-primary bg-gradient text-white shadow-lg">
                <div class="card-header bg-primary bg-gradient d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-plus-circle me-2"></i>
                        <h4 class="mb-0">Add New E-learning Grade Rule</h4>
                    </div>
                    <a href="{{ route('evaluation.rule.grade.salary.index') }}" class="btn btn-light btn-sm rounded-pill">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                <div class="card-body bg-white text-dark rounded-bottom">
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="fas fa-info-circle fs-4 me-3"></i>
                        <div>
                            You are creating a new grade salary rule. Please fill in all required fields.
                        </div>
                    </div>

                    <form id="createForm" action="{{ route('evaluation.rule.grade.salary.store') }}" method="POST">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="grade" class="form-label fw-bold">Grade <span class="text-danger">*</span></label>
                                <select class="form-select form-control-lg @error('grade') is-invalid @enderror" id="grade" name="grade" required>
                                    <option value="" selected disabled>Select a grade</option>
                                    @php
                                    $grades = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'D-', 'E+', 'E', 'F'];
                                    @endphp

                                    @foreach($grades as $grade)
                                    <option value="{{ $grade }}" {{ old('grade') == $grade ? 'selected' : '' }}>
                                        {{ $grade }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('grade')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="grade-validation-message" class="text-danger mt-1 d-none"></div>
                                <small class="text-muted">Choose a grade that isn't already defined</small>
                            </div>

                            <div class="col-md-6">
                                <label for="value_salary" class="form-label fw-bold">Salary Value <span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" step="0.01" class="form-control @error('value_salary') is-invalid @enderror"
                                        id="value_salary" name="value_salary" value="{{ old('value_salary') }}"
                                        placeholder="Enter salary amount" required>
                                </div>
                                @error('value_salary')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Enter the salary value for this grade</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 gap-2">
                            <a href="{{ route('evaluation.rule.grade.salary.index') }}" class="btn btn-light btn-lg px-4">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" id="saveBtn" class="btn btn-primary btn-lg px-4">
                                <i class="fas fa-save me-1"></i> Save Grade Rule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    body {
        background: linear-gradient(135deg, #2b4c8a 0%, #1e3c72 100%);
    }

    .card {
        border-radius: 10px;
        overflow: hidden;
    }

    .form-control,
    .form-select,
    .input-group-text {
        border: 1px solid #ced4da;
        padding: 0.75rem 1rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .input-group-text {
        background-color: #f8f9fa;
    }

    .btn-lg {
        padding: 0.75rem 1.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        const gradeSelect = $('#grade');

        // Check if grade already exists when selecting a grade
        gradeSelect.on('change', function() {
            const selectedGrade = $(this).val();
            const validationMessage = $('#grade-validation-message');

            // Reset validation state
            gradeSelect.removeClass('is-invalid');
            validationMessage.addClass('d-none');

            if (selectedGrade) {
                // Send AJAX request to check if grade exists
                $.ajax({
                    url: '{{ route("evaluation.rule.grade.salary.check") }}',
                    type: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'grade': selectedGrade
                    },
                    success: function(response) {
                        if (response.exists) {
                            // Grade already exists, show error
                            gradeSelect.addClass('is-invalid');
                            validationMessage.removeClass('d-none').html('<i class="fas fa-exclamation-circle me-1"></i> This grade already has a salary rule defined.');
                            $('#saveBtn').prop('disabled', true);
                        } else {
                            // Grade is available, enable submit button
                            $('#saveBtn').prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error checking grade:', xhr.responseText);
                    }
                });
            }
        });

        // Form submission with validation
        $('#createForm').on('submit', function(e) {
            if (gradeSelect.hasClass('is-invalid')) {
                e.preventDefault();
                return false;
            }
            return true;
        });
    });
</script>
@endpush