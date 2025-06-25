<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - PT Timur Jaya Indosteel</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/IconTimurJayaIndosteel.png') }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary: #0a2463;
            --primary-light: #1d52d1;
            --secondary: #247ba0;
            --accent: #3da5d9;
            --light: #f5f9ff;
            --dark: #0a1128;
            --success: #06d6a0;
            --warning: #ffd166;
            --danger: #ef476f;
        }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary), var(--secondary), var(--primary-light));
            background-attachment: fixed;
            color: var(--light);
        }

        /* Navbar Styles */
        .navbar {
            background: rgba(10, 36, 99, 0.9);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand img {
            height: 40px;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover img {
            transform: scale(1.05);
        }

        /* Login Container */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 6rem 2rem;
            min-height: calc(100vh - 140px); /* Adjusted for navbar and footer */
            position: relative;
        }

        /* Floating shapes background */
        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 20s infinite;
            z-index: 0;
        }

        .shape-1 {
            width: 200px;
            height: 200px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 150px;
            height: 150px;
            top: 20%;
            right: 15%;
            animation-delay: -5s;
        }

        .shape-3 {
            width: 300px;
            height: 300px;
            bottom: 15%;
            right: 10%;
            animation-delay: -10s;
        }

        @keyframes float {
            0% {
                transform: translate(0, 0) rotate(0deg);
            }

            25% {
                transform: translate(50px, 50px) rotate(90deg);
            }

            50% {
                transform: translate(0, 100px) rotate(180deg);
            }

            75% {
                transform: translate(-50px, 50px) rotate(270deg);
            }

            100% {
                transform: translate(0, 0) rotate(360deg);
            }
        }

        /* Login Card */
        .login-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            width: 100%;
            max-width: 500px;
            position: relative;
            z-index: 1;
            animation: slideUp 1s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            border-radius: 6px;
            max-width: 150px;
            margin-bottom: 2rem;
            animation: pulse 2s infinite;
            filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.3));
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .login-title {
            color: white;
            font-weight: 600;
            margin-bottom: 2rem;
            position: relative;
            display: inline-block;
        }

        .login-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--accent);
            border-radius: 2px;
        }

        .form-label {
            color: white;
            font-weight: 500;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.8rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--accent);
            box-shadow: 0 0 0 0.25rem rgba(61, 165, 217, 0.25);
            color: white;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }

        .input-group-text:hover {
            cursor: pointer;
            background: rgba(255, 255, 255, 0.2);
        }

        .form-check-label {
            color: white;
        }

        .form-check-input:checked {
            background-color: var(--accent);
            border-color: var(--accent);
        }

        .btn {
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            margin-bottom: 0.5rem;
        }

        .btn-primary {
            background: var(--accent);
            border: none;
            color: white;
        }

        .btn-primary:hover {
            background: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(61, 165, 217, 0.3);
        }

        .btn-outline-secondary {
            border: 2px solid white;
            color: white;
            background: transparent;
        }

        .btn-outline-secondary:hover {
            background: white;
            color: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 255, 255, 0.2);
        }

        .invalid-feedback {
            color: var(--warning);
            font-size: 0.875rem;
        }

        /* Icon colors */
        .fa-envelope,
        .fa-lock,
        .fa-eye,
        .fa-eye-slash {
            color: rgba(255, 255, 255, 0.8);
        }

        /* Footer */
        .footer {
            background: rgba(10, 17, 40, 0.8);
            backdrop-filter: blur(10px);
            padding: 2rem 0;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            text-align: center;
        }

        .footer-logo {
            height: 40px;
            opacity: 0.8;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .footer-text {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 0.5rem;
        }

        .social-icons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1rem;
        }

        .social-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            transition: all 0.3s ease;
        }

        .social-icon:hover {
            background: var(--accent);
            color: white;
            transform: translateY(-3px);
        }

        @media (max-width: 768px) {
            .login-container {
                padding: 4rem 1rem;
            }

            .login-card {
                padding: 2rem;
                margin: 1rem;
            }

            .btn {
                width: 100%;
            }

            .row {
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('welcome') }}">
                <img src="{{ asset('storage/logoTimurJayaIndosteel.png') }}" alt="Timur Jaya Indosteel">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('welcome') }}">
                            <i class="fas fa-home me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('job_vacancy.index') }}">
                            <i class="fas fa-briefcase me-1"></i> Careers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i> Admin Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Login Section -->
    <div class="login-container" data-aos="fade-up">
        <!-- Floating shapes -->
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>

        <div class="login-card">
            <!-- Logo -->
            <div class="text-center">
                <h3 class="login-title">{{ __('Admin Login') }}</h3>
            </div>
            <div class="text-center">
                <img src="{{ asset('storage/logoTimurJayaIndosteel.png') }}" alt="Logo PT Timur Jaya Indosteel" class="logo">
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Input -->
                <div class="mb-4">
                    <label for="email" class="form-label">{{ __('Email Address') }}</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                            placeholder="Enter your email">
                    </div>
                    @error('email')
                    <span class="invalid-feedback d-block">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="mb-4">
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" required autocomplete="current-password"
                            placeholder="Enter your password" value="{{ old('password') }}">

                        <span class="input-group-text" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    @error('password')
                    <span class="invalid-feedback d-block">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <!-- Remember Me -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>

                    <!-- Forgot Password -->
                    <div>
                        <a href="{{ route('password.forgot') }}" class="text-white text-decoration-none">Forget Password?</a>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-arrow-left me-2"></i>Home
                        </a>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>{{ __('Login') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <img src="{{ asset('storage/logoTimurJayaIndosteel.png') }}" alt="Timur Jaya Indosteel" class="footer-logo">
            <p class="footer-text">© {{ date('Y') }} PT Timur Jaya Indosteel. All rights reserved.</p>

            <div class="social-icons">
                <a href="https://www.instagram.com/timurjayaindosteel_official/" class="social-icon" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://api.whatsapp.com/send?phone=6281130585555&text=Halo%20PT.%20Timur%20Jaya%20Indosteel.%0ASaya%20pengunjung%20Website%2C%20Saya%20membutuhkan%20informasi%20terkait%20distributor%20besi." class="social-icon" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-whatsapp"></i>
                </a>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Toggle Password Visibility
        $(document).ready(function() {
            // Toggle Password Visibility
            $('#togglePassword').on('click', function() {
                let passwordInput = $('#password');
                let icon = $(this).find('i');

                if (passwordInput.attr('type') === "password") {
                    passwordInput.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordInput.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Load saved credentials if Remember Me was checked
            if (localStorage.getItem("rememberMe") === "true") {
                $('#email').val(localStorage.getItem("email") || "");
                $('#password').val(localStorage.getItem("password") || "");
                $('#remember').prop('checked', true);
            }

            // Handle form submission
            $('form').on('submit', function() {
                if ($('#remember').is(':checked')) {
                    localStorage.setItem("rememberMe", "true");
                    localStorage.setItem("email", $('#email').val());
                    localStorage.setItem("password", $('#password').val());
                } else {
                    localStorage.removeItem("rememberMe");
                    localStorage.removeItem("email");
                    localStorage.removeItem("password");
                }
            });
        });
    </script>
</body>

</html>