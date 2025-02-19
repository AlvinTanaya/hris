@extends('layouts.app')

@section('content')
<style>
    .nav-tabs .nav-link {
        color: white;
        font-weight: 500;
        padding: 1rem;
        transition: all 0.3s ease;
    }




    .nav-tabs .nav-link.active {
        color: #0d6efd;
        border-bottom: 3px solid #0d6efd;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .table thead th {
        border-top: none;
        border-bottom: 2px solid #dee2e6;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .accordion-button:not(.collapsed) {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }

    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(13, 110, 253, 0.1);
    }

    .badge {
        padding: 0.5em 0.75em;
    }

    .modal-header {
        border-bottom: none;
    }

    .modal-footer {
        border-top: none;
    }
</style>

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<a href="{{ route('recruitment.index.interview') }}" class="btn btn-danger px-5 mb-3">
    <i class="fas fa-arrow-left me-2"></i>Back
</a>
<div class="container-fluid px-4 py-5">


    <h1 class="text-center text-warning" style="margin-bottom: 65px;">
        <i class="fas fa-users-cog me-2"></i>Applicant Management
    </h1>

    <div class="row mb-5">
        <div class="col-md-2">
            <h3 class="text-white">
                Position
            </h3>
            <h3 class="text-white">
                Department
            </h3>
        </div>
        <div class="col-md-10">
            <h3 class="text-white">
                : {{ $demand->position }}
            </h3>
            <h3 class="text-white">
                : {{ $demand->department }}
            </h3>
        </div>
    </div>

    <!-- Nav Tabs -->
    <ul class="nav nav-tabs nav-fill mb-4" id="applicantTab" role="tablist">
        <li class="nav-item" role="presentation" style="width: 33.33%">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button">
                <i class="fas fa-user-clock me-2"></i>Pending Applicants
            </button>
        </li>
        <li class="nav-item" role="presentation" style="width: 33.33%">
            <button class="nav-link" id="interview-tab" data-bs-toggle="tab" data-bs-target="#interview" type="button">
                <i class="fas fa-user-tie me-2"></i>Interview Process
            </button>
        </li>
        <li class="nav-item" role="presentation" style="width: 33.33%">
            <button class="nav-link" id="final-tab" data-bs-toggle="tab" data-bs-target="#final" type="button">
                <i class="fas fa-user-check me-2"></i>Final Status
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="applicantTabContent">
        <!-- Pending Applicants Tab -->
        <div class="tab-pane fade show active" id="pending" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive" style="padding-right: 1%;">
                        <table class="table mb-3 pt-3 table-bordered table-hover" id="pendingTable">
                            <thead class="table-dark">
                                <tr class="align-middle">
                                    <th style="width:8%">Profile</th>
                                    <th style="width:12%">Status</th>
                                    <th>Name</th>
                                    <th style="width:14%">Application Date</th>
                                    <th style="width:17%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applicant as $item)
                                <tr class="align-middle">
                                    <td class="text-center">

                                        <img src="{{ $item->photo_profile_path ? asset('storage/'. $item->photo_profile_path) : asset('storage/default_profile.png') }}"
                                            class="rounded-circle"
                                            style="width: 40px; height: 40px; object-fit: cover; border: 2px solid blue;">
                                    </td>
                                    <td>
                                        <span class="
                                                    @if($item->status_applicant == 'Pending')  
                                                        badge bg-warning
                                                    @elseif($item->status_applicant == 'Approved')  
                                                        badge bg-success
                                                    @elseif($item->status_applicant == 'Declined')  
                                                        badge bg-danger
                                                    @endif
                                                    ">
                                            {{ $item->status_applicant }}
                                        </span>
                                    </td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->created_at->format('d M Y, H:i') }}</td>
                                    <td>
                                        <button class="btn btn-info btn-sm view-btn" data-id="{{ $item->id }}">
                                            <i class="fas fa-eye me-1"></i>View
                                        </button>
                                        <button class="btn btn-primary btn-sm schedule-btn" data-id="{{ $item->id }}">
                                            <i class="fas fa-calendar-alt me-1"></i>Schedule
                                        </button>
                                    </td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interview Process Tab -->
        <div class="tab-pane fade" id="interview" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto; padding-right: 1%;">
                        <table class="table mb-3 pt-3 ps-0 pe-0 table-bordered table-hover"
                            id="interviewTable"
                            style="width:100%; table-layout: fixed; min-width: 1000px;">

                            <!-- Menentukan lebar setiap kolom -->
                            <colgroup>
                                <col style="width: 7%;"> <!-- Profile -->
                                <col> <!-- Name (auto) -->
                                <col style="width: 7%;"> <!-- Status -->
                                <col style="width: 13%;"> <!-- Application Date -->
                                <col style="width: 13%;"> <!-- Interview Date -->
                                <col style="width: 42%;"> <!-- Actions -->
                            </colgroup>

                            <thead class="table-dark">
                                <tr class="align-middle">
                                    <th>Profile</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Application Date</th>
                                    <th>Interview Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applicantInterview as $interview)
                                <tr class="align-middle">
                                    <td class="text-center">
                                        <img src="{{ asset('storage/'.$interview->photo_profile_path) }}"
                                            class="rounded-circle"
                                            style="width: 40px; height: 40px; object-fit: cover; border: 2px solid blue;">
                                    </td>
                                    <td>
                                        @if ($interview->exchange_note == null)
                                        {{ $interview->name }}
                                        @else
                                        {{ $interview->name }}
                                        <span class="badge bg-warning"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="{{ $interview->exchange_note }}">
                                            <i class="fas fa-exchange-alt"></i>
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $interview->status_applicant }}</span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($interview->created_at)->format('d M Y, H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($interview->interview_date)->format('d M Y, H:i') }}</td>
                                    <td>
                                        <button class="btn btn-info btn-sm view-btn" data-id="{{ $interview->id }}">
                                            <i class="fas fa-eye me-1"></i> View
                                        </button>
                                        <button class="btn btn-success btn-sm approve-btn" data-id="{{ $interview->id }}">
                                            <i class="fas fa-check me-1"></i> Approve
                                        </button>
                                        <button class="btn btn-danger btn-sm decline-btn" data-id="{{ $interview->id }}">
                                            <i class="fas fa-times me-1"></i> Decline
                                        </button>
                                        <button class="btn btn-warning btn-sm exchange-btn" data-id="{{ $interview->id }}">
                                            <i class="fas fa-exchange-alt me-1"></i> Exchange
                                        </button>
                                        <button class="btn btn-primary btn-sm schedule-btn" data-id="{{ $interview->id }}">
                                            <i class="fa-solid fa-calendar-days"></i> Change Date
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- Final Status Tab -->
        <div class="tab-pane fade" id="final" role="tabpanel">
            <!-- Approved Applicants -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Approved Applicants
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto; padding-right: 1%;">
                        <table class="table mb-3 pt-3 table-bordered table-hover"
                            id="approvedTable"
                            style="width:100%; table-layout: fixed; min-width: 1000px;">

                            <!-- Menentukan ukuran setiap kolom -->
                            <colgroup>
                                <col style="width: 7%;">
                                <col>
                                <col style="width: 13%;">
                                <col style="width: 13%;">
                                <col>
                                <col style="width: 22%;">
                            </colgroup>
                            <thead class="table-dark">
                                <tr class="align-middle">
                                    <th>Profile</th>
                                    <th>Name</th>
                                    <th>Application Date</th>
                                    <th>Interview Date</th>
                                    <th>Reason</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applicantApproved as $approved)
                                <tr class="align-middle">
                                    <td class="text-center">
                                        <img src="{{ asset('storage/'.$approved->photo_profile_path) }}"
                                            class="rounded-circle"
                                            style="width: 40px; height: 40px; object-fit: cover; border: 2px solid blue;">
                                    </td>
                                    <td>
                                        @if ($approved->exchange_note == null)
                                        {{ $approved->name }}
                                        @else
                                        {{ $approved->name }}
                                        <span class="badge bg-warning"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="{{ $approved->exchange_note }}">
                                            <i class="fas fa-exchange-alt"></i>
                                        </span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($approved->created_at)->format('d M Y, H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($approved->interview_date)->format('d M Y, H:i') }}</td>
                                    <td>{{ $approved->status_note }}</td>
                                    <td>
                                        <button class="btn btn-info btn-sm view-btn" data-id="{{ $approved->id }}">
                                            <i class="fas fa-eye me-1"></i>View
                                        </button>
                                        @if ($approved->status_applicant == 'Approved')
                                        <button class="btn btn-primary btn-sm add-employee-btn"
                                            data-id="{{ $approved->id }}">
                                            <i class="fas fa-user-plus me-1"></i>Add to Employees
                                        </button>
                                        @elseif ($approved->status_applicant == 'Done')
                                        <button class="btn btn-primary btn-sm" disabled>
                                            <i class="fas fa-user-plus me-1"></i>Add to Employees
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

            <!-- Declined Applicants -->
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-times-circle me-2"></i>Declined Applicants
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto; padding-right: 1%;">
                        <table class="table mb-3 pt-3 table-bordered table-hover"
                            id="declinedTable"
                            style="width:100%; table-layout: fixed; min-width: 1000px;">

                            <!-- Menentukan ukuran setiap kolom -->
                            <colgroup>
                                <col style="width: 7%;">
                                <col>
                                <col style="width: 13%;">
                                <col style="width: 13%;">
                                <col>
                                <col style="width: 8%;">
                            </colgroup>

                            <thead class="table-dark">
                                <tr class="align-middle">
                                    <th>Profile</th>
                                    <th>Name</th>
                                    <th>Application Date</th>
                                    <th>Interview Date</th>
                                    <th>Reason</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applicantDeclined as $declined)
                                <tr class="align-middle">
                                    <td class="text-center">
                                        <img src="{{ asset('storage/'.$declined->photo_profile_path) }}"
                                            class="rounded-circle"
                                            style="width: 40px; height: 40px; object-fit: cover; border: 2px solid blue;">
                                    </td>
                                    <td>
                                        @if ($declined->exchange_note == null)
                                        {{ $declined->name }}
                                        @else
                                        {{ $declined->name }}
                                        <span class="badge bg-warning"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="{{ $declined->exchange_note }}">
                                            <i class="fas fa-exchange-alt"></i>
                                        </span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($declined->created_at)->format('d M Y, H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($declined->interview_date)->format('d M Y, H:i') }}</td>
                                    <td>{{ $declined->status_note }}</td>
                                    <td>
                                        <button class="btn btn-info btn-sm view-btn"
                                            data-id="{{ $declined->id }}">
                                            <i class="fas fa-eye me-1"></i>View
                                        </button>
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

<!-- View Applicant Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white" id="viewHeader">
                <h5 class="modal-title">
                    <i class="fas fa-file-alt me-2"></i>Applicant Detail
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <!-- Basic Information Card -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm p-3" style="background-color: #e3f2fd; background-size: cover;">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-9 align-middle">
                                        <h5 class="text-primary fw-bold"><i class="fa-solid fa-address-card"></i> Personal Information</h5>

                                        <div class="row">
                                            <div class="col-md-4 d-flex justify-content-between align-items-top"><strong>ID Number</strong> <span>:</span></div>
                                            <div class="col-md-8"><span id="applicantIDNumber"></span></div>
                                        </div>




                                        <div class="row">
                                            <div class="col-md-4 d-flex justify-content-between align-items-top"><strong>Name</strong> <span>:</span></div>
                                            <div class="col-md-8"><span id="applicantName"></span></div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4 d-flex justify-content-between align-items-top"><strong>Birth Place & Date</strong> <span>:</span></div>
                                            <div class="col-md-8"><span id="applicantBirth"></span></div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4 d-flex justify-content-between align-items-top"><strong>Gender</strong> <span>:</span></div>
                                            <div class="col-md-8"><span id="applicantGender"></span></div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4 d-flex justify-content-between align-items-top"><strong>Address</strong> <span>:</span></div>
                                            <div class="col-md-8"><span id="applicantIDAddress"></span></div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4 d-flex justify-content-between align-items-top"><strong>Religion</strong> <span>:</span></div>
                                            <div class="col-md-8"><span id="applicantReligion"></span></div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 d-flex flex-column align-items-center justify-content-center">
                                        <div class="row">
                                            <div class="col-12 text-center">
                                                <img id="applicantPhoto" src="" class="img-fluid rounded border border-dark" style="max-width: 120px;">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 text-center">
                                                <span id="applicantBirthPlace"></span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Other Personal Info Card -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-check-circle me-2"></i>Other Personal Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-group">
                                            <label>Email</label>
                                            <p id="applicantEmail"></p>
                                        </div>

                                        <div class="info-group">
                                            <label>Weight</label>
                                            <p id="applicantWeight"></p>
                                        </div>

                                        <div class="info-group">
                                            <label>BPJS Health</label>
                                            <p id="applicantBPJSHealth"></p>
                                        </div>

                                    </div>

                                    <div class="col-md-6">
                                        <div class="info-group">
                                            <label>Domicile Address</label>
                                            <p id="applicantDomicileAddress"></p>
                                        </div>
                                        <div class="info-group">
                                            <label>Height</label>
                                            <p id="applicantHeight"></p>
                                        </div>
                                        <div class="info-group">
                                            <label>Status Applicant</label>
                                            <p id="applicantStatus"></p>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ID Card -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fa-solid fa-file-image me-2"></i> Applicant ID Card</h6>
                            </div>
                            <div class="card-body text-center">
                                <img id="applicantIDCard" src="" width="100%" height="500px" style="border: none; object-fit: contain;">

                            </div>
                        </div>
                    </div>

                    <!-- CV Applicant -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fa-solid fa-file-pdf me-2"></i>CV Applicant</h6>
                            </div>
                            <div class="card-body">
                                <iframe id="applicantCVFrame" src="" width="100%" height="500px" style="border: none;"></iframe>
                            </div>
                        </div>
                    </div>



                    <!-- Detailed Lists -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-list me-2"></i>Detailed Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="accordion" id="detailsAccordion">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#familyInfo">
                                                Applicant Family Data
                                            </button>
                                        </h2>
                                        <div id="familyInfo" class="accordion-collapse collapse" data-bs-parent="#detailsAccordion">
                                            <div class="accordion-body">
                                                <div id="familyDetails"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#educationInfo">
                                                Applicant Educational Data
                                            </button>
                                        </h2>

                                        <div id="educationInfo" class="accordion-collapse collapse" data-bs-parent="#detailsAccordion">
                                            <div class="accordion-body">
                                                <div id="educationDetails"></div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#experienceInfo">
                                                Applicant Work Experience Data
                                            </button>
                                        </h2>
                                        <div id="experienceInfo" class="accordion-collapse collapse" data-bs-parent="#detailsAccordion">
                                            <div class="accordion-body">
                                                <div id="experienceDetails"></div>
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

<!-- Schedule Interview Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-alt me-2"></i>Schedule Interview
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="scheduleForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Interview Date & Time</label>
                        <input type="datetime-local" class="form-control" name="interview_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="interview_note" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveSchedule">Save</button>
            </div>
        </div>
    </div>
</div>



<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white" id="statusHeader">
                <h5 class="modal-title" id="statusModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    @csrf
                    <input type="hidden" name="status" id="statusFormStatus">
                    <div class="mb-3">
                        <label class="form-label">Notes/Reason</label>
                        <textarea class="form-control" name="status_note" rows="3" required></textarea>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveStatus">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- Position Exchange Modal -->
<div class="modal fade" id="exchangeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-exchange-alt me-2"></i>Exchange Position
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="exchangeForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Select New Position</label>
                        <select class="form-select" name="new_demand_id" required>
                            <option value="">Choose position...</option>
                            <!-- Add your positions dynamically -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason for Exchange</label>
                        <textarea class="form-control" name="exchange_reason" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveExchange">Save</button>
            </div>
        </div>
    </div>
</div>


@endsection


@push('scripts')
<script>
    $(document).ready(function() {

        // Aktifkan tooltip untuk elemen dengan data-bs-toggle="tooltip"
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Initialize DataTables
        const tables = ['#pendingTable', '#interviewTable', '#approvedTable', '#declinedTable'];
        tables.forEach(table => {
            $(table).DataTable({
                responsive: true,
                lengthMenu: [5, 10, 25, 50],
                language: {
                    search: "<i class='fas fa-search'></i>",
                    searchPlaceholder: "Search records..."
                }
            });
        });

        // View Applicant Details
        $('.view-btn').click(function() {
            const id = $(this).data('id');
            $.ajax({
                url: `/recruitment/applicant/show/${id}`,
                method: 'GET',
                success: function(response) {
                    // Populate personal information
                    console.log(response.applicant.cv_path);
                    $('#applicantPhoto').attr('src', '/storage/' + response.applicant.photo_profile_path);
                    $('#applicantCVFrame').attr('src', '/storage/' + response.applicant.cv_path);
                    $('#applicantIDCard').attr('src', '/storage/' + response.applicant.ID_card_path);
                    $('#applicantName').text(response.applicant.name);

                    $('#applicantEmail').text(response.applicant.email);
                    $('#applicantPhoneNumber').text(response.applicant.phone_number);
                    $('#applicantIDNumber').text(response.applicant.ID_number);
                    $('#applicantBirth').text(response.applicant.birth_place + " / " + response.applicant.birth_date);
                    $('#applicantBirthPlace').text(response.applicant.birth_place);
                    $('#applicantReligion').text(response.applicant.religion);
                    $('#applicantGender').text(response.applicant.gender);
                    $('#applicantDomicileAddress').text(response.applicant.domicile_address);
                    $('#applicantIDAddress').text(response.applicant.ID_address);
                    $('#applicantWeight').text(response.applicant.weight + " kg");
                    $('#applicantHeight').text(response.applicant.height + " cm");
                    $('#applicantBPJSHealth').text(response.applicant.bpjs_health);
                    const status = response.applicant.status_applicant;
                    const statusElement = $('#applicantStatus');


                    statusElement.text(status);
                    statusElement.removeClass('text-success text-danger text-warning');
                    // Tambahkan class warna sesuai status
                    if (status === 'Pending') {
                        statusElement.addClass('text-warning'); // Hijau
                    } else if (status === 'Declined') {
                        statusElement.addClass('text-danger'); // Merah
                    } else {
                        statusElement.addClass('text-success'); // Kuning
                    }

                    const header = $('#viewHeader');
                    // Hapus semua class bg-* terlebih dahulu
                    header.removeClass('bg-warning bg-success bg-danger');

                    // Tambahkan class sesuai status
                    if (status === 'Pending') {
                        header.addClass('bg-warning');

                    } else if (status === 'Declined') {
                        header.addClass('bg-danger');
                    } else {
                        header.addClass('bg-success');
                    }

                    // Populate family information
                    let familyHtml = '<div class="table-responsive" style="padding-right: 1%;"><table class="table mb-3 pt-3 table-bordered">';
                    familyHtml += '<thead>     <tr class="align-middle"><th>Relation</th><th>Name</th><th>Occupation</th></tr></thead><tbody>';
                    response.family.forEach(member => {
                        familyHtml += `<tr class="align-middle">
                        <td>${member.relation}</td>
                        <td>${member.name}</td>
                        <td>${member.job}</td>
                    </tr>`;
                    });
                    familyHtml += '</tbody></table></div>';
                    $('#familyDetails').html(familyHtml);

                    // Populate education information
                    let educationHtml = '<div class="table-responsive" style="padding-right: 1%;"><table class="table mb-3 pt-3 table-bordered">';
                    educationHtml += '<thead>     <tr class="align-middle"><th>Level</th><th>Institution</th><th>Period</th><th>Major</th></tr></thead><tbody>';
                    response.education.forEach(edu => {
                        educationHtml += `<tr class="align-middle">
                        <td>${edu.degree}</td>
                        <td>${edu.educational_place}</td>
                        <td>(${edu.start_education}) - (${edu.end_education})</td>
                        <td>${edu.major}</td>
                    </tr>`;
                    });
                    educationHtml += '</tbody></table></div>';
                    $('#educationDetails').html(educationHtml);

                    // Populate work experience
                    let experienceHtml = '<div class="table-responsive" style="padding-right: 1%;"><table class="table mb-3 pt-3 table-bordered">';
                    experienceHtml += '<thead>     <tr class="align-middle"><th>Company</th><th>Position</th><th>Period</th></tr></thead><tbody>';
                    response.experience.forEach(exp => {
                        experienceHtml += `<tr class="align-middle">
                        <td>${exp.company_name}</td>
                        <td>${exp.position}</td>
                        <td>(${exp.working_start}) - (${exp.working_end})</td>
                    </tr>`;
                    });
                    experienceHtml += '</tbody></table></div>';
                    $('#experienceDetails').html(experienceHtml);

                    $('#viewModal').modal('show');
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Failed to load applicant details', 'error');
                }
            });
        });

        // Schedule Interview
        $('.schedule-btn').click(function() {
            const id = $(this).data('id');
            $('#scheduleForm').data('id', id);
            $('#scheduleModal').modal('show');
        });

        $('#saveSchedule').click(function() {
            const id = $('#scheduleForm').data('id');
            const formData = new FormData($('#scheduleForm')[0]);

            $.ajax({
                url: `/recruitment/applicant/schedule/${id}`,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#scheduleModal').modal('hide');
                    Swal.fire('Success', 'Interview scheduled successfully', 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Failed to schedule interview', 'error');
                }
            });
        });

        // Handle Status Updates (Approve/Decline)
        $('.approve-btn, .decline-btn').click(function() {

            const id = $(this).data('id');
            const status = $(this).hasClass('approve-btn') ? 'Approved' : 'Declined';
            $('#statusModalTitle').html(`<i class="fas fa-${status === 'Approved' ? 'check' : 'times'} me-2"></i>
            ${status === 'Approved' ? 'Approve' : 'Decline'} Applicant`);
            $('#statusForm').data('id', id);
            $('#statusFormStatus').val(status);


            const header = $('#statusHeader');

            // Hapus semua class bg-* terlebih dahulu
            header.removeClass('bg-warning bg-success bg-danger');

            // Tambahkan class sesuai status
            if (status === 'Approved') {
                header.addClass('bg-success');
            } else if (status === 'Declined') {
                header.addClass('bg-danger');
            } else {
                header.addClass('bg-warning');
            }

            $('#statusModal').modal('show');
        });

        $('#saveStatus').click(function() {
            const id = $('#statusForm').data('id');
            const formData = new FormData($('#statusForm')[0]);

            $.ajax({
                url: `/recruitment/applicant/status/${id}`,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#statusModal').modal('hide');
                    Swal.fire('Success', 'Applicant status has been updated successfully', 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Failed to update status interview', 'error');
                }
            });
        });


        // Position Exchange
        $('.exchange-btn').click(function() {
            const id = $(this).data('id');

            // Load available positions
            $.ajax({
                url: `/recruitment/applicant/positions/${id}`,
                method: 'GET',
                success: function(response) {
                    // console.log(response); // Debugging

                    const select = $('#exchangeForm select[name="new_demand_id"]');
                    select.empty().append('<option value="">Choose position...</option>');

                    if (response.positions.length > 0) {
                        response.positions.forEach(position => {
                            select.append(`<option value="${position.id}">${position.recruitment_demand_id} - ${position.position} - ${position.department}</option>`);
                        });
                    } else {
                        select.append('<option value="">No available positions</option>');
                    }


                    // Hapus input hidden jika sudah ada sebelumnya
                    $('#exchangeForm input[name="applicant_id"]').remove();

                    // Tambahkan input hidden dengan nilai applicant_id
                    $('#exchangeForm').append(`<input type="hidden" name="applicant_id" value="${response.applicant.id}">`);

                    $('#exchangeForm').data('id', id);
                    $('#exchangeModal').modal('show');
                },
                error: function(xhr) {
                    console.log(xhr.responseJSON.message);
                    alert('Failed to load positions: ' + xhr.responseJSON.message);
                }
            });
        });


        $('#saveExchange').click(function() {
            const id = $('#exchangeForm').data('id');
            const formData = new FormData($('#exchangeForm')[0]);

            $.ajax({
                url: `/recruitment/applicant/exchange/${id}`,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#exchangeModal').modal('hide');
                    Swal.fire('Success', 'Position exchanged successfully', 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Failed to exchange position', 'error');
                }
            });
        });

        // Setup CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Add to Employees handler
        $('.add-employee-btn').click(function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Add to Employees',
                text: 'Are you sure you want to add this applicant to employees?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, add to employees',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/recruitment/applicant/employee/${id}`,
                        method: 'POST',
                        success: function(response) {
                            Swal.fire('Success', 'Applicant added to employees successfully', 'success')
                                .then(() => location.reload());
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Failed to add to employees', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush