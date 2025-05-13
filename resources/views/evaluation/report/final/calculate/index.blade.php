@extends('layouts.app')

@section('content')

<style>
    /* DataTable enhancements */
    #finalTable {
        width: 100% !important;
        /* Memastikan tabel menggunakan lebar penuh */
        margin: 0 !important;
        /* Menghilangkan margin default */
        border-collapse: separate;
        border-spacing: 0;
    }

    /* Membuat kolom # (nomor urut) lebih kecil */
    #finalTable th:first-child,
    #finalTable td:first-child {
        width: 40px !important;
        /* Lebar kolom # yang lebih kecil */
        min-width: 40px !important;
        max-width: 40px !important;
        text-align: center;
        padding: 12px 5px !important;
    }

    #finalTable thead th {
        position: sticky;
        top: 0;
        background: #f8f9fa;
        z-index: 10;
        padding: 15px 10px !important;
        /* Padding header yang lebih baik */
        text-align: center;
    }

    #finalTable tbody td {
        padding: 12px 10px !important;
        /* Padding sel data yang lebih baik */
        vertical-align: middle !important;
    }

    /* Container untuk tabel */
    .dataTables_wrapper {
        width: 100% !important;
        padding: 20px !important;
        /* Padding di sekitar tabel keseluruhan */
        box-sizing: border-box;
    }

    /* Membuat elemen pencarian lebih menarik */
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 0.5rem;
        padding: 8px 12px;
        border: 1px solid #dee2e6;
        width: 250px;
        /* Lebar yang lebih konsisten */
        margin-left: 10px;
    }

    /* Gaya untuk dropdown "Show entries" */
    .dataTables_wrapper .dataTables_length select {
        border-radius: 0.5rem;
        padding: 8px 30px 8px 10px;
        border: 1px solid #dee2e6;
        margin: 0 5px;
    }

    /* Meningkatkan tampilan pagination */
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 15px;
        padding-bottom: 5px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 8px 14px !important;
        margin: 0 3px;
        border-radius: 5px;
        border: 1px solid #dee2e6 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #0d6efd !important;
        color: white !important;
        border-color: #0d6efd !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #e9ecef !important;
        color: #212529 !important;
        border-color: #dee2e6 !important;
    }

    /* Gaya untuk baris tabel */
    .table tbody tr:nth-child(odd) {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .table tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
        transition: background-color 0.2s ease;
    }

    /* Gaya untuk info jumlah data dan tampilan pagination */
    .dataTables_info {
        padding-top: 15px !important;
        color: #6c757d;
    }

    /* Style untuk sel score */
    .table td .fw-bold {
        font-size: 1rem;
        font-weight: 600 !important;
    }

    .table td .text-muted.small {
        font-size: 0.8rem;
        display: block;
        margin-top: 3px;
    }

    /* Perbaikan untuk ukuran kolom agar tidak terlalu mepet */
    .table th,
    .table td {
        min-width: 80px;
        /* Minimal lebar kolom */
    }

    /* Memastikan responsivitas tabel */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }




    /* Score color classes */
    .bg-success {
        background-color: #28a745 !important;
    }

    .bg-primary {
        background-color: #007bff !important;
    }

    .bg-info {
        background-color: #17a2b8 !important;
    }

    .bg-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }

    .bg-danger {
        background-color: #dc3545 !important;
    }

    /* Highlight colors for different score types */
    .text-primary {
        color: #4d8bf8 !important;
    }

    .text-warning {
        color: #f1c40f !important;
    }

    .text-info {
        color: #1abc9c !important;
    }



    /* Enhanced General styles */
    body {
        background-color: #f8f9fa;
    }

    .card {
        border-radius: 0.75rem;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.07);
        border: none;
        overflow: hidden;
    }

    .card:hover {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    .card-header {
        border-top-left-radius: 0.75rem !important;
        border-top-right-radius: 0.75rem !important;
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
    }

    .card-header i {
        font-size: 1.5rem;
        margin-right: 0.75rem;
        opacity: 0.9;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    /* Improved Form elements */
    .form-control,
    .form-select {
        border-radius: 0.5rem;
        transition: all 0.2s ease-in-out;
        padding: 0.625rem 1rem;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }



    .input-group-text {
        border-radius: 0.5rem;
        background: linear-gradient(to right, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.6));
    }

    .btn {
        border-radius: 0.5rem;
        padding: 0.625rem 1.25rem;
        font-weight: 500;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        border: none;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4d8bf8, #2d6cdf);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #3a78e5, #1a59cc);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Enhanced avatar */
    .avatar {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1rem;
        border-radius: 50%;
        color: white;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.8);
    }

    /* Improved animation for calculation */
    @keyframes highlightAnimation {
        0% {
            background-color: rgba(13, 110, 253, 0.05);
            transform: scale(1);
        }

        50% {
            background-color: rgba(13, 110, 253, 0.2);
            transform: scale(1.05);
        }

        100% {
            background-color: transparent;
            transform: scale(1);
        }
    }

    .highlight-animation {
        animation: highlightAnimation 1.5s ease;
    }



    /* Enhanced badge styling */
    .badge {
        font-weight: 600;
        padding: 0.5em 0.8em;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        letter-spacing: 0.05rem;
    }




    /* Print styles */
    @media print {
        .no-print {
            display: none !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .table {
            width: 100% !important;
        }

        body {
            background-color: white !important;
        }
    }



    /* Media query for better responsiveness */
    @media (max-width: 767px) {
        .filter-section .form-group {
            margin-bottom: 1rem;
        }

        .card-body {
            padding: 1rem;
        }

        .table td,
        .table th {
            padding: 0.75rem;
        }
    }


    /* Improved Grade badge colors with gradients */
    .badge.grade-a {
        background: linear-gradient(135deg, #2ecc71, #27ae60);
        color: white;
    }

    .badge.grade-b {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
    }

    .badge.grade-c {
        background: linear-gradient(135deg, #1abc9c, #16a085);
        color: white;
    }

    .badge.grade-d {
        background: linear-gradient(135deg, #f1c40f, #f39c12);
        color: #212529;
    }

    .badge.grade-e {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
    }

    .badge.grade-f {
        background: linear-gradient(135deg, #7f8c8d, #2c3e50);
        color: white;
    }

    /* Enhanced Grade list styling */
    .grade-list {
        list-style-type: none;
        padding: 0;
        margin: 0;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        background-color: white;
    }

    .grade-list li {
        padding: 0.85rem 1.25rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease;
    }

    .grade-list li:hover {
        background-color: rgba(0, 0, 0, 0.02);
        transform: translateX(3px);
    }

    .grade-list li:last-child {
        border-bottom: none;
    }

    .grade-label {
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .grade-label .badge {
        margin-right: 12px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .grade-value {
        font-weight: bold;
        font-family: 'Courier New', monospace;
        padding: 0.3rem 0.8rem;
        background-color: #f8f9fa;
        border-radius: 0.25rem;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
        color: #495057;
        min-width: 90px;
        text-align: center;
    }

    /* Filter section styling */
    .filter-section {
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    .filter-section .card {
        border-radius: 0.75rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        border: none;
    }

    .filter-section .card-header {
        background: linear-gradient(135deg, #6c757d, #495057);
        color: white;
        border-top-left-radius: 0.75rem !important;
        border-top-right-radius: 0.75rem !important;
    }

    /* Enhanced input styles */
    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        border-color: #4d8bf8;
    }

    /* Mini score cards styling */
    .score-card {
        border-radius: 0.5rem;
        padding: 0.5rem 0.8rem;
        margin-bottom: 0.25rem;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 500;
        font-size: 0.85rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }

    .score-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .score-card.bg-success {
        background: linear-gradient(135deg, #2ecc71, #27ae60);
    }

    .score-card.bg-primary {
        background: linear-gradient(135deg, #3498db, #2980b9);
    }

    .score-card.bg-info {
        background: linear-gradient(135deg, #1abc9c, #16a085);
    }

    .score-card.bg-warning {
        background: linear-gradient(135deg, #f1c40f, #f39c12);
        color: #212529;
    }

    .score-card.bg-danger {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
    }

    .weighted-score {
        background-color: rgba(255, 255, 255, 0.25);
        padding: 0.2rem 0.5rem;
        border-radius: 0.25rem;
        margin-left: 0.5rem;
        font-weight: 600;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    /* Label improvements */
    .form-label {
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.05rem;
        margin-bottom: 0.5rem;
    }

    /* Alert improvement */
    .alert {
        border-radius: 0.75rem;
        border: none;
    }

    /* Enhanced weight settings card */
    #weightValidation {
        border-radius: 0.5rem;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
    }

    #weightValidation.alert-danger {
        background: linear-gradient(135deg, #ffecec, #ffe0e0);
        border-left: 4px solid #dc3545;
    }

    #weightValidation.alert-info {
        background: linear-gradient(135deg, #e6f2ff, #d9e9ff);
        border-left: 4px solid #0d6efd;
    }
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Weight Settings Card (includes year selection) -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-balance-scale"></i>
                    <h5 class="mb-0">{{ __('Evaluation Weight Settings') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="year" class="form-label text-primary">{{ __('ACADEMIC YEAR') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt text-primary"></i>
                                    </span>
                                    <select name="year" id="year" class="form-select">
                                        @foreach($availableYears as $year)
                                        <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="selectedYear" value="{{ $selectedYear }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="performanceWeight" class="form-label text-success">{{ __('PERFORMANCE WEIGHT (%)') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-chart-line text-success"></i>
                                    </span>
                                    <input type="number" id="performanceWeight" class="form-control weight-input" value="60" min="0" max="100">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="disciplineWeight" class="form-label text-warning">{{ __('DISCIPLINE WEIGHT (%)') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user-clock text-warning"></i>
                                    </span>
                                    <input type="number" id="disciplineWeight" class="form-control weight-input" value="30" min="0" max="100">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="elearningWeight" class="form-label text-info">{{ __('E-LEARNING WEIGHT (%)') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-laptop-code text-info"></i>
                                    </span>
                                    <input type="number" id="elearningWeight" class="form-control weight-input" value="10" min="0" max="100">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-8">
                            <div class="alert alert-info shadow-sm d-flex align-items-center" id="weightValidation">
                                <i class="fas fa-info-circle me-2 text-primary fs-4"></i>
                                <div>
                                    <span class="text-muted fw-bold">TOTAL WEIGHT:</span> <span id="totalWeight" class="fs-5 fw-bold">100%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-center justify-content-end">
                            <button type="button" id="calculateButton" class="btn btn-primary shadow-sm" onclick="handleCalculateClick(this)">
                                <i class="fas fa-calculator me-2"></i>Calculate Final Scores
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grade Info Card with Improved UI -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-graduation-cap"></i>
                    <h5 class="mb-0">{{ __('Grade Conversion Guide') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="grade-list">
                                <li>
                                    <div class="grade-label">
                                        <span class="badge grade-a">A</span> Excellent
                                    </div>
                                    <span class="grade-value">5.0</span>
                                </li>
                                <li>
                                    <div class="grade-label">
                                        <span class="badge grade-a">A-</span> Very Good
                                    </div>
                                    <span class="grade-value">4.6 - 4.9</span>
                                </li>
                                <li>
                                    <div class="grade-label">
                                        <span class="badge grade-b">B+</span> Good Plus
                                    </div>
                                    <span class="grade-value">4.1 - 4.5</span>
                                </li>
                                <li>
                                    <div class="grade-label">
                                        <span class="badge grade-b">B</span> Good
                                    </div>
                                    <span class="grade-value">4.0</span>
                                </li>
                                <li>
                                    <div class="grade-label">
                                        <span class="badge grade-b">B-</span> Above Average
                                    </div>
                                    <span class="grade-value">3.6 - 3.9</span>
                                </li>
                                <li>
                                    <div class="grade-label">
                                        <span class="badge grade-c">C+</span> Average Plus
                                    </div>
                                    <span class="grade-value">3.1 - 3.5</span>
                                </li>
                                <li>
                                    <div class="grade-label">
                                        <span class="badge grade-c">C</span> Average
                                    </div>
                                    <span class="grade-value">3.0</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="grade-list">
                                <li>
                                    <div class="grade-label">
                                        <span class="badge grade-c">C-</span> Below Average
                                    </div>
                                    <span class="grade-value">2.6 - 2.9</span>
                                </li>
                                <li>
                                    <div class="grade-label">
                                        <span class="badge grade-d">D+</span> Fair Plus
                                    </div>
                                    <span class="grade-value">2.1 - 2.5</span>
                                </li>
                                <li>
                                    <div class="grade-label">
                                        <span class="badge grade-d">D</span> Fair
                                    </div>
                                    <span class="grade-value">2.0</span>
                                </li>
                                <li>
                                    <div class="grade-label">
                                        <span class="badge grade-d">D-</span> Poor
                                    </div>
                                    <span class="grade-value">1.6 - 1.9</span>
                                </li>
                                <li>
                                    <div class="grade-label">
                                        <span class="badge grade-e">E+</span> Very Poor
                                    </div>
                                    <span class="grade-value">1.1 - 1.5</span>
                                </li>
                                <li>
                                    <div class="grade-label">
                                        <span class="badge grade-e">E</span> Insufficient
                                    </div>
                                    <span class="grade-value">1.0</span>
                                </li>
                                <li>
                                    <div class="grade-label">
                                        <span class="badge grade-f">F</span> Failing
                                    </div>
                                    <span class="grade-value">0 - 0.9</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Moved Filter Section Above Results Card -->
            <div class="filter-section mt-4" id="filterSection" style="display: none;">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-secondary text-white">
                        <i class="fas fa-filter"></i>
                        <h5 class="mb-0">{{ __('Filter Results') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- <div class="col-md-4">
                                <label for="filterEmployee" class="form-label text-secondary">
                                    <i class="fas fa-user-tie me-1"></i>{{ __('EMPLOYEE') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search text-secondary"></i>
                                    </span>
                                    <select id="filterEmployee" class="form-select">
                                        <option value="">{{ __('All Employees') }}</option>
                                    </select>
                                </div>
                            </div> -->
                            <div class="col-md-6">
                                <label for="filterDepartment" class="form-label text-secondary">
                                    <i class="fas fa-building me-1"></i>{{ __('DEPARTMENT') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-sitemap text-secondary"></i>
                                    </span>
                                    <select id="filterDepartment" class="form-select">
                                        <option value="">{{ __('All Departments') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="filterPosition" class="form-label text-secondary">
                                    <i class="fas fa-id-badge me-1"></i>{{ __('POSITION') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-briefcase text-secondary"></i>
                                    </span>
                                    <select id="filterPosition" class="form-select">
                                        <option value="">{{ __('All Positions') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 d-flex justify-content-end">
                                <button type="button" class="btn btn-outline-secondary me-2" id="resetFilters">
                                    <i class="fas fa-undo me-1"></i> Reset Filters
                                </button>
                                <button type="button" class="btn btn-primary" id="applyFilters">
                                    <i class="fas fa-filter me-1"></i> Apply Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Results Card -->
            <div class="card shadow-sm" id="resultsCard" style="display: none;">
                <div class="card-header bg-gradient-primary text-primary d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-list-alt"></i>
                        <h5 class="mb-0 d-inline">{{ __('Final Evaluation Results') }}</h5>
                    </div>
                    <div>
                        <a href="{{ route('evaluation.report.final.calculate.export', [
                                        'year' => $selectedYear,
                                        'employee' => request('employee'),
                                        'position' => request('position'),
                                        'department' => request('department'),
                                        'performance_weight' => request('performanceWeight', 60),
                                        'discipline_weight' => request('disciplineWeight', 30),
                                        'elearning_weight' => request('elearningWeight', 10)
                                    ]) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-file-excel me-1"></i> Export to Excel
                        </a>
                        <button id="saveResultsBtn" class="btn btn-success ms-2">
                            <i class="fas fa-save"></i> Save Results
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped border-light" id="finalTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" width="3%">#</th>
                                    <th width="8%">{{ ('Employee ID') }}</th>
                                    <th width="15%">{{ ('Name') }}</th>
                                    <th width="15%">{{ ('Department') }}</th>
                                    <th width="15%">{{ ('Position') }}</th>
                                    <!-- Performance Columns -->
                                    <th width="8%">{{ ('Performance') }}</th>
                                    <th width="8%">{{ ('Performance Score') }}</th>
                                    <!-- Discipline Columns -->
                                    <th width="8%">{{ ('Discipline') }}</th>
                                    <th width="8%">{{ ('Discipline Score') }}</th>
                                    <!-- E-Learning Columns -->
                                    <th width="8%">{{ ('E-Learning') }}</th>
                                    <th width="8%">{{ ('E-Learning Score') }}</th>
                                    <!-- Final Results -->
                                    <th width="8%">{{ __('Final Score') }}</th>
                                    <th width="8%">{{ __('Final Grade') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($finalData as $index => $data)
                                <tr data-user-id="{{ $data['user_id'] }}"
                                    data-employee-id="{{ $data['employee_id'] }}"
                                    data-name="{{ $data['name'] }}"
                                    data-department="{{ $data['department'] }}"
                                    data-position="{{ $data['position'] }}">
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $data['employee_id'] }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-2"
                                                style="background: linear-gradient(135deg, {{ '#'.substr(md5($data['name']), 0, 6) }}, {{ '#'.substr(md5($data['name'].$data['employee_id']), 0, 6) }});">
                                                <span>{{ strtoupper(substr($data['name'], 0, 1)) }}</span>
                                            </div>
                                            <div>{{ $data['name'] }}</div>
                                        </div>
                                    </td>
                                    <td>{{ $data['department'] }}</td>
                                    <td>{{ $data['position'] }}</td>
                                    <!-- Performance Cells -->
                                    <td data-original="">
                                        <span class="badge rounded-pill bg-danger text-white">E</span>
                                        <span class="small">(N/A)</span>
                                    </td>
                                    <td class="text-center">-</td>
                                    <!-- Discipline Cells -->
                                    <td data-original="">
                                        <span class="badge rounded-pill bg-danger text-white">E</span>
                                        <span class="small">(N/A)</span>
                                    </td>
                                    <td class="text-center">-</td>
                                    <!-- E-Learning Cells -->
                                    <td data-original="">
                                        <span class="badge rounded-pill bg-danger text-white">E</span>
                                        <span class="small">(N/A)</span>
                                    </td>
                                    <td class="text-center">-</td>
                                    <!-- Final Results -->
                                    <td class="final-score fw-bold text-center">-</td>
                                    <td class="final-grade fw-bold text-center">-</td>
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
<script>
    $(document).ready(function() {


        
    // Initialize an empty table with proper configuration
    const finalTable = $('#finalTable').DataTable({
        responsive: true,
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>><"table-responsive"t><"d-flex justify-content-between align-items-center"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100, -1],

        // Language settings
        language: {
            emptyTable: "No data available. Click 'Calculate' to load evaluation data.",
            zeroRecords: "No matching records found",
            search: "_INPUT_",
            searchPlaceholder: "Search...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)"
        },

        // Column definitions
        columnDefs: [
            {
                targets: 0, // # column
                orderable: false,
                className: 'text-center',
                width: '40px',
                responsivePriority: 1
            },
            {
                targets: [5, 7, 9], // Grade columns
                className: 'text-center'
            },
            {
                targets: [6, 8, 10], // Score columns
                className: 'text-center'
            },
            {
                targets: [11, 12], // Final score/grade columns
                className: 'text-center fw-bold'
            }
        ],
        order: [[11, 'desc']] // Default sort by final score descending
    });

    // Initially hide the results card
    $('#resultsCard').hide();

    // Initialize weight validation
    validateWeights();

    // Weight input validation
    $('.weight-input').on('input', function() {
        validateWeights();
    });

    // Calculate button handler
    $('#calculateBtn').on('click', function() {
        handleCalculateClick(this);
    });

    // Save results button click handler
    $('#saveResultsBtn').click(function() {
        saveResults(this);
    });

    // Initialize year dropdown change handler
    $('#year').on('change', function() {
        window.location.href = "{{ route('evaluation.report.final.calculate.index') }}?year=" + $(this).val();
    });
});

/**
 * Function to collect all user IDs from the DataTable
 * Ensures all IDs are collected regardless of pagination
 */
function getUserIds() {
    let userIds = [];

    // Check if we have access to the correct DataTable
    if ($.fn.DataTable.isDataTable('#finalTable')) {
        const table = $('#finalTable').DataTable();

        // Collect all user IDs from all rows programmatically
        table.rows().every(function() {
            const $row = $(this.node());
            const userId = $row.data('user-id');
            if (userId) {
                userIds.push(userId);
            }
        });

        console.log(`Successfully retrieved ${userIds.length} user IDs from DataTable API`);

        // Store user IDs for future use
        $('#finalTable').data('all-user-ids', userIds);
        $('#allUserIds').val(JSON.stringify(userIds));

        return userIds;
    }

    // Fallback method if DataTable is not available
    $('#finalTable tbody tr').each(function() {
        const userId = $(this).data('user-id');
        if (userId) {
            userIds.push(userId);
        }
    });

    console.log(`Retrieved ${userIds.length} user IDs with DOM fallback method`);
    return userIds;
}

/**
 * Function to handle Calculate button click
 * Fetches data and calculates final scores
 */
function handleCalculateClick(buttonElement) {
    console.log('Running calculations');
    const $button = $(buttonElement);
    const selectedYear = $('#year').val();

    // Validate weights first
    if (!validateWeights()) {
        Swal.fire({
            title: 'Invalid Weights',
            text: 'Total weight must be 100%. Please adjust the weight values.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Change button status to loading
    $button.prop('disabled', true)
        .html('<i class="fas fa-spinner fa-spin me-2"></i>Processing data...');

    // Show loading overlay
    Swal.fire({
        title: 'Processing Data',
        html: 'Calculating employee evaluations...<br><small class="text-muted">This process may take a moment</small>',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Get all user IDs using the enhanced function
    const userIds = getUserIds();

    // Make sure we have user IDs to process
    if (!userIds.length) {
        Swal.fire({
            title: 'Error',
            text: 'No user data found. Please refresh the page and try again.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        $button.prop('disabled', false)
            .html('<i class="fas fa-calculator me-2"></i>Calculate Final Scores');
        return;
    }

    console.log('User IDs to process:', userIds.length, userIds);

    // Fetch data via AJAX with enhanced error handling
    $.ajax({
        url: "{{ route('evaluation.report.final.calculate.getData') }}",
        method: "POST",
        timeout: 900000, // 15 minutes
        data: {
            userIds: userIds,
            year: selectedYear,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            handleFetchSuccess(response);
        },
        error: function(xhr) {
            handleFetchError(xhr);
        }
    });
}

function handleFetchSuccess(response) {
    console.log('Data successfully received:', response);
    Swal.close();

    // Store evaluation data
    evaluationData = response;

    // Store total number of users for verification
    const totalUsers = Object.keys(response.performance || {}).length;
    console.log(`Total users in response: ${totalUsers}`);

    // Destroy existing DataTable if it exists
    if ($.fn.DataTable.isDataTable('#finalTable')) {
        $('#finalTable').DataTable().destroy();
    }

    // Clear table body to rebuild it properly
    const $tableBody = $('#finalTable tbody');

    // Check if we have data
    if (totalUsers === 0) {
        Swal.fire({
            title: 'No Data Found',
            text: 'No evaluation data found for the selected year.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        $('#calculateBtn').prop('disabled', false)
            .html('<i class="fas fa-calculator me-2"></i>Calculate Final Scores');
        return;
    }

    // Update all rows with new data and hide those without data
    const rowsToShow = [];
    $tableBody.find('tr').each(function() {
        const $row = $(this);
        const userId = $row.data('user-id');

        if (!userId) return;

        let hasData = false;

        // Performance Data
        if (response.performance && response.performance[userId]) {
            hasData = true;
            const perfData = response.performance[userId];
            $row.data('performance', perfData.grade);
            $row.data('performance-score', perfData.score);
            const originalHtml = `<span class="badge rounded-pill grade-${perfData.grade.toLowerCase().charAt(0)}">${perfData.grade}</span><span class="small">(${perfData.score})</span>`;
            $row.find('td:eq(5)').attr('data-original', originalHtml).html(originalHtml);
        } else {
            // If no performance data, clear the column
            $row.data('performance', 'F');
            $row.data('performance-score', 0);
            $row.find('td:eq(5)').html('<span class="badge rounded-pill grade-f">F</span><span class="small">(0)</span>');
        }

        // Discipline Data
        if (response.discipline && response.discipline[userId]) {
            hasData = true;
            const discData = response.discipline[userId];
            $row.data('discipline', discData.grade);
            $row.data('discipline-score', discData.score);
            const originalHtml = `<span class="badge rounded-pill grade-${discData.grade.toLowerCase().charAt(0)}">${discData.grade}</span><span class="small">(${discData.score})</span>`;
            $row.find('td:eq(7)').attr('data-original', originalHtml).html(originalHtml);
        } else {
            // If no discipline data, clear the column
            $row.data('discipline', 'F');
            $row.data('discipline-score', 0);
            $row.find('td:eq(7)').html('<span class="badge rounded-pill grade-f">F</span><span class="small">(0)</span>');
        }

        // E-learning Data
        if (response.elearning && response.elearning[userId]) {
            hasData = true;
            const eLearnData = response.elearning[userId];
            $row.data('elearning', eLearnData.grade);
            $row.data('elearning-score', eLearnData.score);
            const originalHtml = `<span class="badge rounded-pill grade-${eLearnData.grade.toLowerCase().charAt(0)}">${eLearnData.grade}</span><span class="small">(${eLearnData.score})</span>`;
            $row.find('td:eq(9)').attr('data-original', originalHtml).html(originalHtml);
        } else {
            // If no e-learning data, clear the column
            $row.data('elearning', 'F');
            $row.data('elearning-score', 0);
            $row.find('td:eq(9)').html('<span class="badge rounded-pill grade-f">F</span><span class="small">(0)</span>');
        }

        // If the employee has at least one evaluation data, add to the list of rows to show
        if (hasData) {
            rowsToShow.push(userId);
            $row.removeClass('d-none').show();
        } else {
            // For rows without evaluation data, mark with permanent-hide class
            $row.addClass('d-none permanent-hide').hide();
        }
    });

    console.log(`Showing ${rowsToShow.length} employees with evaluation data`);

    // Calculate final scores for all employees (including hidden ones, for data completeness)
    calculateFinalScores(true);

    // CRITICAL FIX: Remove rows without data from DOM before DataTable initialization
    // This ensures DataTable only counts visible rows
    $tableBody.find('tr.permanent-hide').detach();

    // Reinitialize DataTable with fixed configuration
    $('#finalTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100, -1],
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>><"table-responsive"t><"d-flex justify-content-between align-items-center"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',
        order: [[11, 'desc']], // Sort by final score by default
        
        // Language settings with correct count
        language: {
            emptyTable: "No data available. Click 'Calculate' to load evaluation data.",
            zeroRecords: "No matching records found",
            search: "_INPUT_",
            searchPlaceholder: "Search...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)"
        }
    });

    // Show filter section and results
    $('#filterSection').show();
    $('#resultsCard').show();

    // Populate filters with current data
    populateFilters();

    // Reset button status
    $('#calculateBtn').prop('disabled', false)
        .html('<i class="fas fa-calculator me-2"></i>Calculate Final Scores');

    // Show success message
    setTimeout(() => {
        Swal.fire({
            title: 'Calculation Complete',
            text: 'Final scores have been successfully calculated.',
            icon: 'success',
            confirmButtonText: 'Great!',
            timer: 2000,
            timerProgressBar: true
        });
    }, 300);
}



/**
 * Function to handle AJAX errors
 */
function handleFetchError(xhr) {
    console.error("Error fetching data:", xhr);

    // Close loading overlay
    Swal.close();

    // Reset button status
    $('#calculateBtn').prop('disabled', false)
        .html('<i class="fas fa-calculator me-2"></i>Calculate Final Scores');

    // Show error message
    setTimeout(() => {
        Swal.fire({
            title: 'Error',
            text: 'Failed to retrieve evaluation data. Please try again.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }, 300);
}

/**
 * Function to validate weights
 */
function validateWeights() {
    const performanceWeight = parseInt($('#performanceWeight').val());
    const disciplineWeight = parseInt($('#disciplineWeight').val());
    const elearningWeight = parseInt($('#elearningWeight').val());

    const totalWeight = performanceWeight + disciplineWeight + elearningWeight;
    $('#totalWeight').text(totalWeight + '%');

    // Update header weights
    $('#perfHeaderWeight').text(performanceWeight + '%');
    $('#discHeaderWeight').text(disciplineWeight + '%');
    $('#elHeaderWeight').text(elearningWeight + '%');

    if (totalWeight === 100) {
        $('#weightValidation').removeClass('alert-danger').addClass('alert-info');
        return true;
    } else {
        $('#weightValidation').removeClass('alert-info').addClass('alert-danger');
        return false;
    }
}

/**
 * Function to calculate final scores
 * Enhancement: All data is calculated even if not displayed
 */
function calculateFinalScores(animate = false) {
    const performanceWeight = parseInt($('#performanceWeight').val()) / 100;
    const disciplineWeight = parseInt($('#disciplineWeight').val()) / 100;
    const elearningWeight = parseInt($('#elearningWeight').val()) / 100;

    $('#finalTable tbody tr').each(function() {
        const $row = $(this);

        // Get grades
        const performanceGrade = $row.data('performance') || 'F';
        const disciplineGrade = $row.data('discipline') || 'F';
        const elearningGrade = $row.data('elearning') || 'F';

        // Get original scores
        const performanceScore = parseFloat($row.data('performance-score')) || 0;
        const disciplineScore = parseFloat($row.data('discipline-score')) || 0;
        const elearningScore = parseFloat($row.data('elearning-score')) || 0;

        // Convert grade to numeric value
        const gradeValues = {
            'A': 5.0,
            'A-': 4.75,
            'B+': 4.3,
            'B': 4.0,
            'B-': 3.75,
            'C+': 3.3,
            'C': 3.0,
            'C-': 2.75,
            'D+': 2.3,
            'D': 2.0,
            'D-': 1.75,
            'E+': 1.3,
            'E': 1.0,
            'F': 0.5
        };

        const performanceGradeValue = gradeValues[performanceGrade] || 0;
        const disciplineGradeValue = gradeValues[disciplineGrade] || 0;
        const elearningGradeValue = gradeValues[elearningGrade] || 0;

        // Calculate weighted scores
        const weightedPerformance = performanceGradeValue * performanceWeight;
        const weightedDiscipline = disciplineGradeValue * disciplineWeight;
        const weightedElearning = elearningGradeValue * elearningWeight;

        // Calculate final score
        const finalScore = weightedPerformance + weightedDiscipline + weightedElearning;
        const finalGrade = getGradeFromScore(finalScore);

        // Update display for Performance
        $row.find('td:eq(5)').html(`
            <span class="badge rounded-pill grade-${performanceGrade.toLowerCase().charAt(0)}">${performanceGrade}</span>
            <span class="small">(${performanceScore})</span>
        `);
        $row.find('td:eq(6)').html(`
            <div class="fw-bold">${performanceGradeValue.toFixed(2)}</div>
            <div class="text-muted small">${(performanceWeight * 100)}%</div>
            <div class="text-primary fw-bold mt-1">${weightedPerformance.toFixed(2)}</div>
        `);

        // Update display for Discipline
        $row.find('td:eq(7)').html(`
            <span class="badge rounded-pill grade-${disciplineGrade.toLowerCase().charAt(0)}">${disciplineGrade}</span>
            <span class="small">(${disciplineScore})</span>
        `);
        $row.find('td:eq(8)').html(`
            <div class="fw-bold">${disciplineGradeValue.toFixed(2)}</div>
            <div class="text-muted small">${(disciplineWeight * 100)}%</div>
            <div class="text-warning fw-bold mt-1">${weightedDiscipline.toFixed(2)}</div>
        `);

        // Update display for E-Learning
        $row.find('td:eq(9)').html(`
            <span class="badge rounded-pill grade-${elearningGrade.toLowerCase().charAt(0)}">${elearningGrade}</span>
            <span class="small">(${elearningScore})</span>
        `);
        $row.find('td:eq(10)').html(`
            <div class="fw-bold">${elearningGradeValue.toFixed(2)}</div>
            <div class="text-muted small">${(elearningWeight * 100)}%</div>
            <div class="text-info fw-bold mt-1">${weightedElearning.toFixed(2)}</div>
        `);

        // Display final score and grade
        $row.find('.final-score').text(finalScore.toFixed(2));
        $row.find('.final-grade').html(`<span class="badge grade-${finalGrade.toLowerCase().charAt(0)}">${finalGrade}</span>`);

        // Add animation effect if requested
        if (animate) {
            $row.find('.final-score, .final-grade').addClass('highlight-animation');
            setTimeout(() => {
                $row.find('.final-score, .final-grade').removeClass('highlight-animation');
            }, 1200);
        }
    });

    // Sort table by final score (descending)
    if ($.fn.DataTable.isDataTable('#finalTable')) {
        $('#finalTable').DataTable().order([11, 'desc']).draw();
    }
}

/**
 * Function to save results
 * FIXED: Now saves ALL employees regardless of current page/pagination
 */
function saveResults(buttonElement) {
    const $button = $(buttonElement);

    // Disable button to prevent double submission
    $button.prop('disabled', true);
    $button.html('<i class="fas fa-spinner fa-spin"></i> Saving...');

    // Show loading overlay
    Swal.fire({
        title: 'Saving Data',
        text: 'Saving evaluation data to server...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Collect data from ALL rows across ALL pages of the DataTable
    let evaluationData = [];
    
    // Get DataTable instance
    let table = null;
    if ($.fn.DataTable.isDataTable('#finalTable')) {
        // Use DataTable API to access ALL rows, not just current page
        table = $('#finalTable').DataTable();
        
        // Critical fix: Use rows().nodes() to get ALL rows in the DataTable across all pages
        table.rows().every(function() {
            const $row = $(this.node());
            const userId = $row.data('user-id');
            
            if (!userId || $row.hasClass('permanent-hide')) return; // Skip invalid rows
            
            // Get grades from badges in each column
            const performanceGrade = $row.find('td:eq(5) .badge').text().trim() || 'F';
            const disciplineGrade = $row.find('td:eq(7) .badge').text().trim() || 'F';
            const elearningGrade = $row.find('td:eq(9) .badge').text().trim() || 'F';

            // Get weighted scores (be careful to get the numbers correctly)
            const performanceScore = parseFloat($row.find('td:eq(6) .text-primary').text().trim()) || 0;
            const disciplineScore = parseFloat($row.find('td:eq(8) .text-warning').text().trim()) || 0;
            const elearningScore = parseFloat($row.find('td:eq(10) .text-info').text().trim()) || 0;

            // Get final score and grade
            const finalScore = parseFloat($row.find('.final-score').text().trim()) || 0;
            const finalGrade = $row.find('.final-grade .badge').text().trim() || 'F';

            // Add to collection
            evaluationData.push({
                user_id: userId,
                year: $('#year').val(),
                performance: performanceGrade,
                performance_score: performanceScore,
                discipline: disciplineGrade,
                discipline_score: disciplineScore,
                elearning: elearningGrade,
                elearning_score: elearningScore,
                final_score: finalScore,
                final_grade: finalGrade
            });
        });
    } else {
        // Fallback to DOM method if DataTable is not available
        $('#finalTable tbody tr').not('.permanent-hide').not('.d-none').each(function() {
            const $row = $(this);
            const userId = $row.data('user-id');

            if (!userId) return; // Skip rows without user ID

            // Get grades from badges in each column
            const performanceGrade = $row.find('td:eq(5) .badge').text().trim() || 'F';
            const disciplineGrade = $row.find('td:eq(7) .badge').text().trim() || 'F';
            const elearningGrade = $row.find('td:eq(9) .badge').text().trim() || 'F';

            // Get weighted scores
            const performanceScore = parseFloat($row.find('td:eq(6) .text-primary').text().trim()) || 0;
            const disciplineScore = parseFloat($row.find('td:eq(8) .text-warning').text().trim()) || 0;
            const elearningScore = parseFloat($row.find('td:eq(10) .text-info').text().trim()) || 0;

            // Get final score and grade
            const finalScore = parseFloat($row.find('.final-score').text().trim()) || 0;
            const finalGrade = $row.find('.final-grade .badge').text().trim() || 'F';

            // Add to collection
            evaluationData.push({
                user_id: userId,
                year: $('#year').val(),
                performance: performanceGrade,
                performance_score: performanceScore,
                discipline: disciplineGrade,
                discipline_score: disciplineScore,
                elearning: elearningGrade,
                elearning_score: elearningScore,
                final_score: finalScore,
                final_grade: finalGrade
            });
        });
    }

    console.log(`Saving evaluation data for ${evaluationData.length} employees across all pages`);
    
    // Debugging: Log the first few records to verify data
    if (evaluationData.length > 0) {
        console.log('Sample data being saved:', evaluationData.slice(0, 2));
    }

    // Send data via AJAX
    $.ajax({
        url: '/evaluation/report/final/calculate/save',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            evaluations: evaluationData
        },
        dataType: 'json',
        success: function(response) {
            // Close loading overlay
            Swal.close();

            // Enable button again
            $button.prop('disabled', false).html('<i class="fas fa-save"></i> Save Results');

            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: `Evaluation results for ${evaluationData.length} employees have been successfully saved.`,
                timer: 3000
            });
        },
        error: function(xhr) {
            // Close loading overlay
            Swal.close();

            // Enable button again
            $button.prop('disabled', false).html('<i class="fas fa-save"></i> Save Results');

            // Show error message
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to save evaluation results. Please try again.',
            });

            console.error('Error saving results:', xhr.responseText);
        }
    });
}
/**
 * Function to get grade from score
 */
function getGradeFromScore(score) {
    if (score >= 5.0) return 'A';
    if (score >= 4.6) return 'A-';
    if (score >= 4.1) return 'B+';
    if (score >= 4.0) return 'B';
    if (score >= 3.6) return 'B-';
    if (score >= 3.1) return 'C+';
    if (score >= 3.0) return 'C';
    if (score >= 2.6) return 'C-';
    if (score >= 2.1) return 'D+';
    if (score >= 2.0) return 'D';
    if (score >= 1.6) return 'D-';
    if (score >= 1.1) return 'E+';
    if (score >= 1.0) return 'E';
    return 'F';
}

/**
 * Function to populate filters
 */
function populateFilters() {
    console.log('Populating filters');

    // Get unique values from visible rows only
    const departments = new Set();
    const positions = new Set();
    const employees = new Map(); // Use Map to avoid duplicates based on employee ID

    // PERBAIKAN: Hanya gunakan baris yang terlihat (tidak memiliki kelas d-none)
    $('#finalTable tbody tr').not('.d-none').each(function() {
        const $row = $(this);
        
        const dept = $row.data('department');
        const pos = $row.data('position');
        const empId = $row.data('employee-id');
        const empName = $row.data('name');

        if (dept) departments.add(dept);
        if (pos) positions.add(pos);
        if (empId && empName) {
            employees.set(empId, empName);
        }
    });

    // Clear existing options first (except the first "All" option)
    $('#filterEmployee').find('option').not(':first').remove();
    $('#filterDepartment').find('option').not(':first').remove();
    $('#filterPosition').find('option').not(':first').remove();

    // Fill department filter
    Array.from(departments).sort().forEach(dept => {
        $('#filterDepartment').append(`<option value="${dept}">${dept}</option>`);
    });

    // Fill position filter
    Array.from(positions).sort().forEach(pos => {
        $('#filterPosition').append(`<option value="${pos}">${pos}</option>`);
    });

    // Fill employee filter - sort by name
    Array.from(employees.entries())
        .sort((a, b) => a[1].localeCompare(b[1]))
        .forEach(([id, name]) => {
            $('#filterEmployee').append(`<option value="${id}">${name} (${id})</option>`);
        });

    // Reset filter button
    $('#resetFilters').off('click').on('click', function() {
        $('#filterEmployee, #filterDepartment, #filterPosition').val('');
        applyFilters();
    });

    // Apply filter button
    $('#applyFilters').off('click').on('click', applyFilters);
}

/**
 * Function to apply filters
 */
function applyFilters() {
    const departmentFilter = $('#filterDepartment').val();
    const positionFilter = $('#filterPosition').val();
    const employeeFilter = $('#filterEmployee').val();

    // Get DataTable instance if available
    let table = null;
    if ($.fn.DataTable.isDataTable('#finalTable')) {
        table = $('#finalTable').DataTable();
    }
    
    // Destroy the existing DataTable to rebuild it
    if (table) {
        table.destroy();
    }
    
    // Apply filters directly to DOM first
    let filteredRowCount = 0;
    $('#finalTable tbody tr').each(function() {
        const $row = $(this);
        
        // Skip permanently hidden rows
        if ($row.hasClass('permanent-hide')) {
            return;
        }
        
        const department = $row.data('department');
        const position = $row.data('position');
        const employeeId = $row.data('employee-id');
        
        // Check filter matches
        const departmentMatch = !departmentFilter || department === departmentFilter;
        const positionMatch = !positionFilter || position === positionFilter;
        const employeeMatch = !employeeFilter || employeeId === employeeFilter;
        
        // Show/hide based on filter matches
        if (departmentMatch && positionMatch && employeeMatch) {
            $row.removeClass('filter-hide').show();
            filteredRowCount++;
        } else {
            $row.addClass('filter-hide').hide();
        }
    });
    
    // Reinitialize DataTable on filtered rows
    $('#finalTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100, -1],
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>><"table-responsive"t><"d-flex justify-content-between align-items-center"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',
        order: [[11, 'desc']] // Sort by final score by default
    });
    
    // Update filter UI feedback
    updateFilterCounters(filteredRowCount);
}

/**
 * Function to update filter counters
 */
function updateFilterCounters(filteredCount) {
    const totalRows = $('#finalTable tbody tr').not('.permanent-hide').length;
    const visibleRows = filteredCount || $('#finalTable tbody tr').not('.permanent-hide').not('.filter-hide').length;
    
    $('#filterCount').text(`Showing ${visibleRows} of ${totalRows} employees`);
    
    // Show reset button only when filters are active
    if (visibleRows < totalRows) {
        $('#resetFilters').show();
    } else {
        $('#resetFilters').hide();
    }
}



</script>
@endpush