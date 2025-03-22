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
                            <label for="type" class="form-label">Warning Type</label>
                            <select class="form-select" name="type" id="type" required>
                                <option value="">Select Warning Type</option>
                                <!-- Warning types will be populated via AJAX -->
                            </select>
                            <small class="text-muted type-info"></small>
                            @error('type')
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
        // Load available warning types when page loads
        loadAvailableWarningTypes();
        
        function loadAvailableWarningTypes() {
            let userId = "{{ $employee->id }}";
            let warningId = "{{ $warning_letter->id }}";
            let currentType = "{{ $warning_letter->type }}";
            
            if(userId) {
                $.ajax({
                    url: "{{ route('warning.letter.get-available-types-for-edit') }}",
                    type: "GET",
                    data: {
                        user_id: userId, 
                        warning_id: warningId
                    },
                    success: function(data) {
                        $('#type').empty();
                        $('#type').append('<option value="">Select Warning Type</option>');
                        
                        $.each(data.available_types, function(key, value) {
                            let selected = (key === currentType) ? 'selected' : '';
                            $('#type').append('<option value="' + key + '" ' + selected + '>' + value + '</option>');
                        });
                        
                        // If current type is not in available types, add it anyway
                        if(!data.available_types[currentType]) {
                            $('#type').append('<option value="' + currentType + '" selected>' + getWarningTypeLabel(currentType) + '</option>');
                        }
                        
                        $('.type-info').html(data.message || '');
                    }
                });
            }
        }
        
        function getWarningTypeLabel(type) {
            const typeLabels = {
                'Verbal': 'Verbal Warning',
                'ST1': 'ST1 (Surat Teguran 1)',
                'ST2': 'ST2 (Surat Teguran 2)',
                'SP1': 'SP1 (Surat Peringatan 1)',
                'SP2': 'SP2 (Surat Peringatan 2)',
                'SP3': 'SP3 (Surat Peringatan 3)'
            };
            
            return typeLabels[type] || type;
        }
    });
</script>
@endpush