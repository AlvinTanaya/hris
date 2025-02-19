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

    <div class="text-center">
        <a href="{{ route('elearning.elearning_quiz', $task->invitation_id) }}" class="btn btn-success btn-lg mt-3 px-4 shadow-sm">
            <i class="fas fa-play"></i> Start Quiz
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script>
    const url = "{{ asset('storage/' . $task->lesson_file) }}";

    pdfjsLib.getDocument(url).promise.then(pdf => {
        const pdfContainer = document.getElementById('pdf-pages');
        for (let i = 1; i <= pdf.numPages; i++) {
            pdf.getPage(i).then(page => {
                let canvas = document.createElement("canvas");
                let context = canvas.getContext('2d');
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
                    let div = document.createElement('div');
                    div.className = 'carousel-item' + (i === 1 ? ' active' : '');
                    div.appendChild(canvas);
                    pdfContainer.appendChild(div);
                });
            });
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

    .btn-success:hover {
        transform: scale(1.05);
    }

    canvas {
        width: 100% !important;
        height: auto !important;
        max-height: 450px;
    }
</style>
@endpush