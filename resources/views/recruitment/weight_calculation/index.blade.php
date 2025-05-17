@extends('layouts.app')

@section('content')



<h1 class="page-title text-warning mb-5">
    <i class="fa-solid fa-weight-scale"></i> Weight Based Recommendation System
</h1>
<div class="container mb-4 p-0 mx-auto" id="container-body">
    <!-- Criteria Percentage Form -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5>Criteria Weights</h5>
            <small>Assign percentage weights to each criterion (total must be 100%)</small>
        </div>
        <div class="card-body pt-3">
            <form id="weightForm" onsubmit="return false;">
                @csrf
                <div>
                    <label for="demandId" class="mb-3">Select Demand:</label>
                    <select name="demandId" id="demandId" class="form-select" required>
                        <option value="" disabled selected>-- Select Demand --</option>
                        @foreach($demands as $demand)

                        <option value="{{ $demand->id }}">{{ $demand->recruitment_demand_id }} - {{ $demand->positionRelation->position }} ({{ $demand->departmentRelation->department }})</option>
                        @endforeach
                    </select>
                </div>

                <div id="suggestionContainer" style="display: none;"></div>

                <!-- Criteria selector (initially hidden) -->
                <div id="criteriaSelector" class="criteria-selector" style="display: none;">
                    <h5 class="mb-3">Select Criteria to Include</h5>
                    <div class="row">
                        <div class="col-md-10 offset-md-1">
                            <div class="input-group">
                                <select id="availableCriteria" class="form-select" style="height: 50px;">
                                    <option value="" disabled selected>-- Select Criteria --</option>
                                    @foreach(array_merge($criteria) as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button type="button" id="addCriteriaBtn" class="btn btn-primary" style="height: 50px; padding-top: 13px;">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Criteria container (initially hidden) -->
                <div class="criteria-container" id="criteriaContainer" style="display: none;"></div>

                <div class="percentage-total" id="percentageTotal" style="display: none;">
                    <strong>Total: <span id="totalValue">0.0</span>%</strong>
                </div>

                <div style="text-align: center">
                    <button type="button" id="calculateBtn" class="calculate-btn" style="display: none;">Calculate Ranking</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Ranking Results -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Applicant Ranking Results</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="rankingTable" class="table table-striped table-hover" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>Rank</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Expected Salary</th>
                            <th>Distance</th>
                            <th>Education</th>
                            <th>Experience</th>
                            <th>Organization</th>
                            <th>Training</th>
                            <th>Total Score</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="rankingResults">
                        <tr>
                            <td colspan="12" class="text-center">Please select a demand and add criteria to see results</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Age Range Config Modal -->
<div class="modal fade" id="ageConfigModal" tabindex="-1" aria-labelledby="ageConfigModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title" id="ageConfigModalLabel">
                    <i class="fas fa-sliders-h me-2"></i>Configure Age Ranges
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-4">Define age ranges and their corresponding scores. Higher ranks will receive a higher score.</p>

                <div id="ageRangesList" class="mb-4">
                    <!-- Age ranges will be added here dynamically -->
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="button" id="addAgeRangeBtn" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add Age Range
                        </button>
                    </div>
                </div>

                <div class="alert alert-info mt-4 d-flex align-items-center border-0 shadow-sm">
                    <i class="fas fa-info-circle me-3 fa-lg text-primary"></i>
                    <div>
                        <strong>Note:</strong> Age ranges should not overlap. The system will automatically assign a score based on rank (1st rank = highest score).
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary pt-2 px-4 #availableCriteriapx-4" id="saveAgeConfig">
                    Save Configuration
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Education Config Modal -->
<div class="modal fade" id="educationConfigModal" tabindex="-1" aria-labelledby="educationConfigModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title" id="educationConfigModalLabel">
                    <i class="fas fa-graduation-cap me-2"></i>Configure Education Levels
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-4">Define education level ranking and their corresponding scores. Higher ranks will receive a higher score.</p>


                <div class="alert alert-info mt-4 d-flex align-items-center border-0 shadow-sm">
                    <i class="fas fa-info-circle me-3 fa-lg text-primary"></i>
                    <div>
                        <strong>Note:</strong> Please define the percentage of importance between Degree and Grade.
                        However, you can only adjust the Degree percentage, while the Grade percentage will automatically be adjusted to ensure a total of 100%.
                        <br><strong>Tip:</strong> It is recommended not to set it as 50/50 for a more meaningful weighting.
                    </div>
                </div>

                <!-- Weight Distribution Section -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Weight Distribution</h6>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="educationLevelWeight">Education Level Degree Weight: (%)</label>

                                    <input type="number" id="educationLevelWeight" class="form-control" value="80" min="0" max="100">

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="educationGradeWeight">Education Grade Weight: (%)</label>

                                    <input type="number" id="educationGradeWeight" class="form-control" value="20" min="0" max="100" readonly>

                                </div>
                            </div>
                        </div>
                        <div class="percentage-total mt-3" id="educationWeightTotal">
                            <strong>Total: <span id="educationWeightTotalValue">100.0</span>%</strong>
                        </div>
                    </div>
                </div>


                <div class="alert alert-info mt-4 mb-4 d-flex align-items-center border-0 shadow-sm">
                    <i class="fas fa-info-circle me-3 fa-lg text-primary"></i>
                    <div>
                        <strong>Note:</strong> Drag and drop to rearrange education levels. The system will automatically assign a score based on rank (1st rank = highest score).

                    </div>
                </div>



                <!-- Configure Ranking Level / Degree Section -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Configure Ranking Level Degree</h6>
                    </div>
                    <div class="card-body">
                        <div id="educationLevelsList">
                            <!-- Education levels will be added here dynamically -->
                        </div>
                    </div>
                </div>






            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary pt-2 px-4" id="saveEducationConfig">
                    Save Configuration
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Distance Configuration Modal -->
<div class="modal fade" id="distanceConfigModal" tabindex="-1" aria-labelledby="distanceConfigModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title" id="distanceConfigModalLabel">
                    <i class="fas fa-map-marker-alt me-2"></i>Configure Distance Scoring
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row mb-3">
                        <div class="col-12">
                            <p class="text-muted">Configure how distance is scored. Rank 1 is the most preferred (shortest distance), with higher ranks being less preferred.</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div id="distanceRangesList" class="mb-3">
                                <!-- Distance range rows will be added here dynamically -->
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-success" id="addDistanceRangeBtn">
                                <i class="bi bi-plus"></i> Add Distance Range
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary pt-2 px-4" id="saveDistanceConfig">Save Configuration</button>
            </div>
        </div>
    </div>
</div>

<!-- Expected Salary Configuration Modal -->
<div class="modal fade" id="expectedSalaryConfigModal" tabindex="-1" aria-labelledby="expectedSalaryConfigModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title" id="expectedSalaryConfigModalLabel">
                    <i class="fas fa-money-bill-wave me-2"></i>Configure Expected Salary Scoring
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row mb-3">
                        <div class="col-12">
                            <p class="text-muted">Configure how expected salary is scored. Rank 1 is the most preferred salary range, with higher ranks being less preferred.</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div id="salaryRangesList" class="mb-3">
                                <!-- Salary range rows will be added here dynamically -->
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-success" id="addSalaryRangeBtn">
                                <i class="bi bi-plus"></i> Add Salary Range
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary pt-2 px-4" id="saveExpectedSalaryConfig">Save Configuration</button>
            </div>
        </div>
    </div>
</div>

<!-- Training Configuration Modal -->
<div class="modal fade" id="trainingConfigModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Configure Training Scoring
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <div class="mb-3 fw-bold">Period and Amount Weights</div>
                    <div class="alert alert-info mt-4 d-flex align-items-center border-0 shadow-sm">
                        <i class="fas fa-info-circle me-3 fa-lg text-primary"></i>
                        <div>
                            <strong>Note:</strong> Please define the percentage of importance between Period and Amount.
                            However, you can only adjust the Period percentage, while the Amount percentage will automatically be adjusted to ensure a total of 100%.
                            <br><strong>Tip:</strong> It is recommended not to set it as 50/50 for a more meaningful weighting.
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label">Period Weight</label>
                                <input type="number" class="form-control" id="trainingPeriodWeight" min="0" max="100" value="70">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label">Amount Weight</label>
                                <input type="number" class="form-control" id="trainingAmountWeight" min="0" max="100" value="30" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div id="trainingWeightTotal" class="text-end mt-4">
                                <span id="trainingWeightTotalValue">100.0</span>%
                            </div>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-tabs mb-3" id="trainingTabLinks" role="tablist">
                    <li class="nav-item w-50" role="presentation">
                        <button class="nav-link active w-100" id="training-period-tab" data-bs-toggle="tab" data-bs-target="#training-period-content" type="button" role="tab" aria-controls="training-period-content" aria-selected="true">Period Scoring</button>
                    </li>
                    <li class="nav-item w-50" role="presentation">
                        <button class="nav-link w-100" id="training-amount-tab" data-bs-toggle="tab" data-bs-target="#training-amount-content" type="button" role="tab" aria-controls="training-amount-content" aria-selected="false">Amount Scoring</button>
                    </li>
                </ul>

                <div class="tab-content" id="trainingTabContent">
                    <!-- Training Period Tab -->
                    <div class="tab-pane fade show active" id="training-period-content" role="tabpanel" aria-labelledby="training-period-tab">
                        <div class="d-flex justify-content-start mb-3">
                            <h5>Training Period Rankings</h5>
                        </div>
                        <div id="trainingPeriodList">
                            <!-- Training period ranges will be added here -->
                        </div>
                        <div class="d-flex justify-content-start mt-3">
                            <button type="button" class="btn btn-success" id="addTrainingPeriodBtn">
                                <i class="bi bi-plus"></i> Add Range
                            </button>
                        </div>
                    </div>

                    <!-- Training Amount Tab -->
                    <div class="tab-pane fade" id="training-amount-content" role="tabpanel" aria-labelledby="training-amount-tab">
                        <div class="d-flex justify-content-start mb-3">
                            <h5>Training Amount Rankings</h5>
                        </div>
                        <div id="trainingAmountList">
                            <!-- Training amount ranges will be added here -->
                        </div>
                        <div class="d-flex justify-content-start mt-3">
                            <button type="button" class="btn btn-success" id="addTrainingAmountBtn">
                                <i class="bi bi-plus"></i> Add Range
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary pt-2 px-4" id="saveTrainingConfig">Save Configuration</button>
            </div>
        </div>
    </div>
</div>

<!-- Experience Duration Configuration Modal -->
<div class="modal fade" id="experienceConfigModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title">
                    <i class="fas fa-briefcase me-2"></i>Configure Experience Scoring
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <div class="mb-3 fw-bold">Period and Amount Weights</div>
                    <div class="alert alert-info mt-4 d-flex align-items-center border-0 shadow-sm">
                        <i class="fas fa-info-circle me-3 fa-lg text-primary"></i>
                        <div>
                            <strong>Note:</strong> Please define the percentage of importance between Period and Amount.
                            However, you can only adjust the Period percentage, while the Amount percentage will automatically be adjusted to ensure a total of 100%.
                            <br><strong>Tip:</strong> It is recommended not to set it as 50/50 for a more meaningful weighting.
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label">Period Weight</label>
                                <input type="number" class="form-control" id="experiencePeriodWeight" min="0" max="100" value="70">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label">Amount Weight</label>
                                <input type="number" class="form-control" id="experienceAmountWeight" min="0" max="100" value="30" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div id="experienceWeightTotal" class="text-end mt-4">
                                <span id="experienceWeightTotalValue">100.0</span>%
                            </div>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-tabs mb-3" id="experienceTabLinks" role="tablist">
                    <li class="nav-item w-50" role="presentation">
                        <button class="nav-link active w-100" id="experience-period-tab" data-bs-toggle="tab" data-bs-target="#experience-period-content" type="button" role="tab" aria-controls="experience-period-content" aria-selected="true">Period Scoring</button>
                    </li>
                    <li class="nav-item w-50" role="presentation">
                        <button class="nav-link w-100" id="experience-amount-tab" data-bs-toggle="tab" data-bs-target="#experience-amount-content" type="button" role="tab" aria-controls="experience-amount-content" aria-selected="false">Amount Scoring</button>
                    </li>
                </ul>

                <div class="tab-content" id="experienceTabContent">
                    <!-- Experience Period Tab -->
                    <div class="tab-pane fade show active" id="experience-period-content" role="tabpanel" aria-labelledby="experience-period-tab">
                        <div class="d-flex justify-content-start mb-3">
                            <h5>Experience Period Rankings</h5>
                        </div>
                        <div id="experiencePeriodList">
                            <!-- Experience period ranges will be added here -->
                        </div>
                        <div class="d-flex justify-content-start mt-3">
                            <button type="button" class="btn btn-success" id="addExperiencePeriodBtn">
                                <i class="bi bi-plus"></i> Add Range
                            </button>
                        </div>
                    </div>

                    <!-- Experience Amount Tab -->
                    <div class="tab-pane fade" id="experience-amount-content" role="tabpanel" aria-labelledby="experience-amount-tab">
                        <div class="d-flex justify-content-start mb-3">
                            <h5>Experience Amount Rankings</h5>
                        </div>
                        <div id="experienceAmountList">
                            <!-- Experience amount ranges will be added here -->
                        </div>
                        <div class="d-flex justify-content-start mt-3">
                            <button type="button" class="btn btn-success" id="addExperienceAmountBtn">
                                <i class="bi bi-plus"></i> Add Range
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary pt-2 px-4" id="saveExperienceConfig">Save Configuration</button>
            </div>
        </div>
    </div>
</div>

<!-- Organization Configuration Modal -->
<div class="modal fade" id="organizationConfigModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title">
                    <i class="fas fa-building me-2"></i>Configure Organization Scoring
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <div class="mb-3 fw-bold">Period and Amount Weights</div>
                    <div class="alert alert-info mt-4 d-flex align-items-center border-0 shadow-sm">
                        <i class="fas fa-info-circle me-3 fa-lg text-primary"></i>
                        <div>
                            <strong>Note:</strong> Please define the percentage of importance between Period and Amount.
                            However, you can only adjust the Period percentage, while the Amount percentage will automatically be adjusted to ensure a total of 100%.
                            <br><strong>Tip:</strong> It is recommended not to set it as 50/50 for a more meaningful weighting.
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label">Period Weight</label>
                                <input type="number" class="form-control" id="organizationPeriodWeight" min="0" max="100" value="70">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label">Amount Weight</label>
                                <input type="number" class="form-control" id="organizationAmountWeight" min="0" max="100" value="30" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div id="organizationWeightTotal" class="text-end mt-4">
                                <span id="organizationWeightTotalValue">100.0</span>%
                            </div>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-tabs mb-3" id="organizationTabLinks" role="tablist">
                    <li class="nav-item w-50" role="presentation">
                        <button class="nav-link active w-100" id="organization-period-tab" data-bs-toggle="tab" data-bs-target="#organization-period-content" type="button" role="tab" aria-controls="organization-period-content" aria-selected="true">Period Scoring</button>
                    </li>
                    <li class="nav-item w-50" role="presentation">
                        <button class="nav-link w-100" id="organization-amount-tab" data-bs-toggle="tab" data-bs-target="#organization-amount-content" type="button" role="tab" aria-controls="organization-amount-content" aria-selected="false">Amount Scoring</button>
                    </li>
                </ul>

                <div class="tab-content" id="organizationTabContent">
                    <!-- Organization Period Tab -->
                    <div class="tab-pane fade show active" id="organization-period-content" role="tabpanel" aria-labelledby="organization-period-tab">
                        <div class="d-flex justify-content-start mb-3">
                            <h5>Organization Period Rankings</h5>
                        </div>
                        <div id="organizationPeriodList">
                            <!-- Organization period ranges will be added here -->
                        </div>
                        <div class="d-flex justify-content-start mt-3">
                            <button type="button" class="btn btn-success" id="addOrganizationPeriodBtn">
                                <i class="bi bi-plus"></i> Add Range
                            </button>
                        </div>
                    </div>

                    <!-- Organization Amount Tab -->
                    <div class="tab-pane fade" id="organization-amount-content" role="tabpanel" aria-labelledby="organization-amount-tab">
                        <div class="d-flex justify-content-start mb-3">
                            <h5>Organization Amount Rankings</h5>
                        </div>
                        <div id="organizationAmountList">
                            <!-- Organization amount ranges will be added here -->
                        </div>
                        <div class="d-flex justify-content-start mt-3">
                            <button type="button" class="btn btn-success" id="addOrganizationAmountBtn">
                                <i class="bi bi-plus"></i> Add Range
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary pt-2 px-4" id="saveOrganizationConfig">Save Configuration</button>
            </div>
        </div>
    </div>
</div>

<!-- Add this modal to your HTML file outside of any functions -->
<div class="modal fade" id="scheduleInterviewModal" tabindex="-1" aria-labelledby="scheduleInterviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleInterviewModalLabel">Schedule Interview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="scheduleInterviewForm">
                    <input type="hidden" id="applicantId" name="applicantId">

                    <div class="mb-3">
                        <label for="applicantName" class="form-label">Applicant Name</label>
                        <input type="text" class="form-control" id="applicantName" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="interviewDate" class="form-label">Interview Date and Time</label>
                        <input type="datetime-local" class="form-control" id="interviewDate" name="interview_date" required>
                    </div>

                    <div class="mb-3">
                        <label for="interviewNote" class="form-label">Interview Notes</label>
                        <textarea class="form-control" id="interviewNote" name="interview_note" rows="3" required></textarea>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="confirmSchedule" required>
                        <label class="form-check-label" for="confirmSchedule">Confirm interview schedule</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary pt-2 px-4" id="saveInterviewBtn">Schedule</button>
            </div>
        </div>
    </div>
</div>


<!-- Tambahkan modal view pelamar di bagian bawah halaman -->
<div class="modal fade" id="applicantRankingModal" tabindex="-1" aria-labelledby="applicantRankingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="applicantRankingModalLabel">
                    <i class="fas fa-user me-2"></i>Applicant Detail
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Isi modal akan diisi secara dinamis melalui JavaScript -->
                <div id="applicantModalContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection




<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
    /* Base styles */
    #container-body {
        margin: 0;
        padding: 20px;
        min-height: 100vh;
    }

    .error {
        color: red;
        font-weight: bold;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .page-title {
        text-align: center;
        margin: 2rem 0;
        font-size: 2.5rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Card styles */
    .card {
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card-header {
        color: #0d6efd;
        padding: 1.5rem;
    }

    .card-header h5 {
        margin: 0;
        font-size: 1.25rem;
    }

    .card-header small {
        opacity: 0.8;
        display: block;
        margin-top: 0.5rem;
    }

    .card-body {
        padding: 2rem;
    }

    /* Form elements */
    .form-select,
    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        margin-bottom: 1.5rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    .form-control-static {
        padding: 6px 12px;
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }

    /* Criteria components */
    .criteria-container {
        margin: 35px 0 1.5rem;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: center;
    }

    .criteria-box,
    .criteria-selector {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
        position: relative;
    }

    .criteria-box {
        min-width: 150px;
    }

    .criteria-selector {
        max-width: 100%;
    }

    .criteria-label {
        margin-top: 34px;
        font-size: 14px;
        font-weight: bold;
        color: #2c3e50;
    }

    .criteria-percentage {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: bold;
        color: #3498db;
    }

    .percentage-input {
        width: 60px;
        padding: 5px;
        border: 2px solid #ddd;
        border-radius: 8px;
        text-align: center;
        font-weight: bold;
        color: #2c3e50;
    }

    .percentage-total {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 15px;
        color: #3498db;
        padding: 1rem;
    }

    .percentage-total.error {
        color: #e74c3c;
    }

    #experienceWeightTotal,
    #organizationWeightTotal,
    #trainingWeightTotal {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 15px;
        color: #3498db;
        padding: 1rem;
    }


    .criteria-actions {
        position: absolute;
        top: 5px;
        right: 5px;
        display: flex;
        gap: 5px;
    }

    .remove-criteria,
    .configure-criteria {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .remove-criteria {
        color: #e74c3c;
    }

    .configure-criteria {
        color: #3498db;
    }

    .config-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 15px;
        height: 15px;
        background-color: #e74c3c;
        border-radius: 50%;
        border: 2px solid white;
        display: none;
    }

    .config-badge.active {
        display: block;
    }

    /* Buttons */
    .btn-primary,
    .btn-success,
    .calculate-btn,
    .add-criteria-btn,
    .age-range-delete {
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary,
    .btn-success {
        color: white;
        border: none;
        padding: 1rem 2rem;
        font-size: 1.1rem;
    }

    .calculate-btn {
        display: block;
        width: 200px;
        margin: 0 auto;
        background-color: #3498db;
        color: white;
        padding: 10px;
        border: none;
        font-size: 16px;
    }

    .add-criteria-btn {
        background-color: #2ecc71;
        color: white;
        border: none;
        padding: 10px 15px;
        margin: 10px auto;
        display: block;
    }

    .btn-primary {
        background: #3498db;
    }

    .btn-success {
        background: #2ecc71;
        margin-top: 1rem;
    }

    .btn-primary:hover {
        background: #2c3e50;
        transform: scale(1.05);
    }

    .btn-success:hover,
    .calculate-btn:hover {
        background: #27ae60;
        transform: scale(1.05);
    }

    .calculate-btn:hover {
        border: 2px solid #ccc;
        background-color: #2ecc71;
        color: white;
    }

    .modal-footer .btn {
        min-width: 150px;
        height: 38px;
        font-size: 14px;
    }

    /* Table styles */
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .table th {
        background: #2c3e50;
        color: white;
        padding: 1rem;
        text-align: left;
    }

    .table td {
        padding: 1rem;
        border-bottom: 1px solid #ddd;
    }

    .table tbody tr:hover {
        background: #ecf0f1;
    }

    /* Range rows (age, education, distance, salary) */
    .age-range-row,
    .education-level-row,
    .distance-range-row,
    .salary-range-row {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        padding: 15px;
        border-radius: 8px;
        background-color: #f8f9fa;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        position: relative;
    }

    .age-range-row:hover,
    .education-level-row:hover,
    .distance-range-row:hover,
    .salary-range-row:hover {
        background-color: #f0f4f7;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .age-range-handle,
    .education-level-handle,
    .distance-range-handle,
    .salary-range-handle {
        cursor: move;
        color: #6c757d;
        font-size: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        transition: color 0.3s ease;

    }

    .age-range-handle:hover,
    .education-level-handle:hover,
    .distance-range-handle:hover,
    .salary-range-handle:hover {
        color: #3498db;
    }

    .age-range-delete {
        color: #e74c3c;
        background: none;
        border: none;
        margin-top: 25px;
        font-size: 16px;
        padding: 8px 12px;
    }

    .age-range-delete i {
        font-size: 18px;
    }

    /* Sortable styles */
    .sortable-ghost {
        opacity: 0.4;
        background-color: #f0f8ff !important;
        border: 2px dashed #3498db !important;
    }

    .sortable-drag {
        opacity: 0.8;
        background-color: #ffffff !important;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2) !important;
        transform: scale(1.05);
        z-index: 1000;
    }

    .sortable-chosen {
        background-color: #e9f7fe !important;
        border-left: 4px solid #3498db;
    }

    #suggestionContainer .alert-info {
        background-color: #f8f9fa;
        border-left: 4px solid #17a2b8;
        border-radius: 0;
    }

    #suggestionContainer {
        margin-top: 15px;
        transition: all 0.3s ease;
    }

    /* Animations */
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes highlight-effect {
        0% {
            background-color: #fff9c4;
            transform: translateY(0);
        }

        50% {
            background-color: #ffecb3;
        }

        100% {
            background-color: #f8f9fa;
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes highlight {
        0% {
            background-color: #f8f9fa;
        }

        50% {
            background-color: #d4edda;
        }

        100% {
            background-color: #f8f9fa;
        }
    }

    .highlight-row {
        animation: highlight-effect 1s ease;
    }

    .age-range-row-new,
    .education-level-row-new {
        animation: fadeIn 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
    }

    .distance-range-row-new,
    .salary-range-row-new {
        animation: fadeInDown 0.5s;
    }

    .spinner-border {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 3px solid white;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }


    /* Tambahkan di bagian CSS Anda */
    #applicantRankingModal .modal-xl {
        max-width: 1200px;
    }

    #applicantRankingModal .card {
        margin-bottom: 1rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    #applicantRankingModal .nav-tabs .nav-link {
        color: #495057;
        border: none;
        padding: 0.75rem 1.25rem;
    }

    #applicantRankingModal .nav-tabs .nav-link.active {
        color: #fff;
        background-color: #4361ee;
        border-radius: 0;
    }

    #applicantRankingModal .list-group-item {
        border-left: none;
        border-right: none;
        border-radius: 0;
    }

    #applicantRankingModal .list-group-item:first-child {
        border-top: none;
    }

    #applicantRankingModal .list-group-item:last-child {
        border-bottom: none;
    }

    #applicantRankingModal .badge {
        font-size: 0.9em;
        padding: 0.35em 0.65em;
    }


    #rankingTable_wrapper .row {
        margin-bottom: 15px;
    }

    #rankingTable thead th {
        font-weight: 600;
        white-space: nowrap;
    }

    #rankingTable tbody tr:hover {
        background-color: rgba(67, 97, 238, 0.05);
    }

    #rankingTable .btn-group .btn {
        margin-right: 5px;
    }

    #rankingTable .progress {
        height: 8px;
        margin-top: 3px;
    }

    #rankingTable .score-cell {
        min-width: 80px;
    }

    #rankingTable .actions-cell {
        min-width: 200px;
    }

    .dataTables_info,
    .dataTables_paginate {
        margin-top: 15px;
    }

    .dataTables_filter input {
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
        padding: 0.375rem 0.75rem;
    }

    .dataTables_length select {
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
        padding: 0.375rem 0.5rem;
    }
