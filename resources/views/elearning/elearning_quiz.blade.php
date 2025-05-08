<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Learning Quiz Platform</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #173b77;
            --primary-light: #2a56b5;
            --primary-dark: #0d2654; 
            --secondary-color: #4a89dc;
            --accent-color: #ffc107;
            --bg-gradient-1: #173b77;
            --bg-gradient-2: #2a56b5;
            --bg-gradient-3: #4a89dc;
            --text-color: #ffffff;
            --text-dark: #2c3e50;
            --gray-light: #f8f9fa;
            --gray: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f4f8;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .quiz-wrapper {
            padding: 2rem 0;
            position: relative;
        }

        .quiz-wrapper::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 280px;
            background: linear-gradient(135deg, var(--bg-gradient-1), var(--bg-gradient-2), var(--bg-gradient-3));
            z-index: -1;
        }

        .quiz-header {
            color: white;
            text-align: center;
            margin-bottom: 2.5rem;
            padding-top: 1rem;
        }

        .quiz-header h1 {
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .quiz-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .main-container {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
        }

        .timer-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            border-left: 5px solid var(--primary-color);
            transition: all 0.3s ease;
        }

        .timer-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1rem;
            font-size: 1.3rem;
            text-align: center;
        }

        .timer-display {
            display: flex;
            justify-content: center;
            gap: 1.8rem;
        }

        .time-unit {
            text-align: center;
        }

        .time-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--text-dark);
            background: var(--gray-light);
            padding: 0.8rem 1.2rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            min-width: 70px;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .time-label {
            font-size: 0.9rem;
            color: var(--gray);
            margin-top: 0.7rem;
            font-weight: 500;
        }

        .progress-container {
            background: white;
            border-radius: 16px;
            padding: 1.2rem 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            border-left: 5px solid var(--primary-color);
        }

        .progress-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 0.8rem;
            font-size: 1.1rem;
        }

        .progress-bar {
            height: 10px;
            background: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 5px;
            transition: width 0.5s ease;
        }

        .progress-stats {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: var(--gray);
            font-weight: 500;
        }

        .question-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
        }

        .question-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            padding: 1.2rem 1.5rem;
            border-bottom: none;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }

        .card-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .question-number {
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
        }

        .question-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 500;
            backdrop-filter: blur(5px);
        }

        .card-body {
            padding: 1.8rem;
        }

        .question-text {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1.8rem;
            line-height: 1.5;
        }

        .choices-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-check {
            margin-bottom: 0;
            padding-left: 0;
        }

        .custom-radio {
            position: relative;
        }

        .form-check-input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .form-check-label {
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            padding: 1.2rem 1.5rem 1.2rem 3.5rem !important;
            position: relative;
            cursor: pointer;
            font-weight: 500;
            color: var(--text-dark);
            background: #f8f9fa;
        }

        .form-check-label:before {
            content: '';
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            width: 22px;
            height: 22px;
            border: 2px solid #cbd3da;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .form-check-input:checked + .form-check-label:before {
            border-color: var(--primary-color);
            background-color: var(--primary-color);
            box-shadow: inset 0 0 0 4px white;
        }

        .form-check-input:checked + .form-check-label {
            border-color: var(--primary-color);
            background: rgba(42, 86, 181, 0.05);
            box-shadow: 0 5px 15px rgba(42, 86, 181, 0.1);
            transform: translateX(7px);
        }

        .form-check-label:hover {
            background: rgba(42, 86, 181, 0.03);
            transform: translateX(5px);
        }

        .submit-btn {
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(42, 86, 181, 0.3);
            transition: all 0.3s ease;
            margin-bottom: 3rem;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(42, 86, 181, 0.4);
            background: linear-gradient(90deg, var(--primary-light), var(--primary-color));
        }

        .timer-warning .time-value {
            color: var(--danger);
            animation: pulse 1s infinite;
        }

        .floating-nav {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 100;
        }

        .floating-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .floating-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .quiz-footer {
            text-align: center;
            margin-top: 2rem;
            margin-bottom: 4rem;
            color: var(--gray);
            font-size: 0.9rem;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.03);
                box-shadow: 0 5px 20px rgba(220, 53, 69, 0.4);
            }
            100% {
                transform: scale(1);
            }
        }

        @media (max-width: 768px) {
            .quiz-wrapper::before {
                height: 220px;
            }

            .quiz-header h1 {
                font-size: 2rem;
            }

            .timer-display {
                gap: 1rem;
            }

            .time-value {
                font-size: 1.8rem;
                min-width: 60px;
                padding: 0.6rem 0.8rem;
            }

            .question-text {
                font-size: 1.1rem;
            }

            .form-check-label {
                padding: 1rem 1.2rem 1rem 3rem !important;
            }

            .card-body {
                padding: 1.5rem;
            }

            .submit-btn {
                padding: 0.8rem 1.5rem;
                font-size: 1rem;
            }
        }

        /* Utility Classes */
        .bg-primary-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        }
        
        .shadow-hover {
            transition: all 0.3s ease;
        }
        
        .shadow-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
