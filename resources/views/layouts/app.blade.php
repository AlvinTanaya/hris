<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PT Timur Jaya Indosteel</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/IconTimurJayaIndosteel.png') }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Plugin CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    @vite(['resources/sass/app.scss'])
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

        #sidebar {
            width: var(--sidebar-width);
            position: fixed;
            left: 0;
            height: 100vh;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding-top: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        #sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }




        #sidebar .logo-container {
            padding: 1.5rem 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1rem;
        }

        #sidebar .logo {
            height: 60px;
            border-radius: 8px;
            transition: all 0.3s;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }

        #sidebar.collapsed .logo {
            height: 40px;
        }

        #sidebar .nav-link {
            color: var(--text-color);
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            border-radius: 0.5rem;
            margin: 0.3rem 0.7rem;
            position: relative;
            overflow: hidden;
        }

        #sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: var(--accent-color);
            transform: translateX(5px);
        }

        #sidebar .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 12px;
            font-size: 1.1rem;
        }

        #sidebar.collapsed .nav-link span,
        #sidebar.collapsed .dropdown-icon {
            display: none;
        }

        #sidebar .dropdown-container {
            margin-left: 1rem;
            display: none;
        }

        #sidebar .dropdown-container.show {
            display: block;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #content {
            margin-left: var(--sidebar-width);
            padding: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }

        #sidebar .dropdown-icon {
            margin-left: auto;
            transition: transform 0.3s;
        }

        #sidebar .dropdown-toggle[aria-expanded="true"] .dropdown-icon {
            transform: rotate(90deg);
        }

        #content {
            margin-left: 250px;
            padding: 1rem;
            transition: all 0.3s;
        }

        #content.expanded {
            margin-left: 100px;
        }



        .profile-container {
            display: flex;
            align-items: center;
            color: var(--text-color);
            z-index: 1;
        }

        .profile-container img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 12px;
            border: 2px solid var(--accent-color);
            object-fit: cover;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }



        .top-bar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            /* Changed to space-between */
            align-items: center;
            border-radius: 12px;
            margin-bottom: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .top-bar-right {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .top-bar-icon {
            color: var(--text-color);
            font-size: 1.2rem;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
        }

        .top-bar-icon:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .nav-menu {
            flex-grow: 1;
            overflow-y: auto;
            overflow-x: hidden;
            /* Add this to prevent horizontal scrolling */
        }

        .profile-section {
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: auto;
            position: sticky;
            bottom: 0;
            background: inherit;
            backdrop-filter: blur(10px);
        }

        .profile-link {
            display: flex;
            align-items: center;
            color: var(--text-color);
            padding: 0.8rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .profile-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--accent-color);
        }

        .profile-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 12px;
            border: 2px solid var(--accent-color);
            object-fit: cover;
        }

        #sidebar.collapsed .profile-link span {
            display: none;
        }

        #sidebar.collapsed .profile-image {
            margin-right: 0;
        }





        /* Card Styles */
        .custom-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .custom-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            :root {
                --sidebar-width: 0px;
            }

            #sidebar {
                transform: translateX(-100%);
            }

            #sidebar.show {
                transform: translateX(0);
                width: 250px;
            }

            #content {
                margin-left: 0;
            }

            .top-bar {
                margin: 0 -1rem 1rem -1rem;
                border-radius: 0;
            }
        }

        /* Loading Spinner */
        .loader {
            width: 48px;
            height: 48px;
            border: 5px solid var(--accent-color);
            border-bottom-color: transparent;
            border-radius: 50%;
            display: inline-block;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div id="sidebar" style="padding-top:0%">
        <div class="logo-container">
            <a href="{{ url('/home') }}">
                <img src="{{ asset('storage/logoTimurJayaIndosteel.png') }}" alt="Logo" class="logo">
            </a>
        </div>

        <div class="nav-menu">


            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ url('/dashboard') }}" class="nav-link">
                        <i class="fa-solid fa-gauge"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @if (
                Auth::user()->department == 'Human Resources' ||
                Auth::user()->department == 'General Manager' ||
                Auth::user()->department == 'Director' ||
                Auth::user()->position != 'Staff'
                )
                <li class="nav-item">
                    <a href="{{ url('/user/index') }}" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Employee</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-graduation-cap"></i>
                        <span>E-learning &nbsp;</span>

                    </a>
                    <div class="dropdown-container">
                        <a href="{{ url('/elearning/index') }}" class="nav-link">
                            <i class="fas fa-book"></i>
                            <span>E-learning</span>
                        </a>
                        <a href="{{ url('/elearning/index2/' . Auth::user()->id) }}" class="nav-link">
                            <i class="fas fa-tasks"></i>
                            <span>E-learning Duty</span>
                        </a>
                    </div>
                </li>

                <!-- Rekruitmen Menu -->
                <li class="nav-item">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-user-plus"></i>
                        <span>Recruitment &nbsp;</span>
                    </a>
                    <div class="dropdown-container">
                        <a href="{{ url('/recruitment/labor_demand/index/') }}" class="nav-link">
                            <i class="fas fa-file-signature"></i>
                            <span>Labor Demand</span>
                        </a>
                        <a href="{{ url('/recruitment/ahp_recruitment/index/') }}" class="nav-link">
                            <i class="fas fa-user-check"></i>
                            <span>AHP Recomendation</span>
                        </a>
                        <a href="{{ url('/recruitment/interview/index') }}" class="nav-link">
                            <i class="fas fa-comments"></i>
                            <span>Interview</span>
                        </a>
                    </div>
                </li>

                <!-- Time Management Menu -->
                <li class="nav-item">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-clock"></i>
                        <span>Time Management &nbsp;</span>
                    </a>
                    <div class="dropdown-container">
                        <a href="{{ url('/time/work-shift') }}" class="nav-link">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Work Shift</span>
                        </a>
                        <a href="{{ url('/time/attendance') }}" class="nav-link">
                            <i class="fas fa-user-clock"></i>
                            <span>Absensi</span>
                        </a>
                        <a href="{{ url('/time/leave') }}" class="nav-link">
                            <i class="fas fa-umbrella-beach"></i>
                            <span>Cuti</span>
                        </a>
                        <a href="{{ url('/time/overtime') }}" class="nav-link">
                            <i class="fas fa-business-time"></i>
                            <span>Lembur</span>
                        </a>
                        <a href="{{ url('/time/resignation') }}" class="nav-link">
                            <i class="fas fa-door-open"></i>
                            <span>Resign</span>
                        </a>
                        <a href="{{ url('/time/warning-verbal') }}" class="nav-link">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>Teguran</span>
                        </a>
                        <a href="{{ url('/time/warning-letter') }}" class="nav-link">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Surat Peringatan</span>
                        </a>
                    </div>
                </li>

                @else
                <li class="nav-item">
                    <a href="{{ url('/elearning/index2/' . Auth::user()->id) }}" class="nav-link">
                        <i class="fas fa-tasks"></i>
                        <span>E-learning Duty</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        <!-- Profile section at bottom of sidebar -->
        <div class="profile-section">

            <a href="{{ route('user.edit', Auth::user()->id) }}" class="profile-link">
                <img src="{{ Auth::user()->photo_profile_path ? asset('storage/'. Auth::user()->photo_profile_path) : asset('storage/default_profile.png') }}"
                    alt="Profile Picture" class="profile-image">
                <span>{{ Auth::user()->name }}</span>
            </a>
        </div>
    </div>

    <div id="content">
        <div class="top-bar">
            <button id="sidebarToggle" class="btn btn-link text-white">
                <i class="fas fa-bars"></i>
            </button>
            <div class="top-bar-right">
                <a href="#" class="top-bar-icon">
                    <i class="fas fa-bell"></i>
                </a>
                <a href="{{ route('logout') }}"
                    class="top-bar-icon"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Core Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Plugin Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chart Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

    @vite(['resources/js/app.js'])

    <script>
        $(document).ready(function() {
            // Toggle sidebar
            $('#sidebarToggle').click(function() {
                $('#sidebar').toggleClass('collapsed');
                $('#content').toggleClass('expanded');
            });

            // Handle dropdown toggles
            $('.dropdown-toggle').click(function(e) {
                e.preventDefault();
                let dropdownContainer = $(this).siblings('.dropdown-container');
                dropdownContainer.slideToggle();
                $(this).toggleClass('active');
            });

            // Handle mobile responsiveness
            if ($(window).width() < 768) {
                $('#sidebar').addClass('collapsed');
                $('#content').addClass('expanded');
            }

            // Adjust on window resize
            $(window).resize(function() {
                if ($(window).width() < 768) {
                    $('#sidebar').addClass('collapsed');
                    $('#content').addClass('expanded');
                }
            });


        });
    </script>

    @stack('scripts')
</body>

</html>