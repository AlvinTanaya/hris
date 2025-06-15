@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<style>
    /* Base Styles */
    .test {
        --primary-color: #4e73df;
        --primary-light: rgba(78, 115, 223, 0.1);
        --secondary-color: #1cc88a;
        --info-color: #36b9cc;
        --warning-color: #f6c23e;
        --danger-color: #e74a3b;
        --light-bg: #f8f9fc;
        --dark-color: #5a5c69;
        --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    
    body {
        background-color: #f5f7fb;
        color: #4a4a4a;
    }
    
    .card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        transition: var(--transition);
        overflow: hidden;
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.25rem 1.5rem;
        background-color: #fff;
    }
    
    /* Card Hover Effect */
    .card-hover {
        transition: var(--transition);
        position: relative;
    }
    
    .card-hover:hover {
        transform: translateY(-8px);
        box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2);
    }
    
    .card-hover::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.15);
        opacity: 0;
        transition: var(--transition);
        z-index: -1;
    }
    
    .card-hover:hover::after {
        opacity: 1;
    }
    
    /* Border Accents */
    .border-start-4 {
        border-left-width: 4px !important;
        border-left-style: solid !important;
    }
    
    /* Progress Bars */
    .progress {
        background-color: #f0f2f5;
        border-radius: 0.375rem;
        height: 0.5rem;
    }
    
    .progress-bar {
        border-radius: 0.375rem;
        transition: width 0.6s ease;
    }
    
    /* Table Styling */
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table thead th {
        background-color: var(--light-bg);
        color: var(--dark-color);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-bottom: none;
        padding: 1rem 1.25rem;
    }
    
    .table tbody td {
        padding: 1rem 1.25rem;
        vertical-align: middle;
        border-top: 1px solid rgba(0, 0, 0, 0.03);
    }
    
    .table-hover tbody tr {
        transition: var(--transition);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.03);
        transform: scale(1.005);
    }
    
    /* Badges */
    .badge {
        padding: 0.5em 0.75em;
        font-weight: 600;
        letter-spacing: 0.5px;
        border-radius: 0.375rem;
    }
    
    /* Buttons */
    .btn {
        border-radius: 0.375rem;
        padding: 0.5rem 1.25rem;
        font-weight: 500;
        transition: var(--transition);
        letter-spacing: 0.5px;
    }
    
    .btn-light {
        background-color: #fff;
        border-color: rgba(0, 0, 0, 0.1);
    }
    
    .btn-light:hover {
        background-color: #f8f9fa;
        border-color: rgba(0, 0, 0, 0.15);
    }
    
    /* Header Gradient */
    .bg-gradient-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
    }
    
    /* Chart Containers */
    .chart-area, .chart-pie {
        position: relative;
    }
    
    /* Custom Shadows */
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
    
    /* Animation Effects */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .card-header {
            padding: 1rem;
        }
        
        .table thead th,
        .table tbody td {
            padding: 0.75rem;
        }
        
        .chart-area {
            height: 250px !important;
        }
    }
    
    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* Floating Animation for Cards */
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
    
    .floating-card {
        animation: float 6s ease-in-out infinite;
    }
    
    /* Delay animations for staggered effect */
    .card:nth-child(1) { animation-delay: 0s; }
    .card:nth-child(2) { animation-delay: 0.2s; }
    .card:nth-child(3) { animation-delay: 0.4s; }
    .card:nth-child(4) { animation-delay: 0.6s; }
    
    /* Glow Effect for Important Elements */
    .glow-on-hover:hover {
        box-shadow: 0 0 15px rgba(78, 115, 223, 0.4);
    }
    
    /* Custom Checkbox */
    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    /* Tooltip Customization */
    .tooltip-inner {
        background-color: var(--dark-color);
        border-radius: 0.25rem;
        padding: 0.5rem 1rem;
    }
    
    .bs-tooltip-auto[data-popper-placement^=top] .tooltip-arrow::before,
    .bs-tooltip-top .tooltip-arrow::before {
        border-top-color: var(--dark-color);
    }
</style>

