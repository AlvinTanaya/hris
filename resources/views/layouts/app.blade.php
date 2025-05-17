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


    @vite(['resources/sass/app.scss'])
    <style>
        :root {
            /* Base colors - combining both themes */
            --primary-color: #0a2463;
            --secondary-color: #247ba0;
            --accent-color: #ffc107;
            --primary-light: #1d52d1;
            --text-color: #ffffff;
            --dark: #0a1128;
            --success: #06d6a0;
            --warning: #ffd166;
            --danger: #ef476f;
            --light: #f5f9ff;

            /* Layout variables */
            --sidebar-width: 275px;
            --sidebar-collapsed-width: 100px;
            --transition-time: 0.3s;
            --blur-intensity: 10px;
            --border-radius: 12px;
        }

        /* Base Styles */
        body {
            font-family: 'Inter', 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color), var(--primary-light));
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            position: relative;
            overflow-x: hidden;
        }

        /* Background Elements */
        body::before,
        body::after,
        .floating-shape {
            content: "";
            position: fixed;
            z-index: -1;
            opacity: 0.2;
        }

        body::before {
            top: -100px;
            right: -50px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: linear-gradient(#ffc107, #ff6b6b);
        }

        body::after {
            bottom: -100px;
            left: -50px;
            width: 200px;
            height: 200px;
            background: linear-gradient(#4facfe, #00f2fe);
            clip-path: polygon(50% 0%, 100% 38%, 82% 100%, 18% 100%, 0% 38%);
        }

        .floating-shape {
            width: 150px;
            height: 150px;
            background: linear-gradient(#f093fb, #f5576c);
            bottom: 50px;
            right: 20%;
            clip-path: polygon(25% 0%, 100% 0%, 75% 100%, 0% 100%);
            transform: rotate(45deg);
            animation: float 8s infinite ease-in-out;
        }

        /* Shapes from second theme */
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

        /* Animations */
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

        @keyframes float {
            0% {
                transform: rotate(0deg) translate(0, 0);
            }

            25% {
                transform: rotate(5deg) translate(10px, 10px);
            }

            50% {
                transform: rotate(0deg) translate(0, 20px);
            }

            75% {
                transform: rotate(-5deg) translate(-10px, 10px);
            }

            100% {
                transform: rotate(0deg) translate(0, 0);
            }
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
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


        /* Sidebar Styles */
        #sidebar {
            width: var(--sidebar-width);
            position: fixed;
            left: 0;
            height: 100vh;
            background: rgba(10, 36, 99, 0.8);
            backdrop-filter: blur(var(--blur-intensity));
            border-right: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            z-index: 1000;
            transition: all var(--transition-time) cubic-bezier(0.4, 0, 0.2, 1);
            padding-top: 0;
            display: flex;
            flex-direction: column;
            transform: translateX(0);
        }

        #sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        #sidebar::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #ffc107, #ff6b6b, #4facfe, #00f2fe, #f093fb, #f5576c);
            z-index: 2;
        }

        /* Sidebar Logo */
        #sidebar .logo-container {
            padding: 1.5rem 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1rem;
        }

        #sidebar .logo {
            height: 70px;
            border-radius: 8px;
            transition: all var(--transition-time);
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
            animation: pulse 2s infinite;
        }

        #sidebar.collapsed .logo {
            height: 40px;
        }



        /* Navigation Menu */
        .nav-menu {
            flex-grow: 1;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
        }

        .nav-menu::-webkit-scrollbar {
            width: 8px;
        }

        .nav-menu::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }

        /* Navigation Links */
        #sidebar .nav-link {
            color: var(--text-color);
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            transition: all var(--transition-time);
            border-radius: var(--border-radius);
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

        /* Dropdown Styles */
        #sidebar .dropdown-container {
            margin-left: 1rem;
            width: calc(100% - 1.4rem);
            display: none;
            position: relative;
            z-index: 1001;
            animation: slideDown var(--transition-time) ease-out;
        }

        #sidebar .dropdown-container.show {
            display: block;
        }

        .dropdown-icon {
            margin-left: auto;
            transition: transform var(--transition-time);
            font-size: 0.85rem;
        }

        #sidebar .dropdown-toggle[aria-expanded="true"] .dropdown-icon,
        #sidebar .shift-dropdown.active .dropdown-icon,
        #sidebar .timeOff-dropdown.active .dropdown-icon,
        #sidebar .attendance-dropdown.active .dropdown-icon,
        #sidebar .warningLetter-dropdown.active .dropdown-icon {
            transform: rotate(90deg);
        }

        /* Nested Dropdowns */
        .shift-submenu .nav-link,
        .warningLetter-submenu .nav-link,
        .attendance-submenu .nav-link,
        .timeOff-submenu .nav-link {
            padding-left: 3rem !important;
            font-size: 0.9rem;
        }

        .shift-submenu .nav-link:not(:first-child),
        .warningLetter-submenu .nav-link:not(:first-child),
        .attendance-submenu .nav-link:not(:first-child),
        .timeOff-submenu .nav-link:not(:first-child) {
            padding-left: 3.5rem !important;
        }

        /* Profile Section */
        .profile-section {
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: auto;
            position: sticky;
            bottom: 0;
            background: inherit;
            backdrop-filter: blur(var(--blur-intensity));
        }

        .settings-icon {
            margin-left: 20px;
            color: white;
        }

        .settings-icon i {
            font-size: 18px;
        }

        .profile-link {
            display: flex;
            align-items: center;
            color: var(--text-color);
            padding: 0.8rem;
            border-radius: var(--border-radius);
            transition: all var(--transition-time) ease;
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





        /* Content Area */
        #content {
            margin-left: var(--sidebar-width);
            padding: 1rem;
            transition: margin-left var(--transition-time) cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 990;
        }

        #content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Top Bar */
        .top-bar,
        .navbar {
            background: rgba(10, 36, 99, 0.8);
            backdrop-filter: blur(var(--blur-intensity));
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            position: relative;
            z-index: 995;
            align-items: center;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand img {
            height: 40px;
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover img {
            transform: scale(1.05);
        }

        .top-bar-right {
            display: flex;
            gap: 1rem;
            align-items: center;
            position: relative;
        }

        .top-bar-icon {
            color: var(--text-color);
            font-size: 1.2rem;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all var(--transition-time) ease;
            background: rgba(255, 255, 255, 0.1);
        }

        .top-bar-icon:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        /* Notification Dropdown */
        .notification-dropdown {
            position: absolute;
            top: 150%;
            right: 0;
            z-index: 1010;
            background: white;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            border-radius: var(--border-radius);
            width: 320px;
            max-height: 400px;
            overflow-y: auto;
            display: none;
            backdrop-filter: blur(var(--blur-intensity));
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .notification-header {
            background: rgba(255, 255, 255, 0.2);
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .notification-item {
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
            cursor: pointer;
        }

        .notification-item:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .notification-item.bg-light {
            border-left: 4px solid var(--accent-color);
        }

        .notification-badge {
            font-size: 0.6rem;
            padding: 0.2rem 0.4rem;
        }

        .notification-body {
            max-height: 300px;
            overflow-y: auto;
        }

        .notification-item p {
            color: #333;
        }

        .notification-footer a {
            text-decoration: none;
            font-weight: bold;
            transition: all var(--transition-time) ease;
        }

        .notification-footer a:hover {
            text-decoration: underline;
            color: #ffcc00;
        }

        .bg-light {
            background: rgba(255, 255, 255, 0.3) !important;
        }



        /* Custom Cards */
        .custom-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(var(--blur-intensity));
            border-radius: var(--border-radius);
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all var(--transition-time) ease;
            position: relative;
        }

        .custom-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .custom-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #ffc107, #ff6b6b, #4facfe, #00f2fe, #f093fb, #f5576c);
            z-index: 2;
        }



        /* Buttons */
        .btn {
            border-radius: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            border: none;
        }

        .btn-primary:hover {
            color: var(--primary-color);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(61, 165, 217, 0.3);
        }

        .btn-outline-secondary {
            border: 2px solid white;
            background: transparent;
        }

        .btn-outline-secondary:hover {
            color: var(--primary-color);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 255, 255, 0.2);
        }


        /* Loader */
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

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            :root {
                --sidebar-width: 250px;
                --sidebar-collapsed-width: 0px;
            }

            #sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width);
                max-width: 350px;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            }

            #sidebar.show {
                transform: translateX(0);
            }

            #content {
                margin-left: 0 !important;
                width: 100%;
            }

            .shift-submenu .nav-link,
            .warningLetter-submenu .nav-link,
            .attendance-submenu .nav-link,
            .timeOff-submenu .nav-link {

                padding-left: 3.5rem !important;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }

            .sidebar-overlay.show {
                display: block;
            }

            .top-bar {
                padding: 0.75rem;
                margin-bottom: 1rem;
                border-radius: 10px;
                z-index: 990;
            }



            /* Ensure dropdown arrows remain visible on mobile */
            #sidebar.collapsed .dropdown-icon {
                display: block !important;
            }

            /* Improved mobile dropdown visibility */
            #sidebar .dropdown-container {
                background: rgba(0, 0, 0, 0.1);
                border-radius: 8px;
                margin: 0 0.7rem;
            }
        }

        /* Utility Classes */
        .hidden {
            display: none !important;
        }

        .active {
            display: block !important;
        }

        .transition-all {
            transition: all var(--transition-time) ease;
        }


        /* Tambahkan di bagian CSS */
        .no-scroll {
            overflow: hidden;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            /* Pastikan di bawah sidebar tapi di atas konten */
        }

        .sidebar-overlay.show {
            display: block;
        }

        @media (max-width: 768px) {
            #sidebar {
                transform: translateX(-100%);
                width: 280px;
                z-index: 1000;
                transition: transform 0.3s ease;
            }

            #sidebar.show {
                transform: translateX(0);
            }

            /* Pastikan konten utama tidak scroll saat sidebar terbuka */
            body.no-scroll {
                overflow: hidden;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar Navigation with Improved Dropdown Arrows -->
    <div id="sidebar">
        <div class="logo-container">
            <a href="{{ url('/home') }}">
                <img src="{{ asset('storage/logoTimurJayaIndosteel.png') }}" alt="Logo" class="logo">
            </a>
        </div>

        <div class="nav-menu">
            <ul class="nav flex-column">
                <!-- Announcement for management -->
                @if (Auth::user()->isSuperAdmin())
                <!-- announcement -->
                <li class="nav-item">
                    <a href="{{ route('announcement.index', ['user_id' => Auth::user()->id]) }}" class="nav-link">
                        <i class="fa-solid fa-bullhorn"></i>
                        <span>Announcement</span>
                    </a>
                </li>
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ url('/dashboard') }}" class="nav-link">
                        <i class="fa-solid fa-gauge"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <!-- User submenu -->
                <a href="#" class="nav-link dropdown-toggle user-dropdown">
                    <i class="fas fa-tasks"></i>
                    <span>User</span>
                    <!-- <i class="dropdown-icon fas fa-chevron-right ms-auto"></i> -->
                </a>
                <div class="user-submenu" style="display: none; padding-left: 15px;">
                    <a href="{{ route('user.departments.index') }}" class="nav-link">
                        <i class="fas fa-building"></i>
                        <span>Master Departments</span>
                    </a>
                    <a href="{{ route('user.positions.index') }}" class="nav-link">
                        <i class="fas fa-briefcase"></i>
                        <span>Master Positions</span>
                    </a>
                    <a href="{{ route('user.employees.index') }}" class="nav-link">
                        <i class="fas fa-id-badge"></i>
                        <span>Master Employees</span>
                    </a>
                </div>


                <!-- E-learning with consistent dropdown arrow -->
                <li class="nav-item">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-graduation-cap"></i>
                        <span>E-learning</span>
                        <!-- <i class="dropdown-icon fas fa-chevron-right ms-auto"></i> -->
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

                <!-- Recruitment with consistent dropdown arrow -->
                <li class="nav-item">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-user-plus"></i>
                        <span>Recruitment</span>
                        <!-- <i class="dropdown-icon fas fa-chevron-right ms-auto"></i> -->
                    </a>
                    <div class="dropdown-container">
                        <a href="{{ url('/recruitment/labor_demand/index/') }}" class="nav-link">
                            <i class="fas fa-file-signature"></i>
                            <span>Labor Demand</span>
                        </a>
                        <a href="{{ url('/recruitment/ahp_recruitment/index/') }}" class="nav-link">
                            <i class="fas fa-user-check"></i>
                            <span>AHP Recommendation</span>
                        </a>
                        <a href="{{ url('/recruitment/weight_calculation/index') }}" class="nav-link">
                            <i class="fa-solid fa-weight-scale"></i>
                            <span>Weight Based Recommendation</span>
                        </a>

                        <a href="{{ url('/recruitment/interview/index') }}" class="nav-link">
                            <i class="fas fa-comments"></i>
                            <span>Interview</span>
                        </a>
                    </div>
                </li>

                <!-- Time Management -->
                <li class="nav-item">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-clock"></i>
                        <span>Time Management</span>
                    </a>
                    <div class="dropdown-container">
                        <!-- Human Resources -->
                        <a href="#" class="nav-link dropdown-toggle hr-dropdown">
                            <i class="fas fa-user-tie"></i>
                            <span>Management</span>
                            <!-- <i class="dropdown-icon fas fa-chevron-right ms-auto"></i> -->
                        </a>
                        <div class="hr-submenu" style="display: none; padding-left: 15px;">
                            <!-- Existing shift dropdown with icon -->
                            <a href="#" class="nav-link dropdown-toggle shift-dropdown">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Shift</span>
                                <!-- <i class="dropdown-icon fas fa-chevron-right ms-auto"></i> -->
                            </a>
                            <div class="shift-submenu" style="display: none; padding-left: 15px;">
                                <a href="{{ url('/time_management/rule_shift/index') }}" class="nav-link">
                                    <i class="fas fa-cogs"></i> {{-- Ikon untuk pengaturan aturan --}}
                                    <span>Rule</span>
                                </a>
                                <a href="{{ url('/time_management/set_shift/index') }}" class="nav-link">
                                    <i class="fas fa-user-clock"></i> {{-- Ikon untuk penjadwalan shift --}}
                                    <span>Set Shift</span>
                                </a>
                                <a href="{{ route('change_shift.index')}}" class="nav-link">
                                    <i class="fas fa-exchange-alt"></i> {{-- Ikon untuk permintaan/perubahan shift --}}
                                    <span>Request Shift</span>
                                </a>
                            </div>


                            <a href="#" class="nav-link dropdown-toggle attendance-dropdown">
                                <i class="fa-solid fa-clipboard-user"></i>
                                <span>Attendance</span>
                                <!-- <i class="dropdown-icon fas fa-chevron-right ms-auto"></i> -->
                            </a>
                            <div class="attendance-submenu" style="display: none; padding-left: 15px;">
                                <a href="{{ url('/time_management/employee_absent/custom_holiday/index') }}" class="nav-link">
                                    <i class="fa-solid fa-snowman"></i>
                                    <span>Custom Holiday</span>
                                </a>
                                <a href="{{ url('/time_management/employee_absent/attendance/index') }}" class="nav-link">
                                    <i class="fas fa-user-clock"></i>
                                    <span>Employee Absent</span>
                                </a>

                            </div>

                            <a href="#" class="nav-link dropdown-toggle timeOff-dropdown">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Time Off</span>
                                <!-- <i class="dropdown-icon fas fa-chevron-right ms-auto"></i> -->
                            </a>
                            <div class="timeOff-submenu" style="display: none; padding-left: 15px;">
                                <a href="{{ url('/time_management/time_off/policy/index') }}" class="nav-link">
                                    <i class="fa-solid fa-building-shield"></i>
                                    <span>Policy</span>
                                </a>
                                <a href="{{ url('/time_management/time_off/assign/index') }}" class="nav-link">
                                    <i class="fas fa-users-cog"></i>
                                    <span>Set Assign</span>
                                </a>
                                <a href="{{ url('/time_management/time_off/request_time_off/index') }}" class="nav-link">
                                    <i class="fa-solid fa-user-tie"></i>
                                    <span>Request Time Off</span>
                                </a>
                            </div>

                            <a href="#" class="nav-link dropdown-toggle overtime-dropdown">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Ovetime</span>
                                <!-- <i class="dropdown-icon fas fa-chevron-right ms-auto"></i> -->
                            </a>
                            <div class="overtime-submenu" style="display: none; padding-left: 15px;">
                                <a href="{{ url('/time_management/overtime/management/index') }}" class="nav-link">
                                    <i class="fas fa-business-time"></i>
                                    <span>Management</span>
                                </a>
                                <a href="{{ url('/time_management/overtime/report/index') }}" class="nav-link">
                                    <i class="fa-solid fa-user-tie"></i>
                                    <span>Report</span>
                                </a>
                            </div>

                            <a href="{{ url('/time_management/request_resign/index') }}" class="nav-link">
                                <i class="fas fa-door-open"></i>
                                <span>Resign</span>
                            </a>
                            <a href="#" class="nav-link dropdown-toggle warningLetter-dropdown">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span>Warning Letter</span>
                                <!-- <i class="dropdown-icon fas fa-chevron-right ms-auto"></i> -->
                            </a>
                            <div class="warningLetter-submenu" style="display: none; padding-left: 15px;">
                                <a href="{{ url('/time_management/warning_letter/rule/index') }}" class="nav-link">
                                    <i class="fas fa-balance-scale"></i>
                                    <span>Rule</span>
                                </a>
                                <a href="{{ url('/time_management/warning_letter/assign/index') }}" class="nav-link">
                                    <i class="fa-solid fa-user-tie"></i>
                                    <span>Assign Letter</span>
                                </a>
                            </div>

                        </div>

                        <!-- Employee -->
                        <a href="#" class="nav-link dropdown-toggle employee-dropdown">
                            <i class="fa-solid fa-user"></i>
                            <span>Employee</span>
                        </a>
                        <div class="employee-submenu" style="display: none; padding-left: 15px;">
                            <a href="{{ url('/time_management/change_shift/index/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fa-solid fa-user"></i>
                                <span>Employee Shift</span>
                            </a>
                            <a href="{{ url('/time_management/warning_letter/assign/index2/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Warning Letter</span>
                            </a>
                            <a href="{{ url('/time_management/overtime/management/index2/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fas fa-business-time"></i>
                                <span>Overtime</span>
                            </a>
                            <a href="{{ url('/time_management/request_resign/index2/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fas fa-door-open"></i>
                                <span>Resign</span>
                            </a>
                            <a href="{{ url('/time_management/time_off/request_time_off/index2/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fa-solid fa-user-tie"></i>
                                <span>Time Off</span>
                            </a>
                        </div>
                    </div>
                </li>

                <!-- Evaluation -->
                <li class="nav-item">
                    <a href="#" class="nav-link dropdown-toggle evaluation-dropdown">
                        <i class="fas fa-chart-line"></i>
                        <span>Evaluation</span>
                    </a>
                    <div class="dropdown-container">


                        <!-- Rule submenu -->
                        <a href="#" class="nav-link dropdown-toggle rule-dropdown">
                            <i class="fas fa-balance-scale"></i>
                            <span>Rule</span>
                        </a>
                        <div class="rule-submenu" style="display: none; padding-left: 15px;">


                            <!-- Performance -->
                            <a href="#" class="nav-link dropdown-toggle rule-discipline-dropdown">
                                <i class="fa-solid fa-scale-balanced"></i> <!-- Ikon timbangan hukum untuk Discipline -->
                                <span>Discipline</span>
                            </a>
                            <div class="rule-discipline-submenu" style="display: none; padding-left: 15px;">

                                <!-- Grade -->
                                <a href="{{ route('evaluation.rule.discipline.grade.index') }}" class="nav-link">
                                    <i class="fa-solid fa-graduation-cap"></i> <!-- Ikon topi wisuda untuk Grade -->
                                    <span>Grade</span>
                                </a>

                                <!-- Score -->
                                <a href="{{ route('evaluation.rule.discipline.score.index') }}" class="nav-link">
                                    <i class="fa-solid fa-medal"></i> <!-- Ikon medali untuk Score -->
                                    <span>Score</span>
                                </a>

                            </div>



                            <!-- Performance -->
                            <a href="#" class="nav-link dropdown-toggle performance-dropdown">
                                <i class="fas fa-chart-bar"></i>
                                <span>Performance</span>
                            </a>
                            <div class="performance-submenu" style="display: none; padding-left: 15px;">
                                <!-- Criteria -->
                                <a href="{{ route('evaluation.rule.performance.criteria.index') }}" class="nav-link">
                                    <i class="fas fa-list-ol"></i>
                                    <span>Criteria</span>
                                </a>

                                <!-- Weight -->
                                <a href="{{ route('evaluation.rule.performance.weight.index') }}" class="nav-link">
                                    <i class="fas fa-weight"></i>
                                    <span>Weight</span>
                                </a>

                                <!-- Reduction -->
                                <a href="{{ route('evaluation.rule.performance.reduction.index') }}" class="nav-link">
                                    <i class="fa-solid fa-square-minus"></i>
                                    <span>Reduction</span>
                                </a>

                                <!-- Grade -->
                                <a href="{{ route('evaluation.rule.performance.grade.index') }}" class="nav-link">
                                    <i class="fa-solid fa-graduation-cap"></i> <!-- Ikon topi wisuda untuk Grade -->
                                    <span>Grade</span>
                                </a>

                            </div>


                            <!-- elearning -->
                            <a href="#" class="nav-link dropdown-toggle evaluation-elearning-dropdown">
                                <i class="fas fa-book"></i>
                                <span>E-learning</span>
                            </a>
                            <div class="evaluation-elearning-submenu" style="display: none; padding-left: 15px;">
                                <!-- Grade -->
                                <a href="{{ route('evaluation.rule.elearning.grade.index') }}" class="nav-link">
                                    <i class="fas fa-list-ol"></i>
                                    <span>Grade</span>
                                </a>
                            </div>

                            <!-- elearning -->
                            <a href="#" class="nav-link dropdown-toggle evaluation-final-dropdown">
                                <i class="fa-solid fa-square-poll-vertical"></i>
                                <span>Final</span>
                            </a>
                            <div class="evaluation-final-submenu" style="display: none; padding-left: 15px;">
                                <!-- Grade -->
                                <a href="{{ route('evaluation.rule.grade.salary.index') }}" class="nav-link">
                                    <i class="fa-solid fa-coins"></i>
                                    <span>Salary Grade</span>
                                </a>
                            </div>
                        </div>

                        <!-- Assignment submenu -->
                        <a href="#" class="nav-link dropdown-toggle evaluation-assignment-dropdown">
                            <i class="fas fa-tasks"></i>
                            <span>Assignment</span>
                        </a>
                        <div class="evaluation-assignment-submenu" style="display: none; padding-left: 15px;">
                            <!-- Performance -->
                            <a href="{{ url('/evaluation/assign/performance/index/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fas fa-chart-bar"></i>
                                <span>Performance</span>
                            </a>
                        </div>


                        <!-- Report submenu -->
                        <a href="#" class="nav-link dropdown-toggle evaluation-report-dropdown">
                            <i class="fa-solid fa-book"></i>
                            <span>Report</span>
                        </a>
                        <div class="evaluation-report-submenu" style="display: none; padding-left: 15px;">
                            <!-- Performance -->

                            <a href="{{  route(    'evaluation.report.performance.index') }}" class="nav-link">
                                <i class="fas fa-chart-bar"></i>
                                <span>Performance</span>
                            </a>

                            <a href="{{  route('evaluation.report.discipline.index') }}" class="nav-link">
                                <i class="fa-solid fa-clipboard-user"></i>
                                <span>Discipline</span>
                            </a>

                            <a href="{{  route('evaluation.report.elearning.index') }}" class="nav-link">
                                <i class="fa-solid fa-chalkboard"></i>
                                <span>E-learning</span>
                            </a>


                            <a href="#" class="nav-link dropdown-toggle final-dropdown">
                                <i class="fa-solid fa-file-lines"></i>
                                <span>Final</span>
                                <!-- <i class="dropdown-icon fas fa-chevron-right ms-auto"></i> -->
                            </a>
                            <div class="final-submenu" style="display: none; padding-left: 15px;">
                                <a href="{{  route('evaluation.report.final.calculate.index') }}" class="nav-link">
                                    <i class="fa fa-calculator"></i>
                                    <span>Calculate</span>
                                </a>
                                <a href="{{ route('evaluation.report.final.result.index') }}" class="nav-link">
                                    <i class="fa-solid fa-square-poll-vertical"></i>
                                    <span>Result</span>
                                </a>

                                <a href="{{ route('evaluation.report.final.graph.index') }}" class="nav-link">
                                    <i class="fa-solid fa-chart-simple"></i>
                                    <span>Graphic</span>
                                </a>

                            </div>
                        </div>
                    </div>
                </li>

                <!-- Pay Roll -->
                <a href="#" class="nav-link dropdown-toggle payroll-dropdown d-flex align-items-center gap-2">
                    <i class="fas fa-money-check-alt"></i>
                    <span>Payroll</span>
                </a>
                <div class="payroll-submenu" style="display: none; padding-left: 15px;">
                    <a href="{{ route('payroll.master.salary.index') }}" class="nav-link d-flex align-items-center gap-2">
                        <i class="fas fa-coins"></i>
                        <span>Base Salary</span>
                    </a>

                    <a href="{{ route('payroll.salary_history.index') }}" class="nav-link d-flex align-items-center gap-2">
                        <i class="fas fa-history"></i>
                        <span>Salary History</span>
                    </a>

                    <a href="{{ route('payroll.assign.index') }}" class="nav-link d-flex align-items-center gap-2">
                        <i class="fas fa-user-cog"></i>
                        <span>Assign Payroll</span>
                    </a>
                </div>


                @elseif(Auth::user()->isManagerAcptHR())
                <li class="nav-item">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-book"></i>
                        <span>E-learning</span>
                    </a>
                    <div class="dropdown-container">
                        <a href="{{ url('/elearning/index2/' . Auth::user()->id) }}" class="nav-link">
                            <i class="fas fa-tasks"></i>
                            <span>E-learning Duty</span>
                        </a>
                    </div>
                </li>


                <!-- Time Management -->
                <li class="nav-item">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-clock"></i>
                        <span>Time Management</span>
                    </a>
                    <div class="dropdown-container">
                        <!-- Human Resources -->
                        <a href="#" class="nav-link dropdown-toggle hr-dropdown">
                            <i class="fas fa-user-tie"></i>
                            <span>Management</span>
                            <!-- <i class="dropdown-icon fas fa-chevron-right ms-auto"></i> -->
                        </a>
                        <div class="hr-submenu" style="display: none; padding-left: 15px;">
                            <!-- Existing shift dropdown with icon -->
                            <a href="#" class="nav-link dropdown-toggle shift-dropdown">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Shift</span>
                                <!-- <i class="dropdown-icon fas fa-chevron-right ms-auto"></i> -->
                            </a>
                            <div class="shift-submenu" style="display: none; padding-left: 15px;">

                                <a href="{{ route('change_shift.index')}}" class="nav-link">
                                    <i class="fas fa-exchange-alt"></i> {{-- Ikon untuk permintaan/perubahan shift --}}
                                    <span>Request Shift</span>
                                </a>
                            </div>




                            <a href="#" class="nav-link dropdown-toggle timeOff-dropdown">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Time Off</span>
                                <!-- <i class="dropdown-icon fas fa-chevron-right ms-auto"></i> -->
                            </a>
                            <div class="timeOff-submenu" style="display: none; padding-left: 15px;">

                                <a href="{{ url('/time_management/time_off/request_time_off/index') }}" class="nav-link">
                                    <i class="fa-solid fa-user-tie"></i>
                                    <span>Request Time Off</span>
                                </a>
                            </div>

                            <a href="#" class="nav-link dropdown-toggle overtime-dropdown">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Ovetime</span>
                                <!-- <i class="dropdown-icon fas fa-chevron-right ms-auto"></i> -->
                            </a>
                            <div class="overtime-submenu" style="display: none; padding-left: 15px;">
                                <a href="{{ url('/time_management/overtime/management/index') }}" class="nav-link">
                                    <i class="fas fa-business-time"></i>
                                    <span>Management</span>
                                </a>

                            </div>



                        </div>

                        <!-- Employee -->
                        <a href="#" class="nav-link dropdown-toggle employee-dropdown">
                            <i class="fa-solid fa-user"></i>
                            <span>Employee</span>
                        </a>
                        <div class="employee-submenu" style="display: none; padding-left: 15px;">
                            <a href="{{ url('/time_management/change_shift/index/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fa-solid fa-user"></i>
                                <span>Employee Shift</span>
                            </a>
                            <a href="{{ url('/time_management/warning_letter/assign/index2/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Warning Letter</span>
                            </a>
                            <a href="{{ url('/time_management/overtime/management/index2/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fas fa-business-time"></i>
                                <span>Overtime</span>
                            </a>
                            <a href="{{ url('/time_management/request_resign/index2/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fas fa-door-open"></i>
                                <span>Resign</span>
                            </a>
                            <a href="{{ url('/time_management/time_off/request_time_off/index2/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fa-solid fa-user-tie"></i>
                                <span>Time Off</span>
                            </a>
                        </div>
                    </div>
                </li>

                <!-- Evaluation -->
                <li class="nav-item">
                    <a href="#" class="nav-link dropdown-toggle evaluation-dropdown">
                        <i class="fas fa-chart-line"></i>
                        <span>Evaluation</span>
                    </a>
                    <div class="dropdown-container">
                        <!-- Assignment submenu -->
                        <a href="#" class="nav-link dropdown-toggle evaluation-assignment-dropdown">
                            <i class="fas fa-tasks"></i>
                            <span>Assignment</span>
                        </a>
                        <div class="evaluation-assignment-submenu" style="display: none; padding-left: 15px;">
                            <!-- Performance -->
                            <a href="{{ url('/evaluation/assign/performance/index/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fas fa-chart-bar"></i>
                                <span>Performance</span>
                            </a>
                        </div>
                    </div>
                </li>


                @elseif(Auth::user()->isSupervisor())
                <li class="nav-item">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-book"></i>
                        <span>E-learning</span>
                    </a>
                    <div class="dropdown-container">
                        <a href="{{ url('/elearning/index2/' . Auth::user()->id) }}" class="nav-link">
                            <i class="fas fa-tasks"></i>
                            <span>E-learning Duty</span>
                        </a>
                    </div>
                </li>

                <!-- Time Management -->
                <li class="nav-item">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-clock"></i>
                        <span>Time Management</span>
                    </a>
                    <div class="dropdown-container">
                        <a href="#" class="nav-link dropdown-toggle employee-dropdown">
                            <i class="fa-solid fa-user"></i>
                            <span>Employee</span>
                        </a>
                        <div class="employee-submenu" style="display: none; padding-left: 15px;">
                            <a href="{{ url('/time_management/change_shift/index/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fa-solid fa-user"></i>
                                <span>Employee Shift</span>
                            </a>
                            <a href="{{ url('/time_management/warning_letter/assign/index2/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Warning Letter</span>
                            </a>
                            <a href="{{ url('/time_management/overtime/management/index2/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fas fa-business-time"></i>
                                <span>Overtime</span>
                            </a>
                            <a href="{{ url('/time_management/request_resign/index2/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fas fa-door-open"></i>
                                <span>Resign</span>
                            </a>
                            <a href="{{ url('/time_management/time_off/request_time_off/index2/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fa-solid fa-user-tie"></i>
                                <span>Time Off</span>
                            </a>
                        </div>
                    </div>
                </li>

                <!-- Evaluation -->
                <li class="nav-item">
                    <a href="#" class="nav-link dropdown-toggle evaluation-dropdown">
                        <i class="fas fa-chart-line"></i>
                        <span>Evaluation</span>
                    </a>
                    <div class="dropdown-container">
                        <!-- Assignment submenu -->
                        <a href="#" class="nav-link dropdown-toggle evaluation-assignment-dropdown">
                            <i class="fas fa-tasks"></i>
                            <span>Assignment</span>
                        </a>
                        <div class="evaluation-assignment-submenu" style="display: none; padding-left: 15px;">
                            <!-- Performance -->
                            <a href="{{ url('/evaluation/assign/performance/index/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fas fa-chart-bar"></i>
                                <span>Performance</span>
                            </a>
                        </div>
                    </div>
                </li>

                @elseif(Auth::user()->isStaff())
                <!-- E-learning -->
                <li class="nav-item">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-book"></i>
                        <span>E-learning</span>
                    </a>
                    <div class="dropdown-container">
                        <a href="{{ url('/elearning/index2/' . Auth::user()->id) }}" class="nav-link">
                            <i class="fas fa-tasks"></i>
                            <span>E-learning Duty</span>
                        </a>
                    </div>
                </li>

                <!-- Time Management -->
                <li class="nav-item">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-clock"></i>
                        <span>Time Management</span>
                    </a>
                    <div class="dropdown-container">
                        <a href="#" class="nav-link dropdown-toggle employee-dropdown">
                            <i class="fa-solid fa-user"></i>
                            <span>Employee</span>
                        </a>
                        <div class="employee-submenu" style="display: none; padding-left: 15px;">
                            <a href="{{ url('/time_management/change_shift/index/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fa-solid fa-user"></i>
                                <span>Employee Shift</span>
                            </a>
                            <a href="{{ url('/time_management/warning_letter/assign/index2/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Warning Letter</span>
                            </a>
                            <a href="{{ url('/time_management/overtime/management/index2/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fas fa-business-time"></i>
                                <span>Overtime</span>
                            </a>
                            <a href="{{ url('/time_management/request_resign/index2/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fas fa-door-open"></i>
                                <span>Resign</span>
                            </a>
                            <a href="{{ url('/time_management/time_off/request_time_off/index2/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fa-solid fa-user-tie"></i>
                                <span>Time Off</span>
                            </a>
                        </div>
                    </div>
                </li>



                @else

                <!-- E-learning -->
                <li class="nav-item">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-book"></i>
                        <span>E-learning</span>
                    </a>
                    <div class="dropdown-container">
                        <a href="{{ url('/elearning/index2/' . Auth::user()->id) }}" class="nav-link">
                            <i class="fas fa-tasks"></i>
                            <span>E-learning Duty</span>
                        </a>
                    </div>
                </li>

                <!-- Time Management -->
                <li class="nav-item">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-clock"></i>
                        <span>Time Management</span>
                    </a>
                    <div class="dropdown-container">
                        <!-- Employee -->
                        <a href="#" class="nav-link dropdown-toggle employee-dropdown">
                            <i class="fa-solid fa-user"></i>
                            <span>Employee</span>
                        </a>
                        <div class="employee-submenu" style="display: none; padding-left: 15px;">
                            <a href="{{ url('/time_management/change_shift/index/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fa-solid fa-user"></i>
                                <span>Employee Shift</span>
                            </a>
                            <a href="{{ url('/time_management/warning_letter/assign/index2/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Warning Letter</span>
                            </a>
                            <a href="{{ url('/time_management/overtime/management/index2/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fas fa-business-time"></i>
                                <span>Overtime</span>
                            </a>
                            <a href="{{ url('/time_management/request_resign/index2/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fas fa-door-open"></i>
                                <span>Resign</span>
                            </a>
                            <a href="{{ url('/time_management/time_off/request_time_off/index2/' . Auth::user()->id) }}" class="nav-link">
                                <i class="fa-solid fa-user-tie"></i>
                                <span>Time Off</span>
                            </a>
                        </div>
                    </div>
                </li>
                @endif
            </ul>
        </div>

        <!-- Profile section at bottom of sidebar -->
        <div class="profile-section">

            <a href="{{ route('user.employees.edit', Auth::user()->id) }}" class="profile-link">
                <img src="{{ Auth::user()->photo_profile_path ? asset('storage/'. Auth::user()->photo_profile_path) : asset('storage/default_profile.png') }}" alt="Profile Picture" class="profile-image">
                <div class="profile-info">
                    <span>{{ Auth::user()->name }}</span>
                </div>
                <div class="settings-icon">
                    <i class="fa-solid fa-gear"></i>
                </div>
            </a>
        </div>

    </div>

    <div id="content">
        <div class="top-bar">
            <button id="sidebarToggle" class="btn btn-link text-white">
                <i class="fas fa-bars"></i>
            </button>
            <div class="top-bar-right">
                <div class="dropdown">
                    <a href="#" class="top-bar-icon position-relative" id="notificationDropdown">
                        <i class="fas fa-bell"></i>
                        @php
                        $unreadCount = \App\Models\notification::where('users_id', Auth::id())
                        ->where('status', 'Unread')
                        ->count();
                        @endphp
                        @if($unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge">
                            {{ $unreadCount }}
                        </span>
                        @endif
                    </a>
                    <div class="notification-dropdown" aria-labelledby="notificationDropdown">
                        <div class="notification-header d-flex justify-content-between align-items-center p-3 border-bottom">
                            <h6 class="m-0">Notifications</h6>
                            <span class="badge bg-primary rounded-pill">
                                {{ \App\Models\notification::where('users_id', Auth::id())->where('status', 'Unread')->count() }}
                            </span>
                        </div>
                        <div class="notification-body">
                            @php
                            $notifications = \App\Models\notification::where('users_id', Auth::id())
                            ->where('status', 'Unread')
                            ->orderBy('created_at', 'desc')
                            ->take(3)
                            ->get();



                            $notificationMakers = \App\Models\User::whereIn('id', $notifications->pluck('maker_id'))
                            ->pluck('name', 'id');
                            @endphp

                            @if($notifications->count() > 0)
                            @foreach($notifications as $notification)

                            <div class="dropdown-item notification-item p-3 border-bottom {{ $notification->status == 'Unread' ? 'bg-light' : '' }}"
                                data-id="{{ $notification->id }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>{{ $notificationMakers[$notification->maker_id] ?? 'System' }}</strong>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                                </div>
                                <div class="row align-items-center">
                                    <div class="col-md-10">
                                        <p class="mb-0 text-truncate">{{ $notification->message }}</p>
                                    </div>

                                    @if($notification->type == 'general')
                                    <div class="col-md-2 text-end mt-1">
                                        <a href="{{ route('notification.index') }}" class="text-primary">
                                            <i class="fa-solid fa-arrow-right fa-xl"></i>
                                        </a>
                                    </div>
                                    @elseif($notification->type == 'elearning_duty')
                                    <div class="col-md-2 text-end mt-1">
                                        <a href="{{ url('/elearning/index2/' . Auth::user()->id) }}" class="text-primary">
                                            <i class="fa-solid fa-arrow-right fa-xl"></i>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                            @else
                            <div class="p-3 text-center">No notifications</div>
                            @endif
                        </div>
                        <div class="notification-footer p-2 text-center border-top">
                            <a href="{{ route('notification.index') }}" class="text-primary">View All Notifications</a>
                        </div>
                    </div>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>

                <a href="{{ route('logout') }}" class="top-bar-icon"
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


    <!-- DataTables CSS & JS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- Chart Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">




    @vite(['resources/js/app.js'])
    <script>
        $(document).ready(function() {
            // HR Dropdown Toggle
            $('.hr-dropdown').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle active class for arrow rotation
                $(this).toggleClass('active');

                // Toggle the HR submenu with animation
                $('.hr-submenu').slideToggle(300);

                return false;
            });

            // Shift Dropdown Toggle (inside HR submenu)
            $('.shift-dropdown').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle active class for arrow rotation
                $(this).toggleClass('active');

                // Toggle the shift submenu with animation
                $('.shift-submenu').slideToggle(300);

                return false;
            });

            // TimeOff Dropdown Toggle (inside HR submenu)
            $('.timeOff-dropdown').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle active class for arrow rotation
                $(this).toggleClass('active');

                // Toggle the timeOff submenu with animation
                $('.timeOff-submenu').slideToggle(300);

                return false;
            });


            $('.overtime-dropdown').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle active class for arrow rotation
                $(this).toggleClass('active');

                // Toggle the overtime submenu with animation
                $('.overtime-submenu').slideToggle(300);

                return false;
            });

            $('.attendance-dropdown').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle active class for arrow rotation
                $(this).toggleClass('active');

                // Toggle the attendance submenu with animation
                $('.attendance-submenu').slideToggle(300);

                return false;
            });

            $('.warningLetter-dropdown').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle active class for arrow rotation
                $(this).toggleClass('active');

                // Toggle the warningLetter submenu with animation
                $('.warningLetter-submenu').slideToggle(300);

                return false;
            });

            // Employee Dropdown Toggle
            $('.employee-dropdown').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle active class for arrow rotation
                $(this).toggleClass('active');

                // Toggle the employee submenu with animation
                $('.employee-submenu').slideToggle(300);

                return false;
            });

            // Evaluation Rule Dropdown Toggle
            $('.rule-dropdown').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle active class for arrow rotation
                $(this).toggleClass('active');

                // Toggle the rule submenu with animation
                $('.rule-submenu').slideToggle(300);

                return false;
            });

            $('.evaluation-assignment-dropdown').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle active class for arrow rotation
                $(this).toggleClass('active');

                // Toggle the rule submenu with animation
                $('.evaluation-assignment-submenu').slideToggle(300);

                return false;
            });

            $('.evaluation-report-dropdown').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle active class for arrow rotation
                $(this).toggleClass('active');

                // Toggle the rule submenu with animation
                $('.evaluation-report-submenu').slideToggle(300);

                return false;
            });



            $('.user-dropdown').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle active class for arrow rotation
                $(this).toggleClass('active');

                // Toggle the rule submenu with animation
                $('.user-submenu').slideToggle(300);

                return false;
            });

            $('.payroll-dropdown').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle active class for arrow rotation
                $(this).toggleClass('active');

                // Toggle the rule submenu with animation
                $('.payroll-submenu').slideToggle(300);

                return false;
            });

            $('.performance-dropdown').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle active class for arrow rotation
                $(this).toggleClass('active');

                // Toggle the performance submenu with animation
                $('.performance-submenu').slideToggle(300);

                return false;
            });

            $('.evaluation-elearning-dropdown').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle active class for arrow rotation
                $(this).toggleClass('active');

                // Toggle the elearning submenu with animation
                $('.evaluation-elearning-submenu').slideToggle(300);

                return false;
            });

            $('.evaluation-final-dropdown').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle active class for arrow rotation
                $(this).toggleClass('active');


                $('.evaluation-final-submenu').slideToggle(300);

                return false;
            });

            $('.final-dropdown').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle active class for arrow rotation
                $(this).toggleClass('active');


                $('.final-submenu').slideToggle(300);

                return false;
            });

            $('.rule-discipline-dropdown').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle active class for arrow rotation
                $(this).toggleClass('active');

                // Toggle the performance submenu with animation
                $('.rule-discipline-submenu').slideToggle(300);

                return false;
            });


            // Add floating shapes to the body
            $('body').append('<div class="floating-shape"></div>');

            // Make sure the Time Management dropdown works properly
            $('.nav-link.dropdown-toggle').not('.shift-dropdown').on('click', function(e) {
                e.preventDefault();

                // Ensure the shift submenu is closed when another main dropdown is clicked
                if (!$(this).hasClass('shift-dropdown') && !$(this).closest('.shift-submenu').length) {
                    $('.shift-dropdown').removeClass('active');
                    $('.shift-submenu').slideUp(300);
                }
            });

            $('.nav-link.dropdown-toggle').not('.timeOff-dropdown').on('click', function(e) {
                e.preventDefault();

                // Ensure the shift submenu is closed when another main dropdown is clicked
                if (!$(this).hasClass('shift-dropdown') && !$(this).closest('.timeOff-submenu').length) {
                    $('.timeOff-dropdown').removeClass('active');
                    $('.timeOff-submenu').slideUp(300);
                }
            });


            $('.nav-link.dropdown-toggle').not('.overtime-dropdown').on('click', function(e) {
                e.preventDefault();

                // Ensure the shift submenu is closed when another main dropdown is clicked
                if (!$(this).hasClass('shift-dropdown') && !$(this).closest('.overtime-submenu').length) {
                    $('.overtime-dropdown').removeClass('active');
                    $('.overtime-submenu').slideUp(300);
                }
            });

            $('.nav-link.dropdown-toggle').not('.attendance-dropdown').on('click', function(e) {
                e.preventDefault();

                // Ensure the shift submenu is closed when another main dropdown is clicked
                if (!$(this).hasClass('shift-dropdown') && !$(this).closest('.attendance-submenu').length) {
                    $('.attendance-dropdown').removeClass('active');
                    $('.attendance-submenu').slideUp(300);
                }
            });

            $('.nav-link.dropdown-toggle').not('.warningLetter-dropdown').on('click', function(e) {
                e.preventDefault();

                // Ensure the shift submenu is closed when another main dropdown is clicked
                if (!$(this).hasClass('shift-dropdown') && !$(this).closest('.warningLetter-submenu').length) {
                    $('.warningLetter-dropdown').removeClass('active');
                    $('.warningLetter-submenu').slideUp(300);
                }
            });

            $('.nav-link.dropdown-toggle').not('.performance-dropdown').on('click', function(e) {
                e.preventDefault();

                // Ensure the shift submenu is closed when another main dropdown is clicked
                if (!$(this).hasClass('shift-dropdown') && !$(this).closest('.performance-submenu').length) {
                    $('.performance-dropdown').removeClass('active');
                    $('.performance-submenu').slideUp(300);
                }
            });


            $('.nav-link.dropdown-toggle').not('.elarning-dropdown').on('click', function(e) {
                e.preventDefault();

                // Ensure the shift submenu is closed when another main dropdown is clicked
                if (!$(this).hasClass('shift-dropdown') && !$(this).closest('.elarning-submenu').length) {
                    $('.elarning-dropdown').removeClass('active');
                    $('.elarning-submenu').slideUp(300);
                }
            });

            $('.nav-link.dropdown-toggle').not('.rule-discipline-dropdown').on('click', function(e) {
                e.preventDefault();

                // Ensure the shift submenu is closed when another main dropdown is clicked
                if (!$(this).hasClass('shift-dropdown') && !$(this).closest('.rule-discipline-submenu').length) {
                    $('.rule-discipline-dropdown').removeClass('active');
                    $('.rule-discipline-submenu').slideUp(300);
                }
            });






            // Close submenus when clicking elsewhere
            $(document).on('click', function(e) {
                // HR Submenu
                if (!$(e.target).closest('.hr-dropdown, .hr-submenu').length) {
                    $('.hr-dropdown').removeClass('active');
                    $('.hr-submenu').slideUp(300);
                }

                // Shift Submenu
                if (!$(e.target).closest('.shift-dropdown, .shift-submenu').length) {
                    $('.shift-dropdown').removeClass('active');
                    $('.shift-submenu').slideUp(300);
                }

                // TimeOff Submenu
                if (!$(e.target).closest('.timeOff-dropdown, .timeOff-submenu').length) {
                    $('.timeOff-dropdown').removeClass('active');
                    $('.timeOff-submenu').slideUp(300);
                }

                // overtime Submenu
                if (!$(e.target).closest('.overtime-dropdown, .overtime-submenu').length) {
                    $('.overtime-dropdown').removeClass('active');
                    $('.overtime-submenu').slideUp(300);
                }

                // TimeOff Submenu
                if (!$(e.target).closest('.attendance-dropdown, .attendance-submenu').length) {
                    $('.attendance-dropdown').removeClass('active');
                    $('.attendance-submenu').slideUp(300);
                }
                if (!$(e.target).closest('.rule-discipline-dropdown, .rule-discipline-submenu').length) {
                    $('.rule-discipline-dropdown').removeClass('active');
                    $('.rule-discipline-submenu').slideUp(300);
                }


                if (!$(e.target).closest('.warningLetter-dropdown, .warningLetter-submenu').length) {
                    $('.warningLetter-dropdown').removeClass('active');
                    $('.warningLetter-submenu').slideUp(300);
                }

                // Employee Submenu
                if (!$(e.target).closest('.employee-dropdown, .employee-submenu').length) {
                    $('.employee-dropdown').removeClass('active');
                    $('.employee-submenu').slideUp(300);
                }

                // Rule Submenu
                if (!$(e.target).closest('.rule-dropdown, .rule-submenu').length) {
                    $('.rule-dropdown').removeClass('active');
                    $('.rule-submenu').slideUp(300);
                }

                // Rule Submenu
                if (!$(e.target).closest('.evaluation-assignment-dropdown, .evaluation-assignment-submenu').length) {
                    $('.evaluation-assignment-dropdown').removeClass('active');
                    $('.evaluation-assignment-submenu').slideUp(300);
                }

                // Rule Submenu
                if (!$(e.target).closest('.evaluation-report-dropdown, .evaluation-report-submenu').length) {
                    $('.evaluation-report-dropdown').removeClass('active');
                    $('.evaluation-report-submenu').slideUp(300);
                }

                // Rule Submenu
                if (!$(e.target).closest('.user-dropdown, .user-submenu').length) {
                    $('.user-dropdown').removeClass('active');
                    $('.user-submenu').slideUp(300);
                }


                if (!$(e.target).closest('.payroll-dropdown, .payroll-submenu').length) {
                    $('.payroll-dropdown').removeClass('active');
                    $('.payroll-submenu').slideUp(300);
                }


                if (!$(e.target).closest('.performance-dropdown, .performance-submenu').length) {
                    $('.performance-dropdown').removeClass('active');
                    $('.performance-submenu').slideUp(300);
                }

                if (!$(e.target).closest('.evaluation-elearning-dropdown, .evaluation-elearning-submenu').length) {
                    $('.evaluation-elearning-dropdown').removeClass('active');
                    $('.evaluation-elearning-submenu').slideUp(300);
                }

                if (!$(e.target).closest('.evaluation-final-dropdown, .evaluation-final-submenu').length) {
                    $('.evaluation-final-dropdown').removeClass('active');
                    $('.evaluation-final-submenu').slideUp(300);
                }

                if (!$(e.target).closest('.final-dropdown, .final-submenu').length) {
                    $('.final-dropdown').removeClass('active');
                    $('.final-submenu').slideUp(300);
                }
            });

            // Prevent parent dropdown from closing when clicking on submenu
            $('.hr-submenu, .shift-submenu, .timeOff-submenu, .overtime-submenu,.evaluation-elearning-submenu, .evaluation-final-submenu, .final-submenu, .attendance-submenu, rule-discipline-submenu, .warningLetter-submenu, .employee-submenu, .rule-submenu, .user-submenu, .evaluation-assignment-submenu, .evaluation-report-submenu, .performance-submenu').on('click', function(e) {
                e.stopPropagation();
            });



            // Create overlay element for mobile sidebar
            $('body').append('<div class="sidebar-overlay"></div>');

            // Clean notification handling
            $("#notificationDropdown").on("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).siblings(".notification-dropdown").toggle();
            });

            // Close notification dropdown when clicking elsewhere
            $(document).on("click", function(e) {
                if (!$(e.target).closest(".dropdown").length) {
                    $(".notification-dropdown").hide();
                }
            });

            // Prevent dropdown from closing when clicking inside
            $(".notification-dropdown").on("click", function(e) {
                e.stopPropagation();
            });

            // Mark notifications as read
            $(".notification-item").on("click", function() {
                var notificationId = $(this).data("id");
                var item = $(this);

                $.ajax({
                    url: "/notification/mark-read/" + notificationId,
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            item.removeClass("bg-light");
                        }
                    }
                });
            });

            // Improved sidebar toggle handling
            // Ganti kode sidebar toggle dengan ini
            $('#sidebarToggle').click(function(e) {
                e.preventDefault();
                e.stopPropagation();

                if ($(window).width() < 768) {
                    // Mobile behavior
                    $('#sidebar').toggleClass('show');
                    $('.sidebar-overlay').toggleClass('show');
                    $('body').toggleClass('no-scroll');
                } else {
                    // Desktop behavior
                    $('#sidebar').toggleClass('collapsed');
                    $('#content').toggleClass('expanded');
                }
            });

            // Close sidebar when overlay is clicked
            $('.sidebar-overlay').click(function() {
                $('#sidebar').removeClass('show');
                $('.sidebar-overlay').removeClass('show');
                $('body').removeClass('no-scroll');
            });
            // PERBAIKAN: Handle sidebar dropdowns
            $('.dropdown-toggle').click(function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle dropdown container with animation
                $(this).siblings('.dropdown-container').slideToggle(300);

                // Toggle aria-expanded state
                let isExpanded = $(this).attr('aria-expanded') === 'true';
                $(this).attr('aria-expanded', !isExpanded);

                // Focus handling for better accessibility
                if (!isExpanded) {
                    $(this).siblings('.dropdown-container').find('a:first').focus();
                }

                return false;
            });

            $('.dropdown-toggle').keydown(function(e) {
                // Space or Enter key
                if (e.keyCode === 32 || e.keyCode === 13) {
                    e.preventDefault();
                    $(this).click();
                }
            });



            // Handle responsive layout
            function adjustForScreenSize() {
                if ($(window).width() < 768) {
                    // Mobile view
                    $('#content').css('margin-left', '0');

                    // Close sidebar by default on mobile
                    $('#sidebar').removeClass('collapsed').removeClass('show');
                    $('.sidebar-overlay').removeClass('show');
                    $('#sidebarToggle').removeClass('active');
                } else {
                    // Desktop view
                    $('#sidebar').removeClass('show');
                    $('.sidebar-overlay').removeClass('show');

                    if ($('#sidebar').hasClass('collapsed')) {
                        $('#content').addClass('expanded');
                    } else {
                        $('#content').removeClass('expanded');
                    }
                }
            }


            // Initialize responsive behavior
            adjustForScreenSize();
            $(window).resize(adjustForScreenSize);

            // PERBAIKAN: Close sidebar when a menu item is clicked on mobile - BUT NOT DROPDOWN TOGGLES
            $('#sidebar .nav-link:not(.dropdown-toggle)').click(function() {
                if ($(window).width() < 768) {
                    $('#sidebar').removeClass('show');
                    $('.sidebar-overlay').removeClass('show');
                    $('body').removeClass('no-scroll');
                }
            });

            // Add sub-menu accessibility
            $('.dropdown-toggle').attr('aria-expanded', 'false');
            $('.dropdown-toggle').attr('role', 'button');
            $('.dropdown-container').attr('role', 'menu');

            // PERBAIKAN UNTUK MODAL: Pastikan modal memiliki z-index lebih tinggi dari sidebar
            // Tambahkan kode CSS inline
            $('<style>')
                .prop('type', 'text/css')
                .html(`
              
                    .modal-backdrop {
                        z-index: 1040 !important;
                    }
                    .modal {
                        z-index: 1050 !important;
                    }
                    .modal-dialog {
                        margin: 30px auto;
                        max-height: calc(100% - 60px);
                    }
                    .modal-content {
                        max-height: calc(100vh - 120px);
                        overflow-y: auto;
                    }
        `)
                .appendTo('head');
        });
    </script>

    @stack('scripts')
</body>

</html>