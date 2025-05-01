@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12 text-center mb-5">
            <h1 class="text-warning">
                <i class="fas fa-star"></i> Discipline Score Rules Management
            </h1>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white p-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-sliders-h me-2"></i>
                <h4 class="mb-0">Discipline Score List</h4>
            </div>
        </div>
        <div class="card-body p-0">
            <!-- Enhanced Tabs with Icons -->
            <ul class="nav nav-tabs nav-fill bg-light" id="rulesTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active py-3" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab">
                        <i class="fas fa-percentage me-1"></i> Attendance %
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3" id="late-tab" data-bs-toggle="tab" data-bs-target="#late" type="button" role="tab">
                        <i class="fas fa-clock me-1"></i> Late
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3" id="early-leave-tab" data-bs-toggle="tab" data-bs-target="#early-leave" type="button" role="tab">
                        <i class="fas fa-sign-out-alt me-1"></i> Early Leave (Pulang Awal)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3" id="afternoon-shift-tab" data-bs-toggle="tab" data-bs-target="#afternoon-shift" type="button" role="tab">
                        <i class="fas fa-sun me-1"></i> Afternoon Shift
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3" id="st-tab" data-bs-toggle="tab" data-bs-target="#st" type="button" role="tab">
                        <i class="fas fa-medkit me-1"></i> ST
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3" id="sp-tab" data-bs-toggle="tab" data-bs-target="#sp" type="button" role="tab">
                        <i class="fas fa-id-card-alt me-1"></i> SP
                    </button>
                </li>
            </ul>

            <div class="tab-content p-4" id="rulesTabContent">
                <!-- Attendance Rules Tab -->
                <div class="tab-pane fade show active" id="attendance" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-primary mb-0">
                            <i class="fas fa-percentage me-2"></i>Attendance Percentage Rules (Aturan Persentase Kehadiran)
                        </h5>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addAttendanceRuleModal">
                            <i class="fas fa-plus me-1"></i> Add Rule
                        </button>
                    </div>

                    <div class="alert alert-info bg-light border-start border-info border-4 shadow-sm">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x text-info"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">Attendance Rules Information</h6>
                                <p class="mb-0">Rules for attendance percentage ranges. Each range must be unique and not overlap with others.</p>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive rounded">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3">Range</th>
                                    <th class="py-3">Score</th>
                                    <th class="py-3 text-center" width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendanceRules as $rule)
                                <tr>
                                    <td class="align-middle">
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-normal" style="font-size: 1rem;">
                                            @if($rule->min_value == 100 && $rule->max_value == 100)
                                            Exactly 100%
                                            @elseif($rule->max_value)
                                            @if($rule->min_value == 0)
                                            Below {{ $rule->max_value }}%
                                            @else
                                            {{ $rule->min_value }}% - {{ $rule->max_value }}%
                                            @endif
                                            @else
                                            {{ $rule->min_value }}% and above
                                            @endif
                                        </span>
                                    </td>
                                    <td class="align-middle fw-bold">{{ $rule->score_value }}</td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary edit-rule" style="border-radius: 4px;"
                                                data-id="{{ $rule->id }}"
                                                data-rule-type="{{ $rule->rule_type }}"
                                                data-min-value="{{ $rule->min_value }}"
                                                data-max-value="{{ $rule->max_value }}"
                                                data-score-value="{{ $rule->score_value }}"
                                                data-operation="{{ $rule->operation }}">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </button>
                                            &nbsp;
                                            <form action="{{ route('evaluation.rule.discipline.score.destroy', $rule->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-outline-danger btn-delete">
                                                    <i class="fas fa-trash-alt me-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-info-circle fa-2x mb-3 text-secondary"></i>
                                            <p>No rules defined yet</p>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAttendanceRuleModal">
                                                <i class="fas fa-plus me-1"></i> Create First Rule
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Late Rules Tab -->
                <div class="tab-pane fade" id="late" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-primary mb-0">
                            <i class="fas fa-clock me-2"></i>Late Occurrence (Kejadian/Peristiwa) Rules
                        </h5>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addLateRuleModal">
                            <i class="fas fa-plus me-1"></i> Add Rule
                        </button>
                    </div>

                    <div class="alert alert-info bg-light border-start border-info border-4 shadow-sm">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x text-info"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">Late Rules Information</h6>
                                <p class="mb-0">Rules for late Occurrence (Kejadian/Peristiwa). Multiple non-overlapping ranges allowed.</p>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive rounded">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3">Range</th>
                                    <th class="py-3">Score Deduction (Pengurangan Skor)</th>
                                    <th class="py-3 text-center" width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lateRules as $rule)
                                <tr>
                                    <td class="align-middle">
                                        <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill fw-normal" style="font-size: 1rem;">

                                            @if($rule->max_value)
                                            @if($rule->min_value == 0)
                                            Up to {{ $rule->max_value}} times
                                            @else
                                            {{ $rule->min_value }}-{{ $rule->max_value}} times
                                            @endif
                                            @else
                                            {{ $rule->min_value }}+ times
                                            @endif
                                        </span>
                                    </td>
                                    <td class="align-middle fw-bold text-danger">-{{ $rule->score_value }}</td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary edit-rule" style="border-radius: 4px;"
                                                data-id="{{ $rule->id }}"
                                                data-rule-type="{{ $rule->rule_type }}"
                                                data-min-value="{{ $rule->min_value }}"
                                                data-max-value="{{ $rule->max_value }}"
                                                data-score-value="{{ $rule->score_value }}"
                                                data-operation="{{ $rule->operation }}">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </button>

                                            &nbsp;
                                            <form action="{{ route('evaluation.rule.discipline.score.destroy', $rule->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-outline-danger btn-delete">
                                                    <i class="fas fa-trash-alt me-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-info-circle fa-2x mb-3 text-secondary"></i>
                                            <p>No rules defined yet</p>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addLateRuleModal">
                                                <i class="fas fa-plus me-1"></i> Create First Rule
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Other Rules Tabs -->
                @foreach(['early_leave' => ['Early Leave (Pulang Awal)', 'fa-sign-out-alt', 'danger'],
                'afternoon_shift' => ['Afternoon Shift', 'fa-sun', 'warning'],
                'st' => ['Surat Teguran (ST)', 'fa-medkit', 'info'],
                'sp' => ['Surat Peringatan (SP)', 'fa-id-card-alt', 'secondary']] as $type => $data)
                <div class="tab-pane fade" id="{{ str_replace('_', '-', $type) }}" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-primary mb-0">
                            <i class="fas {{ $data[1] }} me-2"></i>{{ $data[0] }} Rules
                        </h5>
                        @if(empty($otherRules[$type]))
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#add{{ ucfirst($type) }}RuleModal">
                            <i class="fas fa-plus me-1"></i> Add Rule
                        </button>
                        @endif
                    </div>

                    <div class="alert alert-info bg-light border-start border-info border-4 shadow-sm">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x text-info"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">{{ $data[0] }} Rules Information</h6>
                                <p class="mb-0">Rules for {{ strtolower($data[0]) }}. Deduction is applied based on Occurrence Multiplier (Pengali Kejadian).</p>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive rounded">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3">Rule</th>
                                    <th class="py-3 text-center" width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($otherRules[$type]))
                                <tr>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-{{ $data[2] }} rounded-circle p-3 me-3">
                                                <i class="fas {{ $data[1] }}"></i>
                                            </span>
                                            <span>
                                                Deduction of <span class="fw-bold text-danger">{{ $otherRules[$type]->score_value }}</span>
                                                every <span class="fw-bold">{{ $otherRules[$type]->occurrence }}</span> occurrence (Kejadian/Peristiwa)(s)
                                            </span>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary edit-rule" style="border-radius: 4px;"
                                                data-id="{{ $otherRules[$type]->id }}"
                                                data-rule-type="{{ $otherRules[$type]->rule_type }}"
                                                data-occurrence="{{ $otherRules[$type]->occurrence }}"
                                                data-score-value="{{ $otherRules[$type]->score_value }}"
                                                data-operation="{{ $otherRules[$type]->operation }}">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </button>
                                            &nbsp;
                                            <form action="{{ route('evaluation.rule.discipline.score.destroy', $otherRules[$type]->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-outline-danger btn-delete">
                                                    <i class="fas fa-trash-alt me-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @else
                                <tr>
                                    <td colspan="2" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-info-circle fa-2x mb-3 text-secondary"></i>
                                            <p>No rules defined yet</p>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#add{{ ucfirst($type) }}RuleModal">
                                                <i class="fas fa-plus me-1"></i> Create First Rule
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Include modals -->
@include('evaluation.rule.discipline.score.modals')

