@extends('layouts.app')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
    .notification-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .notification-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .notification-card.unread {
        border-left: 4px solid var(--accent-color);
    }

    .notification-time {
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.6);
    }

    .notification-title {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .notification-content {
        color: rgba(255, 255, 255, 0.8);
    }

    .ring {
        animation: ring 2s ease infinite;
        transform-origin: 50% 0;
        display: inline-block;
    }

    @keyframes ring {
        0% {
            transform: rotate(0);
        }

        5% {
            transform: rotate(15deg);
        }

        10% {
            transform: rotate(-15deg);
        }

        15% {
            transform: rotate(15deg);
        }

        20% {
            transform: rotate(-15deg);
        }

        25% {
            transform: rotate(0);
        }

        100% {
            transform: rotate(0);
        }
    }

    .pagination-custom .page-item .page-link {
        color: white;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 8px 12px;
        margin: 0 5px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .pagination-custom .page-item.active .page-link {
        background: var(--accent-color);
        border-color: var(--accent-color);
        color: white;
    }

    .pagination-custom .page-item .page-link:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    .pagination-custom .page-item.disabled .page-link {
        color: rgba(255, 255, 255, 0.5);
        pointer-events: none;
    }
</style>


<h1 class="page-title text-warning text-center mb-5 mt-5">
    <i class="fas fa-bell ring"></i> Notifications
</h1>

<div class="container mb-4 p-0 mx-auto" id="container-body">
    <a href="{{ route('home') }}" class="btn btn-danger px-5 mb-5">
        <i class="fas fa-arrow-left me-2"></i>Back
    </a>
    <div class="custom-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-white m-0">All Notifications</h4>
            <button id="markAllRead" class="btn btn-sm btn-outline-light">
                <i class="fas fa-check-double"></i> Mark All as Read
            </button>
        </div>

        <div id="notifications-container">
            @if($notifications->count() > 0)
            @foreach($notifications as $notification)
            <div class="notification-card p-3 {{ $notification->status == 'Unread' ? 'unread' : '' }}">
                <div class="d-flex justify-content-between">
                    <div class="notification-title text-white">
                        @php
                        $maker = $notificationMakers[$notification->maker_id] ?? null;
                        $position = $maker->position ?? 'Unknown';
                        $department = $maker->department ?? 'Unknown';

                        $displayTitle = ($position == $department) ? $position : "$position $department";
                        @endphp
                        From: {{ $maker->name ?? 'System' }} ({{ $displayTitle }})
                    </div>
                    <div class="notification-time">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</div>
                </div>
                <div class="notification-content">{{ $notification->message }}</div>
                <div class="d-flex justify-content-end mt-2">
                    @if($notification->type == 'elearning_duty')
                    <a href="{{ url('/elearning/index') }}" class="btn btn-sm btn-primary me-2">
                        <i class="fas fa-arrow-right"></i> View
                    </a>
                    @endif

                    @if($notification->status == 'Unread')
                    <button class="btn btn-sm btn-danger mark-read" data-id="{{ $notification->id }}">
                        Mark as Read
                    </button>
                    @endif
                </div>
            </div>
            @endforeach

            <!-- Tampilkan tombol navigasi pagination -->
            <div class="d-flex justify-content-center mt-5">
                <ul class="pagination pagination-custom">
                    {{-- Tombol "Previous" --}}
                    @if ($notifications->onFirstPage())
                    <li class="page-item disabled mt-1">
                        <span class="page-link"><i class="fas fa-angle-left"></i></span>
                    </li>
                    @else
                    <li class="page-item mt-1">
                        <a class="page-link" href="{{ $notifications->previousPageUrl() }}">
                            <i class="fas fa-angle-left"></i>
                        </a>
                    </li>
                    @endif

                    {{-- Nomor Halaman --}}
                    @foreach ($notifications->getUrlRange(1, $notifications->lastPage()) as $page => $url)
                    <li class="page-item {{ $page == $notifications->currentPage() ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                    @endforeach

                    {{-- Tombol "Next" --}}
                    @if ($notifications->hasMorePages())
                    <li class="page-item mt-1">
                        <a class="page-link" href="{{ $notifications->nextPageUrl() }}">
                            <i class="fas fa-angle-right"></i>
                        </a>
                    </li>
                    @else
                    <li class="page-item disabled mt-1">
                        <span class="page-link"><i class="fas fa-angle-right"></i></span>
                    </li>
                    @endif
                </ul>
            </div>



            @else
            <div class="text-center text-white p-4">No notifications yet</div>
            @endif
        </div>

    </div>
</div>
@endsection
<!-- Core Scripts -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Mark all as read
        $('#markAllRead').click(function() {
            console.log('asdasd');
            $.ajax({
                url: "/notification/mark-all-read",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'All notifications marked as read',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    window.location.reload();
                }
            });
        });

        // Mark individual notification as read
        $('.mark-read').click(function() {
            let notificationId = $(this).data('id');

            $.ajax({
                url: "/notification/mark-read/" + notificationId,
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Notification marked as read',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    window.location.reload();
                }
            });
        });
    });
</script>