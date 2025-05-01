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
    function handleCalculateClick(buttonElement) {
        console.log('Calculate button handler executed');
        const $button = $(buttonElement);
        const selectedYear = $('#year').val();

        // Validate weights first
        if (!validateWeights()) {
            Swal.fire({
                title: 'Invalid Weights',
                text: 'The total weight must equal 100%. Please adjust your weights.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Change button state to loading
        $button.prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin me-2"></i>Processing data...');

        // Show loading overlay
        Swal.fire({
            title: 'Loading Data',
            text: 'Fetching evaluation data, please wait...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Get the user IDs
        const userIds = [];
        $('#finalTable tbody tr').each(function() {
            userIds.push($(this).data('user-id'));
        });

        // Fetch data via AJAX
        $.ajax({
            url: "{{ route('evaluation.report.final.calculate.getData') }}",
            method: "POST",
            data: {
                userIds: userIds,
                year: selectedYear,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                console.log('Data received successfully:', response);

                // Close the loading overlay first
                Swal.close();

                // Store the fetched data
                evaluationData = response;

                // Update each row with the fetched data
                $('#finalTable tbody tr').each(function() {
                    const $row = $(this);
                    const userId = $row.data('user-id');

                    // Set the performance data with enhanced UI
                    if (response.performance && response.performance[userId]) {
                        const perfData = response.performance[userId];
                        $row.data('performance', perfData.grade);
                        $row.data('performance-score', perfData.score);

                        // Store original display format
                        const originalHtml = `
                            <span class="badge rounded-pill grade-${perfData.grade.toLowerCase().charAt(0)}">${perfData.grade}</span>
                            <span class="small">(${perfData.score})</span>
                        `;

                        $row.find('td:eq(5)')
                            .attr('data-original', originalHtml)
                            .html(originalHtml);
                    }

                    // Set the discipline data with enhanced UI
                    if (response.discipline && response.discipline[userId]) {
                        const discData = response.discipline[userId];
                        $row.data('discipline', discData.grade);
                        $row.data('discipline-score', discData.score);

                        // Store original display format
                        const originalHtml = `
                            <span class="badge rounded-pill grade-${discData.grade.toLowerCase().charAt(0)}">${discData.grade}</span>
                            <span class="small">(${discData.score})</span>
                        `;

                        $row.find('td:eq(6)')
                            .attr('data-original', originalHtml)
                            .html(originalHtml);
                    }

                    // Set the e-learning data with enhanced UI
                    if (response.elearning && response.elearning[userId]) {
                        const eLearnData = response.elearning[userId];
                        $row.data('elearning', eLearnData.grade);
                        $row.data('elearning-score', eLearnData.score);

                        // Store original display format
                        const originalHtml = `
                            <span class="badge rounded-pill grade-${eLearnData.grade.toLowerCase().charAt(0)}">${eLearnData.grade}</span>
                            <span class="small">(${eLearnData.score})</span>
                        `;

                        $row.find('td:eq(7)')
                            .attr('data-original', originalHtml)
                            .html(originalHtml);
                    }
                });

                // Calculate final scores and display weighted scores
                calculateFinalScores(true);

                // Show the filter section and results card
                $('#filterSection').show();
                $('#resultsCard').show();

                // Force refresh the DataTable
                if ($.fn.DataTable.isDataTable('#finalTable')) {
                    $('#finalTable').DataTable().draw();
                }

                // Populate filters - clear existing options first
                $('#filterEmployee, #filterDepartment, #filterPosition').find('option:not(:first)').remove();
                populateFilters();

                // Reset button and show success
                $button.prop('disabled', false)
                    .html('<i class="fas fa-calculator me-2"></i>Calculate Final Scores');

                // Show success message
                setTimeout(() => {
                    Swal.fire({
                        title: 'Calculation Complete',
                        text: 'The final scores have been calculated successfully.',
                        icon: 'success',
                        confirmButtonText: 'Great!',
                        timer: 2000,
                        timerProgressBar: true
                    });
                }, 300);
            },
            error: function(xhr) {
                console.error("Error fetching data:", xhr);

                // Close the loading overlay first
                Swal.close();

                // Reset button state
                $button.prop('disabled', false)
                    .html('<i class="fas fa-calculator me-2"></i>Calculate Final Scores');

                // Show error message
                setTimeout(() => {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to fetch evaluation data. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }, 300);
            }
        });
    }

    // Modified populateFilters function with improved filtering
    function populateFilters() {
        console.log('Populating filters');

        // Get unique values from the table
        const departments = [];
        const positions = [];
        const employees = [];

        $('#finalTable tbody tr').each(function() {
            const dept = $(this).data('department');
            const pos = $(this).data('position');
            const empId = $(this).data('employee-id');
            const empName = $(this).data('name');

            if (dept && !departments.includes(dept)) departments.push(dept);
            if (pos && !positions.includes(pos)) positions.push(pos);
            if (empId && empName) {
                // Check if employee already exists in the array
                const exists = employees.some(e => e.id === empId);
                if (!exists) {
                    employees.push({
                        id: empId,
                        name: empName
                    });
                }
            }
        });

        // Clear existing options first (except the first "All" option)
        $('#filterEmployee').find('option').not(':first').remove();
        $('#filterDepartment').find('option').not(':first').remove();
        $('#filterPosition').find('option').not(':first').remove();

        // Populate department filter
        departments.sort().forEach(dept => {
            $('#filterDepartment').append(`<option value="${dept}">${dept}</option>`);
        });

        // Populate position filter
        positions.sort().forEach(pos => {
            $('#filterPosition').append(`<option value="${pos}">${pos}</option>`);
        });

        // Populate employee filter
        employees.sort((a, b) => a.name.localeCompare(b.name)).forEach(emp => {
            $('#filterEmployee').append(`<option value="${emp.id}">${emp.name} (${emp.id})</option>`);
        });

        // Reset filters button
        $('#resetFilters').on('click', function() {
            $('#filterEmployee, #filterDepartment, #filterPosition').val('');
            applyFilters();
        });

        // Apply filters button
        $('#applyFilters').on('click', applyFilters);
    }

    function applyFilters() {
        const departmentFilter = $('#filterDepartment').val();
        const positionFilter = $('#filterPosition').val();
        const employeeFilter = $('#filterEmployee').val();

        $('#finalTable tbody tr').each(function() {
            const $row = $(this);

            // Skip rows that don't have the data attributes we need
            if (!$row.data('user-id')) {
                $row.hide(); // Hide any empty or non-data rows
                return true; // continue to next row
            }

            const department = $row.data('department');
            const position = $row.data('position');
            const employeeId = $row.data('employee-id');
            const employeeName = $row.data('name').toLowerCase();

            // Check if we're filtering by employee ID or name
            let employeeMatch = true;
            if (employeeFilter) {
                const filterText = employeeFilter.toLowerCase();
                employeeMatch = employeeId === employeeFilter ||
                    employeeName.includes(filterText);
            }

            const departmentMatch = !departmentFilter || department === departmentFilter;
            const positionMatch = !positionFilter || position === positionFilter;

            $row.toggle(departmentMatch && positionMatch && employeeMatch);
        });
    }
    // Validate that weights add up to 100%

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
    // Add a new function to create the calculation breakdown table
    function createCalculationBreakdown(performanceGrade, disciplineGrade, elearningGrade, weights) {
        // Define grade values according to the grading guide
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

        // Get numeric values for grades
        const perfScore = gradeValues[performanceGrade] || 0;
        const discScore = gradeValues[disciplineGrade] || 0;
        const elScore = gradeValues[elearningGrade] || 0;

        // Calculate weighted scores
        const perfWeighted = perfScore * weights.performance;
        const discWeighted = discScore * weights.discipline;
        const elWeighted = elScore * weights.elearning;

        // Calculate final score
        const finalScore = perfWeighted + discWeighted + elWeighted;

        // Create the table HTML
        return `
    <div class="calculation-table">
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Category</th>
                    <th>Grade</th>
                    <th>Value</th>
                    <th>Weight</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Performance</td>
                    <td><span class="badge grade-${performanceGrade.toLowerCase().charAt(0)}">${performanceGrade}</span></td>
                    <td>${perfScore.toFixed(2)}</td>
                    <td>${(weights.performance * 100)}%</td>
                    <td>${perfWeighted.toFixed(2)}</td>
                </tr>
                <tr>
                    <td>Discipline</td>
                    <td><span class="badge grade-${disciplineGrade.toLowerCase().charAt(0)}">${disciplineGrade}</span></td>
                    <td>${discScore.toFixed(2)}</td>
                    <td>${(weights.discipline * 100)}%</td>
                    <td>${discWeighted.toFixed(2)}</td>
                </tr>
                <tr>
                    <td>E-Learning</td>
                    <td><span class="badge grade-${elearningGrade.toLowerCase().charAt(0)}">${elearningGrade}</span></td>
                    <td>${elScore.toFixed(2)}</td>
                    <td>${(weights.elearning * 100)}%</td>
                    <td>${elWeighted.toFixed(2)}</td>
                </tr>
                <tr class="table-active fw-bold">
                    <td colspan="4" class="text-end">Final Score:</td>
                    <td>${finalScore.toFixed(2)}</td>
                </tr>
            </tbody>
        </table>
    </div>`;
    }

    // Add CSS for the calculation table
    const calculationTableCSS = `
        <style>
            .calculation-table {
                padding: 10px;
                border-radius: 0.5rem;
                background-color: #f8f9fa;
                box-shadow: 0 2px 5px rgba(0,0,0,0.05);
                max-width: 100%;
                margin-top: 10px;
            }
            
            .calculation-table table {
                margin-bottom: 0;
                font-size: 0.85rem;
            }
            
            .calculation-table th, 
            .calculation-table td {
                text-align: center;
                vertical-align: middle;
                padding: 0.5rem;
            }
            
            .calculation-table tr:hover {
                background-color: rgba(0,0,0,0.02);
            }
            
            /* Add a subtle hover effect to calculation button */
            .btn-calculation {
                border-radius: 0.5rem;
            }
            
            .btn-calculation:hover {
                background-color: #edf2ff;
                color: #0d6efd;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
        </style>`;

    // Add the CSS to the document
    $('head').append(calculationTableCSS);


    function calculateFinalScores(animate = false) {
        const performanceWeight = parseInt($('#performanceWeight').val()) / 100;
        const disciplineWeight = parseInt($('#disciplineWeight').val()) / 100;
        const elearningWeight = parseInt($('#elearningWeight').val()) / 100;

        $('#finalTable tbody tr').each(function() {
            const $row = $(this);

            // Get original scores
            const performanceScore = parseFloat($row.data('performance-score')) || 0;
            const disciplineScore = parseFloat($row.data('discipline-score')) || 0;
            const elearningScore = parseFloat($row.data('elearning-score')) || 0;

            // Get grades
            const performanceGrade = $row.data('performance') || 'F';
            const disciplineGrade = $row.data('discipline') || 'F';
            const elearningGrade = $row.data('elearning') || 'F';

            // Convert grades to numeric values
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

            if (animate) {
                $row.find('.final-score, .final-grade').addClass('highlight-animation');
                setTimeout(() => {
                    $row.find('.final-score, .final-grade').removeClass('highlight-animation');
                }, 1200);
            }
        });

        // Sort table by final score (descending)
        if ($.fn.DataTable.isDataTable('#finalTable')) {
            $('#finalTable').DataTable().order([11, 'desc']).draw(); // Update column index for final score
        }
    }

    // Helper function to get grade from score
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


    // Helper function for score coloring
    function getScoreColorClass(score) {
        if (score >= 4.6) return 'bg-success';
        if (score >= 3.6) return 'bg-primary';
        if (score >= 2.6) return 'bg-info';
        if (score >= 1.6) return 'bg-warning';
        return 'bg-danger';
    }

    // Weight input validation
    $('.weight-input').on('input', function() {
        validateWeights();
    });

    // Initialize the page
    $(document).ready(function() {
        // Initialize year dropdown change handler
        $('#year').on('change', function() {
            window.location.href = "{{ route('evaluation.report.final.calculate.index') }}?year=" + $(this).val();
        });

        $('#finalTable').DataTable({
            responsive: true,
            dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>><"table-responsive"t><"d-flex justify-content-between align-items-center"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',
            columnDefs: [{
                    targets: 0, // # column
                    orderable: false,
                    className: 'text-center',
                    width: '40px', // Explicitly set the width for the # column
                    responsivePriority: 1 // Ensure it stays visible on smaller screens
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
            order: [
                [11, 'desc']
            ], // Default sort by final score descending
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            drawCallback: function() {
                // Highlight final score cells based on grade
                this.api().cells('.final-score').every(function() {
                    var score = parseFloat(this.data());
                    var colorClass = getScoreColorClass(score);
                    $(this.node()).removeClass('bg-success bg-primary bg-info bg-warning bg-danger')
                        .addClass(colorClass + ' text-white');
                });
            }
        });

        // Helper function for score coloring
        function getScoreColorClass(score) {
            if (score >= 4.6) return 'bg-success';
            if (score >= 3.6) return 'bg-primary';
            if (score >= 2.6) return 'bg-info';
            if (score >= 1.6) return 'bg-warning';
            return 'bg-danger';
        }

        // Initialize weight validation
        validateWeights();

        // Save Results button click handler
        $('#saveResultsBtn').click(function() {
            // Disable the button to prevent multiple submissions
            $(this).prop('disabled', true);
            $(this).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

            // Collect data from the table
            let evaluationData = [];
            $('#finalTable tbody tr').each(function() {
                const row = $(this);
                const userId = row.data('user-id');

                // Get the grade from the badge in the respective columns
                const performanceGrade = row.find('td:eq(5) .badge').text().trim() || '';
                const disciplineGrade = row.find('td:eq(7) .badge').text().trim() || '';
                const elearningGrade = row.find('td:eq(9) .badge').text().trim() || '';
                
                // Get the weighted scores (highlighted in the image with red circles)
                const performanceScore = parseFloat(row.find('td:eq(6) .text-primary').text().trim()) || 0;
                const disciplineScore = parseFloat(row.find('td:eq(8) .text-warning').text().trim()) || 0;
                const elearningScore = parseFloat(row.find('td:eq(10) .text-info').text().trim()) || 0;
                
                // Get the final score and grade
                const finalScore = parseFloat(row.find('.final-score').text().trim()) || 0;
                const finalGrade = row.find('.final-grade .badge').text().trim() || '';

                evaluationData.push({
                    user_id: userId,
                    year: $('#selectedYear').val(),
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
                    // Re-enable the button
                    $('#saveResultsBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Save Results');

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Evaluation results have been saved successfully.',
                        timer: 3000
                    });
                },
                error: function(xhr) {
                    // Re-enable the button
                    $('#saveResultsBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Save Results');

                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to save evaluation results. Please try again.',
                    });

                    console.error('Error saving results:', xhr.responseText);
                }
            });
        });
 


    });
</script>

@endpush