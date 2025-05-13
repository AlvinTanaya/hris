@extends('layouts.app')

@section('content')


<div class="container-fluid px-4 py-5 test">
    <a href="{{ route('recruitment.index.interview') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i>Back to Recruitment
    </a>

    <h1 class="text-center text-white mb-5">
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
                : {{ $demand->positionRelation->position }}
            </h3>
            <h3 class="text-white">
                : {{ $demand->departmentRelation->department }}
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
                            <thead>
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

                            <thead>
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
                                        <div class="d-flex gap-1 flex-wrap">
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
                            <thead>
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
                                        <div class="d-flex gap-1">
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
                                        </div>
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

                            <thead>
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

                    <!-- Other Info Card -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-check-circle me-2"></i>Other Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-group">
                                            <strong>Email</strong>
                                            <p id="applicantEmail"></p>
                                        </div>

                                        <div class="info-group">
                                            <strong>Phone Number</strong>
                                            <p id="applicantPhoneNumber"></p>
                                        </div>
                                        <div class="info-group">
                                            <strong>Emergency Contact</strong>
                                            <p id="applicantEmergencyContact"></p>
                                        </div>

                                        <div class="info-group">
                                            <strong>Weight</strong>
                                            <p id="applicantWeight"></p>
                                        </div>
                                        <div class="info-group">
                                            <strong>Height</strong>
                                            <p id="applicantHeight"></p>
                                        </div>

                                        <div class="info-group">
                                            <strong>BPJS Employment</strong>
                                            <p id="applicantBPJSEmployment"></p>
                                        </div>


                                        <div class="info-group">
                                            <strong>BPJS Health</strong>
                                            <p id="applicantBPJSHealth"></p>
                                        </div>






                                    </div>

                                    <div class="col-md-6">
                                        <div class="info-group">
                                            <strong>Domicile Address</strong>
                                            <p id="applicantDomicileAddress"></p>
                                        </div>
                                        <div class="info-group">
                                            <strong>Distance Company to Address</strong>
                                            <p id="applicantDistance"></p>
                                        </div>

                                        <div class="info-group">
                                            <strong>SIM</strong>
                                            <p id="applicantSim">-</p>
                                        </div>

                                        <div class="info-group">
                                            <strong>Expected Salary</strong>
                                            <p id="applicantExpectedSalary"></p>
                                        </div>


                                        <div class="info-group">
                                            <strong>Expected Facility</strong>
                                            <p id="applicantExpectedFacility"></p>
                                        </div>



                                        <div class="info-group">
                                            <strong>Expected Benefit</strong>
                                            <p id="applicantExpectedBenefit"></p>
                                        </div>




                                        <div class="info-group">
                                            <strong>Status Applicant</strong>
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
                                <img id="applicantIDCard" src="" width="100%" height="250px" style="border: none; object-fit: contain;">

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

                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fa-solid fa-file-pdf me-2"></i>Achievement</h6>
                            </div>
                            <div class="card-body">
                                <iframe id="applicantAchievementFrame" src="" width="100%" height="500px" style="border: none;"></iframe>
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
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#trainingInfo">
                                                Applicant Training Data
                                            </button>
                                        </h2>

                                        <div id="trainingInfo" class="accordion-collapse collapse" data-bs-parent="#detailsAccordion">
                                            <div class="accordion-body">
                                                <div id="trainingDetails"></div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#languageInfo">
                                                Applicant Language Data
                                            </button>
                                        </h2>

                                        <div id="languageInfo" class="accordion-collapse collapse" data-bs-parent="#detailsAccordion">
                                            <div class="accordion-body">
                                                <div id="languageDetails"></div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#organizationInfo">
                                                Applicant Organization Data
                                            </button>
                                        </h2>

                                        <div id="organizationInfo" class="accordion-collapse collapse" data-bs-parent="#detailsAccordion">
                                            <div class="accordion-body">
                                                <div id="organizationDetails"></div>
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

                    <!-- Reschedule option -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="rescheduleCheckbox" name="needs_reschedule">
                        <label class="form-check-label" for="rescheduleCheckbox">Reschedule interview?</label>
                    </div>

                    <!-- Hidden reschedule fields (will show when checkbox is checked) -->
                    <div id="rescheduleFields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">New Interview Date & Time</label>
                            <input type="datetime-local" class="form-control" name="interview_date">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Interview Notes</label>
                            <textarea class="form-control" name="interview_note" rows="3"></textarea>
                        </div>
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
<!-- Modal for Adding Employee -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEmployeeModalLabel">Add Applicant to Employees</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addEmployeeForm">
                    <input type="hidden" id="applicant_id">
                    <div class="mb-3">
                        <label for="join_date" class="form-label">Join Date</label>
                        <input type="date" class="form-control" id="join_date" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Continue</button>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection

