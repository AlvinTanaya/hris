@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <!-- Filter Card -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-filter"></i> Filter Evaluations
                </div>
                <div class="card-body">
                    <form action="{{ route('evaluation.assign.performance.index', Auth::user()->id) }}" method="GET">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label for="filter-name">Employee</label>
                                <select id="filter-name" name="employee" class="form-control">
                                    <option value="">All Employees</option>
                                    @foreach($employeesList as $emp)
                                    <option value="{{ $emp->id }}" {{ request('employee') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="filter-position">Position</label>
                                <select id="filter-position" name="position" class="form-control">
                                    <option value="">All Positions</option>
                                    @foreach($positionsList as $position)
                                    <option value="{{ $position->id }}" {{ request('position') == $position->id ? 'selected' : '' }}>
                                        {{ $position->position }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="filter-department">Department</label>
                                <select id="filter-department" name="department" class="form-control">
                                    <option value="">All Departments</option>
                                    @foreach($departmentsList as $department)
                                    <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                                        {{ $department->department }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4 mb-2">
                                <label for="filter-month">Month</label>
                                <select id="filter-month" name="month" class="form-control">
                                    <option value="">All Months</option>
                                    @foreach(range(1, 12) as $month)
                                    <option value="{{ $month }}" {{ (request('month', $currentMonth) == $month) ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="filter-year">Year</label>
                                <select id="filter-year" name="year" class="form-control">
                                    <option value="">All Years</option>
                                    @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ (request('year', $currentYear) == $year) ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-search"></i> Apply Filters
                                </button>
                                &nbsp; &nbsp;
                                <a href="{{ route('evaluation.assign.performance.index', Auth::user()->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Performance Evaluations Assigned</h5>
                    <a href="{{ route('evaluation.assign.performance.create', Auth::user()->id) }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Assign New Evaluation
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif


                    <div class="table-responsive">
                        <table id="evaluations-table" class="table table-bordered table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Employee Name</th>
                                    <th>Position</th>
                                    <th>Department</th>
                                    <th>Evaluation Period</th>
                                    <th>Score</th>


                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($evaluations->count() > 0)
                                @foreach($evaluations as $key => $evaluation)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $evaluation->user->name ?? 'N/A' }}</td>
                                    <td>{{ $evaluation->user->position->position ?? 'N/A' }}</td>
                                    <td>{{ $evaluation->user->department->department ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($evaluation->date)->format('F Y') }}</td>
                                    <td>
                                        <strong>{{ fmod($evaluation->final_score, 1) == 0 ? number_format($evaluation->final_score, 0) : $evaluation->final_score }}</strong>
                                    </td>


                                    <td>
                                        <a href="{{ route('evaluation.assign.performance.edit', $evaluation->id) }}"
                                            class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="{{ route('evaluation.assign.performance.detail', $evaluation->id) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="8" class="text-center">No evaluations found</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#evaluations-table').DataTable({
            "order": [
                [6, "desc"]
            ], // Sort by created date by default
            "pageLength": 25,
            "language": {
                "search": "Search:"
            },
            "dom": '<"top"f>rt<"bottom"lip><"clear">',
            "columnDefs": [{
                    "orderable": false,
                    "targets": [7]
                } // Disable sorting on action column
            ]
        });

        // Apply custom filters
        $('#filter-name, #filter-position, #filter-department').on('change', function() {
            applyFilters();
        });

        // Date filters are mandatory
        $('#filter-month, #filter-year').on('change', function() {
            if ($('#filter-month').val() && $('#filter-year').val()) {
                applyFilters();

                // Make AJAX call to refresh data with new date
                $.ajax({
                    url: "{{ route('evaluation.assign.performance.filter') }}",
                    type: "GET",
                    data: {
                        month: $('#filter-month').val(),
                        year: $('#filter-year').val()
                    },
                    success: function(response) {
                        // Replace table content
                        table.clear().draw();
                        $.each(response.evaluations, function(index, item) {
                            let badgeClass = item.score >= 80 ? 'success' : (item.score >= 60 ? 'warning' : 'danger');

                            table.row.add([
                                index + 1,
                                item.user.name || 'N/A',
                                item.user.position.name || 'N/A',
                                item.user.department.name || 'N/A',
                                item.period,
                                '<span class="badge badge-' + badgeClass + '">' + item.score.toFixed(2) + '</span>',
                                item.created_at,
                                '<a href="/evaluation/assign/performance/edit/' + item.id + '" class="btn btn-sm btn-info"><i class="fas fa-edit"></i> Edit</a> ' +
                                '<a href="/evaluation/assign/performance/detail/' + item.id + '" class="btn btn-sm btn-secondary"><i class="fas fa-eye"></i> Detail</a>'
                            ]).draw(false);
                        });
                    }
                });
            }
        });

        // Reset filters
        $('#reset-filters').click(function() {
            $('#filter-name, #filter-position, #filter-department, #filter-month, #filter-year').val('');
            applyFilters();

            // Reload page to reset to default data
            window.location.reload();
        });

        // Filter function
        function applyFilters() {
            table.columns(1).search($('#filter-name').val());
            table.columns(2).search($('#filter-position').val());
            table.columns(3).search($('#filter-department').val());
            table.draw();
        }

        // Force selection of month and year on page load
        if (!$('#filter-month').val() || !$('#filter-year').val()) {
            $('#filter-month').val("{{ $currentMonth }}");
            $('#filter-year').val("{{ $currentYear }}");

            // Trigger change event to apply filters
            $('#filter-month').trigger('change');
        }
    });
</script>
@endsection