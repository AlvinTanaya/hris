@extends('layouts.app')

@section('content')
</a>
<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px"><i class="fas fa-pencil"></i> E-learning Quiz</h1>

<div class="container mt-4 mx-auto">

    <!-- Timer -->
    <div class="card shadow-sm mb-4">
        <div class="card-body text-center">
            <h3 class="text-primary">Waktu Tersisa:</h3>
            <div class="d-flex justify-content-center gap-3 my-2">
                <div class="bg-light rounded p-3">
                    <span id="hours" class="h4">00</span>
                    <small class="d-block text-muted">Jam</small>
                </div>
                <div class="bg-light rounded p-3">
                    <span id="minutes" class="h4">00</span>
                    <small class="d-block text-muted">Menit</small>
                </div>
                <div class="bg-light rounded p-3">
                    <span id="seconds" class="h4">00</span>
                    <small class="d-block text-muted">Detik</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Questions Form -->
    <form action="{{ route('elearning.elearning_store_quiz', $task->invitation_id) }}" method="POST" id="quiz-form">
        @csrf

        @foreach ($questions as $index => $q)
        <div class="card shadow-sm mb-4 question-card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>Pertanyaan {{ $index + 1 }}</span>
                <span class="badge bg-light text-primary">{{ $index + 1 }}/{{ count($questions) }}</span>
            </div>
            <div class="card-body">
                <h5 class="mb-4">{{ $q->question }}</h5>

                @php $choices = explode(';', $q->multiple_choice); @endphp
                <div class="choices-container">
                    @foreach ($choices as $choice)
                    <div class="form-check custom-radio mb-3">
                        <input class="form-check-input" type="radio" name="answers[{{ $q->id }}]"
                            id="choice{{ $q->id }}_{{ $loop->index }}" value="{{ $choice }}" required>
                        <label class="form-check-label p-3 rounded w-100 hover-effect"
                            for="choice{{ $q->id }}_{{ $loop->index }}">
                            {{ $choice }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach

        <button type="submit" class="btn btn-warning btn-lg w-100 mt-3 mb-5">Submit Quiz</button>
    </form>
</div>

<style>
    .custom-radio .form-check-label {
        border: 2px solid #dee2e6;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .custom-radio .form-check-input:checked+.form-check-label {
        background-color: #e9ecef;
        border-color: #0d6efd;
    }

    .hover-effect:hover {
        background-color: #f8f9fa;
        transform: translateY(-2px);
    }

    .question-card {
        transition: transform 0.3s ease;
    }

    .question-card:hover {
        transform: translateY(-5px);
    }
</style>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        let totalSeconds = localStorage.getItem('timeLeft') ?
            parseInt(localStorage.getItem('timeLeft')) : {{$task->duration * 60}};

        function submitForm() {
            localStorage.removeItem('timeLeft');
            localStorage.removeItem('quizAnswers');
            $('#quiz-form').off('submit').submit();
        }

        function updateTimer() {
            if (totalSeconds <= 0) {
                localStorage.removeItem('timeLeft');
                Swal.fire({
                    title: 'Waktu Habis!',
                    text: 'Jawaban akan dikirim secara otomatis',
                    icon: 'warning',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    submitForm();
                });
                return;
            }

            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            $('#hours').text(String(hours).padStart(2, '0'));
            $('#minutes').text(String(minutes).padStart(2, '0'));
            $('#seconds').text(String(seconds).padStart(2, '0'));

            totalSeconds--;
            localStorage.setItem('timeLeft', totalSeconds);
            setTimeout(updateTimer, 1000);
        }

        function saveAnswers() {
            let answers = {};
            $('input[type=radio]:checked').each(function() {
                answers[$(this).attr('name')] = $(this).val();
            });
            localStorage.setItem('quizAnswers', JSON.stringify(answers));
        }

        function loadAnswers() {
            let storedAnswers = localStorage.getItem('quizAnswers');
            if (storedAnswers) {
                storedAnswers = JSON.parse(storedAnswers);
                for (let name in storedAnswers) {
                    $(`input[name='${name}'][value='${storedAnswers[name]}']`).prop('checked', true);
                }
            }
        }

        $('input[type=radio]').on('change', function() {
            saveAnswers();
        });

        $('#quiz-form').on('submit', function(e) {
            if (totalSeconds > 0) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Submit',
                    text: 'Apakah Anda yakin ingin mengirim jawaban?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Submit!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitForm();
                    }
                });
            }
        });

        loadAnswers();
        updateTimer();
    });
</script>
@endpush