@extends('layouts.app')

@section('content')
<a href="{{ route('user.index') }}" class="btn btn-danger px-5 mb-3">
    <i class="fas fa-arrow-left me-2"></i>Back
</a>
<h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px"><i class="fas fa-exchange-alt me-2"></i> Employee Transfer</h1>

<div class="container mt-4 mx-auto">
    <form action="{{ route('user.transfer_user', $user->id) }}" method="POST">
        <div class="card shadow-lg border-0 rounded">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-2">Employee ID &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong>{{$user->employee_id}}</strong></h2>
                    <h4 class="mb-0">Nama Employee : <strong>{{$user->name}}</strong></h4>
            </div>

            <div class="card-body p-4">
                <!-- Display validation errors -->
                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif


                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <label for="transfer_type" class="form-label fw-bold">Jenis Transfer</label>
                        <select class="form-select" id="transfer_type" name="transfer_type" required>
                            <option selected disabled>Pilih Jenis Transfer</option>
                            @if ($user->employee_status !== "Tetap"){
                            <option value="Penetapan">Penetapan</option>
                            }
                            @endif
                            <option value="Demosi">Demosi</option>
                            <option value="Promosi">Promosi</option>
                            <option value="Mutasi">Mutasi</option>
                            <option value="Resign">Resign</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="Reason" class="form-label fw-bold">Reason</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                            <input type="text" class="form-control" id="reason" name="reason" placeholder="Masukkan reason" required>
                        </div>
                    </div>
                </div>


                <div id="transfer-fields" style="display: none;">
                    <h5 class="text-primary fw-bold mt-4">Transfer Detail</h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="old_position" class="form-label fw-bold">Old Position</label>
                            <input type="text" class="form-control bg-light" id="old_position" name="old_position" value="{{ old('position', $user->position) }}" readonly>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="old_department" class="form-label fw-bold">Old Department</label>
                            <input type="text" class="form-control bg-light" id="old_department" name="old_department" value="{{ old('department', $user->department) }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="new_position" class="form-label fw-bold">New Position</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                <select class="form-control" id="new_position" name="new_position" required>
                                    <option value="" disabled selected>Select Position</option>

                                    <option value="Director" {{ old('position') == 'Director' ? 'selected' : '' }}>Director</option>
                                    <option value="General Manager" {{ old('position') == 'General Manager' ? 'selected' : '' }}>General Manager</option>
                                    <option value="Manager" {{ old('position') == 'Manager' ? 'selected' : '' }}>Manager</option>
                                   
                                    <option value="Supervisor" {{ old('position') == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                                    <option value="Staff" {{ old('position') == 'Staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="new_department" class="form-label fw-bold">New Department</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                <select class="form-control" id="new_department" name="new_department" required>
                                    <option value="" selected disabled>Select Department</option>
                                    <option value="Director" {{ old('department') == 'Director' ? 'selected' : '' }}>
                                        Director
                                    </option>
                                    <option value="General Manager" {{ old('department') == 'General Manager' ? 'selected' : '' }}>
                                        General Manager
                                    </option>
                                    <option value="Human Resources" {{ old('department') == 'Human Resources' ? 'selected' : '' }}>
                                        Human Resources
                                    </option>
                                    <option value="Finance and Accounting" {{ old('department') == 'Finance and Accounting' ? 'selected' : '' }}>
                                        Finance and Accounting
                                    </option>
                               
                                </select>
                            </div>
                        </div>
                    </div>

                </div>





            </div>


        </div>
        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-success px-5">
                <i class="fas fa-exchange-alt me-2"></i>Transfer Employee
            </button>
        </div>
    </form>
</div>

@endsection


@push('scripts')
<script>
    $(document).ready(function() {
        $('#transfer_type').on('change', function() {
            var transferType = $(this).val();

            if (transferType === 'Mutasi' || transferType === 'Demosi' || transferType === 'Promosi') {
                $('#transfer-fields').slideDown();
            } else {
                $('#transfer-fields').slideUp();
                $('#new_position, #new_department').val('')
            }
        });
    });
</script>
@endpush