@extends('layouts.app')

@section('content')
<div class="container mt-4 mx-auto">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="text-primary mt-2">
                        <i class="fas fa-exclamation-triangle"></i> Edit Warning Letter
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('warning.letter.update', $warning_letter->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="maker_id" value="{{ Auth::user()->id }}">
                        <input type="hidden" name="user_id" value="{{ $employee->id }}">

                        <div class="mb-3">
                            <label for="user_id" class="form-label">Employee</label>
                            <input type="text" name="user" readonly class="form-control" value="{{$employee->name}} ({{$employee->employee_id}})">

                            @error('user_id')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="reason_warning" class="form-label">Warning Reason</label>
                            <textarea class="form-control" id="reason_warning" name="reason_warning" rows="5" required>{{ $warning_letter->reason_warning }}</textarea>
                            @error('reason_warning')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('warning.letter.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update
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
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });
</script>
@endpush