@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ route('elearning.index2', $id ) }}" class="btn btn-danger px-4 shadow-sm d-flex align-items-center rounded-pill">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
                <h1 class="text-center text-warning fw-bold mb-0">
                    <i class="far fa-sticky-note me-2"></i>E-learning Material
                </h1>
                <div style="width: 115px;"></div> <!-- Spacer to balance the header -->
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-4">
                    <h5 class="card-title text-center text-primary fw-bold mb-0">
                        <i class="fas fa-book-reader me-2"></i>Learning Materials
                    </h5>
                </div>
                <div class="card-body text-center p-0">
                    <!-- PDF Viewer as Carousel -->
                    <div id="pdf-carousel" class="carousel slide" data-bs-ride="false">
                        <div class="carousel-inner" id="pdf-pages" style=" overflow: hidden;"></div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#pdf-carousel" data-bs-slide="prev">
                            <div class="d-flex justify-content-center align-items-center bg-dark bg-opacity-50 rounded-circle" style="width: 40px; height: 40px;">
                                <span class="carousel-control-prev-icon small-icon"></span>
                            </div>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#pdf-carousel" data-bs-slide="next">
                            <div class="d-flex justify-content-center align-items-center bg-dark bg-opacity-50 rounded-circle" style="width: 40px; height: 40px;">
                                <span class="carousel-control-next-icon small-icon"></span>
                            </div>
                        </button>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pb-4">
                    <!-- Progress and Notification -->
                    <div class="alert alert-info rounded-3 shadow-sm" id="reading-notification">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle fs-4 me-3"></i>
                            <span><strong>Note:</strong> You must read every single page before starting the quiz.</span>
                        </div>
                    </div>
                    
                    <div class="progress mt-3 mb-4" style="height: 12px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" id="reading-progress" role="progressbar" 
                            style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                    </div>
                    
                    <div class="text-center">
                        <a href="{{ route('elearning.elearning_quiz', $task->invitation_id) }}"
                            class="btn btn-success btn-lg px-5 py-3 shadow disabled rounded-pill"
                            id="start-quiz-btn">
                            <i class="fas fa-play me-2"></i> Start Quiz
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
    /* Improved styling */
    body {
        background-color: #f8f9fa;
        color: #343a40;
    }
    
    .small-icon {
        width: 24px;
        height: 24px;
    }

    .btn-danger {
        background-color: #dc3545;
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-danger:hover {
        background-color: #c82333;
        transform: translateX(-5px);
    }

    .btn-success {
        background: linear-gradient(145deg, #28a745, #218838);
        border: none;
        transition: all 0.3s ease;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .btn-success:hover:not(.disabled) {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3) !important;
    }
    
    .btn-success.disabled {
        background: linear-gradient(145deg, #6c757d, #5a6268);
        opacity: 0.7;
    }

    canvas {
        width: 100% !important;
        height: auto !important;

        border-radius: 5px;
    }

    .progress {
        height: 12px;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        background-color: #e9ecef;
    }
    
    .progress-bar {
        border-radius: 20px;
        transition: width 0.5s ease;
    }

    .alert {
        border-radius: 12px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        border: none;
        padding: 16px;
    }
    
    .card {
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
    }
    
    .text-warning {
        color: #ffc107 !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }
    
    /* Carousel navigation styling */
    .carousel-control-prev, .carousel-control-next {
        width: 10%;
        opacity: 0.7;
    }
    
    .carousel-control-prev:hover, .carousel-control-next:hover {
        opacity: 1;
    }
    
    /* PDF Page counter */
    .page-counter {
        position: absolute;
        bottom: 15px;
        right: 15px;
        background: rgba(0,0,0,0.6);
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 14px;
        z-index: 10;
    }
</style>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script>
    $(document).ready(function() {
        const url = "{{ asset('storage/' . $task->lesson_file) }}";
        console.log(url);

        let totalPages = 0;
        let currentIndex = 0; // Indeks awal pada halaman pertama
        const $pdfContainer = $("#pdf-pages");
        const $prevButton = $(".carousel-control-prev");
        const $nextButton = $(".carousel-control-next");
        const $startQuizButton = $("#start-quiz-btn");
        const $readingProgress = $("#reading-progress");
        const $readingNotification = $("#reading-notification");

        // Nonaktifkan tombol quiz di awal
        $startQuizButton.prop("disabled", true);
        $startQuizButton.addClass("disabled");

        // Add page counter
        const $pageCounter = $('<div class="page-counter"></div>');
        $("#pdf-carousel").append($pageCounter);

        pdfjsLib.getDocument(url).promise.then(pdf => {
            totalPages = pdf.numPages;
            $pageCounter.text(`Page 1 of ${totalPages}`);

            for (let i = 1; i <= totalPages; i++) {
                pdf.getPage(i).then(page => {
                    let canvas = document.createElement("canvas");
                    let context = canvas.getContext("2d");
                    let viewport = page.getViewport({
                        scale: 1.5
                    });
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;

                    let renderContext = {
                        canvasContext: context,
                        viewport: viewport
                    };
                    page.render(renderContext).promise.then(() => {
                        let div = document.createElement("div");
                        div.className = "carousel-item" + (i === 1 ? " active" : "");
                        div.appendChild(canvas);
                        $pdfContainer.append(div);

                        // Setelah semua halaman di-load, jalankan event handler
                        if (i === totalPages) {
                            setupCarousel();
                        }
                    });
                });
            }
        });

        function setupCarousel() {
            updateButtons();

            $("#pdf-carousel").on("slid.bs.carousel", function(event) {
                currentIndex = $(this).find(".carousel-item.active").index();
                updateButtons();
                updateProgress();
                $pageCounter.text(`Page ${currentIndex + 1} of ${totalPages}`);
            });
        }

        function updateProgress() {
            // Update progress bar with animation
            let progressPercent = Math.round((currentIndex + 1) / totalPages * 100);
            $readingProgress.css("width", progressPercent + "%");
            $readingProgress.attr("aria-valuenow", progressPercent);
            $readingProgress.text(progressPercent + "%");
        }

        function updateButtons() {
            // Cek jika di slide pertama, sembunyikan tombol "prev"
            $prevButton.toggle(currentIndex !== 0);

            // Cek jika di slide terakhir, sembunyikan tombol "next"
            $nextButton.toggle(currentIndex !== totalPages - 1);

            // Aktifkan tombol "Start Quiz" hanya di halaman terakhir
            if (currentIndex === totalPages - 1) {
                $startQuizButton.removeClass("disabled");
                $startQuizButton.prop("disabled", false);
                $readingNotification.removeClass("alert-info").addClass("alert-success");
                $readingNotification.html('<div class="d-flex align-items-center"><i class="fas fa-check-circle fs-4 me-3"></i><span><strong>Great job!</strong> You have completed reading all pages. You can now start the quiz.</span></div>');
                
                // Add celebration animation
                $startQuizButton.addClass("animate__animated animate__pulse animate__infinite");
            } else {
                $startQuizButton.addClass("disabled");
                $startQuizButton.prop("disabled", true);
                $readingNotification.removeClass("alert-success").addClass("alert-info");
                $readingNotification.html('<div class="d-flex align-items-center"><i class="fas fa-info-circle fs-4 me-3"></i><span><strong>Note:</strong> You must read every single page before starting the quiz.</span></div>');
                $startQuizButton.removeClass("animate__animated animate__pulse animate__infinite");
            }
        }
    });
</script>
@endpush