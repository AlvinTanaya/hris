@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit"></i> Edit Warning Letter Rule
                    </h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('warning.letter.rule.update', $rule->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ $rule->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="3">{{ $rule->description }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="expired_length" class="form-label">Expired Length (months)</label>
                            <input type="number" class="form-control" id="expired_length" 
                                   name="expired_length" min="1" value="{{ $rule->expired_length }}">
                            <small class="text-muted">Leave empty if this rule doesn't expire</small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('warning.letter.rule.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Rule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection