<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Learning Quiz Platform</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e3c72;
            --secondary-color: #2a5298;
            --accent-color: #ffc107;
            --text-color: #ffffff;
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 100px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color), #2c3e50);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .quiz-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .quiz-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .quiz-title {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 2.2rem;
            position: relative;
            display: inline-block;
        }

        .quiz-title:after {
            content: "";
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 70%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-light), var(--success));
            border-radius: 2px;
        }

        .timer-container {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .timer-container:before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, var(--primary), var(--primary-light));
        }

        .timer-title {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .timer-display {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
        }

        .time-unit {
            text-align: center;
            width: 80px;
        }

        .time-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            background: var(--light);
            padding: 0.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .time-label {
            font-size: 0.9rem;
            color: var(--gray);
            margin-top: 0.5rem;
        }

        .question-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .question-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .question-card:before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, var(--primary), var(--primary-light));
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }

        .question-number {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary);
        }

        .question-indicator {
            background: var(--primary);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .question-text {
            font-size: 1.2rem;
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 1.5rem;
        }

        .options-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .option-item {
            position: relative;
        }

        .option-input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .option-label {
            display: block;
            padding: 1rem 1.5rem;
            background: var(--light);
            border-radius: 12px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            cursor: pointer;
            font-weight: 500;
        }

        .option-input:checked+.option-label {
            border-color: var(--primary);
            background: rgba(67, 97, 238, 0.05);
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.15);
        }

        .option-label:hover {
            background: rgba(67, 97, 238, 0.05);
            transform: translateX(5px);
        }

        .navigation-container {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }

        .nav-button {
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .prev-button {
            background: white;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .next-button,
        .submit-button {
            background: var(--primary);
            color: white;
        }

        .nav-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .progress-container {
            margin-bottom: 2rem;
        }

        .progress-bar {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--success));
            border-radius: 4px;
            width: 66.66%;
            transition: width 0.5s ease;
        }

        .progress-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: var(--gray);
        }

        .timer-warning {
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
                box-shadow: 0 5px 15px rgba(247, 37, 133, 0.3);
            }

            100% {
                transform: scale(1);
            }
        }

        @media (max-width: 768px) {
            .timer-display {
                gap: 1rem;
            }

            .time-unit {
                width: 60px;
            }

            .time-value {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px">
            <i class="fas fa-pencil"></i> E-learning Quiz
        </h1>

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
                <input hidden type="text" name="user_id" value="{{ Auth::user()->id }}">


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
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Prevent back button
            history.pushState(null, null, location.href);
            window.onpopstate = function() {
                history.go(1);
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Tombol back tidak bisa digunakan selama quiz berlangsung',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            };

            // Set unique identifier for this quiz
            const quizId = "{{ $task->invitation_id }}";
            const initialDuration = "{{$task->duration*60}}";

            // Check if timer exists in localStorage, otherwise create new
            let totalSeconds = localStorage.getItem('timeLeft_' + quizId) ?
                parseInt(localStorage.getItem('timeLeft_' + quizId)) :
                initialDuration;

            function submitForm() {
                localStorage.removeItem('timeLeft_' + quizId);
                localStorage.removeItem('quizAnswers_' + quizId);
                $('#quiz-form').off('submit').submit();
            }

            function updateTimer() {
                if (totalSeconds <= 0) {
                    localStorage.removeItem('timeLeft_' + quizId);
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

                // Add warning effect when time is running low (less than 5 minutes)
                if (totalSeconds < 300) {
                    $('#hours, #minutes, #seconds').addClass('timer-warning');
                }

                totalSeconds--;
                localStorage.setItem('timeLeft_' + quizId, totalSeconds);
                setTimeout(updateTimer, 1000);
            }

            function saveAnswers() {
                let answers = {};
                $('input[type=radio]:checked').each(function() {
                    answers[$(this).attr('name')] = $(this).val();
                });
                localStorage.setItem('quizAnswers_' + quizId, JSON.stringify(answers));
            }

            function loadAnswers() {
                let storedAnswers = localStorage.getItem('quizAnswers_' + quizId);
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

            // Show warning when user tries to refresh or close the page
            window.addEventListener('beforeunload', function(e) {
                saveAnswers();
                if (totalSeconds > 0) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
            $('#quiz-form').on('submit', function(e) {
                e.preventDefault(); // Mencegah submit default

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
                        $(this).off('submit').submit(); // Matikan event handler lalu submit
                    }
                });
            });


            loadAnswers();
            updateTimer();

            // Show welcome message
            Swal.fire({
                title: 'Quiz Dimulai!',
                text: 'Pastikan untuk menjawab semua pertanyaan sebelum waktu habis.',
                icon: 'info',
                confirmButtonText: 'Mulai'
            });
        });
    </script>
</body>

</html>