</style>

@push('scripts')
<!-- Core Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    function initializeDataTable() {
        // Destroy existing DataTable instance if it exists
        if ($.fn.DataTable.isDataTable('#rankingTable')) {
            $('#rankingTable').DataTable().destroy();
        }

        // Initialize DataTable with options
        $('#rankingTable').DataTable({
            responsive: true,
            language: {
                search: "<i class='fas fa-search'></i>",
                searchPlaceholder: "Search applicants...",
                emptyTable: "No applicant data available",
                zeroRecords: "No matching applicants found",
                info: "Showing _START_ to _END_ of _TOTAL_ applicants",
                infoEmpty: "Showing 0 to 0 of 0 applicants",
                infoFiltered: "(filtered from _MAX_ total applicants)",
                paginate: {
                    first: '<i class="fas fa-angle-double-left"></i>',
                    previous: '<i class="fas fa-angle-left"></i>',
                    next: '<i class="fas fa-angle-right"></i>',
                    last: '<i class="fas fa-angle-double-right"></i>'
                }
            },
            columnDefs: [{
                    orderable: false,
                    targets: -1
                } // Disable sorting for action buttons column
            ],
            order: [
                [0, 'asc']
            ], // Default sort by rank ascending
            dom: '<"row align-items-center"<"col-md-6"l><"col-md-6"f>>rtip',
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ]
        });
    }


    $(document).ready(function() {
        // Cache frequently used elements
        const $form = $('#weightForm');
        const $calculateBtn = $('#calculateBtn');
        const $rankingResults = $('#rankingResults');
        const $totalValueSpan = $('#totalValue');
        const $percentageTotalDiv = $('#percentageTotal');
        const $criteriaContainer = $('#criteriaContainer');
        const $suggestionContainer = $('#suggestionContainer');
        const $criteriaSelector = $('#criteriaSelector');
        const $demandSelect = $('#demandId');
        const $availableCriteria = $('#availableCriteria');
        const $addCriteriaBtn = $('#addCriteriaBtn');
        const $ageConfigModal = $('#ageConfigModal');
        const $addAgeRangeBtn = $('#addAgeRangeBtn');
        const $ageRangesList = $('#ageRangesList');
        const $saveAgeConfig = $('#saveAgeConfig');


        $(document).on('input', '.criteria-percentage input', updateTotal);

        // Define all criteria
        const allCriteria = {
            'age': 'Age',
            'experience_duration': 'Experience',
            'education': 'Education',
            'training': 'Training',
            'organization': 'Organization',
            'distance': 'Distance',
            'expected_salary': 'Expected Salary'
        };

        // Store custom configurations for criteria
        const criteriaConfigs = {
            age: {
                ranges: [{
                        min: 21,
                        max: 25,
                        rank: 1
                    },
                    {
                        min: 15,
                        max: 20,
                        rank: 2
                    },
                    {
                        min: 26,
                        max: 32,
                        rank: 3
                    },

                ],
                customized: false
            },

            education: { // Memasukkan levels dan weights ke dalam education
                levels: {
                    list: [{
                            name: "S2",
                            rank: 1
                        },
                        {
                            name: "S1",
                            rank: 2
                        },
                        {
                            name: "D3",
                            rank: 3
                        },
                        {
                            name: "SMK",
                            rank: 4
                        },
                        {
                            name: "SMA",
                            rank: 5
                        },
                    ],
                    customized: false
                },
                weights: {
                    level: 70,
                    grade: 30
                }
            },



            distance: {
                ranges: [{
                        min: 0,
                        max: 3,
                        rank: 1
                    },
                    {
                        min: 3.1,
                        max: 5,
                        rank: 2
                    },
                    {
                        min: 5.1,
                        max: 8,
                        rank: 3
                    },

                ],
                customized: false
            },

            expected_salary: {
                ranges: [{
                        min: 5000001,
                        max: 8000000,
                        rank: 1
                    },
                    {
                        min: 0,
                        max: 5000000,
                        rank: 2
                    },
                    {
                        min: 8000001,
                        max: 10000000,
                        rank: 3
                    },

                ],
                customized: false
            },

            training: {
                period: [{
                        min: 5.1,
                        max: 8,
                        rank: 1
                    },
                    {
                        min: 3.1,
                        max: 5,
                        rank: 2
                    },
                    {
                        min: 1,
                        max: 3,
                        rank: 3
                    },
                    {
                        min: 0.1,
                        max: 1,
                        rank: 4
                    },
                ],
                amount: [{
                        min: 6,
                        max: 7,
                        rank: 1
                    },
                    {
                        min: 4,
                        max: 5,
                        rank: 2
                    },
                    {
                        min: 2,
                        max: 3,
                        rank: 3
                    },
                    {
                        min: 0,
                        max: 1,
                        rank: 4
                    },
                ],
                weights: {
                    period: 70,
                    amount: 30
                },
                customized: false
            },

            organization: {
                period: [{
                        min: 5.1,
                        max: 8,
                        rank: 1
                    },
                    {
                        min: 3.1,
                        max: 5,
                        rank: 2
                    },
                    {
                        min: 1.1,
                        max: 3,
                        rank: 3
                    },
                    {
                        min: 0,
                        max: 1,
                        rank: 4
                    },

                ],
                amount: [{
                        min: 6,
                        max: 7,
                        rank: 1
                    },
                    {
                        min: 4,
                        max: 5,
                        rank: 2
                    },
                    {
                        min: 2,
                        max: 3,
                        rank: 3
                    },
                    {
                        min: 0,
                        max: 1,
                        rank: 4
                    },
                ],
                weights: {
                    period: 70,
                    amount: 30
                },
                customized: false
            },

            experience_duration: { // Memasukkan levels dan weights ke dalam education
                period: [{
                        min: 5.1,
                        max: 8,
                        rank: 1
                    },
                    {
                        min: 3.1,
                        max: 5,
                        rank: 2
                    },
                    {
                        min: 1.1,
                        max: 3,
                        rank: 3
                    },
                    {
                        min: 0,
                        max: 1,
                        rank: 4
                    },

                ],
                amount: [{
                        min: 6,
                        max: 7,
                        rank: 1
                    },
                    {
                        min: 4,
                        max: 5,
                        rank: 2
                    },
                    {
                        min: 2,
                        max: 3,
                        rank: 3
                    },
                    {
                        min: 0,
                        max: 1,
                        rank: 4
                    },
                ],
                weights: {
                    period: 70,
                    amount: 30
                },
                customized: false
            },

        };

        // Track which criteria are currently added
        let activeCriteria = [];

        // Show criteria selector when a demand is selected
        $demandSelect.on('change', function() {
            if ($(this).val()) {
                $criteriaSelector.slideDown();
                $suggestionContainer.html(`
                        <div class="alert alert-info mt-3">
                            <h5><i class="fas fa-lightbulb"></i> Suggestions for Better Calculation:</h5>
                            <ol class="mb-0">
                                <li><strong>Add More Criteria:</strong> We recommend at least 3 criteria for more accurate results.</li>
                                <li><strong>Vary the Weights:</strong> Avoid equal percentages</li>
                                <li><strong>Customize Criteria:</strong> Configure each criterion to match your specific needs.</li>
                            </ol>
                        </div>
                    `).slideDown();
                resetCriteria();
            } else {
                $criteriaSelector.slideUp();
                $criteriaContainer.slideUp();
                $percentageTotalDiv.hide();
                $calculateBtn.hide();
                $suggestionContainer.slideUp();
            }
        });

        // Add a criterion to the form
        $addCriteriaBtn.on('click', function() {
            const criterionKey = $availableCriteria.val();
            const criterionLabel = allCriteria[criterionKey];

            if (!criterionKey || activeCriteria.includes(criterionKey)) return;

            activeCriteria.push(criterionKey);

            const criteriaBox = $(`
        <div class="criteria-box" data-key="${criterionKey}">
            <div class="criteria-actions">
                <button type="button" class="configure-criteria" title="Configure criteria" data-key="${criterionKey}" style="font-size: 16px; padding: 5px; background: #f0f0f0; border-radius: 4px;">
                    <i class="fas fa-cog"></i> Configure
                </button>
                <button type="button" class="remove-criteria" title="Remove criteria">
                    <i class="fas fa-times"></i>
                </button>
            </div>
           
            <div class="criteria-label">${criterionLabel}</div>
            <div class="criteria-percentage mt-2">
                <input type="number" name="${criterionKey}" 
                       class="percentage-input criteria-percentage"
                       min="0" max="100" value="0"
                       required style="width: 80px; height: 45px; font-size: 20px;">
                <span>%</span>
            </div>
        </div>
    `);

            $criteriaContainer.append(criteriaBox);

            // If this is the first criterion, show the container and buttons
            if (activeCriteria.length === 1) {
                $criteriaContainer.slideDown();
                $percentageTotalDiv.show();
                $calculateBtn.show();

            }



            // Hide suggestions when they reach 5 criteria
            if (activeCriteria.length >= 5) {
                $suggestionContainer.slideUp();
            }

            // Remove the added criterion from the dropdown
            $availableCriteria.find(`option[value="${criterionKey}"]`).prop('disabled', true);
            $availableCriteria.val('');

            distributePercentages();
            updateTotal();

            // If the criterion has customized config, show the badge
            if (criteriaConfigs[criterionKey] && criteriaConfigs[criterionKey].customized) {
                $(`#config-badge-${criterionKey}`).addClass('active');
            }
        });



        // Remove a criterion
        $criteriaContainer.on('click', '.remove-criteria', function() {
            const $box = $(this).closest('.criteria-box');
            const key = $box.data('key');

            // Remove from activeCriteria array
            const index = activeCriteria.indexOf(key);
            if (index > -1) activeCriteria.splice(index, 1);

            // Re-enable in dropdown
            $availableCriteria.find(`option[value="${key}"]`).prop('disabled', false);

            // Remove the box
            $box.remove();

            // If no criteria left, hide containers
            if (activeCriteria.length === 0) {
                $criteriaContainer.slideUp();
                $percentageTotalDiv.hide();
                $calculateBtn.hide();
            } else {
                distributePercentages();
            }

            updateTotal();
        });

        // Configure a criterion
        $criteriaContainer.on('click', '.configure-criteria', function() {
            const key = $(this).data('key');

            if (key === 'age') {
                openAgeConfigModal();
            } else if (key === 'education') {
                openEducationConfigModal();
            } else if (key === 'distance') {
                openDistanceConfigModal();
            } else if (key === 'expected_salary') {
                openExpectedSalaryConfigModal();
            } else if (key === 'training') {
                openTrainingConfigModal();
            } else if (key === 'experience_duration') {
                openExperienceConfigModal();
            } else if (key === 'organization') {
                openOrganizationConfigModal();
            }
            // Add other criteria configuration handlers as needed
        });


        // ----------------------- START CONFIGURATION -----------------------

        // ----------------------- AGE CONFIGURATION -----------------------
        // Open age configuration modal
        function openAgeConfigModal() {

            const ageRanges = criteriaConfigs.age.ranges;

            // Clear the existing list
            $ageRangesList.empty();

            // Add each age range
            ageRanges.forEach((range, index) => {
                addAgeRangeRow(range.min, range.max, range.rank);
            });

            // Initialize drag and drop
            initSortable();

            // Show the modal
            $ageConfigModal.modal('show');
        }



        // Add a new age range row to the modal
        function addAgeRangeRow(min = 15, max = 20, rank = null) {
            const rowCount = $ageRangesList.children().length;
            rank = rank || rowCount + 1;

            const row = $(`
        <div class="age-range-row age-range-row-new" data-rank="${rank}">
            <div class="row w-100" style="margin: 0;">
                <div class="col-md-1" style="flex: 1;">
                    <div class="form-group">
                        <label>Rank</label>
                        <div class="form-control-static rank-display">${rank}</div>
                    </div>
                </div>
                <div class="col-md-4" style="flex: 3;">
                    <div class="form-group">
                        <label>Min Age</label>
                        <input type="number" class="form-control age-min" value="${min}" min="15" max="100">
                    </div>
                </div>
                <div class="col-md-4" style="flex: 3;">
                    <div class="form-group">
                        <label>Max Age</label>
                        <input type="number" class="form-control age-max" value="${max}" min="15" max="100">
                    </div>
                </div>
                <div class="col-md-1 align-items-center d-flex" style="flex: 1; justify-content: center;">
                    <div class="age-range-handle">
                        <i class="fa-solid fa-arrows-up-down fa-lg"></i>
                    </div>
                </div>
                <div class="col-md-1 align-items-center d-flex" style="flex: 1; justify-content: center;">
                    <button type="button" class="btn btn-danger age-range-delete">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    `);

            $ageRangesList.append(row);

            // Add field validation
            row.find('.age-min, .age-max').on('change', validateAgeRanges);

            // Remove the animation class after animation completes
            setTimeout(() => {
                row.removeClass('age-range-row-new');
            }, 600);
        }

        // Add function to check for overlapping ranges
        function validateAgeRanges() {
            const rows = $('.age-range-row');
            let hasError = false;

            // Reset all error states
            rows.removeClass('border-danger');
            $('.validation-error').remove();

            // Check each row
            rows.each(function(i) {
                const minAge = parseInt($(this).find('.age-min').val());
                const maxAge = parseInt($(this).find('.age-max').val());

                // Check min <= max
                if (minAge >= maxAge) {
                    $(this).addClass('border border-danger');
                    $(this).append('<div class="validation-error text-danger mt-2">Min age must be less than max age</div>');
                    hasError = true;
                    return;
                }

                // Check for overlaps with other rows
                rows.each(function(j) {
                    if (i !== j) {
                        const otherMin = parseInt($(this).find('.age-min').val());
                        const otherMax = parseInt($(this).find('.age-max').val());

                        if ((minAge <= otherMax && maxAge >= otherMin)) {
                            rows.eq(i).addClass('border border-danger');
                            if (!rows.eq(i).find('.validation-error').length) {
                                rows.eq(i).append('<div class="validation-error text-danger mt-2">Age ranges cannot overlap</div>');
                            }
                            hasError = true;
                        }
                    }
                });
            });

            // Disable save button if errors exist
            $('#saveAgeConfig').prop('disabled', hasError);

            return !hasError;
        }

        // Initialize sortable functionality for reordering rows
        function initSortable() {
            if (typeof Sortable !== 'undefined') {
                new Sortable(document.getElementById('ageRangesList'), {
                    handle: '.age-range-handle',
                    animation: 400, // Increased from 150 to 400ms
                    easing: "cubic-bezier(0.68, -0.55, 0.265, 1.55)", // Add bouncy easing
                    ghostClass: "sortable-ghost", // Class for the drop placeholder
                    chosenClass: "sortable-chosen", // Class for the chosen item
                    dragClass: "sortable-drag", // Class for the dragging item
                    onEnd: function(evt) {
                        // Update rank numbers after sorting
                        $('.age-range-row').each(function(index) {
                            $(this).attr('data-rank', index + 1);
                            $(this).find('.rank-display').text(index + 1);
                        });

                        // Add highlight effect to the moved row
                        const movedRow = $(evt.item);
                        movedRow.addClass('highlight-row');
                        setTimeout(() => {
                            movedRow.removeClass('highlight-row');
                        }, 1000);
                    }
                });
            }
        }



        // Update ranks after sorting
        function updateRanks() {
            $ageRangesList.children().each(function(index) {
                const rank = index + 1;
                $(this).attr('data-rank', rank);
                $(this).find('.rank-display').text(rank); // Update the displayed rank text
            });
        }



        // Add a new age range
        $addAgeRangeBtn.on('click', function() {
            // Find the highest max value to suggest a new range
            let highestMax = 0;
            $ageRangesList.find('.age-max').each(function() {
                const max = parseInt($(this).val());
                if (max > highestMax) highestMax = max;
            });

            // Default to 15-20 if no ranges exist yet
            const newMin = highestMax > 0 ? highestMax + 1 : 15;
            const newMax = newMin + 9 > 100 ? 100 : newMin + 9;

            addAgeRangeRow(newMin, newMax);
            updateRanks();
        });

        // Delete an age range
        $ageRangesList.on('click', '.age-range-delete', function() {
            $(this).closest('.age-range-row').remove();
            updateRanks();
        });

        // Save age configuration
        $saveAgeConfig.on('click', function() {
            // Validate ranges don't overlap
            const ranges = [];
            let isValid = true;
            let errorMessage = '';

            $ageRangesList.children().each(function() {
                const $row = $(this);
                const min = parseInt($row.find('.age-min').val());
                const max = parseInt($row.find('.age-max').val());
                const rank = parseInt($row.attr('data-rank'));

                // Basic validation
                if (min > max) {
                    errorMessage = 'Min age cannot be greater than max age';
                    isValid = false;
                    return false; // break the loop
                }

                // Check for overlaps with existing ranges
                for (const range of ranges) {
                    if ((min >= range.min && min <= range.max) ||
                        (max >= range.min && max <= range.max) ||
                        (min <= range.min && max >= range.max)) {
                        errorMessage = `Age range ${min}-${max} overlaps with ${range.min}-${range.max}`;
                        isValid = false;
                        return false; // break the loop
                    }
                }

                ranges.push({
                    min,
                    max,
                    rank
                });
            });

            if (!isValid) {
                Swal.fire('Error', errorMessage, 'error');
                return;
            }

            // Sort ranges by rank (lowest first)
            ranges.sort((a, b) => a.rank - b.rank);

            // Save the configuration
            criteriaConfigs.age.ranges = ranges;
            criteriaConfigs.age.customized = true;

            // Show the configuration badge
            $('#config-badge-age').addClass('active');

            // Close the modal
            $ageConfigModal.modal('hide');

            // Confirm to the user
            Swal.fire({
                icon: 'success',
                title: 'Age Ranges Configured',
                text: 'Your custom age scoring configuration has been saved',
                timer: 2000,
                showConfirmButton: false
            });
        });


        // ----------------------- EDUCATION CONFIGURATION -----------------------
        // Open education configuration modal
        function openEducationConfigModal() {
            const educationLevels = criteriaConfigs.education.levels;
            const weights = criteriaConfigs.education.weights;

            console.log(educationLevels);

            // Set weight inputs
            $('#educationLevelWeight').val(weights.level);
            $('#educationGradeWeight').val(weights.grade);

            // Clear the existing list
            $('#educationLevelsList').empty();

            // Add each education level
            educationLevels.list.forEach((level) => {
                addEducationLevelRow(level.name, level.rank);
            });

            // Initialize drag and drop
            initEducationSortable();

            // Show the modal
            $('#educationConfigModal').modal('show');
        }

        // Add a new education level row to the modal
        function addEducationLevelRow(name, rank) {
            const row = $(`
        <div class="education-level-row education-level-row-new" data-rank="${rank}">
            <div class="row w-100 align-items-center" style="margin: 0;">
                <div class="col-md-1" style="flex: 1;">
                    <div class="form-group">
                        <label>Rank</label>
                        <div class="form-control-static rank-display">${rank}</div>
                    </div>
                </div>
                <div class="col-md-8" style="flex: 4;">
                    <div class="form-group">
                        <label>Education Level</label>
                        <div class="form-control-static">${name}</div>
                    </div>
                </div>
                <div class="col-md-3 align-items-center d-flex" style="flex: 2; justify-content: center;">
                    <div class="education-level-handle">
                        <i class="fa-solid fa-arrows-up-down fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    `);

            $('#educationLevelsList').append(row);

            // Remove the animation class after animation completes
            setTimeout(() => {
                row.removeClass('education-level-row-new');
            }, 600);
        }

        // Initialize sortable functionality for reordering education levels
        function initEducationSortable() {
            if (typeof Sortable !== 'undefined') {
                new Sortable(document.getElementById('educationLevelsList'), {
                    handle: '.education-level-handle',
                    animation: 400,
                    easing: "cubic-bezier(0.68, -0.55, 0.265, 1.55)",
                    ghostClass: "sortable-ghost",
                    chosenClass: "sortable-chosen",
                    dragClass: "sortable-drag",
                    onEnd: function(evt) {
                        // Update rank numbers after sorting
                        $('.education-level-row').each(function(index) {
                            $(this).attr('data-rank', index + 1);
                            $(this).find('.rank-display').text(index + 1);
                        });

                        // Add highlight effect to the moved row
                        const movedRow = $(evt.item);
                        movedRow.addClass('highlight-row');
                        setTimeout(() => {
                            movedRow.removeClass('highlight-row');
                        }, 1000);
                    }
                });
            }
        }

        // Update education level ranks
        function updateEducationRanks() {
            $('#educationLevelsList').children().each(function(index) {
                const rank = index + 1;
                $(this).attr('data-rank', rank);
                $(this).find('.rank-display').text(rank);
            });
        }

        // Handle education level weight changes with improved validation
        $('#educationLevelWeight').on('input', function() {
            let levelWeight = parseInt($(this).val()) || 0;

            // Validation to ensure value is not negative and not more than 100
            if (levelWeight < 0) {
                levelWeight = 0;
                $(this).val(0);
            } else if (levelWeight > 100) {
                levelWeight = 100;
                $(this).val(100);
            }

            const gradeWeight = 100 - levelWeight;

            $('#educationGradeWeight').val(gradeWeight);
            $('#educationWeightTotalValue').text('100.0');

            // Always remove error class and enable save button
            $('#educationWeightTotal').removeClass('error');
            $('#saveEducationConfig').prop('disabled', false);
        });

        // Add min and max attributes to prevent invalid input
        $('#educationLevelWeight').attr('min', '0');
        $('#educationLevelWeight').attr('max', '100');
        $('#educationGradeWeight').attr('min', '0');
        $('#educationGradeWeight').attr('max', '100');

        // Save education configuration
        $('#saveEducationConfig').on('click', function() {
            const levels = [];

            // Get level weight (allow 0 and 100)
            const levelWeight = parseInt($('#educationLevelWeight').val());
            const gradeWeight = 100 - levelWeight;

            // Collect education levels with their ranks
            $('#educationLevelsList').children().each(function() {
                const $row = $(this);
                const name = $row.find('.form-control-static:not(.rank-display)').text();
                const rank = parseInt($row.attr('data-rank'));

                levels.push({
                    name,
                    rank
                });
            });

            // Sort levels by rank (lowest first)
            levels.sort((a, b) => a.rank - b.rank);

            // Save the configuration
            criteriaConfigs.education.levels = {
                list: levels // Wrap levels in a 'list' property to match original structure
            };
            criteriaConfigs.education.weights = {
                level: levelWeight,
                grade: gradeWeight
            };
            criteriaConfigs.education.customized = true;

            // Show the configuration badge
            $('#config-badge-education').addClass('active');

            // Close the modal
            $('#educationConfigModal').modal('hide');

            // Confirm to the user
            Swal.fire({
                icon: 'success',
                title: 'Education Settings Configured',
                text: 'Your custom education scoring configuration has been saved',
                timer: 2000,
                showConfirmButton: false
            });
        });


        // ----------------------- DISTANCE CONFIGURATION -----------------------
        function openDistanceConfigModal() {
            const distanceRanges = criteriaConfigs.distance.ranges;

            // Clear the existing list
            $('#distanceRangesList').empty();

            // Add each distance range
            distanceRanges.forEach((range, index) => {
                addDistanceRangeRow(range.min, range.max, range.rank);
            });

            // Initialize drag and drop
            initDistanceSortable();

            // Show the modal
            $('#distanceConfigModal').modal('show');
        }

        // Add a new distance range row to the modal
        function addDistanceRangeRow(min = 0, max = 5, rank = null) {
            const rowCount = $('#distanceRangesList').children().length;
            rank = rank || rowCount + 1;

            const row = $(`
        <div class="distance-range-row distance-range-row-new" data-rank="${rank}">
            <div class="row w-100" style="margin: 0;">
                <div class="col-md-1" style="flex: 1;">
                    <div class="form-group">
                        <label>Rank</label>
                        <div class="form-control-static rank-display">${rank}</div>
                    </div>
                </div>
                <div class="col-md-4" style="flex: 3;">
                    <div class="form-group">
                        <label>Min Distance (km)</label>
                        <input type="number" class="form-control distance-min" value="${min}" min="0" max="100" step="0.1">
                    </div>
                </div>
                <div class="col-md-4" style="flex: 3;">
                    <div class="form-group">
                        <label>Max Distance (km)</label>
                        <input type="number" class="form-control distance-max" value="${max}" min="0" max="100" step="0.1">
                    </div>
                </div>
                <div class="col-md-1 align-items-center d-flex" style="flex: 1; justify-content: center;">
                    <div class="distance-range-handle">
                        <i class="fa-solid fa-arrows-up-down fa-lg"></i>
                    </div>
                </div>
                <div class="col-md-1 align-items-center d-flex" style="flex: 1; justify-content: center;">
                    <button type="button" class="btn btn-danger distance-range-delete">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    `);

            $('#distanceRangesList').append(row);

            // Add field validation
            row.find('.distance-min, .distance-max').on('change', validateDistanceRanges);

            // Remove the animation class after animation completes
            setTimeout(() => {
                row.removeClass('distance-range-row-new');
            }, 600);
        }

        // Initialize sortable functionality for reordering distance rows
        function initDistanceSortable() {
            if (typeof Sortable !== 'undefined') {
                new Sortable(document.getElementById('distanceRangesList'), {
                    handle: '.distance-range-handle',
                    animation: 400,
                    easing: "cubic-bezier(0.68, -0.55, 0.265, 1.55)",
                    ghostClass: "sortable-ghost",
                    chosenClass: "sortable-chosen",
                    dragClass: "sortable-drag",
                    onEnd: function(evt) {
                        // Update rank numbers after sorting
                        $('.distance-range-row').each(function(index) {
                            $(this).attr('data-rank', index + 1);
                            $(this).find('.rank-display').text(index + 1);
                        });

                        // Add highlight effect to the moved row
                        const movedRow = $(evt.item);
                        movedRow.addClass('highlight-row');
                        setTimeout(() => {
                            movedRow.removeClass('highlight-row');
                        }, 1000);
                    }
                });
            }
        }

        // Update distance ranks
        function updateDistanceRanks() {
            $('#distanceRangesList').children().each(function(index) {
                const rank = index + 1;
                $(this).attr('data-rank', rank);
                $(this).find('.rank-display').text(rank);
            });
        }

        // Validate distance ranges for overlaps
        function validateDistanceRanges() {
            const rows = $('.distance-range-row');
            let hasError = false;

            // Reset all error states
            rows.removeClass('border-danger');
            $('.validation-error').remove();

            // Check each row
            rows.each(function(i) {
                const minDistance = parseFloat($(this).find('.distance-min').val());
                const maxDistance = parseFloat($(this).find('.distance-max').val());

                // Check min <= max
                if (minDistance >= maxDistance) {
                    $(this).addClass('border border-danger');
                    $(this).append('<div class="validation-error text-danger mt-2">Min distance must be less than max distance</div>');
                    hasError = true;
                    return;
                }

                // Check for overlaps with other rows
                rows.each(function(j) {
                    if (i !== j) {
                        const otherMin = parseFloat($(this).find('.distance-min').val());
                        const otherMax = parseFloat($(this).find('.distance-max').val());

                        if ((minDistance <= otherMax && maxDistance >= otherMin)) {
                            rows.eq(i).addClass('border border-danger');
                            if (!rows.eq(i).find('.validation-error').length) {
                                rows.eq(i).append('<div class="validation-error text-danger mt-2">Distance ranges cannot overlap</div>');
                            }
                            hasError = true;
                        }
                    }
                });
            });

            // Disable save button if errors exist
            $('#saveDistanceConfig').prop('disabled', hasError);

            return !hasError;
        }

        // Add a new distance range
        $('#addDistanceRangeBtn').on('click', function() {
            // Find the highest max value to suggest a new range
            let highestMax = 0;
            $('#distanceRangesList').find('.distance-max').each(function() {
                const max = parseFloat($(this).val());
                if (max > highestMax) highestMax = max;
            });

            // Default to 0-3 if no ranges exist yet
            const newMin = highestMax > 0 ? highestMax + 0.1 : 0;
            const newMax = newMin + 3 > 100 ? 100 : newMin + 3;

            addDistanceRangeRow(newMin, newMax);
            updateDistanceRanks();
        });

        // Delete a distance range
        $('#distanceRangesList').on('click', '.distance-range-delete', function() {
            $(this).closest('.distance-range-row').remove();
            updateDistanceRanks();
        });

        // Save distance configuration
        $('#saveDistanceConfig').on('click', function() {
            // Validate ranges don't overlap
            const ranges = [];
            let isValid = true;
            let errorMessage = '';

            $('#distanceRangesList').children().each(function() {
                const $row = $(this);
                const min = parseFloat($row.find('.distance-min').val());
                const max = parseFloat($row.find('.distance-max').val());
                const rank = parseInt($row.attr('data-rank'));

                // Basic validation
                if (min > max) {
                    errorMessage = 'Min distance cannot be greater than max distance';
                    isValid = false;
                    return false; // break the loop
                }

                // Check for overlaps with existing ranges
                for (const range of ranges) {
                    if ((min >= range.min && min <= range.max) ||
                        (max >= range.min && max <= range.max) ||
                        (min <= range.min && max >= range.max)) {
                        errorMessage = `Distance range ${min}-${max} overlaps with ${range.min}-${range.max}`;
                        isValid = false;
                        return false; // break the loop
                    }
                }

                ranges.push({
                    min,
                    max,
                    rank
                });
            });

            if (!isValid) {
                Swal.fire('Error', errorMessage, 'error');
                return;
            }

            // Sort ranges by rank (lowest first)
            ranges.sort((a, b) => a.rank - b.rank);

            // Save the configuration
            criteriaConfigs.distance.ranges = ranges;
            criteriaConfigs.distance.customized = true;

            // Show the configuration badge
            $('#config-badge-distance').addClass('active');

            // Close the modal
            $('#distanceConfigModal').modal('hide');

            // Confirm to the user
            Swal.fire({
                icon: 'success',
                title: 'Distance Ranges Configured',
                text: 'Your custom distance scoring configuration has been saved',
                timer: 2000,
                showConfirmButton: false
            });
        });

        // ----------------------- SALARY CONFIGURATION -----------------------
        // Expected Salary Configuration
        function openExpectedSalaryConfigModal() {
            const salaryRanges = criteriaConfigs.expected_salary.ranges;

            // Clear the existing list
            $('#salaryRangesList').empty();

            // Add each salary range
            salaryRanges.forEach((range, index) => {
                addSalaryRangeRow(range.min, range.max, range.rank);
            });

            // Initialize drag and drop
            initSalarySortable();

            // Show the modal
            $('#expectedSalaryConfigModal').modal('show');
        }

        // Add a new salary range row to the modal
        function addSalaryRangeRow(min = 1000000, max = 5000000, rank = null) {
            const rowCount = $('#salaryRangesList').children().length;
            rank = rank || rowCount + 1;

            const row = $(`
        <div class="salary-range-row salary-range-row-new" data-rank="${rank}">
            <div class="row w-100" style="margin: 0;">
                <div class="col-md-1" style="flex: 1;">
                    <div class="form-group">
                        <label>Rank</label>
                        <div class="form-control-static rank-display">${rank}</div>
                    </div>
                </div>
                <div class="col-md-4" style="flex: 3;">
                    <div class="form-group">
                        <label>Min Salary (Rp)</label>
                        <input type="number" class="form-control salary-min" value="${min}" min="0" step="100000">
                    </div>
                </div>
                <div class="col-md-4" style="flex: 3;">
                    <div class="form-group">
                        <label>Max Salary (Rp)</label>
                        <input type="number" class="form-control salary-max" value="${max}" min="0" step="100000">
                    </div>
                </div>
                <div class="col-md-1 align-items-center d-flex" style="flex: 1; justify-content: center;">
                    <div class="salary-range-handle">
                        <i class="fa-solid fa-arrows-up-down fa-lg"></i>
                    </div>
                </div>
                <div class="col-md-1 align-items-center d-flex" style="flex: 1; justify-content: center;">
                    <button type="button" class="btn btn-danger salary-range-delete">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    `);

            $('#salaryRangesList').append(row);

            // Add field validation
            row.find('.salary-min, .salary-max').on('change', validateSalaryRanges);

            // Remove the animation class after animation completes
            setTimeout(() => {
                row.removeClass('salary-range-row-new');
            }, 600);
        }

        // Initialize sortable functionality for reordering salary rows
        function initSalarySortable() {
            if (typeof Sortable !== 'undefined') {
                new Sortable(document.getElementById('salaryRangesList'), {
                    handle: '.salary-range-handle',
                    animation: 400,
                    easing: "cubic-bezier(0.68, -0.55, 0.265, 1.55)",
                    ghostClass: "sortable-ghost",
                    chosenClass: "sortable-chosen",
                    dragClass: "sortable-drag",
                    onEnd: function(evt) {
                        // Update rank numbers after sorting
                        $('.salary-range-row').each(function(index) {
                            $(this).attr('data-rank', index + 1);
                            $(this).find('.rank-display').text(index + 1);
                        });

                        // Add highlight effect to the moved row
                        const movedRow = $(evt.item);
                        movedRow.addClass('highlight-row');
                        setTimeout(() => {
                            movedRow.removeClass('highlight-row');
                        }, 1000);
                    }
                });
            }
        }

        // Update salary ranks
        function updateSalaryRanks() {
            $('#salaryRangesList').children().each(function(index) {
                const rank = index + 1;
                $(this).attr('data-rank', rank);
                $(this).find('.rank-display').text(rank);
            });
        }

        // Validate salary ranges for overlaps
        function validateSalaryRanges() {
            const rows = $('.salary-range-row');
            let hasError = false;

            // Reset all error states
            rows.removeClass('border-danger');
            $('.validation-error').remove();

            // Check each row
            rows.each(function(i) {
                const minSalary = parseInt($(this).find('.salary-min').val());
                const maxSalary = parseInt($(this).find('.salary-max').val());

                // Check min <= max
                if (minSalary >= maxSalary) {
                    $(this).addClass('border border-danger');
                    $(this).append('<div class="validation-error text-danger mt-2">Min salary must be less than max salary</div>');
                    hasError = true;
                    return;
                }

                // Check for overlaps with other rows
                rows.each(function(j) {
                    if (i !== j) {
                        const otherMin = parseInt($(this).find('.salary-min').val());
                        const otherMax = parseInt($(this).find('.salary-max').val());

                        if ((minSalary <= otherMax && maxSalary >= otherMin)) {
                            rows.eq(i).addClass('border border-danger');
                            if (!rows.eq(i).find('.validation-error').length) {
                                rows.eq(i).append('<div class="validation-error text-danger mt-2">Salary ranges cannot overlap</div>');
                            }
                            hasError = true;
                        }
                    }
                });
            });

            // Disable save button if errors exist
            $('#saveExpectedSalaryConfig').prop('disabled', hasError);

            return !hasError;
        }

        // Add a new salary range
        $('#addSalaryRangeBtn').on('click', function() {
            // Find the highest max value to suggest a new range
            let highestMax = 0;
            $('#salaryRangesList').find('.salary-max').each(function() {
                const max = parseInt($(this).val());
                if (max > highestMax) highestMax = max;
            });

            // Default to 1M-2M if no ranges exist yet
            const newMin = highestMax > 0 ? highestMax + 100000 : 1000000;
            const newMax = newMin + 2000000;

            addSalaryRangeRow(newMin, newMax);
            updateSalaryRanks();
        });

        // Delete a salary range
        $('#salaryRangesList').on('click', '.salary-range-delete', function() {
            $(this).closest('.salary-range-row').remove();
            updateSalaryRanks();
        });

        // Save expected salary configuration
        $('#saveExpectedSalaryConfig').on('click', function() {
            // Validate ranges don't overlap
            const ranges = [];
            let isValid = true;
            let errorMessage = '';

            $('#salaryRangesList').children().each(function() {
                const $row = $(this);
                const min = parseInt($row.find('.salary-min').val());
                const max = parseInt($row.find('.salary-max').val());
                const rank = parseInt($row.attr('data-rank'));

                // Basic validation
                if (min > max) {
                    errorMessage = 'Min salary cannot be greater than max salary';
                    isValid = false;
                    return false; // break the loop
                }

                // Check for overlaps with existing ranges
                for (const range of ranges) {
                    if ((min >= range.min && min <= range.max) ||
                        (max >= range.min && max <= range.max) ||
                        (min <= range.min && max >= range.max)) {
                        errorMessage = `Salary range ${min}-${max} overlaps with ${range.min}-${range.max}`;
                        isValid = false;
                        return false; // break the loop
                    }
                }

                ranges.push({
                    min,
                    max,
                    rank
                });
            });

            if (!isValid) {
                Swal.fire('Error', errorMessage, 'error');
                return;
            }

            // Sort ranges by rank (lowest first)
            ranges.sort((a, b) => a.rank - b.rank);

            // Save the configuration
            criteriaConfigs.expected_salary.ranges = ranges;
            criteriaConfigs.expected_salary.customized = true;

            // Show the configuration badge
            $('#config-badge-expected_salary').addClass('active');

            // Close the modal
            $('#expectedSalaryConfigModal').modal('hide');

            // Confirm to the user
            Swal.fire({
                icon: 'success',
                title: 'Salary Ranges Configured',
                text: 'Your custom salary scoring configuration has been saved',
                timer: 2000,
                showConfirmButton: false
            });
        });


        // ----------------------- TRAINING CONFIGURATION -----------------------
        // Open training configuration modal
        function openTrainingConfigModal() {
            // Set weight inputs
            $('#trainingPeriodWeight').val(criteriaConfigs.training.weights.period);
            $('#trainingAmountWeight').val(criteriaConfigs.training.weights.amount);

            // Clear existing lists
            $('#trainingPeriodList').empty();
            $('#trainingAmountList').empty();

            // Add each training period range
            criteriaConfigs.training.period.forEach((range) => {
                addTrainingRangeRow('period', range.min, range.max, range.rank);
            });

            // Add each training amount range
            criteriaConfigs.training.amount.forEach((range) => {
                addTrainingRangeRow('amount', range.min, range.max, range.rank);
            });

            // Initialize drag and drop
            initTrainingSortable('period');
            initTrainingSortable('amount');

            // Show the modal
            $('#trainingConfigModal').modal('show');
        }

        // Add training range row (period or amount)
        function addTrainingRangeRow(type, min = 0, max = 3, rank = null) {
            const listId = `training${type.charAt(0).toUpperCase() + type.slice(1)}List`;
            const rowCount = $(`#${listId}`).children().length;
            rank = rank || rowCount + 1;

            const row = $(`
        <div class="training-range-row training-range-row-new" data-rank="${rank}" data-type="${type}">
            <div class="row w-100" style="margin: 0;">
                <div class="col-md-1" style="flex: 1;">
                    <div class="form-group">
                        <label>Rank</label>
                        <div class="form-control-static rank-display">${rank}</div>
                    </div>
                </div>
                <div class="col-md-4" style="flex: 3;">
                    <div class="form-group">
                        <label>Min ${type === 'period' ? 'Years' : 'Count'}</label>
                        <input type="number" class="form-control training-min" value="${min}" min="0" max="${type === 'period' ? '50' : '100'}" step="0.1">
                    </div>
                </div>
                <div class="col-md-4" style="flex: 3;">
                    <div class="form-group">
                        <label>Max ${type === 'period' ? 'Years' : 'Count'}</label>
                        <input type="number" class="form-control training-max" value="${max}" min="0" max="${type === 'period' ? '50' : '100'}" step="0.1">
                    </div>
                </div>
                <div class="col-md-1 align-items-center d-flex" style="flex: 1; justify-content: center;">
                    <div class="training-range-handle">
                        <i class="fa-solid fa-arrows-up-down fa-lg"></i>
                    </div>
                </div>
                <div class="col-md-1 align-items-center d-flex" style="flex: 1; justify-content: center;">
                    <button type="button" class="btn btn-danger training-range-delete">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    `);

            $(`#${listId}`).append(row);

            // Add field validation
            row.find('.training-min, .training-max').on('change', validateTrainingRanges);

            // Remove the animation class after animation completes
            setTimeout(() => {
                row.removeClass('training-range-row-new');
            }, 600);
        }

        // Initialize sortable functionality for training ranges
        function initTrainingSortable(type) {
            const listId = `training${type.charAt(0).toUpperCase() + type.slice(1)}List`;

            if (typeof Sortable !== 'undefined') {
                new Sortable(document.getElementById(listId), {
                    handle: '.training-range-handle',
                    animation: 400,
                    easing: "cubic-bezier(0.68, -0.55, 0.265, 1.55)",
                    ghostClass: "sortable-ghost",
                    chosenClass: "sortable-chosen",
                    dragClass: "sortable-drag",
                    onEnd: function(evt) {
                        // Update rank numbers after sorting
                        $(`#${listId} .training-range-row`).each(function(index) {
                            $(this).attr('data-rank', index + 1);
                            $(this).find('.rank-display').text(index + 1);
                        });

                        // Add highlight effect to the moved row
                        const movedRow = $(evt.item);
                        movedRow.addClass('highlight-row');
                        setTimeout(() => {
                            movedRow.removeClass('highlight-row');
                        }, 1000);
                    }
                });
            }
        }

        // Validate training ranges
        function validateTrainingRanges() {
            const type = $(this).closest('.training-range-row').data('type');
            const listId = `training${type.charAt(0).toUpperCase() + type.slice(1)}List`;
            const rows = $(`#${listId} .training-range-row`);
            let hasError = false;

            // Reset all error states
            rows.removeClass('border-danger');
            $(`#${listId} .validation-error`).remove();

            // Check each row
            rows.each(function(i) {
                const minVal = parseFloat($(this).find('.training-min').val());
                const maxVal = parseFloat($(this).find('.training-max').val());

                // Check min <= max
                if (minVal >= maxVal) {
                    $(this).addClass('border border-danger');
                    $(this).append('<div class="validation-error text-danger mt-2">Min value must be less than max value</div>');
                    hasError = true;
                    return;
                }

                // Check for overlaps with other rows
                rows.each(function(j) {
                    if (i !== j) {
                        const otherMin = parseFloat($(this).find('.training-min').val());
                        const otherMax = parseFloat($(this).find('.training-max').val());

                        if ((minVal < otherMax && maxVal > otherMin)) {
                            rows.eq(i).addClass('border border-danger');
                            if (!rows.eq(i).find('.validation-error').length) {
                                rows.eq(i).append('<div class="validation-error text-danger mt-2">Ranges cannot overlap</div>');
                            }
                            hasError = true;
                        }
                    }
                });
            });

            // Disable save button if errors exist
            $('#saveTrainingConfig').prop('disabled', hasError);

            return !hasError;
        }


        // Update ranks for training ranges
        function updateTrainingRanks(type) {
            const listId = `training${type.charAt(0).toUpperCase() + type.slice(1)}List`;

            $(`#${listId}`).children().each(function(index) {
                const rank = index + 1;
                $(this).attr('data-rank', rank);
                $(this).find('.rank-display').text(rank);
            });
        }

        // Add new training period range
        $('#addTrainingPeriodBtn').on('click', function() {
            // Find the highest max value to suggest a new range
            let highestMax = 0;
            $('#trainingPeriodList').find('.training-max').each(function() {
                const max = parseFloat($(this).val());
                if (max > highestMax) highestMax = max;
            });

            // Default to 0-1 if no ranges exist yet
            const newMin = highestMax > 0 ? highestMax + 1 : 0;
            const newMax = newMin + 2 > 50 ? 50 : newMin + 2;

            addTrainingRangeRow('period', newMin, newMax);
            updateTrainingRanks('period');
        });

        // Add new training amount range
        $('#addTrainingAmountBtn').on('click', function() {
            // Find the highest max value to suggest a new range
            let highestMax = 0;
            $('#trainingAmountList').find('.training-max').each(function() {
                const max = parseFloat($(this).val());
                if (max > highestMax) highestMax = max;
            });

            // Default to 0-1 if no ranges exist yet
            const newMin = highestMax > 0 ? highestMax + 1 : 0;
            const newMax = newMin + 2 > 100 ? 100 : newMin + 2;

            addTrainingRangeRow('amount', newMin, newMax);
            updateTrainingRanks('amount');
        });

        // Delete training range
        $('#trainingPeriodList, #trainingAmountList').on('click', '.training-range-delete', function() {
            const row = $(this).closest('.training-range-row');
            const type = row.data('type');

            row.remove();
            updateTrainingRanks(type);
        });

        // Handle training weight changes
        $('#trainingPeriodWeight').on('input', function() {
            const periodWeight = parseFloat($(this).val()) || 0;

            // Ensure periodWeight is between 0-100
            const validPeriodWeight = Math.min(Math.max(periodWeight, 0), 100);
            if (validPeriodWeight !== periodWeight) {
                $(this).val(validPeriodWeight);
            }

            const amountWeight = 100 - validPeriodWeight;

            $('#trainingAmountWeight').val(amountWeight);
            $('#trainingWeightTotalValue').text('100.0');

            // Hapus kondisi pembatasan sebelumnya
            $('#trainingWeightTotal').removeClass('error');
            $('#saveTrainingConfig').prop('disabled', false);
        });


        // Add input validation directly on the input element
        $('#trainingPeriodWeight').attr('min', '0').attr('max', '100').on('change', function() {
            const value = parseFloat($(this).val());
            if (isNaN(value) || value < 0) {
                $(this).val(0);
                $('#trainingAmountWeight').val(100);
            } else if (value > 100) {
                $(this).val(100);
                $('#trainingAmountWeight').val(0);
            }
        });


        // Save training configuration
        $('#saveTrainingConfig').on('click', function() {
            // Collect period ranges with their ranks
            const periodRanges = [];
            $('#trainingPeriodList').children().each(function() {
                const min = parseFloat($(this).find('.training-min').val());
                const max = parseFloat($(this).find('.training-max').val());
                const rank = parseFloat($(this).attr('data-rank'));

                periodRanges.push({
                    min,
                    max,
                    rank
                });
            });

            // Collect amount ranges with their ranks
            const amountRanges = [];
            $('#trainingAmountList').children().each(function() {
                const min = parseFloat($(this).find('.training-min').val());
                const max = parseFloat($(this).find('.training-max').val());
                const rank = parseFloat($(this).attr('data-rank'));

                amountRanges.push({
                    min,
                    max,
                    rank
                });
            });

            // Get weights
            const periodWeight = parseFloat($('#trainingPeriodWeight').val());
            const amountWeight = parseFloat($('#trainingAmountWeight').val());

            // Tambahkan validasi untuk memastikan bobot tidak diatur ulang ke default
            if (periodRanges.length > 0 && amountRanges.length > 0) {
                // Sort ranges by rank (lowest first)
                periodRanges.sort((a, b) => a.rank - b.rank);
                amountRanges.sort((a, b) => a.rank - b.rank);

                // Save the configuration
                criteriaConfigs.training.period = periodRanges;
                criteriaConfigs.training.amount = amountRanges;
                criteriaConfigs.training.weights = {
                    period: periodWeight,
                    amount: amountWeight
                };
                criteriaConfigs.training.customized = true;

                // Show the configuration badge
                $('#config-badge-training').addClass('active');

                // Close the modal
                $('#trainingConfigModal').modal('hide');

                // Confirm to the user
                Swal.fire({
                    icon: 'success',
                    title: 'Training Settings Configured',
                    text: 'Your custom training scoring configuration has been saved',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                // Tampilkan pesan kesalahan jika tidak ada range yang valid
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Configuration',
                    text: 'Please add at least one range for both period and amount',
                    showConfirmButton: true
                });
            }
        });

        // ----------------------- EXPERIENCE CONFIGURATION -----------------------
        // Open experience configuration modal
        function openExperienceConfigModal() {
            // Set weight inputs
            $('#experiencePeriodWeight').val(criteriaConfigs.experience_duration.weights.period);
            $('#experienceAmountWeight').val(criteriaConfigs.experience_duration.weights.amount);

            // Clear existing lists
            $('#experiencePeriodList').empty();
            $('#experienceAmountList').empty();

            // Add each experience period range
            criteriaConfigs.experience_duration.period.forEach((range) => {
                addExperienceRangeRow('period', range.min, range.max, range.rank);
            });

            // Add each experience amount range
            criteriaConfigs.experience_duration.amount.forEach((range) => {
                addExperienceRangeRow('amount', range.min, range.max, range.rank);
            });

            // Initialize drag and drop
            initExperienceSortable('period');
            initExperienceSortable('amount');

            // Show the modal
            $('#experienceConfigModal').modal('show');
        }

        // Add experience range row (period or amount)
        function addExperienceRangeRow(type, min = 0, max = 3, rank = null) {
            const listId = `experience${type.charAt(0).toUpperCase() + type.slice(1)}List`;
            const rowCount = $(`#${listId}`).children().length;
            rank = rank || rowCount + 1;

            const row = $(`
        <div class="experience-range-row experience-range-row-new" data-rank="${rank}" data-type="${type}">
            <div class="row w-100" style="margin: 0;">
                <div class="col-md-1" style="flex: 1;">
                    <div class="form-group">
                        <label>Rank</label>
                        <div class="form-control-static rank-display">${rank}</div>
                    </div>
                </div>
                <div class="col-md-4" style="flex: 3;">
                    <div class="form-group">
                        <label>Min ${type === 'period' ? 'Years' : 'Jobs'}</label>
                        <input type="number" class="form-control experience-min" value="${min}" min="0" max="${type === 'period' ? '50' : '100'}" step="0.1">
                    </div>
                </div>
                <div class="col-md-4" style="flex: 3;">
                    <div class="form-group">
                        <label>Max ${type === 'period' ? 'Years' : 'Jobs'}</label>
                        <input type="number" class="form-control experience-max" value="${max}" min="0" max="${type === 'period' ? '50' : '100'}" step="0.1">
                    </div>
                </div>
                <div class="col-md-1 align-items-center d-flex" style="flex: 1; justify-content: center;">
                    <div class="experience-range-handle">
                        <i class="fa-solid fa-arrows-up-down fa-lg"></i>
                    </div>
                </div>
                <div class="col-md-1 align-items-center d-flex" style="flex: 1; justify-content: center;">
                    <button type="button" class="btn btn-danger experience-range-delete">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    `);

            $(`#${listId}`).append(row);

            // Add field validation
            row.find('.experience-min, .experience-max').on('change', validateExperienceRanges);

            // Remove the animation class after animation completes
            setTimeout(() => {
                row.removeClass('experience-range-row-new');
            }, 600);
        }

        // Initialize sortable functionality for experience ranges
        function initExperienceSortable(type) {
            const listId = `experience${type.charAt(0).toUpperCase() + type.slice(1)}List`;

            if (typeof Sortable !== 'undefined') {
                new Sortable(document.getElementById(listId), {
                    handle: '.experience-range-handle',
                    animation: 400,
                    easing: "cubic-bezier(0.68, -0.55, 0.265, 1.55)",
                    ghostClass: "sortable-ghost",
                    chosenClass: "sortable-chosen",
                    dragClass: "sortable-drag",
                    onEnd: function(evt) {
                        // Update rank numbers after sorting
                        $(`#${listId} .experience-range-row`).each(function(index) {
                            $(this).attr('data-rank', index + 1);
                            $(this).find('.rank-display').text(index + 1);
                        });

                        // Add highlight effect to the moved row
                        const movedRow = $(evt.item);
                        movedRow.addClass('highlight-row');
                        setTimeout(() => {
                            movedRow.removeClass('highlight-row');
                        }, 1000);
                    }
                });
            }
        }
        // Validate experience ranges
        function validateExperienceRanges() {
            const type = $(this).closest('.experience-range-row').data('type');
            const listId = `experience${type.charAt(0).toUpperCase() + type.slice(1)}List`;
            const rows = $(`#${listId} .experience-range-row`);
            let hasError = false;

            // Reset all error states
            rows.removeClass('border-danger');
            $(`#${listId} .validation-error`).remove();

            // Check each row
            rows.each(function(i) {
                const minVal = parseFloat($(this).find('.experience-min').val());
                const maxVal = parseFloat($(this).find('.experience-max').val());

                // Check min <= max
                if (minVal >= maxVal) {
                    $(this).addClass('border border-danger');
                    $(this).append('<div class="validation-error text-danger mt-2">Min value must be less than max value</div>');
                    hasError = true;
                    return;
                }

                // Check for overlaps with other rows
                rows.each(function(j) {
                    if (i !== j) {
                        const otherMin = parseFloat($(this).find('.experience-min').val());
                        const otherMax = parseFloat($(this).find('.experience-max').val());

                        if ((minVal <= otherMax && maxVal >= otherMin)) {
                            rows.eq(i).addClass('border border-danger');
                            if (!rows.eq(i).find('.validation-error').length) {
                                rows.eq(i).append('<div class="validation-error text-danger mt-2">Ranges cannot overlap</div>');
                            }
                            hasError = true;
                        }
                    }
                });
            });

            // Disable save button if errors exist
            $('#saveExperienceConfig').prop('disabled', hasError);

            return !hasError;
        }

        // Update ranks for experience ranges
        function updateExperienceRanks(type) {
            const listId = `experience${type.charAt(0).toUpperCase() + type.slice(1)}List`;

            $(`#${listId}`).children().each(function(index) {
                const rank = index + 1;
                $(this).attr('data-rank', rank);
                $(this).find('.rank-display').text(rank);
            });
        }

        // Add new experience period range
        $('#addExperiencePeriodBtn').on('click', function() {
            // Find the highest max value to suggest a new range
            let highestMax = 0;
            $('#experiencePeriodList').find('.experience-max').each(function() {
                const max = parseFloat($(this).val());
                if (max > highestMax) highestMax = max;
            });

            // Default to 0-1 if no ranges exist yet
            const newMin = highestMax > 0 ? highestMax + 1 : 0;
            const newMax = newMin + 2 > 50 ? 50 : newMin + 2;

            addExperienceRangeRow('period', newMin, newMax);
            updateExperienceRanks('period');
        });

        // Add new experience amount range
        $('#addExperienceAmountBtn').on('click', function() {
            // Find the highest max value to suggest a new range
            let highestMax = 0;
            $('#experienceAmountList').find('.experience-max').each(function() {
                const max = parseFloat($(this).val());
                if (max > highestMax) highestMax = max;
            });

            // Default to 0-1 if no ranges exist yet
            const newMin = highestMax > 0 ? highestMax + 1 : 0;
            const newMax = newMin + 2 > 100 ? 100 : newMin + 2;

            addExperienceRangeRow('amount', newMin, newMax);
            updateExperienceRanks('amount');
        });

        // Delete experience range
        $('#experiencePeriodList, #experienceAmountList').on('click', '.experience-range-delete', function() {
            const row = $(this).closest('.experience-range-row');
            const type = row.data('type');

            row.remove();
            updateExperienceRanks(type);
        });

        // Handle experience weight changes
        $('#experiencePeriodWeight').on('input', function() {
            const periodWeight = parseFloat($(this).val()) || 0;
            const validPeriodWeight = Math.min(Math.max(periodWeight, 0), 100);

            if (validPeriodWeight !== periodWeight) {
                $(this).val(validPeriodWeight);
            }

            const amountWeight = 100 - validPeriodWeight;
            $('#experienceAmountWeight').val(amountWeight);
            $('#experienceWeightTotalValue').text('100.0');

            // Hapus kondisi pembatasan
            $('#experienceWeightTotal').removeClass('error');
            $('#saveExperienceConfig').prop('disabled', false);
        });

        // Add input validation directly on the input element
        $('#experiencePeriodWeight').attr('min', '0').attr('max', '100').on('change', function() {
            const value = parseFloat($(this).val());
            if (isNaN(value) || value < 0) {
                $(this).val(0);
                $('#experienceAmountWeight').val(100);
            } else if (value > 100) {
                $(this).val(100);
                $('#experienceAmountWeight').val(0);
            }
        });

        // Save experience configuration
        $('#saveExperienceConfig').on('click', function() {
            // Collect period ranges with their ranks
            const periodRanges = [];
            $('#experiencePeriodList').children().each(function() {
                const min = parseFloat($(this).find('.experience-min').val());
                const max = parseFloat($(this).find('.experience-max').val());
                const rank = parseFloat($(this).attr('data-rank'));

                periodRanges.push({
                    min,
                    max,
                    rank
                });
            });

            // Collect amount ranges with their ranks
            const amountRanges = [];
            $('#experienceAmountList').children().each(function() {
                const min = parseFloat($(this).find('.experience-min').val());
                const max = parseFloat($(this).find('.experience-max').val());
                const rank = parseFloat($(this).attr('data-rank'));

                amountRanges.push({
                    min,
                    max,
                    rank
                });
            });

            // Get weights
            const periodWeight = parseFloat($('#experiencePeriodWeight').val());
            const amountWeight = parseFloat($('#experienceAmountWeight').val());

            // Tambahkan validasi untuk memastikan range dan bobot valid
            if (periodRanges.length > 0 && amountRanges.length > 0) {
                // Validasi min max di setiap range
                const isPeriodRangesValid = periodRanges.every(range => range.min < range.max);
                const isAmountRangesValid = amountRanges.every(range => range.min < range.max);

                if (isPeriodRangesValid && isAmountRangesValid) {
                    // Sort ranges by rank (lowest first)
                    periodRanges.sort((a, b) => a.rank - b.rank);
                    amountRanges.sort((a, b) => a.rank - b.rank);

                    // Save the configuration
                    criteriaConfigs.experience_duration.period = periodRanges;
                    criteriaConfigs.experience_duration.amount = amountRanges;
                    criteriaConfigs.experience_duration.weights = {
                        period: periodWeight,
                        amount: amountWeight
                    };
                    criteriaConfigs.experience_duration.customized = true;

                    // Show the configuration badge
                    $('#config-badge-experience_duration').addClass('active');

                    // Close the modal
                    $('#experienceConfigModal').modal('hide');

                    // Confirm to the user
                    Swal.fire({
                        icon: 'success',
                        title: 'Experience Settings Configured',
                        text: 'Your custom experience scoring configuration has been saved',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    // Tampilkan pesan kesalahan jika range tidak valid
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Ranges',
                        text: 'Ensure each range has a minimum value less than its maximum value',
                        showConfirmButton: true
                    });
                }
            } else {
                // Tampilkan pesan kesalahan jika tidak ada range
                Swal.fire({
                    icon: 'error',
                    title: 'Incomplete Configuration',
                    text: 'Please add at least one range for both period and amount',
                    showConfirmButton: true
                });
            }
        });

        // ----------------------- ORGANIZATION CONFIGURATION -----------------------
        // Open organization configuration modal
        function openOrganizationConfigModal() {
            // Clear existing lists
            $('#organizationPeriodList').empty();
            $('#organizationAmountList').empty();

            // Set weight values
            $('#organizationPeriodWeight').val(criteriaConfigs.organization.weights.period);
            $('#organizationAmountWeight').val(criteriaConfigs.organization.weights.amount);

            // Add period ranges
            criteriaConfigs.organization.period.forEach((range) => {
                addOrganizationPeriodRow(range.min, range.max, range.rank);
            });

            // Add amount ranges
            criteriaConfigs.organization.amount.forEach((range) => {
                addOrganizationAmountRow(range.min, range.max, range.rank);
            });

            // Initialize drag and drop
            initOrganizationSortable('organizationPeriodList');
            initOrganizationSortable('organizationAmountList');

            // Show the modal
            $('#organizationConfigModal').modal('show');
        }

        // Add organization period row
        function addOrganizationPeriodRow(min = 0, max = 0, rank = null) {
            const rowCount = $('#organizationPeriodList').children().length;
            rank = rank || rowCount + 1;

            const row = $(`
        <div class="organization-period-row organization-row-new" data-rank="${rank}">
            <div class="row w-100" style="margin: 0;">
                <div class="col-md-1">
                    <div class="form-group">
                        <label>Rank</label>
                        <div class="form-control-static rank-display">${rank}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Min Years</label>
                        <input type="number" class="form-control period-min" value="${min}" min="0" step="0.1">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Max Years</label>
                        <input type="number" class="form-control period-max" value="${max}" min="0" step="0.1">
                    </div>
                </div>
                <div class="col-md-1 align-items-center d-flex" style="justify-content: center;">
                    <div class="organization-handle">
                        <i class="fa-solid fa-arrows-up-down fa-lg"></i>
                    </div>
                </div>
                <div class="col-md-2 align-items-center d-flex" style="justify-content: center;">
                    <button type="button" class="btn btn-danger organization-delete">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    `);

            $('#organizationPeriodList').append(row);

            // Remove the animation class after animation completes
            setTimeout(() => {
                row.removeClass('organization-row-new');
            }, 600);
        }

        // Add organization amount row
        function addOrganizationAmountRow(min = 0, max = 0, rank = null) {
            const rowCount = $('#organizationAmountList').children().length;
            rank = rank || rowCount + 1;

            const row = $(`
        <div class="organization-amount-row organization-row-new" data-rank="${rank}">
            <div class="row w-100" style="margin: 0;">
                <div class="col-md-1">
                    <div class="form-group">
                        <label>Rank</label>
                        <div class="form-control-static rank-display">${rank}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Min Amount</label>
                        <input type="number" class="form-control amount-min" value="${min}" min="0" step="1">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Max Amount</label>
                        <input type="number" class="form-control amount-max" value="${max}" min="0" step="1">
                    </div>
                </div>
                <div class="col-md-1 align-items-center d-flex" style="justify-content: center;">
                    <div class="organization-handle">
                        <i class="fa-solid fa-arrows-up-down fa-lg"></i>
                    </div>
                </div>
                <div class="col-md-2 align-items-center d-flex" style="justify-content: center;">
                    <button type="button" class="btn btn-danger organization-delete">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    `);

            $('#organizationAmountList').append(row);

            // Remove the animation class after animation completes
            setTimeout(() => {
                row.removeClass('organization-row-new');
            }, 600);
        }

        // Initialize sortable functionality for organizations
        function initOrganizationSortable(listId) {
            if (typeof Sortable !== 'undefined') {
                new Sortable(document.getElementById(listId), {
                    handle: '.organization-handle',
                    animation: 400,
                    easing: "cubic-bezier(0.68, -0.55, 0.265, 1.55)",
                    ghostClass: "sortable-ghost",
                    chosenClass: "sortable-chosen",
                    dragClass: "sortable-drag",
                    onEnd: function(evt) {
                        // Update rank numbers after sorting
                        $(`#${listId} .organization-period-row, #${listId} .organization-amount-row`).each(function(index) {
                            $(this).attr('data-rank', index + 1);
                            $(this).find('.rank-display').text(index + 1);
                        });

                        // Add highlight effect to the moved row
                        const movedRow = $(evt.item);
                        movedRow.addClass('highlight-row');
                        setTimeout(() => {
                            movedRow.removeClass('highlight-row');
                        }, 1000);
                    }
                });
            }
        }

        // Tambahkan handler untuk input bobot
        $('#organizationPeriodWeight').on('input', function() {
            const periodWeight = parseFloat($(this).val()) || 0;
            const validPeriodWeight = Math.min(Math.max(periodWeight, 0), 100);

            if (validPeriodWeight !== periodWeight) {
                $(this).val(validPeriodWeight);
            }

            const amountWeight = 100 - validPeriodWeight;
            $('#organizationAmountWeight').val(amountWeight);
            $('#organizationWeightTotalValue').text('100.0');
        });

        // Add input validation directly on the input element
        $('#organizationPeriodWeight').attr('min', '0').attr('max', '100').on('change', function() {
            const value = parseFloat($(this).val());
            if (isNaN(value) || value < 0) {
                $(this).val(0);
                $('#organizationAmountWeight').val(100);
            } else if (value > 100) {
                $(this).val(100);
                $('#organizationAmountWeight').val(0);
            }
        });

        // Add new organization period range
        $('#addOrganizationPeriodBtn').on('click', function() {
            addOrganizationPeriodRow();
            updateOrganizationRanks('organizationPeriodList');
        });

        // Add new organization amount range
        $('#addOrganizationAmountBtn').on('click', function() {
            addOrganizationAmountRow();
            updateOrganizationRanks('organizationAmountList');
        });

        // Update ranks for organizations
        function updateOrganizationRanks(listId) {
            $(`#${listId}`).children().each(function(index) {
                const rank = index + 1;
                $(this).attr('data-rank', rank);
                $(this).find('.rank-display').text(rank);
            });
        }

        // Delete organization row
        $('#organizationPeriodList, #organizationAmountList').on('click', '.organization-delete', function() {
            const row = $(this).closest('.organization-period-row, .organization-amount-row');
            const listId = $(this).closest('#organizationPeriodList, #organizationAmountList').attr('id');

            row.remove();
            updateOrganizationRanks(listId);
        });

        $('#saveOrganizationConfig').on('click', function() {
            // Get weight values
            const periodWeight = parseFloat($('#organizationPeriodWeight').val());
            const amountWeight = parseFloat($('#organizationAmountWeight').val());

            // Collect period ranges with their ranks
            const periodRanges = [];
            $('#organizationPeriodList').children().each(function() {
                const min = parseFloat($(this).find('.period-min').val());
                const max = parseFloat($(this).find('.period-max').val());
                const rank = parseFloat($(this).attr('data-rank'));

                periodRanges.push({
                    min,
                    max,
                    rank
                });
            });

            // Collect amount ranges with their ranks
            const amountRanges = [];
            $('#organizationAmountList').children().each(function() {
                const min = parseFloat($(this).find('.amount-min').val());
                const max = parseFloat($(this).find('.amount-max').val());
                const rank = parseFloat($(this).attr('data-rank'));

                amountRanges.push({
                    min,
                    max,
                    rank
                });
            });

            // Validasi menyeluruh
            if (periodRanges.length > 0 && amountRanges.length > 0) {
                // Validasi range
                const isPeriodRangesValid = periodRanges.every(range =>
                    !isNaN(range.min) && !isNaN(range.max) && range.min < range.max
                );
                const isAmountRangesValid = amountRanges.every(range =>
                    !isNaN(range.min) && !isNaN(range.max) && range.min < range.max
                );

                // Validasi bobot
                const isWeightValid = !isNaN(periodWeight) &&
                    !isNaN(amountWeight) &&
                    periodWeight >= 0 &&
                    amountWeight >= 0 &&
                    periodWeight + amountWeight === 100;

                if (isPeriodRangesValid && isAmountRangesValid && isWeightValid) {
                    // Sort by rank (lowest first)
                    periodRanges.sort((a, b) => a.rank - b.rank);
                    amountRanges.sort((a, b) => a.rank - b.rank);

                    // Save the configuration
                    criteriaConfigs.organization.period = periodRanges;
                    criteriaConfigs.organization.amount = amountRanges;
                    criteriaConfigs.organization.weights = {
                        period: periodWeight,
                        amount: amountWeight
                    };
                    criteriaConfigs.organization.customized = true;

                    // Show the configuration badge
                    $('#config-badge-organization').addClass('active');

                    // Close the modal
                    $('#organizationConfigModal').modal('hide');

                    // Confirm to the user
                    Swal.fire({
                        icon: 'success',
                        title: 'Organization Settings Configured',
                        text: 'Your custom organization scoring configuration has been saved',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    // Pesan kesalahan yang spesifik
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Configuration',
                        html: `
                    ${!isPeriodRangesValid ? '- Invalid period ranges<br>' : ''}
                    ${!isAmountRangesValid ? '- Invalid amount ranges<br>' : ''}
                    ${!isWeightValid ? '- Weights must total 100%' : ''}
                `,
                        showConfirmButton: true
                    });
                }
            } else {
                // Pesan kesalahan jika tidak ada range
                Swal.fire({
                    icon: 'error',
                    title: 'Incomplete Configuration',
                    text: 'Please add at least one range for both period and amount',
                    showConfirmButton: true
                });
            }
        });




        // ----------------------- END CONFIGURATION -----------------------


        // Distribute percentages evenly among all active criteria
        function distributePercentages() {
            if (activeCriteria.length === 0) return;

            const evenPercentage = Math.floor(100 / activeCriteria.length);
            const remainder = 100 - (evenPercentage * activeCriteria.length);

            $criteriaContainer.find('.criteria-percentage input').val(evenPercentage);

            // Add remainder to the first criterion
            if (remainder > 0) {
                $criteriaContainer.find('.criteria-percentage input').first().val(evenPercentage + remainder);
            }

            updateTotal();
        }

        // Reset all criteria
        function resetCriteria() {
            activeCriteria = [];
            $criteriaContainer.empty();
            $availableCriteria.find('option').prop('disabled', false);
            $totalValueSpan.text('0.0');
        }

        // Update the total percentage
        function updateTotal() {
            let total = 0;
            $('.criteria-percentage input').each(function() {
                total += Number($(this).val()) || 0;
            });

            $('#totalValue').text(total.toFixed(1));

            // Make sure this value is visible
            $('#percentageTotal').show();

            // Better visual indication when total isn't 100%
            if (Math.abs(total - 100) > 0.1) {
                $('#percentageTotal').addClass('error');
                $('#percentageTotal').css('color', 'red');
                $('#calculateButton').prop('disabled', true);
            } else {
                $('#percentageTotal').removeClass('error');
                $('#percentageTotal').css('color', '');
                $('#calculateButton').prop('disabled', false);
            }
        }
        // Extend the calculate function to include all criteria configs
        $calculateBtn.on('click', function() {
            if (!$demandSelect.val()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Choose Labor Demand First!',
                    confirmButtonColor: '#3498db',
                    confirmButtonText: 'OK'
                }).then(() => {
                    $demandSelect.focus();
                });
                return;
            }

            if (activeCriteria.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Criteria Selected',
                    text: 'Please add at least one criterion',
                    confirmButtonColor: '#3498db',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Check if total is 100%
            let total = 0;
            $criteriaContainer.find('.criteria-percentage input').each(function() {
                total += Number($(this).val()) || 0;
            });

            if (Math.abs(total - 100) > 0.1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Weights',
                    text: 'Total percentage must be 100%',
                    confirmButtonColor: '#3498db',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Show confirmation dialog before proceeding
            Swal.fire({
                icon: 'question',
                title: 'Confirm Submission',
                text: 'Are you sure you want to submit? Please ensure you have saved the configuration for each selected criterion, even if you agree with the default settings.',
                showCancelButton: true,
                confirmButtonColor: '#3498db',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Submit',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    processCriteriaAndCalculate();
                }
            });
        });



        // Function to process criteria and calculate rankings
        function processCriteriaAndCalculate() {
            $calculateBtn.prop('disabled', true);
            $calculateBtn.html('<span class="spinner-border" role="status" aria-hidden="true"></span> Loading...');

            const formData = $form.serializeArray();
            const jsonData = {};
            $.each(formData, function(_, field) {
                jsonData[field.name] = field.value;
            });

            // Add custom configurations
            jsonData.criteria_configs = {};

            // Include all active criteria configs, whether customized or default
            activeCriteria.forEach(criterion => {
                switch (criterion) {
                    case 'age':
                        jsonData.criteria_configs.age = criteriaConfigs.age;
                        break;
                    case 'education':
                        jsonData.criteria_configs.education = criteriaConfigs.education;
                        break;

                    case 'distance':
                        jsonData.criteria_configs.distance = criteriaConfigs.distance;
                        break;
                    case 'expected_salary':
                        jsonData.criteria_configs.expected_salary = criteriaConfigs.expected_salary;
                        break;
                    case 'training':
                        jsonData.criteria_configs.training = criteriaConfigs.training;
                        break;
                    case 'experience_duration':
                        jsonData.criteria_configs.experience_duration = criteriaConfigs.experience_duration;
                        break;
                    case 'organization':
                        jsonData.criteria_configs.organization = criteriaConfigs.organization;
                        break;
                }
            });

            $.ajax({
                url: '/recruitment/weight_calculation/weight_calculate',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val(),
                    'Accept': 'application/json'
                },
                contentType: 'application/json',
                data: JSON.stringify(jsonData),
                success: function(data) {
                    if (data.success) {
                        if ($.fn.DataTable.isDataTable('#rankingTable')) {
                            $('#rankingTable').DataTable().clear().destroy();
                        }

                        $('#rankingResults').html(''); // Kosongkan isi tabel

                        displayRankings(data.rankings); // Render ulang

                        $('#rankingTable').DataTable(); // Inisialisasi ulang DataTable
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    let errorMessage = 'Server Error';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire('Error', errorMessage, 'error');
                },
                complete: function() {
                    $calculateBtn.prop('disabled', false);
                    $calculateBtn.html('Calculate Ranking');
                }
            });
        }

        function createProgressBar(score) {
            // Ensure score is a number
            const numScore = parseFloat(score) || 0;

            // Determine color class based on score
            let colorClass = 'bg-danger';
            if (numScore >= 70) colorClass = 'bg-success';
            else if (numScore >= 40) colorClass = 'bg-warning';

            return `
        <div class="progress my-1" style="height: 8px;" data-bs-toggle="tooltip" title="${numScore.toFixed(1)}%">
            <div class="progress-bar ${colorClass}" role="progressbar" 
                style="width: ${numScore}%" aria-valuenow="${numScore}" 
                aria-valuemin="0" aria-valuemax="100">
            </div>
        </div>
        <div class="text-end small">${numScore.toFixed(1)}%</div>
    `;
        }

        // Improved function to display rankings in the table
        function displayRankings(rankings) {
            let html = '';

            // If no rankings data, show empty message
            if (!rankings || rankings.length === 0) {
                html = '<tr><td colspan="12" class="text-center">No data available</td></tr>';
                $('#rankingResults').html(html);
                initializeDataTable();
                return;
            }

            // Loop through each ranking item
            $.each(rankings, function(index, item) {
                try {
                    // Skip if applicant data is missing
                    if (!item.applicant) {
                        html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>N/A</td>
                    <td>N/A</td>
                </tr>
            `;
                        return;
                    }

                    // --- Calculate & Format Data ---
                    // Age
                    const age = item.applicant.birth_date ?
                        moment().diff(moment(item.applicant.birth_date), 'years') :
                        null;
                    const ageDisplay = age !== null ? `${age} Year` : "N/A";
                    const ageScoreRaw = item.breakdown?.age || 0;
                    const ageScore = ageScoreRaw * 100;

                    // Expected Salary (formatted as Rp X.XXX.XXX)
                    const salary = item.applicant.expected_salary;
                    const salaryDisplay = salary ?
                        `Rp ${Number(salary).toLocaleString('id-ID')}` :
                        "N/A";
                    const salaryScoreRaw = item.breakdown?.expected_salary || 0;
                    const salaryScore = salaryScoreRaw * 100;

                    // Distance
                    const distance = item.applicant.distance;
                    const distanceDisplay = distance ? `${distance} KM` : "N/A";
                    const distanceScoreRaw = item.breakdown?.distance || 0;
                    const distanceScore = distanceScoreRaw * 100;

                    // Other breakdown scores - handle all possible missing data
                    const educationScoreRaw = item.breakdown?.education || 0;
                    const educationScore = educationScoreRaw * 100;

                    const experienceScoreRaw = item.breakdown?.experience_duration || 0;
                    const experienceScore = experienceScoreRaw * 100;

                    const organizationScoreRaw = item.breakdown?.organization || 0;
                    const organizationScore = organizationScoreRaw * 100;

                    const trainingScoreRaw = item.breakdown?.training || 0;
                    const trainingScore = trainingScoreRaw * 100;

                    // Total Score
                    const totalScore = item.score ? (item.score * 100) : 0;

                    // --- Generate HTML Row ---
                    html += `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${item.applicant.name || "N/A"}</strong></td>
                <td>
                    <div class="d-flex flex-column">
                        <span>${ageDisplay}</span>
                        ${createProgressBar(ageScore)}
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <span>${salaryDisplay}</span>
                        ${createProgressBar(salaryScore)}
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <span>${distanceDisplay}</span>
                        ${createProgressBar(distanceScore)}
                    </div>
                </td>
                <td>${createProgressBar(educationScore)}</td>
                <td>${createProgressBar(experienceScore)}</td>
                <td>${createProgressBar(organizationScore)}</td>
                <td>${createProgressBar(trainingScore)}</td>
                <td><strong>${createProgressBar(totalScore)}</strong></td>
                <td class="actions-cell">
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-info view-applicant-btn" 
                                data-id="${item.applicant.id}" 
                                data-name="${item.applicant.name}">
                            <i class="fas fa-eye me-1"></i> View
                        </button>
                        <button class="btn btn-sm btn-outline-primary schedule-interview" 
                                id="scheduleBtn-${item.applicant.id}" 
                                data-id="${item.applicant.id}" 
                                data-name="${item.applicant.name}">
                            <i class="fas fa-calendar-alt me-1"></i> Schedule
                        </button>
                    </div>
                </td>
            </tr>
        `;
                } catch (error) {
                    console.error("Error processing applicant:", error);
                    html += `
            <tr>
                <td>${index + 1}</td>
                <td colspan="11" class="text-danger">Error loading data: ${error.message}</td>
            </tr>
        `;
                }
            });

            // Update the table body
            $('#rankingResults').html(html);

            // Initialize DataTable
            initializeDataTable();

            // Initialize tooltips
            initTooltips();
        }
        /**
         * Function to initialize tooltip components after table renders
         */
        function initTooltips() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Fungsi untuk memformat konten modal
        function formatApplicantModalContent(response) {
            const applicant = response.applicant;

            // Helper functions for formatting data
            const formatData = (value) => value || '-';
            const formatDate = (dateString) => {
                if (!dateString) return '-';
                return new Date(dateString).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            };

            // Format profile photo
            const photoUrl = applicant.photo_profile_path ?
                `/storage/${applicant.photo_profile_path}` :
                '/storage/default_profile.png';

            // Format CV and other documents
            const cvUrl = applicant.cv_path ? `/storage/${applicant.cv_path}` : '#';
            const idCardUrl = applicant.ID_card_path ? `/storage/${applicant.ID_card_path}` : '#';
            const achievementUrl = applicant.achievement_path ? `/storage/${applicant.achievement_path}` : '#';

            return `
        <div class="row">
            <!-- Basic Information Column -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <img src="${photoUrl}" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        <h4>${formatData(applicant.name)}</h4>
                        <p class="text-muted">Applicant #${formatData(applicant.id)}</p>
                        
                        <div class="d-flex justify-content-center mb-3">
                            <span class="badge ${applicant.status_applicant === 'Approved' ? 'bg-success' : 
                                applicant.status_applicant === 'Declined' ? 'bg-danger' : 'bg-warning'}">
                                ${formatData(applicant.status_applicant)}
                            </span>
                        </div>
                        
                        <hr>
                        
                        <div class="text-start">
                            <p><strong><i class="fas fa-id-card me-2"></i>ID Number:</strong> ${formatData(applicant.ID_number)}</p>
                            <p><strong><i class="fas fa-birthday-cake me-2"></i>Birth:</strong> ${formatData(applicant.birth_place)}, ${formatDate(applicant.birth_date)}</p>
                            <p><strong><i class="fas fa-venus-mars me-2"></i>Gender:</strong> ${formatData(applicant.gender)}</p>
                            <p><strong><i class="fas fa-map-marker-alt me-2"></i>ID Address:</strong> ${formatData(applicant.ID_address)}</p>
                            <p><strong><i class="fas fa-home me-2"></i>Domicile:</strong> ${formatData(applicant.domicile_address)}</p>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-phone-alt me-2"></i>Contact
                    </div>
                    <div class="card-body">
                        <p><strong>Email:</strong> ${formatData(applicant.email)}</p>
                        <p><strong>Phone:</strong> ${formatData(applicant.phone_number)}</p>
                        <p><strong>Emergency Contact:</strong> ${formatData(applicant.emergency_contact)}</p>
                    </div>
                </div>
            </div>
            
            <!-- Detailed Information Column -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-info-circle me-2"></i>Additional Information
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Religion:</strong> ${formatData(applicant.religion)}</p>
                                <p><strong>Height:</strong> ${applicant.height ? applicant.height + ' cm' : '-'}</p>
                                <p><strong>Weight:</strong> ${applicant.weight ? applicant.weight + ' kg' : '-'}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Health Insurance:</strong> ${formatData(applicant.bpjs_health)}</p>
                                <p><strong>Employment Insurance:</strong> ${formatData(applicant.bpjs_employment)}</p>
                                <p><strong>Distance to Company:</strong> ${applicant.distance ? applicant.distance + ' km' : '-'}</p>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h5 class="mb-3">Expectations</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Expected Salary:</strong> ${applicant.expected_salary ? 'Rp ' + Number(applicant.expected_salary).toLocaleString('id-ID') : '-'}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Expected Facilities:</strong> ${formatData(applicant.expected_facility)}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Documents -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-file-alt me-2"></i>Documents
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <h6>ID Card</h6>
                                ${idCardUrl !== '#' ? 
                                    `<a href="${idCardUrl}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> View ID Card
                                    </a>` : 
                                    '<p class="text-muted">Not available</p>'}
                            </div>
                            <div class="col-md-4 mb-3">
                                <h6>CV</h6>
                                ${cvUrl !== '#' ? 
                                    `<a href="${cvUrl}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> View CV
                                    </a>` : 
                                    '<p class="text-muted">Not available</p>'}
                            </div>
                            <div class="col-md-4 mb-3">
                                <h6>Achievement</h6>
                                ${achievementUrl !== '#' ? 
                                    `<a href="${achievementUrl}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> View Achievement
                                    </a>` : 
                                    '<p class="text-muted">Not available</p>'}
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Detailed Information Tabs -->
                <div class="card">
                    <div class="card-header bg-warning">
                        <ul class="nav nav-tabs card-header-tabs" id="applicantDetailTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="education-tab" data-bs-toggle="tab" data-bs-target="#education" type="button" role="tab">
                                    Education
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="experience-tab" data-bs-toggle="tab" data-bs-target="#experience" type="button" role="tab">
                                    Experience
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="organization-tab" data-bs-toggle="tab" data-bs-target="#organization" type="button" role="tab">
                                    Organization
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="training-tab" data-bs-toggle="tab" data-bs-target="#training" type="button" role="tab">
                                    Training
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="language-tab" data-bs-toggle="tab" data-bs-target="#language" type="button" role="tab">
                                    Language
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="family-tab" data-bs-toggle="tab" data-bs-target="#family" type="button" role="tab">
                                    Family
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="applicantDetailTabContent">
                            <!-- Education Tab -->
                            <div class="tab-pane fade show active" id="education" role="tabpanel">
                                ${formatEducationTab(response.education)}
                            </div>
                            
                            <!-- Experience Tab -->
                            <div class="tab-pane fade" id="experience" role="tabpanel">
                                ${formatExperienceTab(response.experience)}
                            </div>
                            
                            <!-- Organization Tab -->
                            <div class="tab-pane fade" id="organization" role="tabpanel">
                                ${formatOrganizationTab(response.organization)}
                            </div>
                            
                            <!-- Training Tab -->
                            <div class="tab-pane fade" id="training" role="tabpanel">
                                ${formatTrainingTab(response.training)}
                            </div>
                            
                            <!-- Language Tab -->
                            <div class="tab-pane fade" id="language" role="tabpanel">
                                ${formatLanguageTab(response.language)}
                            </div>
                            
                            <!-- Family Tab -->
                            <div class="tab-pane fade" id="family" role="tabpanel">
                                ${formatFamilyTab(response.family)}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
        }

        // Function to format education tab
        function formatEducationTab(educationData) {
            if (!educationData || educationData.length === 0) {
                return '<p class="text-muted">No education data available</p>';
            }

            let html = '<div class="list-group">';

            educationData.sort((a, b) => new Date(b.end_education) - new Date(a.end_education))
                .forEach(edu => {
                    const transcriptBtn = edu.transcript_file_path ?
                        `<a href="/storage/${edu.transcript_file_path}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-file-pdf me-1"></i> Transcript
                </a>` : '';

                    html += `
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="mb-1">${formatData(edu.degree)}</h5>
                        <p class="mb-1"><strong>${formatData(edu.educational_place)}</strong></p>
                        <p class="mb-1">${formatData(edu.major)}</p>
                        <small class="text-muted">${formatDate(edu.start_education)} - ${formatDate(edu.end_education)}</small>
                    </div>
                    ${transcriptBtn}
                </div>
            </div>
        `;
                });

            html += '</div>';
            return html;
        }

        // Function to format experience tab
        function formatExperienceTab(experienceData) {
            if (!experienceData || experienceData.length === 0) {
                return '<p class="text-muted">No work experience data available</p>';
            }

            let html = '<div class="list-group">';

            experienceData.sort((a, b) => new Date(b.end_date) - new Date(a.end_date))
                .forEach(exp => {
                    html += `
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="mb-1">${formatData(exp.position)}</h5>
                        <p class="mb-1"><strong>${formatData(exp.company_name)}</strong></p>
                        <p class="mb-1">${formatData(exp.company_address)}</p>
                        <p class="mb-1">Salary: ${exp.salary ? 'Rp ' + Number(exp.salary).toLocaleString('id-ID') : '-'}</p>
                        <small class="text-muted">${formatDate(exp.working_start)} - ${formatDate(exp.working_end)}</small>
                    </div>
                </div>
                <div class="mt-2">
                    <p class="mb-1"><strong>Job Description:</strong></p>
                    <p>${formatData(exp.job_desc)}</p>
                </div>
            </div>
        `;
                });

            html += '</div>';
            return html;
        }

        // Function to format organization tab
        function formatOrganizationTab(organizationData) {
            if (!organizationData || organizationData.length === 0) {
                return '<p class="text-muted">No organization data available</p>';
            }

            let html = '<div class="list-group">';

            organizationData.sort((a, b) => new Date(b.end_date) - new Date(a.end_date))
                .forEach(org => {
                    html += `
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="mb-1">${formatData(org.organization_name)}</h5>
                        <p class="mb-1"><strong>Position: ${formatData(org.position)}</strong></p>
                        <p class="mb-1">Activity: ${formatData(org.activity_type)}</p>
                        <p class="mb-1">Location: ${formatData(org.city)}, ${formatData(org.province)}</p>
                        <small class="text-muted">${formatDate(org.start_date)} - ${formatDate(org.end_date)}</small>
                    </div>
                </div>
            </div>
        `;
                });

            html += '</div>';
            return html;
        }

        // Function to format training tab
        function formatTrainingTab(trainingData) {
            if (!trainingData || trainingData.length === 0) {
                return '<p class="text-muted">No training data available</p>';
            }

            let html = '<div class="list-group">';

            trainingData.sort((a, b) => new Date(b.end_date) - new Date(a.end_date))
                .forEach(training => {
                    html += `
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="mb-1">${formatData(training.training_name)}</h5>
                        <p class="mb-1">Location: ${formatData(training.training_city)}, ${formatData(training.training_province)}</p>
                        <small class="text-muted">${formatDate(training.start_date)} - ${formatDate(training.end_date)}</small>
                    </div>
                </div>
            </div>
        `;
                });

            html += '</div>';
            return html;
        }

        // Function to format language tab
        function formatLanguageTab(languageData) {
            if (!languageData || languageData.length === 0) {
                return '<p class="text-muted">No language data available</p>';
            }

            let html = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Language</th>
                        <th>Verbal Proficiency</th>
                        <th>Written Proficiency</th>
                    </tr>
                </thead>
                <tbody>
    `;

            languageData.forEach(lang => {
                html += `
            <tr>
                <td>${formatData(lang.language)}</td>
                <td>${formatData(lang.verbal)}</td>
                <td>${formatData(lang.written)}</td>
            </tr>
        `;
            });

            html += `
                </tbody>
            </table>
        </div>
    `;

            return html;
        }

        // Function to format family tab
        function formatFamilyTab(familyData) {
            if (!familyData || familyData.length === 0) {
                return '<p class="text-muted">No family data available</p>';
            }

            let html = '<div class="row">';

            familyData.forEach(member => {
                html += `
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">${formatData(member.name)}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">${formatData(member.relation)}</h6>
                    <p class="card-text">
                        <i class="fas fa-phone me-1"></i> ${formatData(member.phone_number)}<br>
                        <i class="fas fa-venus-mars me-1"></i> ${formatData(member.gender)}
                    </p>
                </div>
            </div>
        </div>
    `;
            });

            html += '</div>';
            return html;
        }

        // Helper function for formatting data
        function formatData(value) {
            return value || '-';
        }

        // Helper function for formatting dates
        function formatDate(dateString) {
            if (!dateString) return '-';
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        // Event handler for view button
        $(document).on('click', '.view-applicant-btn', function() {
            const applicantId = $(this).data('id');
            const applicantName = $(this).data('name');

            // Update modal title
            $('#applicantRankingModalLabel').html(`<i class="fas fa-user me-2"></i>Applicant Details - ${applicantName}`);

            // Show loading spinner
            $('#applicantModalContent').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading applicant data...</p>
        </div>
    `);

            // Show modal
            $('#applicantRankingModal').modal('show');

            // Get applicant data via AJAX
            $.ajax({
                url: `/recruitment/applicant/show/${applicantId}`,
                method: 'GET',
                success: function(response) {
                    // Format modal content
                    const modalContent = formatApplicantModalContent(response);
                    $('#applicantModalContent').html(modalContent);
                },
                error: function(xhr) {
                    $('#applicantModalContent').html(`
                <div class="alert alert-danger">
                    Failed to load applicant data. Please try again.
                </div>
            `);
                }
            });
        });



        // ----------------------- INTERVIEW -----------------------
        // Event listener for schedule interview buttons
        $(document).on('click', '.schedule-interview', function() {
            const applicantId = $(this).data('id');
            const applicantName = $(this).data('name');

            // Set values in the modal
            $('#applicantId').val(applicantId);
            $('#applicantName').val(applicantName);

            // Show the modal
            $('#scheduleInterviewModal').modal('show');
        });

        // Event listener for the schedule button in modal
        // Event listener for the schedule button in modal
        $('#saveInterviewBtn').click(function() {
            // Check if form is valid
            if (!$('#scheduleInterviewForm')[0].checkValidity()) {
                $('#scheduleInterviewForm').addClass('was-validated');
                return;
            }

            // Check if confirmation checkbox is checked
            if (!$('#confirmSchedule').is(':checked')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Confirmation Required',
                    text: 'Please confirm the interview schedule'
                });
                return;
            }

            // Show loading state
            $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
            $(this).prop('disabled', true);

            // Get form data
            const applicantId = $('#applicantId').val();
            const interviewDate = $('#interviewDate').val();
            const interviewNote = $('#interviewNote').val();

            // Send AJAX request
            $.ajax({
                url: `/recruitment/ahp-schedule-interview/${applicantId}`,
                type: 'POST',
                data: {
                    interview_date: interviewDate,
                    interview_note: interviewNote,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Hide modal
                    $('#scheduleInterviewModal').modal('hide');

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Interview scheduled successfully',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    // Disable schedule button for this applicant
                    $(`#scheduleBtn-${applicantId}`).prop('disabled', true).text('Scheduled').removeClass('btn-primary').addClass('btn-success');

                    // Reload data after a short delay
                    setTimeout(function() {
                        loadRankingResults(); // Assuming you have this function to reload the table
                    }, 1500);
                },
                error: function(xhr) {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Failed to schedule interview'
                    });

                    // Reset button state
                    $('#saveInterviewBtn').html('Schedule');
                    $('#saveInterviewBtn').prop('disabled', false);
                },
                complete: function() {
                    // Reset form
                    $('#scheduleInterviewForm')[0].reset();
                    $('#scheduleInterviewForm').removeClass('was-validated');
                }
            });
        });


    });
</script>
@endpush