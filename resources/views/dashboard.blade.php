@extends('layouts.app')

@section('content')
<div class="container">

    <h1 class="text-center text-warning" style="margin-bottom: 60px; margin-top:25px"><i class="fas fa-columns"></i> {{ __('Dashboard') }}</h1>

    @if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
    @endif

    <!-- Stats Cards Row -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Pegawai</h5>
                    <h2 class="display-4">{{ $totalUsers }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Rata-rata Umur</h5>
                    <h2 class="display-4">{{ round($avgAge) }} Tahun</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Distribusi Generasi</h5>
                    <div class="d-flex justify-content-center">
                        <canvas id="generationChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Distribusi Gender</h5>
                    <div class="d-flex justify-content-center">
                        <canvas id="genderChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<style>
    .bg-primary {
        background: linear-gradient(45deg, #4e73df, #224abe) !important;
    }

    .bg-success {
        background: linear-gradient(45deg, #1cc88a, #13855c) !important;
    }

    .display-4 {
        font-size: 2.5rem;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .display-4 {
            font-size: 2rem;
        }
    }

    canvas {
        width: 100% !important;
        /* Membuat chart responsif */
        max-width: 500px;
        /* Batas lebar maksimal */
        max-height: 300px;
        /* Batas tinggi maksimal */
        height: auto !important;
        /* Menyesuaikan tinggi sesuai dengan lebar */
    }
</style>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data yang di-passing dari Blade
        const generasiData = @json($generasiData);
        const genderData = @json($genderData);

        // Cek data yang diterima dari PHP
        console.log("Generasi Data:", generasiData);
        console.log("Gender Data:", genderData);

        // Mencari generasi yang terdaftar
        const allGenerations = ["Gen Z", "Millennials", "Gen X", "Boomers"]; // Tentukan semua generasi yang ingin ditampilkan
        const generasiLabels = allGenerations;

        // Menyesuaikan data untuk setiap generasi
        const generasiCounts = generasiLabels.map(gen => {
            const found = generasiData.find(item => item.generasi === gen);
            return found ? found.total : 0; // Jika tidak ditemukan, totalnya 0
        });

        // Pastikan elemen canvas ada sebelum membuat chart
        const genCanvas = document.getElementById('generationChart');
        console.log('Canvas untuk Generation Chart:', genCanvas);

        if (genCanvas && genCanvas.getContext) {
            const genCtx = genCanvas.getContext('2d');
            new Chart(genCtx, {
                type: 'bar',
                data: {
                    labels: generasiLabels,
                    datasets: [{
                        label: 'Jumlah Pegawai',
                        data: generasiCounts,
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 159, 64, 0.8)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        } else {
            console.warn('Canvas dengan ID "generationChart" tidak ditemukan atau bukan canvas.');
        }

        // Gender Chart (Pie Chart)
        const genderCanvas = document.getElementById('genderChart');
        console.log('Canvas untuk Gender Chart:', genderCanvas);

        if (genderCanvas && genderCanvas.getContext) {
            setTimeout(function() {
                const genderCtx = genderCanvas.getContext('2d');
                new Chart(genderCtx, {
                    type: 'pie',
                    data: {
                        labels: genderData.map(item => item.jenis_kelamin),
                        datasets: [{
                            data: genderData.map(item => item.total),
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.8)', // Laki-laki
                                'rgba(255, 99, 132, 0.8)' // Perempuan
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }, 500);
        } else {
            console.warn('Canvas dengan ID "genderChart" tidak ditemukan atau bukan canvas.');
        }
    });
</script>
@endpush