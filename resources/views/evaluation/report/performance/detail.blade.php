@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold">Performance Evaluation Report</h5>
                    <div>
                        <a href="{{ route('evaluation.report.performance.index') }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Company Header -->
                    <div class="text-center mb-4">
                        <img src="{{ asset('storage/logoTimurJayaIndosteel.png') }}" alt="Logo" class="logo" style="height: 80px;">
                        <h3 class="mb-1 font-weight-bold">PT. TIMUR JAYA INDOSTEEL</h3>
                        <h4 class="text-primary font-weight-bold">PERFORMANCE EVALUATION</h4>
                        <div class="border-top border-primary mx-auto my-2" style="width: 100px;"></div>
                    </div>

                    <!-- Employee Information -->
                    <div class="row mb-4">
                        <div class="col-md-6 d-flex">
                            <div class="card border-0 shadow-sm w-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 font-weight-bold">Employee Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 text-center">
                                            <img src="{{ asset($user->photo_profile_path ? 'storage/'.$user->photo_profile_path : 'images/default_profile.png') }}"
                                                alt="Profile Picture"
                                                class="img-thumbnail mb-3"
                                                style="width: 120px; height: 120px; object-fit: cover;">
                                        </div>
                                        <div class="col-md-8">
                                            <table class="table table-borderless table-sm mb-0">
                                                <tr>
                                                    <th width="35%" class="text-muted">Employee</th>
                                                    <td>: {{ $user->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Period</th>
                                                    <td>: {{ $year }}</td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Position</th>
                                                    <td>: {{ optional($user->position)->position ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Department</th>
                                                    <td>: {{ optional($user->department)->department ?? 'N/A' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Summary -->
                        <div class="col-md-6 d-flex">
                            <div class="card border-0 shadow-sm w-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 font-weight-bold">Performance Summary</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center h-100">
                                        <!-- Final Score Section -->
                                        <div class="text-center px-3 flex-grow-1">
                                            @php
                                            $gradeColor = match($grade->grade ?? null) {
                                            'A' => 'success',
                                            'B' => 'primary',
                                            'C' => 'warning',
                                            'D' => 'danger',
                                            default => 'secondary'
                                            };
                                            @endphp

                                            <div class="text-muted small mb-1">FINAL SCORE</div>
                                            <div class="display-4 font-weight-bold text-{{ $gradeColor }}">{{ number_format($overallAverage, 0) }}</div>
                                        </div>

                                        <!-- Vertical Divider -->
                                        <div class="vr mx-2" style="height: 135px; opacity: 0.3;"></div>

                                        <!-- Grade Section -->
                                        <div class="text-center px-3 flex-grow-1">
                                            <div class="text-muted small mb-1">GRADE</div>
                                            <div>

                                                <span class="badge bg-{{ $gradeColor }} p-2" style="font-size: 1.2rem;">
                                                    {{ $grade ? $grade->grade : '?' }}
                                                </span>
                                                @if($grade && $grade->description)
                                                <div class="text-muted small mt-1">{{ $grade->description }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Table -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-0">
                            <div class="table-responsive rounded">
                                <table class="table table-bordered table-hover mb-0 ">
                                    <thead class="table-dark">
                                        <tr>
                                            <th rowspan="2" class="align-middle text-center" style="width: 30px;">#</th>
                                            <th rowspan="2" class="align-middle text-center" style="min-width: 150px;">CRITERIA</th>
                                            <th rowspan="2" class="align-middle text-center" style="width: 60px;">WEIGHT</th>
                                            @foreach($monthNames as $month)
                                            <th colspan="2" class="text-center">{{ strtoupper($month) }}</th>
                                            @endforeach
                                            <th colspan="2" class="text-center">FINAL</th>
                                        </tr>
                                        <tr>
                                            @foreach($monthNames as $month)
                                            <th class="text-center" style="width: 50px;">SCORE</th>
                                            <th class="text-center" style="width: 60px;">TOTAL</th>
                                            @endforeach
                                            <th class="text-center" style="width: 50px;">SCORE</th>
                                            <th class="text-center" style="width: 60px;">TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($criteriaList as $index => $criterion)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $criterion['name'] }}</td>
                                            <td class="text-center">{{ $criterion['weight'] }}</td>

                                            @foreach($monthlyData as $monthIndex => $month)
                                            @php
                                            $criteriaScore = collect($month['scores'])->firstWhere('name', $criterion['name']);
                                            $value = $criteriaScore['value'] ?? null;
                                            $total = $criteriaScore['score'] ?? null;

                                            $colors = [
                                            'high' => ['bg' => '#e7f1ff', 'text' => '#0062ff'],
                                            'medium' => ['bg' => '#e6ffed', 'text' => '#00a854'],
                                            'low' => ['bg' => '#fff0f0', 'text' => '#f5222d']
                                            ];

                                            $style = '';
                                            if ($value !== null) {
                                            $colorKey = $value >= 2.5 ? 'high' : ($value >= 1.5 ? 'medium' : 'low');
                                            $style = "background-color: {$colors[$colorKey]['bg']}; color: {$colors[$colorKey]['text']}";
                                            }
                                            @endphp

                                            <td class="text-center font-weight-bold" style="{{ $style }}">
                                                {{ $value !== null ? number_format($value, 1) : '' }}
                                            </td>
                                            <td class="text-center font-weight-bold" style="{{ $style }}">
                                                {{ $total !== null ? number_format($total, 0) : '' }}
                                            </td>
                                            @endforeach

                                            @php
                                            $finalValue = $averageValues[$criterion['name']] ?? 0;
                                            $finalTotal = $averageTotals[$criterion['name']] ?? 0;

                                            $finalColorKey = $finalValue >= 2.5 ? 'high' : ($finalValue >= 1.5 ? 'medium' : 'low');
                                            $finalStyle = "background-color: {$colors[$finalColorKey]['bg']}; color: {$colors[$finalColorKey]['text']}";
                                            @endphp

                                            <td class="text-center font-weight-bold" style="{{ $finalStyle }}">
                                                {{ number_format($finalValue, 1) }}
                                            </td>
                                            <td class="text-center font-weight-bold" style="{{ $finalStyle }}">
                                                {{ number_format($finalTotal, 0) }}
                                            </td>
                                        </tr>
                                        @endforeach

                                        <!-- Total Evaluation Score -->
                                        <tr class="font-weight-bold bg-light">
                                            <td colspan="2" class="text-right">TOTAL EVALUATION SCORE</td>
                                            <td class="text-center">{{ $totalPossible }}</td>

                                            @foreach($monthlyData as $monthIndex => $month)
                                            <td></td>
                                            <td class="text-center text-black fw-bolder">
                                                {{ $month['rawScore'] ? number_format($month['rawScore'], 0) : '' }}
                                            </td>
                                            @endforeach

                                            <td></td>
                                            <td class="text-center text-black fw-bolder">
                                                {{ number_format($overallRawAverage, 0) }}
                                            </td>
                                        </tr>

                                        <!-- Warning Letters & Deductions Section -->
                                        <tr>
                                            <td colspan="{{ 3 + (count($monthNames) * 2) + 2 }}" class="bg-danger text-white font-weight-bold text-uppercase">
                                                <i class="fas fa-exclamation-triangle mr-2"></i>Warning Letters & Deductions
                                            </td>
                                        </tr>

                                        @foreach($yearlyReductions as $ruleId => $ruleData)
                                        <tr>
                                            <td colspan="2">{{ $ruleData['name'] }}</td>
                                            <td class="text-center text-danger">-{{ $ruleData['weight'] }}</td>

                                            @foreach($monthNames as $monthIndex => $month)
                                            @php
                                            $monthNumber = $monthIndex + 1;
                                            $monthData = $ruleData['monthly'][$monthNumber] ?? ['count' => 0, 'reduction' => 0];
                                            $hasDeduction = $monthData['count'] > 0;
                                            @endphp

                                            <td class="text-center">
                                                @if($hasDeduction)
                                                <span class="badge bg-danger">{{ $monthData['count'] }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center text-danger">
                                                {{ $hasDeduction ? -$monthData['reduction'] : 0 }}
                                            </td>
                                            @endforeach

                                            <td class="text-center">
                                                @if($ruleData['total_count'] > 0)
                                                <span class="badge bg-danger">{{ $ruleData['total_count'] }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center text-danger">
                                                -{{ $ruleData['total_reduction'] }}
                                            </td>
                                        </tr>
                                        @endforeach

                                        <!-- Total Deductions Row -->
                                        <tr class="font-weight-bold bg-light">
                                            <td colspan="2" class="text-right">TOTAL DEDUCTIONS</td>
                                            <td class="text-center text-danger">-{{ $maxPossibleDeductions }}</td>

                                            @foreach($monthNames as $monthIndex => $month)
                                            <td></td>
                                            <td class="text-center text-danger">
                                                @php
                                                $monthDeduction = $monthlyData[$monthIndex]['deductions'] ?? 0;
                                                @endphp
                                                {{ $monthDeduction > 0 ? '-'.number_format($monthDeduction, 0) : 0 }}
                                            </td>
                                            @endforeach

                                            <td></td>
                                            <td class="text-center text-danger">-{{ number_format($totalDeductions, 0) }}</td>
                                        </tr>

                                        <!-- Final Score Row -->
                                        <tr class="font-weight-bold" style="background-color: #f8f9fa; border-top: 2px solid #dee2e6;">
                                            <td colspan="2" class="text-right">FINAL SCORE</td>
                                            <td></td>

                                            @foreach($monthlyData as $monthIndex => $month)
                                            <td></td>
                                            <td class="text-center text-black fw-bolder">
                                                {{ $month['finalScore'] ? number_format($month['finalScore'], 0) : '' }}
                                            </td>
                                            @endforeach

                                            <td></td>
                                            <td class="text-center text-black fw-bolder">
                                                {{ number_format($overallAverage, 0) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    @if($evaluationMessages && count($evaluationMessages) > 0)
                    <div class="row mt-4 border-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header pt-3 bg-primary text-warning">
                                    <h5><i class="fa-solid fa-circle-exclamation"></i>&nbsp;Evaluator's Comments:</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive rounded">
                                        <table class="table table-bordered ">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="65%">COMMENT</th>
                                                    <th width="30%">DATE</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($evaluationMessages as $index => $message)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $message->message }}</td>
                                                    <td>{{ $message->created_at->format('d M Y H:i') }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if(isset($evaluationId))
                                    <div class="mt-3 text-end text-muted small">
                                        Evaluation ID: {{ $evaluationId }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#btn-print').click(function() {
            window.print();
        });
    });
</script>

<style>
    body {
        background-color: #f8f9fa;
    }

    .card {
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .badge.bg-primary {
        background-color: #0062ff !important;
    }

    .badge.bg-secondary {
        background-color: #6c757d !important;
    }

    .card-header {
        padding: 0.75rem 1.25rem;
    }

    .table {
        font-size: 0.875rem;
    }

    .table thead th {
        vertical-align: middle;
        white-space: nowrap;
    }

    .table td,
    .table th {
        vertical-align: middle;
    }

    .badge {
        font-size: 0.75em;
        font-weight: 500;
        padding: 0.35em 0.65em;
    }

    .display-4 {
        font-size: 2.5rem;
        font-weight: 300;
        line-height: 1.2;
    }

    @media print {
        body {
            background-color: white;
            font-size: 11pt;
        }

        .card,
        .card-header {
            border: none;
            box-shadow: none;
        }

        .table {
            font-size: 9pt;
        }

        .no-print,
        .actions {
            display: none !important;
        }

        .card-body {
            padding: 0;
        }
    }

    @media (max-width: 768px) {
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }
</style>
@endsection