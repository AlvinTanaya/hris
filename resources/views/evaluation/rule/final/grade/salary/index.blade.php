@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-center mb-4">
        <div class="text-center">
            <h1 class="text-warning">
                <i class="fas fa-money-bill-wave"></i> E-learning Grade Salary Rules
            </h1>
        </div>
    </div>

    <div class="card bg-primary bg-gradient text-white shadow-lg">
        <div class="card-header bg-primary bg-gradient d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="fas fa-list-ul me-2"></i>
                <h4 class="mb-0">E-learning Grade Salary List</h4>
            </div>
            <a href="{{ route('evaluation.rule.grade.salary.create') }}" class="btn btn-light rounded-pill">
                <i class="fas fa-plus-circle me-1"></i> Add New Grade
            </a>
        </div>
        <div class="card-body bg-white text-dark rounded-bottom">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <div class="row mb-3">
                <div class="col-md-2">
                    <div class="d-flex align-items-center">
                        <label class="me-2">Show</label>
                        <select class="form-select form-select-sm" id="entries-select">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label class="ms-2">entries</label>
                    </div>
                </div>
                <div class="col-md-3 ms-auto">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" id="search-input" placeholder="Search...">
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table id="grade-salary-table" class="table table-hover align-middle">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th class="fw-bold">Grade</th>
                            <th class="fw-bold">Salary Value</th>
                            <th class="fw-bold">Created At</th>
                            <th class="fw-bold">Updated At</th>
                            <th class="fw-bold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gradeSalaries as $item)
                        <tr>
                            <td>
                                <span class="badge bg-primary rounded-pill px-3 py-2 fw-bold">{{ $item->grade }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark rounded-pill px-3 py-2">
                                    Rp {{ number_format($item->value_salary, 2) }}
                                </span>
                            </td>
                            <td>{{ $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '-' }}</td>
                            <td>{{ $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('evaluation.rule.grade.salary.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-btn" 
                                        data-id="{{ $item->id }}" 
                                        data-grade="{{ $item->grade }}">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Showing <span id="showing-entries">1 to {{ min(count($gradeSalaries), 10) }}</span> of {{ count($gradeSalaries) }} entries
                </div>
                <div class="pagination-container">
                    <button class="btn btn-sm btn-outline-secondary me-1" id="prev-btn" disabled>Previous</button>
                    <button class="btn btn-sm btn-primary mx-1">1</button>
                    <button class="btn btn-sm btn-outline-secondary ms-1" id="next-btn" {{ count($gradeSalaries) <= 10 ? 'disabled' : '' }}>Next</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle text-warning me-3 fs-1"></i>
                    <p class="mb-0">Are you sure you want to delete the grade salary rule for <strong id="delete-grade" class="text-danger"></strong>?</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <form id="delete-form" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    body {
        background: linear-gradient(135deg, #2b4c8a 0%, #1e3c72 100%);
    }
    .card {
        border-radius: 10px;
        overflow: hidden;
    }
    .table thead th {
        border-bottom: none;
        padding: 12px 15px;
    }
    .table tbody td {
        padding: 12px 15px;
    }
    .btn-outline-primary:hover, .btn-outline-danger:hover {
        color: white;
    }
    .pagination-container .btn {
        min-width: 40px;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Set up DataTable
        const table = $('#grade-salary-table').DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            ordering: true,
            searching: true,
            responsive: true,
            dom: 't', // Only show the table, we'll handle pagination and search ourselves
            language: {
                emptyTable: "No grade salary rules found"
            }
        });
        
        // Connect our custom search box to DataTable
        $('#search-input').on('keyup', function() {
            table.search(this.value).draw();
            updateShowingEntries();
        });
        
        // Connect our custom length menu to DataTable
        $('#entries-select').on('change', function() {
            table.page.len($(this).val()).draw();
            updateShowingEntries();
            
            // Update the Next button state
            const pageInfo = table.page.info();
            $('#next-btn').prop('disabled', pageInfo.page >= pageInfo.pages - 1);
        });
        
        // Custom pagination
        $('#prev-btn').on('click', function() {
            table.page('previous').draw('page');
            updatePaginationButtons();
            updateShowingEntries();
        });
        
        $('#next-btn').on('click', function() {
            table.page('next').draw('page');
            updatePaginationButtons();
            updateShowingEntries();
        });
        
        // Update showing entries text
        function updateShowingEntries() {
            const pageInfo = table.page.info();
            $('#showing-entries').text((pageInfo.start + 1) + ' to ' + pageInfo.end);
        }
        
        // Update pagination buttons state
        function updatePaginationButtons() {
            const pageInfo = table.page.info();
            $('#prev-btn').prop('disabled', pageInfo.page === 0);
            $('#next-btn').prop('disabled', pageInfo.page >= pageInfo.pages - 1);
            
            // Update active page button
            $('.pagination-container .btn-primary').removeClass('btn-primary').addClass('btn-outline-secondary');
            $(`.pagination-container .btn:contains('${pageInfo.page + 1}')`).removeClass('btn-outline-secondary').addClass('btn-primary');
        }
        
        // Delete confirmation modal
        $('.delete-btn').on('click', function() {
            const id = $(this).data('id');
            const grade = $(this).data('grade');
            
            $('#delete-grade').text(grade);
            $('#delete-form').attr('action', `{{ route('evaluation.rule.grade.salary.destroy', '') }}/${id}`);
            $('#deleteModal').modal('show');
        });
        
        // Auto-close alert after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    });
</script>
@endpush