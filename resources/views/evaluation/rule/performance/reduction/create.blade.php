@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Add Performance Reduction Rule</h4>
                </div>
                <div class="card-body">
                    <form id="createReductionForm">
                        @csrf

                        <div class="mb-3">
                            <label for="type_id" class="form-label">Warning Letter Type</label>
                            <select class="form-select" id="type_id" name="type_id" required>
                                <option value="">Select Type</option>
                                @foreach($types as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            <div id="type_id-error" class="invalid-feedback d-none"></div>
                        </div>

                        <div class="mb-3">
                            <label for="weight" class="form-label">Weight</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="weight" name="weight" required>
                            <small class="text-muted">Please enter a positive number</small>
                            <div id="weight-error" class="invalid-feedback d-none"></div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Active" selected>Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('evaluation.rule.performance.reduction.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save me-1"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Check if type exists on change
        $('#type_id').change(function() {
            let type_id = $(this).val();
            if (type_id) {
                $.ajax({
                    url: "{{ route('evaluation.rule.performance.reduction.check.type') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        type_id: type_id
                    },
                    success: function(response) {
                        if (response.exists) {
                            $('#type_id').addClass('is-invalid');
                            $('#type_id-error').text('This warning letter type already has a reduction rule').removeClass('d-none');
                            $('#submitBtn').prop('disabled', true);
                        } else {
                            $('#type_id').removeClass('is-invalid');
                            $('#type_id-error').addClass('d-none');
                            $('#submitBtn').prop('disabled', false);
                        }
                    }
                });
            }
        });

        $('#status').change(function() {
            if ($(this).val() === 'Inactive') {
                $('#weight').val(0).prop('readonly', true);
            } else {
                $('#weight').val('').prop('readonly', false);
            }
        });

        // Form submission
        $('#createReductionForm').submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('evaluation.rule.performance.reduction.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirect;
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            $('#' + field).addClass('is-invalid');
                            $('#' + field + '-error').text(errors[field][0]).removeClass('d-none');
                        }
                    }
                }
            });
        });
    });
</script>
@endpush