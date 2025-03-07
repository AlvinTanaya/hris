@extends('layouts.app')

@section('content')
<a href="{{ route('time.rule.index') }}" class="btn btn-danger ms-2 px-5">
    <i class="fas fa-arrow-left me-2"></i> Back
</a>

<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px">
    <i class="fas fa-edit"></i> Edit Shift Rule
</h1>

<div class="container mt-4 mx-auto">
    <div class="card shadow-lg">
        <div class="card-body">
            <form action="{{ route('time.rule.update', $rule_shift->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">
                            <i class="fas fa-briefcase"></i> Rule Type
                        </label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="" disabled>Select type</option>

                            {{-- Default Types --}}
                            @foreach($defaultTypes as $defaultType)
                            <option value="{{ $defaultType }}" {{ $rule_shift->type == $defaultType ? 'selected' : '' }}>
                                {{ $defaultType }}
                            </option>
                            @endforeach

                            {{-- Tambahkan hanya jika dari database tidak ada di default types --}}
                            @foreach($types as $type)
                            <option value="{{ $type }}" {{ $rule_shift->type == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                            @endforeach
                            <option value="Other">Other (Specify)</option>
                        </select>
                        <input type="text" class="form-control mt-2 d-none" id="custom_type" name="custom_type" placeholder="Enter custom rule type">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="days" class="form-label">
                            <i class="fas fa-calendar"></i> Rule Days
                        </label>
                        <select class="form-control select2" id="days" name="days[]" multiple required>
                            @php
                            $selectedDays = json_decode($rule_shift->days, true);
                            @endphp
                            <option value="Monday" {{ in_array('Monday', $selectedDays ?? []) ? 'selected' : '' }}>Monday</option>
                            <option value="Tuesday" {{ in_array('Tuesday', $selectedDays ?? []) ? 'selected' : '' }}>Tuesday</option>
                            <option value="Wednesday" {{ in_array('Wednesday', $selectedDays ?? []) ? 'selected' : '' }}>Wednesday</option>
                            <option value="Thursday" {{ in_array('Thursday', $selectedDays ?? []) ? 'selected' : '' }}>Thursday</option>
                            <option value="Friday" {{ in_array('Friday', $selectedDays ?? []) ? 'selected' : '' }}>Friday</option>
                            <option value="Saturday" {{ in_array('Saturday', $selectedDays ?? []) ? 'selected' : '' }}>Saturday</option>
                            <option value="Sunday" {{ in_array('Sunday', $selectedDays ?? []) ? 'selected' : '' }}>Sunday</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="hour_start" class="form-label">
                            <i class="fas fa-clock"></i> Start Time
                        </label>
                        <input type="time" class="form-control" id="hour_start" name="hour_start" value="{{ $rule_shift->hour_start }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="hour_end" class="form-label">
                            <i class="fas fa-clock"></i> End Time
                        </label>
                        <input type="time" class="form-control" id="hour_end" name="hour_end" value="{{ $rule_shift->hour_end }}" required>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-success px-5">
                        <i class="fas fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<!-- SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script>
    $(document).ready(function() {
        $('#days').select2({
            width: '100%',
            placeholder: "Select Days",
            allowClear: true
        });

        $('#type').change(function() {
            if ($(this).val() === 'Other') {
                $('#custom_type').removeClass('d-none').prop('required', true);
            } else {
                $('#custom_type').addClass('d-none').prop('required', false);
            }
        });
    });
</script>
@endpush