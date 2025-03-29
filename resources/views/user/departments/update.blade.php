@extends('layouts.app')

@section('content')
<div class="container mt-4 mx-auto">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mt-2"><i class="fas fa-edit"></i> Edit Department</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.departments.update', $department->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="department" class="form-label">Department Name</label>
                            <input type="text" class="form-control" id="department" name="department"
                                value="{{ $department->department }}" required>
                            <small class="text-danger">* Department name must be unique and cannot be duplicated.</small>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('user.departments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection