@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 test">
    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="hero-card position-relative overflow-hidden">
                <div class="hero-bg"></div>
                <div class="hero-content position-relative z-index-2 text-center py-5">
                    <div class="hero-icon mb-3">
                        <i class="fas fa-wallet fa-3x text-white"></i>
                    </div>
                    <h1 class="text-white fw-bold mb-2">My Salary Information</h1>
                    <p class="text-white-50 mb-0 fs-5">Your complete compensation overview</p>
                </div>
            </div>
        </div>
    </div>

    @if($employeeSalary)
    <!-- Profile Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <img src="{{ $employeeSalary->user->photo_profile_path ? asset('storage/'. $employeeSalary->user->photo_profile_path) : asset('storage/default_profile.png') }}" 
                             alt="Profile Picture" 
                             class="avatar-img">
                        <div class="avatar-badge">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                    <div class="profile-info">
                        <h3 class="profile-name">{{ $employeeSalary->user->name }}</h3>
                        <p class="profile-email">{{ $employeeSalary->user->email }}</p>
                        <div class="profile-id">
                            <span class="badge bg-primary-gradient px-3 py-2">
                                <i class="fas fa-id-card me-2"></i>
                                Employee ID: #{{ str_pad($employeeSalary->user->id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Salary Cards -->
    <div class="row g-4 mb-4">
        <!-- Basic Salary -->
        <div class="col-lg-3 col-md-6">
            <div class="salary-stat-card bg-primary-gradient" data-aos="fade-up" data-aos-delay="100">
                <div class="card-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="card-content">
                    <h6 class="card-title">Basic Salary</h6>
                    <h4 class="card-value">Rp {{ number_format($employeeSalary->basic_salary, 0, ',', '.') }}</h4>
                    <div class="card-trend">
                        <i class="fas fa-arrow-up"></i>
                        <span>Monthly</span>
                    </div>
                </div>
                <div class="card-decoration"></div>
            </div>
        </div>

        <!-- Overtime Rate -->
        <div class="col-lg-3 col-md-6">
            <div class="salary-stat-card bg-success-gradient" data-aos="fade-up" data-aos-delay="200">
                <div class="card-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="card-content">
                    <h6 class="card-title">Overtime Rate</h6>
                    <h4 class="card-value">Rp {{ number_format($employeeSalary->overtime_rate_per_hour, 0, ',', '.') }}</h4>
                    <div class="card-trend">
                        <i class="fas fa-clock"></i>
                        <span>Per Hour</span>
                    </div>
                </div>
                <div class="card-decoration"></div>
            </div>
        </div>

        <!-- Allowance -->
        <div class="col-lg-3 col-md-6">
            <div class="salary-stat-card bg-info-gradient" data-aos="fade-up" data-aos-delay="300">
                <div class="card-icon">
                    <i class="fas fa-gift"></i>
                </div>
                <div class="card-content">
                    <h6 class="card-title">Allowance</h6>
                    <h4 class="card-value">Rp {{ number_format($employeeSalary->allowance, 0, ',', '.') }}</h4>
                    <div class="card-trend">
                        <i class="fas fa-plus"></i>
                        <span>Benefits</span>
                    </div>
                </div>
                <div class="card-decoration"></div>
            </div>
        </div>

        <!-- Total Base -->
        <div class="col-lg-3 col-md-6">
            <div class="salary-stat-card bg-warning-gradient" data-aos="fade-up" data-aos-delay="400">
                <div class="card-icon">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="card-content">
                    <h6 class="card-title">Total Base</h6>
                    <h4 class="card-value">Rp {{ number_format($employeeSalary->basic_salary + $employeeSalary->allowance, 0, ',', '.') }}</h4>
                    <div class="card-trend">
                        <i class="fas fa-chart-line"></i>
                        <span>Monthly</span>
                    </div>
                </div>
                <div class="card-decoration"></div>
            </div>
        </div>
    </div>

    <!-- Details Section -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <!-- Salary Breakdown Chart -->
            <div class="card modern-card mb-4" data-aos="fade-up">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie text-primary me-2"></i>
                        Salary Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="salaryChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card modern-card" data-aos="fade-up" data-aos-delay="100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-warning me-2"></i>
                        Quick Info
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-list">
                        <div class="info-item">
                            <div class="info-icon bg-primary-light">
                                <i class="fas fa-calendar-alt text-primary"></i>
                            </div>
                            <div class="info-content">
                                <h6>Last Updated</h6>
                                <p>{{ $employeeSalary->updated_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon bg-success-light">
                                <i class="fas fa-user-check text-success"></i>
                            </div>
                            <div class="info-content">
                                <h6>Status</h6>
                                <p>Active Employee</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon bg-info-light">
                                <i class="fas fa-building text-info"></i>
                            </div>
                            <div class="info-content">
                                <h6>Department</h6>
                                <p>Human Resources</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @else
    <!-- No Data State -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="empty-state text-center py-5">
                <div class="empty-icon mb-4">
                    <i class="fas fa-exclamation-triangle fa-4x text-warning"></i>
                </div>
                <h3 class="text-muted mb-3">No Salary Data Found</h3>
                <p class="text-muted mb-4">Your salary information has not been set up yet. Please contact the HR department for assistance.</p>
                <button class="btn btn-primary-gradient btn-lg">
                    <i class="fas fa-envelope me-2"></i>
                    Contact HR Department
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection


<style>
    .test {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        --info-gradient: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);
        --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --card-shadow: 0 10px 30px rgba(0,0,0,0.1);
        --hover-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* Hero Section */
    .hero-card {
        border-radius: 20px;
        box-shadow: var(--card-shadow);
        min-height: 200px;
    }

    .hero-bg {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: var(--primary-gradient);
        border-radius: 20px;
    }

    .hero-bg::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        border-radius: 20px;
    }

    .z-index-2 {
        z-index: 2;
    }

    /* Profile Section */
    .profile-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
    }

    .profile-card:hover {
        box-shadow: var(--hover-shadow);
        transform: translateY(-5px);
    }

    .profile-header {
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .profile-avatar {
        position: relative;
    }

    .avatar-img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid white;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .avatar-badge {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 30px;
        height: 30px;
        background: #28a745;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        border: 3px solid white;
    }

    .profile-name {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #2d3748;
    }

    .profile-email {
        color: #718096;
        font-size: 1.1rem;
        margin-bottom: 1rem;
    }

    .bg-primary-gradient {
        background: var(--primary-gradient) !important;
    }

    /* Salary Stat Cards */
    .salary-stat-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        color: white;
        min-height: 180px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .salary-stat-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--hover-shadow);
    }

    .salary-stat-card .card-icon {
        font-size: 3rem;
        opacity: 0.3;
        position: absolute;
        top: 1rem;
        right: 1rem;
    }

    .salary-stat-card .card-content {
        position: relative;
        z-index: 2;
    }

    .salary-stat-card .card-title {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        opacity: 0.9;
    }

    .salary-stat-card .card-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .salary-stat-card .card-trend {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        opacity: 0.8;
    }

    .salary-stat-card .card-decoration {
        position: absolute;
        bottom: -50px;
        right: -50px;
        width: 100px;
        height: 100px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    .bg-success-gradient {
        background: var(--success-gradient) !important;
    }

    .bg-info-gradient {
        background: var(--info-gradient) !important;
    }

    .bg-warning-gradient {
        background: var(--warning-gradient) !important;
    }

    /* Modern Cards */
    .modern-card {
        border: none;
        border-radius: 20px;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
    }

    .modern-card:hover {
        box-shadow: var(--hover-shadow);
        transform: translateY(-2px);
    }

    .modern-card .card-header {
        padding: 1.5rem 1.5rem 0;
    }

    .modern-card .card-title {
        font-weight: 700;
        color: #2d3748;
    }

    /* Timeline */
    .timeline {
        position: relative;
        padding-left: 2rem;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e2e8f0;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 2rem;
    }

    .timeline-marker {
        position: absolute;
        left: -23px;
        top: 5px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .timeline-content {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 10px;
        border-left: 3px solid #e2e8f0;
    }

    .timeline-title {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #2d3748;
    }

    .timeline-text {
        color: #718096;
        margin-bottom: 0.5rem;
    }

    .timeline-date {
        color: #a0aec0;
        font-size: 0.8rem;
    }

    /* Info List */
    .info-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .info-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .bg-primary-light {
        background: rgba(102, 126, 234, 0.1);
    }

    .bg-success-light {
        background: rgba(17, 153, 142, 0.1);
    }

    .bg-info-light {
        background: rgba(0, 198, 255, 0.1);
    }

    .info-content h6 {
        font-weight: 600;
        margin-bottom: 0.25rem;
        color: #2d3748;
    }

    .info-content p {
        color: #718096;
        margin: 0;
        font-size: 0.9rem;
    }

    /* Buttons */
    .btn-primary-gradient {
        background: var(--primary-gradient);
        border: none;
        color: white;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .btn-primary-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        color: white;
    }

    .btn-outline-primary {
        border: 2px solid #667eea;
        color: #667eea;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
    }

    /* Empty State */
    .empty-state {
        background: white;
        border-radius: 20px;
        box-shadow: var(--card-shadow);
        padding: 3rem;
    }

    .empty-icon {
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        60% {
            transform: translateY(-5px);
        }
    }

    /* Chart Container */
    .chart-container {
        position: relative;
        height: 300px;
        padding: 1rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .profile-header {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }

        .salary-stat-card .card-value {
            font-size: 1.2rem;
        }

        .hero-content {
            padding: 2rem 1rem !important;
        }

        .profile-name {
            font-size: 1.5rem;
        }
    }
</style>


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Initialize AOS
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
    });

    @if($employeeSalary)
    // Initialize Chart
    const ctx = document.getElementById('salaryChart').getContext('2d');
    const salaryChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Basic Salary', 'Allowance', 'Overtime Potential'],
            datasets: [{
                data: [
                    {{ $employeeSalary->basic_salary }},
                    {{ $employeeSalary->allowance }},
                    {{ $employeeSalary->overtime_rate_per_hour * 40 }} // Estimated monthly overtime
                ],
                backgroundColor: [
                    'rgba(102, 126, 234, 0.8)',
                    'rgba(17, 153, 142, 0.8)',
                    'rgba(0, 198, 255, 0.8)'
                ],
                borderColor: [
                    'rgba(102, 126, 234, 1)',
                    'rgba(17, 153, 142, 1)',
                    'rgba(0, 198, 255, 1)'
                ],
                borderWidth: 2,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': Rp ' + context.parsed.toLocaleString('id-ID');
                        }
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    });
    @endif

    // Salary card hover effects
    $('.salary-stat-card').hover(
        function() {
            $(this).find('.card-icon').css('opacity', '0.6');
        },
        function() {
            $(this).find('.card-icon').css('opacity', '0.3');
        }
    );

    // Download payslip button
    $('.btn-primary-gradient:contains("Download")').click(function() {
        Swal.fire({
            title: 'Download Payslip',
            text: 'Your payslip is being prepared for download.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Download',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#667eea',
            customClass: {
                popup: 'swal-modern'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Payslip download started.',
                    icon: 'success',
                    confirmButtonColor: '#667eea',
                    customClass: {
                        popup: 'swal-modern'
                    }
                });
            }
        });
    });

    // Contact HR button
    $('.btn-outline-primary, .btn:contains("Contact HR")').click(function() {
        Swal.fire({
            title: 'Contact HR Department',
            html: `
                <div class="text-start">
                    <p><strong>Email:</strong> hr@company.com</p>
                    <p><strong>Phone:</strong> +62 123 456 7890</p>
                    <p><strong>Office Hours:</strong> Mon-Fri, 9:00 AM - 5:00 PM</p>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'Got it',
            confirmButtonColor: '#667eea',
            customClass: {
                popup: 'swal-modern'
            }
        });
    });

    // Add some interactive animations
    $('.timeline-item').hover(
        function() {
            $(this).find('.timeline-marker').css('transform', 'scale(1.2)');
        },
        function() {
            $(this).find('.timeline-marker').css('transform', 'scale(1)');
        }
    );

    // Smooth scrolling for better UX
    $('html').css('scroll-behavior', 'smooth');
});
</script>

<style>
/* SweetAlert2 Custom Styling */
.swal-modern {
    border-radius: 20px !important;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
}

.swal2-confirm {
    border-radius: 10px !important;
}
</style>
@endpush