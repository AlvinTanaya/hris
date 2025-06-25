@extends('layouts.app')

@section('content')
<style>
    /* Gaya khusus untuk tab yang aktif */
    .nav-tabs .nav-link.active {
        background-color: #0d6efd;
        /* Bootstrap primary */
        color: #fff !important;
        font-weight: bold;
    }
</style>

<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12 text-center mb-5">
            <h1 class="text-warning">
                <i class="fas fa-clipboard-check"></i> Employee Discipline Report
            </h1>
        </div>
    </div>


    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h4 class="m-0 font-weight-bold text-primary"><i class="fa fa-filter"></i> Report Filters</h4>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="showGradeSettings">
                <label class="form-check-label" for="showGradeSettings">Show Grade Settings</label>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="year" class="form-label">Year</label>
                        <select class="form-select" id="year">
                            @foreach($years as $year)
                            <option value="{{ $year }}" {{ $currentYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="employee" class="form-label">Employee Name</label>
                        <select class="form-select" id="employee">
                            <option value="">All Employees</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="department" class="form-label">Department</label>
                        <select class="form-select" id="department">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->department }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="position" class="form-label">Position</label>
                        <select class="form-select" id="position">
                            <option value="">All Positions</option>
                            @foreach($positions as $position)
                            <option value="{{ $position->id }}">{{ $position->position }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row mt-3 align-items-end">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="export_type" class="form-label">Export Type</label>
                        <select class="form-select" id="export_type">
                            <option value="monthly">All Monthly Reports</option>
                            <option value="final">Final Report Only</option>
                            <option value="all">Complete Report (All)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-start gap-2">
                        <button type="button" class="btn btn-success" id="btnExport">
                            <i class="fa fa-file-excel"></i> Export Excel
                        </button>
                        <button type="button" class="btn btn-primary" id="btnFilter">
                            <i class="fa fa-filter"></i> Apply Filter
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Grade Settings Card (Hidden by default) -->
    <div class="card shadow mb-4" id="gradeSettingsCard" style="display: none;">
        <div class="card-header py-3 bg-primary">
            <h6 class="m-0 font-weight-bold text-white">Grade Settings</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-9">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Grade</th>
                                    <th>Min Score</th>
                                    <th>Max Score</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody id="grade-settings-data">
                                <!-- Grade data will be loaded here -->
                                <tr>
                                    <td colspan="4" class="text-center">Loading grade settings...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle me-2"></i>
                        <p>
                            <strong>Note:</strong> Grade settings are loaded from the database.
                            If you want to modify or configure the Grade settings, please go to
                            <br>
                            <em>Evaluation → Rule → Discipline → Grade</em>
                            <br>
                            in the menu.
                        </p>

                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <button type="button" class="btn btn-secondary" id="btnCancelGrades">
                        <i class="fa fa-times"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Data Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <ul class="nav nav-tabs card-header-tabs d-flex w-100" id="month-tabs" role="tablist">
                @php
                $months = [
                '01' => 'January',
                '02' => 'February',
                '03' => 'March',
                '04' => 'April',
                '05' => 'May',
                '06' => 'June',
                '07' => 'July',
                '08' => 'August',
                '09' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December',
                'final' => 'Final'
                ];
                @endphp

                @foreach($months as $key => $monthName)
                <li class="nav-item"
                    style="flex: {{ $key === 'final' ? '2' : '1' }}; text-align: center;">
                    <a class="nav-link {{ $key == $currentMonth ? 'active' : '' }}"
                        style="border-radius: 5px; padding: 10px 15px; margin: 2px;"
                        id="tab-{{ $key }}"
                        data-bs-toggle="tab"
                        href="#content-{{ $key }}"
                        role="tab"
                        aria-controls="content-{{ $key }}"
                        aria-selected="{{ $key == $currentMonth ? 'true' : 'false' }}">
                        {{ $monthName }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="month-tab-content">
                @foreach($months as $key => $monthName)
                <div class="tab-pane fade {{ $key == $currentMonth ? 'show active' : '' }}"
                    id="content-{{ $key }}"
                    role="tabpanel"
                    aria-labelledby="tab-{{ $key }}">

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="discipline-table-{{ $key }}">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" class="align-middle bg-success text-dark">NIK</th>
                                    <th rowspan="2" class="align-middle bg-success text-dark">NAMA</th>
                                    <th rowspan="2" class="align-middle bg-success text-dark">JABATAN</th>
                                    <th rowspan="2" class="align-middle bg-success text-dark">DEPARTEMEN</th>
                                    <th rowspan="2" class="align-middle bg-success text-dark">TTL KERJA/BL</th>
                                    <th rowspan="2" class="align-middle bg-success text-dark">KEHADIRAN</th>
                                    <th rowspan="2" class="align-middle bg-success text-dark">%</th>
                                    <th rowspan="2" class="align-middle bg-success text-dark">TERLAMBAT</th>
                                    <th rowspan="2" class="align-middle bg-success text-dark">IJIN</th>
                                    <th rowspan="2" class="align-middle bg-success text-dark">MASUK SIANG</th>
                                    <th rowspan="2" class="align-middle bg-success text-dark">PULANG AWAL</th>
                                    <th rowspan="2" class="align-middle bg-success text-dark">SAKIT</th>
                                    <th rowspan="2" class="align-middle bg-success text-dark">PENGURANGAN</th>
                                    <th rowspan="2" class="align-middle bg-success text-dark">ST</th>
                                    <th rowspan="2" class="align-middle bg-success text-dark">SP</th>
                                    <th colspan="6" class="text-center bg-success text-dark">SCORE</th>
                                    <th rowspan="2" class="align-middle bg-success text-dark">TOTAL</th>
                                    @if($key == 'final')
                                    <th rowspan="2" class="align-middle bg-success text-dark">GRADE</th>
                                    @endif
                                </tr>
                                <tr>
                                    <th class="align-middle bg-success text-dark">KEHADIRAN</th>
                                    <th class="align-middle bg-success text-dark">TERLAMBAT</th>
                                    <th class="align-middle bg-success text-dark">MASUK SIANG</th>
                                    <th class="align-middle bg-success text-dark">PULANG AWAL</th>
                                    <th class="align-middle bg-success text-dark">ST</th>
                                    <th class="align-middle bg-success text-dark">SP</th>
                                </tr>
                            </thead>
                            <tbody id="discipline-data-{{ $key }}">
                                <!-- Data will be loaded here -->
                                <tr>
                                    <td colspan="{{ $key == 'final' ? '22' : '21' }}" class="text-center">
                                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        Loading data...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Load data for the active tab on page load
        loadDisciplineData();

        // Filter button click
        $('#btnFilter').click(function() {
            loadDisciplineData();
        });

        // Export to Excel button
        $('#btnExport').click(function() {
            exportToExcel();
        });

        // Tab change event 
        $('#month-tabs a').on('click', function(e) {
            e.preventDefault();
            $(this).tab('show');

            // Extract month from tab id
            const month = $(this).attr('id').replace('tab-', '');
            loadDisciplineData(month);
        });

        // Grade settings toggle
        $('#showGradeSettings').change(function() {
            if ($(this).is(':checked')) {
                loadGradeSettings();
                $('#gradeSettingsCard').slideDown();
            } else {
                $('#gradeSettingsCard').slideUp();
            }
        });

        $('#btnCancelGrades').click(function() {
            $('#showGradeSettings').prop('checked', false);
            $('#gradeSettingsCard').slideUp();
        });

        function loadGradeSettings() {
            $.ajax({
                url: '{{ route("evaluation.report.discipline.grades") }}',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    renderGradeSettings(response);
                },
                error: function(xhr) {
                    console.error('Error loading grade settings:', xhr);
                    $('#grade-settings-data').html('<tr><td colspan="4" class="text-center">Error loading grade settings. Please try again.</td></tr>');
                }
            });
        }

        function renderGradeSettings(data) {
            if (!data.length) {
                $('#grade-settings-data').html('<tr><td colspan="4" class="text-center">No grade settings available</td></tr>');
                return;
            }

            let html = '';
            data.forEach(function(item) {
                html += `<tr>
                    <td><span class="badge bg-${getGradeBadgeColor(item.grade)}">${item.grade}</span></td>
                    <td>${item.min_score}</td>
                    <td>${item.max_score || 'No limit'}</td>
                    <td>${item.description || ''}</td>
                </tr>`;
            });

            $('#grade-settings-data').html(html);
        }

        function getGradeBadgeColor(grade) {
            switch (grade) {
                case 'A':
                    return 'success';
                case 'B':
                    return 'primary';
                case 'C':
                    return 'info';
                case 'D':
                    return 'warning';
                case 'E':
                    return 'danger';
                default:
                    return 'secondary';
            }
        }



        function loadDisciplineData(tabMonth = null) {
            // Get active tab month if not specified
            const month = tabMonth || $('.nav-link.active').attr('id').replace('tab-', '');
            const year = $('#year').val();
            const employeeId = $('#employee').val();
            const departmentId = $('#department').val();
            const positionId = $('#position').val();

            // Show loading message with spinner
            $(`#discipline-data-${month}`).html(`
    <tr>
        <td colspan="${month === 'final' ? '23' : '22'}" class="text-center">
            <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            Loading data...
        </td>
    </tr>
`);
            // Make AJAX request
            $.ajax({
                url: '{{ route("evaluation.report.discipline.data") }}',
                type: 'GET',
                timeout: 900000, // 15 minutes in milliseconds (15 * 60 * 1000)
                data: {
                    month: month,
                    year: year,
                    employee_id: employeeId,
                    department_id: departmentId,
                    position_id: positionId
                },
                dataType: 'json',
                success: function(response) {
                    renderTable(response, month);
                },
                error: function(xhr) {
                    console.error('Error loading data:', xhr);
                    $(`#discipline-data-${month}`).html(`
    <tr>
        <td colspan="${month === 'final' ? '23' : '22'}" class="text-center">
            <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            Loading data...
        </td>
    </tr>
`);
                }
            });
        }



        function renderTable(data, month) {
            if (!data.length) {
                $(`#discipline-data-${month}`).html(`
            <tr>
                <td colspan="${month === 'final' ? '23' : '22'}" class="text-center">
                    <div class="alert alert-info mb-0">
                        <i class="fa fa-info-circle me-2"></i>
                        No data available
                    </div>
                </td>
            </tr>
        `);
                return;
            }

            let html = '';
            data.forEach(function(item) {
                // Apply background color based on total score
                let rowClass = '';
                if (item.total_score === null || item.total_score === '' || item.total_score <= 0) {
                    rowClass = 'table-danger';
                }

                // Calculate reduction (pengurangan) - ADD THIS CODE HERE
                // Inside your renderTable function where you calculate the reduction
                let reduction = '';
                if (item.working_days && item.presence) {
                    const workingDays = parseInt(item.working_days);
                    const presence = parseInt(item.presence);
                    const diff = workingDays - presence; // Note: This should be workingDays - presence
                    if (diff > 0) {
                        reduction = `${diff}`; // Use negative sign to indicate reduction
                    }
                }

                // Then add this value to your HTML table row:
                html += `<tr class="${rowClass}">
    <td>${item.employee_id}</td>
    <td>${item.name}</td>
    <td>${item.position || ''}</td>
    <td>${item.department || ''}</td>
    <td>${item.working_days || ''}</td>
    <td>${item.presence || ''}</td>
    <td>${item.attendance_percentage || ''}</td>
    <td>${item.late_arrivals || ''}</td>
    <td>${item.permission || ''}</td>
    <td>${item.afternoon_shift_count || ''}</td>
    <td>${item.early_departures || ''}</td>
    <td>${item.sick_leave || ''}</td>
    <td>${reduction || ''}</td> <!-- This is where you need to add the reduction value -->
    <td>${item.st_count || ''}</td>
    <td>${item.sp_count || ''}</td>
    <td>${item.attendance_score || ''}</td>
    <td>${item.late_score || ''}</td>
    <td>${item.afternoon_shift_score || ''}</td>
    <td>${item.early_departure_score || ''}</td>
    <td>${item.st_score || ''}</td>
    <td>${item.sp_score || ''}</td>
    <td class="fw-bold">${item.total_score || ''}</td>`;

                // Add grade column for final view
                if (month === 'final') {
                    let gradeClass = '';
                    if (item.grade) {
                        gradeClass = `badge bg-${getGradeBadgeColor(item.grade)}`;
                    }
                    html += `<td><span class="${gradeClass}">${item.grade || ''}</span></td>`;
                }

                html += `</tr>`;
            });

            $(`#discipline-data-${month}`).html(html);
        }


        function exportToExcel() {
            const month = $('.nav-link.active').attr('id').replace('tab-', '');
            const year = $('#year').val();
            const employeeId = $('#employee').val();
            const departmentId = $('#department').val();
            const positionId = $('#position').val();
            const exportType = $('#export_type').val();

            // Show loading spinner with progress message
            $('#btnExport').prop('disabled', true)
                .html('<i class="fa fa-spinner fa-spin"></i> Preparing data...');

            // Create loading overlay for better UX
            showLoadingOverlay('Generating report...');

            // Construct URL with parameters
            let url = `{{ route('evaluation.report.discipline.export') }}?year=${year}&employee_id=${employeeId}&department_id=${departmentId}&position_id=${positionId}&export_type=${exportType}`;

            // Add month parameter only for single month export
            if (exportType === 'single') {
                url += `&month=${month}`;
            }

            // Use fetch API for better control
            fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    // Update loading message
                    updateLoadingMessage('Downloading file...');

                    // Get filename from Content-Disposition header
                    const contentDisposition = response.headers.get('Content-Disposition');
                    let filename = 'discipline_report.xlsx'; // fallback filename

                    // Try to get filename from the header
                    try {
                        if (contentDisposition) {
                            const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                            const matches = filenameRegex.exec(contentDisposition);
                            if (matches != null && matches[1]) {
                                filename = matches[1].replace(/['"]/g, '');
                            }
                        }
                    } catch (e) {
                        console.error('Error parsing filename:', e);
                    }

                    // Convert response to blob for download
                    return response.blob().then(blob => {
                        // Create blob URL and trigger download
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.href = url;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();

                        // Cleanup
                        window.URL.revokeObjectURL(url);
                        setTimeout(() => {
                            document.body.removeChild(a);
                            hideLoadingOverlay();
                            $('#btnExport').prop('disabled', false)
                                .html('<i class="fa fa-file-excel"></i> Export Excel');
                        }, 1000);
                    });
                })
                .catch(error => {
                    console.error('Download failed:', error);
                    hideLoadingOverlay();
                    $('#btnExport').prop('disabled', false)
                        .html('<i class="fa fa-file-excel"></i> Export Excel');

                    // Show error message to user
                    Swal.fire({
                        icon: 'error',
                        title: 'Export Failed',
                        text: 'There was a problem generating the report. Please try again.',
                        confirmButtonColor: '#3085d6'
                    });
                });
        }

        // Helper functions for loading overlay
        function showLoadingOverlay(message) {
            // Create overlay if it doesn't exist
            if ($('#export-loading-overlay').length === 0) {
                const overlay = `
            <div id="export-loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                background-color: rgba(0,0,0,0.5); z-index: 9999; display: flex; justify-content: center; 
                align-items: center; flex-direction: column;">
                <div style="background-color: white; padding: 20px; border-radius: 5px; text-align: center;">
                    <i class="fa fa-spinner fa-spin fa-3x"></i>
                    <div id="export-loading-message" style="margin-top: 10px; font-size: 16px;">Preparing data...</div>
                </div>
            </div>
        `;
                $('body').append(overlay);
            } else {
                $('#export-loading-overlay').show();
            }

            if (message) {
                $('#export-loading-message').text(message);
            }
        }

        function updateLoadingMessage(message) {
            $('#export-loading-message').text(message);
        }

        function hideLoadingOverlay() {
            $('#export-loading-overlay').hide();
        }
    });
</script>
@endpush