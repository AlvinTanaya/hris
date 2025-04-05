@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="mb-0">Performance Evaluation Details</h5>
                    <a href="{{ route('evaluation.assign.performance.index', Auth::user()->id) }}" class="btn btn-danger">
                        <i class="fas fa-arrow-left mr-2"></i> Back to List
                    </a>
                </div>

                <div class="card-body">
                    <!-- Employee and Summary Cards -->
                    <div class="row mb-4">
                        <!-- Employee Information -->
                        <div class="col-lg-6 mb-4 mb-lg-0">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-primary text-white d-flex align-items-center">
                                    <i class="fas fa-user-tie mr-2"></i>
                                    <h6 class="mb-0">&nbsp;Employee Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="avatar avatar-xl mr-3">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" alt="Avatar" class="rounded-circle">
                                        </div>
                                        <div>
                                            <h4 class="mb-0">&nbsp;{{ $user->name }}</h4>
                                            <span class="text-muted">&nbsp;{{ $user->position->position ?? 'N/A' }}</span>
                                        </div>
                                    </div>

                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="fas fa-building text-muted mr-2"></i> Department</span>
                                            <span class="font-weight-bold">{{ $user->department->department ?? 'N/A' }}</span>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="fas fa-calendar-alt text-muted mr-2"></i> Evaluation Period</span>
                                            <span class="font-weight-bold">{{ date('F Y', strtotime($evaluation->date)) }}</span>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="fas fa-id-card text-muted mr-2"></i> Employee ID</span>
                                            <span class="font-weight-bold">{{ $user->employee_id ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Evaluation Summary -->
                        <div class="col-lg-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-primary text-white d-flex align-items-center">
                                    <i class="fas fa-chart-bar mr-2"></i>
                                    <h6 class="mb-0">&nbsp;Evaluation Summary</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-4">
                                        <div class="position-relative d-inline-block">
                                            <canvas id="scoreGauge" width="200" height="100"></canvas>
                                            <div class="position-absolute w-100 text-center" style="top: 60%; left: 50%; transform: translate(-50%, -50%);">
                                                <div class="display-3 font-weight-bold text-primary">{{ number_format($finalScore, 1) }}</div>
                                                <div class="text-uppercase text-muted small">Final Score</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="progress mb-3" style="height: 20px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"
                                            aria-valuemin="0" aria-valuemax="3"></div>
                                    </div>

                                    <div class="row text-center">
                                        <div class="col-4 border-right">
                                            <div class="text-muted small">Raw Score</div>
                                            <div class="h4 font-weight-bold">{{ number_format($weightedScore, 2) }}</div>
                                        </div>
                                        <div class="col-4 border-right">
                                            <div class="text-muted small">Total Reduction</div>
                                            <div class="h4 font-weight-bold text-danger">-{{ number_format($totalReduction, 2) }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-muted small">Final Score</div>
                                            <div class="h4 font-weight-bold text-primary">{{ number_format($finalScore, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Criteria -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-primary text-white d-flex align-items-center">
                                    <i class="fas fa-clipboard-check mr-2"></i>
                                    <h6 class="mb-0">&nbsp;Performance Criteria Scores</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th class="pl-4">Criteria</th>
                                                    <th class="text-center">Weight</th>
                                                    <th class="text-center">Rating (1-3)</th>
                                                    <th class="text-center">Score</th>
                                                    <th class="text-center">Max Score</th>
                                                    <th class="text-center">Visual</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Performance Criteria Section -->
                                                @foreach($criteriaScores as $criteria)
                                                <tr>
                                                    <td class="pl-4 font-weight-bold">{{ $criteria['criteria'] }}</td>
                                                    <td class="text-center">{{ $criteria['weight'] }}</td>
                                                    <td class="text-center">{{ $criteria['value'] }}</td>
                                                    <td class="text-center font-weight-bold">{{ $criteria['score'] }}</td>
                                                    <td class="text-center">{{ $criteria['max_score'] }}</td>
                                                    <td>
                                                        <div class="progress" style="height: 8px;">
                                                            <div class="progress-bar 
                                                                    @if($criteria['value'] >= 2.5) bg-success
                                                                    @elseif($criteria['value'] >= 1.5) bg-warning
                                                                    @else 
                                                                    bg-danger
                                                                    @endif
                                                                    "
                                                                style="width: {{ ($criteria['value']/3)*100 }}%">
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach


                                            </tbody>
                                            <tfoot class="bg-light">
                                                <tr>
                                                    <th class="pl-4">Total</th>
                                                    <th class="text-center">{{ $criteriaScores->sum('weight') }}</th>

                                                    <th></th>
                                                    <th class="text-center font-weight-bold text-primary">{{ number_format($weightedScore, 2) }}</th>
                                                    <th class="text-center">3</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(count($reductionDetails) > 0)
                    <!-- Warning Letter Reductions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-danger text-white d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle  mr-2"></i>
                                    <h6 class="mb-0">Warning Letter Reductions</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-dark">
                                                <tr>

                                                    <th class="pl-4">Type</th>
                                                    <th class="text-center">Letter Number</th>
                                                    <th class="text-center">Date</th>

                                                    <th class="text-center">Reduction Points</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($reductionDetails as $reduction)
                                                <tr>
                                                    <td class="pl-4 font-weight-bold">
                                                        {{ $reduction['type'] }}
                                                    </td>
                                                    <td class="text-center">{{ $reduction['letter_number'] }}</td>

                                                    <td class="text-center">{{ $reduction['date'] }}</td>

                                                    <td class="text-center font-weight-bold text-danger">-{{ $reduction['reduction'] }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-light">
                                                <tr>
                                                    <th colspan="3" class="text-right pr-4">Total Reduction:</th>
                                                    <th class="text-center font-weight-bold text-danger">-{{ $totalReduction }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Evaluator's Comments Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-primary text-white d-flex align-items-center">
                                    <i class="fas fa-edit mr-2"></i>
                                    <h6 class="mb-0">&nbsp; Evaluator's Comments</h6>
                                </div>

                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        @if($evaluation->messages->count() > 0)
                                        <table class="table table-hover mb-0">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th style="width: 5%" class="text-center">#</th>
                                                    <th>Comment</th>
                                                    <th style="width: 15%" class="text-center">Date/Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($evaluation->messages as $index => $message)
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td>{{ $message->message }}</td>
                                                    <td class="text-muted text-nowrap text-center">
                                                        {{ $message->created_at->format('d M Y H:i') }}
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @else
                                        <div class="p-4 text-center text-muted">
                                            <i class="fas fa-comment-slash fa-2x mb-3"></i>
                                            <p class="mb-0">There are no messages from the evaluator</p>
                                        </div>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer with print/download options -->
                <div class="card-footer bg-light ">
                    <div>
                        <span class="text-muted small">Evaluation ID: {{ $evaluation->id }}</span>
                    </div>
                  
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@endpush


<style>
    .avatar {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border-radius: 50%;
    }

    .avatar img {
        max-width: 100%;
        max-height: 100%;
        border-radius: 50%;
    }

    .card {
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, .05);
    }

    .table thead th {
        border-bottom: 1px solid #dee2e6;
        border-top: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .table td,
    .table th {
        vertical-align: middle;
        padding: 1rem;
    }

    .badge-pill {
        padding: 0.5em 0.8em;
        font-size: 0.85rem;
    }



    .progress {
        border-radius: 10px;
    }

    .progress-bar {
        border-radius: 10px;
    }

    .list-group-item {
        border-left: 0;
        border-right: 0;
        padding: 1rem 0;
    }

    .list-group-item:first-child {
        border-top: 0;
        padding-top: 0;
    }

    .list-group-item:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }
</style>
@endsection