<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/IconTimurJayaIndosteel.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3c72, #2a5298, #2c3e50);
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        /* Decorative shapes */
        .shape {
            position: absolute;
            opacity: 0.2;
            z-index: -1;
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: linear-gradient(#ffc107, #ff6b6b);
            top: -100px;
            right: -50px;
        }

        .shape-2 {
            width: 200px;
            height: 200px;
            background: linear-gradient(#4facfe, #00f2fe);
            bottom: -100px;
            left: -50px;
            clip-path: polygon(50% 0%, 100% 38%, 82% 100%, 18% 100%, 0% 38%);
        }

        .shape-3 {
            width: 150px;
            height: 150px;
            background: linear-gradient(#f093fb, #f5576c);
            bottom: 50px;
            right: 20%;
            clip-path: polygon(25% 0%, 100% 0%, 75% 100%, 0% 100%);
            transform: rotate(45deg);
        }

        .otp-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        .otp-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #ffc107, #ff6b6b, #4facfe, #00f2fe, #f093fb, #f5576c);
            z-index: 2;
        }

        h3 {
            text-align: center;
            font-weight: 700;
            margin-bottom: 1.8rem;
            background: linear-gradient(90deg, #ffc107, #ff6b6b);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            position: relative;
            display: inline-block;
            left: 50%;
            transform: translateX(-50%);
        }

        h3::after {
            content: "";
            position: absolute;
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #ffc107, #ff6b6b);
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 3px;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.8rem 1rem;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .otp-input-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .otp-input {
            width: 45px;
            height: 50px;
            text-align: center;
            font-size: 1.25rem;
            border-radius: 8px;
            margin: 0 4px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }

        .otp-input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #ffc107;
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }

        .btn-primary {
            background: linear-gradient(90deg, #ffc107, #ff6b6b);
            border: none;
            width: 100%;
            font-weight: 600;
            padding: 0.8rem;
            margin-top: 0.5rem;
            transition: 0.3s;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, #ff6b6b, #ffc107);
            box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
            transform: translateY(-2px);
        }

        .btn-resend {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 10px;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-resend:hover:not(:disabled) {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
        }

        .btn-resend:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .timer {
            margin-top: 1.2rem;
            font-size: 0.9rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
        }

        .user-email {
            font-weight: 600;
            text-decoration: underline;
            text-decoration-color: #ffc107;
            text-decoration-thickness: 2px;
            text-underline-offset: 3px;
        }

        .invalid-feedback {
            font-size: 0.875rem;
            color: #ff6b6b;
            margin-top: 0.5rem;
        }

        .alert {
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
            border: none;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            border-left: 4px solid #dc3545;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
            }
        }

        .pulse {
            animation: pulse 1.5s infinite;
        }
    </style>
</head>

<body>
    <!-- Decorative shapes -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>

    <div class="otp-container">
        <h3>OTP Verification</h3>

        <!-- Flash Message -->
        @if(session('status'))
        <div class="alert alert-success text-center">{{ session('status') }}</div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
        @endif

        <div id="alert-container"></div>

        <p class="text-center mb-4">OTP code has been sent to <span class="user-email" id="display-email"></span></p>

        <!-- Form Verify OTP -->
        <form id="verify-otp-form" method="POST" action="{{ route('otp.verify.submit') }}">
            @csrf
            <input type="hidden" id="email" name="email" value="">

            <div class="mb-4">
                <label for="otp-input" class="form-label text-center w-100">Enter OTP Code</label>
                <div class="otp-input-container">
                    <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                    <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                    <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                    <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                    <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                    <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                </div>
                <input type="hidden" id="otp" name="otp" required>
                <div class="invalid-feedback text-center d-none" id="otp-error"></div>
            </div>

            <button type="submit" class="btn btn-primary pulse">Verify OTP</button>

            <div class="timer text-center" id="timer">
                Resend in <span id="countdown">02:00</span>
            </div>
        </form>
        <div class="text-center">
            <button type="button" class="btn btn-resend mt-2" id="resend-btn" disabled>
                <i class="fas fa-redo-alt me-1"></i> Resend OTP
            </button>
        </div>

    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Set up CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Get email from URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const email = urlParams.get('email');

            if (email) {
                $('#email').val(email);
                $('#display-email').text(email);

                // Auto focus to first OTP input
                $('.otp-input').first().focus();

                // Start countdown
                startCountdown(120); // 2 minutes
            } else {
                // Redirect to forgot password page if no email
                window.location.href = "{{ route('password.forgot') }}";
            }

            // Handle OTP input
            $('.otp-input').on('input', function() {
                // Allow only numbers
                $(this).val($(this).val().replace(/[^0-9]/g, ''));

                // Auto focus to next input
                if ($(this).val() && $(this).index() < $('.otp-input').length - 1) {
                    $('.otp-input').eq($(this).index() + 1).focus();
                }

                // Combine all inputs to hidden field
                combineOtpValues();
            });

            $('.otp-input').on('keydown', function(e) {
                // On backspace, move to previous input if current is empty
                if (e.key === 'Backspace') {
                    if (!$(this).val() && $(this).index() > 0) {
                        $('.otp-input').eq($(this).index() - 1).focus();
                        e.preventDefault();
                    }
                }
            });

            function combineOtpValues() {
                let combinedOtp = '';
                $('.otp-input').each(function() {
                    combinedOtp += $(this).val();
                });
                $('#otp').val(combinedOtp);

                // Hide error if inputs are being filled
                $('#otp-error').addClass('d-none');
            }

            // Handle resend OTP button
            $('#resend-btn').on('click', function() {
                if ($(this).prop('disabled')) return;

                // Show loading state
                var originalText = $(this).html();
                $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
                $(this).prop('disabled', true);

                // Send AJAX request
                $.ajax({
                    url: "{{ route('password.otp.send') }}",
                    type: 'POST',
                    data: {
                        email: $('#email').val(),
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Restart countdown
                            startCountdown(120);

                            // Show success message
                            $('#alert-container').html('<div class="alert alert-success text-center">' + (response.message || "New OTP has been sent to your email.") + '</div>');

                            // Reset OTP inputs
                            $('.otp-input').val('');
                            $('#otp').val('');
                            $('.otp-input').first().focus();

                            // Hide success message after 5 seconds
                            setTimeout(function() {
                                $('#alert-container').empty();
                            }, 5000);
                        } else {
                            // Show error message
                            $('#alert-container').html('<div class="alert alert-danger text-center">' + (response.message || "Failed to send OTP.") + '</div>');
                            $('#resend-btn').html(originalText);
                            $('#resend-btn').prop('disabled', false);
                        }
                    },
                    error: function() {
                        $('#alert-container').html('<div class="alert alert-danger text-center">An error occurred. Please try again.</div>');
                        $('#resend-btn').html(originalText);
                        $('#resend-btn').prop('disabled', false);
                    }
                });
            });

            // Start countdown timer
            function startCountdown(seconds) {
                // Clear any existing interval
                if (window.countdownInterval) {
                    clearInterval(window.countdownInterval);
                }

                $('#resend-btn').prop('disabled', true);
                $('#resend-btn').removeClass('pulse');

                const endTime = Date.now() + seconds * 1000;

                function updateCountdown() {
                    const timeLeft = Math.max(0, Math.floor((endTime - Date.now()) / 1000));
                    const minutes = Math.floor(timeLeft / 60).toString().padStart(2, '0');
                    const secs = (timeLeft % 60).toString().padStart(2, '0');

                    $('#countdown').text(`${minutes}:${secs}`);

                    if (timeLeft === 0) {
                        clearInterval(window.countdownInterval);
                        $('#resend-btn').prop('disabled', false);
                        $('#resend-btn').addClass('pulse');
                        $('#timer').html('Didn\'t receive the code? <span class="text-white-50">Try again</span>');
                    }
                }

                updateCountdown();
                window.countdownInterval = setInterval(updateCountdown, 1000);
            }
        });
    </script>
</body>

</html>