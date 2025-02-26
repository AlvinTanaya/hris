@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center text-warning mb-5 mt-4">
        <i class="fas fa-columns"></i> {{ __('Dashboard') }}
    </h1>

    @if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
    @endif

    <!-- Stats Cards Row -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 bg-gradient-primary text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Pegawai</h5>
                    <h2 class="fw-bold">{{ $totalUsers }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 bg-gradient-success text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Rata-rata Umur</h5>
                    <h2 class="fw-bold">{{ round($avgAge) }} Tahun</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mt-3">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-center">Distribusi Generasi</h5>
                    <canvas id="generationChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-center">Distribusi Gender</h5>
                    <canvas id="genderChart"></canvas>
                </div>
            </div>
        </div>
    </div>




    <!-- Employee Alerts -->
    <div class="row g-4 mt-3">
        <div class="col-md-6">
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-user-clock"></i> Pegawai 55+ Tahun</h5>

                    @if (in_array(Auth::user()->department, ['Human Resources', 'Director', 'General Manager']) && Auth::user()->position != 'staff')
                    <!-- Button to User Index -->
                    <a href="{{ route('user.index') }}" class="btn btn-danger">
                        <i class="fa-solid fa-arrow-right"></i> Employee
                    </a>
                    @endif
                </div>

                <div class="card-body">
                    @if(count($olderEmployees) > 0)
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th class="text-center">Usia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($olderEmployees as $employee)
                            <tr>
                                <td>{{ $employee->name }}</td>
                                <td class="text-center">{{ $employee->age }} Tahun</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="text-center">Tidak ada pegawai berusia 55 tahun atau lebih.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-warning">


                <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-calendar-times"></i> Kontrak Berakhir</h5>

                    @if (in_array(Auth::user()->department, ['Human Resources', 'Director', 'General Manager']) && Auth::user()->position != 'staff')
                    <!-- Button to User Index -->
                    <a href="{{ route('user.index') }}" class="btn btn-warning text-white">
                        <i class="fa-solid fa-arrow-right"></i> Employee
                    </a>
                    @endif
                </div>
                <div class="card-body">
                    @if(count($contractEndingSoon) > 0)
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th class="text-center">Tanggal Berakhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contractEndingSoon as $employee)
                            <tr>
                                <td>{{ $employee->name }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($employee->contract_end_date)->format('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="text-center">Tidak ada kontrak berakhir dalam 2 bulan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>






</div>

<style>
    /* Global Styling */
    body {
        background-color: #f4f6f9;
    }

    .container {
        padding-top: 30px;
    }

    /* Dashboard Title */
    .text-warning {
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    /* Stats Cards */
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
    }

    /* Gradient Backgrounds */
    .bg-gradient-primary {
        background: linear-gradient(145deg, #6a11cb 0%, #2575fc 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(145deg, #56ab2f 0%, #a8e063 100%);
    }

    /* Card Headers */
    .card-header {
        padding: 15px;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }

    /* Typography */
    .card-title {
        font-weight: 600;
        opacity: 0.8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .card-body h2 {
        color: white;
        font-size: 2.2rem;
        font-weight: 700;
    }

    /* Tables */
    .table {
        border-radius: 10px;
        overflow: hidden;
    }

    .table thead {
        background-color: #f8f9fc;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.02);
    }

    /* Alert Styles */
    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
        border-radius: 10px;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .card {
            margin-bottom: 20px;
        }
    }

    /* Chart Specific */
    .card-body canvas {
        max-height: 300px;
        width: 100%;
    }

    /* Icons */
    .card-header i {
        margin-right: 10px;
        opacity: 0.7;
    }
</style>
@endsection

@push('scripts')

<script>
    $(document).ready(function() {
        // Data dari Blade
        const generasiData = @json($generasiData);
        const genderData = @json($genderData);

        console.log("Generasi Data:", generasiData);
        console.log("Gender Data:", genderData);

        const allGenerations = ["Gen Z", "Millennials", "Gen X", "Boomers"];
        const generasiLabels = allGenerations;

        const generasiCounts = generasiLabels.map(gen => {
            const found = generasiData.find(item => item.generasi === gen);
            return found ? found.total : 0;
        });

        // Cek apakah canvas ada sebelum membuat chart
        if ($("#generationChart").length) {
            const genCtx = $("#generationChart")[0].getContext('2d');
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
            console.warn('Canvas dengan ID "generationChart" tidak ditemukan.');
        }

        // Gender Chart (Pie Chart)
        if ($("#genderChart").length) {
            setTimeout(function() {
                const genderCtx = $("#genderChart")[0].getContext('2d');
                new Chart(genderCtx, {
                    type: 'pie',
                    data: {
                        labels: genderData.map(item => item.gender),
                        datasets: [{
                            data: genderData.map(item => item.total),
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.8)', // Male
                                'rgba(255, 99, 132, 0.8)' // Female
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
            console.warn('Canvas dengan ID "genderChart" tidak ditemukan.');
        }
    });
</script>
@endpush