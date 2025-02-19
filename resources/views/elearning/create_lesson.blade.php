@extends('layouts.app')

@section('content')
<a href="{{ route('elearning.index') }}" class="btn btn-danger ms-2 px-5"> <i class="fas fa-arrow-left me-2"></i>Back</a>
<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px"><i class="fas fa-plus"></i> Add Lesson</h1>

<div class="container mt-4 mx-auto">
    <div class="card shadow-lg">
        <div class="card-body">
            <form action="{{ route('elearning.store_lesson') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label"><i class="fas fa-book-open"></i> Lesson Title</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="duration" class="form-label"><i class="fas fa-stopwatch"></i> Duration (minutes)</label>
                        <input type="number" name="duration" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="passing_grade" class="form-label"><i class="fas fa-marker"></i> Passing Grade</label>
                        <input type="number" name="passing_grade" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lesson_file" class="form-label"><i class="far fa-file-pdf"></i> Upload Material (PDF)</label>
                        <input type="file" name="lesson_file" class="form-control" accept=".pdf" required>
                    </div>
                </div>

                <h4 class="text-primary mt-4">Multiple Choice Questions</h4>
                <table class="table table-bordered" id="questionsTable">
                    <thead>
                        <tr>
                            <th style="width: 4%">No</th>
                            <th>Question</th>
                            <th style="width: 9%">Score</th>
                            <th>Choices</th>
                            <th style="width: 9%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="questionRow">
                            <td class="questionNumber">1</td>
                            <td><textarea name="questions[0][question]" class="form-control" required></textarea></td>
                            <td><input type="number" name="questions[0][grade]" class="form-control question-grade" required></td>
                            <td>
                                <div class="choices">
                                    <div class="choice-group d-flex align-items-center mb-2">
                                        <input type="radio" name="questions[0][answer_key]" value="0" required>
                                        <input type="text" name="questions[0][choices][]" class="form-control mx-2 choice-input" required placeholder="Option A">
                                        <button type="button" class="btn btn-danger remove-choice">&times;</button>
                                    </div>
                                    <div class="choice-group d-flex align-items-center mb-2">
                                        <input type="radio" name="questions[0][answer_key]" value="1">
                                        <input type="text" name="questions[0][choices][]" class="form-control mx-2 choice-input" required placeholder="Option B">
                                        <button type="button" class="btn btn-danger remove-choice">&times;</button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-info addChoice">+ Add Option</button>
                            </td>
                            <td><button type="button" class="btn btn-danger removeRow">&times; Remove</button></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-end"><strong>Total Score:</strong></td>
                            <td id="totalScore">0</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>

                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-primary" id="addQuestion">+ Add Question</button>
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>

    
</div>


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

    $(document).ready(function() {
        let questionIndex = 1;

        $('#addQuestion').click(function() {
            let newRow = `
            <tr class="questionRow">
                <td class="questionNumber">${questionIndex + 1}</td>
                <td><textarea name="questions[${questionIndex}][question]" class="form-control" required></textarea></td>
                <td><input type="number" name="questions[${questionIndex}][grade]" class="form-control question-grade" required></td>
                <td>
                    <div class="choices">
                        <div class="choice-group d-flex align-items-center mb-2">
                            <input type="radio" name="questions[${questionIndex}][answer_key]" value="0" required>
                            <input type="text" name="questions[${questionIndex}][choices][]" class="form-control mx-2 choice-input" required placeholder="Option A">
                            <button type="button" class="btn btn-danger remove-choice">&times;</button>
                        </div>
                        <div class="choice-group d-flex align-items-center mb-2">
                            <input type="radio" name="questions[${questionIndex}][answer_key]" value="1">
                            <input type="text" name="questions[${questionIndex}][choices][]" class="form-control mx-2 choice-input" required placeholder="Option B">
                            <button type="button" class="btn btn-danger remove-choice">&times;</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-info addChoice">+ Add Option</button>
                </td>
                <td><button type="button" class="btn btn-danger removeRow">&times; Remove</button></td>
            </tr>
        `;
            $('#questionsTable tbody').append(newRow);
            questionIndex++;
            updateTotalScore();
        });

        $(document).on('input', '.question-grade', function() {
            updateTotalScore();
        });

        $(document).on('click', '.removeRow', function() {
            $(this).closest('tr').remove();
            updateTotalScore();
        });
    });
</script>
@endpush
@endsection