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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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

        .header {
            text-align: center;
            padding: 2rem 0;
            position: relative;
            z-index: 1;
        }

        .logo {
            max-width: 220px;
            animation: pulse 2s infinite;
            filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.3));
        }

        .filters-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem auto;
            max-width: 1200px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .form-control,
        .form-select {
            background: #2a5298;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            backdrop-filter: blur(5px);
        }


        .form-control:focus,
        .form-select:focus {
            background: #2a5298;
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.1);
        }

        .form-control::placeholder,
        .form-select {
            color: rgba(255, 255, 255, 0.7);
        }

        .job-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 1.5rem;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }

        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        .job-title {
            color: #ffc107;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .department-badge {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
        }

        .job-info {
            margin: 1.5rem 0;
            padding-left: 1rem;
            border-left: 3px solid #ffc107;
        }

        .btn-apply {
            background: #ffc107;
            color: #1e3c72;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-apply:hover {
            background: #ffca2c;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
        }

        .shape {
            position: fixed;
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
        }

        .shape-2 {
            width: 150px;
            height: 150px;
            top: 20%;
            right: 15%;
        }

        .shape-3 {
            width: 300px;
            height: 300px;
            bottom: 15%;
            right: 10%;
        }

        /* Add this to your existing style section */
        .pagination {
            --bs-pagination-color: #fff;
            --bs-pagination-bg: rgba(255, 255, 255, 0.1);
            --bs-pagination-border-color: rgba(255, 255, 255, 0.18);
            --bs-pagination-hover-color: #1e3c72;
            --bs-pagination-hover-bg: #ffc107;
            --bs-pagination-hover-border-color: #ffc107;
            --bs-pagination-focus-color: #1e3c72;
            --bs-pagination-focus-bg: #ffc107;
            --bs-pagination-active-color: #1e3c72;
            --bs-pagination-active-bg: #ffc107;
            --bs-pagination-active-border-color: #ffc107;
            --bs-pagination-disabled-color: rgba(255, 255, 255, 0.5);
            --bs-pagination-disabled-bg: rgba(255, 255, 255, 0.05);
            --bs-pagination-disabled-border-color: rgba(255, 255, 255, 0.1);
            justify-content: flex-end;
            /* Geser pagination ke kanan */
        }

        .small.text-muted {
            display: none !important;
            /* Sembunyikan teks tambahan */
        }



        .page-link {
            backdrop-filter: blur(5px);
            border-radius: 8px;
            margin: 0 3px;
        }

        .page-item.active .page-link {
            font-weight: bold;
        }

        .pagination-info {
            font-size: 0.9rem;
            text-align: center;
        }

        @keyframes float {
            0% {
                transform: translate(0, 0) rotate(0deg);
            }

            50% {
                transform: translate(50px, 50px) rotate(180deg);
            }

            100% {
                transform: translate(0, 0) rotate(360deg);
            }
        }
    </style>
</head>