</head>

<body>
    <div class="quiz-wrapper">
        <div class="quiz-header">
            <h1><i class="fas fa-graduation-cap me-2"></i> E-Learning Quiz</h1>
            <p>Answer all questions before the time runs out</p>
        </div>

        <div class="container main-container">
            <!-- Timer Card -->
            <div class="timer-card shadow-hover">
                <h3 class="timer-title"><i class="fas fa-clock me-2"></i> Time Remaining</h3>
                <div class="timer-display">
                    <div class="time-unit">
                        <div class="time-value" id="hours">00</div>
                        <div class="time-label">Hours</div>
                    </div>
                    <div class="time-unit">
                        <div class="time-value" id="minutes">00</div>
                        <div class="time-label">Minutes</div>
                    </div>
                    <div class="time-unit">
                        <div class="time-value" id="seconds">00</div>
                        <div class="time-label">Seconds</div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="progress-container shadow-hover">
                <h4 class="progress-title"><i class="fas fa-tasks me-2"></i> Quiz Progress</h4>
                <div class="progress-bar">
                    <div class="progress-fill" id="progress-bar" style="width: 0%"></div>
                </div>
                <div class="progress-stats">
                    <span>Completed: <strong id="answered-count">0</strong></span>
                    <span>Total questions: <strong id="total-questions">0</strong></span>
                </div>
            </div>

            <!-- Quiz Form -->
            <form action="{{ route('elearning.elearning_store_quiz', $task->invitation_id) }}" method="POST" id="quiz-form">
                @csrf
                <input hidden type="text" name="user_id" value="{{ Auth::user()->id }}">

                @foreach ($questions as $index => $q)
                <div class="question-card shadow-hover question-item" data-question-id="{{ $q->id }}">
                    <div class="card-header">
                        <div class="card-header-content">
                            <span class="question-number">Question {{ $index + 1 }}</span>
                            <span class="question-badge"><i class="fas fa-list-ol me-1"></i> {{ $index + 1 }}/{{ count($questions) }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="question-text">{{ $q->question }}</div>

                        @php $choices = explode(';', $q->multiple_choice); @endphp
                        <div class="choices-container">
                            @foreach ($choices as $choice)
                            <div class="form-check custom-radio">
                                <input class="form-check-input answer-input" type="radio" name="answers[{{ $q->id }}]"
                                    id="choice{{ $q->id }}_{{ $loop->index }}" value="{{ $choice }}" required>
                                <label class="form-check-label w-100 rounded" for="choice{{ $q->id }}_{{ $loop->index }}">
                                    {{ $choice }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach

                <button type="submit" class="btn btn-primary submit-btn w-100">
                    <i class="fas fa-paper-plane me-2"></i> Submit Quiz
                </button>
            </form>

            <div class="quiz-footer">
                <p>© 2025 E-Learning Quiz Platform. Refreshing or leaving this page is prohibited.</p>
            </div>
        </div>

        <!-- Floating Navigation -->
        <div class="floating-nav">
            <div class="floating-btn" id="scroll-top">
                <i class="fas fa-arrow-up"></i>
            </div>
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
                    title: 'Warning!',
                    text: 'Back button cannot be used during the quiz',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#173b77'
                });
            };

            // Set unique identifier for this quiz
            const quizId = "{{ $task->invitation_id }}";
            const initialDuration = "{{$task->duration*60}}";
            
            // Update total questions counter
            const totalQuestions = {{ count($questions) }};
            $('#total-questions').text(totalQuestions);

            // Check if timer exists in localStorage, otherwise create new
            let totalSeconds = localStorage.getItem('timeLeft_' + quizId) ?
                parseInt(localStorage.getItem('timeLeft_' + quizId)) :
                initialDuration;

            function submitForm() {
                // Remove the beforeunload event handler to prevent the browser alert
                window.removeEventListener('beforeunload', beforeUnloadHandler);
                
                // Clear localStorage data
                localStorage.removeItem('timeLeft_' + quizId);
                localStorage.removeItem('quizAnswers_' + quizId);
                
                // Submit the form
                $('#quiz-form').off('submit').submit();
            }

            function updateTimer() {
                if (totalSeconds <= 0) {
                    localStorage.removeItem('timeLeft_' + quizId);
                    Swal.fire({
                        title: 'Time\'s Up!',
                        text: 'Your answers will be submitted automatically',
                        icon: 'warning',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        confirmButtonColor: '#173b77'
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
                    $('.time-unit').addClass('timer-warning');
                }

                totalSeconds--;
                localStorage.setItem('timeLeft_' + quizId, totalSeconds);
                setTimeout(updateTimer, 1000);
            }

            function updateProgressBar() {
                const answeredCount = $('input[type=radio]:checked').length;
                const progressPercentage = (answeredCount / totalQuestions) * 100;
                
                $('#progress-bar').css('width', progressPercentage + '%');
                $('#answered-count').text(answeredCount);
                
                // Change progress bar color based on completion
                if (progressPercentage === 100) {
                    $('#progress-bar').css('background', 'linear-gradient(90deg, #28a745, #20c997)');
                }
            }

            function saveAnswers() {
                let answers = {};
                $('input[type=radio]:checked').each(function() {
                    answers[$(this).attr('name')] = $(this).val();
                });
                localStorage.setItem('quizAnswers_' + quizId, JSON.stringify(answers));
                updateProgressBar();
            }

            function loadAnswers() {
                let storedAnswers = localStorage.getItem('quizAnswers_' + quizId);
                if (storedAnswers) {
                    storedAnswers = JSON.parse(storedAnswers);
                    for (let name in storedAnswers) {
                        $(`input[name='${name}'][value='${storedAnswers[name]}']`).prop('checked', true);
                    }
                }
                updateProgressBar();
            }

            // Update progress when answer is selected
            $('input[type=radio]').on('change', function() {
                saveAnswers();
                
                // Add subtle animation to the card
                $(this).closest('.question-card').addClass('border-primary');
                setTimeout(() => {
                    $(this).closest('.question-card').removeClass('border-primary');
                }, 500);
            });

            // Scroll to top button
            $('#scroll-top').on('click', function() {
                $('html, body').animate({ scrollTop: 0 }, 'slow');
            });

            // Show/hide scroll to top button based on scroll position
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    $('#scroll-top').fadeIn();
                } else {
                    $('#scroll-top').fadeOut();
                }
            });

            // Define the beforeunload handler as a named function so we can remove it later
            function beforeUnloadHandler(e) {
                saveAnswers();
                if (totalSeconds > 0) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            }
            
            // Show warning when user tries to refresh or close the page
            window.addEventListener('beforeunload', beforeUnloadHandler);

            // Submit form confirmation
            $('#quiz-form').on('submit', function(e) {
                e.preventDefault(); // Prevent default submission

                // Count unanswered questions
                const answeredCount = $('input[type=radio]:checked').length;
                const unansweredCount = totalQuestions - answeredCount;
                
                let confirmMessage = 'Are you sure you want to submit your answers?';
                if (unansweredCount > 0) {
                    confirmMessage = `You have ${unansweredCount} unanswered question(s). Submit anyway?`;
                }

                Swal.fire({
                    title: 'Confirm Submission',
                    text: confirmMessage,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Submit!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    confirmButtonColor: '#173b77',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Remove the beforeunload event handler to prevent the browser alert
                        window.removeEventListener('beforeunload', beforeUnloadHandler);
                        
                        // Clear localStorage data
                        localStorage.removeItem('timeLeft_' + quizId);
                        localStorage.removeItem('quizAnswers_' + quizId);
                        
                        // Disable event handler then submit
                        $(this).off('submit').submit();
                    }
                });
            });

            // Initialize
            loadAnswers();
            updateTimer();
            updateProgressBar();

            // Show welcome message
            Swal.fire({
                title: 'Quiz Started!',
                html: `
                    <p>Welcome to the E-Learning Quiz Platform.</p>
                    <p>Be sure to answer all questions before time runs out.</p>
                    <ul class="text-start">
                        <li>Total questions: ${totalQuestions}</li>
                        <li>Time: ${Math.floor(initialDuration/60)} minutes</li>
                        <li>Your answers are saved automatically</li>
                    </ul>
                `,
                icon: 'info',
                confirmButtonText: 'Start Quiz',
                confirmButtonColor: '#173b77',
                allowOutsideClick: false
            });
        });
    </script>
</body>

</html>