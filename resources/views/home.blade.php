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
            <div class="icon-container">
                <div class="icon-group">
                    <span class="dynamic-icon"></span>
                </div>
            </div>
            <div class="header-content">
                <h1 id="welcome-text"><span class="typing-text"></span></h1>
            </div>
            <div class="shape shape1"></div>
            <div class="shape shape2"></div>
            <div class="shape shape3"></div>
        </div>
    </div>
</div>
<style>
    #welcome {
        display: flex;
        justify-content: center;
        align-items: center;
        padding-top: 0;
        font-family: 'Poppins', sans-serif;
        text-align: center;
        overflow: hidden;
        position: relative;
        height: calc(100vh - 100px);
        width: 100%;
        max-width: 100%;
    }

    .welcome-container {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;
        position: relative;
        z-index: 2;
    }

    .content-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 2rem;
        padding: 2.5rem;
        background: rgba(27, 42, 78, 0.8);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(8px);
        width: 80%;
        max-width: 700px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .icon-container {
        background-color: rgba(255, 255, 255, 0.1);
        padding: 1.5rem;
        border-radius: 50%;
        margin-bottom: 0.5rem;
    }

    .header-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }

    #welcome-text {
        font-size: 2.5rem; /* Ukuran teks dikurangi dari 4rem ke 2.5rem */
        font-weight: 700;
        margin: 0;
        color: white;
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.4);
        letter-spacing: 0.5px;
    }

    .icon-group {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .dynamic-icon {
        font-size: 3.5rem; /* Ukuran icon dikurangi dari 5rem ke 3.5rem */
        color: white;
        text-shadow: 0 0 15px rgba(255, 255, 255, 0.5);
    }

    .typing-text {
        display: inline-block;
        min-height: 60px; /* Tinggi dikurangi dari 100px ke 60px */
    }

    .typing-text::after {
        content: '|';
        animation: blink 1s infinite;
    }

    .shape {
        position: absolute;
        opacity: 0.2;
        z-index: -1;
        border-radius: 50%;
    }

    .shape1 {
        width: 180px;
        height: 180px;
        background-color: #5472d2;
        top: -50px;
        right: -50px;
    }

    .shape2 {
        width: 130px;
        height: 130px;
        background-color: #3e6fd1;
        bottom: -40px;
        left: -40px;
    }

    .shape3 {
        width: 90px;
        height: 90px;
        background-color: #6b90e5;
        top: 50%;
        right: 20%;
    }

    @keyframes blink {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0;
        }
    }

    @media (max-width: 768px) {
        #welcome-text {
            font-size: 2rem;
        }

        .dynamic-icon {
            font-size: 3rem;
        }
        
        .content-wrapper {
            padding: 1.5rem;
            width: 90%;
        }
        
        .icon-container {
            padding: 1.2rem;
        }
    }
    
    @media (max-width: 480px) {
        #welcome-text {
            font-size: 1.8rem;
        }
        
        .dynamic-icon {
            font-size: 2.5rem;
        }
        
        .content-wrapper {
            padding: 1.2rem;
            width: 95%;
        }
    }
</style>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
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
        const typingSpeed = 80; // Sedikit lebih cepat
        const deletingSpeed = 40;
        const pauseTime = 2000;

        function typeText() {
            const currentText = texts[textIndex];
            const typingElement = $('.typing-text');

            if (isDeleting) {
                typingElement.text(currentText.substring(0, charIndex - 1));
                charIndex--;

                if (charIndex === 0) {
                    isDeleting = false;
                    textIndex = (textIndex + 1) % texts.length;
                    setTimeout(typeText, pauseTime / 2);
                    return;
                }
            } else {
                typingElement.text(currentText.substring(0, charIndex + 1));
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
            const iconElement = $('.dynamic-icon');

            // Remove all existing classes except dynamic-icon
            iconElement.attr('class', 'dynamic-icon');

            // Add the new icon class
            iconElement.addClass(icons[iconIndex]);

            // Add a fade effect
            iconElement.css('opacity', 0).animate({opacity: 1}, 500);

            // Update index for next icon
            iconIndex = (iconIndex + 1) % icons.length;

            // Schedule next icon change
            setTimeout(cycleIcons, 3000);
        }

        // Animate shapes subtly
        function animateShapes() {
            $('.shape1').animate({
                top: '-30px',
                right: '-30px'
            }, 5000, function() {
                $(this).animate({
                    top: '-50px',
                    right: '-50px'
                }, 5000, animateShapes);
            });
            
            $('.shape2').animate({
                bottom: '-20px',
                left: '-20px'
            }, 4000, function() {
                $(this).animate({
                    bottom: '-40px',
                    left: '-40px'
                }, 4000);
            });
            
            $('.shape3').animate({
                top: '45%',
                right: '25%'
            }, 6000, function() {
                $(this).animate({
                    top: '50%',
                    right: '20%'
                }, 6000);
            });
        }

        // Start animations
        typeText();
        cycleIcons();
        animateShapes();
        
        // Add subtle parallax effect with jQuery
        $(document).mousemove(function(e) {
            const x = e.pageX / $(window).width();
            const y = e.pageY / $(window).height();
            
            $('.shape1').css({
                'transform': 'translate(' + (x * 15) + 'px, ' + (y * 15) + 'px)'
            });
            
            $('.shape2').css({
                'transform': 'translate(' + (x * -10) + 'px, ' + (y * -10) + 'px)'
            });
            
            $('.shape3').css({
                'transform': 'translate(' + (x * 8) + 'px, ' + (y * 8) + 'px)'
            });
        });
    });
</script>
@endpush