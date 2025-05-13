@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 test">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-warning fw-bold">
            <i class="fas fa-chart-line me-2"></i>Employee Evaluation Analytics
        </h1>
    </div>

    <!-- Filter Card -->
    <div class="card shadow border-0 mb-4">
        <div class="card-header bg-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-filter me-2"></i>Report Filters
                </h5>
                <span class="badge bg-white text-primary">{{ now()->format('F j, Y') }}</span>
            </div>
        </div>
        <div class="card-body bg-light-gradient">
            <div class="row g-3">
                <div class="col-md-5">
                    <div class="form-floating">
                        <select class="form-select select2" id="years" name="years[]" multiple="multiple">
                            <!-- Will be populated via AJAX -->
                        </select>
                        <label for="years" class="fw-semibold">
                            <i class="fas fa-calendar-alt me-2"></i>Select Years (Max 5)
                        </label>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-floating">
                        <select class="form-select select2" id="employees" name="employees[]" multiple="multiple">
                            <!-- Will be populated via AJAX -->
                        </select>
                        <label for="employees" class="fw-semibold">
                            <i class="fas fa-users me-2"></i>Select Employees (Max 5)
                        </label>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" id="generateReport" class="btn btn-primary btn-lg w-100 shadow-sm py-3">
                        <i class="fas fa-chart-bar me-2"></i> Generate
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Content -->
    <div id="reportContent" class="d-none">
        <!-- Performance Section -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-gradient-primary text-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-tachometer-alt me-2"></i>Performance Metrics
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Grade Distribution</h6>
                            </div>
                            <div class="card-body position-relative" style="height: 300px;">
                                <canvas id="performanceChart"></canvas>
                                <div class="chart-overlay d-none">
                                    <div class="spinner-border text-primary" role="status"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Score Comparison</h6>
                            </div>
                            <div class="card-body position-relative" style="height: 300px;">
                                <canvas id="performanceScoreChart"></canvas>
                                <div class="chart-overlay d-none">
                                    <div class="spinner-border text-primary" role="status"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Discipline Section -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-gradient-warning text-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-user-shield me-2"></i>Discipline Metrics
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-warning text-white">
                                <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Grade Distribution</h6>
                            </div>
                            <div class="card-body position-relative" style="height: 300px;">
                                <canvas id="disciplineChart"></canvas>
                                <div class="chart-overlay d-none">
                                    <div class="spinner-border text-primary" role="status"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Score Comparison</h6>
                            </div>
                            <div class="card-body position-relative" style="height: 300px;">
                                <canvas id="disciplineScoreChart"></canvas>
                                <div class="chart-overlay d-none">
                                    <div class="spinner-border text-primary" role="status"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- E-Learning Section -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-gradient-info text-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-laptop-code me-2"></i>E-Learning Metrics
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Grade Distribution</h6>
                            </div>
                            <div class="card-body position-relative" style="height: 300px;">
                                <canvas id="elearningChart"></canvas>
                                <div class="chart-overlay d-none">
                                    <div class="spinner-border text-primary" role="status"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Score Comparison</h6>
                            </div>
                            <div class="card-body position-relative" style="height: 300px;">
                                <canvas id="elearningScoreChart"></canvas>
                                <div class="chart-overlay d-none">
                                    <div class="spinner-border text-primary" role="status"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Final Scores Section -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-gradient-purple text-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-star me-2"></i>Final Evaluation Metrics
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Grade Distribution</h6>
                            </div>
                            <div class="card-body position-relative" style="height: 300px;">
                                <canvas id="finalGradeChart"></canvas>
                                <div class="chart-overlay d-none">
                                    <div class="spinner-border text-primary" role="status"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-dark text-white">
                                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Score Comparison</h6>
                            </div>
                            <div class="card-body position-relative" style="height: 300px;">
                                <canvas id="finalScoreChart"></canvas>
                                <div class="chart-overlay d-none">
                                    <div class="spinner-border text-primary" role="status"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Radar Chart Section -->
        <div class="card shadow border-0">
            <div class="card-header bg-gradient-success text-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-radar me-2"></i>Overall Performance Comparison
                </h5>
            </div>
            <div class="card-body">
                <div class="card border-0 shadow-sm">
                    <div class="card-body position-relative" style="height: 400px;">
                        <canvas id="radarChart"></canvas>
                        <div class="chart-overlay d-none">
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<style>
    .test {
        --primary-color: #3c8dbc;
        --secondary-color: #6c757d;
        --success-color: #28a745;
        --info-color: #17a2b8;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --indigo-color: #6610f2;
        --purple-color: #6f42c1;
    }

    body {
        background-color: #f8f9fa;
    }

    .card {
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.1) !important;
    }

    .card-header {
        border-bottom: none;
        font-weight: 600;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #3c8dbc 0%, #367fa9 100%);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
    }

    .bg-gradient-purple {
        background: linear-gradient(135deg, #6f42c1 0%, #5a2d9a 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
    }


    .form-floating>label {
        padding: 1rem 0.75rem;
    }

    .select2-container--default .select2-selection--multiple {
        min-height: calc(3.5rem + 2px);
        padding-top: 1.625rem;
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: var(--primary-color);
        border: none;
        color: var(--primary-color);
        padding: 0.25rem 0.5rem;
        margin-top: 0.5rem;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: var(--primary-color);
        margin-right: 5px;
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-primary:hover {
        background-color: #367fa9;
        border-color: #367fa9;
    }

    .chart-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10;
    }

    .bg-light-gradient {
        background: linear-gradient(to bottom, #f8f9fa 0%, #e9ecef 100%);
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #367fa9;
    }

    /* Tooltip styling */
    .chart-tooltip {
        position: absolute;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 12px;
        pointer-events: none;
        z-index: 100;
        transition: all 0.3s ease;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-header h5 {
            font-size: 1.1rem;
        }

        .form-floating>label {
            font-size: 0.9rem;
        }
    }

    #toast-container>.toast-success {
        background-color: #28a745 !important;
        /* Warna hijau yang lebih terang */
        color: white !important;
        /* Teks jadi putih */
        opacity: 1 !important;
        /* Hilangkan transparansi */
        font-weight: bold;
    }
</style>


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Initialize toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000"
        };

        // Available grades in order
        const allGrades = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'D-', 'E+', 'E', 'F'];

        // Grade colors based on value
        const gradeColors = {
            'A': 'rgba(0, 200, 83, 0.7)',
            'A-': 'rgba(76, 175, 80, 0.7)',
            'B+': 'rgba(139, 195, 74, 0.7)',
            'B': 'rgba(205, 220, 57, 0.7)',
            'B-': 'rgba(255, 235, 59, 0.7)',
            'C+': 'rgba(255, 193, 7, 0.7)',
            'C': 'rgba(255, 152, 0, 0.7)',
            'C-': 'rgba(255, 87, 34, 0.7)',
            'D+': 'rgba(244, 67, 54, 0.7)',
            'D': 'rgba(211, 47, 47, 0.7)',
            'D-': 'rgba(198, 40, 40, 0.7)',
            'E+': 'rgba(183, 28, 28, 0.7)',
            'E': 'rgba(165, 0, 0, 0.7)'
        };

        const gradeBorderColors = {
            'A': 'rgb(0, 200, 83)',
            'A-': 'rgb(76, 175, 80)',
            'B+': 'rgb(139, 195, 74)',
            'B': 'rgb(205, 220, 57)',
            'B-': 'rgb(255, 235, 59)',
            'C+': 'rgb(255, 193, 7)',
            'C': 'rgb(255, 152, 0)',
            'C-': 'rgb(255, 87, 34)',
            'D+': 'rgb(244, 67, 54)',
            'D': 'rgb(211, 47, 47)',
            'D-': 'rgb(198, 40, 40)',
            'E+': 'rgb(183, 28, 28)',
            'E': 'rgb(165, 0, 0)'
        };

        // Initialize Select2 for employees with enhanced styling
        $('#employees').select2({
            placeholder: 'Search employees...',
            maximumSelectionLength: 5,
            width: '100%',
            closeOnSelect: false,
            templateResult: formatEmployee,
            templateSelection: formatEmployeeSelection,
            ajax: {
                url: "{{ route('api.employees') }}",
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.name,
                                id: item.id,
                                department: item.department || 'N/A',
                                position: item.position || 'N/A'
                            }
                        })
                    };
                },
                cache: true
            }
        });

        // Format employee display in dropdown
        function formatEmployee(employee) {
            if (!employee.id) {
                return employee.text;
            }

            var $container = $(
                '<div class="d-flex justify-content-between align-items-center">' +
                '<div>' +
                '<span class="fw-semibold">' + employee.text + '</span>' +
                '<div class="text-muted small">' + employee.position.position + '</div>' +
                '</div>' +
                '<span class="badge bg-primary">' + employee.department.department + '</span>' +
                '</div>'
            );

            return $container;
        }

        // Format selected employee display
        function formatEmployeeSelection(employee) {
            if (!employee.id) {
                return employee.text;
            }

            return $(
                '<span class="fw-semibold">' + employee.text + '</span>' +
                '<span class="text-muted small ms-2">' + (employee.department.department || '') + '</span>'
            );
        }

        // Initialize Select2 for years with enhanced styling
        $('#years').select2({
            placeholder: 'Select years...',
            maximumSelectionLength: 5,
            width: '100%',
            closeOnSelect: false
        });

        // Fetch years dynamically
        $.ajax({
            url: "{{ route('evaluation.report.years') }}",
            type: "GET",
            success: function(response) {
                const yearSelect = $('#years');
                yearSelect.empty();

                if (response.length === 0) {
                    // If no years available, add current year
                    const currentYear = new Date().getFullYear();
                    yearSelect.append(new Option(currentYear, currentYear, true, true));
                } else {
                    // Sort years in descending order
                    response.sort((a, b) => b - a);

                    // Add all available years
                    $.each(response, function(index, year) {
                        yearSelect.append(new Option(year, year, index === 0, index === 0));
                    });
                }

                // Trigger change to update Select2
                yearSelect.trigger('change');
            },
            error: function(error) {
                console.error("Error fetching years:", error);

                // Fallback to current year
                const currentYear = new Date().getFullYear();
                $('#years').append(new Option(currentYear, currentYear, true, true)).trigger('change');
            }
        });

        let charts = {};

        // Show loading indicator for a chart
        function showChartLoading(chartId) {
            $('#' + chartId).siblings('.chart-overlay').removeClass('d-none');
        }

        // Hide loading indicator for a chart
        function hideChartLoading(chartId) {
            $('#' + chartId).siblings('.chart-overlay').addClass('d-none');
        }

        // Event handler for the generate report button
        $('#generateReport').click(function() {
            const years = $('#years').val();
            const employeeIds = $('#employees').val();
            const $btn = $(this);

            // Save original button content
            const originalContent = $btn.html();

            // Show loading state
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Generating...');

            if (!employeeIds || employeeIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selection Required',
                    text: 'Please select at least one employee',
                    confirmButtonColor: '#3c8dbc'
                });
                $btn.prop('disabled', false).html(originalContent);
                return;
            }

            if (!years || years.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selection Required',
                    text: 'Please select at least one year',
                    confirmButtonColor: '#3c8dbc'
                });
                $btn.prop('disabled', false).html(originalContent);
                return;
            }

            // Show the report content with animation
            $('#reportContent').removeClass('d-none').hide().fadeIn(500);

            // Show loading indicators for all charts
            const chartIds = [
                'performanceChart', 'performanceScoreChart', 'disciplineChart',
                'disciplineScoreChart', 'elearningChart', 'elearningScoreChart',
                'finalScoreChart', 'finalGradeChart', 'radarChart'
            ];

            chartIds.forEach(showChartLoading);

            // Fetch data from API
            $.ajax({
                url: "{{ route('evaluation.report.final.graph.data') }}",
                type: "GET",
                data: {
                    years: years,
                    employees: employeeIds
                },
                success: function(response) {
                    renderCharts(response);

                    // Show success notification
                    toastr.success('Report generated successfully!');

                    // Scroll to report section
                    $('html, body').animate({
                        scrollTop: $('#reportContent').offset().top - 20
                    }, 500);
                },
                error: function(error) {
                    console.error("Error fetching data:", error);

                    Swal.fire({
                        icon: 'error',
                        title: 'Data Loading Error',
                        text: 'Error loading evaluation data. Please try again.',
                        confirmButtonColor: '#3c8dbc'
                    });

                    // Hide report section
                    $('#reportContent').addClass('d-none');
                },
                complete: function() {
                    // Restore button state
                    $btn.prop('disabled', false).html(originalContent);

                    // Hide loading indicators
                    chartIds.forEach(hideChartLoading);
                }
            });
        });

        // Modified renderCharts function to use the selected years
        function renderCharts(data) {
            // Destroy existing charts to prevent duplicates
            Object.keys(charts).forEach(key => {
                if (charts[key]) {
                    charts[key].destroy();
                }
            });

            // Create Performance chart
            charts.performance = createGradeLineChart(
                'performanceChart',
                'Performance Grade Distribution',
                data
            );

            // Create Discipline chart
            charts.discipline = createGradeLineChart(
                'disciplineChart',
                'Discipline Grade Distribution',
                data
            );

            // Create E-Learning chart
            charts.elearning = createGradeLineChart(
                'elearningChart',
                'E-Learning Grade Distribution',
                data
            );

            // Create Final Grade chart
            charts.finalGrade = createGradeLineChart(
                'finalGradeChart',
                'Final Grade Distribution',
                data
            );

            // Create Radar Chart for overall performance
            charts.radar = createEnhancedRadarChart(
                'radarChart',
                data
            );

            // Keep score comparison charts as bar charts
            charts.performanceScore = createScoreComparisonChart(
                'performanceScoreChart',
                'Performance Scores',
                data,
                'performance_score',
                'rgba(40, 167, 69, 0.7)',
                'rgb(40, 167, 69)'
            );

            charts.disciplineScore = createScoreComparisonChart(
                'disciplineScoreChart',
                'Discipline Scores',
                data,
                'discipline_score',
                'rgba(220, 53, 69, 0.7)',
                'rgb(220, 53, 69)'
            );

            charts.elearningScore = createScoreComparisonChart(
                'elearningScoreChart',
                'E-Learning Scores',
                data,
                'elearning_score',
                'rgba(102, 16, 242, 0.7)',
                'rgb(102, 16, 242)'
            );

            charts.finalScore = createScoreComparisonChart(
                'finalScoreChart',
                'Final Scores',
                data,
                'final_score',
                'rgba(33, 37, 41, 0.7)',
                'rgb(33, 37, 41)'
            );
        }

        function createGradeLineChart(canvasId, label, data) {
            const ctx = document.getElementById(canvasId).getContext('2d');

            // Extract unique employees and years (from actual data, not from selection)
            const employees = [...new Set(data.map(item => item.employee_name))];
            const years = [...new Set(data.map(item => item.year))].sort(); // Sort years in ascending order

            // Define your grade order from highest (A) to lowest (F)
            const gradeOrder = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'D-', 'E+', 'E', 'F'];

            // Determine which field to use based on chart type
            const metricType = label.toLowerCase().includes('performance') ? 'performance' :
                label.toLowerCase().includes('discipline') ? 'discipline' :
                label.toLowerCase().includes('learning') ? 'elearning' : 'final_grade';

            // Prepare datasets for each employee
            const datasets = [];

            employees.forEach((employee, index) => {
                // Filter data for this employee
                const employeeData = data.filter(item => item.employee_name === employee);

                // Get employee grades across years
                const gradeData = [];

                // Create points for line chart
                years.forEach(year => {
                    const yearData = employeeData.find(item => item.year == year);
                    if (yearData) {
                        gradeData.push({
                            x: year,
                            y: yearData[metricType],
                            grade: yearData[metricType],
                            score: yearData[`${metricType}_score`] || 0
                        });
                    }
                });

                // Generate a unique color for this employee
                const hue = (index * 137) % 360;
                const color = `hsl(${hue}, 70%, 50%)`;

                datasets.push({
                    label: employee,
                    data: gradeData,
                    borderColor: color,
                    backgroundColor: `hsla(${hue}, 70%, 60%, 0.2)`,
                    borderWidth: 3,
                    tension: 0.4,
                    pointRadius: 6,
                    pointBackgroundColor: color,
                    pointBorderColor: '#fff',
                    pointHoverRadius: 8,
                    fill: false
                });
            });

            return new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'category',
                            labels: years,
                            title: {
                                display: true,
                                text: 'Year',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            type: 'category',
                            labels: gradeOrder,
                            reverse: false,
                            title: {
                                display: true,
                                text: 'Grade',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            padding: 12,
                            callbacks: {
                                title: function(tooltipItems) {
                                    return `${tooltipItems[0].dataset.label} - ${tooltipItems[0].raw.x}`;
                                },
                                label: function(context) {
                                    return [
                                        `Grade: ${context.raw.grade}`,
                                        `Score: ${context.raw.score}`
                                    ];
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 12
                                },
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }

        function createScoreComparisonChart(canvasId, label, data, scoreKey, backgroundColor, borderColor) {
            const ctx = document.getElementById(canvasId).getContext('2d');

            // Group by year and employee
            const yearGroups = {};
            const years = [...new Set(data.map(item => item.year))].sort();

            years.forEach(year => {
                yearGroups[year] = data.filter(item => item.year == year);
            });

            // Create datasets for each year
            const datasets = [];

            years.forEach((year, yearIndex) => {
                // Generate a color for this year
                const hue = (yearIndex * 137) % 360;
                const bgColor = yearIndex === 0 ? backgroundColor : `hsla(${hue}, 70%, 60%, 0.7)`;
                const bdColor = yearIndex === 0 ? borderColor : `hsl(${hue}, 70%, 50%)`;

                datasets.push({
                    label: `Year ${year}`,
                    data: yearGroups[year].map(item => item[scoreKey]),
                    backgroundColor: bgColor,
                    borderColor: bdColor,
                    borderWidth: 1,
                    borderRadius: 4,
                    hoverBackgroundColor: `hsla(${hue}, 70%, 50%, 0.9)`,
                    hoverBorderColor: bdColor
                });
            });

            // Get employee names (using the first year's data for labels)
            const employeeLabels = yearGroups[years[0]] ? yearGroups[years[0]].map(item => item.employee_name) : [];

            return new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: employeeLabels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true,
                                drawBorder: false
                            },
                            ticks: {
                                precision: 0
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            padding: 12,
                            displayColors: true,
                            callbacks: {
                                title: function(tooltipItems) {
                                    return tooltipItems[0].label;
                                },
                                label: function(context) {
                                    const datasetLabel = context.dataset.label || '';
                                    const value = context.raw !== null ? context.raw : 0;
                                    return `${datasetLabel}: ${value.toFixed(2)}`;
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 12
                                },
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'rectRounded'
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }

        function createEnhancedRadarChart(canvasId, data) {
            const ctx = document.getElementById(canvasId).getContext('2d');

            const datasets = [];
            const uniqueEmployees = {};

            // Group by employee and year
            data.forEach(item => {
                const key = `${item.employee_name}_${item.year}`;
                if (!uniqueEmployees[key]) {
                    uniqueEmployees[key] = {
                        employeeName: item.employee_name,
                        year: item.year,
                        data: item
                    };
                }
            });

            // Create a dataset for each employee-year combination
            let index = 0;
            Object.values(uniqueEmployees).forEach(employee => {
                // Generate a unique color
                const hue = (index * 137) % 360;
                const color = `hsla(${hue}, 70%, 60%, 0.5)`;
                const borderColor = `hsl(${hue}, 70%, 50%)`;

                datasets.push({
                    label: `${employee.employeeName} (${employee.year})`,
                    data: [
                        employee.data.performance_score,
                        employee.data.discipline_score,
                        employee.data.elearning_score,
                        employee.data.final_score
                    ],
                    backgroundColor: color,
                    borderColor: borderColor,
                    borderWidth: 2,
                    pointBackgroundColor: borderColor,
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: borderColor,
                    pointRadius: 5,
                    pointHoverRadius: 7
                });

                index++;
            });



            return new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: ['Performance', 'Discipline', 'E-Learning', 'Final Score'],
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            beginAtZero: true,
                            min: 0,
                            max: 5,
                            ticks: {
                                stepSize: 1
                            },
                            pointLabels: {
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            padding: 10,
                            cornerRadius: 6,
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 13
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush