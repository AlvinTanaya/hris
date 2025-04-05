<!-- Modal for adding attendance rule -->
<div class="modal fade" id="addAttendanceRuleModal" tabindex="-1" aria-labelledby="addAttendanceRuleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="addAttendanceRuleModalLabel">
                    <i class="fas fa-calendar-check me-2"></i>Add Attendance Rule
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('evaluation.rule.discipline.score.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="rule_type" value="attendance">
                    <input type="hidden" name="operation" value="set">

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Define score values based on attendance percentage ranges.
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Attendance Range (%)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-percentage"></i></span>
                            <input type="number" class="form-control py-2" name="min_value" step="0.01" min="0" max="100" placeholder="Minimum %">
                            <span class="input-group-text bg-light fw-bold">to</span>
                            <input type="number" class="form-control py-2" name="max_value" step="0.01" min="0" max="100" placeholder="Maximum %">
                        </div>
                        <small class="text-muted mt-1 d-block">
                            <i class="fas fa-lightbulb me-1"></i> Leave max empty for "min and above" or leave min empty for "below max"
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Score Value</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-star"></i></span>
                            <input type="number" class="form-control py-2" name="score_value" required placeholder="Enter score value">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-save me-1"></i> Save Rule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for adding late rule -->
<div class="modal fade" id="addLateRuleModal" tabindex="-1" aria-labelledby="addLateRuleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold" id="addLateRuleModalLabel">
                    <i class="fas fa-clock me-2"></i>Add Late Arrival Rule
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('evaluation.rule.discipline.score.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="rule_type" value="late">
                    <input type="hidden" name="operation" value="subtract">

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>Define score deductions for late arrivals based on occurrence ranges.
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Occurrence Range</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-list-ol"></i></span>
                            <input type="number" class="form-control py-2" name="min_value" min="0" placeholder="Minimum occurrences">
                            <span class="input-group-text bg-light fw-bold">to</span>
                            <input type="number" class="form-control py-2" name="max_value" min="0" placeholder="Maximum occurrences">
                        </div>
                        <small class="text-muted mt-1 d-block">
                            <i class="fas fa-lightbulb me-1"></i> Leave max empty for "min and above" or leave min empty for "below max"
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Score Deduction</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-minus-circle text-danger"></i></span>
                            <input type="number" class="form-control py-2" name="score_value" required placeholder="Enter deduction value">
                        </div>
                        <small class="text-muted mt-1 d-block">
                            <i class="fas fa-info-circle me-1"></i> Enter positive value (will be subtracted from total score)
                        </small>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 text-white">
                        <i class="fas fa-save me-1"></i> Save Rule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modals for other rule types (Early Leave, Afternoon Shift, ST, SP) -->
@foreach(['early_leave', 'afternoon_shift', 'st', 'sp'] as $type)
@php
    $titleMap = [
        'early_leave' => 'Early Leave',
        'afternoon_shift' => 'Afternoon Shift',
        'st' => 'Surat Teguran',
        'sp' => 'Surat Peringatan'
    ];
    $iconMap = [
        'early_leave' => 'fa-sign-out-alt',
        'afternoon_shift' => 'fa-sun',
        'st' => 'fa-stopwatch',
        'sp' => 'fa-exclamation-circle'
    ];
    $colorMap = [
        'early_leave' => 'bg-danger',
        'afternoon_shift' => 'bg-info',
        'st' => 'bg-purple',
        'sp' => 'bg-secondary'
    ];
@endphp

<div class="modal fade" id="add{{ ucfirst($type) }}RuleModal" tabindex="-1" aria-labelledby="add{{ ucfirst($type) }}RuleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header {{ $colorMap[$type] }} text-white">
                <h5 class="modal-title fw-bold" id="add{{ ucfirst($type) }}RuleModalLabel">
                    <i class="fas {{ $iconMap[$type] }} me-2"></i>Add {{ $titleMap[$type] }} Rule
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('evaluation.rule.discipline.score.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="rule_type" value="{{ $type }}">
                    <input type="hidden" name="operation" value="subtract">

                    <div class="alert alert-{{ str_replace('bg-', '', $colorMap[$type]) }}">
                        <i class="fas fa-exclamation-circle me-2"></i>Define score deductions for {{ strtolower($titleMap[$type]) }} occurrences.
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Occurrence Multiplier</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-redo"></i></span>
                            <input type="number" class="form-control py-2" name="occurrence" min="1" value="1" required placeholder="Enter multiplier">
                        </div>
                        <small class="text-muted mt-1 d-block">
                            <i class="fas fa-lightbulb me-1"></i> Deduction will be applied every X occurrences (e.g., 3 means every 3 occurrences)
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Score Deduction</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-minus-circle text-danger"></i></span>
                            <input type="number" class="form-control py-2" name="score_value" required placeholder="Enter deduction value">
                        </div>
                        <small class="text-muted mt-1 d-block">
                            <i class="fas fa-info-circle me-1"></i> Enter positive value (will be subtracted from total score)
                        </small>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn {{ $colorMap[$type] }} rounded-pill px-4 text-white">
                        <i class="fas fa-save me-1"></i> Save Rule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<style>
    .bg-purple {
        background-color: #6f42c1;
    }
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        border-color: #86b7fe;
    }
    .modal-content {
        border-radius: 0.7rem;
        overflow: hidden;
    }
    .modal-header {
        padding: 1.2rem 1.5rem;
    }
    .modal-title {
        font-size: 1.25rem;
    }
    .modal-body {
        padding: 1.5rem 2rem;
    }
    .modal-footer {
        padding: 1.2rem 1.5rem;
    }
    .input-group-text {
        transition: all 0.3s ease;
    }
    .btn-rounded {
        border-radius: 50px;
    }
</style>