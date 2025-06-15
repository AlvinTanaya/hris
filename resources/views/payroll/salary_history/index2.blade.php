@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- User Profile Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary">
                <div class="card-body text-white">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <img src="{{ $user->photo_profile_path ? asset('storage/'. $user->photo_profile_path) : asset('storage/default_profile.png') }}"
                                alt="Profile Picture"
                                class="profile-image rounded-circle shadow"
                                style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                        <div class="col">
                            <h3 class="mb-1 fw-bold">{{ $user->name }}</h3>
                            <p class="mb-1 opacity-75">Employee ID: {{ $user->employee_id }}</p>
                            <p class="mb-0 opacity-75">
                                <i class="fas fa-briefcase me-1"></i>
                                {{ $user->position->position ?? 'N/A' }} - {{ $user->department->department ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="col-auto">
                            <div class="text-end">
                                <h5 class="mb-1">Personal Salary History</h5>
                                <p class="mb-0 opacity-75">Track your salary progression</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="fas fa-history fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-primary mb-1">{{ $stats['total_records'] }}</h3>
                    <p class="text-muted mb-0 small">Total Records</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="fas fa-arrow-up fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1">{{ $stats['increases'] }}</h3>
                    <p class="text-muted mb-0 small">Salary Increases</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1">{{ $stats['overtime_changes'] }}</h3>
                    <p class="text-muted mb-0 small">Overtime Changes</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="fas fa-gift fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-info mb-1">{{ $stats['allowance_changes'] }}</h3>
                    <p class="text-muted mb-0 small">Allowance Changes</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card border-0 shadow">
        <div class="card-header bg-white border-bottom-0 p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="text-dark mb-1 fw-bold">
                        <i class="fas fa-chart-line me-2 text-primary"></i>
                        Your Salary Modification History
                    </h4>
                    <p class="text-muted mb-0 small">Detailed view of all your salary changes over time</p>
                </div>
                @if($salaryHistories->count() > 0)
                <div class="text-end">
                    <span class="badge bg-primary-soft text-primary px-3 py-2">
                        Latest Update: {{ $salaryHistories->first()->created_at->format('M d, Y') }}
                    </span>
                </div>
                @endif
            </div>
        </div>

        <div class="card-body p-0">
            @if($salaryHistories->count() > 0)
            <!-- Timeline View for Mobile -->
            <div class="d-md-none">
                <div class="timeline-container p-3">
                    @foreach($salaryHistories as $history)
                    <div class="timeline-item mb-4">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="fw-bold mb-0">Salary Update</h6>
                                        <small class="text-muted">{{ $history->created_at->format('M d, Y') }}</small>
                                    </div>

                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-light rounded">
                                                <small class="text-muted d-block">Previous</small>
                                                <strong class="text-dark">Rp {{ number_format($history->old_basic_salary, 0, ',', '.') }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-primary bg-opacity-10 rounded">
                                                <small class="text-muted d-block">Current</small>
                                                <strong class="text-primary">Rp {{ number_format($history->new_basic_salary, 0, ',', '.') }}</strong>
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                    $salaryDiff = $history->new_basic_salary - $history->old_basic_salary;
                                    $percentage = $history->old_basic_salary > 0 ? round(($salaryDiff / $history->old_basic_salary) * 100, 1) : 0;
                                    @endphp

                                    <div class="text-center">
                                        @if($salaryDiff > 0)
                                        <span class="badge bg-success">
                                            <i class="fas fa-arrow-up me-1"></i>+{{ abs($percentage) }}%
                                        </span>
                                        @elseif($salaryDiff < 0)
                                            <span class="badge bg-danger">
                                            <i class="fas fa-arrow-down me-1"></i>-{{ abs($percentage) }}%
                                            </span>
                                            @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-minus me-1"></i>No Change
                                            </span>
                                            @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Table View for Desktop -->
            <div class="d-none d-md-block">
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0" id="salary-history-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-uppercase text-dark fw-bold border-0 ps-4">Date</th>
                                <th class="text-uppercase text-dark fw-bold border-0">Position/Department</th>
                                <th class="text-uppercase text-dark fw-bold border-0">Previous Salary</th>
                                <th class="text-uppercase text-dark fw-bold border-0">New Salary</th>
                                <th class="text-uppercase text-dark fw-bold border-0">Change</th>
                                <th class="text-uppercase text-dark fw-bold border-0">Overtime Rate</th>
                                <th class="text-uppercase text-dark fw-bold border-0">Allowance</th>
                                <th class="text-uppercase text-dark fw-bold border-0 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salaryHistories as $index => $history)
                            <tr class="border-0">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="timeline-dot bg-primary me-3"></div>
                                        <div>
                                            <div class="fw-semibold">{{ $history->created_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $history->created_at->format('H:i') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-semibold">{{ $history->effectivePosition->position ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $history->effectiveDepartment->department ?? 'N/A' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-muted">
                                        Rp {{ number_format($history->old_basic_salary, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary">
                                        Rp {{ number_format($history->new_basic_salary, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td>
                                    @php
                                    $salaryDiff = $history->new_basic_salary - $history->old_basic_salary;
                                    $percentage = $history->old_basic_salary > 0 ? round(($salaryDiff / $history->old_basic_salary) * 100, 1) : 0;
                                    @endphp

                                    @if($salaryDiff > 0)
                                    <span class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-20 px-3 py-2">
                                        <i class="fas fa-arrow-up me-1"></i>+{{ abs($percentage) }}%
                                    </span>
                                    @elseif($salaryDiff < 0)
                                        <span class="badge bg-danger bg-opacity-20 text-danger border border-danger border-opacity-20 px-3 py-2">
                                        <i class="fas fa-arrow-down me-1"></i>-{{ abs($percentage) }}%
                                        </span>
                                        @else
                                        <span class="badge bg-secondary bg-opacity-20 text-secondary border border-secondary border-opacity-20 px-3 py-2">
                                            <i class="fas fa-minus me-1"></i>No Change
                                        </span>
                                        @endif
                                </td>
                                <td>
                                    <div class="small">
                                        <div class="text-muted">Rp {{ number_format($history->new_overtime_rate_per_hour, 0, ',', '.') }}/hr</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <div class="text-muted">Rp {{ number_format($history->new_allowance, 0, ',', '.') }}</div>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-outline-primary btn-sm rounded-pill view-details"
                                        data-id="{{ $history->id }}"
                                        data-user-name="{{ $user->name }}"
                                        data-old-salary="{{ $history->old_basic_salary }}"
                                        data-new-salary="{{ $history->new_basic_salary }}"
                                        data-old-ot="{{ $history->old_overtime_rate_per_hour }}"
                                        data-new-ot="{{ $history->new_overtime_rate_per_hour }}"
                                        data-old-allowance="{{ $history->old_allowance }}"
                                        data-new-allowance="{{ $history->new_allowance }}"
                                        data-date="{{ $history->created_at->format('Y-m-d H:i:s') }}">
                                        <i class="fas fa-eye me-1"></i>Details
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <div class="empty-state-illustration mb-4">
                    <i class="fas fa-chart-line text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                </div>
                <h5 class="text-muted fw-bold mb-2">No Salary History Yet</h5>
                <p class="text-muted mb-0">Your salary modification history will appear here once changes are made to your compensation.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Enhanced Salary History Detail Modal -->
<div class="modal fade" id="salaryDetailModal" tabindex="-1" aria-labelledby="salaryDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white border-0">
                <h5 class="modal-title fw-bold" id="salaryDetailModalLabel">
                    <i class="fas fa-file-invoice-dollar me-2"></i> Salary Change Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="employee-info mb-4 text-center">
                    <img src="{{ $user->photo_profile_path ? asset('storage/'. $user->photo_profile_path) : asset('storage/default_profile.png') }}"
                        alt="Profile Picture"
                        class="profile-image rounded-circle shadow mb-3 mx-auto d-block"
                        style="width: 80px; height: 80px; object-fit: cover;">
                    <h4 class="fw-bold employee-name mb-1"></h4>
                    <p class="text-muted change-date mb-0"><i class="far fa-calendar-alt me-1"></i> <span></span></p>
                </div>

                <div class="row g-4">
                    <!-- Basic Salary Change -->
                    <div class="col-12">
                        <div class="card bg-light border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-3 text-primary">
                                    <i class="fas fa-money-bill-wave me-2"></i>Basic Salary Change
                                </h6>
                                <div class="row align-items-center">
                                    <div class="col-5">
                                        <div class="text-center p-3 bg-white rounded">
                                            <p class="text-muted mb-1 small">Previous Salary</p>
                                            <h4 class="old-salary fw-bold mb-0 text-dark">Rp 0</h4>
                                        </div>
                                    </div>
                                    <div class="col-2 text-center">
                                        <i class="fas fa-arrow-right text-primary fa-2x"></i>
                                    </div>
                                    <div class="col-5">
                                        <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                                            <p class="text-muted mb-1 small">New Salary</p>
                                            <h4 class="new-salary fw-bold mb-0 text-primary">Rp 0</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <span class="badge salary-change-badge fs-6 px-3 py-2"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Overtime Rate Change -->
                    <div class="col-md-6">
                        <div class="card bg-light border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-3 text-warning">
                                    <i class="fas fa-clock me-2"></i>Overtime Rate (Per Hour)
                                </h6>
                                <div class="text-center mb-3">
                                    <div class="mb-2">
                                        <small class="text-muted">Previous</small>
                                        <div class="old-ot fw-bold text-dark">Rp 0</div>
                                    </div>
                                    <i class="fas fa-arrow-down text-warning"></i>
                                    <div class="mt-2">
                                        <small class="text-muted">New</small>
                                        <div class="new-ot fw-bold text-warning">Rp 0</div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <span class="badge ot-change-badge"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Allowance Change -->
                    <div class="col-md-6">
                        <div class="card bg-light border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-3 text-info">
                                    <i class="fas fa-gift me-2"></i>Allowance
                                </h6>
                                <div class="text-center mb-3">
                                    <div class="mb-2">
                                        <small class="text-muted">Previous</small>
                                        <div class="old-allowance fw-bold text-dark">Rp 0</div>
                                    </div>
                                    <i class="fas fa-arrow-down text-info"></i>
                                    <div class="mt-2">
                                        <small class="text-muted">New</small>
                                        <div class="new-allowance fw-bold text-info">Rp 0</div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <span class="badge allowance-change-badge"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
    .profile-image {
        border: 3px solid rgba(255, 255, 255, 0.3);
        transition: all 0.3s ease;
    }

    .profile-image:hover {
        transform: scale(1.05);
        border-color: rgba(255, 255, 255, 0.5);
    }

    /* For modal profile image */
    .modal-body .profile-image {
        border: 3px solid rgba(78, 115, 223, 0.2);
    }

    .modal-body .profile-image:hover {
        border-color: rgba(78, 115, 223, 0.4);
    }

    /* Enhanced Styles */
    body {
        background-color: #f8f9fa;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .bg-primary-soft {
        background-color: rgba(78, 115, 223, 0.1) !important;
    }

    /* Card Enhancements */
    .card {
        border-radius: 15px;
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    /* Avatar Styles */
    .avatar-xl {
        width: 80px;
        height: 80px;
        font-size: 2rem;
    }

    /* Timeline Styles for Mobile */
    .timeline-container {
        position: relative;
        padding-left: 30px;
    }

    .timeline-container::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, #667eea, #764ba2);
        border-radius: 1px;
    }

    .timeline-item {
        position: relative;
    }

    .timeline-marker {
        position: absolute;
        left: -22px;
        top: 20px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 0 0 2px #667eea;
    }

    /* Timeline Dot for Desktop */
    .timeline-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        position: relative;
    }

    .timeline-dot::before {
        content: '';
        position: absolute;
        top: 50%;
        left: -15px;
        width: 30px;
        height: 1px;
        background-color: #e9ecef;
        transform: translateY(-50%);
    }

    /* Table Enhancements */
    .table th {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 1.25rem 1rem;
        letter-spacing: 0.5px;
    }

    .table td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        border-color: #f8f9fa;
    }

    .table tbody tr:hover {
        background-color: rgba(78, 115, 223, 0.05);
        transform: scale(1.01);
        transition: all 0.2s ease;
    }

    /* Badge Enhancements */
    .badge {
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.75rem;
    }

    /* Button Enhancements */
    .btn {
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    }

    /* Modal Enhancements */
    .modal-content {
        border-radius: 15px;
        overflow: hidden;
    }

    .modal-header {
        padding: 1.5rem;
    }

    /* Responsive Enhancements */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 15px;
            padding-right: 15px;
        }

        .timeline-container {
            padding-left: 20px;
        }

        .timeline-marker {
            left: -17px;
        }
    }

    /* Loading Animation */
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

    .card {
        animation: fadeInUp 0.6s ease forwards;
    }

    .table tbody tr {
        animation: fadeInUp 0.4s ease forwards;
    }
</style>



@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable for desktop view only
        if (window.innerWidth >= 768) {
            $('#salary-history-table').DataTable({
                responsive: false,
                autoWidth: false,
                searching: false,
                ordering: true,
                order: [
                    [0, 'desc']
                ], // Sort by date descending
                pageLength: 10,
                lengthChange: false,
                info: true,
                pagingType: 'simple_numbers',
                language: {
                    info: 'Showing _START_ to _END_ of _TOTAL_ records',
                    infoEmpty: 'No records available',
                    infoFiltered: '(filtered from _MAX_ total records)',
                    paginate: {
                        first: 'First',
                        last: 'Last',
                        next: 'Next',
                        previous: 'Previous'
                    }
                },
                columnDefs: [{
                        orderable: false,
                        targets: [7]
                    }, // Disable sorting on Actions column
                    {
                        className: 'text-center',
                        targets: [4, 7]
                    } // Center align specific columns
                ],
                drawCallback: function() {
                    // Add custom styling after table redraw
                    $('.dataTables_paginate .paginate_button').addClass('btn btn-sm btn-outline-primary mx-1');
                    $('.dataTables_paginate .paginate_button.current').removeClass('btn-outline-primary').addClass('btn-primary');
                }
            });
        }

        // View Details Modal Handler
        $('.view-details').on('click', function() {
            const data = $(this).data();

            // Populate employee info
            $('.employee-initial').text(data.userName.charAt(0).toUpperCase());
            $('.employee-name').text(data.userName);
            $('.change-date span').text(new Date(data.date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }));

            // Populate salary information
            $('.old-salary').text('Rp ' + formatNumber(data.oldSalary));
            $('.new-salary').text('Rp ' + formatNumber(data.newSalary));
            $('.old-ot').text('Rp ' + formatNumber(data.oldOt));
            $('.new-ot').text('Rp ' + formatNumber(data.newOt));
            $('.old-allowance').text('Rp ' + formatNumber(data.oldAllowance));
            $('.new-allowance').text('Rp ' + formatNumber(data.newAllowance));

            // Calculate and display changes
            updateChangeDisplay('.salary-change-badge', data.oldSalary, data.newSalary, 'salary');
            updateChangeDisplay('.ot-change-badge', data.oldOt, data.newOt, 'overtime');
            updateChangeDisplay('.allowance-change-badge', data.oldAllowance, data.newAllowance, 'allowance');

            // Show modal
            $('#salaryDetailModal').modal('show');
        });

        // Format number with thousand separators
        function formatNumber(num) {
            return parseInt(num).toLocaleString('id-ID');
        }

        // Update change display with percentage and styling
        function updateChangeDisplay(selector, oldValue, newValue, type) {
            const diff = newValue - oldValue;
            const percentage = oldValue > 0 ? ((diff / oldValue) * 100).toFixed(1) : 0;
            const badge = $(selector);

            if (diff > 0) {
                badge.removeClass().addClass('badge bg-success')
                    .html('<i class="fas fa-arrow-up me-1"></i>+' + Math.abs(percentage) + '%');
            } else if (diff < 0) {
                badge.removeClass().addClass('badge bg-danger')
                    .html('<i class="fas fa-arrow-down me-1"></i>-' + Math.abs(percentage) + '%');
            } else {
                badge.removeClass().addClass('badge bg-secondary')
                    .html('<i class="fas fa-minus me-1"></i>No Change');
            }
        }

        // Responsive table handling
        $(window).resize(function() {
            if (window.innerWidth < 768) {
                // Destroy DataTable on mobile
                if ($.fn.DataTable.isDataTable('#salary-history-table')) {
                    $('#salary-history-table').DataTable().destroy();
                }
            } else {
                // Reinitialize DataTable on desktop
                if (!$.fn.DataTable.isDataTable('#salary-history-table')) {
                    initializeDataTable();
                }
            }
        });

        // Initialize DataTable function
        function initializeDataTable() {
            $('#salary-history-table').DataTable({
                responsive: false,
                autoWidth: false,
                searching: false,
                ordering: true,
                order: [
                    [0, 'desc']
                ],
                pageLength: 10,
                lengthChange: false,
                info: true,
                pagingType: 'simple_numbers',
                language: {
                    info: 'Showing _START_ to _END_ of _TOTAL_ records',
                    infoEmpty: 'No records available',
                    infoFiltered: '(filtered from _MAX_ total records)',
                    paginate: {
                        first: 'First',
                        last: 'Last',
                        next: 'Next',
                        previous: 'Previous'
                    }
                },
                columnDefs: [{
                        orderable: false,
                        targets: [7]
                    },
                    {
                        className: 'text-center',
                        targets: [4, 7]
                    }
                ],
                drawCallback: function() {
                    $('.dataTables_paginate .paginate_button').addClass('btn btn-sm btn-outline-primary mx-1');
                    $('.dataTables_paginate .paginate_button.current').removeClass('btn-outline-primary').addClass('btn-primary');
                }
            });
        }

        // Add smooth scrolling to table on mobile
        $('.table-responsive').on('scroll', function() {
            if ($(this).scrollLeft() > 0) {
                $(this).addClass('scrolled');
            } else {
                $(this).removeClass('scrolled');
            }
        });

        // Add loading animation delay for cards
        $('.card').each(function(index) {
            $(this).css('animation-delay', (index * 0.1) + 's');
        });

        // Enhanced tooltip initialization
        $('[data-bs-toggle="tooltip"]').tooltip({
            boundary: 'window'
        });

        // Print functionality (optional)
        $('.print-history').on('click', function() {
            window.print();
        });

        // Export functionality (optional)
        $('.export-history').on('click', function() {
            if ($.fn.DataTable.isDataTable('#salary-history-table')) {
                // Simple CSV export
                const table = $('#salary-history-table').DataTable();
                const data = table.rows().data().toArray();

                let csvContent = "Date,Position,Department,Previous Salary,New Salary,Change %,Overtime Rate,Allowance\n";

                data.forEach(function(row) {
                    // Extract text content from HTML elements
                    const rowData = $(row).map(function() {
                        return $(this).text().trim();
                    }).get();
                    csvContent += rowData.slice(0, -1).join(',') + '\n'; // Exclude actions column
                });

                // Download CSV
                const blob = new Blob([csvContent], {
                    type: 'text/csv'
                });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'salary_history.csv';
                a.click();
                window.URL.revokeObjectURL(url);
            }
        });

        // Modal animation enhancements
        $('#salaryDetailModal').on('show.bs.modal', function() {
            $(this).find('.modal-content').addClass('animate__animated animate__zoomIn');
        });

        $('#salaryDetailModal').on('hidden.bs.modal', function() {
            $(this).find('.modal-content').removeClass('animate__animated animate__zoomIn');
        });
    });
</script>
@endpush