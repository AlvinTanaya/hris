<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/IconTimurJayaIndosteel.png') }}">

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

        .reset-container {
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
        }

        .reset-container::before {
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
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.8rem 1rem;
            border-radius: 10px;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-control:focus {
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
            margin-top: 1rem;
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

        .invalid-feedback {
            font-size: 0.875rem;
            color: #ff6b6b;
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

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: rgba(255, 255, 255, 0.7);
        }

        .password-input-container {
            position: relative;
        }

        .password-strength {
            height: 5px;
            margin-top: 8px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .strength-weak {
            background: #dc3545;
            width: 30%;
        }

        .strength-medium {
            background: #ffc107;
            width: 60%;
        }

        .strength-strong {
            background: #28a745;
            width: 100%;
        }

        .strength-text {
            font-size: 0.75rem;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <!-- Decorative shapes -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>

    <div class="reset-container">
        <h3>Reset Password</h3>

        @if(session('status'))
        <div class="alert alert-success text-center">{{ session('status') }}</div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="mb-3">
                <label for="email-display" class="form-label fw-bold text-warning">Email:</label>
                <div class="p-2 border rounded bg-light text-dark">
                    {{ $email }}
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <div class="password-input-container">
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password" placeholder="Enter new password">
                    <span class="password-toggle" id="toggle-password">
                        <i class="fa-regular fa-eye"></i>
                    </span>
                </div>
                <div class="password-strength" id="password-strength"></div>
                <div class="strength-text" id="strength-text"></div>
                @error('password')
                <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="password-input-container">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required autocomplete="new-password" placeholder="Re-enter new password">
                    <span class="password-toggle" id="toggle-confirmation">
                        <i class="fa-regular fa-eye"></i>
                    </span>
                </div>
                <div id="password-match" class="mt-1 text-center" style="font-size: 0.8rem;"></div>
            </div>

            <button type="submit" class="btn btn-primary">Reset Password</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // Password toggle functionality
            $('#toggle-password').click(function() {
                togglePasswordVisibility($('#password'), $(this));
            });

            $('#toggle-confirmation').click(function() {
                togglePasswordVisibility($('#password_confirmation'), $(this));
            });

            function togglePasswordVisibility(input, toggle) {
                const type = input.attr('type') === 'password' ? 'text' : 'password';
                input.attr('type', type);

                // Toggle eye icon
                const icon = toggle.find('i');
                icon.toggleClass('fa-eye fa-eye-slash');
            }

            // Password strength checker
            $('#password').on('input', function() {
                const password = $(this).val();
                let strength = 0;

                // Check password length
                if (password.length >= 8) {
                    strength += 1;
                }

                // Check for mixed case
                if (/[a-z]/.test(password) && /[A-Z]/.test(password)) {
                    strength += 1;
                }

                // Check for numbers
                if (/\d/.test(password)) {
                    strength += 1;
                }

                // Check for special characters
                if (/[^a-zA-Z\d]/.test(password)) {
                    strength += 1;
                }

                // Update strength indicator
                $('#password-strength').removeClass('strength-weak strength-medium strength-strong');
                $('#strength-text').text('');

                if (password.length === 0) {
                    $('#password-strength').css('width', '0');
                } else if (strength < 2) {
                    $('#password-strength').addClass('strength-weak').css('width', '33%');
                    $('#strength-text').text('Weak').css('color', '#dc3545');
                } else if (strength < 4) {
                    $('#password-strength').addClass('strength-medium').css('width', '66%');
                    $('#strength-text').text('Medium').css('color', '#ffc107');
                } else {
                    $('#password-strength').addClass('strength-strong').css('width', '100%');
                    $('#strength-text').text('Strong').css('color', '#28a745');
                }

                // Check password match if confirmation has value
                if ($('#password_confirmation').val()) {
                    checkPasswordMatch();
                }
            });

            // Check if passwords match
            $('#password_confirmation').on('input', checkPasswordMatch);

            function checkPasswordMatch() {
                const password = $('#password').val();
                const confirmation = $('#password_confirmation').val();

                if (confirmation.length === 0) {
                    $('#password-match').text('');
                    return;
                }

                if (password === confirmation) {
                    $('#password-match').text('Password cocok').css('color', '#28a745');
                } else {
                    $('#password-match').text('Password tidak cocok').css('color', '#dc3545');
                }
            }
        });
    </script>
</body>

</html>