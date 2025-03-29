@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Employee Performance Evaluation</h2>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="month">Month</label>
                            <select name="month" id="month" class="form-control">
                                @foreach(range(1, 12) as $month)
                                <option value="{{ $month }}" {{ $month == $currentMonth ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="year">Year</label>
                            <select name="year" id="year" class="form-control">
                                @foreach(range(date('Y') - 5, date('Y')) as $year)
                                <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="department">Department</label>
                            <select name="department" id="department" class="form-control">
                                <option value="">All Departments</option>
                                @foreach($departments as $department)
                                <option value="{{ $department }}">{{ $department }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="position">Position</label>
                            <select name="position" id="position" class="form-control">
                                <option value="">All Positions</option>
                                @foreach($positions as $position)
                                <option value="{{ $position }}">{{ $position }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('evaluation.assign.performance.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Create New Evaluation
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Evaluation List -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Employee</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Total Score</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluations as $index => $evaluation)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $evaluation->employee_name }}</td>
                            <td>{{ $evaluation->position }}</td>
                            <td>{{ $evaluation->department }}</td>
                            <td>{{ number_format($evaluation->total_score, 2) }}</td>
                            <td>
                                <button class="btn btn-info btn-sm view-details" 
                                        data-user-id="{{ $evaluation->user_id }}"
                                        data-month="{{ $currentMonth }}"
                                        data-year="{{ $currentYear }}">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <a href="{{ route('evaluation.assign.performance.edit', ['id' => $evaluation->user_id]) }}" 
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Details -->
<div class="modal fade" id="evaluationDetailsModal" tabindex="-1" role="dialog" aria-labelledby="evaluationDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="evaluationDetailsModalLabel">Evaluation Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Weight</th>
                            <th>Score</th>
                            <th>Weighted Score</th>
                        </tr>
                    </thead>
                    <tbody id="detailsContent">
                        <!-- Content will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('.view-details').click(function() {
        var userId = $(this).data('user-id');
        var month = $(this).data('month');
        var year = $(this).data('year');
        
        $.ajax({
            url: "{{ route('evaluation.assign.performance.details') }}",
            type: "GET",
            data: {
                user_id: userId,
                month: month,
                year: year
            },
            success: function(response) {
                $('#detailsContent').html(response);
                $('#evaluationDetailsModal').modal('show');
            },
            error: function(xhr) {
                alert('Error loading details');
            }
        });
    });
});
</script>
@endsection