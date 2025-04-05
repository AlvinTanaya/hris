@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h3 class="card-title m-0 fw-bold">Create Discipline Grade Rule</h3>
                    <a href="{{ route('evaluation.rule.discipline.grade.index') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('evaluation.rule.discipline.grade.store') }}" method="POST" id="gradeForm">
                        @csrf
                        
                        @if ($errors->any())
                        <div class="alert alert-danger border-start border-danger border-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        
                        <div id="overlap-error" class="alert alert-danger border-start border-danger border-4" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i> <span id="overlap-error-message"></span>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="grade" name="grade" required maxlength="2" value="{{ old('grade') }}" placeholder="Grade">
                                    <label for="grade">Grade <span class="text-danger">*</span></label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control range-check" id="min_score" name="min_score" min="0" required value="{{ old('min_score', 0) }}" placeholder="Min Score">
                                    <label for="min_score">Min Score <span class="text-danger">*</span></label>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i> Minimum score will be set to 0 if empty or negative
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control range-check" id="max_score" name="max_score" min="0" value="{{ old('max_score') }}" placeholder="Max Score">
                                    <label for="max_score">Max Score</label>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i> Leave empty for no upper limit
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-floating mb-4">
                            <textarea class="form-control" id="description" name="description" style="height: 100px" placeholder="Description">{{ old('description') }}</textarea>
                            <label for="description">Description</label>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('evaluation.rule.discipline.grade.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                                <i class="fas fa-save me-1"></i> Create Grade Rule
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    const $minScoreInput = $('#min_score');
    const $maxScoreInput = $('#max_score');
    const $overlapError = $('#overlap-error');
    const $overlapErrorMessage = $('#overlap-error-message');
    const $form = $('#gradeForm');
    const $submitBtn = $('#submitBtn');
    
    // Ensure min_score is never negative on input
    $minScoreInput.on('input', function() {
        const value = parseInt($(this).val());
        if (value < 0 || isNaN(value)) {
            $(this).val(0);
        }
    });
    
    // Function to validate ranges
    function validateRanges() {
        const min = parseInt($minScoreInput.val()) || 0;
        const max = $maxScoreInput.val() ? parseInt($maxScoreInput.val()) : null;
        
        // Ensure min is never negative
        if (min < 0) {
            $minScoreInput.val(0);
        }
        
        // Basic validation
        if (max !== null && min >= max) {
            $overlapErrorMessage.text('Maximum score must be greater than minimum score');
            $overlapError.fadeIn();
            $submitBtn.prop('disabled', true);
            return false;
        }
        
        // Check for overlapping ranges via AJAX
        $.ajax({
            url: '{{ route("evaluation.rule.discipline.grade.check-overlap") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: JSON.stringify({
                min_score: min,
                max_score: max
            }),
            contentType: 'application/json',
            dataType: 'json',
            success: function(data) {
                if (!data.valid) {
                    $overlapErrorMessage.text(data.message);
                    $overlapError.fadeIn();
                    $submitBtn.prop('disabled', true);
                } else {
                    $overlapError.fadeOut();
                    $submitBtn.prop('disabled', false);
                }
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }
    
    // Add input event listeners with debounce for better performance
    let debounceTimer;
    $('.range-check').on('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(validateRanges, 300);
    });
    
    // Validate on page load if values are present
    if ($minScoreInput.val() || $maxScoreInput.val()) {
        validateRanges();
    }
    
    // Form submission validation
    $form.on('submit', function(e) {
        e.preventDefault();
        
        // Ensure min_score is not negative
        const minValue = parseInt($minScoreInput.val());
        if (minValue < 0 || isNaN(minValue)) {
            $minScoreInput.val(0);
        }
        
        // Final overlap check
        $.ajax({
            url: '{{ route("evaluation.rule.discipline.grade.check-overlap") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: JSON.stringify({
                min_score: parseInt($minScoreInput.val()) || 0,
                max_score: $maxScoreInput.val() ? parseInt($maxScoreInput.val()) : null
            }),
            contentType: 'application/json',
            dataType: 'json',
            success: function(data) {
                if (data.valid) {
                    $form[0].submit();
                } else {
                    $overlapErrorMessage.text(data.message);
                    $overlapError.fadeIn();
                    $('html, body').animate({ scrollTop: $overlapError.offset().top - 100 }, 'slow');
                }
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    });
});
</script>
@endpush