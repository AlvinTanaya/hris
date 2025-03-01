<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to PT Timur Jaya Indosteel</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/IconTimurJayaIndosteel.png') }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3c72, #2a5298, #2c3e50);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: white;
            overflow-x: hidden;
        }

        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .main-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .content-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 1200px;
            z-index: 2;
        }

        .title-section {
            text-align: center;
            margin-bottom: 3rem;
        }

        .main-title {
            font-weight: 700;
            font-size: 2.8rem;
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .title-underline {
            width: 100px;
            height: 4px;
            background: #ffc107;
            margin: 0.5rem auto 1.5rem;
            border-radius: 2px;
        }

        .company-tagline {
            font-size: 1.2rem;
            margin-top: 1rem;
        }

        .logo-container {
            margin-bottom: 2rem;
        }

        .logo {
            width: 180px;
            border-radius: 6px;
            filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.3));
        }

        .cards-section {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 2rem;
            width: 100%;
        }

        .card-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            height: 350px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px 0 rgba(31, 38, 135, 0.5);
        }

        .card-icon {
            font-size: 3rem;
            color: #ffc107;
            margin-bottom: 1.5rem;
        }

        .card-title {
            font-weight: 600;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .title-line {
            width: 60px;
            height: 3px;
            background: #ffc107;
            margin: 0.5rem auto 1rem;
            border-radius: 2px;
        }

        .card-text {
            margin-bottom: 1.5rem;
            font-size: 1rem;
            line-height: 1.5;
        }

        .btn-action {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.8rem 2.5rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            width: 100%;
            max-width: 220px;
            text-decoration: none;
        }

        .btn-action i {
            margin-right: 8px;
        }

        .btn-apply {
            background: transparent;
            border: 2px solid white;
            color: white;
        }

        .btn-apply:hover {
            background: white;
            color: #1e3c72;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 255, 255, 0.2);
        }

        .btn-login {
            background: #ffc107;
            border: none;
            color: #1e3c72;
        }

        .btn-login:hover {
            background: #ffca2c;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 193, 7, 0.3);
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
            width: 300px;
            height: 300px;
            top: 15%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 200px;
            height: 200px;
            top: 20%;
            right: 10%;
            animation-delay: -5s;
        }

        .shape-3 {
            width: 400px;
            height: 400px;
            bottom: 15%;
            right: 20%;
            animation-delay: -10s;
        }

        .shape-4 {
            width: 350px;
            height: 350px;
            bottom: 10%;
            left: 15%;
            animation-delay: -15s;
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

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .cards-section {
                flex-direction: column;
            }

            .card-box {
                max-width: 500px;
                margin-bottom: 2rem;
            }
        }

        @media (max-width: 576px) {
            .main-title {
                font-size: 2rem;
            }

            .company-tagline {
                font-size: 1rem;
            }

            .card-box {
                padding: 2rem;
                max-width: 100%;
            }

            .card-title {
                font-size: 1.5rem;
            }
        }
    </style>

    @if(Auth::check())
    <script>
        window.location.href = "/home"; // Ganti dengan halaman tujuan setelah login
    </script>
    @endif

</head>

<body>
    <div class="main-container">
        <!-- Floating shapes -->
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
        <div class="shape shape-4"></div>

        <div class="content-wrapper">
            <!-- Logo -->
            <div class="logo-container">
                <img src="{{ asset('storage/logoTimurJayaIndosteel.png') }}" alt="Logo PT Timur Jaya Indosteel" class="logo">
            </div>

            <!-- Title Section -->
            <div class="title-section">
                <h1 class="main-title">Welcome to PT Timur Jaya Indosteel</h1>
                <div class="title-underline"></div>
                <p class="company-tagline">
                    <span id="animated-text"></span>
                </p>

            </div>

            <!-- Cards Section -->
            <div class="cards-section">
                <!-- Job Vacancy Card -->
                <div class="card-box">
                    <i class="fas fa-user-tie card-icon"></i>
                    <h2 class="card-title">Job Vacancy</h2>
                    <div class="title-line"></div>
                    <p class="card-text">
                        Looking for a career opportunity? Join our team and grow with us. Check out our available positions and apply today.
                    </p>
                    <a href="{{ route('job_vacancy.index') }}" class="btn-action btn-apply">
                        <i class="fas fa-briefcase"></i>
                        APPLY NOW
                    </a>
                </div>

                <!-- Admin Portal Card -->
                <div class="card-box">
                    <i class="fas fa-user-shield card-icon"></i>
                    <h2 class="card-title">Admin Portal</h2>
                    <div class="title-line"></div>
                    <p class="card-text">
                        Staff and admin access portal. Log in to manage resources, projects, and company information.
                    </p>
                    <a href="{{ route('login') }}" class="btn-action btn-login">
                        <i class="fas fa-sign-in-alt"></i>
                        LOGIN
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
<script>
    var options = {
        strings: [
            "Building the future with quality steel and exceptional service",
            "Forging excellence, shaping the future",
            "Innovating steel, empowering progress",
            "Strength in every structure",
            "Committed to quality, built to last"
        ],
        typeSpeed: 50,
        backSpeed: 25,
        backDelay: 2000,
        startDelay: 500,
        loop: true
    };

    var typed = new Typed("#animated-text", options);
</script>


</html>