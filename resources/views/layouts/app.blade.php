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

        body::before {
            content: "";
            position: fixed;
            top: -100px;
            right: -50px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: linear-gradient(#ffc107, #ff6b6b);
            opacity: 0.2;
            z-index: -1;
        }

        body::after {
            content: "";
            position: fixed;
            bottom: -100px;
            left: -50px;
            width: 200px;
            height: 200px;
            background: linear-gradient(#4facfe, #00f2fe);
            opacity: 0.2;
            z-index: -1;
            clip-path: polygon(50% 0%, 100% 38%, 82% 100%, 18% 100%, 0% 38%);
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

        /* Sidebar Styles */
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
        }

        .floating-shape {
            position: fixed;
            width: 150px;
            height: 150px;
            background: linear-gradient(#f093fb, #f5576c);
            opacity: 0.2;
            z-index: -1;
            bottom: 50px;
            right: 20%;
            clip-path: polygon(25% 0%, 100% 0%, 75% 100%, 0% 100%);
            transform: rotate(45deg);
            animation: float 8s infinite ease-in-out;
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

        /* Perbaikan untuk dropdown sidebar */
        #sidebar .dropdown-container {
            margin-left: 1rem;
            display: none;
            position: relative;
            z-index: 1001;
            animation: slideDown 0.3s ease-out;
        }

        #sidebar .dropdown-container.show {
            display: block;
            animation: slideDown 0.3s ease-out;
        }



        /* Pastikan dropdown item memiliki pointer events */
        #sidebar .dropdown-container .nav-link {
            pointer-events: auto;
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

        #sidebar .dropdown-icon {
            margin-left: auto;
            transition: transform 0.3s;
            font-size: 0.85rem;
        }



        #sidebar .dropdown-toggle[aria-expanded="true"] .dropdown-icon,
        #sidebar .shift-dropdown.active .dropdown-icon {
            transform: rotate(90deg);
        }

        .nav-menu {
            flex-grow: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Content Styles */
        #content {
            margin-left: 250px;
            padding: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 990;
        }

        #content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Top Bar Styles */
        .top-bar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            position: relative;
            z-index: 995;
            align-items: center;
            border-radius: 12px;
            margin-bottom: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
        }

        .top-bar-icon:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        /* Dropdown Styles - Fixed */
        .dropdown {
            position: relative;
        }

        .notification-dropdown {
            position: absolute;
            top: 150%;
            right: 0;
            z-index: 1010;
            background: white;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            width: 320px;
            max-height: 400px;
            overflow-y: auto;
            display: none;
            backdrop-filter: blur(10px);
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
            transition: all 0.3s ease;
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
            transition: all 0.3s ease;
        }

        .notification-footer a:hover {
            text-decoration: underline;
            color: #ffcc00;
        }

        .bg-light {
            background: rgba(255, 255, 255, 0.3) !important;
        }

        .notification-body::-webkit-scrollbar {
            width: 6px;
        }

        .notification-body::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.5);
            border-radius: 10px;
        }

        .notification-body::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Profile Styles */
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

        .custom-card::before,
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


        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes float {
            0% {
                transform: rotate(45deg) translate(0, 0);
            }

            50% {
                transform: rotate(50deg) translate(10px, -10px);
            }

            100% {
                transform: rotate(45deg) translate(0, 0);
            }
        }

        /* Improved Mobile Sidebar Styles */
        @media (max-width: 768px) {

            /* Pastikan dropdown tidak tertutup sidebar */
            .dropdown-container .nav-link {
                padding: 0.8rem 1rem 0.8rem 2rem !important;
            }




            :root {
                --sidebar-width: 250px;
                --sidebar-collapsed-width: 0px;
            }

            #sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width);
                position: fixed;
                z-index: 1050;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            }

            #sidebar.show {
                transform: translateX(0);
            }

            #content {
                margin-left: 0 !important;
                width: 100%;
            }

            /* Overlay for when sidebar is shown on mobile */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1030;
            }


            .sidebar-overlay.show {
                display: block;
            }

            /* Ensure proper padding for mobile view */
            .top-bar {
                padding: 0.75rem;
                margin-bottom: 1rem;
                border-radius: 10px;
            }

            /* Adjust dropdown positioning for mobile */
            .notification-dropdown {
                position: fixed;
                top: 70px;
                right: 10px;
                left: auto;
                width: 90%;
                max-width: 350px;
            }

            /* Make sure sidebar content is scrollable on small screens */
            .nav-menu {
                max-height: calc(100vh - 150px);
                overflow-y: auto;
            }

            /* Improve dropdown visibility on mobile */
            #sidebar .dropdown-container {
                background: rgba(0, 0, 0, 0.1);
                border-radius: 8px;
                margin: 0 0.7rem;
            }
        }

        /* Smooth transitions for sidebar */
        #sidebar {
            transition: transform 0.3s ease, width 0.3s ease;
        }

        #content {
            transition: margin-left 0.3s ease;
        }

        /* Improved sidebar toggle animation */
        #sidebarToggle i {
            transition: transform 0.3s ease;
        }

        #sidebarToggle.active i {
            transform: rotate(90deg);
        }


        /* Custom styling for nested dropdowns */
        .shift-submenu .nav-link {
            padding-left: 2.5rem !important;
            font-size: 0.9rem;
        }

        .shift-dropdown {
            position: relative;
        }

        .shift-dropdown .dropdown-icon {
            transition: transform 0.3s;
        }

        .shift-dropdown.active .dropdown-icon {
            transform: rotate(90deg);
        }

        @media (max-width: 768px) {

            /* Ensure dropdown arrows remain visible on mobile */
            #sidebar.collapsed .dropdown-icon {
                display: block !important;
            }

            /* Proper indentation for nested items */
            .shift-submenu .nav-link {
                padding-left: 3rem !important;
            }

            /* Improved mobile dropdown visibility */
            #sidebar .dropdown-container {
                background: rgba(0, 0, 0, 0.1);
                border-radius: 8px;
                margin: 0 0.7rem;
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
                @if (Auth::user()->position == 'Manager'|| Auth::user()->position == 'Director'|| Auth::user()->position == 'General Manager')
                <li class="nav-item">
                    <a href="{{ url('/announcement/index')}}" class="nav-link">
                        <i class="fa-solid fa-bullhorn"></i>
                        <span>Announcement</span>
                    </a>
                </li>
                @endif

                <!-- HR and Management Menu Items -->
                @if (Auth::user()->department == 'Human Resources' ||
                Auth::user()->department == 'General Manager' ||
                Auth::user()->department == 'Director' ||
                Auth::user()->position != 'Staff')

                <li class="nav-item">
                    <a href="{{ url('/dashboard') }}" class="nav-link">
                        <i class="fa-solid fa-gauge"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('/user/index') }}" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Employee</span>
                    </a>
                </li>

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
                            <span>AHP Recomendation</span>
                        </a>
                        <a href="{{ url('/recruitment/interview/index') }}" class="nav-link">
                            <i class="fas fa-comments"></i>
                            <span>Interview</span>
                        </a>
                    </div>
                </li>

                <!-- Time Management with consistent dropdown arrow -->
                <li class="nav-item">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-clock"></i>
                        <span>Time Management</span>
                        <!-- <i class="dropdown-icon fas fa-chevron-right ms-auto"></i> -->
                    </a>
                    <div class="dropdown-container">
                        <!-- Shift submenu with consistent dropdown arrow -->
                        <a href="#" class="nav-link dropdown-toggle shift-dropdown">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Shift</span>
                            <!-- <i class="dropdown-icon fas fa-chevron-right ms-auto"></i> -->
                        </a>
                        <div class="shift-submenu" style="display: none; padding-left: 15px;">
                            <a href="{{ url('/time_management/rule_shift/index') }}" class="nav-link">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Rule</span>
                            </a>
                            <a href="{{ url('/time_management/set_shift/index') }}" class="nav-link">
                                <i class="fas fa-users-cog"></i>
                                <span>Employee Shift</span>
                            </a>
                        </div>

                        <!-- Other time management links -->
                        <a href="{{ url('/time_management/employee_absent/index') }}" class="nav-link">
                            <i class="fas fa-user-clock"></i>
                            <span>Employee Attendance</span>
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
                <!-- Limited menu for Staff users -->
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
                                {{ \App\Models\notification::where('users_id', Auth::id())->count() }}
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
                                    @if($notification->type == 'elearning_duty')
                                    <div class="col-md-2 text-end mt-1">
                                        <a href="{{ url('/elearning/index2/' . Auth::user()->id) }}" class="text-primary">
                                            <i class="fa-solid fa-arrow-right fa-xl"></i>
                                        </a>
                                    </div>
                                    @elseif($notification->type == 'general')
                                    <div class="col-md-2 text-end mt-1">
                                        <a href="{{ route('notification.index') }}" class="text-primary">
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

    <!-- Chart Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

    @vite(['resources/js/app.js'])
    <script>
        $(document).ready(function() {
            $('.shift-dropdown').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle active class for arrow rotation
                $(this).toggleClass('active');

                // Toggle the shift submenu with animation
                $('.shift-submenu').slideToggle(300);

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

            // Close shift submenu when clicking elsewhere
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.shift-dropdown, .shift-submenu').length) {
                    $('.shift-dropdown').removeClass('active');
                    $('.shift-submenu').slideUp(300);
                }
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
            $('#sidebarToggle').click(function() {
                if ($(window).width() < 768) {
                    // Mobile behavior
                    $('#sidebar').toggleClass('show');
                    $('.sidebar-overlay').toggleClass('show');
                    $(this).toggleClass('active');
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
                $('#sidebarToggle').removeClass('active');
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
                    $('#sidebarToggle').removeClass('active');
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