<style>
    .test {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --success-color: #4cc9f0;
        --warning-color: #f72585;
        --danger-color: #e63946;
        --light-bg: #f8f9fa;
        --dark-bg: #212529;
        --card-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        --transition: all 0.3s ease;
    }

    .container-fluid {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Modern tab styling */
    .nav-tabs {
        border-bottom: none;
        gap: 10px;
        margin-bottom: 25px;
    }

    .nav-tabs .nav-item {
        margin-bottom: 0;
    }

    .nav-tabs .nav-link {
        color: white;
        font-weight: 600;
        padding: 1rem 1.5rem;
        transition: var(--transition);
        border-radius: 10px 10px 0 0;
        border: none;
        background-color: rgba(255, 255, 255, 0.1);
        position: relative;
        overflow: hidden;
    }

    .nav-tabs .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.2);
        transform: translateY(-3px);
    }

    .nav-tabs .nav-link.active {
        color: #fff;
        border: none;
        background-color: var(--primary-color);
        box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.1);
    }

    .nav-tabs .nav-link i {
        margin-right: 8px;
    }

    /* Card styling */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
    }

    .card-header {
        border-bottom: none;
        padding: 1.25rem 1.5rem;
        font-weight: 600;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Table styling */
    .table {
        margin-bottom: 0;
    }

    .table thead th {
        border-top: none;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #555;
        padding: 1rem;
        vertical-align: middle;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    .table-bordered {
        border: none;
    }

    .table-bordered td,
    .table-bordered th {
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(67, 97, 238, 0.05);
    }

    /* Button styling */
    .btn {
        font-weight: 500;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        transition: var(--transition);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
        border-radius: 6px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        border: none;
    }

    .btn-info {
        background: linear-gradient(135deg, #4cc9f0, #4895ef);
        border: none;
        color: white;
    }

    .btn-success {
        background: linear-gradient(135deg, #4cc9f0, #2a9d8f);
        border: none;
    }

    .btn-danger {
        background: linear-gradient(135deg, #f72585, #e63946);
        border: none;
    }

    .btn-warning {
        background: linear-gradient(135deg, #ffd166, #f8961e);
        border: none;
        color: #343a40;
    }

    /* Badge styling */
    .badge {
        padding: 0.5em 0.8em;
        font-weight: 500;
        border-radius: 6px;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .bg-warning {
        background-color: #ffd166 !important;
        color: #343a40;
    }

    .bg-success {
        background-color: #2a9d8f !important;
    }

    .bg-danger {
        background-color: #e63946 !important;
    }

    .bg-primary {
        background: linear-gradient(135deg, #4361ee, #3a0ca3) !important;
    }

    /* Profile image styling */
    .rounded-circle {
        border: 3px solid white !important;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .rounded-circle:hover {
        transform: scale(1.1);
    }

    /* Modal styling */
    .modal-content {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    }

    .modal-header {
        padding: 1.5rem;
        border-bottom: none;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        padding: 1.5rem;
        border-top: none;
    }

    /* Form controls */
    .form-control,
    .form-select {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #dee2e6;
        transition: var(--transition);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
    }

    /* Accordion styling */
    .accordion-item {
        border: none;
        margin-bottom: 10px;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .accordion-button {
        padding: 1.25rem 1.5rem;
        font-weight: 600;
        background-color: #f8f9fa;
        color: #212529;
    }

    .accordion-button:not(.collapsed) {
        background-color: rgba(67, 97, 238, 0.1);
        color: var(--primary-color);
    }

    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(67, 97, 238, 0.1);
    }

    /* Header styling */
    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        font-weight: 600;
    }

    h1.text-center {
        font-size: 2.5rem;
        margin-bottom: 3rem;
        position: relative;
        padding-bottom: 1rem;
    }

    h1.text-center:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, #4361ee, #f72585);
        border-radius: 10px;
    }

    /* Back button */
    .btn-back {
        display: inline-flex;
        align-items: center;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .btn-back:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }

    .btn-back i {
        margin-right: 10px;
        font-size: 1.1rem;
    }

    /* Personal info card */
    .personal-info-card {
        background: linear-gradient(135deg, #e3f2fd, #bbdefb);
        border-radius: 15px;
    }

    .info-row {
        margin-bottom: 0.5rem;
        display: flex;
    }

    .info-label {
        font-weight: 600;
        color: #555;
        width: 40%;
        display: flex;
        justify-content: space-between;
        align-items: top;
    }

    .info-value {
        width: 60%;
    }

    .info-group {
        margin-bottom: 1.25rem;
    }

    .info-group strong {
        display: block;
        margin-bottom: 5px;
        color: #555;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .info-group p {
        margin-bottom: 0;
        color: #212529;
        font-weight: 500;
    }

    /* Tab pane animations */
    .tab-pane {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Dark mode dashboard style */
    .dashboard-dark {
        background-color: #111827;
        color: #f3f4f6;
    }

    .dashboard-dark .card {
        background-color: #1f2937;
        color: #f3f4f6;
    }

    /* Custom checkbox */
    .form-check-input {
        width: 1.2em;
        height: 1.2em;
        margin-top: 0.15em;
        cursor: pointer;
    }

    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    /* PDF iframe styling */
    iframe {
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
</style>


@push('scripts')


<script>
    function formatList(value) {
        if (!value) return '-'; // Jika kosong, tampilkan strip
        const items = value.split(';').map(item => item.trim()).filter(item => item !== '');
        return `<ul><li>` + items.join('</li><li>') + `</li></ul>`;
    }

    function formatSim(sim, simNumber) {
        if (!sim) return '-'; // Jika SIM null, tampilkan tanda strip

        const simList = sim.split(','); // Memisahkan SIM jika ada banyak (misal: "A,C")
        let formattedText = '';

        try {
            const simNumbers = simNumber ? JSON.parse(simNumber) : {}; // Parse JSON jika ada nomor SIM

            simList.forEach(type => {
                type = type.trim(); // Hapus spasi ekstra
                const number = simNumbers[type] || '-'; // Ambil nomor jika ada, jika tidak tampilkan "-"
                formattedText += `SIM ${type}: ${number}<br>`; // Format hasilnya
            });
        } catch (error) {
            formattedText = 'Format SIM tidak valid';
        }

        return formattedText;
    }

    // Function to format date strings
    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    // Populate family information with cards
    function populateFamilyDetails(response) {
        let familyHtml = '<div class="row g-4">';
        response.family.forEach(member => {
            familyHtml += `
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">${member.relation ?? "-"}</h5>
                </div>
                <div class="card-body">
                    <h6 class="card-subtitle mb-3 text-black">${member.name ?? "-"}</h6>
                    
                    <div class="mb-2">
                        <i class="fas fa-phone me-2"></i>${member.phone_number ?? "-"}
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-id-card me-2"></i>${member.gender ?? "-"}
                    </div>
                
                </div>
            </div>
        </div>
    `;
        });
        familyHtml += '</div>';
        $('#familyDetails').html(familyHtml);
    }

    // Populate education information with timeline
    function populateEducationDetails(response) {
        let educationHtml = '<div class="education-timeline">';
        response.education.sort((a, b) => new Date(b.end_education) - new Date(a.end_education))
            .forEach(edu => {
                let transcriptBtn = edu.transcript_file_path ?
                    `<a href="/storage/${edu.transcript_file_path}" target="_blank" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-file-pdf me-2"></i>View Transcript
               </a>` :
                    "";

                educationHtml += `
        <div class="timeline-item">
            <div class="timeline-marker"></div>
            <div class="timeline-content card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-primary mb-2">${edu.degree ?? "-"}</h5>
                        ${transcriptBtn}
                    </div>
                    <h6 class="mb-3">${edu.educational_place ?? "-"}</h6>
                    <div class="mb-2">
                        <i class="fa-solid fa-tree-city me-2"></i> ${edu.educational_province ?? "-"}
                    </div>
                    <div class="mb-2">
                        <i class="fa-solid fa-city me-2"></i> ${edu.educational_city ?? "-"}
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-graduation-cap me-2"></i>${edu.major ?? "-"}
                    </div>
                    <div class="mb-2">
                        <i class="fa-solid fa-marker me-2"></i> ${edu.grade ?? "-"}
                    </div>
                    <div class="text-muted">
                        <i class="fas fa-calendar me-2"></i>
                        ${formatDate(edu.start_education) ?? "-"} - ${formatDate(edu.end_education) ?? "-"}
                    </div>
                </div>
            </div>
        </div>
    `;
            });
        educationHtml += '</div>';
        $('#educationDetails').html(educationHtml);
    }

    // Populate work experience with modern cards
    function populateWorkExperience(response) {
        let experienceHtml = '<div class="education-timeline">';
        response.experience.sort((a, b) => new Date(b.end_date) - new Date(a.end_date))
            .forEach(exp => {
                experienceHtml += `
        <div class="timeline-item">
            <div class="timeline-marker"></div>
            <div class="timeline-content card border-0 shadow-sm">    
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="text-primary mb-1">${exp.company_name ?? "-"}</h5>
                            <h6 class="mb-0">${exp.position ?? "-"}</h6>
                        </div>
                        <span class="badge bg-secondary">
                            ${formatDate(exp.working_start) ?? "-"} - ${formatDate(exp.working_end) ?? "-"}
                        </span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <i class="fas fa-map-marker-alt me-2"></i>${exp.company_address ?? "-"}
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-phone me-2"></i>${exp.company_phone ?? "-"}
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-dollar-sign me-2"></i>Rp. ${exp.salary ? parseInt(exp.salary).toLocaleString('id-ID') : "-"}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <i class="fas fa-user me-2"></i>Supervisor: ${exp.supervisor_name ?? "-"}
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-phone me-2"></i>Supervisor: ${exp.supervisor_phone ?? "-"}
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="row">
                                <div class="col-md-6">
                                <h6 class="mb-2">Job Description</h6>
                                    ${formatList(exp.job_desc) ?? "-"}
                    
                                </div>
                                <div class="col-md-6">
                                <h6 class="mb-2">Reason for Leaving</h6>
                                ${formatList(exp.reason) ?? "-"}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-2">Benefits</h6>
                                    ${formatList(exp.benefit) ?? "-"}
                    
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-2">Facilities</h6>
                                    ${formatList(exp.facility) ?? "-"}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
            });
        experienceHtml += '</div>';
        $('#experienceDetails').html(experienceHtml);
    }

    function populateWorkTraining(response) {
        let trainingHtml = '<div class="education-timeline">';
        response.training.sort((a, b) => new Date(b.end_date) - new Date(a.end_date))
            .forEach(tra => {
                trainingHtml += `
            <div class="timeline-item">
                <div class="timeline-marker"></div>
                <div class="timeline-content card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="text-primary mb-2">${tra.training_name ?? "-"}</h5>
                        <div class="mb-2">
                        <i class="fa-solid fa-city me-2"></i> ${tra.training_city ?? "-"}
                        </div>
                        <div class="mb-2">
                        <i class="fa-solid fa-tree-city me-2"></i> ${tra.training_province ?? "-"}
                        </div>
                 
                        <div class="text-muted">
                            <i class="fas fa-calendar me-2"></i>
                            ${formatDate(tra.start_date) ?? "-"} - ${formatDate(tra.end_date) ?? "-"}
                        </div>
                    </div>
                </div>
            </div>
        `;
            });
        trainingHtml += '</div>';
        $('#trainingDetails').html(trainingHtml);
    }

    function populateOrganizations(response) {
        let orgHtml = '<div class="education-timeline">';
        response.organization.sort((a, b) => new Date(b.end_date) - new Date(a.end_date))
            .forEach(org => {
                orgHtml += `
        <div class="timeline-item">
            <div class="timeline-marker"></div>
            <div class="timeline-content card border-0 shadow-sm">    
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="text-primary mb-1">${org.organization_name ?? "-"}</h5>
                            <p class="text-muted mb-2">${org.position ?? "-"}</p>
                        </div>
                        <span class="badge bg-secondary">
                            ${formatDate(org.start_date) ?? "-"} - ${formatDate(org.end_date) ?? "-"}
                        </span>
                    </div>
                    <div class="mb-2">
                        <i class="fa-solid fa-tree-city me-2"></i> ${org.province ?? "-"}
                    </div>
                    <div class="mb-2">
                        <i class="fa-solid fa-city me-2"></i> ${org.city ?? "-"}
                    </div>
                 
                 
                    <div class="row">
                        <div class="col-md-12">
                            <h6>Activity</h6>
                            ${formatList(org.activity_type) ?? "-"}
            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
            });
        orgHtml += '</div>';
        $('#organizationDetails').html(orgHtml);
    }

    function populateLanguages(response) {
        let languageHtml = '<div class="row g-4">';
        response.language.forEach(lang => {
            languageHtml += `
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="text-primary mb-3">${lang.language ?? "-"}</h5>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Written</span>
                        <span class="badge ${lang.written === 'Active' ? 'bg-success' : 'bg-secondary'}">
                            ${lang.written ?? "-"}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Verbal</span>
                        <span class="badge ${lang.verbal === 'Active' ? 'bg-success' : 'bg-secondary'}">
                            ${lang.verbal ?? "-"}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    `;
        });
        languageHtml += '</div>';
        $('#languageDetails').html(languageHtml);
    }

    $('<style>')
        .text(`
        .education-timeline {
            position: relative;
            padding: 2rem 0;
        }
        
        .education-timeline::before {
            content: '';
            position: absolute;
            top: 0;
            left: 1rem;
            height: 100%;
            width: 2px;
            background: #e9ecef;
        }
        
        .timeline-item {
            position: relative;
            padding-left: 3rem;
            margin-bottom: 2rem;
        }
        
        .timeline-marker {
            position: absolute;
            left: 0.35rem;
            width: 1.3rem;
            height: 1.3rem;
            border-radius: 50%;
            background: #primary;
            border: 3px solid blue;
            box-shadow: 0 0 0 2px #primary;
        }
        
        .timeline-content {
            margin-left: 1rem;
        }
        
        .card {
            transition: transform 0.2s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
    `)
        .appendTo('head');

    $(document).ready(function() {




        // Aktifkan tooltip untuk elemen dengan data-bs-toggle="tooltip"
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Initialize DataTables
        const tables = ['#pendingTable', '#interviewTable', '#approvedTable', '#declinedTable'];
        tables.forEach(table => {
            $(table).DataTable();
        });


        $('#rescheduleCheckbox').on('change', function() {
            if ($(this).is(':checked')) {
                $('#rescheduleFields').show();
                // Make fields required when checkbox is checked
                $('#rescheduleFields input, #rescheduleFields textarea').prop('required', true);
            } else {
                $('#rescheduleFields').hide();
                // Remove required attribute when checkbox is unchecked
                $('#rescheduleFields input, #rescheduleFields textarea').prop('required', false);
            }
        });


        // View Applicant Details
        $(document).on('click', '.view-btn', function() {
            const id = $(this).data('id');
            $.ajax({
                url: `/recruitment/applicant/show/${id}`,
                method: 'GET',
                success: function(response) {
                    // Populate personal information

                    $('#applicantPhoto').attr('src', '/storage/' + response.applicant.photo_profile_path);
                    $('#applicantCVFrame').attr('src', '/storage/' + response.applicant.cv_path);
                    $('#applicantIDCard').attr('src', '/storage/' + response.applicant.ID_card_path);
                    $('#applicantAchievementFrame').attr('src', '/storage/' + response.applicant.achievement_path);

                    $('#applicantExpectedSalary').text(
                        "Rp. " + parseInt(response.applicant.expected_salary).toLocaleString('id-ID')
                    );

                    $('#applicantExpectedFacility').html(formatList(response.applicant.expected_facility));
                    $('#applicantExpectedBenefit').html(formatList(response.applicant.expected_benefit));
                    $('#applicantSim').html(formatSim(response.applicant.sim, response.applicant.sim_number));


                    $('#applicantName').text(response.applicant.name ?? "-");
                    $('#applicantEmail').text(response.applicant.email ?? "-");
                    $('#applicantPhoneNumber').text(response.applicant.phone_number ?? "-");
                    $('#applicantIDNumber').text(response.applicant.ID_number ?? "-");
                    $('#applicantBirth').text((response.applicant.birth_place && response.applicant.birth_date) ? response.applicant.birth_place + " / " + response.applicant.birth_date : "-");
                    $('#applicantBirthPlace').text(response.applicant.birth_place ?? "-");
                    $('#applicantReligion').text(response.applicant.religion ?? "-");
                    $('#applicantGender').text(response.applicant.gender ?? "-");
                    $('#applicantDomicileAddress').text(response.applicant.domicile_address ?? "-");
                    $('#applicantIDAddress').text(response.applicant.ID_address ?? "-");
                    $('#applicantWeight').text(response.applicant.weight ? response.applicant.weight + " kg" : "-");
                    $('#applicantHeight').text(response.applicant.height ? response.applicant.height + " cm" : "-");
                    $('#applicantBPJSHealth').text(response.applicant.bpjs_health ?? "-");
                    $('#applicantBPJSEmployment').text(response.applicant.bpjs_employment ?? "-");
                    $('#applicantPhoneNumber').text(response.applicant.phone_number ?? "-");
                    $('#applicantDistance').text(response.applicant.distance ?? "-");
                    $('#applicantEmergencyContact').text(response.applicant.emergency_contact ?? "-");



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

                    populateFamilyDetails(response);
                    populateEducationDetails(response);
                    populateWorkExperience(response);
                    populateWorkTraining(response);
                    populateLanguages(response);
                    populateOrganizations(response);

                    $('#viewModal').modal('show');
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Failed to load applicant details', 'error');
                }
            });
        });



        
        // Untuk tombol Schedule
        $(document).on('click', '.schedule-btn', function() {
            const id = $(this).data('id');
            $('#scheduleForm').data('id', id);
            $('#scheduleModal').modal('show');
        });

        $(document).on('click', '#saveSchedule', function() {
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
        $(document).on('click', '.approve-btn, .decline-btn', function() {
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

        $(document).on('#saveStatus', function() {
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
        $(document).on('click', '.exchange-btn', function() {
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
                            select.append(`<option value="${position.id}">${position.recruitment_demand_id} - ${position.position_relation.position} - ${position.department_relation.department}</option>`);
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
        let selectedApplicantId = null;

        // Untuk tombol Add Employee
        $(document).on('click', '.add-employee-btn', function() {
            selectedApplicantId = $(this).data('id');
            $('#applicant_id').val(selectedApplicantId);
            $('#addEmployeeModal').modal('show');
        });

        // Ketika form modal disubmit
        $('#addEmployeeForm').submit(function(e) {
            e.preventDefault();

            const joinDate = $('#join_date').val();
            if (!joinDate) {
                Swal.fire('Error', 'Please select a Join Date', 'error');
                return;
            }

            // Tampilkan konfirmasi SweetAlert
            Swal.fire({
                title: 'Add to Employees',
                text: `Are you sure you want to add this applicant to employees with Join Date: ${joinDate}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, add to employees',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/recruitment/applicant/employee/${selectedApplicantId}`,
                        method: 'POST',
                        data: {
                            join_date: joinDate
                        },
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

            $('#addEmployeeModal').modal('hide'); // Sembunyikan modal setelah submit
        });
    });
</script>
@endpush