@extends('layouts.app')
@section('content')
<div class="container" id="welcome">
    @if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
    @endif
    <div class="welcome-container">
        <div class="content-wrapper">
            <!-- <img src="{{ asset('storage/logoTimurJayaIndosteel.png') }}" alt="Logo PT Timur Jaya Indosteel" class="company-logo"> -->
            <div class="icon-group">
                <span class="dynamic-icon"></span>
            </div>
            <div class="header-content">

                <h1 id="welcome-text"><span class="typing-text"></span></h1>
            </div>

        </div>
    </div>
</div>
<style>
    #welcome {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        /* Ubah ke flex-start untuk posisi atas */
        padding-top: 6rem;
        /* Tambahkan padding-top untuk memberi jarak dari atas */
        font-family: 'Poppins', sans-serif;
        text-align: center;
        overflow: hidden;
    }

    .welcome-container {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        /* Ubah ke flex-start untuk posisi atas */
        width: 100%;
        height: auto;
        padding-top: 10vh;
        /* Ubah ke auto agar tidak memenuhi seluruh tinggi layar */
    }

    .content-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        /* Ubah ke flex-start untuk posisi atas */
        gap: 2rem;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .company-logo {
        max-width: 400px;
        animation: float 3s ease-in-out infinite;
    }

    #welcome-text {
        font-size: 2rem;
        font-weight: bold;
        margin: 0;
        color: white;
    }

    .icon-group {
        margin-top: 1rem;
    }

    .dynamic-icon {
        font-size: 3.5rem;
        color: white;
        animation: fadeInOut 1s ease-in-out;
    }

    .typing-text::after {
        content: '|';
        animation: blink 1s infinite;
    }

    @keyframes float {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }

        100% {
            transform: translateY(0px);
        }
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0;
        }
    }

    @keyframes fadeInOut {
        0% {
            opacity: 0;
            transform: scale(0.8);
        }

        50% {
            opacity: 1;
            transform: scale(1.1);
        }

        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            gap: 1rem;
        }

        #welcome-text {
            font-size: 1.5rem;
        }

        .company-logo {
            max-width: 80px;
        }

        .dynamic-icon {
            font-size: 2.5rem;
        }
    }
</style>
@endsection
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Array of texts to cycle through
        const texts = [
            "Welcome to PT Timur Jaya Indosteel",
            "Your Steel Solutions Partner",
            "Excellence in Steel Industry",
            "Quality & Reliability",
            "Innovation in Steel",
            "Building the Future Together"
        ];

        // Array of icons to cycle through (Font Awesome classes)
        const icons = [
            'fas fa-industry',
            'fas fa-cogs',
            'fas fa-tools',
            'fas fa-hard-hat',
            'fas fa-building',
            'fas fa-chart-line',
            'fas fa-users',
            'fas fa-handshake',
            'fas fa-award',
            'fas fa-shield-alt'
        ];

        let textIndex = 0;
        let charIndex = 0;
        let isDeleting = false;
        let iconIndex = 0;
        const typingSpeed = 100;
        const deletingSpeed = 50;
        const pauseTime = 2000;

        function typeText() {
            const currentText = texts[textIndex];
            const typingElement = document.querySelector('.typing-text');

            if (isDeleting) {
                typingElement.textContent = currentText.substring(0, charIndex - 1);
                charIndex--;

                if (charIndex === 0) {
                    isDeleting = false;
                    textIndex = (textIndex + 1) % texts.length;
                    setTimeout(typeText, pauseTime / 2);
                    return;
                }
            } else {
                typingElement.textContent = currentText.substring(0, charIndex + 1);
                charIndex++;

                if (charIndex === currentText.length) {
                    isDeleting = true;
                    setTimeout(typeText, pauseTime);
                    return;
                }
            }

            setTimeout(typeText, isDeleting ? deletingSpeed : typingSpeed);
        }

        function cycleIcons() {
            const iconElement = document.querySelector('.dynamic-icon');

            // Remove all existing classes
            iconElement.className = 'dynamic-icon';

            // Add the new icon class
            iconElement.classList.add(...icons[iconIndex].split(' '));

            // Update index for next icon
            iconIndex = (iconIndex + 1) % icons.length;

            // Schedule next icon change
            setTimeout(cycleIcons, 3000);
        }

        // Start animations
        typeText();
        cycleIcons();
    });
</script>
@endpush