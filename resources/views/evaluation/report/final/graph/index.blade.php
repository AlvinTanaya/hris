@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-chart-line me-2"></i>Employee Evaluation Report</h4>
                </div>
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group mb-3">
                                <label for="years" class="form-label fw-bold">Select Years (Max 5)</label>
                                <select class="form-control select2 border-0 shadow-sm" id="years" name="years[]" multiple="multiple">
                                    <!-- Will be populated via AJAX -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group mb-3">
                                <label for="employees" class="form-label fw-bold">Select Employees (Max 5)</label>
                                <select class="form-control select2 border-0 shadow-sm" id="employees" name="employees[]" multiple="multiple">
                                    <!-- Will be populated via AJAX -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" id="generateReport" class="btn btn-primary w-100 shadow-sm">
                                <i class="fas fa-chart-bar me-2"></i> Generate Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="reportContent" class="d-none">
        <!-- First row - Performance -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-lg h-100">
                    <div class="card-header text-white" style="background-color: #00c0ef;">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Performance Grade Distribution</h5>
                    </div>
                    <div class="card-body" style="height: 300px;">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-lg h-100">
                    <div class="card-header text-white" style="background-color: #00a65a;">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Performance Score Comparison</h5>
                    </div>
                    <div class="card-body" style="height: 300px;">
                        <canvas id="performanceScoreChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second row - Discipline -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-lg h-100">
                    <div class="card-header text-white" style="background-color: #f39c12;">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Discipline Grade Distribution</h5>
                    </div>
                    <div class="card-body" style="height: 300px;">
                        <canvas id="disciplineChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-lg h-100">
                    <div class="card-header text-white" style="background-color: #dd4b39;">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Discipline Score Comparison</h5>
                    </div>
                    <div class="card-body" style="height: 300px;">
                        <canvas id="disciplineScoreChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Third row - E-Learning -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-lg h-100">
                    <div class="card-header text-white" style="background-color: #3c8dbc;">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>E-Learning Grade Distribution</h5>
                    </div>
                    <div class="card-body" style="height: 300px;">
                        <canvas id="elearningChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-lg h-100">
                    <div class="card-header text-white" style="background-color: #605ca8;">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>E-Learning Score Comparison</h5>
                    </div>
                    <div class="card-body" style="height: 300px;">
                        <canvas id="elearningScoreChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fourth row - Final scores -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-lg h-100">
                    <div class="card-header text-white" style="background-color: #7e57c2;">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Final Grade Distribution</h5>
                    </div>
                    <div class="card-body" style="height: 300px;">
                        <canvas id="finalGradeChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-lg h-100">
                    <div class="card-header text-white" style="background-color: #222d32;">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Final Score Comparison</h5>
                    </div>
                    <div class="card-body" style="height: 300px;">
                        <canvas id="finalScoreChart"></canvas>
                    </div>
                </div>
            </div>

        </div>

        <!-- Fifth row - Radar chart -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-header text-white" style="background-color: #00a65a;">
                        <h5 class="mb-0"><i class="fas fa-radar me-2"></i>Overall Performance Radar</h5>
                    </div>
                    <div class="card-body" style="height: 400px;">
                        <canvas id="radarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<style>
    .select2-container .select2-selection--multiple {
        min-height: 38px;
    }

    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, .175) !important;
    }

    .select2-container--default .select2-selection--multiple {
        border-radius: 0.375rem;
    }

    .form-select:focus,
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .btn {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        transition: all 0.15s ease-in-out;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    /* Improved Select2 styling */
    .select2-container {
        width: 100% !important;
    }

    .select2-selection__choice {
        background-color: #3c8dbc !important;
        color: white !important;
        border: none !important;
        padding: 2px 8px !important;
        margin: 3px !important;
        border-radius: 3px !important;
    }

    .select2-selection__choice__remove {
        color: white !important;
        margin-right: 5px !important;
    }

    .select2-search__field {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--multiple {
        background-color: white;
        border: 1px solid #ced4da;
        padding: 5px;
    }

    /* Custom tooltip styling */
    .chart-tooltip {
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 12px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
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

        // Initialize Select2 for employees
        $('#employees').select2({
            placeholder: 'Select employees (max 5)',
            maximumSelectionLength: 5,
            width: '100%',
            ajax: {
                url: "{{ route('api.employees') }}",
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });

        // Initialize Select2 for years
        $('#years').select2({
            placeholder: 'Select years (max 5)',
            maximumSelectionLength: 5,
            width: '100%'
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
            },
            error: function(error) {
                console.error("Error fetching years:", error);

                // Fallback to current year
                const currentYear = new Date().getFullYear();
                $('#years').append(new Option(currentYear, currentYear, true, true));
            }
        });

        let charts = {};

        // Add loading indicator
        function showLoading(elementId) {
            const element = $('#' + elementId);
            element.html('<div class="d-flex justify-content-center align-items-center h-100"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        }

        // Event handler for the generate report button
        $('#generateReport').click(function() {
            const years = $('#years').val();
            const employeeIds = $('#employees').val();

            if (!employeeIds || employeeIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selection Required',
                    text: 'Please select at least one employee',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            if (!years || years.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selection Required',
                    text: 'Please select at least one year',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // Show the report content
            $('#reportContent').removeClass('d-none');

            // Show loading indicators
            ['performanceChart', 'performanceScoreChart', 'disciplineChart',
                'disciplineScoreChart', 'elearningChart', 'elearningScoreChart',
                'finalScoreChart', 'finalGradeChart', 'radarChart'
            ].forEach(showLoading);

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
                },
                error: function(error) {
                    console.error("Error fetching data:", error);

                    Swal.fire({
                        icon: 'error',
                        title: 'Data Loading Error',
                        text: 'Error loading evaluation data. Please try again.',
                        confirmButtonColor: '#3085d6'
                    });

                    // Hide report section
                    $('#reportContent').addClass('d-none');
                }
            });
        });

        // Modify your renderCharts function to use the new chart type
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
                'rgba(0, 166, 90, 0.7)',
                'rgb(0, 166, 90)'
            );

            charts.disciplineScore = createScoreComparisonChart(
                'disciplineScoreChart',
                'Discipline Scores',
                data,
                'discipline_score',
                'rgba(221, 75, 57, 0.7)',
                'rgb(221, 75, 57)'
            );

            charts.elearningScore = createScoreComparisonChart(
                'elearningScoreChart',
                'E-Learning Scores',
                data,
                'elearning_score',
                'rgba(96, 92, 168, 0.7)',
                'rgb(96, 92, 168)'
            );

            charts.finalScore = createScoreComparisonChart(
                'finalScoreChart',
                'Final Scores',
                data,
                'final_score',
                'rgba(34, 45, 50, 0.7)',
                'rgb(34, 45, 50)'
            );
        }

        function createGradeLineChart(canvasId, label, data) {
            const ctx = document.getElementById(canvasId).getContext('2d');

            // Extract unique employees and years
            const employees = [...new Set(data.map(item => item.employee_name))];
            const years = [...new Set(data.map(item => item.year))].sort();

            // Define your grade order
            const gradeOrder = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'D-', 'E', 'F'];

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
                            x: yearData[metricType], // Grade as x-value
                            y: year, // Year as y-value
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
                            labels: gradeOrder,
                            title: {
                                display: true,
                                text: 'Grade',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        y: {
                            type: 'category',
                            labels: years,
                            reverse: false,
                            title: {
                                display: true,
                                text: 'Year',
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
                            callbacks: {
                                title: function(tooltipItems) {
                                    return `${tooltipItems[0].dataset.label} - ${tooltipItems[0].raw.y}`;
                                },
                                label: function(context) {
                                    return `Grade: ${context.raw.grade} (Score: ${context.raw.score})`;
                                }
                            }
                        },
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });
        }


        function createScoreComparisonChart(canvasId, label, data, scoreKey, backgroundColor, borderColor) {
            const ctx = document.getElementById(canvasId).getContext('2d');

            // Group by year and employee
            const yearGroups = {};
            const years = [...new Set(data.map(item => item.year))];

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
                    borderWidth: 1
                });
            });

            // Get employee names (using the first year's data for labels)
            const employeeLabels = yearGroups[years[0]].map(item => item.employee_name);

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
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const datasetLabel = context.dataset.label || '';
                                    const value = context.raw;
                                    return `${datasetLabel}: ${value}`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Modified function for radar chart
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
                const color = `hsla(${hue}, 70%, 60%, 0.7)`;
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
                    pointRadius: 4
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