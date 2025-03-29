@extends('layouts.app')

@section('content')
<style>
    .modal-content {
        border: none;
        border-radius: 15px;
    }

    .modal-header {
        border-radius: 15px 15px 0 0;
        background: linear-gradient(135deg, #1e3c72, #2a5298);
    }

    .card {
        transition: transform 0.2s;
        border-radius: 10px;
    }

    .info-group {
        margin-bottom: 1rem;
    }

    .info-group label {
        display: block;
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    .info-group p {
        margin: 0;
        font-size: 1rem;
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
    }

    .timeline-item div {
        margin-left: 10px;
    }

    .accordion-button:not(.collapsed) {
        background-color: rgba(30, 60, 114, 0.1);
        color: #1e3c72;
    }

    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(30, 60, 114, 0.1);
    }

    .list-group-item {
        border-left: none;
        border-right: none;
        border-radius: 0;
        padding: 1rem;
        position: relative;
        padding-left: 2rem;
    }

    .list-group-item::before {
        content: '•';
        position: absolute;
        left: 0.75rem;
        color: #1e3c72;
    }

    @media (max-width: 768px) {
        .modal-dialog {
            margin: 0.5rem;
        }
    }
</style>


<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px">
    <i class="fas fa-user-tie"></i> Labor Demand
</h1>
<div class="container mt-4 mx-auto">
    <div class="card shadow-sm">

        <div class="card-header">
            <h5 class="text-primary mt-2"><i class="fas fa-filter"></i> Filter Labor Demands</h5>
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
    <div class="card shadow-sm mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="text-primary mt-2"><i class="fas fa-user"></i> Labor Demand List</h5>
            <a href="{{ route('recruitment.labor.demand.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> Add Form Labor Demannd
            </a>

        </div>

        <div class="card-body">
            <div class="table-responsive" style="padding-right: 1%;">
                <table id="laborDemandTable" class="table table-bordered mb-3 pt-3 align-middle align-items-center">
                    <thead class="table-dark">
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
                            <td class="text-center p-2">
                                @if($item->status_demand == 'Pending')
                                <span class="badge bg-warning text-dark fs-6 p-2">{{ $item->status_demand }}</span>
                                @elseif($item->status_demand == 'Approved')
                                <span class="badge bg-success fs-6 p-2">{{ $item->status_demand }}</span>
                                @elseif($item->status_demand == 'Declined')
                                <span class="badge bg-danger fs-6 p-2"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Reason: {{ $item->response_reason }}">
                                    {{ $item->status_demand }}
                                </span>
                                @elseif($item->status_demand == 'Revised')
                                <span class="badge bg-primary fs-6 p-2"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Revision: {{ $item->response_reason }}">
                                    {{ $item->status_demand }}
                                </span>
                                @endif
                            </td>


                            <td>{{ $item->department_name }}</td>
                            <td>{{ $item->position_name }}</td>
                            <td>{{ $item->status_job}}</td>
                            <td>{{ $item->opening_date }}</td>
                            <td>{{ $item->closing_date }}</td>
                            <td>{{ $item->qty_needed }}</td>
                            <td>{{ $item->qty_fullfil }}</td>
                            <td>{{ $item->maker_name }}</td>

                            <td class="d-flex">
                                @if($item->status_demand == 'Pending' || $item->status_demand == 'Revised')
                                <a href="{{ route('recruitment.labor.demand.edit', $item->id) }}" class="btn btn-warning btn-sm me-2">
                                    <i class="fas fa-pencil"></i> Edit
                                </a>
                                @if (Auth::user()->department == 'General Manager' && Auth::user()->position == 'General Manager')
                                <a href="#" class="btn btn-success btn-sm me-2 approve-btn" data-id="{{ $item->id }}">
                                    <i class="fa-solid fa-thumbs-up"></i> Approve
                                </a>
                                <a href="#" class="btn btn-danger btn-sm me-2 decline-btn" data-id="{{ $item->id }}">
                                    <i class="fa-solid fa-thumbs-down"></i> Decline
                                </a>
                                @if($item->status_demand != 'Revised')
                                <a href="#" class="btn btn-primary btn-sm me-2 revise-btn" data-id="{{ $item->id }}">
                                    <i class="fa-solid fa-pencil-alt"></i> Revise
                                </a>
                                @endif
                                @endif
                                @else
                                <a disabled class="btn btn-secondary btn-sm me-2">
                                    <i class="fas fa-pencil"></i> Edit
                                </a>
                                <button class="btn btn-info btn-sm me-2 view-btn"
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
                                    data-declined-reason="{{ $item->response_reason ?? 'N/A' }}"
                                    data-skills="{{ $item->skills }}">
                                    <i class="fas fa-eye"></i> View
                                </button>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="declineModalLabel">Decline Labor Demand Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="declineForm" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="response_reason" class="form-label">Decline Reason</label>
                        <textarea class="form-control" id="response_reason" name="response_reason" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDecline">Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- Revise Modal -->
<div class="modal fade" id="reviseModal" tabindex="-1" aria-labelledby="reviseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="reviseModalLabel">Revision Request for Labor Demand</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reviseForm" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="revision_reason" class="form-label">Revision Reason</label>
                        <textarea class="form-control" id="revision_reason" name="revision_reason" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmRevise">Submit</button>
            </div>
        </div>
    </div>
</div>


<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
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
                                    <p id="view-status"></p>
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
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
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
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-list me-2"></i>Detailed Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="accordion" id="detailsAccordion">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#purposeCollapse">
                                                Job Purpose
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
                                                Experience Required
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
                                                Required Skills
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
                                                Reasons
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
        </div>
    </div>
</div>



@endsection

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
        if (value) {
            // Split by newline and trim each item  
            let items = value.split(/\n/).map(item => item.trim()).filter(item => item !== '');

            if (items.length > 0) {
                items.forEach(item => {
                    // Remove leading '- ' if it exists  
                    if (item.startsWith('- ')) {
                        item = item.substring(2); // Remove the first two characters  
                    }
                    list.append(`<li class="list-group-item">${item}</li>`);
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
                response: $(this).data('declined-reason'),
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
        //tooltip
        $('[data-bs-toggle="tooltip"]').tooltip();
        // Handle decline button click
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