@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center mb-5">
            <h1 class="text-warning">
                <i class="fas fa-clipboard-check"></i> Employee Performance Report
            </h1>
        </div>
    </div>
    <div class="row">
        <!-- Filter Card -->
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-filter me-2"></i> Report Filters
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="showGradeToggle">
                        <label class="form-check-label text-white" for="showGradeToggle">Show Grades</label>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('evaluation.report.performance.index') }}" method="GET" id="filterForm">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="filter-name" class="form-label">Employees</label>
                                <select id="filter-name" name="employee" class="form-select">
                                    <option value="">All Employees</option>
                                    @foreach($employeesList as $emp)
                                    <option value="{{ $emp->id }}" {{ request('employee') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="filter-position" class="form-label">Positions</label>
                                <select id="filter-position" name="position" class="form-select">
                                    <option value="">All Positions</option>
                                    @foreach($positionsList as $position)
                                    <option value="{{ $position->id }}" {{ request('position') == $position->id ? 'selected' : '' }}>
                                        {{ $position->position }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="filter-department" class="form-label">Departments</label>
                                <select id="filter-department" name="department" class="form-select">
                                    <option value="">All Departments</option>
                                    @foreach($departmentsList as $department)
                                    <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                                        {{ $department->department }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="filter-year" class="form-label">Year</label>
                                <select id="filter-year" name="year" class="form-select">
                                    @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ (request('year', $currentYear) == $year) ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i> Filter
                                </button>
                                <a href="{{ route('evaluation.report.performance.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Grade Rules Section -->
        <div class="col-12 mb-4" id="gradeRulesCard">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-award me-2"></i> Performance Grade Levels
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Grade</th>
                                            <th>Min Score</th>
                                            <th>Max Score</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($gradeRules as $rule)
                                        <tr>
                                            <td class="fw-bold">
                                                <span class="badge 
                                            @if($rule->grade == 'A') bg-success 
                                            @elseif($rule->grade == 'B') bg-info 
                                            @elseif($rule->grade == 'C') bg-warning 
                                            @elseif($rule->grade == 'D') bg-danger 
                                            @else bg-secondary 
                                            @endif
                                            text-white">
                                                    {{ $rule->grade }}
                                                </span>
                                            </td>
                                            <td>{{ $rule->min_score }}</td>
                                            <td>{{ $rule->max_score }}</td>
                                            <td>{{ $rule->description }}</td>
                                        </tr>
                                        @endforeach
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
                                    <em>Evaluation → Rule → Performance → Grade</em>
                                    <br>
                                    in the menu.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Data Table Card -->
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-chart-bar me-2"></i> Report Evaluation Performance
                    </h5>
                    <div>
                        <button id="btnExportPerformance" class="btn btn-success">
                            <i class="fas fa-file-excel me-1"></i> Export Excel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table id="evaluations-table" class="table table-bordered table-striped mb-2 pt-3 align-middle align-items-center table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Employee Name</th>
                                    <th>Position</th>
                                    <th>Department</th>
                                    <th>Evaluation Period</th>
                                    <th>Evaluator</th>
                                    <th>Score</th>
                                    <th>Deduction</th>
                                    <th>Final Score</th>
                                    <th>Grade</th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>

                                @foreach($evaluations as $key => $evaluation)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $evaluation->user->name ?? 'N/A' }}</td>
                                    <td>{{ $evaluation->user->position->position ?? 'N/A' }}</td>
                                    <td>{{ $evaluation->user->department->department ?? 'N/A' }}</td>
                                    <td>{{ date('F Y', strtotime($evaluation->date)) }}</td>
                                    <td>{{ $evaluation->evaluator->name ?? 'N/A' }}</td>
                                    <td class="fw-bold">{{ $evaluation->total_score }}</td>
                                    <td class="text-danger">{{ $evaluation->total_reduction }}</td>
                                    <td class="fw-bold text-primary">
                                        {{ $evaluation->total_score - $evaluation->total_reduction }}
                                    </td>
                                    <td class="fw-bold">
                                        @php
                                        $gradeClass = '';
                                        $gradeValue = $evaluation->grade ?? '';

                                        if ($gradeValue === '') {
                                        $gradeClass = '';
                                        } else {
                                        switch ($gradeValue) {
                                        case 'A': $gradeClass = 'bg-success text-white'; break;
                                        case 'B': $gradeClass = 'bg-info text-white'; break;
                                        case 'C': $gradeClass = 'bg-warning'; break;
                                        case 'D': $gradeClass = 'bg-danger text-white'; break;
                                        default: $gradeClass = 'bg-secondary text-white';
                                        }
                                        }
                                        @endphp

                                        @if ($gradeValue !== '')
                                        <span class="badge {{ $gradeClass }}">{{ $gradeValue }}</span>
                                        @else
                                        <span class="text-muted">—</span> {{-- tampilkan strip atau kosong --}}
                                        @endif
                                    </td>

                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('evaluation.report.performance.detail', $evaluation->user_id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </div>
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
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#evaluations-table').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true
        });

        // Show/hide grade rules card with toggle
        $('#showGradeToggle').change(function() {
            if ($(this).is(':checked')) {
                $('#gradeRulesCard').slideDown(300);
            } else {
                $('#gradeRulesCard').slideUp(300);
            }
        });

        // Check if there's a stored preference for showing grades
        // Default to false if no preference is set
        const showGrades = localStorage.getItem('showGrades') === 'true';
        $('#showGradeToggle').prop('checked', showGrades);

        // Set initial state based on preference
        if (showGrades) {
            $('#gradeRulesCard').show();
        } else {
            $('#gradeRulesCard').hide();
        }

        // Store grade visibility preference
        $('#showGradeToggle').change(function() {
            localStorage.setItem('showGrades', $(this).is(':checked'));
        });


        $('#btnExportPerformance').click(function(e) {
            e.preventDefault();
            
            // Get any filter values from your form
            const year = $('#year').val() || '{{ date('Y') }}';
            const employeeId = $('#employee').val() || '';
            const departmentId = $('#department').val() || '';
            const positionId = $('#position').val() || '';
            
            // Show loading spinner on button
            $(this).prop('disabled', true)
                .html('<i class="fa fa-spinner fa-spin"></i> Preparing data...');
                
            // Create loading overlay
            showLoadingOverlay('Generating performance report...');
            
            // Construct URL with parameters
            let url = '{{ route('evaluation.report.performance.export') }}' + 
                    '?year=' + year + 
                    '&employee=' + employeeId + 
                    '&department=' + departmentId + 
                    '&position=' + positionId;
            
            // Use fetch API for the request
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
                let filename = 'performance_report.xlsx'; // fallback filename
                
                // Try to get filename from the header
                if (contentDisposition) {
                    const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    const matches = filenameRegex.exec(contentDisposition);
                    if (matches != null && matches[1]) {
                        filename = matches[1].replace(/['"]/g, '');
                    }
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
                        $('#btnExportPerformance').prop('disabled', false)
                            .html('<i class="fas fa-file-excel me-1"></i> Export Excel');
                    }, 1000);
                });
            })
            .catch(error => {
                console.error('Download failed:', error);
                hideLoadingOverlay();
                $('#btnExportPerformance').prop('disabled', false)
                    .html('<i class="fas fa-file-excel me-1"></i> Export Excel');
                    
                // Show error message to user
                Swal.fire({
                    icon: 'error',
                    title: 'Export Failed',
                    text: 'There was a problem generating the report. Please try again.',
                    confirmButtonColor: '#3085d6'
                });
            });
        });
        
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