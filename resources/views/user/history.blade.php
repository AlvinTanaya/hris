@extends('layouts.app')

@section('content')
<style>
    #historyTabs .nav-link {
        color: white;
        font-weight: 500;
        padding: 1rem;
        transition: all 0.3s ease;
    }

    #historyTabs .nav-link.active {
        color: #0d6efd;
        border-bottom: 3px solid #0d6efd;
    }

    #historyTabs .nav-item {
        flex: 1;
        text-align: center;
        max-width: 33.3333%;
    }
</style>
<a href="{{ route('user.index') }}" class="btn btn-danger px-5">
    <i class="fas fa-arrow-left me-2"></i>Back
</a>


<div class="container mt-4 mx-auto">

    <h1 class="text-center text-warning" style="margin-bottom: 65px; margin-top:25px">
        <i class="fas fa-history me-2"></i> Employee History
    </h1>
    <div class="row mb-5 align-items-center">
        <div class="col-md-1 d-flex justify-content-center">
            <img src="{{ $user->photo_profile_path ? asset('storage/'. $user->photo_profile_path) : asset('storage/default_profile.png') }}"
                alt="Profile Picture"
                style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%; border: 2px solid #FFCC00;">
        </div>
        <div class="col-md-11">
            <div class="row">
                <div class="col-md-3">
                    <h3 class="text-white">Employee ID</h3>
                    <h3 class="text-white">Employee Name</h3>
                </div>
                <div class="col-md-9">
                    <h3 class="text-white">: {{ $user->employee_id }}</h3>
                    <h3 class="text-white">: {{ $user->name }}</h3>
                </div>
            </div>
        </div>
    </div>


    <!-- Nav Tabs -->
    <ul class="nav nav-tabs" id="historyTabs">
        <li class="nav-item">
            <a class="nav-link active" id="transfer-tab" data-bs-toggle="tab" href="#transfer">Transfer</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="extend-tab" data-bs-toggle="tab" href="#extend">Extend</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="payment-tab" data-bs-toggle="tab" href="#payment">Payment</a>
        </li>
    </ul>




    <div class="tab-content mt-3">
        <!-- Transfer Tab -->
        <div class="tab-pane fade show active" id="transfer">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive" style="padding-right: 1%;">
                        <table id="historyTable" class="table table-bordered table-striped mb-3 pt-3 align-middle align-items-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Old Position</th>
                                    <th>New Position</th>
                                    <th>Old Department</th>
                                    <th>New Department</th>
                                    <th>Transfer Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($historyTransfers as $index => $history)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $history->old_position }}</td>
                                    <td>{{ $history->new_position }}</td>
                                    <td>{{ $history->old_department }}</td>
                                    <td>{{ $history->new_department }}</td>
                                    <td>{{ $history->created_at }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Extend Tab -->
        <div class="tab-pane fade" id="extend">
            <div class="card shadow-sm">
                <div class="card-body">


                    <div class="table-responsive" style="padding-right: 1%;">
                        <table id="extendTable" class="table table-bordered table-striped mb-3 pt-3 align-middle align-items-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Position</th>
                                    <th>Department/th>
                                    <th>Old Contract End</th>
                                    <th>New Contract End</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($historyExtend as $index => $history)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $history->position }}</td>
                                    <td>{{ $history->department }}</td>
                                    <td>{{ $history->start_date }}</td>
                                    <td>{{ $history->end_date }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Tab (Masih kosong) -->
        <div class="tab-pane fade" id="payment">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1>Payment data is not yet available.</h1>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#historyTable').DataTable();
        $('#extendTable').DataTable();
    });
</script>
@endpush