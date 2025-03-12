@extends('layouts.app')

@section('content')
<div class="container mt-4 mx-auto">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="text-primary mt-2">
                        <i class="fas fa-exclamation-triangle"></i> Create Warning Letter
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('warning.letter.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="maker_id" value="{{ Auth::user()->id }}">
                        
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Employee</label>
                            <select class="form-select select2" name="user_id" id="user_id" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }} - {{ $employee->position }} ({{ $employee->department }})</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="reason_warning" class="form-label">Warning Reason</label>
                            <textarea class="form-control" id="reason_warning" name="reason_warning" rows="5" required></textarea>
                            @error('reason_warning')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('warning.letter.index') }}" class="btn btn-danger">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save
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