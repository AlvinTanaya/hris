@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12 text-center mb-5">
            <h1 class="text-warning">
                <i class="fas fa-star"></i> Performance Grade Rules
            </h1>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
            <h3 class="card-title m-0 fw-bold"><i class="fas fa-sliders-h me-2"></i> Performance Grade List</h3>
            <a href="{{ route('evaluation.rule.performance.grade.create') }}" class="btn btn-light text-primary">
                <i class="fas fa-plus-circle me-1"></i> Add New Grade
            </a>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3 border-start border-success border-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="card-body">
            <div class="table-responsive">
                <table id="gradesTable" class="table table-bordered table-striped mb-3 pt-3 align-middle align-items-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Grade</th>
                            <th>Score Range</th>
                            <th>Description</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grades as $grade)
                        <tr>
                            <td>
                                <span class="badge bg-primary fs-5 px-3">{{ $grade->grade }}</span>
                            </td>
                            <td>
                                @if($grade->max_score)
                                <span class="badge bg-info text-dark">{{ $grade->min_score }} - {{ $grade->max_score }}</span>
                                @else
                                <span class="badge bg-success">{{ $grade->min_score }} and above</span>
                                @endif
                            </td>
                            <td>{{ $grade->description ?: 'No description' }}</td>
                            <td><small>{{ $grade->created_at->format('Y-m-d H:i:s') }}</small></td>
                            <td><small>{{ $grade->updated_at->format('Y-m-d H:i:s') }}</small></td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('evaluation.rule.performance.grade.edit', $grade->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-grade" data-id="{{ $grade->id }}">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-info-circle me-2"></i> No performance grade rules found
                                </div>
                                <a href="{{ route('evaluation.rule.performance.grade.create') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-plus-circle me-1"></i> Create Your First Grade Rule
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i> Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Are you sure you want to delete this grade rule? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <form id="deleteForm" method="POST">
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

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {

        $('#gradesTable').DataTable({});

        // Set up delete confirmation
        $('.delete-grade').on('click', function() {
            const gradeId = $(this).data('id');
            $('#deleteForm').attr('action', `/evaluation/rule/performance/grade/destroy/${gradeId}`);
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        });
    });
</script>
@endpush