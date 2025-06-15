@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 test">
    <!-- Page Header with Profile -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg page-header-box">
                <div class="card-body p-4 text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="profile-avatar2 me-4">
                                <img src="{{ Auth::user()->photo_profile_path ? asset('storage/'. Auth::user()->photo_profile_path) : asset('storage/default_profile.png') }}"
                                    alt="Profile Picture" class="profile-image2">
                                <div class="profile-status"></div>
                            </div>
                            <div>
                                <h2 class="h3 mb-2 font-weight-bold">
                                    <i class="fas fa-money-check-alt me-3"></i>My Payroll History
                                </h2>
                                <p class="mb-0 opacity-90">View your salary and payroll information</p>
                            </div>
                        </div>
                        <div class="header-stats">
                            <div class="stat-item">
                                <div class="stat-value">{{ $payrolls->count() }}</div>
                                <div class="stat-label">Records</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Info Card with Glass Effect -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg glass-card">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="font-weight-bold text-dark mb-4 section-title">
                                <i class="fas fa-user me-2 text-gradient"></i>Employee Information
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">Full Name</div>
                                        <div class="info-value">{{ $user->name }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Email Address</div>
                                        <div class="info-value">{{ $user->email }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">Department</div>
                                        <div class="info-value">
                                            <span class="badge badge-department">{{ $user->department->department ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Position</div>
                                        <div class="info-value">
                                            <span class="badge badge-position">{{ $user->position->position ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="employee-icon">
                                <i class="fas fa-id-badge fa-4x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Filter Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg glass-card">
                <div class="card-body p-4">
                    <form method="GET" id="filterForm">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label class="form-label font-weight-bold filter-label">
                                    <i class="fas fa-calendar-alt me-2"></i>Filter by Month/Year
                                </label>
                                <div class="input-group">
                                    <input type="month" class="form-control modern-input" name="month_year"
                                        value="{{ request('month_year') }}"
                                        onchange="document.getElementById('filterForm').submit()">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-clear-filters" onclick="clearFilters()">
                                    <i class="fas fa-times me-2"></i>Clear Filters
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Payroll Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg glass-card">
                <div class="card-header bg-transparent border-bottom py-4">
                    <h6 class="font-weight-bold text-dark mb-0 section-title">
                        <i class="fas fa-table me-2 text-gradient"></i>Payroll Records
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($payrolls->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 modern-table" id="payrollTable">
                            <thead>
                                <tr>
                                    <th class="border-0">#</th>
                                    <th class="border-0">Period</th>
                                    <th class="border-0">Department</th>
                                    <th class="border-0">Position</th>
                                    <th class="border-0">Basic Salary</th>
                                    <th class="border-0">Overtime Hours</th>
                                    <th class="border-0">Overtime Pay</th>
                                    <th class="border-0">Allowance</th>
                                    <th class="border-0">Reduction</th>
                                    <th class="border-0">Bonus</th>
                                    <th class="border-0">Total</th>
                                    <th class="border-0">Attachment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payrolls as $payroll)
                                <tr class="table-row-hover">
                                    <td class="align-middle">
                                        <span class="badge badge-number">{{ $payroll->display_number }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="period-info">
                                            <div class="period-month">{{ $payroll->created_at->format('M Y') }}</div>
                                            <div class="period-date">{{ $payroll->created_at->format('d M Y') }}</div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-department-table">{{ $payroll->historical_department ?? 'N/A' }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-position-table">{{ $payroll->historical_position ?? 'N/A' }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="salary-amount positive">Rp {{ number_format($payroll->basic_salary, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-overtime">{{ $payroll->overtime_hours ?? 0 }} hrs</span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="salary-amount info">Rp {{ number_format($payroll->overtime_salary ?? 0, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="salary-amount positive">Rp {{ number_format($payroll->allowance ?? 0, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="salary-amount negative">Rp {{ number_format($payroll->reduction_salary ?? 0, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="salary-amount bonus">Rp {{ number_format($payroll->bonus ?? 0, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="align-middle">
                                        @php
                                        $total = ($payroll->basic_salary ?? 0) +
                                        ($payroll->overtime_salary ?? 0) +
                                        ($payroll->allowance ?? 0) +
                                        ($payroll->bonus ?? 0) -
                                        ($payroll->reduction_salary ?? 0);
                                        @endphp
                                        <div class="salary-amount total">Rp {{ number_format($total, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="align-middle">
                                        @if($payroll->file_path)
                                        <button type="button"
                                            class="btn btn-attachment view-attachment-btn"
                                            data-file="{{ asset('storage/' . $payroll->file_path) }}"
                                            data-employee="{{ $payroll->user->name }}"
                                            data-period="{{ $payroll->created_at->format('M Y') }}"
                                            title="View Attachment">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @else
                                        <span class="badge badge-no-file">No File</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Enhanced Pagination -->
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Showing {{ $payrolls->firstItem() ?? 0 }} to {{ $payrolls->lastItem() ?? 0 }} of {{ $payrolls->total() }} results
                        </div>
                        <div class="pagination-controls">
                            {{ $payrolls->links() }}
                        </div>
                    </div>
                    @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h5 class="empty-title">No Payroll Records Found</h5>
                        <p class="empty-description">There are no payroll records available for the selected criteria.</p>
                        <button type="button" class="btn btn-clear-filters" onclick="clearFilters()">
                            <i class="fas fa-refresh me-2"></i>Reset Filters
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced View Attachment Modal -->
<div class="modal fade test" id="viewAttachmentModal" tabindex="-1" aria-labelledby="viewAttachmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-xl modal-modern">
            <div class="modal-header">
                <h5 class="modal-title" id="viewAttachmentModalLabel">
                    <i class="fas fa-file-image me-2"></i> Payroll Attachment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="attachment-info" id="view-attachment-info"></div>
                <div id="image-container" class="text-center mb-4">
                    <img id="payroll-image" src="" class="payroll-image-preview img-fluid d-none" alt="Payroll Attachment">
                    <div id="no-image-message" class="d-none empty-attachment">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>No attachment uploaded yet.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
                <a id="download-attachment" href="#" class="btn btn-primary" download>
                    <i class="fas fa-download me-1"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
    .test {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        --glass-bg: rgba(255, 255, 255, 0.25);
        --glass-border: rgba(255, 255, 255, 0.18);
        --shadow-lg: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        --shadow-xl: 0 35px 60px -12px rgba(0, 0, 0, 0.3);
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* Header Styles */
    .page-header-box {
        background: var(--primary-gradient);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
    }

    .page-header-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="%23ffffff" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
        opacity: 0.3;
    }

    /* Updated Profile Status Styles */
    .profile-avatar2 {
        position: relative;
        width: 80px !important;
        height: 80px !important;
    }

    .profile-image2 {
        width: 80px !important;
        height: 80px !important;
        border-radius: 50%;
        border: 3px solid rgba(255, 255, 255, 0.3);
        object-fit: cover;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .profile-image2:hover {
        transform: scale(1.05);
        border-color: rgba(255, 255, 255, 0.8);
    }

    .profile-status {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 22px;
        height: 22px;
        background: #4ade80;
        border: 3px solid white;
        border-radius: 50%;
        box-shadow: 0 3px 10px rgba(74, 222, 128, 0.4);
        animation: pulse 2s infinite;
        z-index: 10;
    }

    .header-stats {
        text-align: center;
    }

    .stat-item {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 1rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: white;
    }

    .stat-label {
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.8);
        margin-top: 0.25rem;
    }

    /* Glass Card Effect */
    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(16px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        box-shadow: var(--shadow-lg);
        transition: all 0.3s ease;
    }

    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-xl);
    }

    /* Section Titles */
    .section-title {
        position: relative;
        padding-bottom: 0.5rem;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: var(--primary-gradient);
        border-radius: 2px;
    }

    .text-gradient {
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Employee Info Styles */
    .info-item {
        margin-bottom: 1.5rem;
    }

    .info-label {
        font-size: 0.8rem;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-size: 1rem;
        color: #1f2937;
        font-weight: 600;
    }

    .employee-icon {
        background: var(--primary-gradient);
        width: 120px;
        height: 120px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: white;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        transform: rotate(-5deg);
        transition: all 0.3s ease;
    }

    .employee-icon:hover {
        transform: rotate(0deg) scale(1.05);
    }

    /* Enhanced Badges */
    .badge-department,
    .badge-position {
        background: var(--primary-gradient);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .badge-department-table {
        background: var(--success-gradient);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .badge-position-table {
        background: var(--secondary-gradient);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .badge-number {
        background: var(--primary-gradient);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 15px;
        font-weight: 700;
    }

    .badge-overtime {
        background: var(--warning-gradient);
        color: white;
        padding: 0.3rem 0.6rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .badge-no-file {
        background: #f3f4f6;
        color: #6b7280;
        padding: 0.3rem 0.6rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    /* Modern Input Styles */
    .modern-input {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(8px);
    }

    .modern-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        background: white;
    }

    .input-group-text {
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: 0 12px 12px 0;
    }

    .filter-label {
        color: #374151;
        margin-bottom: 0.5rem;
    }

    /* Button Styles */
    .btn-clear-filters {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(238, 90, 36, 0.3);
    }

    .btn-clear-filters:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(238, 90, 36, 0.4);
        color: white;
    }

    .btn-attachment {
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: 8px;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .btn-attachment:hover {
        transform: scale(1.1);
        color: white;
    }

    /* Modern Table Styles */
    .modern-table {
        border-radius: 15px;
        overflow: hidden;
    }

    .modern-table thead th {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        color: #475569;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 1.25rem 1rem;
        border: none;
    }

    .table-row-hover {
        transition: all 0.3s ease;
    }

    .table-row-hover:hover {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        transform: scale(1.01);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    /* Salary Amount Styles */
    .salary-amount {
        font-weight: 700;
        padding: 0.25rem 0.5rem;
        border-radius: 8px;
        font-size: 0.9rem;
    }

    .salary-amount.positive {
        color: #059669;
        background: rgba(5, 150, 105, 0.1);
    }

    .salary-amount.negative {
        color: #dc2626;
        background: rgba(220, 38, 38, 0.1);
    }

    .salary-amount.info {
        color: #0284c7;
        background: rgba(2, 132, 199, 0.1);
    }

    .salary-amount.bonus {
        color: #7c3aed;
        background: rgba(124, 58, 237, 0.1);
    }

    .salary-amount.total {
        color: #059669;
        background: rgba(5, 150, 105, 0.15);
        font-size: 1rem;
        font-weight: 800;
    }

    /* Period Info */
    .period-info {
        text-align: left;
    }

    .period-month {
        font-weight: 700;
        color: #1f2937;
        font-size: 0.9rem;
    }

    .period-date {
        font-size: 0.75rem;
        color: #6b7280;
    }

    /* Pagination Styles */
    .pagination-wrapper {
        display: flex;
        justify-content: between;
        align-items: center;
        padding: 1.5rem;
        background: rgba(248, 250, 252, 0.8);
        backdrop-filter: blur(8px);
        border-top: 1px solid rgba(226, 232, 240, 0.8);
    }

    .pagination-info {
        color: #64748b;
        font-weight: 500;
    }

    .pagination .page-link {
        color: #667eea;
        border: none;
        margin: 0 0.25rem;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        transition: all 0.3s ease;
    }

    .pagination .page-item.active .page-link {
        background: var(--primary-gradient);
        border: none;
        color: white;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-icon {
        background: var(--glass-bg);
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        backdrop-filter: blur(16px);
        border: 1px solid var(--glass-border);
    }

    .empty-icon i {
        font-size: 2.5rem;
        color: #9ca3af;
    }

    .empty-title {
        color: #374151;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .empty-description {
        color: #6b7280;
        margin-bottom: 2rem;
    }

    /* Modal Styles */
    .modal-modern .modal-header {
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: 20px 20px 0 0;
    }

    .modal-modern .modal-content {
        border-radius: 20px;
        border: none;
        overflow: hidden;
    }

    .modal-modern .modal-footer {
        background: rgba(248, 250, 252, 0.8);
        backdrop-filter: blur(8px);
        border: none;
    }

    .attachment-info {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .payroll-image-preview {
        max-width: 100%;
        max-height: 400px;
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .payroll-image-preview:hover {
        transform: scale(1.02);
    }

    .empty-attachment {
        background: rgba(254, 226, 226, 0.8);
        border-radius: 12px;
        padding: 2rem;
        color: #dc2626;
    }

    .empty-attachment i {
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {

        .profile-avatar2 {
            width: 50px;
            height: 50px;
        }

        .profile-image2 {
            width: 50px;
            height: 50px;
        }

        .profile-status {
            width: 16px;
            height: 16px;
            border: 2px solid white;
            bottom: -1px;
            right: -1px;
        }


        .employee-icon {
            width: 80px;
            height: 80px;
        }

        .employee-icon i {
            font-size: 2rem;
        }

        .pagination-wrapper {
            flex-direction: column;
            gap: 1rem;
        }

        .stat-item {
            padding: 0.75rem;
        }
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Enhanced pulse animation for better visibility */
    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
            box-shadow: 0 3px 10px rgba(74, 222, 128, 0.4);
        }

        50% {
            transform: scale(1.1);
            box-shadow: 0 3px 15px rgba(74, 222, 128, 0.6);
        }
    }

    .glass-card {
        animation: fadeInUp 0.6s ease-out;
    }

    .profile-status {
        animation: pulse 2s infinite;
    }

    /* Loading States */
    .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    /* Custom Scrollbar */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: var(--primary-gradient);
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #667eea 100%);
    }
</style>

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable with enhanced options
        $('#payrollTable').DataTable({
            "paging": false,
            "searching": false,
            "info": false,
            "ordering": true,
            "order": [
                [1, "desc"]
            ],
            "columnDefs": [{
                "orderable": false,
                "targets": [0, 11]
            }],
            "language": {
                "emptyTable": "No payroll records available",
                "zeroRecords": "No matching records found"
            }
        });

        // Enhanced View Attachment Modal
        $('.view-attachment-btn').on('click', function() {
            const fileUrl = $(this).data('file');
            const employeeName = $(this).data('employee');
            const period = $(this).data('period');

            // Show loading state
            $('#view-attachment-info').html(`
                <div class="d-flex align-items-center justify-content-center py-3">
                    <div class="spinner-border text-primary me-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span>Loading attachment...</span>
                </div>
            `);

            // Set attachment info
            setTimeout(() => {
                $('#view-attachment-info').html(`
                    <div class="row">
                        <div class="col-6">
                            <strong>Employee:</strong><br>
                            <span class="text-muted">${employeeName}</span>
                        </div>
                        <div class="col-6">
                            <strong>Period:</strong><br>
                            <span class="text-muted">${period}</span>
                        </div>
                    </div>
                `);
            }, 500);

            if (fileUrl) {
                $('#payroll-image').attr('src', fileUrl).removeClass('d-none');
                $('#download-attachment').attr('href', fileUrl).removeClass('d-none');
                $('#no-image-message').addClass('d-none');

                // Handle image load error
                $('#payroll-image').on('error', function() {
                    $(this).addClass('d-none');
                    $('#no-image-message').removeClass('d-none');
                    $('#download-attachment').addClass('d-none');
                });
            } else {
                $('#payroll-image').addClass('d-none');
                $('#download-attachment').addClass('d-none');
                $('#no-image-message').removeClass('d-none');
            }

            $('#viewAttachmentModal').modal('show');
        });

        // Enhanced animations on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all cards
        document.querySelectorAll('.glass-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease-out';
            observer.observe(card);
        });

        // Add hover effects to table rows
        $('.table-row-hover').hover(
            function() {
                $(this).addClass('shadow-sm');
            },
            function() {
                $(this).removeClass('shadow-sm');
            }
        );

        // Enhanced success notifications
        function showSuccessMessage(message) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                showConfirmButton: false,
                timer: 3000,
                background: 'rgba(255, 255, 255, 0.95)',
                backdrop: 'rgba(0, 0, 0, 0.4)',
                customClass: {
                    popup: 'border-0 shadow-xl',
                    title: 'text-success fw-bold',
                    content: 'text-muted'
                }
            });
        }

        // Add loading states for filter changes
        $('#filterForm input[name="month_year"]').on('change', function() {
            const form = document.getElementById('filterForm');
            const submitBtn = form.querySelector('button[type="submit"]') ||
                form.querySelector('input[type="submit"]');

            // Show loading overlay
            $('body').append(`
                <div id="loading-overlay" class="position-fixed w-100 h-100 d-flex align-items-center justify-content-center" 
                     style="top: 0; left: 0; background: rgba(255, 255, 255, 0.8); z-index: 9999; backdrop-filter: blur(4px);">
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted">Filtering payroll records...</p>
                    </div>
                </div>
            `);
        });

        // Remove loading overlay when page loads
        $(window).on('load', function() {
            $('#loading-overlay').fadeOut(300, function() {
                $(this).remove();
            });
        });
    });

    function clearFilters() {
        // Show confirmation dialog
        Swal.fire({
            title: 'Clear Filters?',
            text: 'This will reset all filters and show all payroll records.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#667eea',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Yes, clear filters',
            cancelButtonText: 'Cancel',
            background: 'rgba(255, 255, 255, 0.95)',
            backdrop: 'rgba(0, 0, 0, 0.4)',
            customClass: {
                popup: 'border-0 shadow-xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                $('body').append(`
                    <div id="loading-overlay" class="position-fixed w-100 h-100 d-flex align-items-center justify-content-center" 
                         style="top: 0; left: 0; background: rgba(255, 255, 255, 0.8); z-index: 9999; backdrop-filter: blur(4px);">
                        <div class="text-center">
                            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted">Resetting filters...</p>
                        </div>
                    </div>
                `);

                setTimeout(() => {
                    window.location.href = window.location.pathname;
                }, 500);
            }
        });
    }

    // Add smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Enhanced table responsiveness
    function adjustTableLayout() {
        const table = document.querySelector('.modern-table');
        const container = document.querySelector('.table-responsive');

        if (window.innerWidth < 768) {
            // Mobile optimization
            table.classList.add('table-sm');
            container.style.fontSize = '0.85rem';
        } else {
            table.classList.remove('table-sm');
            container.style.fontSize = '';
        }
    }

    // Call on load and resize
    window.addEventListener('load', adjustTableLayout);
    window.addEventListener('resize', adjustTableLayout);

    // Add keyboard navigation
    document.addEventListener('keydown', function(e) {
        // Press 'F' to focus on filter
        if (e.key === 'f' || e.key === 'F') {
            const filterInput = document.querySelector('input[name="month_year"]');
            if (filterInput && !e.ctrlKey && !e.altKey) {
                e.preventDefault();
                filterInput.focus();
            }
        }

        // Press 'Escape' to clear filters
        if (e.key === 'Escape') {
            const activeModal = document.querySelector('.modal.show');
            if (!activeModal) {
                clearFilters();
            }
        }
    });

    // Add tooltips for better UX
    $(document).ready(function() {
        $('[title]').tooltip({
            placement: 'top',
            trigger: 'hover',
            customClass: 'custom-tooltip'
        });
    });
</script>

<!-- Add custom tooltip styles -->
<style>
    .custom-tooltip .tooltip-inner {
        background: var(--primary-gradient);
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .custom-tooltip .tooltip-arrow::before {
        border-top-color: #667eea;
    }
</style>
@endpush