<body>


    <!-- Floating shapes -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>

    <body>



        <!-- Back Button (Ditempatkan dalam Container) -->
        <div class="container mt-3">
            <a href="{{ route('welcome') }}" class="btn btn-danger mt-4 px-4">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
        </div>


        <!-- Header -->
        <div class="header">

            <img src="{{ asset('storage/logoTimurJayaIndosteel.png') }}" alt="Timur Jaya Indosteel" class="logo mb-4" style="  border-radius: 8px;">
            <h1 class="display-4 fw-bold mt-4">Career Opportunities</h1>
        </div>

        <!-- Filters -->
        <div class="container">


            <div class="filters-section">
                <form action="{{ route('job_vacancy.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Department</label>
                        <select class="form-select" name="department">
                            <option value="">All Departments</option>
                            @foreach($demand->pluck('department')->unique() as $dept)
                            <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                                {{ $dept }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Position</label>
                        <select class="form-select" name="position">
                            <option value="">All Positions</option>
                            @foreach($demand->pluck('position')->unique() as $position)
                            <option value="{{ $position }}" {{ request('position') == $position ? 'selected' : '' }}>
                                {{ $position }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Filter by Date</label>
                        <input type="date" class="form-control" name="filter_date" value="{{ request('filter_date') }}">
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-apply">
                            <i class="fas fa-filter me-2"></i>Apply Filters
                        </button>
                    </div>
                </form>
            </div>

            <!-- Job Listings -->
            <div class="row">
                @foreach($demand as $vacancy)
                <!-- Your existing job card code -->
                <div class="col-lg-12">
                    <div class="job-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="job-title d-flex align-items-center">
                                    {{ $vacancy->position }}
                                    <span class="department-badge ms-2">
                                        <i class="fas fa-building"></i> {{ $vacancy->department }}
                                    </span>
                                </h3>
                            </div>
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-users me-1"></i>{{ $vacancy->qty_needed }} Quota
                            </span>
                        </div>

                        <div class="job-info">
                            <!-- Basic Info -->
                            <div class="mb-3">
                                <h6 class="fw-bold text-warning">
                                    <i class="fas fa-info-circle me-2"></i>Basic Requirements
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p>
                                            <i class="fas fa-venus-mars me-2"></i>Gender:
                                            {{ $vacancy->gender == 'Both' ? 'Male/Female' : $vacancy->gender }}
                                        </p>
                                        <p>
                                            <i class="fas fa-graduation-cap me-2"></i>Education:
                                            {{ $vacancy->education }}
                                        </p>
                                        <p>
                                            <i class="fas fa-toggle-on me-2"></i>Status:
                                            {{ $vacancy->status_job }}
                                        </p>

                                    </div>
                                    <div class="col-md-6">
                                        <p>
                                            <i class="fas fa-briefcase me-2"></i>Experience:
                                            {{ str_replace(['1_12_', '1_3_', '3_5_', '5_plus_'], ['1-12 ', '1-3 ', '3-5 ', '5+ '], $vacancy->time_work_experience) }}
                                        </p>
                                        <p>
                                            <i class="fas fa-book me-2"></i>Major:
                                            {{ $vacancy->major }}
                                        </p>

                                        @if ($vacancy->length_of_working !== null)
                                        <p>
                                            <i class="fas fa-clock me-2"></i>Working Period:
                                            {{ $vacancy->length_of_working }} months
                                        </p>
                                        @endif

                                    </div>
                                </div>
                            </div>

                            <!-- Di dalam job-card -->
                            <div class="mb-3">
                                <h6 class="fw-bold text-warning">
                                    <i class="fas fa-bullseye me-2"></i>Job Purpose
                                </h6>
                                <ul class="list-unstyled">
                                    @foreach(explode("\n", $vacancy->job_goal) as $purpose)
                                    @if(trim($purpose))
                                    <li class="mb-1">
                                        <i class="fas fa-chevron-right text-warning me-2"></i>
                                        {{ trim(str_replace('-', '', $purpose)) }}
                                    </li>
                                    @endif
                                    @endforeach
                                </ul>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-bold text-warning">
                                    <i class="fas fa-star me-2"></i>Required Skills
                                </h6>
                                <ul class="list-unstyled">
                                    @foreach(explode("\n", $vacancy->skills) as $skill)
                                    @if(trim($skill))
                                    <li class="mb-1">
                                        <i class="fas fa-chevron-right text-warning me-2"></i>
                                        {{ trim(str_replace('-', '', $skill)) }}
                                    </li>
                                    @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-light small">
                                <div><i class="far fa-calendar-plus me-1"></i>
                                    Open: {{ \Carbon\Carbon::parse($vacancy->opening_date)->format('d M Y') }}</div>
                                <div><i class="far fa-calendar-minus me-1"></i>
                                    Close: {{ \Carbon\Carbon::parse($vacancy->closing_date)->format('d M Y') }}</div>
                            </div>
                            <a href="{{ route('job_vacancy.create', ['id' => $vacancy->id]) }}" class="btn btn-apply">
                                <i class="fas fa-paper-plane me-2"></i>Apply Now
                            </a>

                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Pagination links -->
                @if ($demand->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4 mb-5">
                    <span class="text-white">
                        Showing {{ $demand->firstItem() }} to {{ $demand->lastItem() }} of {{ $demand->total() }} results
                    </span>
                    @if ($demand->hasPages())
                    {{ $demand->links('pagination::bootstrap-5') }}
                    @endif
                </div>

                @endif
            </div>

        </div>





    </body>

</html>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Date filter validation
        $('input[name="end_date"]').change(function() {
            var startDate = $('input[name="start_date"]').val();
            var endDate = $(this).val();

            if (startDate && endDate && startDate > endDate) {
                alert('End date must be after start date');
                $(this).val('');
            }
        });
    });
</script>