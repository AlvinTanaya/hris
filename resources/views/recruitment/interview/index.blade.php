@extends('layouts.app')

@section('content')



<div class="container test">

    <div class="page-header">
        <div class="container">
            <div class="d-flex align-items-center justify-content-center">
                <div class="text-center">
                    <h1 class="page-title"><i class="fas fa-users-viewfinder me-2"></i>Interview Management</h1>
                    <p class="text-white-50 mt-2 mb-0">View and manage labor demands and applicant interviews</p>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-filter me-2"></i>Filter Labor Demands</h5>
            <span class="badge bg-primary">{{ count($demand) }} Records</span>
        </div>
        <div class="card-body">
            <form action="{{ route('recruitment.index.interview') }}" method="GET" class="row g-3">
                <div class="col-md-4 col-lg-4">
                    <label for="department_id" class="form-label">Department</label>
                    <select class="form-select" id="department_id" name="department_id">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->department }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-lg-4">
                    <label for="position_id" class="form-label">Position</label>
                    <select class="form-select" id="position_id" name="position_id">
                        <option value="">All Positions</option>
                        @foreach($positions as $pos)
                        <option value="{{ $pos->id }}" {{ request('position_id') == $pos->id ? 'selected' : '' }}>
                            {{ $pos->position }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-lg-4">
                    <label for="status_job" class="form-label">Job Status</label>
                    <select class="form-select" id="status_job" name="status_job">
                        <option value="">All Job Status</option>
                        @foreach($jobStatuses as $status)
                        <option value="{{ $status }}" {{ request('status_job') == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-lg-4">
                    <label for="opening_date" class="form-label">Opening Date</label>
                    <input type="date" class="form-control" id="opening_date" name="opening_date" value="{{ request('opening_date') }}">
                </div>
                <div class="col-md-6 col-lg-4">
                    <label for="closing_date" class="form-label">Closing Date</label>
                    <input type="date" class="form-control" id="closing_date" name="closing_date" value="{{ request('closing_date') }}">
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('recruitment.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-briefcase me-2"></i>Labor Demand List</h5>
            <div>
                <span class="badge bg-info">{{ date('F Y') }}</span>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="laborDemandTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Opened At</th>
                            <th>Closed At</th>
                            <th>Needed</th>
                            <th>Fulfilled</th>
                            <th>Applicants</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($demand as $item)
                        <tr>
                            <td>{{ $item->recruitment_demand_id }}</td>
                            <td>{{ $item->department_name }}</td>
                            <td><span class="fw-medium">{{ $item->position_name }}</span></td>
                            <td>{{ $item->opening_date }}</td>
                            <td>{{ $item->closing_date }}</td>
                            <td><span class="badge bg-primary">{{ $item->qty_needed }}</span></td>
                            <td><span class="badge bg-success">{{ $item->qty_fullfil }}</span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <span class="badge bg-warning px-2">Pending: {{ $item->pending_count }}</span>
                                    <span class="badge bg-info px-2">Approved: {{ $item->approved_count }}</span>
                                    <span class="badge bg-danger px-2">Declined: {{ $item->declined_count }}</span>
                                    <span class="badge bg-success px-2">Done: {{ $item->done_count }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('recruitment.applicant', $item->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fa-solid fa-users me-1"></i> Applicants
                                    </a>
                                    <button class="btn btn-info btn-sm view-btn"
                                        data-id="{{ $item->id }}"
                                        data-recruitment_demand_id="{{ $item->recruitment_demand_id }}"
                                        data-status="{{ $item->status_demand }}"
                                        data-department="{{ $item->department_name }}"
                                        data-position="{{ $item->position_name }}"
                                        data-opened="{{ $item->opening_date }}"
                                        data-closed="{{ $item->closing_date }}"
                                        data-status-job="{{ $item->status_job }}"
                                        data-reason="{{ $item->reason }}"
                                        data-needed="{{ $item->qty_needed }}"
                                        data-fullfill="{{ $item->qty_fullfil }}"
                                        data-gender="{{ $item->gender }}"
                                        data-job-goal="{{ $item->job_goal }}"
                                        data-education="{{ $item->education }}"
                                        data-major="{{ $item->major }}"
                                        data-experience="{{ $item->experience }}"
                                        data-length-of-working="{{ $item->length_of_working ?? 'N/A'}}"
                                        data-time-work-experience="{{ $item->time_work_experience }}"
                                        data-declined-reason="{{ $item->declined_reason ?? 'N/A' }}"
                                        data-skills="{{ $item->skills }}">
                                        <i class="fas fa-eye me-1"></i> Details
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-alt me-2"></i>Labor Demand Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <!-- Basic Information Card -->
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="info-group">
                                        <label>PTK ID</label>
                                        <p id="view-recruitment_demand_id" class="fw-bold"></p>
                                    </div>
                                    <div class="info-group">
                                        <label>Status</label>
                                        <p id="view-status" class="badge bg-primary"></p>
                                    </div>
                                    <div class="info-group">
                                        <label>Department</label>
                                        <p id="view-department"></p>
                                    </div>
                                    <div class="info-group">
                                        <label>Position</label>
                                        <p id="view-position" class="text-primary fw-bold"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Card -->
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Timeline & Status</h6>
                                </div>
                                <div class="card-body">
                                    <div class="timeline">
                                        <div class="timeline-item">
                                            <i class="fas fa-calendar-plus text-success"></i>
                                            <div>
                                                <label>Opened Date</label>
                                                <p id="view-opened"></p>
                                            </div>
                                        </div>
                                        <div class="timeline-item">
                                            <i class="fas fa-calendar-minus text-danger"></i>
                                            <div>
                                                <label>Closed Date</label>
                                                <p id="view-closed"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info-group mt-3">
                                        <label>Job Status</label>
                                        <p id="view-status-job"></p>
                                    </div>
                                    <div class="d-flex justify-content-between mt-3">
                                        <div class="text-center">
                                            <label>Needed</label>
                                            <h4 id="view-needed" class="text-primary mb-0"></h4>
                                        </div>
                                        <div class="text-center">
                                            <label>Fulfilled</label>
                                            <h4 id="view-fullfill" class="text-success mb-0"></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Requirements Card -->
                        <div class="col-md-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-check-circle me-2"></i>Requirements</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="info-group">
                                                <label>Gender</label>
                                                <p id="view-gender"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-group">
                                                <label>Education</label>
                                                <p id="view-education"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-group">
                                                <label>Major</label>
                                                <p id="view-major"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-group">
                                                <label>Working Period</label>
                                                <p id="view-years-work"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detailed Lists -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-list me-2"></i>Detailed Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="accordion" id="detailsAccordion">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#purposeCollapse">
                                                    <i class="fas fa-bullseye me-2"></i> Job Purpose
                                                </button>
                                            </h2>
                                            <div id="purposeCollapse" class="accordion-collapse collapse show" data-bs-parent="#detailsAccordion">
                                                <div class="accordion-body">
                                                    <ul id="view-purpose-list" class="list-group list-group-flush"></ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#experienceCollapse">
                                                    <i class="fas fa-briefcase me-2"></i> Experience Required
                                                </button>
                                            </h2>
                                            <div id="experienceCollapse" class="accordion-collapse collapse" data-bs-parent="#detailsAccordion">
                                                <div class="accordion-body">
                                                    <ul id="view-experience-list" class="list-group list-group-flush"></ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#skillsCollapse">
                                                    <i class="fas fa-tools me-2"></i> Required Skills
                                                </button>
                                            </h2>
                                            <div id="skillsCollapse" class="accordion-collapse collapse" data-bs-parent="#detailsAccordion">
                                                <div class="accordion-body">
                                                    <ul id="view-skills-list" class="list-group list-group-flush"></ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#reasonCollapse">
                                                    <i class="fas fa-clipboard-list me-2"></i> Reasons
                                                </button>
                                            </h2>
                                            <div id="reasonCollapse" class="accordion-collapse collapse" data-bs-parent="#detailsAccordion">
                                                <div class="accordion-body">
                                                    <ul id="view-reason-list" class="list-group list-group-flush"></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection


<style>
    .test {
        --primary-color: #4361ee;
        --primary-dark: #3a56d4;
        --secondary-color: #f8f9fa;
        --accent-color: #ff6b6b;
        --success-color: #2ecc71;
        --warning-color: #f39c12;
        --info-color: #3498db;
        --text-dark: #343a40;
        --text-muted: #6c757d;
        --border-radius: 0.75rem;
        --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        --shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 1rem 2rem rgba(0, 0, 0, 0.15);
        --transition: all 0.3s ease;
    }

    body {
        background-color: #f8f9fa;
    }

    .page-header {
        position: relative;
        padding: 2.5rem 0;
        background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        margin-bottom: 2.5rem;
    }

    .page-title {
        font-weight: 700;
        color: white;
        margin-bottom: 0;
        letter-spacing: 0.5px;
    }

    .card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .card:hover {
        box-shadow: var(--shadow);
        transform: translateY(-5px);
    }

    .card-header {
        background: white;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.25rem 1.5rem;
    }

    .card-header h5 {
        margin-bottom: 0;
        font-weight: 600;
        color: var(--primary-color);
    }

    .card-body {
        padding: 1.5rem;
    }

    .form-label {
        font-weight: 500;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }

    .form-control,
    .form-select {
        border-radius: 0.5rem;
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        transition: var(--transition);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.1);
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: var(--transition);
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-primary:hover {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
        transform: translateY(-2px);
    }

    .btn-secondary {
        background-color: #e2e8f0;
        border-color: #e2e8f0;
        color: var(--text-dark);
    }

    .btn-secondary:hover {
        background-color: #cbd5e1;
        border-color: #cbd5e1;
        transform: translateY(-2px);
    }

    .btn-warning {
        background-color: var(--warning-color);
        border-color: var(--warning-color);
    }

    .btn-warning:hover {
        background-color: #e67e22;
        border-color: #e67e22;
        transform: translateY(-2px);
    }

    .btn-info {
        background-color: var(--info-color);
        border-color: var(--info-color);
    }

    .btn-info:hover {
        background-color: #2980b9;
        border-color: #2980b9;
        transform: translateY(-2px);
    }

    .table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .table th {
        background-color: var(--primary-color);
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 1rem;
        vertical-align: middle;
    }

    .table td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
    }

    .table tr:last-child td {
        border-bottom: none;
    }

    .table-responsive {
        border-radius: var(--border-radius);
        overflow: hidden;
    }

    .modal-content {
        border: none;
        border-radius: var(--border-radius);
        overflow: hidden;
    }

    .modal-header {
        border-radius: var(--border-radius) var(--border-radius) 0 0;
        background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
        padding: 1.5rem;
    }

    .modal-title {
        color: white;
        font-weight: 600;
    }

    .modal-body {
        padding: 2rem;
    }

    .info-group {
        margin-bottom: 1.25rem;
    }

    .info-group label {
        display: block;
        font-size: 0.875rem;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
        font-weight: 500;
    }

    .info-group p {
        margin: 0;
        font-size: 1rem;
        color: var(--text-dark);
    }

    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        height: 100%;
        width: 2px;
        background: #e9ecef;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .timeline-item i {
        position: absolute;
        left: -30px;
        background: white;
        padding: 5px;
        border-radius: 50%;
        color: var(--primary-color);
        box-shadow: var(--shadow-sm);
    }

    .timeline-item div {
        margin-left: 10px;
    }

    .accordion-button:not(.collapsed) {
        background-color: rgba(67, 97, 238, 0.1);
        color: var(--primary-color);
        font-weight: 600;
    }

    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(67, 97, 238, 0.1);
    }

    .accordion-button {
        padding: 1.25rem;
        font-weight: 500;
    }

    .accordion-body {
        padding: 1.25rem;
    }

    .list-group-item {
        border-left: none;
        border-right: none;
        border-radius: 0;
        padding: 1rem 1rem 1rem 2rem;
        position: relative;
    }



    .badge {
        padding: 0.5rem 0.75rem;
        font-weight: 500;
        border-radius: 0.5rem;
    }

    .card-header .badge {
        font-size: 0.85rem;
    }

    .badge.bg-success {
        background-color: var(--success-color) !important;
    }

    .badge.bg-warning {
        background-color: var(--warning-color) !important;
    }

    .badge.bg-info {
        background-color: var(--info-color) !important;
    }

    .badge.bg-primary {
        background-color: var(--primary-color) !important;
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 1.5rem 0;
        }

        .modal-dialog {
            margin: 0.5rem;
        }

        .btn {
            padding: 0.5rem 1rem;
        }

        .card-body {
            padding: 1rem;
        }
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Function to populate the modal with labor demand details
    function showLaborDetails(data) {
        // Basic information
        $('#view-recruitment_demand_id').text(data.recruitment_demand_id);
        // Status with appropriate badge color
        const statusElement = $('#view-status');
        statusElement.text(data.status_demand);

        // Remove all existing badge classes
        statusElement.removeClass('badge-primary badge-success badge-danger badge-warning');

        // Add appropriate badge class based on status
        switch (data.status_demand) {
            case 'Approved':
                statusElement.addClass('badge bg-success');
                break;
            case 'Declined':
                statusElement.addClass('badge bg-danger');
                break;
            case 'Pending':
                statusElement.addClass('badge bg-warning text-dark');
                break;
            case 'Revised':
                statusElement.addClass('badge bg-primary');
                break;
            default:
                statusElement.addClass('badge bg-secondary');
        }
        $('#view-department').text(data.department_name);
        $('#view-position').text(data.position_name);
        $('#view-opened').text(formatDate(data.opening_date));
        $('#view-closed').text(formatDate(data.closing_date));
        $('#view-status-job').text(data.status_job);
        $('#view-needed').text(data.qty_needed);
        $('#view-fullfill').text(data.qty_fullfil);
        $('#view-gender').text(data.gender === 'Both' ? 'Male / Female' : data.gender);
        $('#view-education').text(data.education);
        $('#view-major').text(data.major);
        $('#view-time-experience').text(formatWorkExperience(data.time_work_experience));
        $('#view-years-work').text(data.length_of_working);
        $('#view-reason').text(data.declined_reason || '-');

        // Function to format dates
        function formatDate(date) {
            return new Date(date).toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        // Function to populate lists with proper bullet points
        populateList('view-reason-list', data.reason);
        populateList('view-purpose-list', data.job_goal);
        populateList('view-experience-list', data.experience);
        populateList('view-skills-list', data.skills);

        $('#viewModal').modal('show');
    }

    function formatWorkExperience(value) {
        return value.replace('1_12_', '1-12 ')
            .replace('1_3_', '1-3 ')
            .replace('3_5_', '3-5 ')
            .replace('5_plus_', '5+ ');
    }


    function populateList(elementId, value) {
        let list = $(`#${elementId}`);
        list.empty(); // Clear existing items  
        if (value) {
            // Split by newline and trim each item  
            let items = value.split(/\n/).map(item => item.trim()).filter(item => item !== '');

            if (items.length > 0) {
                items.forEach(item => {
                    // Remove leading '- ' if it exists  
                    if (item.startsWith('- ')) {
                        item = item.substring(2); // Remove the first two characters  
                    }
                    list.append(`
                        <li class="list-group-item d-flex align-items-start">
                            <i class="fas fa-circle mt-2 me-2 text-secondary" style="font-size: 8px;"></i>
                            <span>disini ${item}</span>
                        </li>
                        `);
                });
            } else {
                list.append('<li class="list-group-item text-muted">No data available</li>');
            }
        } else {
            list.append('<li class="list-group-item text-muted">No data available</li>');
        }
    }


    $(document).ready(function() {

        // Event listener for view button
        $('.view-btn').click(function() {
            let id = $(this).data('id');
            // Fetch data via AJAX (Replace with actual API route)
            $.ajax({
                url: `/recruitment/labor_demand/${id}`,
                type: 'GET',
                success: function(response) {
                    showLaborDetails(response);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load data'
                    });
                }
            });
        });

        $('#laborDemandTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            "language": {
                "search": "<i class='fas fa-search'></i> _INPUT_",
                "searchPlaceholder": "Search records...",
                "lengthMenu": "_MENU_ records per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "Showing 0 to 0 of 0 records",
                "infoFiltered": "(filtered from _MAX_ total records)"
            },
            "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
        });


        // Show success message if present in session
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('
            success ') }}',
            timer: 3000,
            timerProgressBar: true
        });
        @endif
    });
</script>
@endpush