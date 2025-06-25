<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Vacancies - PT Timur Jaya Indosteel</title>
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
            color: #ffffff;
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

        .back-btn {
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .back-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
            z-index: -1;
        }

        .back-btn:hover::before {
            left: 100%;
        }

        .header {
            text-align: center;
            padding: 5rem 0 3rem;
            position: relative;
            z-index: 1;
            background: url('{{ asset(' storage/header-bg.jpg') }}') center/cover no-repeat;
            background-attachment: fixed;
            background-blend-mode: overlay;
            background-color: rgba(10, 36, 99, 0.8);
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(0deg, var(--primary), transparent);
            z-index: -1;
        }

        .logo {
            max-width: 220px;
            filter: drop-shadow(0 8px 24px rgba(0, 0, 0, 0.5));
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .page-title {
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 1rem;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .page-subtitle {
            font-weight: 300;
            max-width: 600px;
            margin: 0 auto;
            opacity: 0.9;
        }

        .filters-section {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            margin: -2rem auto 3rem;
            max-width: 1200px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 10;
        }

        .form-label {
            color: #ffffff;
            /* Ensure form labels are bright white */
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-control,
        .form-select {

            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(61, 165, 217, 0.3);
            /* More opaque background */
            color: #ffffff;
            /* Brighter text */
            font-weight: 600;
            /* Bolder text */
            backdrop-filter: blur(5px);
            border-radius: 10px;
            padding: 0.7rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(13, 54, 145, 0.6);
            border-color: var(--accent);
            color: white;
            box-shadow: 0 0 0 0.25rem rgba(61, 165, 217, 0.25);
        }

        .form-control::placeholder,
        .form-select {
            color: rgba(255, 255, 255, 0.7);
        }

        .btn-apply {
            background: linear-gradient(45deg, var(--accent), #5fb8e6);
            color: var(--dark);
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(61, 165, 217, 0.4);
        }

        .btn-apply:hover {
            background: linear-gradient(45deg, #5fb8e6, var(--accent));
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(61, 165, 217, 0.5);
        }

        .job-listings {
            position: relative;
            z-index: 1;
        }

        .job-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 2rem;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .job-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(to bottom, var(--accent), transparent);
        }

        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border-color: rgba(61, 165, 217, 0.3);
        }

        .job-title {
            color: var(--warning);
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
        }

        .department-badge {
            background: rgba(61, 165, 217, 0.15);
            color: var(--warning);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .quota-badge {

            background: rgba(255, 209, 102, 0.3);
            /* More opaque background */
            color: #ffffff;
            /* Brighter text instead of yellow */
            font-weight: 600;
            /* Bolder text */

            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .job-info {
            margin: 1.5rem 0;
            padding-left: 1.5rem;
            border-left: 2px solid rgba(61, 165, 217, 0.3);
        }

        .info-section {
            margin-bottom: 1.5rem;
        }

        .info-section h6 {
            color: #ffffff;
            /* Bright white instead of accent color */
            font-weight: 700;
            /* Extra bold */
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
            /* Text shadow for better readability */
            font-size: 1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            /* More visible background */
        }

        .info-item i {
            color: var(--accent);
            font-size: 1rem;
            min-width: 24px;
            text-align: center;
        }

        .info-item span {
            color: #ffffff;
            /* Brighter text */
            font-weight: 500;
            /* Slightly bolder */
        }


        .list-item {
            margin-bottom: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            transition: all 0.2s ease;
        }

        .list-item span {
            color: #ffffff;
            /* Ensuring text is bright white */
            font-weight: 500;
            /* Slightly bolder */
        }

        .list-item:hover {
            background: rgba(255, 255, 255, 0.06);
            transform: translateX(5px);
        }

        .list-item i {
            color: var(--accent);
            margin-top: 0.25rem;
        }

        .job-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .date-info {
            display: flex;
            gap: 1.5rem;
            color: #ffffff;
        }

        .date-item {

            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: #ffffff;
        }

        .btn-apply-now {
            background: linear-gradient(45deg, var(--accent), #5fb8e6);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(61, 165, 217, 0.4);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-apply-now:hover {
            background: linear-gradient(45deg, #5fb8e6, var(--accent));
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(61, 165, 217, 0.5);
            color: white;
        }

        .pagination {
            --bs-pagination-color: #fff;
            --bs-pagination-bg: rgba(255, 255, 255, 0.05);
            --bs-pagination-border-color: rgba(255, 255, 255, 0.08);
            --bs-pagination-hover-color: var(--dark);
            --bs-pagination-hover-bg: var(--accent);
            --bs-pagination-hover-border-color: var(--accent);
            --bs-pagination-focus-color: var(--dark);
            --bs-pagination-focus-bg: var(--accent);
            --bs-pagination-active-color: var(--dark);
            --bs-pagination-active-bg: var(--accent);
            --bs-pagination-active-border-color: var(--accent);
            --bs-pagination-disabled-color: rgba(255, 255, 255, 0.5);
            --bs-pagination-disabled-bg: rgba(255, 255, 255, 0.03);
            --bs-pagination-disabled-border-color: rgba(255, 255, 255, 0.05);
            justify-content: flex-end;
        }

        .page-link {
            backdrop-filter: blur(5px);
            border-radius: 8px;
            margin: 0 3px;
            padding: 8px 16px;
        }

        .page-item.active .page-link {
            font-weight: bold;
        }

        .pagination-info {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .footer {
            background: rgba(10, 17, 40, 0.8);
            backdrop-filter: blur(10px);
            padding: 2rem 0;
            margin-top: 4rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .footer-logo {
            height: 40px;
            opacity: 0.8;
            border-radius: 10px;
        }

        .footer-text {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.9);
            /* Brighter footer text */
        }

        .social-icons {
            display: flex;
            gap: 1rem;
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

        .small.text-muted {
            display: none !important;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .header {
                padding: 3rem 0 2rem;
            }

            .filters-section {
                margin-top: -1rem;
                padding: 1.5rem;
            }

            .job-card {
                padding: 1.5rem;
            }

            .job-footer {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .date-info {
                flex-direction: column;
                gap: 0.5rem;
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
                        <a class="nav-link active" href="{{ route('job_vacancy.index') }}">
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

    <!-- Header -->
    <header class="header">
        <div class="container">
            <img src="{{ asset('storage/logoTimurJayaIndosteel.png') }}" alt="Timur Jaya Indosteel" class="logo" data-aos="zoom-in">
            <h1 class="page-title" data-aos="fade-up" data-aos-delay="100">Career Opportunities</h1>
            <p class="page-subtitle" data-aos="fade-up" data-aos-delay="200">Join our team and be part of our growing success. Explore our current openings and find your perfect role at PT Timur Jaya Indosteel.</p>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <!-- Filters -->
        <div class="filters-section" data-aos="fade-up" data-aos-delay="300">
            <form action="{{ route('job_vacancy.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-building me-2"></i>Department</label>
                    <select class="form-select" name="department_id">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->department }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-user-tie me-2"></i>Position</label>
                    <select class="form-select" name="position_id">
                        <option value="">All Positions</option>
                        @foreach($positions as $position)
                        <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                            {{ $position->position }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-calendar-alt me-2"></i>Filter by Date</label>
                    <input type="date" class="form-control" name="filter_date" value="{{ request('filter_date') }}">
                </div>
                <div class="col-12 text-center mt-4">
                    <button type="submit" class="btn btn-apply">
                        <i class="fas fa-filter me-2"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Job Listings -->
        <div class="job-listings">
            @foreach($demand as $index => $vacancy)
            <div class="job-card" data-aos="fade-up" data-aos-delay="{{ 100 * ($index % 5) }}">
                <div class="d-flex justify-content-between align-items-start flex-wrap">
                    <div>
                        <h3 class="job-title">
                            {{ $vacancy->positionRelation->position ?? 'Unknown Position' }}
                        </h3>
                        <span class="department-badge">
                            <i class="fas fa-building"></i> {{ $vacancy->departmentRelation->department ?? 'Unknown Department' }}
                        </span>
                    </div>
                    <span class="quota-badge">
                        <i class="fas fa-users me-1"></i>{{ $vacancy->qty_needed }} Positions Available
                    </span>
                </div>

                <div class="job-info">
                    <!-- Basic Info -->
                    <div class="info-section">
                        <h6 class="fw-bold">
                            <i class="fas fa-info-circle"></i>Basic Requirements
                        </h6>
                        <div class="info-grid">
                            <div class="info-item">
                                <i class="fas fa-venus-mars"></i>
                                <span>{{ $vacancy->gender == 'Both' ? 'Male/Female' : $vacancy->gender }}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-graduation-cap"></i>
                                <span>{{ $vacancy->education }}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-toggle-on"></i>
                                <span>{{ $vacancy->status_job }}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-briefcase"></i>
                                <span>{{ str_replace(['1_12_', '1_3_', '3_5_', '5_plus_'], ['1-12 ', '1-3 ', '3-5 ', '5+ '], $vacancy->time_work_experience) }}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-book"></i>
                                <span>{{ $vacancy->major }}</span>
                            </div>
                            @if ($vacancy->length_of_working !== null)
                            <div class="info-item">
                                <i class="fas fa-clock"></i>
                                <span>{{ $vacancy->length_of_working }} months</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Job Purpose -->
                    <div class="info-section">
                        <h6 class="fw-bold">
                            <i class="fas fa-bullseye"></i>Job Purpose
                        </h6>
                        <div>
                            @foreach(explode("\n", $vacancy->job_goal) as $purpose)
                            @if(trim($purpose))
                            <div class="list-item">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ trim(str_replace('-', '', $purpose)) }}</span>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Required Skills -->
                    <div class="info-section">
                        <h6 class="fw-bold">
                            <i class="fas fa-star"></i>Required Skills
                        </h6>
                        <div>
                            @foreach(explode("\n", $vacancy->skills) as $skill)
                            @if(trim($skill))
                            <div class="list-item">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ trim(str_replace('-', '', $skill)) }}</span>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="job-footer">
                    <div class="date-info">
                        <div class="date-item">
                            <i class="far fa-calendar-plus"></i>
                            <span>Opens: {{ \Carbon\Carbon::parse($vacancy->opening_date)->format('d M Y') }}</span>
                        </div>
                        <div class="date-item">
                            <i class="far fa-calendar-minus"></i>
                            <span>Closes: {{ \Carbon\Carbon::parse($vacancy->closing_date)->format('d M Y') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('job_vacancy.create', ['id' => $vacancy->id]) }}" class="btn btn-apply-now">
                        <i class="fas fa-paper-plane"></i>Apply Now
                    </a>
                </div>
            </div>
            @endforeach

            <!-- Pagination links -->
            @if ($demand->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4 mb-5">
                <span class="pagination-info">
                    Showing {{ $demand->firstItem() }} to {{ $demand->lastItem() }} of {{ $demand->total() }} results
                </span>
                {{ $demand->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <img src="{{ asset('storage/logoTimurJayaIndosteel.png') }}" alt="Timur Jaya Indosteel" class="footer-logo mb-3">
                    <p class="footer-text">© {{ date('Y') }} PT Timur Jaya Indosteel. All rights reserved.</p>
                </div>
                <div class="col-md-4 text-center">
                    <p class="footer-text mb-2">Follow Us</p>
                    <div class="social-icons d-flex justify-content-center gap-3">
                        <a href="https://www.instagram.com/timurjayaindosteel_official/" class="social-icon" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-instagram fa-2x"></i>
                        </a>
                        <a href="https://api.whatsapp.com/send?phone=6281130585555&text=Halo%20PT.%20Timur%20Jaya%20Indosteel.%0ASaya%20pengunjung%20Website%2C%20Saya%20membutuhkan%20informasi%20terkait%20distributor%20besi." class="social-icon" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-whatsapp fa-2x"></i>
                        </a>
                    </div>

                </div>
                <div class="col-md-4 text-md-end mt-4 mt-md-0">
                    <p class="footer-text mb-0">PT Timur Jaya Indosteel</p>
                    <p class="footer-text mb-0">Join our team and grow together</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize AOS animation
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true
            });

            // Date filter validation
            $('input[name="filter_date"]').change(function() {
                var startDate = $('input[name="start_date"]').val();
                var endDate = $(this).val();

                if (startDate && endDate && startDate > endDate) {
                    alert('End date must be after start date');
                    $(this).val('');
                }
            });
        });
    </script>
</body>

</html>