<!-- Enhanced Edit Rule Modal -->
<div class="modal fade" id="editRuleModal" tabindex="-1" aria-labelledby="editRuleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editRuleModalLabel">
                    <i class="fas fa-edit me-2"></i>Edit Rule
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRuleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <!-- Form fields will be dynamically populated -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update Rule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Display SweetAlert for errors
    @if($errors -> any())
    Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        html: `@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach`,
        background: '#fff',
        iconColor: '#dc3545',
        confirmButtonColor: '#0d6efd'
    });
    @endif

    // Success message
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success ') }}',
        background: '#fff',
        iconColor: '#198754',
        confirmButtonColor: '#0d6efd',
        timer: 3000
    });
    @endif


    $(document).ready(function() {

        // Delete confirmation - Fixed event handler
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');

            Swal.fire({
                title: 'Are you sure?',
                text: "This rule will be permanently deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash-alt me-1"></i> Yes, delete it!',
                cancelButtonText: '<i class="fas fa-times me-1"></i> Cancel',
                reverseButtons: true,
                background: '#fff',
                iconColor: '#fd7e14',
                heightAuto: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Enhanced Edit Modal Functionality
        $('.edit-rule').click(function() {
            var id = $(this).data('id');
            var ruleType = $(this).data('rule-type');
            var minValue = $(this).data('min-value') || 0;
            var maxValue = $(this).data('max-value');
            var occurrence = $(this).data('occurrence');
            var scoreValue = $(this).data('score-value');
            var operation = $(this).data('operation');

            var modal = $('#editRuleModal');
            var form = $('#editRuleForm');
            var title = '';
            var icon = '';

            form.attr('action', '/evaluation/rule/discipline/score/update/' + id);
            modal.find('.modal-body').empty();

            // Add hidden inputs
            modal.find('.modal-body').append('<input type="hidden" name="rule_type" value="' + ruleType + '">');
            modal.find('.modal-body').append('<input type="hidden" name="operation" value="' + operation + '">');

            // Set modal title and icon based on rule type
            switch (ruleType) {
                case 'attendance':
                    title = 'Edit Attendance Rule';
                    icon = 'percentage';
                    break;
                case 'late':
                    title = 'Edit Late Rule';
                    icon = 'clock';
                    break;
                case 'early_leave':
                    title = 'Edit Early Leave (Pulang Awal) (Pulang Awal) Rule';
                    icon = 'sign-out-alt';
                    break;
                case 'afternoon_shift':
                    title = 'Edit Afternoon Shift Rule';
                    icon = 'sun';
                    break;
                case 'st':
                    title = 'Edit Surat Teguran Rule';
                    icon = 'medkit';
                    break;
                case 'sp':
                    title = 'Edit Surat Peringatan Rule';
                    icon = 'id-card-alt';
                    break;
                default:
                    title = 'Edit Rule';
                    icon = 'edit';
            }

            // Update modal title with icon
            modal.find('.modal-title').html(`<i class="fas fa-${icon} me-2"></i>${title}`);

            if (ruleType === 'attendance') {
                modal.find('.modal-body').append(`
                    <div class="mb-4">
                        <label class="form-label fw-bold">Attendance Range (%)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Min</span>
                            <input type="number" class="form-control" name="min_value" step="1" min="0" max="100" value="${minValue}" required>
                            <span class="input-group-text bg-light">-</span>
                            <span class="input-group-text bg-light">Max</span>
                            <input type="number" class="form-control" name="max_value" step="1" min="0" max="100" value="${maxValue || ''}" placeholder="No limit">
                        </div>
                        <div class="form-text mt-2"><i class="fas fa-info-circle me-1"></i> Example: 0-80 means 0% to 80% (exclusive)</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Score Value</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-star"></i></span>
                            <input type="number" class="form-control" name="score_value" step="0.1" value="${scoreValue}" required>
                        </div>
                    </div>
                `);
            } else if (ruleType === 'late') {
                modal.find('.modal-body').append(`
                    <div class="mb-4">
                        <label class="form-label fw-bold">Occurrence (Kejadian/Peristiwa) Range</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Min</span>
                            <input type="number" class="form-control" name="min_value" min="0" value="${minValue}" required>
                            <span class="input-group-text bg-light">-</span>
                            <span class="input-group-text bg-light">Max</span>
                            <input type="number" class="form-control" name="max_value" min="${minValue ? parseInt(minValue) + 1 : 1}" value="${maxValue || ''}" placeholder="No limit">
                        </div>
                        <div class="form-text mt-2"><i class="fas fa-info-circle me-1"></i> Example: 0-4 means 0 to 3 times (exclusive)</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Score Deduction (Pengurangan Skor)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-danger"><i class="fas fa-minus-circle"></i></span>
                            <input type="number" class="form-control" name="score_value" step="0.1" value="${scoreValue}" required>
                        </div>
                        <div class="form-text mt-2"><i class="fas fa-info-circle me-1"></i> Positive value (will be deducted)</div>
                    </div>
                `);
            } else {
                modal.find('.modal-body').append(`
                    <div class="mb-4">
                        <label class="form-label fw-bold">Occurrence (Kejadian/Peristiwa) Multiplier</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-repeat"></i></span>
                            <input type="number" class="form-control" name="occurrence" min="1" value="${occurrence || 1}" required>
                        </div>
                        <div class="form-text mt-2"><i class="fas fa-info-circle me-1"></i> Deduction applied every X occurrences (Pengurangan diterapkan setiap X kejadian)</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Score Deduction (Pengurangan Skor)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-danger"><i class="fas fa-minus-circle"></i></span>
                            <input type="number" class="form-control" name="score_value" step="0.1" value="${scoreValue}" required>
                        </div>
                        <div class="form-text mt-2"><i class="fas fa-info-circle me-1"></i> Positive value (will be deducted)</div>
                    </div>
                `);
            }

            modal.modal('show');
        });
    });
</script>
@endpush