<div class="container-fluid py-4 test">
    <!-- Header Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header  bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Employee Performance Evaluation Report</h4>
                        <button class="btn btn-light btn-sm" id="printReport">
                            <i class="bi bi-printer me-1"></i> Print Report
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="text-primary">Welcome to your performance dashboard</h5>
                            <p class="text-muted mb-0">Track your performance evaluations over time and identify areas for improvement.</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <div class="badge bg-primary bg-opacity-10 text-primary p-2">
                                <i class="bi bi-calendar me-1"></i> Last updated: {{ now()->format('M d, Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Summary Cards -->
    <div class="row mb-4 g-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-hover border-start border-start-4 border-start-primary shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Latest Score</h6>
                            <h3 class="mb-0">{{ $evaluations->last() ? $evaluations->last()->final_score : 'N/A' }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="bi bi-clipboard-data text-primary fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-hover border-start border-start-4 border-start-success shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Latest Grade</h6>
                            <h3 class="mb-0">{{ $evaluations->last() ? $evaluations->last()->final_grade : 'N/A' }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="bi bi-award text-success fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-hover border-start border-start-4 border-start-info shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Performance Score</h6>
                            <h3 class="mb-0">{{ $evaluations->last() ? $evaluations->last()->performance_score : 'N/A' }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="bi bi-graph-up text-info fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-hover border-start border-start-4 border-start-warning shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Evaluation Years</h6>
                            <h3 class="mb-0">{{ $evaluations->count() }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="bi bi-calendar-week text-warning fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4 g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0 text-primary">Performance History</h5>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 300px;">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0 text-primary">Latest Evaluation Breakdown</h5>
                </div>
                <div class="card-body">
                    <div class="chart-pie" style="height: 250px;">
                        <canvas id="evaluationPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="me-3">
                            <i class="bi bi-circle-fill text-primary me-1"></i> Performance
                        </span>
                        <span class="me-3">
                            <i class="bi bi-circle-fill text-success me-1"></i> Discipline
                        </span>
                        <span>
                            <i class="bi bi-circle-fill text-info me-1"></i> E-Learning
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Table Card -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary">
                    <h5 class="mb-0 text-white">Evaluation History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="evaluationTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Year</th>
                                    <th>Performance</th>
                                    <th>Performance Score</th>
                                    <th>Discipline</th>
                                    <th>Discipline Score</th>
                                    <th>E-Learning</th>
                                    <th>E-Learning Score</th>
                                    <th>Final Score</th>
                                    <th>Final Grade</th>
                                    <th>Proposal Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($evaluations->sortByDesc('year') as $evaluation)
                                <tr>
                                    <td class="fw-bold">{{ $evaluation->year }}</td>
                                    <td>{{ $evaluation->performance }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                <div class="progress-bar bg-primary" role="progressbar"
                                                    style="width: {{ ($evaluation->performance_score / 5) * 100 }}%"
                                                    aria-valuenow="{{ $evaluation->performance_score }}"
                                                    aria-valuemin="0"
                                                    aria-valuemax="5"></div>
                                            </div>
                                            <span class="text-nowrap">{{ number_format($evaluation->performance_score, 2) }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $evaluation->discipline }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                    style="width: {{ ($evaluation->discipline_score / 5) * 100 }}%"
                                                    aria-valuenow="{{ $evaluation->discipline_score }}"
                                                    aria-valuemin="0"
                                                    aria-valuemax="5"></div>
                                            </div>
                                            <span class="text-nowrap">{{ number_format($evaluation->discipline_score, 2) }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $evaluation->elearning }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                <div class="progress-bar bg-info" role="progressbar"
                                                    style="width: {{ ($evaluation->elearning_score / 5) * 100 }}%"
                                                    aria-valuenow="{{ $evaluation->elearning_score }}"
                                                    aria-valuemin="0"
                                                    aria-valuemax="5"></div>
                                            </div>
                                            <span class="text-nowrap">{{ number_format($evaluation->elearning_score, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="fw-bold">{{ number_format($evaluation->final_score, 2) }}</td>
                                    <td>
                                        @php
                                        $gradeClass = 'bg-secondary';
                                        if(in_array($evaluation->final_grade, ['A', 'A+'])) {
                                        $gradeClass = 'bg-success';
                                        } elseif(in_array($evaluation->final_grade, ['B+', 'B', 'B-'])) {
                                        $gradeClass = 'bg-primary';
                                        } elseif(in_array($evaluation->final_grade, ['C+', 'C', 'C-'])) {
                                        $gradeClass = 'bg-warning';
                                        } elseif(in_array($evaluation->final_grade, ['D', 'F'])) {
                                        $gradeClass = 'bg-danger';
                                        }
                                        @endphp
                                        <span class="badge {{ $gradeClass }}">{{ $evaluation->final_grade }}</span>
                                    </td>
                                    <td>
                                        @if($evaluation->proposal_grade)
                                        <span class="badge bg-info">{{ $evaluation->proposal_grade }}</span>
                                        @else
                                        <span class="badge bg-light text-dark">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection





@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable with Bootstrap 5 styling
        $('#evaluationTable').DataTable({
            order: [
                [0, 'desc']
            ],
            pageLength: 10,
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "All"]
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
            }
        });

        // Performance History Line Chart
        var performanceData = @json($performanceHistory);
        var years = performanceData.map(item => item.year);
        var performanceScores = performanceData.map(item => item.performance_score);
        var disciplineScores = performanceData.map(item => item.discipline_score);
        var elearningScores = performanceData.map(item => item.elearning_score);
        var finalScores = performanceData.map(item => item.final_score);

        var ctx = document.getElementById("performanceChart").getContext('2d');
        var performanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: years,
                datasets: [{
                        label: "Final Score",
                        borderColor: "#4e73df",
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        pointBackgroundColor: "#4e73df",
                        pointBorderColor: "#fff",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "#4e73df",
                        pointHoverBorderColor: "#fff",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        borderWidth: 2,
                        tension: 0.1,
                        data: finalScores,
                        fill: true
                    },
                    {
                        label: "Performance",
                        borderColor: "#1cc88a",
                        backgroundColor: "rgba(28, 200, 138, 0.05)",
                        pointBackgroundColor: "#1cc88a",
                        pointBorderColor: "#fff",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "#1cc88a",
                        pointHoverBorderColor: "#fff",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        borderWidth: 2,
                        tension: 0.1,
                        data: performanceScores,
                        fill: true
                    },
                    {
                        label: "Discipline",
                        borderColor: "#f6c23e",
                        backgroundColor: "rgba(246, 194, 62, 0.05)",
                        pointBackgroundColor: "#f6c23e",
                        pointBorderColor: "#fff",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "#f6c23e",
                        pointHoverBorderColor: "#fff",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        borderWidth: 2,
                        tension: 0.1,
                        data: disciplineScores,
                        fill: true
                    },
                    {
                        label: "E-Learning",
                        borderColor: "#36b9cc",
                        backgroundColor: "rgba(54, 185, 204, 0.05)",
                        pointBackgroundColor: "#36b9cc",
                        pointBorderColor: "#fff",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "#36b9cc",
                        pointHoverBorderColor: "#fff",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        borderWidth: 2,
                        tension: 0.1,
                        data: elearningScores,
                        fill: true
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: "#fff",
                        titleColor: "#6e707e",
                        bodyColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        intersect: false,
                        mode: 'index',
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxRotation: 0,
                            padding: 10
                        }
                    },
                    y: {
                        min: 0,
                        max: 5,
                        ticks: {
                            stepSize: 1,
                            padding: 10,
                            callback: function(value) {
                                return value.toFixed(1);
                            }
                        },
                        grid: {
                            color: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }
                }
            }
        });

        // Latest Evaluation Pie Chart
        var latestEvaluation = performanceData.length > 0 ? performanceData[performanceData.length - 1] : null;

        if (latestEvaluation) {
            var ctx2 = document.getElementById("evaluationPieChart").getContext('2d');
            var evaluationPieChart = new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ["Performance", "Discipline", "E-Learning"],
                    datasets: [{
                        data: [
                            latestEvaluation.performance_score,
                            latestEvaluation.discipline_score,
                            latestEvaluation.elearning_score
                        ],
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                        hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: "#fff",
                            bodyColor: "#858796",
                            borderColor: '#dddfeb',
                            borderWidth: 1,
                            padding: 12
                        }
                    }
                }
            });
        }

        // Print Report Button
        $('#printReport').click(function() {
            window.print();
        });
    });
</script>
@endpush