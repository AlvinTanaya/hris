@extends('layouts.app')

@section('content')
<div class="container-fluid py-5">
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
                    <i class="fas fa-pencil me-2"></i>Edit Lesson
                </h1>
                <div class="border-bottom border-warning mt-2 mx-auto" style="width: 100px;"></div>
            </div>
        </div>

        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-primary bg-gradient text-white p-3">
                <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Lesson Information</h4>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('elearning.update_lesson', $lesson->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" name="name" class="form-control border-primary shadow-sm" id="nameInput" placeholder="Lesson Title" value="{{ old('name', $lesson->name) }}" required>
                                <label for="nameInput"><i class="fas fa-book-open me-2"></i>Lesson Title</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="number" name="duration" class="form-control border-primary shadow-sm" id="durationInput" placeholder="Duration" value="{{ old('duration', $lesson->duration) }}" required>
                                <label for="durationInput"><i class="fas fa-stopwatch me-2"></i>Duration (minutes)</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="number" name="passing_grade" class="form-control border-primary shadow-sm" id="passingGradeInput" placeholder="Passing Grade" value="{{ old('passing_grade', $lesson->passing_grade) }}" required>
                                <label for="passingGradeInput"><i class="fas fa-marker me-2"></i>Passing Grade</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="lesson_file" class="form-label fw-bold text-primary"><i class="far fa-file-pdf me-2"></i>Upload New Material (PDF)</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-primary text-white"><i class="fas fa-upload"></i></span>
                                    <input type="file" name="lesson_file" class="form-control border-primary" id="lesson_file" accept=".pdf">
                                </div>
                                @if($lesson->lesson_file)
                                <div class="mt-2">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#pdfModal">
                                        <i class="far fa-eye me-1"></i> View Current PDF
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-12">
                            <div class="card border-primary bg-light shadow-sm mb-4">
                                <div class="card-header bg-primary bg-gradient text-white d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0"><i class="fas fa-question-circle me-2"></i>Multiple Choice Questions</h4>
                                    <div class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill shadow-sm">Total Score: <span id="totalScore" class="fw-bold">{{ $questions->sum('grade') }}</span></div>
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
                                                @foreach($questions as $index => $question)
                                                <tr class="questionRow" data-question-id="{{ $question->id }}">
                                                    <td class="questionNumber align-middle text-center fw-bold bg-light">{{ $index + 1 }}</td>
                                                    <td>
                                                        <textarea name="questions[{{ $index }}][question]" class="form-control border-primary shadow-sm" rows="2" required>{{ old('questions.' . $index . '.question', $question->question) }}</textarea>
                                                        <input type="hidden" name="questions[{{ $index }}][id]" value="{{ $question->id }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="questions[{{ $index }}][grade]" class="form-control border-primary shadow-sm question-grade text-center" value="{{ old('questions.' . $index . '.grade', $question->grade) }}" required>
                                                    </td>
                                                    <td>
                                                        <div class="choices p-2 border rounded bg-white shadow-sm">
                                                            @php 
                                                                $choices = explode(';', $question->multiple_choice); 
                                                                $answerKey = $question->answer_key;
                                                            @endphp
                                                            @foreach($choices as $choiceIndex => $choice)
                                                            <div class="choice-group d-flex align-items-center mb-2">
                                                                <div class="form-check me-2">
                                                                    <input type="radio" name="questions[{{ $index }}][answer_key]" value="{{ $choice }}" class="form-check-input" {{ $choice == $answerKey ? 'checked' : '' }} required>
                                                                </div>
                                                                <input type="text" name="questions[{{ $index }}][choices][]" class="form-control form-control-sm border-primary choice-input" value="{{ $choice }}" required>
                                                                <button type="button" class="btn btn-outline-danger btn-sm ms-2 remove-choice rounded-circle">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                            @endforeach
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
                                                @endforeach
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
                            <i class="fas fa-save me-2"></i> Update Lesson
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- PDF Modal -->
<div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="pdfModalLabel">
                    <i class="far fa-file-pdf me-2"></i> View PDF Material
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($lesson->lesson_file)
                <iframe id="pdfViewer" src="{{ asset('storage/' . $lesson->lesson_file) }}" width="100%" height="600px"></iframe>
                @else
                <p class="text-center">No PDF uploaded.</p>
                @endif
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
        let questionIndex = parseInt("{{ count($questions) }}");

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
                                <input type="radio" name="questions[${questionIndex}][answer_key]" value="Option A" class="form-check-input" required checked>
                            </div>
                            <input type="text" name="questions[${questionIndex}][choices][]" class="form-control form-control-sm border-primary choice-input" value="Option A" required>
                            <button type="button" class="btn btn-outline-danger btn-sm ms-2 remove-choice rounded-circle">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="choice-group d-flex align-items-center mb-2">
                            <div class="form-check me-2">
                                <input type="radio" name="questions[${questionIndex}][answer_key]" value="Option B" class="form-check-input">
                            </div>
                            <input type="text" name="questions[${questionIndex}][choices][]" class="form-control form-control-sm border-primary choice-input" value="Option B" required>
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
            let questionRow = $(this).closest('tr');
            let questionId = questionRow.data('question-id');
            
            // Add a hidden field to mark this question for deletion if it has an ID
            if (questionId) {
                $('form').append(`<input type="hidden" name="deleted_questions[]" value="${questionId}">`);
            }
            
            questionRow.fadeOut(300, function() {
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
            let questionRow = $(this).closest('tr');
            let rowIndex = questionRow.index();
            let optionIndex = choicesContainer.find('.choice-group').length;
            let optionLabels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
            let newOptionLabel = `Option ${optionLabels[optionIndex]}`;
            
            let newOption = `
            <div class="choice-group d-flex align-items-center mb-2" style="display: none;">
                <div class="form-check me-2">
                    <input type="radio" name="questions[${rowIndex}][answer_key]" value="${newOptionLabel}" class="form-check-input">
                </div>
                <input type="text" name="questions[${rowIndex}][choices][]" class="form-control form-control-sm border-primary choice-input" value="${newOptionLabel}" required>
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
            let questionRow = $(this).closest('tr');
            
            // Don't remove if there are only two choices left
            if (choicesContainer.find('.choice-group').length > 2) {
                // Check if we're removing a selected radio button
                let radioBtn = choiceGroup.find('input[type="radio"]');
                let wasChecked = radioBtn.prop('checked');
                
                choiceGroup.fadeOut(200, function() {
                    $(this).remove();
                    
                    // If we removed a checked radio, select the first remaining option
                    if (wasChecked) {
                        questionRow.find('input[type="radio"]:first').prop('checked', true);
                    }
                });
            } else {
                showToast('Each question must have at least two options', 'error');
            }
        });

        // Update radio button values when choice text changes
        $(document).on('input', '.choice-input', function() {
            let choiceGroup = $(this).closest('.choice-group');
            let radioInput = choiceGroup.find('input[type="radio"]');
            let newValue = $(this).val();
            
            // Update the radio value to match the choice text
            radioInput.val(newValue);
            
            // If this radio was checked, update the answer key
            if (radioInput.prop('checked')) {
                radioInput.prop('checked', true);
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