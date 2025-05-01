@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Page Header -->
        <div class="col-12 text-center mb-5">
            <h1 class="text-warning">
                <i class="fas fa-money-check-alt me-2"></i>Edit Payroll Assignment
            </h1>
        </div>

        <!-- Employee Payroll Card -->
        <div class="col-md-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-edit fa-2x me-3"></i>
                            <h4 class="mb-0 fw-bold">Edit Payroll for {{ $payroll->user->name }}</h4>
                        </div>
                        <a href="{{ route('payroll.assign.index') }}" class="btn btn-danger btn-lg px-5">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('payroll.assign.update', $payroll->id) }}" method="POST" id="editPayrollForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Period Display -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light"
                                        value="{{ $payroll->created_at->format('F Y') }}" readonly>
                                    <label>Payroll Period</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light"
                                        value="{{ $payroll->user->employee_id ?? 'N/A' }}" readonly>
                                    <label>Employee ID</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light bg-gradient"
                                        value="{{ $payroll->absences ?? 0 }} day(s)" readonly>
                                    <label><i class="fas fa-calendar-times me-2 text-danger"></i>Absences This Month</label>
                                </div>
                            </div>
                        </div>

                        <!-- Employee Info -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center">
                                        <div class="avatar avatar-xxl bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                                            {{ substr($payroll->user->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control bg-light"
                                                        value="{{ $payroll->user->name }}" readonly>
                                                    <label>Employee Name</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control bg-light"
                                                        value="{{ $payroll->historical_department ?? $payroll->user->department->department ?? 'N/A' }}" readonly>
                                                    <label>Department</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control bg-light"
                                                        value="{{ $payroll->historical_position ?? $payroll->user->position->position ?? 'N/A' }}" readonly>
                                                    <label>Position</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Salary Components -->
                        <div class="row g-4">
                            <!-- Basic Salary -->
                            <div class="col-md-4">
                                <div class="card h-100 border-primary shadow-sm">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i> Basic Salary</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" name="basic_salary"
                                                id="basic_salary" value="{{ $payroll->basic_salary }}"
                                                step="0.01" min="0" required>
                                            <label for="basic_salary">Amount</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Overtime -->
                            <div class="col-md-4">
                                <div class="card h-100 border-warning shadow-sm">
                                    <div class="card-header bg-warning text-white">
                                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i> Overtime</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="form-floating">
                                                    <input type="number" class="form-control" name="overtime_hours"
                                                        id="overtime_hours" value="{{ $payroll->overtime_hours }}"
                                                        step="0.1" min="0" required>
                                                    <label for="overtime_hours">Hours</label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-floating">
                                                    <input type="number" class="form-control" name="overtime_rate"
                                                        id="overtime_rate" value="{{ $payroll->overtime_rate ?? ($payroll->overtime_hours > 0 ? $payroll->overtime_salary / $payroll->overtime_hours : 0) }}"
                                                        step="0.01" min="0" required>
                                                    <label for="overtime_rate">Rate/Hour</label>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-2">
                                                <div class="alert alert-info mb-0 py-2">
                                                    <small><i class="fas fa-info-circle me-1"></i> Overtime Total: <span id="overtime_total">${{ number_format($payroll->overtime_salary, 2) }}</span></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Allowance -->
                            <div class="col-md-4">
                                <div class="card h-100 border-info shadow-sm">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0"><i class="fas fa-hand-holding-usd me-2"></i> Allowance</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" name="allowance"
                                                id="allowance" value="{{ $payroll->allowance }}"
                                                step="0.01" min="0" required>
                                            <label for="allowance">Amount</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bonus -->
                            <div class="col-md-4">
                                <div class="card h-100 border-success shadow-sm">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0"><i class="fas fa-gift me-2"></i> Bonus</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" name="bonus"
                                                id="bonus" value="{{ $payroll->bonus }}"
                                                step="0.01" min="0">
                                            <label for="bonus">Amount</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reduction -->
                            <div class="col-md-4">
                                <div class="card h-100 border-danger shadow-sm">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="mb-0"><i class="fas fa-minus-circle me-2"></i> Reduction</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" name="reduction_salary"
                                                id="reduction_salary" value="{{ $payroll->reduction_salary }}"
                                                step="0.01" min="0" required>
                                            <label for="reduction_salary">Amount</label>
                                        </div>
                                        @if($payroll->absences > 0)
                                        <div class="alert alert-warning mt-2 mb-0 py-2">
                                            <small><i class="fas fa-exclamation-triangle me-1"></i> {{ $payroll->absences }} day(s) absent this month</small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Total Calculation -->
                            <div class="col-md-4">
                                <div class="card h-100 border-dark shadow-sm">
                                    <div class="card-header bg-dark text-white">
                                        <h5 class="mb-0"><i class="fas fa-calculator me-2"></i> Total Calculation</h5>
                                    </div>
                                    <div class="card-body text-center">
                                        <h3 class="total-amount mb-0" id="total_amount">
                                            ${{ number_format($payroll->basic_salary + $payroll->overtime_salary + $payroll->allowance + $payroll->bonus - $payroll->reduction_salary, 2) }}
                                        </h3>
                                        <small class="text-muted">Gross Pay</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attachment Section -->
                        <div class="card mt-4 border-0 shadow-sm">
                            <div class="card-header bg-primary bg-opacity-25">
                                <h5 class="mb-0"><i class="fas fa-paperclip me-2"></i> Payroll Attachment</h5>
                            </div>
                            <div class="card-body">
                                @if($payroll->file_path)
                                <div class="d-flex align-items-center mb-3 p-3 border rounded bg-light">
                                    <div class="me-3">
                                        <i class="fas fa-file-image fa-3x text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Current Attachment</h6>
                                        <small class="text-muted">{{ basename($payroll->file_path) }}</small><br>
                                        <small class="text-muted">Uploaded on {{ $payroll->updated_at->format('M d, Y H:i') }}</small>
                                    </div>
                                    <div>
                                        <a href="{{ asset('storage/'.$payroll->file_path) }}"
                                            class="btn btn-sm btn-primary me-2"
                                            target="_blank"
                                            data-bs-toggle="tooltip"
                                            title="View Attachment">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ asset('storage/'.$payroll->file_path) }}"
                                            class="btn btn-sm btn-success"
                                            download
                                            data-bs-toggle="tooltip"
                                            title="Download Attachment">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                                @else
                                <div class="alert alert-warning mb-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i> No attachment uploaded for this payroll
                                </div>
                                @endif

                                <div class="mt-3">
                                    <label for="attachment" class="form-label"><i class="fas fa-upload me-1"></i> Update Attachment</label>
                                    <input class="form-control" type="file" id="attachment" name="attachment" accept="image/*,.pdf">
                                    <small class="text-muted">Upload a new payroll document (JPG, PNG only, max 2MB)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-danger btn-lg px-5" id="deletePayrollBtn">
                                <i class="fas fa-trash-alt me-2"></i> Delete Payroll
                            </button>
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                <i class="fas fa-save me-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteConfirmationModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i> Confirm Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this payroll record for <strong>{{ $payroll->user->name }}</strong> ({{ $payroll->created_at->format('F Y') }})?</p>
                <p class="text-danger"><i class="fas fa-exclamation-circle me-2"></i>This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancel
                </button>
                <form action="{{ route('payroll.assign.destroy', $payroll->id) }}" method="POST" id="deletePayrollForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-2"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .border-primary {
        border-left: 4px solid #4e73df !important;
    }

    .border-warning {
        border-left: 4px solid #f6c23e !important;
    }

    .border-info {
        border-left: 4px solid #36b9cc !important;
    }

    .border-success {
        border-left: 4px solid #1cc88a !important;
    }

    .border-danger {
        border-left: 4px solid #e74a3b !important;
    }

    .border-dark {
        border-left: 4px solid #5a5c69 !important;
    }

    .total-amount {
        font-weight: 700;
        color: #2e59d9;
        font-size: 1.75rem;
    }

    .avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #3d5afe;
        box-shadow: 0 0 0 0.25rem rgba(61, 90, 254, 0.25);
    }

    .bg-light {
        background-color: #f8f9fc !important;
    }

    /* Progress animation for file upload */
    .progress-bar-animated {
        animation: progress-bar-stripes 1s linear infinite;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Calculate total when salary components change
        $('input[name="basic_salary"], input[name="overtime_hours"], input[name="overtime_rate"], input[name="allowance"], input[name="bonus"], input[name="reduction_salary"]').on('input', function() {
            calculateTotal();
        });

        // Calculate total function
        function calculateTotal() {
            const basicSalary = parseFloat($('#basic_salary').val()) || 0;
            const overtimeHours = parseFloat($('#overtime_hours').val()) || 0;
            const overtimeRate = parseFloat($('#overtime_rate').val()) || 0;
            const allowance = parseFloat($('#allowance').val()) || 0;
            const bonus = parseFloat($('#bonus').val()) || 0;
            const reduction = parseFloat($('#reduction_salary').val()) || 0;

            const overtimeAmount = overtimeHours * overtimeRate;
            $('#overtime_total').text('$' + overtimeAmount.toFixed(2));

            const total = basicSalary + allowance + bonus + overtimeAmount - reduction;

            $('#total_amount').text('$' + total.toFixed(2));
        }

        // Delete payroll button click
        $('#deletePayrollBtn').click(function() {
            $('#deleteConfirmationModal').modal('show');
        });

        // Form submission handling
        $('#editPayrollForm').submit(function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);

            Swal.fire({
                title: 'Update Payroll?',
                text: "Are you sure you want to update this payroll record?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1cc88a',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, update it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Updating...',
                        html: 'Please wait while we update the payroll record',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit form via AJAX
                    $.ajax({
                        url: $(form).attr('action'),
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message || 'Payroll updated successfully',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = "{{ route('payroll.assign.index') }}";
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON?.message || 'Failed to update payroll',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });

        // File input change event - preview validation
        $('#attachment').change(function() {
            const fileInput = this;
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];

                // Check file size
                if (file.size > maxSize) {
                    Swal.fire({
                        title: 'File Too Large',
                        text: 'Maximum file size is 2MB',
                        icon: 'error'
                    });
                    fileInput.value = '';
                    return;
                }

                // Check file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    Swal.fire({
                        title: 'Invalid File Type',
                        text: 'Only JPG and PNG images are allowed',
                        icon: 'error'
                    });
                    fileInput.value = '';
                    return;
                }
            }
        });
    });
</script>
@endpush