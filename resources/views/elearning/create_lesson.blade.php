@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <a href="{{ route('elearning.index') }}" class="btn btn-danger px-4 shadow-sm d-flex align-items-center" style="width: fit-content">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-md-12 text-center">
                <h1 class="display-4 fw-bold text-warning">
                    <i class="fas fa-plus-circle me-2"></i>Add Lesson
                </h1>
                <div class="border-bottom border-warning mt-2 mx-auto" style="width: 100px;"></div>
            </div>
        </div>

        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-primary bg-gradient text-white p-3">
                <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Lesson Information</h4>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('elearning.store_lesson') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" name="name" class="form-control border-primary shadow-sm" id="nameInput" placeholder="Lesson Title" required>
                                <label for="nameInput"><i class="fas fa-book-open me-2"></i>Lesson Title</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="number" name="duration" class="form-control border-primary shadow-sm" id="durationInput" placeholder="Duration" required>
                                <label for="durationInput"><i class="fas fa-stopwatch me-2"></i>Duration (minutes)</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="number" name="passing_grade" class="form-control border-primary shadow-sm" id="passingGradeInput" placeholder="Passing Grade" required>
                                <label for="passingGradeInput"><i class="fas fa-marker me-2"></i>Passing Grade</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="lesson_file" class="form-label fw-bold text-primary"><i class="far fa-file-pdf me-2"></i>Upload Material (PDF)</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-primary text-white"><i class="fas fa-upload"></i></span>
                                    <input type="file" name="lesson_file" class="form-control border-primary" id="lesson_file" accept=".pdf" required>
                                </div>
                                <small class="text-muted">Maximum file size: 10MB</small>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-12">
                            <div class="card border-primary bg-light shadow-sm mb-4">
                                <div class="card-header bg-primary bg-gradient text-white d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0"><i class="fas fa-question-circle me-2"></i>Multiple Choice Questions</h4>
                                    <div class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill shadow-sm">Total Score: <span id="totalScore" class="fw-bold">0</span></div>
                                </div>
                                <div class="card-body p-4">
                                    <div class="table-responsive">
                                        <table class="table table-hover border" id="questionsTable">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th class="text-center" style="width: 4%">No</th>
                                                    <th>Question</th>
                                                    <th style="width: 9%" class="text-center">Score</th>
                                                    <th>Choices</th>
                                                    <th style="width: 9%" class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="questionRow">
                                                    <td class="questionNumber align-middle text-center fw-bold bg-light">1</td>
                                                    <td>
                                                        <textarea name="questions[0][question]" class="form-control border-primary shadow-sm" rows="2" placeholder="Enter your question here..." required></textarea>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="questions[0][grade]" class="form-control border-primary shadow-sm question-grade text-center" required placeholder="0">
                                                    </td>
                                                    <td>
                                                        <div class="choices p-2 border rounded bg-white shadow-sm">
                                                            <div class="choice-group d-flex align-items-center mb-2">
                                                                <div class="form-check me-2">
                                                                    <input type="radio" name="questions[0][answer_key]" value="0" class="form-check-input" required>
                                                                </div>
                                                                <input type="text" name="questions[0][choices][]" class="form-control form-control-sm border-primary choice-input" required placeholder="Option A">
                                                                <button type="button" class="btn btn-outline-danger btn-sm ms-2 remove-choice rounded-circle">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                            <div class="choice-group d-flex align-items-center mb-2">
                                                                <div class="form-check me-2">
                                                                    <input type="radio" name="questions[0][answer_key]" value="1" class="form-check-input">
                                                                </div>
                                                                <input type="text" name="questions[0][choices][]" class="form-control form-control-sm border-primary choice-input" required placeholder="Option B">
                                                                <button type="button" class="btn btn-outline-danger btn-sm ms-2 remove-choice rounded-circle">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                            <button type="button" class="btn btn-info btn-sm addChoice mt-2 w-100 text-white shadow-sm">
                                                                <i class="fas fa-plus me-1"></i> Add Option
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <button type="button" class="btn btn-danger btn-sm removeRow rounded-pill shadow-sm">
                                                            <i class="fas fa-trash me-1"></i> Remove
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-primary mt-3 shadow" id="addQuestion">
                                        <i class="fas fa-plus-circle me-1"></i> Add Question
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-success btn-lg px-5 shadow">
                            <i class="fas fa-save me-2"></i> Save Lesson
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateTotalScore() {
        let total = 0;
        $('input[name$="[grade]"]').each(function() {
            let value = parseInt($(this).val()) || 0;
            total += value;
        });
        $('#totalScore').text(total);
    }

    function updateQuestionNumbers() {
        $('.questionNumber').each(function(index) {
            $(this).text(index + 1);
        });
    }

    // Show toasts for notifications
    function showToast(message, type = 'success') {
        // Create toast container if it doesn't exist
        if (!$('#toastContainer').length) {
            $('body').append('<div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 1050;"></div>');
        }
        
        const toastId = 'toast-' + Date.now();
        const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const toast = `
            <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas ${icon} me-2"></i> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        $('#toastContainer').append(toast);
        const toastElement = new bootstrap.Toast(document.getElementById(toastId), {
            autohide: true,
            delay: 3000
        });
        toastElement.show();
        
        // Remove toast from DOM after it's hidden
        $(`#${toastId}`).on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }

    $(document).ready(function() {
        let questionIndex = 1;

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Add new question row with animation
        $('#addQuestion').click(function() {
            let newRow = `
            <tr class="questionRow" style="display: none;">
                <td class="questionNumber align-middle text-center fw-bold bg-light">${questionIndex + 1}</td>
                <td>
                    <textarea name="questions[${questionIndex}][question]" class="form-control border-primary shadow-sm" rows="2" placeholder="Enter your question here..." required></textarea>
                </td>
                <td>
                    <input type="number" name="questions[${questionIndex}][grade]" class="form-control border-primary shadow-sm question-grade text-center" required placeholder="0">
                </td>
                <td>
                    <div class="choices p-2 border rounded bg-white shadow-sm">
                        <div class="choice-group d-flex align-items-center mb-2">
                            <div class="form-check me-2">
                                <input type="radio" name="questions[${questionIndex}][answer_key]" value="0" class="form-check-input" required>
                            </div>
                            <input type="text" name="questions[${questionIndex}][choices][]" class="form-control form-control-sm border-primary choice-input" required placeholder="Option A">
                            <button type="button" class="btn btn-outline-danger btn-sm ms-2 remove-choice rounded-circle">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="choice-group d-flex align-items-center mb-2">
                            <div class="form-check me-2">
                                <input type="radio" name="questions[${questionIndex}][answer_key]" value="1" class="form-check-input">
                            </div>
                            <input type="text" name="questions[${questionIndex}][choices][]" class="form-control form-control-sm border-primary choice-input" required placeholder="Option B">
                            <button type="button" class="btn btn-outline-danger btn-sm ms-2 remove-choice rounded-circle">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <button type="button" class="btn btn-info btn-sm addChoice mt-2 w-100 text-white shadow-sm">
                            <i class="fas fa-plus me-1"></i> Add Option
                        </button>
                    </div>
                </td>
                <td class="align-middle text-center">
                    <button type="button" class="btn btn-danger btn-sm removeRow rounded-pill shadow-sm">
                        <i class="fas fa-trash me-1"></i> Remove
                    </button>
                </td>
            </tr>
            `;
            $('#questionsTable tbody').append(newRow);
            $('.questionRow:last').fadeIn(300);
            questionIndex++;
            updateTotalScore();
            showToast('New question added successfully!');
        });

        // Update total score when grade input changes
        $(document).on('input', '.question-grade', function() {
            updateTotalScore();
        });

        // Remove question row with animation
        $(document).on('click', '.removeRow', function() {
            let row = $(this).closest('tr');
            row.fadeOut(300, function() {
                $(this).remove();
                updateQuestionNumbers();
                updateTotalScore();
                showToast('Question removed successfully!');
            });
        });

        // Add new choice option with animation
        $(document).on('click', '.addChoice', function() {
            let choicesContainer = $(this).siblings('.choices');
            if (choicesContainer.length === 0) {
                choicesContainer = $(this).parent();
            }
            let questionIndex = $(this).closest('tr').index();
            let optionIndex = choicesContainer.find('.choice-group').length;
            
            let newOption = `
            <div class="choice-group d-flex align-items-center mb-2" style="display: none;">
                <div class="form-check me-2">
                    <input type="radio" name="questions[${questionIndex}][answer_key]" value="${optionIndex}" class="form-check-input">
                </div>
                <input type="text" name="questions[${questionIndex}][choices][]" class="form-control form-control-sm border-primary choice-input" required placeholder="Option ${String.fromCharCode(65 + optionIndex)}">
                <button type="button" class="btn btn-outline-danger btn-sm ms-2 remove-choice rounded-circle">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            `;
            
            $(newOption).insertBefore($(this));
            $('.choice-group:last').fadeIn(300);
            
            // Focus on the new input field
            setTimeout(function() {
                $('.choice-group:last input[type="text"]').focus();
            }, 300);
        });

        // Remove choice option with animation
        $(document).on('click', '.remove-choice', function() {
            let choiceGroup = $(this).closest('.choice-group');
            let choicesContainer = choiceGroup.parent();
            
            // Don't remove if there are only two choices left
            if (choicesContainer.find('.choice-group').length > 2) {
                choiceGroup.fadeOut(200, function() {
                    $(this).remove();
                    
                    // Update the value attributes of radio buttons
                    let questionRow = $(this).closest('tr');
                    let questionIndex = questionRow.index();
                    
                    choicesContainer.find('.choice-group').each(function(index) {
                        $(this).find('input[type="radio"]').val(index);
                    });
                });
            } else {
                showToast('Each question must have at least two options', 'error');
            }
        });

        // Form validation before submit
        $('form').on('submit', function(e) {
            let valid = true;
            
            // Check if any question has no selected answer
            $('.choices').each(function(index) {
                let hasSelectedAnswer = false;
                $(this).find('input[type="radio"]').each(function() {
                    if ($(this).is(':checked')) {
                        hasSelectedAnswer = true;
                    }
                });
                
                if (!hasSelectedAnswer) {
                    valid = false;
                    showToast(`Question #${index + 1} has no selected correct answer`, 'error');
                }
            });
            
            if (!valid) {
                e.preventDefault();
            }
        });

        // Initial setup
        updateTotalScore();
    });
</script>
@endpush