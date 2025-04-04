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
                            <select class="form-select" name="user_id" id="user_id" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }} - {{ $employee->position->position }} ({{ $employee->department->department }})</option>
                                @endforeach
                            </select>
                            @error('user_id')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type_id" class="form-label">Warning Type</label>
                            <select class="form-select" name="type_id" id="type_id" required>
                                <option value="">Select Warning Type</option>
                                <!-- Warning types will be populated via AJAX -->
                            </select>
                            <small class="text-muted type-info"></small>
                            @error('type_id')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="description-container" style="display: none;">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="3" readonly></textarea>
                            @error('description')
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
        // When employee is selected, load available warning types
        $('#user_id').change(function() {
            let userId = $(this).val();
            if (userId) {
                $.ajax({
                    url: "{{ route('warning.letter.get-available-types') }}",
                    type: "GET",
                    data: {
                        user_id: userId
                    },
                    success: function(data) {
                        $('#type_id').empty();
                        $('#type_id').append('<option value="">Select Warning Type</option>');

                        // Hide description field initially
                        $('#description').val('').parent().hide();

                        $.each(data.available_types, function(key, value) {
                            $('#type_id').append('<option value="' + key + '" data-description="' + data.description[key] + '">' + value + '</option>');
                        });

                        $('.type-info').html(data.message.replace(/<br>/g, '<br>'));

                        // Add change event handler for type selection
                        $('#type_id').change(function() {
                            var selectedOption = $(this).find('option:selected');
                            var description = selectedOption.data('description') || '';

                            if ($(this).val()) {
                                $('#description').val(description).parent().show();
                            } else {
                                $('#description').val('').parent().hide();
                            }
                        });
                    }
                });
            } else {
                $('#type_id').empty();
                $('#type_id').append('<option value="">Select Warning Type</option>');
                $('.type-info').html('');
            }
        });
    });
</script>
@endpush