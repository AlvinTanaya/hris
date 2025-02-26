@extends('layouts.app')

@section('content')
<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px"><i class="far fa-sticky-note"></i> E-learning Material</h1>

<div class="container mt-4 mx-auto">

    <div class="d-flex justify-content-center">
        <div class="card shadow-lg p-3 mb-4 bg-white rounded" style="max-width: 800px; width: 100%;">
            <div class="card-body text-center">
                <!-- PDF Viewer as Carousel -->
                <div id="pdf-carousel" class="carousel slide" data-bs-ride="false">
                    <div class="carousel-inner" id="pdf-pages" style="max-height: 450px; overflow: hidden;"></div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#pdf-carousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon small-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#pdf-carousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon small-icon"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress and Notification -->
    <div class="text-center mt-2 mb-3">
        <div class="alert alert-info" id="reading-notification">
            <i class="fas fa-info-circle"></i> <strong>Note:</strong> You must read every single page before starting the quiz.
        </div>
        <div class="progress" style="height: 10px;">
            <div class="progress-bar bg-success" id="reading-progress" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
        </div>
    </div>

    <div class="text-center">
        <a href="{{ route('elearning.elearning_quiz', $task->invitation_id) }}"
            class="btn btn-success btn-lg mt-3 px-4 shadow-sm disabled"
            id="start-quiz-btn">
            <i class="fas fa-play"></i> Start Quiz
        </a>
    </div>

</div>
@endsection

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

        pdfjsLib.getDocument(url).promise.then(pdf => {
            totalPages = pdf.numPages;

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
            });
        }

        function updateProgress() {
            // Update progress bar
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
                $readingNotification.html('<i class="fas fa-check-circle"></i> <strong>Great job!</strong> You have completed reading all pages. You can now start the quiz.');
            } else {
                $startQuizButton.addClass("disabled");
                $startQuizButton.prop("disabled", true);
                $readingNotification.removeClass("alert-success").addClass("alert-info");
                $readingNotification.html('<i class="fas fa-info-circle"></i> <strong>Note:</strong> You must read every single page before starting the quiz.');
            }
        }
    });
</script>

<style>
    .small-icon {
        width: 32px;
        height: 32px;
    }

    .btn-success {
        transition: transform 0.2s ease-in-out;
    }

    .btn-success:hover:not(.disabled) {
        transform: scale(1.05);
    }

    canvas {
        width: 100% !important;
        height: auto !important;
        max-height: 450px;
    }

    .progress {
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .alert {
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush