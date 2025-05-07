@extends('layouts.app')

@section('content')


<div class="labor-demand-container test">
    <div class="page-header">
        <h1>
            <i class="fas fa-user-tie"></i> Labor Demand Management
        </h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-filter"></i> Filter Labor Demands</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('recruitment.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="status_demand" class="form-label">Status</label>
                    <select class="form-select" id="status_demand" name="status_demand">
                        <option value="">All Status</option>
                        <option value="Pending" {{ request('status_demand') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Approved" {{ request('status_demand') == 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Declined" {{ request('status_demand') == 'Declined' ? 'selected' : '' }}>Declined</option>
                        <option value="Revised" {{ request('status_demand') == 'Revised' ? 'selected' : '' }}>Revised</option>
                    </select>
                </div>
                <div class="col-md-4">
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
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <label for="status_job" class="form-label">Job Status</label>
                    <select class="form-select" id="status_job" name="status_job">
                        <option value="">All Job Status</option>
                        @foreach($jobStatuses as $status)
                        <option value="{{ $status }}" {{ request('status_job') == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="opening_date" class="form-label">Opening Date</label>
                    <input type="date" class="form-control" id="opening_date" name="opening_date" value="{{ request('opening_date') }}">
                </div>
                <div class="col-md-4">
                    <label for="closing_date" class="form-label">Closing Date</label>
                    <input type="date" class="form-control" id="closing_date" name="closing_date" value="{{ request('closing_date') }}">
                </div>
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Apply Filters
                        </button>
                        <a href="{{ route('recruitment.index') }}" class="btn btn-secondary">
                            <i class="fas fa-undo me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-list"></i> Labor Demand List</h5>
            <a href="{{ route('recruitment.labor.demand.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> Create New Demand
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="laborDemandTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Status</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Job Status</th>
                            <th>Opened At</th>
                            <th>Closed At</th>
                            <th>Qty Needed</th>
                            <th>Qty Fullfill</th>
                            <th>Maker</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($demand as $item)
                        <tr>
                            <td>{{ $item->recruitment_demand_id }}</td>
                            <td>
                                @if($item->status_demand == 'Pending')
                                <span class="status-badge status-pending" title="Waiting for approval">
                                    {{ $item->status_demand }}
                                </span>
                                @elseif($item->status_demand == 'Approved')
                                <span class="status-badge status-approved" title="Approved by management">
                                    {{ $item->status_demand }}
                                </span>
                                @elseif($item->status_demand == 'Declined')
                                <div>
                                    <span class="status-badge status-declined">
                                        {{ $item->status_demand }}
                                    </span>

                                </div>
                                @elseif($item->status_demand == 'Revised')
                                <div>
                                    <span class="status-badge status-revised">
                                        {{ $item->status_demand }}
                                    </span>

                                </div>
                                @endif
                            </td>

                            <td>{{ $item->department_name }}</td>
                            <td>{{ $item->position_name }}</td>
                            <td>{{ $item->status_job}}</td>
                            <td>{{ \Carbon\Carbon::parse($item->opening_date)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->closing_date)->format('d M Y') }}</td>
                            <td>
                                <span class="quantity-indicator quantity-needed">{{ $item->qty_needed }}</span>
                            </td>
                            <td>
                                <span class="quantity-indicator quantity-fulfilled">{{ $item->qty_fullfil }}</span>
                            </td>
                            <td>{{ $item->maker_name }}</td>
                            <td class="actions-column">
                                <!-- First row: Edit/View -->
                                <div class="action-row">
                                    @if($item->status_demand == 'Pending' || $item->status_demand == 'Revised')
                                    <a href="{{ route('recruitment.labor.demand.edit', $item->id) }}" class="action-btn btn-edit">
                                        <i class="fas fa-pencil-alt"></i> Edit
                                    </a>
                                    @else
                                    <button class="action-btn btn-secondary" disabled>
                                        <i class="fas fa-pencil-alt"></i> Edit
                                    </button>
                                    @endif

                                    <button class="action-btn btn-view view-btn"
                                        data-id="{{ $item->id }}"
                                        data-recruitment_demand_id="{{ $item->recruitment_demand_id }}"
                                        data-status="{{ $item->status_demand }}"
                                        data-department="{{ $item->department_name }}"
                                        data-position="{{ $item->position_name }}"
                                        data-opened="{{ $item->opening_date }}"
                                        data-closed="{{ $item->closing_date }}"
                                        data-status-job="{{ $item->status_job }}"
                                        data-reason="{{ $item->reason ?? 'N/a' }}"
                                        data-needed="{{ $item->qty_needed }}"
                                        data-fullfill="{{ $item->qty_fullfil }}"
                                        data-gender="{{ $item->gender ?? 'N/a' }}"
                                        data-job-goal="{{ $item->job_goal ?? 'N/a' }}"
                                        data-education="{{ $item->education ?? 'N/a' }}"
                                        data-major="{{ $item->major ?? 'N/a' }}"
                                        data-experience="{{ $item->experience ?? 'N/a' }}"
                                        data-length-of-working="{{ $item->length_of_working ?? 'N/A'}}"
                                        data-time-work-experience="{{ $item->time_work_experience ?? 'N/a' }}"
                                        data-response-reason="{{ $item->response_reason ?? 'N/A' }}"
                                        data-response-id="{{ $item->response_id ?? 'N/A' }}"
                                        data-responder-name="{{ $item->responder_name ?? 'N/A' }}"
                                        data-skills="{{ $item->skills ?? 'N/a' }}">
                                        <i class="fas fa-eye"></i> View
                                    </button>

                                </div>

                                <!-- Only for GM: Second row with approve/decline -->
                                @if (Auth::user()->department->department == 'General Manager' && Auth::user()->position->position == 'General Manager')
                                @if($item->status_demand == 'Pending' || $item->status_demand == 'Revised')
                                <div class="action-row">
                                    <a href="#" class="action-btn btn-approve approve-btn" data-id="{{ $item->id }}">
                                        <i class="fa-solid fa-thumbs-up"></i> Approve
                                    </a>

                                    <a href="#" class="action-btn btn-decline decline-btn" data-id="{{ $item->id }}">
                                        <i class="fa-solid fa-thumbs-down"></i> Decline
                                    </a>
                                </div>

                                <!-- Third row with revise button if needed -->
                                @if($item->status_demand != 'Revised')
                                <div class="action-row">
                                    <a href="#" class="action-btn btn-revise revise-btn" data-id="{{ $item->id }}">
                                        <i class="fa-solid fa-pencil-alt"></i> Revise
                                    </a>
                                </div>
                                @endif
                                @endif
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

<!-- Decline Modal -->
<div class="modal fade" id="declineModal" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Decline Labor Demand Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="declineForm" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="response_reason" class="form-label">Reason for Declining</label>
                        <textarea class="form-control" id="response_reason" name="response_reason" rows="4" required placeholder="Please provide the reason for declining this request..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDecline">
                    <i class="fas fa-ban me-2"></i> Decline Request
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Revise Modal -->
<div class="modal fade" id="reviseModal" tabindex="-1" aria-labelledby="reviseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request Revision</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reviseForm" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="revision_reason" class="form-label">Revision Instructions</label>
                        <textarea class="form-control" id="revision_reason" name="revision_reason" rows="4" required placeholder="Please provide clear instructions for the required revisions..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmRevise">
                    <i class="fas fa-edit me-2"></i> Request Revision
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-alt me-2"></i>Labor Demand Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <!-- Basic Information Card -->
                    <div class="col-md-6">
                        <div class="card h-100 border-0">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="info-group">
                                    <label>PTK ID</label>
                                    <p id="view-recruitment_demand_id" class="fw-bold text-primary"></p>
                                </div>

                                <div class="info-group">
                                    <label>Department</label>
                                    <p id="view-department" class="fw-bold"></p>
                                </div>
                                <div class="info-group">
                                    <label>Position</label>
                                    <p id="view-position" class="fw-bold"></p>
                                </div>



                                <div class="info-group">
                                    <label>Status</label>
                                    <p id="view-status"></p>
                                </div>
                                <div class="info-group">
                                    <label>Responder</label>
                                    <p id="view-responder" class="fw-bold"></p>
                                </div>


                                <div class="info-group">
                                    <label>Response Reason</label>
                                    <p id="view-response-reason"></p>
                                </div>


                            </div>
                        </div>
                    </div>

                    <!-- Timeline Card -->
                    <div class="col-md-6">
                        <div class="card h-100 border-0">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Timeline</h6>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <i class="fas fa-calendar-plus"></i>
                                        <div>
                                            <label>Opened Date</label>
                                            <p id="view-opened"></p>
                                        </div>
                                    </div>
                                    <div class="timeline-item">
                                        <i class="fas fa-calendar-minus"></i>
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
                                <div class="info-group mt-3">
                                    <label>Quantity Needed</label>
                                    <h4 id="view-needed" class="text-primary mb-0"></h4>
                                </div>
                                <div class="info-group mt-3">
                                    <label>Quantity Fulfilled</label>
                                    <h4 id="view-fullfill" class="text-success mb-0">></h4>
                                </div>


                            </div>
                        </div>
                    </div>

                    <!-- Requirements Card -->
                    <div class="col-12">
                        <div class="card border-0">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-check-circle me-2"></i>Requirements</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-group">
                                            <label>Gender</label>
                                            <p id="view-gender"></p>
                                        </div>
                                        <div class="info-group">
                                            <label>Education</label>
                                            <p id="view-education"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-group">
                                            <label>Major</label>
                                            <p id="view-major"></p>
                                        </div>
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
                        <div class="card border-0">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-list me-2"></i>Detailed Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="accordion" id="detailsAccordion">
                                    <div class="accordion-item border-0 mb-2">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#purposeCollapse">
                                                <i class="fas fa-bullseye me-2"></i>Job Purpose
                                            </button>
                                        </h2>
                                        <div id="purposeCollapse" class="accordion-collapse collapse" data-bs-parent="#detailsAccordion">
                                            <div class="accordion-body p-0">
                                                <ul id="view-purpose-list" class="list-group list-group-flush"></ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item border-0 mb-2">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#experienceCollapse">
                                                <i class="fas fa-briefcase me-2"></i>Experience Required
                                            </button>
                                        </h2>
                                        <div id="experienceCollapse" class="accordion-collapse collapse" data-bs-parent="#detailsAccordion">
                                            <div class="accordion-body p-0">
                                                <ul id="view-experience-list" class="list-group list-group-flush"></ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item border-0 mb-2">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#skillsCollapse">
                                                <i class="fas fa-tools me-2"></i>Required Skills
                                            </button>
                                        </h2>
                                        <div id="skillsCollapse" class="accordion-collapse collapse" data-bs-parent="#detailsAccordion">
                                            <div class="accordion-body p-0">
                                                <ul id="view-skills-list" class="list-group list-group-flush"></ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item border-0">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#reasonCollapse">
                                                <i class="fas fa-comment-alt me-2"></i>Reasons
                                            </button>
                                        </h2>
                                        <div id="reasonCollapse" class="accordion-collapse collapse" data-bs-parent="#detailsAccordion">
                                            <div class="accordion-body p-0">
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

@endsection


<style>
    .test {
        --primary-color: #1e3c72;
        --secondary-color: #2a5298;
        --accent-color: #4e7ac7;
        --success-color: #28a745;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
    }

    .labor-demand-container {
        max-width: 99%;
        margin: 0 auto;
        padding: 0 15px;
    }

    .page-header {
        position: relative;
        padding: 30px 0;
        margin-bottom: 40px;
        text-align: center;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSg0NSkiPjxyZWN0IHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgZmlsbD0icmdiYSgyNTUsMjU1LDI1NSwwLjA1KSIvPjwvcGF0dGVybj48L2RlZnM+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNwYXR0ZXJuKSIvPjwvc3ZnPg==');
    }

    .page-header h1 {
        position: relative;
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .page-header i {
        margin-right: 15px;
        font-size: 2.2rem;
    }




    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 25px;
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: white;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 20px 25px;
        border-radius: 12px 12px 0 0 !important;
    }

    .card-header h5 {
        font-weight: 600;
        color: var(--primary-color);
        margin: 0;
    }

    .card-header h5 i {
        margin-right: 10px;
        color: var(--accent-color);
    }

    .card-body {
        padding: 25px;
    }






    .form-label {
        font-weight: 500;
        color: #555;
        margin-bottom: 8px;
    }

    .form-control,
    .form-select {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 10px 15px;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 0.25rem rgba(78, 122, 199, 0.25);
    }






    .btn {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn i {
        margin-right: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
        transform: translateY(-2px);
    }

    .btn-secondary {
        background-color: #6c757d;
        border: none;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        transform: translateY(-2px);
    }


    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        background-color: var(--primary-color);
        color: white;
        border-bottom: none;
        padding: 15px;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    .table tbody td {
        padding: 15px;
        vertical-align: middle;
        border-top: 1px solid #f0f0f0;
    }

    .table tbody tr:hover {
        background-color: rgba(78, 122, 199, 0.05);
    }
















    .badge {
        font-weight: 500;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge.bg-warning {
        color: #212529;
    }

    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.8rem;
        border-radius: 6px;
    }

    /* Modal Styles */
    .modal-content {
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }

    .modal-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border-bottom: none;
        padding: 20px;
    }

    .modal-title {
        font-weight: 600;
    }

    .modal-body {
        padding: 25px;
    }

    .modal-footer {
        border-top: 1px solid #f0f0f0;
        padding: 20px;
    }

    /* Timeline Styles */
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
        background: linear-gradient(to bottom, var(--accent-color), rgba(78, 122, 199, 0.3));
    }

    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .timeline-item i {
        position: absolute;
        left: -30px;
        background: white;
        border: 2px solid var(--accent-color);
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent-color);
        box-shadow: 0 0 0 4px white;
    }

    .timeline-item div {
        margin-left: 10px;
    }

    /* Accordion Styles */
    .accordion-button {
        font-weight: 500;
        padding: 15px;
        background-color: rgba(248, 249, 250, 0.5);
    }

    .accordion-button:not(.collapsed) {
        background-color: rgba(30, 60, 114, 0.1);
        color: var(--primary-color);
        box-shadow: none;
    }

    .accordion-button:focus {
        box-shadow: none;
        border-color: transparent;
    }

    .accordion-body {
        padding: 15px;
    }




    /* Responsive adjustments */
    @media (max-width: 768px) {
        .page-header h1 {
            font-size: 2rem;
        }

        .card-header h5 {
            font-size: 1.1rem;
        }

        .action-buttons {
            flex-direction: column;
            align-items: flex-start;
        }

        .btn {
            width: 100%;
            margin-bottom: 8px;
        }
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: var(--accent-color);
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: var(--secondary-color);
    }

    /* Loading spinner */
    .spinner-border {
        width: 1.2rem;
        height: 1.2rem;
        border-width: 0.15em;
    }

    /* Status badges */
    .status-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-approved {
        background-color: #d4edda;
        color: #155724;
    }

    .status-declined {
        background-color: #f8d7da;
        color: #721c24;
    }

    .status-revised {
        background-color: #cce5ff;
        color: #004085;
    }

    /* Quantity indicators */
    .quantity-indicator {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .quantity-needed {
        background-color: #e3f2fd;
        color: var(--primary-color);
    }

    .quantity-fulfilled {
        background-color: #e8f5e9;
        color: var(--success-color);
    }

    /* Info group in modals */
    .info-group {
        margin-bottom: 1.2rem;
    }

    .info-group label {
        display: block;
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 0.3rem;
        font-weight: 500;
    }

    .info-group p {
        margin: 0;
        font-size: 0.95rem;
        color: #212529;
        font-weight: 400;
    }






    .actions-column {
        width: 110px;
    }

    .action-row {
        display: flex;
        margin-bottom: 8px;
    }

    .action-btn {
        width: 100px;
        margin-right: 5px;
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .action-btn i {
        margin-right: 4px;
    }

    .btn-edit {
        background-color: #F8CB00;
        color: #000;
    }

    .btn-view {
        background-color: #00c3e3;
        color: #fff;
    }

    .btn-approve {
        background-color: #28a745;
        color: #fff;
    }

    .btn-decline {
        background-color: #dc3545;
        color: #fff;
    }

    .btn-revise {
        background-color: #0d6efd;
        color: #fff;
    }

    .dropdown-actions {
        position: relative;
        display: inline-block;
    }

    .dropdown-actions-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 180px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
        right: 0;
        border-radius: 4px;
    }

    .dropdown-actions-content a {
        color: black;
        padding: 8px 12px;
        text-decoration: none;
        display: flex;
        align-items: center;
        font-size: 14px;
    }

    .dropdown-actions-content a i {
        margin-right: 10px;
        width: 16px;
    }

    .dropdown-actions-content a:hover {
        background-color: #f1f1f1;
    }

    .dropdown-actions:hover .dropdown-actions-content {
        display: block;
    }

    .dropdown-toggle {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        padding: 4px 8px;
        border-radius: 4px;
        cursor: pointer;
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
        $('#view-opened').text(data.opening_date);
        $('#view-closed').text(data.closing_date);
        $('#view-status-job').text(data.status_job);
        $('#view-needed').text(data.qty_needed);
        $('#view-fullfill').text(data.qty_fullfil);
        $('#view-gender').text(data.gender === 'Both' ? 'Male / Female' : data.gender);
        $('#view-education').text(data.education);
        $('#view-major').text(data.major);
        $('#view-time-experience').text(formatWorkExperience(data.time_work_experience));
        $('#view-years-work').text(data.length_of_working || 'N/A');
        $('#view-responder').text(data.responder_name || 'N/A');
        $('#view-response-reason').text(data.response_reason || 'No reason provided');
        // Populate lists
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

        if (value && value.trim()) {
            // Split by newline and trim each item
            let items = value.split(/\n/).map(item => item.trim()).filter(item => item !== '');

            if (items.length > 0) {
                items.forEach(item => {
                    // Remove any existing bullet characters or dashes
                    if (item.startsWith('- ')) {
                        item = item.substring(2);
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
        $(document).click(function(e) {
            if (!$(e.target).closest('.dropdown-actions').length) {
                $('.dropdown-actions-content').hide();
            }
        });

        // Toggle dropdown on click instead of hover
        $('.dropdown-toggle').click(function(e) {
            e.stopPropagation();
            $(this).next('.dropdown-actions-content').toggle();
        });

        // Event listener for view button
        $('.view-btn').click(function() {
            let id = $(this).data('id');

            // Set the basic data from data attributes first
            let basicData = {
                recruitment_demand_id: $(this).data('recruitment_demand_id'),
                status_demand: $(this).data('status'),
                department_name: $(this).data('department'),
                position_name: $(this).data('position'),
                opening_date: $(this).data('opened'),
                closing_date: $(this).data('closed'),
                status_job: $(this).data('status-job'),
                reason: $(this).data('reason'),
                qty_needed: $(this).data('needed'),
                qty_fullfil: $(this).data('fullfill'),
                gender: $(this).data('gender'),
                job_goal: $(this).data('job-goal'),
                education: $(this).data('education'),
                major: $(this).data('major'),
                experience: $(this).data('experience'),
                length_of_working: $(this).data('length-of-working'),
                time_work_experience: $(this).data('time-work-experience'),
                response_reason: $(this).data('response_reason'),
                responder_name: $(this).data('response_id'),
                skills: $(this).data('skills')
            };

            // Show basic data immediately
            showLaborDetails(basicData);

            // Then fetch additional details via AJAX
            $.ajax({
                url: `/recruitment/labor_demand/${id}`,
                type: 'GET',
                success: function(response) {
                    // Update with the full data from the server
                    showLaborDetails(response);
                },
                error: function(xhr) {
                    console.error('Failed to load full details:', xhr);
                    // Keep showing the basic data we already had
                }
            });
        });




        // Your existing DataTable initialization
        $('#laborDemandTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true
        });



        // Handle decline button click
        $('.decline-btn').click(function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            $('#declineForm').attr('data-id', id);
            $('#declineModal').modal('show');
        });

        // Handle decline confirmation
        $('#confirmDecline').click(function() {
            const id = $('#declineForm').data('id');
            const reason = $('#response_reason').val();
            const $button = $(this);

            if (!reason) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please provide a reason for declining',
                });
                return;
            }

            // Show processing state
            $button.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            $button.prop('disabled', true);

            $.ajax({
                url: `/recruitment/labor_demand/decline/${id}`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    response_reason: reason
                },
                success: function(response) {
                    $('#declineModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'You have declined the labor demand request',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                },
                error: function(xhr) {
                    // Reset button state
                    $button.html('Submit');
                    $button.prop('disabled', false);

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while processing your request',
                    });
                }
            });
        });

        // Handle approve button click
        $('.approve-btn').click(function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const $button = $(this);

            Swal.fire({
                title: 'Confirm Approval',
                text: 'Are you sure you want to approve this labor demand request?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show processing state on the original button
                    $button.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
                    $button.prop('disabled', true);

                    // Add the surrounding elements to a loading state
                    $button.closest('td').find('a, button').not($button).prop('disabled', true);

                    // Redirect with a slight delay to show the processing state
                    setTimeout(function() {
                        window.location.href = `/recruitment/labor_demand/approve/${id}`;
                    }, 500);
                }
            });
        });

        // Handle revise button click
        $('.revise-btn').click(function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            $('#reviseForm').attr('data-id', id);
            $('#reviseModal').modal('show');
        });

        // Handle revise confirmation
        $('#confirmRevise').click(function() {
            const id = $('#reviseForm').data('id');
            const reason = $('#revision_reason').val();
            const $button = $(this);

            if (!reason) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please provide a reason for the revision request',
                });
                return;
            }

            // Show processing state
            $button.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            $button.prop('disabled', true);

            $.ajax({
                url: `/recruitment/labor_demand/revise/${id}`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    revision_reason: reason
                },
                success: function(response) {
                    $('#reviseModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'You have requested revision for the labor demand',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                },
                error: function(xhr) {
                    // Reset button state
                    $button.html('Submit');
                    $button.prop('disabled', false);

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while processing your request',
                    });
                }
            });
        });


    });
</script>


@endpush