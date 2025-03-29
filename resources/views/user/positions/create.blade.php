@extends('layouts.app')

@section('content')
<div class="container mt-4 mx-auto">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mt-2"><i class="fas fa-plus-circle"></i> Create New Position</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.positions.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="position" class="form-label">Position Name</label>
                            <input type="text" class="form-control @error('position') is-invalid @enderror"
                                id="position" name="position" required>
                            <small class="text-danger">* Position name must be unique and cannot be duplicated.</small>
                            @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="ranking" class="form-label">Ranking</label>
                            <input type="number" class="form-control @error('ranking') is-invalid @enderror"
                                id="ranking" name="ranking" required>
                            <small class="text-muted">* Lower numbers indicate higher ranking.</small>
                            @error('ranking')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('user.positions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection