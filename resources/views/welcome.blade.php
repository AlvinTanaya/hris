<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Timur Jaya Indosteel</title>
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

        .hero-section {
            text-align: center;
            padding: 8rem 0 5rem;
            position: relative;
            z-index: 1;
        }

        .company-logo {
            max-width: 220px;
            filter: drop-shadow(0 8px 24px rgba(0, 0, 0, 0.5));
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .hero-title {
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .hero-subtitle {
            font-weight: 300;
            max-width: 700px;
            margin: 0 auto 3rem;
            opacity: 0.9;
            font-size: 1.2rem;
        }

        .tagline-text {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            font-style: italic;
        }

        /* About Us Section */
        .about-section {
            padding: 5rem 0;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(5px);
            border-radius: 20px;
        }

        .section-title {
            font-weight: 700;
            margin-bottom: 2rem;
            color: var(--accent);
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--accent);
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
            .hero-section {
                padding: 5rem 0 3rem;
            }

            .hero-title {
                font-size: 1.8rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .about-section {
                padding: 3rem 0;
            }
        }

        .carousel-item img {
            height: 400px;
            object-fit: cover;
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
                        <a class="nav-link active" href="{{ route('welcome') }}">
                            <i class="fas fa-home me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('job_vacancy.index') }}">
                            <i class="fas fa-briefcase me-1"></i> Careers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i> Admin Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" data-aos="fade-up">
        <div class="container">
            <img src="{{ asset('storage/logoTimurJayaIndosteel.png') }}" alt="Timur Jaya Indosteel" class="company-logo">
            <h1 class="hero-title">PT Timur Jaya Indosteel</h1>
            <p class="tagline-text">"Building the future with quality steel and exceptional service"</p>
            <p class="hero-subtitle">A leading steel distribution company committed to quality, reliability, and customer satisfaction.</p>
        </div>
    </section>

    <!-- About Us Section -->
    <section class="about-section" data-aos="fade-up">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-4 mb-md-0">
                    <h2 class="section-title">About Us</h2>
                    <p>PT Timur Jaya Indosteel is a steel distributor and wholesaler in Surabaya, serving as a trusted supplier for steel shops across Indonesia, particularly in the Eastern region.</p>

                    <p>Founded in the 1970s by Mr. Njoo Hari Sunjoto, we now operate 3 warehouses in Margomulyo, Surabaya. We provide various high-quality steel products (stainless steel, carbon steel, etc.) for oil & gas, petrochemical, pulp & paper, and commercial industries.</p>

                    <p>With our experienced team and excellent management system, we continue to expand our network while maintaining our commitment to product quality, premium service, competitive pricing, and integrity as our business foundation.</p>

                    <p>We are determined to always be a trusted partner for customers, employees, and the Indonesian community in fulfilling all steel needs.</p>
                </div>
                <div class="col-md-6">
                    <div id="companyCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner rounded-3 shadow">
                            <div class="carousel-item active">
                                <img src="{{ asset('storage/company_profile/company_profile_1.jpg') }}" class="d-block w-100" alt="Company Profile 1">
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('storage/company_profile/company_profile_2.jpg') }}" class="d-block w-100" alt="Company Profile 2">
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('storage/company_profile/company_profile_3.jpg') }}" class="d-block w-100" alt="Company Profile 3">
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('storage/company_profile/company_profile_4.jpg') }}" class="d-block w-100" alt="Company Profile 4">
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('storage/company_profile/company_profile_5.jpg') }}" class="d-block w-100" alt="Company Profile 5">
                            </div>

                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#companyCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#companyCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#companyCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#companyCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                            <button type="button" data-bs-target="#companyCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                            <button type="button" data-bs-target="#companyCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
                            <button type="button" data-bs-target="#companyCarousel" data-bs-slide-to="4" aria-label="Slide 5"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    </script>
</body>

</html>