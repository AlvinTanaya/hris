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

                <div class="mb-3">
                    <label for="type" class="form-label">
                        <i class="fas fa-briefcase"></i> Rule Type
                    </label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="" disabled>Select type</option>
                        @foreach($defaultTypes as $defaultType)
                        <option value="{{ $defaultType }}" {{ $rule_shift->type == $defaultType ? 'selected' : '' }}>
                            {{ $defaultType }}
                        </option>
                        @endforeach
                        @foreach($types as $type)
                        <option value="{{ $type }}" {{ $rule_shift->type == $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                        @endforeach
                        <option value="Other">Other (Specify)</option>
                    </select>
                    <input type="text" class="form-control mt-2 {{ $rule_shift->type != 'Morning' && $rule_shift->type != 'Afternoon' && $rule_shift->type != 'Normal' ? '' : 'd-none' }}" id="custom_type" name="custom_type" value="{{ $rule_shift->type }}" placeholder="Enter custom rule type">
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-clock"></i> Mass Time Input
                    </div>
                    <div class="card-body">
                        <div class="row gx-3 align-items-end">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="mass_start_day" class="form-label">
                                        <i class="fas fa-calendar"></i> Start Day
                                    </label>
                                    <select class="form-control" id="mass_start_day">
                                        <option value="" selected disabled>Choose Start Day</option>
                                        @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                        <option value="{{ $day }}">{{ $day }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="mass_end_day" class="form-label">
                                        <i class="fas fa-calendar"></i> End Day
                                    </label>
                                    <select class="form-control" id="mass_end_day">
                                        <option value="" selected disabled>Choose End Day</option>
                                        @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                        <option value="{{ $day }}">{{ $day }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="mass_start_time" class="form-label">
                                        <i class="fas fa-clock"></i> Start Time
                                    </label>
                                    <input type="time" class="form-control" id="mass_start_time">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="mass_end_time" class="form-label">
                                        <i class="fas fa-clock"></i> End Time
                                    </label>
                                    <input type="time" class="form-control" id="mass_end_time">
                                </div>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-primary w-100" id="apply_mass_time">
                                    <i class="fas fa-check"></i> Apply
                                </button>
                            </div>
                        </div>


                    </div>
                </div>

                <table class="table table-bordered mt-4">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Days</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $startTimes = json_decode($rule_shift->hour_start, true) ?? [];
                        $endTimes = json_decode($rule_shift->hour_end, true) ?? [];
                        @endphp

                        @foreach ($days as $index => $day)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $day }} <input type="hidden" name="days[]" value="{{ $day }}"></td>
                            <td>
                                <input type="time" class="form-control" name="start_time[{{ $day }}]"
                                    value="{{ $startTimes[$index] ?? '' }}">
                            </td>
                            <td>
                                <input type="time" class="form-control" name="end_time[{{ $day }}]"
                                    value="{{ $endTimes[$index] ?? '' }}">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <p class="text-danger">Note: If a rule is not needed on a day, leave the time fields empty.</p>

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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#type').change(function() {
            if ($(this).val() === 'Other') {
                $('#custom_type').removeClass('d-none').prop('required', true);
            } else {
                $('#custom_type').addClass('d-none').prop('required', false);
            }
        });

        let daysOrder = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

        $('#apply_mass_time').click(function() {
            let startDay = $('#mass_start_day').val();
            let endDay = $('#mass_end_day').val();
            let startTime = $('#mass_start_time').val();
            let endTime = $('#mass_end_time').val();

            if (!startDay || !endDay) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Selection',
                    text: 'Please select both Start Day and End Day!',
                });
                return;
            }
            if (!startTime || !endTime) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Time',
                    text: 'Please enter both Start Time and End Time!',
                });
                return;
            }
            if (startTime === endTime) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Time',
                    text: 'Start Time and End Time cannot be the same!',
                });
                return;
            }

            let startIndex = daysOrder.indexOf(startDay);
            let endIndex = daysOrder.indexOf(endDay);

            if (startIndex > endIndex) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Range',
                    text: 'Start Day must be before or the same as End Day!',
                });
                return;
            }

            // Apply mass input to selected days
            for (let i = startIndex; i <= endIndex; i++) {
                let day = daysOrder[i];
                $('input[name="start_time[' + day + ']"]').val(startTime);
                $('input[name="end_time[' + day + ']"]').val(endTime);
            }

            // kosongkan
            $('#mass_start_day').val('');
            $('#mass_end_day').val('');
            $('#mass_start_time').val('');
            $('#mass_end_time').val('');


            Swal.fire({
                icon: 'success',
                title: 'Time Applied',
                text: `Time has been applied to ${startDay} - ${endDay}!`,
            });
        });


        $('form').submit(function(event) {
            let isValid = true;
            let missingFields = [];

            $('tbody tr').each(function() {
                let day = $(this).find('input[name^="days"]').val();
                let startTime = $(this).find('input[name^="start_time"]').val();
                let endTime = $(this).find('input[name^="end_time"]').val();

                if ((startTime && !endTime) || (!startTime && endTime)) {
                    isValid = false;
                    missingFields.push(day);
                }
            });

            if (!isValid) {
                event.preventDefault(); // Stop form submission

                Swal.fire({
                    icon: 'error',
                    title: 'Incomplete Time Input',
                    html: `Start Time and End Time must be both filled or both empty for: <b>${missingFields.join(', ')}</b>`,
                });
            }
        });

    });
</script>
@endpush