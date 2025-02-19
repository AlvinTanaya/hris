@extends('layouts.app')

@section('content')
<a href="{{ route('elearning.index') }}" class="btn btn-danger ms-2 px-5"> <i class="fas fa-arrow-left me-2"></i>Back</a>
<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px"><i class="fas fa-pencil"></i> Edit Lesson</h1>

<div class="container mt-4 mx-auto">
    <div class="card shadow-lg">
        <div class="card-body">
            <form action="{{ route('elearning.update_lesson', $lesson->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label"><i class="fas fa-book-open"></i> Lesson Title</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $lesson->name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="duration" class="form-label"><i class="fas fa-stopwatch"></i> Duration (minutes)</label>
                        <input type="number" name="duration" class="form-control" value="{{ old('duration', $lesson->duration) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="passing_grade" class="form-label"><i class="fas fa-marker"></i> Passing Grade</label>
                        <input type="number" name="passing_grade" class="form-control" value="{{ old('passing_grade', $lesson->passing_grade) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lesson_file" class="form-label"><i class="far fa-file-pdf"></i> Upload New Material (PDF)</label>
                        <input type="file" name="lesson_file" class="form-control" accept=".pdf">
                        @if($lesson->lesson_file)
                            <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#pdfModal">
                                View Current PDF
                            </button>
                        @endif

                    </div>
                </div>

                <h4 class="text-primary mt-4">Multiple Choice Questions</h4>
                <table class="table table-bordered" id="questionsTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Question</th>
                            <th>Score</th>
                            <th>Choices</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($questions as $index => $question)
                        <tr class="questionRow">
                            <td class="questionNumber">{{ $index + 1 }}</td>
                            <td><textarea name="questions[{{ $index }}][question]" class="form-control" required>{{ old('questions.' . $index . '.question', $question->question) }}</textarea></td>
                            <td><input type="number" name="questions[{{ $index }}][grade]" class="form-control" value="{{ old('questions.' . $index . '.grade', $question->grade) }}" required></td>
                            <td>
                                <div class="choices">
                                    @php $choices = explode(';', $question->multiple_choice); @endphp
                                    @foreach($choices as $choice)
                                    <div class="choice-group d-flex align-items-center mb-2">
                                        <input type="radio" name="questions[{{ $index }}][answer_key]" value="{{ $choice }}" {{ (old('questions.' . $index . '.answer_key', $question->answer_key) == $choice) ? 'checked' : '' }} required>
                                        <input type="text" name="questions[{{ $index }}][choices][]" class="form-control mx-2 choice-input" value="{{ $choice }}" required>
                                        <button type="button" class="btn btn-danger remove-choice">&times;</button>
                                    </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-info addChoice">+ Add Option</button>
                            </td>
                            <td><button type="button" class="btn btn-danger removeRow">&times; Remove</button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-primary" id="addQuestion">+ Add Question</button>
                    <button type="submit" class="btn btn-success">Update Lesson</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl"> <!-- Gunakan modal besar untuk tampilan PDF lebih nyaman -->
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="pdfModalLabel"><i class="far fa-file-pdf"></i> View PDF Material</h5>
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
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        let questionIndex = {{ count($questions) }};

        $('#addQuestion').click(function() {
            let newRow = `
            <tr class="questionRow">
                <td class="questionNumber">${questionIndex + 1}</td>
                <td><textarea name="questions[${questionIndex}][question]" class="form-control" required></textarea></td>
                <td><input type="number" name="questions[${questionIndex}][grade]" class="form-control" required></td>
                <td>
                    <div class="choices">
                        <div class="choice-group d-flex align-items-center mb-2">
                            <input type="radio" name="questions[${questionIndex}][answer_key]" value="">
                            <input type="text" name="questions[${questionIndex}][choices][]" class="form-control mx-2 choice-input" required placeholder="Option A">
                            <button type="button" class="btn btn-danger remove-choice">&times;</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-info addChoice">+ Add Option</button>
                </td>
                <td><button type="button" class="btn btn-danger removeRow">&times; Remove</button></td>
            </tr>`;
            $('#questionsTable tbody').append(newRow);
            questionIndex++;
        });

        $(document).on('click', '.addChoice', function() {
            let questionRow = $(this).closest('tr');
            let newChoice = `<div class="choice-group d-flex align-items-center mb-2">
                <input type="radio" name="questions[${questionIndex}][answer_key]" value="">
                <input type="text" name="questions[${questionIndex}][choices][]" class="form-control mx-2 choice-input" required placeholder="New Option">
                <button type="button" class="btn btn-danger remove-choice">&times;</button>
            </div>`;
            $(this).siblings('.choices').append(newChoice);
        });
    });
</script>
@endpush

@endsection
