@extends('layouts.app')
@section('content')
<div class="container mb-4 p-0 mx-auto">
    <div class="card shadow mb-5">
        <div class="card-header bg-gradient-primary text-white py-4">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="h3 mb-0 text-white"> <i class="fas fa-calculator me-2"></i> AHP Recommendation System</h1>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <!-- Progress Indicator -->
            <div class="steps-container mb-5">
                <div class="step-progress">
                    <div class="step active" id="step-1">
                        <div class="step-icon"><i class="fas fa-briefcase"></i></div>
                        <div class="step-label">Select Demand</div>
                    </div>
                    <div class="step" id="step-2">
                        <div class="step-icon"><i class="fas fa-list-ul"></i></div>
                        <div class="step-label">Select Criteria</div>
                    </div>
                    <div class="step" id="step-3">
                        <div class="step-icon"><i class="fas fa-sliders-h"></i></div>
                        <div class="step-label">Configure Sub-Criteria</div>
                    </div>
                    <div class="step" id="step-4">
                        <div class="step-icon"><i class="fas fa-balance-scale"></i></div>
                        <div class="step-label">Compare Main Criteria</div>
                    </div>
                    <div class="step" id="step-5">
                        <div class="step-icon"><i class="fas fa-chart-bar"></i></div>
                        <div class="step-label">View Results</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mb-4 p-0 mx-auto" id="container-body">
    <div class="card shadow mb-5">
        <div class="card-header bg-gradient-primary text-white py-4">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="h3 mb-0 text-white"> <i class="fas fa-tasks me-2"></i>AHP Process </h1>
                </div>
            </div>
        </div>

        <div class="card-body pt-3">
            <form id="ahpForm" onsubmit="return false;">
                @csrf
                <!-- Step 1: Select Demand -->
                <div class="step-content active" id="step-1-content">
                    <h5 class="card-title border-bottom pb-2 mb-3">Step 1: Select Job Demand</h5>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Please select a job demand to start the AHP process
                    </div>
                    <div class="mb-3">
                        <label for="demandId" class="form-label">Select Demand:</label>
                        <select name="demandId" id="demandId" class="form-select" required>
                            <option value="" disabled selected>-- Select Demand --</option>
                            @foreach($demands as $demand)
                            <option value="{{ $demand->id }}">{{ $demand->recruitment_demand_id }} - {{ $demand->positionRelation->position }} ({{ $demand->departmentRelation->department }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-primary next-step" data-next="2">
                            Next <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Manage Criteria -->
                <div class="step-content" id="step-2-content">
                    <h5 class="card-title border-bottom pb-2 mb-3">Step 2: Select Criteria</h5>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Select at least 3 criteria that will be used for applicant evaluation
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Selected Criteria:</label>
                        <div id="selectedCriteriaContainer" class="d-flex flex-wrap gap-2 mb-3 p-2 border rounded min-height-50"></div>
                        <button type="button" id="addCriteriaBtn" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add Criteria
                        </button>
                        <small class="text-muted ms-2">Minimum 3 criteria required</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary prev-step" data-prev="1">
                            <i class="fas fa-arrow-left me-1"></i> Previous
                        </button>
                        <button type="button" class="btn btn-primary next-step" data-next="3">
                            Next <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Configure Sub-Criteria -->
                <div class="step-content" id="step-3-content">
                    <h5 class="card-title border-bottom pb-2 mb-3">Step 3: Configure Sub-Criteria</h5>

                    <!-- Saaty Scale Table -->
                    <div class="card mb-4 shadow-sm border-0">
                        <div class="card-header bg-primary text-white d-flex align-items-center">
                            <i class="fas fa-balance-scale me-2"></i>
                            <h6 class="mb-0">Saaty Scale Reference <small class="fw-normal ms-1">(1–9)</small></h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="text-center">
                                            <th style="width: 15%">Intensity</th>
                                            <th style="width: 30%">Definition</th>
                                            <th style="width: 55%">Explanation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center"><span class="badge bg-secondary fs-6">1</span></td>
                                            <td>Equal importance</td>
                                            <td>Both options contribute equally to the objective.</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><span class="badge bg-primary fs-6">3</span></td>
                                            <td>Moderate importance</td>
                                            <td>Experience slightly favors one over the other.</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><span class="badge bg-info text-dark fs-6">5</span></td>
                                            <td>Strong importance</td>
                                            <td>Judgment strongly favors one alternative.</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><span class="badge bg-warning text-dark fs-6">7</span></td>
                                            <td>Very strong importance</td>
                                            <td>Demonstrated preference backed by practical experience.</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><span class="badge bg-danger fs-6">9</span></td>
                                            <td>Absolute importance</td>
                                            <td>One option is overwhelmingly more important than the other.</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><span class="badge bg-light text-dark fs-6">2, 4, 6, 8</span></td>
                                            <td>Intermediate values</td>
                                            <td>Used to express compromise between the judgments above.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> You must configure all criteria before proceeding to the next step
                    </div>

                    <div id="configurationStatus" class="mb-3">
                        <!-- Status indicators will be placed here -->
                    </div>

                    <div id="subCriteriaConfigBtns" class="d-flex flex-wrap gap-2 mb-3">
                        <!-- Sub-criteria config buttons will be generated here -->
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary prev-step" data-prev="2">
                            <i class="fas fa-arrow-left me-1"></i> Previous
                        </button>
                        <button type="button" class="btn btn-primary next-step" data-next="4" id="goToMainComparison" disabled>
                            Next <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 4: Main Criteria Comparison -->
                <div class="step-content" id="step-4-content">
                    <h5 class="card-title border-bottom pb-2 mb-3">Step 4: Compare Main Criteria</h5>

                    <div class="card mb-4 shadow-sm border-0">
                        <div class="card-header bg-primary text-white d-flex align-items-center">
                            <i class="fas fa-balance-scale me-2"></i>
                            <h6 class="mb-0">Saaty Scale Reference <small class="fw-normal ms-1">(1–9)</small></h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="text-center">
                                            <th style="width: 15%">Intensity</th>
                                            <th style="width: 30%">Definition</th>
                                            <th style="width: 55%">Explanation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center"><span class="badge bg-secondary fs-6">1</span></td>
                                            <td>Equal importance</td>
                                            <td>Both options contribute equally to the objective.</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><span class="badge bg-primary fs-6">3</span></td>
                                            <td>Moderate importance</td>
                                            <td>Experience slightly favors one over the other.</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><span class="badge bg-info text-dark fs-6">5</span></td>
                                            <td>Strong importance</td>
                                            <td>Judgment strongly favors one alternative.</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><span class="badge bg-warning text-dark fs-6">7</span></td>
                                            <td>Very strong importance</td>
                                            <td>Demonstrated preference backed by practical experience.</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><span class="badge bg-danger fs-6">9</span></td>
                                            <td>Absolute importance</td>
                                            <td>One option is overwhelmingly more important than the other.</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><span class="badge bg-light text-dark fs-6">2, 4, 6, 8</span></td>
                                            <td>Intermediate values</td>
                                            <td>Used to express compromise between the judgments above.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="mainCriteriaSliders" class="mb-3">
                        <!-- Main criteria sliders will be placed here -->
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary prev-step" data-prev="3">
                            <i class="fas fa-arrow-left me-1"></i> Previous
                        </button>
                        <button type="button" id="calculateBtn" class="btn btn-success">
                            <i class="fas fa-calculator"></i> Calculate Final Ranking
                        </button>
                    </div>
                </div>

                <!-- Step 5: Ranking Results (Integrated into the same card) -->
                <div class="step-content" id="step-5-content">
                    <h5 class="card-title border-bottom pb-2 mb-3">Step 5: Applicant Ranking Results</h5>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i> AHP calculation completed successfully! Below are the ranked applicants.
                    </div>

                    <div class="table-responsive">
                        <table id="rankingTable" class="table table-striped table-hover" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>Rank</th>
                                    <th>Name</th>
                                    <th>Age</th>
                                    <th>Salary</th>
                                    <th>Distance</th>
                                    <th>Education</th>
                                    <th>Experience</th>
                                    <th>Total Score</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="rankingResults">
                                <tr>
                                    <td colspan="9" class="text-center">Please complete the AHP process to view results</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-secondary prev-step" data-prev="4">
                            <i class="fas fa-arrow-left me-1"></i> Previous
                        </button>
                        <button type="button" id="startNewProcessBtn" class="btn btn-primary ms-2">
                            <i class="fas fa-redo me-1"></i> Start New Process
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Add Criteria Modal -->
<div class="modal fade" id="addCriteriaModal" tabindex="-1" aria-labelledby="addCriteriaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title" id="addCriteriaModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Add Criteria
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="form-group">
                    <label for="availableCriteria" class="mb-2">Select Criteria to Add:</label>
                    <select id="availableCriteria" class="form-select">
                        <option value="age" data-name="Age">Age</option>
                        <option value="expected_salary" data-name="Expected Salary">Expected Salary</option>
                        <option value="distance" data-name="Distance">Distance</option>
                        <option value="education" data-name="Education">Education</option>
                        <option value="experience_duration" data-name="Work Experience">Work Experience</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmAddCriteria">Add</button>
            </div>
        </div>
    </div>
</div>

<!-- Range Configuration Modal -->
<div class="modal fade" id="rangeConfigModal" tabindex="-1" aria-labelledby="rangeConfigModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title" id="rangeConfigModalLabel">
                    <i class="fas fa-sliders-h me-2"></i>Configure Ranges
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="rangeConfigContent"></div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    Cancel
                </button>
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

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>


<script>
    // Tambahkan ini di bagian akhir document ready
    $(document).ready(function() {

        // Core elements
        const $demandSelect = $('#demandId');
        const $selectedCriteriaContainer = $('#selectedCriteriaContainer');
        const $subCriteriaConfigBtns = $('#subCriteriaConfigBtns');
        const $configurationStatus = $('#configurationStatus');
        const $mainCriteriaSliders = $('#mainCriteriaSliders');
        const $calculateBtn = $('#calculateBtn');
        const $addCriteriaModal = $('#addCriteriaModal');
        const $rangeConfigModal = $('#rangeConfigModal');
        const $rangeConfigContent = $('#rangeConfigContent');
        const $saveRangeConfig = $('#saveRangeConfig');
        const $goToMainComparison = $('#goToMainComparison');
        const $rankingTable = $('#rankingTable').closest('.card'); // Get the parent container of the table
        const $startNewBtn = $('#startNewProcessBtn');

        // Default AHP Data Structure
        const defaultCriteria = {
            'age': 'Age',
            'distance': 'Distance',
            'education': 'Education'
        };


        // Animasi hover untuk tombol
        $('.btn').hover(
            function() {
                $(this).css('transform', 'translateY(-2px)');
            },
            function() {
                $(this).css('transform', 'translateY(0)');
            }
        );

        // Efek loading saat calculate
        $calculateBtn.on('click', function() {
            $(this).addClass('calculate-pulse');
        });

        // Hapus efek loading setelah selesai
        $(document).ajaxComplete(function() {
            $calculateBtn.removeClass('calculate-pulse');
        });

        // 3. Add Organization and Training to the AHP data structure
        let ahpData = {
            mainCriteria: {
                ...defaultCriteria
            },
            subCriteria: {
                age: {
                    ranges: [{
                            min: 0,
                            max: 20,
                            label: '0-20'
                        },
                        {
                            min: 21,
                            max: 25,
                            label: '21-25'
                        },
                        {
                            min: 26,
                            max: 999,
                            label: '26+'
                        }
                    ],
                    comparisons: {} // Will store pairwise comparisons
                },
                expected_salary: {
                    ranges: [{
                            min: 0,
                            max: 3000000,
                            label: '0-3jt'
                        },
                        {
                            min: 3000001,
                            max: 6000000,
                            label: '3-6jt'
                        },
                        {
                            min: 6000001,
                            max: 999999999,
                            label: '6jt+'
                        }
                    ],
                    comparisons: {}
                },
                distance: {
                    ranges: [{
                            min: 0,
                            max: 3,
                            label: '0-3'
                        },
                        {
                            min: 4,
                            max: 5,
                            label: '4-5'
                        },
                        {
                            min: 6,
                            max: 999,
                            label: '6+'
                        }
                    ],
                    comparisons: {}
                },
                education: {
                    levels: ['SMK', 'SMA', 'D3', 'S1', 'S2'],
                    comparisons: {}
                },
                experience_duration: {
                    periods: ['<1 year', '1-2 years', '3-5 years', '5+ years'],
                    comparisons: {}
                },
                organization: {
                    ranges: [{
                            min: 0,
                            max: 1,
                            label: '0-1'
                        },
                        {
                            min: 2,
                            max: 3,
                            label: '2-3'
                        },
                        {
                            min: 4,
                            max: 999,
                            label: '4-5'
                        },

                    ],
                    comparisons: {}
                },
                training: {
                    ranges: [{
                            min: 0,
                            max: 1,
                            label: '0-1'
                        },
                        {
                            min: 2,
                            max: 3,
                            label: '2-3'
                        },
                        {
                            min: 4,
                            max: 9999,
                            label: '4-5'
                        },

                    ],
                    comparisons: {}
                }
            },
            mainComparisons: {} // Will store main criteria comparisons
        };


        // Current config context
        let currentConfig = {
            criteria: null,
            type: null
        };

        // 2. Add Organization and Training criteria to the available criteria pool
        const allCriteria = {
            'age': 'Age',
            'expected_salary': 'Expected Salary',
            'distance': 'Distance',
            'education': 'Education',
            'experience_duration': 'Work Experience',
            'organization': 'Organization',
            'training': 'Training'
        };



        // Step Navigation
        function navigateToStep(stepNumber) {
            // Hide all step contents
            $('.step-content').removeClass('active');

            // Show the current step content
            $(`#step-${stepNumber}-content`).addClass('active');

            // Update step indicators
            $('.step').removeClass('active completed');
            for (let i = 1; i <= stepNumber; i++) {
                if (i < stepNumber) {
                    $(`#step-${i}`).addClass('completed');
                } else {
                    $(`#step-${i}`).addClass('active');
                }
            }

            // Initialize main criteria comparison if moving to step 4
            if (stepNumber === 4) {
                initializeMainCriteriaComparison();
            }

            // Show/hide ranking table based on step
            if (stepNumber === 5) {
                $rankingTable.show();
            }
            // else {
            //     $rankingTable.hide();
            // }
        }

        // Handle next/previous buttons
        $('.next-step').on('click', function() {
            const nextStep = $(this).data('next');
            navigateToStep(nextStep);
        });

        $('.prev-step').on('click', function() {
            const prevStep = $(this).data('prev');
            navigateToStep(prevStep);
        });

        // Modify the demand select change handler
        $demandSelect.on('change', function() {
            if ($(this).val()) {
                // Enable next button
                $(this).closest('.step-content').find('.next-step').prop('disabled', false);

                // Reset to default state
                ahpData.mainCriteria = {
                    'age': 'Age',
                    'distance': 'Distance',
                    'education': 'Education'
                };
                ahpData.mainComparisons = {};

                // Update UI
                updateSelectedCriteriaDisplay();
                updateSubCriteriaConfigButtons();
                updateConfigurationStatus();
            } else {
                // Disable next button if no demand selected
                $(this).closest('.step-content').find('.next-step').prop('disabled', true);
            }
        });

        // Add this to document ready to initially disable next button
        $('#step-1-content .next-step').prop('disabled', true);










        // 2. Fix the saveRangesFirst function to update range values before trying to validate
        function saveRangesFirst() {
            const criteriaKey = currentConfig.criteria;

            // First update all ranges from the UI inputs
            $('#rangeSliders .range-item').each(function() {
                const index = $(this).data('index');
                const min = parseInt($(this).find('.range-min').val()) || 0;
                const max = parseInt($(this).find('.range-max').val()) || 0;

                // Update the range values in the data structure
                if (ahpData.subCriteria[criteriaKey].ranges[index]) {
                    ahpData.subCriteria[criteriaKey].ranges[index].min = min;
                    ahpData.subCriteria[criteriaKey].ranges[index].max = max;
                }
            });

            // Then validate the ranges
            if (!validateRangesBeforeSave(criteriaKey)) {
                return false;
            }

            // Save range values with updated labels
            const updatedRanges = [];
            $('#rangeSliders .range-item').each(function() {
                const min = parseInt($(this).find('.range-min').val()) || 0;
                const max = parseInt($(this).find('.range-max').val()) || 0;
                let label;

                if (max === 999 || max === 999999999) {
                    label = `${min}+`;
                } else {
                    label = `${min}-${max}`;
                }

                updatedRanges.push({
                    min,
                    max,
                    label
                });
            });

            // Sort ranges by min value for storage
            updatedRanges.sort((a, b) => a.min - b.min);

            // Update the stored ranges
            ahpData.subCriteria[criteriaKey].ranges = updatedRanges;

            // Regenerate comparisons structure when ranges change
            regenerateRangeComparisons(criteriaKey);

            // Set ranges to readonly and show compare section
            setRangesReadonly(true);

            // Render the comparisons for the updated ranges
            renderRangeComparisons(updatedRanges);

            // Show configuration section and scroll to it
            $('#comparisonSection').removeClass('d-none');
            $('#comparisonSection')[0].scrollIntoView({
                behavior: 'smooth'
            });

            return true;
        }

        // New function to separate the logic of finding a gap and creating a range
        function findGapAndCreateRange(criteriaKey) {
            const ranges = ahpData.subCriteria[criteriaKey].ranges;
            const isOrganizationOrTraining = criteriaKey === 'organization' || criteriaKey === 'training';
            const maxLimit = isOrganizationOrTraining ? 7 : 999;
            const unlimitedValue = isOrganizationOrTraining ? 999 : 999999999;

            // Sort ranges by min value
            const sortedRanges = [...ranges].sort((a, b) => a.min - b.min);

            // Find gaps between consecutive ranges
            let bestGap = null;

            // Look for gaps between existing ranges
            for (let i = 0; i < sortedRanges.length - 1; i++) {
                const currentMax = sortedRanges[i].max;
                const nextMin = sortedRanges[i + 1].min;

                // Check if there's a gap (at least 1 unit to create a valid range)
                if (currentMax + 1 < nextMin) {
                    const gapMin = currentMax + 1;
                    const gapMax = nextMin - 1;
                    console.log(`Found gap between ranges: ${gapMin}-${gapMax}`);

                    bestGap = {
                        min: gapMin,
                        max: gapMax
                    };
                    break;
                }
            }

            // If no gap found between ranges, try to add after last range
            if (!bestGap && sortedRanges.length > 0) {
                const lastRange = sortedRanges[sortedRanges.length - 1];
                const newMin = lastRange.max + 1;
                let newMax = newMin + (isOrganizationOrTraining ? 2 : 5); // Default step

                // Adjust for max limit
                if (newMax > maxLimit) {
                    newMax = maxLimit;
                }

                if (newMin <= newMax) {
                    bestGap = {
                        min: newMin,
                        max: newMax
                    };
                    console.log("Adding after last range:", bestGap);
                }
            }

            // If still no gap and no ranges exist, create initial range
            if (!bestGap && sortedRanges.length === 0) {
                bestGap = {
                    min: 0,
                    max: Math.min(5, maxLimit)
                };
                console.log("Creating initial range:", bestGap);
            }

            if (bestGap) {
                const newRange = {
                    min: bestGap.min,
                    max: bestGap.max,
                    label: bestGap.max === maxLimit ? `${bestGap.min}+` : `${bestGap.min}-${bestGap.max}`
                };

                console.log("Adding new range:", newRange);

                // Add the new range to the data structure
                ahpData.subCriteria[criteriaKey].ranges.push(newRange);

                // Sort the ranges
                ahpData.subCriteria[criteriaKey].ranges.sort((a, b) => a.min - b.min);

                console.log("Updated ranges:", JSON.stringify(ahpData.subCriteria[criteriaKey].ranges));

                // Render immediately after adding - this is key to show the updated UI
                renderRanges(criteriaKey, ahpData.subCriteria[criteriaKey].ranges);
                return true;
            }

            console.log("Cannot add range - no space available");
            Swal.fire('Error', 'Cannot add more ranges - no space available between existing ranges', 'error');
            return false;
        }
        // Also make sure the button is properly shown/hidden in setRangesReadonly:
        function setRangesReadonly(readonly) {
            if (readonly) {
                // Disable all inputs and hide delete buttons
                $('#rangeSliders input').prop('readonly', true);
                $('#rangeSliders .range-delete').addClass('d-none');
                $('#addRangeBtn').addClass('d-none');
                $('#saveRangesBtn').addClass('d-none');
                $('#editRangesBtn').removeClass('d-none');
            } else {
                // Enable inputs and show buttons
                $('#rangeSliders input').prop('readonly', false);
                $('#rangeSliders .range-delete').removeClass('d-none');
                $('#addRangeBtn').removeClass('d-none');
                $('#saveRangesBtn').removeClass('d-none');
                $('#editRangesBtn').addClass('d-none');

                // Hide comparison section when editing ranges
                $('#comparisonSection').addClass('d-none');
            }
        }

        // 4. Add function to validate salary ranges without overlap and ensure proper step increments
        function validateSalaryRanges() {
            // Get all range items
            const ranges = [];
            let valid = true;

            $('#rangeSliders .range-item').each(function() {
                const min = parseInt($(this).find('.range-min').val()) || 0;
                const max = parseInt($(this).find('.range-max').val()) || 0;

                // Check if min and max are valid
                if (min >= max) {
                    valid = false;
                    Swal.fire('Error', 'Minimum value must be less than maximum value', 'error');
                    return false;
                }

                // For salary fields, check if values follow step increment of 100,000
                if (currentConfig.criteria === 'expected_salary') {
                    if (min % 100000 !== 0) {
                        valid = false;
                        Swal.fire('Error', 'Salary values must be in increments of 100,000', 'error');
                        return false;
                    }

                    if (max % 100000 !== 0 && max !== 999999999) {
                        valid = false;
                        Swal.fire('Error', 'Salary values must be in increments of 100,000', 'error');
                        return false;
                    }
                }

                ranges.push({
                    min,
                    max
                });
            });

            if (!valid) return false;

            // Sort ranges by min value but place unlimited range last
            ranges.sort((a, b) => {
                if (a.max === 999999999) return 1;
                if (b.max === 999999999) return -1;
                return a.min - b.min;
            });

            // Check for overlaps
            for (let i = 0; i < ranges.length - 1; i++) {
                // Skip checking overlap with the unlimited range
                if (ranges[i].max === 999999999) continue;

                if (ranges[i].max >= ranges[i + 1].min) {
                    valid = false;
                    Swal.fire('Error', 'Ranges cannot overlap', 'error');
                    return false;
                }
            }

            return valid;
        }

        function openRangeBasedComparisonModal(criteriaKey) {
            currentConfig.type = 'range';
            currentConfig.criteria = criteriaKey;
            const criteriaName = ahpData.mainCriteria[criteriaKey];
            const ranges = ahpData.subCriteria[criteriaKey].ranges;

            let content = `
            <h5 class="mb-4 fw-bold text-primary">
    <i class="fas fa-balance-scale me-2"></i>${criteriaName} Range Configuration</h5>
<div class="alert alert-info mb-3">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Step 1:</strong> Configure the ranges below (Max 7 ranges)
</div>
<div class="range-configuration">
    <div id="rangeSliders" class="mb-4"></div>
    <div class="d-flex gap-2">
        <button type="button" id="addRangeBtn" class="btn btn-sm btn-success">
            <i class="fas fa-plus"></i> Add Range
        </button>
        <button type="button" id="saveRangesBtn" class="btn btn-sm btn-primary">
            <i class="fas fa-save"></i> Save Ranges
        </button>
        <button type="button" id="editRangesBtn" class="btn btn-sm btn-warning d-none">
            <i class="fas fa-edit"></i> Edit Ranges
        </button>
    </div>
</div>

<div id="comparisonSection" class="d-none">
  <h5 class="mb-4 fw-bold text-primary">
    <i class="fas fa-balance-scale me-2"></i>Compare ${criteriaName} Ranges
  </h5>

  <div class="alert alert-info d-flex align-items-center">
    <i class="fas fa-info-circle me-2 fs-5"></i>
    <div>Choose how you want to compare the ranges</div>
  </div>

  <div class="row g-4">
    <div class="col-md-6">
      <div class="card h-100 shadow-sm border-0 hover-shadow">
        <div class="card-body text-center">
          <div class="mb-3">
            <i class="fas fa-sliders-h text-primary fs-3"></i>
          </div>
          <h5 class="card-title fw-semibold">Expert Mode</h5>
          <p class="card-text text-muted">Manual comparison via sliders (1–9)</p>
          <button id="expertModeBtn" class="btn btn-outline-primary px-4">Use Expert Mode</button>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card h-100 shadow-sm border-0 hover-shadow">
        <div class="card-body text-center">
          <div class="mb-3">
            <i class="fas fa-magic text-success fs-3"></i>
          </div>
          <h5 class="card-title fw-semibold">Easy Mode</h5>
          <p class="card-text text-muted">Simply rank the ranges by importance</p>
          <button id="easyModeBtn" class="btn btn-outline-success px-4">Use Easy Mode</button>
        </div>
      </div>
    </div>
  </div>

  <div id="comparisonContainer" class="mt-4"></div>
</div>
`;

            $rangeConfigContent.html(content);
            renderRanges(criteriaKey, ranges);

            if (ahpData.subCriteria[criteriaKey].comparisons &&
                Object.keys(ahpData.subCriteria[criteriaKey].comparisons).length > 0) {
                setRangesReadonly(true);
                $('#comparisonSection').removeClass('d-none');

                if (ahpData.subCriteria[criteriaKey].mode === 'easy') {
                    renderRangeEasyMode(ahpData.subCriteria[criteriaKey].ranges, ahpData.subCriteria[criteriaKey].ranking);
                } else {
                    renderRangeExpertMode(ahpData.subCriteria[criteriaKey].ranges, ahpData.subCriteria[criteriaKey].comparisons);
                }
            }

            $('#saveRangesBtn').on('click', function() {
                if (saveRangesFirst()) {
                    $('#comparisonSection').removeClass('d-none');
                    $('#comparisonSection')[0].scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });

            $('#editRangesBtn').off('click').on('click', function() {
                const key = currentConfig.criteria;
                if (ahpData.subCriteria[key].comparisons &&
                    Object.keys(ahpData.subCriteria[key].comparisons).length > 0) {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Editing ranges will clear existing comparisons. Continue?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, edit',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            ahpData.subCriteria[key].comparisons = {};
                            setRangesReadonly(false);
                            $('#comparisonSection').addClass('d-none');
                        }
                    });
                } else {
                    setRangesReadonly(false);
                }
            });

            $('#addRangeBtn').off('click').on('click', function() {
                if ($('#rangeSliders .range-item').length >= 7) {
                    Swal.fire('Error', 'Maximum 7 ranges allowed', 'error');
                    return;
                }
                updateRangesFromUI(criteriaKey);
                findGapAndCreateRange(criteriaKey);
            });

            $('#expertModeBtn').on('click', function() {
                renderRangeExpertMode(
                    ahpData.subCriteria[criteriaKey].ranges,
                    ahpData.subCriteria[criteriaKey].comparisons || {}
                );
                ahpData.subCriteria[criteriaKey].mode = 'expert';
            });

            $('#easyModeBtn').on('click', function() {
                renderRangeEasyMode(
                    ahpData.subCriteria[criteriaKey].ranges,
                    ahpData.subCriteria[criteriaKey].ranking
                );
                ahpData.subCriteria[criteriaKey].mode = 'easy';
            });

            $('#finishComparisonBtn').on('click', function() {
                const comparisons = {};
                $('.range-comparison-slider').each(function() {
                    const r1 = $(this).data('range1');
                    const r2 = $(this).data('range2');
                    const val = parseFloat($(this).val());
                    comparisons[r1] = comparisons[r1] || {};
                    comparisons[r2] = comparisons[r2] || {};
                    comparisons[r1][r2] = val;
                    comparisons[r2][r1] = 1 / val;
                });

                ahpData.subCriteria[criteriaKey].comparisons = comparisons;
                $rangeConfigModal.modal('hide');
                updateSubCriteriaConfigButtons();
                updateConfigurationStatus();
            });

            $rangeConfigModal.modal('show');
        }


        // Function to update range data from UI inputs
        function updateRangesFromUI(criteriaKey) {
            $('#rangeSliders .range-item').each(function() {
                const index = $(this).data('index');
                const min = parseInt($(this).find('.range-min').val()) || 0;
                const max = parseInt($(this).find('.range-max').val()) || 0;

                // Update the range in the data structure
                if (ahpData.subCriteria[criteriaKey].ranges[index]) {
                    ahpData.subCriteria[criteriaKey].ranges[index].min = min;
                    ahpData.subCriteria[criteriaKey].ranges[index].max = max;

                    // Update the label as well
                    if (max === 999 || max === 999999999) {
                        ahpData.subCriteria[criteriaKey].ranges[index].label = `${min}+`;
                    } else {
                        ahpData.subCriteria[criteriaKey].ranges[index].label = `${min}-${max}`;
                    }
                }
            });

            console.log("After UI update - Current ranges:", JSON.stringify(ahpData.subCriteria[criteriaKey].ranges));
        }

        // 3. Improved validateRangesBeforeSave function without step validation for salary
        function validateRangesBeforeSave(criteriaKey) {
            const ranges = [];
            let valid = true;
            const isOrganizationOrTraining = criteriaKey === 'organization' || criteriaKey === 'training';

            // Check if we have too many ranges
            const rangeCount = $('#rangeSliders .range-item').length;
            if (rangeCount > 7) {
                Swal.fire('Error', 'Maximum 7 ranges allowed', 'error');
                return false;
            }

            if (rangeCount < 3) {
                Swal.fire('Error', 'Minimum 3 ranges required', 'error');
                return false;
            }

            $('#rangeSliders .range-item').each(function() {
                const min = parseInt($(this).find('.range-min').val()) || 0;
                const max = parseInt($(this).find('.range-max').val()) || 0;

                // Check if min and max are valid
                if (min >= max) {
                    valid = false;
                    Swal.fire('Error', 'Minimum value must be less than maximum value', 'error');
                    return false;
                }

                // For organization/training fields, check max value limit of 7
                if (isOrganizationOrTraining) {
                    if (max > 7 && max !== 999) {
                        valid = false;
                        Swal.fire('Error', 'Maximum value cannot exceed 7 for this criteria', 'error');
                        return false;
                    }
                }

                ranges.push({
                    min,
                    max
                });
            });

            if (!valid) return false;

            // Sort ranges by min value 
            ranges.sort((a, b) => a.min - b.min);

            // Check for overlaps and gaps
            for (let i = 0; i < ranges.length - 1; i++) {
                // Skip checking if current range is unlimited
                if (ranges[i].max === 999 || ranges[i].max === 999999999) continue;

                // Check for overlap with next range
                if (ranges[i].max >= ranges[i + 1].min) {
                    valid = false;
                    Swal.fire('Error', `Overlap found between range ${i+1} and range ${i+2}`, 'error');
                    return false;
                }

                // Check for gap with next range
                if (ranges[i].max + 1 !== ranges[i + 1].min) {
                    valid = false;
                    Swal.fire('Error', `Gap found between range ${i+1} and range ${i+2}. Ranges must be consecutive.`, 'error');
                    return false;
                }
            }

            return valid;
        }
        // Modify the updateSelectedCriteriaDisplay function
        function updateSelectedCriteriaDisplay() {
            $selectedCriteriaContainer.empty();

            Object.entries(ahpData.mainCriteria).forEach(([key, value]) => {
                const canDelete = Object.keys(ahpData.mainCriteria).length > 3;

                $selectedCriteriaContainer.append(`
            <div class="badge bg-primary p-2 d-flex align-items-center" data-criteria="${key}">
                ${value} 
                ${canDelete ? `<button type="button" class="btn-close btn-close-white ms-2 remove-criteria" 
                        data-criteria="${key}" style="font-size: 0.5rem;"></button>` : ''}
            </div>
        `);
            });

            // Clear any old comparisons when criteria change
            const currentCriteria = Object.keys(ahpData.mainCriteria);

            // Clear main comparisons for removed criteria
            Object.keys(ahpData.mainComparisons).forEach(crit => {
                if (!currentCriteria.includes(crit)) {
                    delete ahpData.mainComparisons[crit];
                }
            });

            // Clear sub criteria comparisons for removed criteria
            Object.keys(ahpData.subCriteria).forEach(crit => {
                if (!currentCriteria.includes(crit)) {
                    ahpData.subCriteria[crit].comparisons = {};
                }
            });

            updateAvailableCriteriaInModal();
        }

        // Update configuration status indicators
        function updateConfigurationStatus() {
            $configurationStatus.empty();

            let html = '<div class="mb-3"><strong>Configuration Status:</strong></div>';
            html += '<div class="row row-cols-1 row-cols-md-3 g-3 mb-3">';

            let allConfigured = true;

            Object.entries(ahpData.mainCriteria).forEach(([key, value]) => {
                const isConfigured = ahpData.subCriteria[key].comparisons &&
                    Object.keys(ahpData.subCriteria[key].comparisons).length > 0;

                if (!isConfigured) allConfigured = false;

                html += `
                <div class="col">
                    <div class="card h-100 ${isConfigured ? 'border-success' : 'border-warning'}">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">${value}</h6>
                                <span class="badge ${isConfigured ? 'bg-success' : 'bg-warning'}">
                                    ${isConfigured ? 'Configured' : 'Not Configured'}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            });

            html += '</div>';

            $configurationStatus.html(html);

            // Update next button status
            $goToMainComparison.prop('disabled', !allConfigured);

            // Update UI message if all configured
            if (allConfigured) {
                $('.alert-warning').removeClass('alert-warning').addClass('alert-success')
                    .html('<i class="fas fa-check-circle me-2"></i> All criteria configured! You can now proceed to compare main criteria.');
            }
        }

        // Update sub-criteria config buttons
        function updateSubCriteriaConfigButtons() {
            $subCriteriaConfigBtns.empty();

            Object.entries(ahpData.mainCriteria).forEach(([key, value]) => {
                const isConfigured = ahpData.subCriteria[key].comparisons &&
                    Object.keys(ahpData.subCriteria[key].comparisons).length > 0;

                $subCriteriaConfigBtns.append(`
                <button type="button" class="btn ${isConfigured ? 'btn-outline-success' : 'btn-outline-primary'} configure-sub-criteria" 
                        data-criteria="${key}">
                    <i class="fas ${isConfigured ? 'fa-check-circle' : 'fa-cog'}"></i> Configure ${value}
                </button>
            `);
            });

            updateConfigurationStatus();
        }

        // Update available criteria in modal
        function updateAvailableCriteriaInModal() {
            const $availableCriteria = $('#availableCriteria');
            $availableCriteria.empty();

            Object.entries(allCriteria).forEach(([key, value]) => {
                if (!ahpData.mainCriteria[key]) {
                    $availableCriteria.append(`
                    <option value="${key}" data-name="${value}">${value}</option>
                `);
                }
            });
        }

        // Add Criteria Button Click
        $('#addCriteriaBtn').on('click', function() {
            updateAvailableCriteriaInModal();
            $addCriteriaModal.modal('show');
        });

        // Confirm Add Criteria
        $('#confirmAddCriteria').on('click', function() {
            const $selected = $('#availableCriteria option:selected');
            const criteriaKey = $selected.val();
            const criteriaName = $selected.data('name');

            if (criteriaKey && !ahpData.mainCriteria[criteriaKey]) {
                ahpData.mainCriteria[criteriaKey] = criteriaName;
                updateSelectedCriteriaDisplay();
                updateSubCriteriaConfigButtons();
                $addCriteriaModal.modal('hide');
            }
        });

        // Also modify the remove-criteria click handler
        $(document).on('click', '.remove-criteria', function(e) {
            e.stopPropagation();

            const criteriaKey = $(this).data('criteria');

            // Ensure minimum 3 criteria
            if (Object.keys(ahpData.mainCriteria).length > 3) {
                delete ahpData.mainCriteria[criteriaKey];

                // Also remove from comparisons
                if (ahpData.mainComparisons[criteriaKey]) {
                    delete ahpData.mainComparisons[criteriaKey];
                }

                // Remove this criteria from other criteria's comparisons
                Object.keys(ahpData.mainComparisons).forEach(key => {
                    if (ahpData.mainComparisons[key][criteriaKey]) {
                        delete ahpData.mainComparisons[key][criteriaKey];
                    }
                });

                // Clear sub criteria comparisons
                if (ahpData.subCriteria[criteriaKey]) {
                    ahpData.subCriteria[criteriaKey].comparisons = {};
                }

                updateSelectedCriteriaDisplay();
                updateSubCriteriaConfigButtons();

                // Reset main criteria comparison as criteria have changed
                ahpData.mainComparisons = {};
            }
        });

        // Set all comparison values to 1 (neutral) — cleaned up default logic
        function setDefaultComparisonValues(criteriaKey, type) {
            const comparisons = {};

            if (type === 'range') {
                const ranges = ahpData.subCriteria[criteriaKey].ranges;
                for (let i = 0; i < ranges.length; i++) {
                    for (let j = i + 1; j < ranges.length; j++) {
                        const r1 = ranges[i];
                        const r2 = ranges[j];

                        const label1 = r1.label || `${r1.min}-${r1.max === 999 || r1.max === 999999999 ? '+' : r1.max}`;
                        const label2 = r2.label || `${r2.min}-${r2.max === 999 || r2.max === 999999999 ? '+' : r2.max}`;

                        if (!comparisons[label1]) comparisons[label1] = {};
                        if (!comparisons[label2]) comparisons[label2] = {};

                        comparisons[label1][label2] = 1;
                        comparisons[label2][label1] = 1;
                    }
                }
                ahpData.subCriteria[criteriaKey].comparisons = comparisons;
            } else if (type === 'education') {
                const levels = ahpData.subCriteria.education.levels;
                for (let i = 0; i < levels.length; i++) {
                    for (let j = i + 1; j < levels.length; j++) {
                        const level1 = levels[i];
                        const level2 = levels[j];

                        if (!comparisons[level1]) comparisons[level1] = {};
                        if (!comparisons[level2]) comparisons[level2] = {};

                        comparisons[level1][level2] = 1;
                        comparisons[level2][level1] = 1;
                    }
                }
                ahpData.subCriteria.education.comparisons = comparisons;
            } else if (type === 'experience') {
                const periods = ahpData.subCriteria.experience_duration.periods;
                for (let i = 0; i < periods.length; i++) {
                    for (let j = i + 1; j < periods.length; j++) {
                        const p1 = periods[i];
                        const p2 = periods[j];

                        if (!comparisons[p1]) comparisons[p1] = {};
                        if (!comparisons[p2]) comparisons[p2] = {};

                        comparisons[p1][p2] = 1;
                        comparisons[p2][p1] = 1;
                    }
                }
                ahpData.subCriteria.experience_duration.comparisons = comparisons;
            }
        }

        // 7. Update the configure sub-criteria button handler to include the new criteria
        $(document).on('click', '.configure-sub-criteria', function() {
            const criteriaKey = $(this).data('criteria');
            currentConfig.criteria = criteriaKey;

            if (criteriaKey === 'education') {
                openEducationComparisonModal();
            } else if (criteriaKey === 'experience_duration') {
                openExperienceComparisonModal();
            } else if (criteriaKey === 'organization' || criteriaKey === 'training') {
                openRangeBasedComparisonModal(criteriaKey);
            } else {
                openRangeConfigModal(criteriaKey);
            }

            // Set all to neutral (1) if no comparisons exist
            const comp = ahpData.subCriteria[criteriaKey].comparisons;
            if (!comp || Object.keys(comp).length === 0) {
                let type = 'range';
                if (criteriaKey === 'education') type = 'education';
                if (criteriaKey === 'experience_duration') type = 'experience';
                setDefaultComparisonValues(criteriaKey, type);
            }
        });


        // Modified openRangeConfigModal function to include mode selection
        function openRangeConfigModal(criteriaKey) {
            currentConfig.type = 'range';
            currentConfig.criteria = criteriaKey;
            const criteriaName = ahpData.mainCriteria[criteriaKey];
            const ranges = ahpData.subCriteria[criteriaKey].ranges;

            let content = `
            <h5 class="mb-4 fw-bold text-primary">
    <i class="fas fa-balance-scale me-2"></i> Range Configuration</h5>
<div class="alert alert-info mb-3">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Step 1:</strong> Configure the ranges below, then click "Save Ranges" button
</div>
<div class="range-configuration">
    <div id="rangeSliders" class="mb-4"></div>
    <div class="d-flex gap-2">
        <button type="button" id="addRangeBtn" class="btn btn-sm btn-success">
            <i class="fas fa-plus"></i> Add Range
        </button>
        <button type="button" id="saveRangesBtn" class="btn btn-sm btn-primary">
            <i class="fas fa-save"></i> Save Ranges
        </button>
        <button type="button" id="editRangesBtn" class="btn btn-sm btn-warning d-none">
            <i class="fas fa-edit"></i> Edit Ranges
        </button>
    </div>
</div>

<div id="comparisonSection" class="d-none">
  <h5 class="mb-4 fw-bold text-primary">
    <i class="fas fa-balance-scale me-2"></i>Compare ${criteriaName} Ranges
  </h5>

  <div class="alert alert-info d-flex align-items-center">
    <i class="fas fa-info-circle me-2 fs-5"></i>
    <div>Choose how you want to compare the ranges</div>
  </div>

  <div class="row g-4">
    <!-- Expert Mode Card -->
    <div class="col-md-6">
      <div class="card h-100 shadow-sm border-0 hover-shadow">
        <div class="card-body text-center">
          <div class="mb-3">
            <i class="fas fa-sliders-h text-primary fs-3"></i>
          </div>
          <h5 class="card-title fw-semibold">Expert Mode</h5>
          <p class="card-text text-muted">Manual comparison via sliders (1–9)</p>
          <button id="expertModeBtn" class="btn btn-outline-primary px-4">Use Expert Mode</button>
        </div>
      </div>
    </div>

    <!-- Easy Mode Card -->
    <div class="col-md-6">
      <div class="card h-100 shadow-sm border-0 hover-shadow">
        <div class="card-body text-center">
          <div class="mb-3">
            <i class="fas fa-magic text-success fs-3"></i>
          </div>
          <h5 class="card-title fw-semibold">Easy Mode</h5>
          <p class="card-text text-muted">Simply rank the ranges by importance</p>
          <button id="easyModeBtn" class="btn btn-outline-success px-4">Use Easy Mode</button>
        </div>
      </div>
    </div>
  </div>

  <div id="comparisonContainer" class="mt-4"></div>
</div>



`;

            $rangeConfigContent.html(content);
            renderRanges(criteriaKey, ranges);

            if (ahpData.subCriteria[criteriaKey].comparisons &&
                Object.keys(ahpData.subCriteria[criteriaKey].comparisons).length > 0) {
                setRangesReadonly(true);
                $('#comparisonSection').removeClass('d-none');

                // Show the appropriate comparison mode based on what was saved
                if (ahpData.subCriteria[criteriaKey].mode === 'easy') {
                    renderRangeEasyMode(ahpData.subCriteria[criteriaKey].ranges, ahpData.subCriteria[criteriaKey].ranking);
                } else {
                    renderRangeExpertMode(ahpData.subCriteria[criteriaKey].ranges, ahpData.subCriteria[criteriaKey].comparisons);
                }
            }

            // Event handler for save ranges
            $('#saveRangesBtn').on('click', function() {
                if (saveRangesFirst()) {
                    // After saving ranges successfully, show comparison section
                    $('#comparisonSection').removeClass('d-none');
                    $('#comparisonSection')[0].scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });

            // In the openRangeConfigModal function, modify the editRangesBtn handler like this:
            $('#editRangesBtn').off('click').on('click', function() {
                const criteriaKey = currentConfig.criteria;
                if (ahpData.subCriteria[criteriaKey].comparisons &&
                    Object.keys(ahpData.subCriteria[criteriaKey].comparisons).length > 0) {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Editing ranges will clear all your existing comparisons. Are you sure?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, edit anyway',
                        cancelButtonText: 'No, keep my comparisons'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            ahpData.subCriteria[criteriaKey].comparisons = {};
                            setRangesReadonly(false);
                            $('#comparisonSection').addClass('d-none');
                        }
                    });
                } else {
                    setRangesReadonly(false);
                }
            });

            // Event handlers for mode selection
            $('#expertModeBtn').on('click', function() {
                renderRangeExpertMode(ahpData.subCriteria[criteriaKey].ranges, ahpData.subCriteria[criteriaKey].comparisons || {});
            });

            $('#easyModeBtn').on('click', function() {
                renderRangeEasyMode(ahpData.subCriteria[criteriaKey].ranges, ahpData.subCriteria[criteriaKey].ranking);
            });

            // Event handler untuk finish comparison
            $('#finishComparisonBtn').on('click', function() {
                // Simpan comparisons
                const comparisons = {};
                $('.range-comparison-slider').each(function() {
                    const range1 = $(this).data('range1');
                    const range2 = $(this).data('range2');
                    const value = parseInt($(this).val());

                    if (!comparisons[range1]) comparisons[range1] = {};
                    if (!comparisons[range2]) comparisons[range2] = {};

                    comparisons[range1][range2] = value;
                    comparisons[range2][range1] = 1 / value;
                });

                ahpData.subCriteria[criteriaKey].comparisons = comparisons;
                $rangeConfigModal.modal('hide');
                updateSubCriteriaConfigButtons();
                updateConfigurationStatus();
            });



            $('#addRangeBtn').off('click').on('click', function() {
                if ($('#rangeSliders .range-item').length >= 7) {
                    Swal.fire('Error', 'Maximum 7 ranges allowed', 'error');
                    return;
                }
                updateRangesFromUI(criteriaKey);
                findGapAndCreateRange(criteriaKey);
            });

            $rangeConfigModal.modal('show');
        }

        // New function to render expert mode for ranges
        function renderRangeExpertMode(ranges, comparisons) {
            $('#comparisonContainer').html(`
            <div class="alert alert-info d-flex align-items-start gap-2">
    <i class="fas fa-info-circle mt-1"></i>
    <div>
        <strong>Note:</strong> <br>The slider is interpreted from <strong>left to right</strong>.
        For example, a value of <strong>2</strong> means that the <strong>left label</strong> is slightly more important than the right.
        <br>
        To reverse the comparison direction, click the 
        <i class="fa-solid fa-right-left text-secondary mx-1"></i> icon.
    </div>
</div>

        <div id="rangeComparisonSliders"></div>
        <div class="mt-4 text-start">
            <button type="button" id="saveRangeComparison" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Range Comparison
            </button>
        </div>
    `);

            renderRangeComparisons(ranges);

            // Initialize sliders with existing comparison values if available
            if (comparisons && Object.keys(comparisons).length > 0) {
                $('.range-comparison-slider').each(function() {
                    const range1 = $(this).data('range1');
                    const range2 = $(this).data('range2');

                    if (comparisons[range1] && comparisons[range1][range2] !== undefined) {
                        $(this).val(comparisons[range1][range2]);
                    } else if (comparisons[range2] && comparisons[range2][range1] !== undefined) {
                        $(this).val(1 / comparisons[range2][range1]);
                    }
                });
            }

            // Save button handler
            $('#saveRangeComparison').on('click', function() {
                const comparisons = {};
                $('.range-comparison-slider').each(function() {
                    const range1 = $(this).data('range1');
                    const range2 = $(this).data('range2');
                    const value = parseFloat($(this).val());

                    comparisons[range1] = comparisons[range1] || {};
                    comparisons[range2] = comparisons[range2] || {};
                    comparisons[range1][range2] = value;
                    comparisons[range2][range1] = 1 / value;
                });

                ahpData.subCriteria[currentConfig.criteria].comparisons = comparisons;
                ahpData.subCriteria[currentConfig.criteria].mode = 'expert'; // Mark as expert mode
                $rangeConfigModal.modal('hide');
                updateSubCriteriaConfigButtons();
                updateConfigurationStatus();
            });
        }

        // New function to render easy mode for ranges
        function renderRangeEasyMode(ranges, existingRanking) {
            // Sort ranges by min value to get default order
            const sortedRanges = [...ranges].sort((a, b) => a.min - b.min);

            // Get range labels
            const rangeLabels = sortedRanges.map(range =>
                range.max === 999 || range.max === 999999999 ?
                `${range.min}+` :
                `${range.min}-${range.max}`
            );

            let orderedRanges;
            if (existingRanking && existingRanking.length > 0) {
                // Use existing ranking if available
                orderedRanges = existingRanking;

                // Add any new ranges that weren't in the existing ranking
                rangeLabels.forEach(label => {
                    if (!orderedRanges.includes(label)) {
                        orderedRanges.push(label);
                    }
                });
            } else {
                // Default order is sorted by min value
                orderedRanges = rangeLabels;
            }

            // Generate sortable list with proper ordering
            let rangeItems = '';
            orderedRanges.forEach((label, index) => {
                rangeItems += `
            <div class="list-group-item range-rank-item" data-range="${label}" data-index="${index}">
                <div class="d-flex align-items-center">
                    <span class="handle me-3">
                        <i class="fas fa-grip-vertical"></i>
                    </span>
                    <span class="rank-number me-3 fw-bold text-primary">${index + 1}.</span>
                    <span class="range-name">${label}</span>
                </div>
            </div>
        `;
            });

            $('#comparisonContainer').html(`
        <h5 class="mb-3 mt-4 border-top pt-3">Easy Mode: Rank Ranges by Importance</h5>
        <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Instructions:</strong> Drag to rank the ranges from most to least important. The system will automatically generate comparison values.
        </div>
        <div id="rangeRankingList" class="list-group mb-4">
            ${rangeItems}
        </div>
        <button id="saveEasyModeBtn" class="btn btn-success">
            <i class="fas fa-save me-2"></i> Save Ranking
        </button>
    `);

            // Initialize SortableJS
            const sortableList = document.getElementById('rangeRankingList');
            const sortable = Sortable.create(sortableList, {
                animation: 150,
                handle: '.handle',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd: function(evt) {
                    updateRankNumbers();
                }
            });

            // Function to update rank numbers
            function updateRankNumbers() {
                const items = document.querySelectorAll('#rangeRankingList .range-rank-item');
                items.forEach((item, index) => {
                    const rankNumber = item.querySelector('.rank-number');
                    rankNumber.textContent = (index + 1) + '.';
                    item.setAttribute('data-index', index);
                });
            }

            // Save button handler
            $('#saveEasyModeBtn').on('click', function() {
                const rankedRanges = [];
                $('#rangeRankingList .range-rank-item').each(function() {
                    rankedRanges.push($(this).data('range'));
                });

                // Generate comparison matrix based on ranking
                const comparisons = generateRangeComparisonFromRanking(rankedRanges);

                // Save easy mode comparisons
                ahpData.subCriteria[currentConfig.criteria].comparisons = comparisons;
                ahpData.subCriteria[currentConfig.criteria].mode = 'easy'; // Mark as easy mode
                ahpData.subCriteria[currentConfig.criteria].ranking = rankedRanges; // Save the ranking

                Swal.fire({
                    title: 'Ranking Saved!',
                    text: 'Ranges have been ranked and comparison values generated automatically.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });

                $rangeConfigModal.modal('hide');
                updateSubCriteriaConfigButtons();
                updateConfigurationStatus();
            });
        }

        // New function to generate comparisons from ranking for ranges
        function generateRangeComparisonFromRanking(rankedRanges) {
            const comparisons = {};
            const n = rankedRanges.length;

            // Initialize comparison matrix
            rankedRanges.forEach(range => {
                comparisons[range] = {};
            });

            // Generate comparison values based on ranking
            // Using a progressive scale that ensures consistency
            for (let i = 0; i < n; i++) {
                for (let j = 0; j < n; j++) {
                    if (i === j) {
                        comparisons[rankedRanges[i]][rankedRanges[j]] = 1;
                    } else if (i < j) {
                        // Higher ranked (lower index) is more important
                        // Use a moderate scale to avoid extreme values
                        const rankDiff = j - i;
                        let value;

                        switch (rankDiff) {
                            case 1:
                                value = 2; // Slightly more important
                                break;
                            case 2:
                                value = 3; // Moderately more important
                                break;
                            case 3:
                                value = 5; // Strongly more important
                                break;
                            case 4:
                                value = 7; // Very strongly more important
                                break;
                            default:
                                value = Math.min(9, 2 * rankDiff); // Cap at 9
                        }

                        comparisons[rankedRanges[i]][rankedRanges[j]] = value;
                        comparisons[rankedRanges[j]][rankedRanges[i]] = 1 / value;
                    }
                }
            }

            return comparisons;
        }


        // 5. Modified renderRanges function to correctly set data-index attribute
        function renderRanges(criteriaKey, ranges) {
            const isFixedBoundaries = criteriaKey === 'age' || criteriaKey === 'expected_salary' || criteriaKey === 'distance';
            const isOrganizationOrTraining = criteriaKey === 'organization' || criteriaKey === 'training';

            $('#rangeSliders').empty();

            // Sort ranges by min value
            let sortedRanges = [...ranges].sort((a, b) => {
                // First sort by min value
                if (a.min !== b.min) {
                    return a.min - b.min;
                }

                // For same min values, push unlimited range to last position
                if (a.max === 999 || a.max === 999999999) return 1;
                if (b.max === 999 || b.max === 999999999) return -1;

                // Otherwise sort by max value
                return a.max - b.max;
            });

            // Update the sorted order back to the data structure
            ahpData.subCriteria[criteriaKey].ranges = sortedRanges;

            sortedRanges.forEach((range, index) => {
                // Check if this is a boundary range (0 or highest)
                const isMinBoundary = range.min === 0;
                const isMaxBoundary = range.max === 999 || range.max === 999999999;

                // Only allow deletion if: 
                // 1. There are more than 3 ranges total
                // 2. This range is not a boundary range in a criteria that requires fixed boundaries
                const canDelete = ranges.length > 3 && !(isFixedBoundaries && (isMinBoundary || isMaxBoundary));

                // Special formatting for "unlimited" max values
                let displayMax;
                if (isMaxBoundary) {
                    if (criteriaKey === 'expected_salary') {
                        displayMax = '999,999,999+ (Unlimited)';
                    } else if (isOrganizationOrTraining) {
                        displayMax = '7+ (Unlimited)';
                    } else {
                        displayMax = '999+ (Unlimited)';
                    }
                } else {
                    displayMax = range.max;
                }

                $('#rangeSliders').append(`
        <div class="range-item mb-3 p-3 border rounded bg-light" data-index="${index}" data-range-id="${index}">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <div class="form-text text-center fw-bold">Range ${index + 1}</div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">Min</span>
                        <input type="number" class="form-control range-min" 
                            value="${range.min}" ${isFixedBoundaries && isMinBoundary ? 'readonly' : ''}
                            step="1"
                            data-original-value="${range.min}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">Max</span>
                        <input type="number" class="form-control range-max" 
                            value="${range.max}" ${isFixedBoundaries && isMaxBoundary ? 'readonly' : ''}
                            step="1"
                            data-original-value="${range.max}"
                            title="${isMaxBoundary ? 'This represents an unlimited upper bound' : ''}">
                    </div>
                    ${isMaxBoundary ? '<small class="text-muted">Represents unlimited</small>' : ''}
                </div>
                <div class="col-md-2 text-center">
                    <button type="button" class="btn btn-sm btn-danger range-delete" ${!canDelete ? 'disabled' : ''}>
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    `);
            });

            // Re-render comparisons if they exist
            if (ahpData.subCriteria[criteriaKey].comparisons &&
                Object.keys(ahpData.subCriteria[criteriaKey].comparisons).length > 0) {
                renderRangeComparisons(sortedRanges);
            }

            // Add input validation handlers
            setupRangeInputValidation(criteriaKey);

            // Add range deletion handlers
            setupRangeDeletionHandlers();
        }

        // 6. Modified setupRangeInputValidation to update the data structure when ranges change
        function setupRangeInputValidation(criteriaKey) {
            const isOrganizationOrTraining = criteriaKey === 'organization' || criteriaKey === 'training';

            // For all criteria, ensure min < max within a range
            $('.range-min').off('change').on('change', function() {
                const min = parseInt($(this).val()) || 0;
                const maxInput = $(this).closest('.range-item').find('.range-max');
                const max = parseInt(maxInput.val()) || 0;
                const rangeIndex = $(this).closest('.range-item').data('index');

                if (min >= max) {
                    // Reset to original value and show error
                    $(this).val($(this).data('original-value'));
                    Swal.fire('Error', 'Minimum value must be less than maximum value', 'error');
                } else {
                    // Update the original value data attribute
                    $(this).data('original-value', min);

                    // Update the actual range data in the data structure
                    if (ahpData.subCriteria[criteriaKey].ranges[rangeIndex]) {
                        ahpData.subCriteria[criteriaKey].ranges[rangeIndex].min = min;
                        // Also update the label
                        if (ahpData.subCriteria[criteriaKey].ranges[rangeIndex].max === 999 ||
                            ahpData.subCriteria[criteriaKey].ranges[rangeIndex].max === 999999999) {
                            ahpData.subCriteria[criteriaKey].ranges[rangeIndex].label = `${min}+`;
                        } else {
                            ahpData.subCriteria[criteriaKey].ranges[rangeIndex].label =
                                `${min}-${ahpData.subCriteria[criteriaKey].ranges[rangeIndex].max}`;
                        }
                    }
                }
            });

            $('.range-max').off('change').on('change', function() {
                const max = parseInt($(this).val()) || 0;
                const minInput = $(this).closest('.range-item').find('.range-min');
                const min = parseInt(minInput.val()) || 0;
                const rangeIndex = $(this).closest('.range-item').data('index');

                // Special case for organization/training with max value limit of 7
                if (isOrganizationOrTraining && max > 7 && max !== 999) {
                    $(this).val($(this).data('original-value'));
                    Swal.fire('Error', 'Maximum value cannot exceed 7 for this criteria', 'error');
                    return;
                }

                if (max <= min) {
                    // Reset to original value and show error
                    $(this).val($(this).data('original-value'));
                    Swal.fire('Error', 'Maximum value must be greater than minimum value', 'error');
                } else {
                    // Update the original value data attribute
                    $(this).data('original-value', max);

                    // Update the actual range data in the data structure
                    if (ahpData.subCriteria[criteriaKey].ranges[rangeIndex]) {
                        ahpData.subCriteria[criteriaKey].ranges[rangeIndex].max = max;
                        // Also update the label
                        if (max === 999 || max === 999999999) {
                            ahpData.subCriteria[criteriaKey].ranges[rangeIndex].label = `${min}+`;
                        } else {
                            ahpData.subCriteria[criteriaKey].ranges[rangeIndex].label = `${min}-${max}`;
                        }
                    }
                }
            });
        }

        // 7. Add function to handle range deletion
        function setupRangeDeletionHandlers() {
            $('.range-delete').off('click').on('click', function() {
                const criteriaKey = currentConfig.criteria;
                const ranges = ahpData.subCriteria[criteriaKey].ranges;
                const rangeIndex = $(this).closest('.range-item').data('index');

                // Check if we have the minimum required ranges
                if (ranges.length <= 3) {
                    Swal.fire('Error', 'Minimum 3 ranges required', 'error');
                    return;
                }

                // Remove the range
                ranges.splice(rangeIndex, 1);

                // Re-render ranges
                renderRanges(criteriaKey, ranges);
            });
        }

        // 3. Fixed regenerateRangeComparisons function
        function regenerateRangeComparisons(criteriaKey) {
            const ranges = ahpData.subCriteria[criteriaKey].ranges;

            // Initialize comparisons object if it doesn't exist
            if (!ahpData.subCriteria[criteriaKey].comparisons) {
                ahpData.subCriteria[criteriaKey].comparisons = {};
            }

            // Get the current comparisons to preserve existing values
            const existingComparisons = ahpData.subCriteria[criteriaKey].comparisons;
            const newComparisons = {};

            // Create comparison structure for each range pair
            for (let i = 0; i < ranges.length; i++) {
                const range1 = ranges[i];
                // Create a deterministic label format
                const label1 = range1.max === 999 || range1.max === 999999999 ?
                    `${range1.min}+` :
                    `${range1.min}-${range1.max}`;

                // Initialize this range's comparisons
                if (!newComparisons[label1]) {
                    newComparisons[label1] = {};
                }

                for (let j = 0; j < ranges.length; j++) {
                    if (i !== j) {
                        const range2 = ranges[j];
                        // Create a deterministic label format
                        const label2 = range2.max === 999 || range2.max === 999999999 ?
                            `${range2.min}+` :
                            `${range2.min}-${range2.max}`;

                        // Try to preserve existing comparison value
                        if (existingComparisons[label1] && existingComparisons[label1][label2] !== undefined) {
                            newComparisons[label1][label2] = existingComparisons[label1][label2];
                        } else if (existingComparisons[label2] && existingComparisons[label2][label1] !== undefined) {
                            // If reciprocal exists, use its inverse
                            newComparisons[label1][label2] = 1 / existingComparisons[label2][label1];
                        } else {
                            // Set default value if not exists
                            newComparisons[label1][label2] = 1;
                        }
                    }
                }
            }

            // Update the comparison structure
            ahpData.subCriteria[criteriaKey].comparisons = newComparisons;
        }


        function renderRangeComparisons(ranges) {
            $('#rangeComparisonSliders').empty();

            const criteriaKey = currentConfig.criteria;
            const rangesArray = Array.isArray(ranges) ? ranges : ahpData.subCriteria[criteriaKey].ranges;

            const sortedRanges = [...rangesArray].sort((a, b) => a.min - b.min);

            for (let i = 0; i < sortedRanges.length; i++) {
                for (let j = i + 1; j < sortedRanges.length; j++) {
                    const r1 = sortedRanges[i];
                    const r2 = sortedRanges[j];

                    const label1 = r1.max === 999 || r1.max === 999999999 ? `${r1.min}+` : `${r1.min}-${r1.max}`;
                    const label2 = r2.max === 999 || r2.max === 999999999 ? `${r2.min}+` : `${r2.min}-${r2.max}`;

                    let savedValue = 1;
                    let flipLabels = false;

                    const comparisons = ahpData.subCriteria[criteriaKey].comparisons || {};

                    if (comparisons[label1] && comparisons[label1][label2] !== undefined) {
                        let val = comparisons[label1][label2];
                        if (val < 1) {
                            savedValue = Math.round(1 / val);
                            flipLabels = true;
                        } else {
                            savedValue = Math.round(val);
                        }
                    } else if (comparisons[label2] && comparisons[label2][label1] !== undefined) {
                        let val = comparisons[label2][label1];
                        if (val >= 1) {
                            savedValue = Math.round(val);
                            flipLabels = true;
                        } else {
                            savedValue = Math.round(1 / val);
                        }
                    }

                    const left = flipLabels ? label2 : label1;
                    const right = flipLabels ? label1 : label2;

                    $('#rangeComparisonSliders').append(`
                <div class="range-comparison mb-3 p-3 border rounded bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold comparison-left">${left}</span>
                        <button class="btn btn-sm btn-outline-secondary flip-btn mx-2" data-range1="${left}" data-range2="${right}"><i class="fa-solid fa-right-left"></i></button>
                        <span class="fw-bold comparison-right">${right}</span>
                    </div>
                    <div class="slider-container">
                        <input type="range" class="form-range range-comparison-slider" 
                               min="1" max="9" value="${savedValue}" step="1"
                               data-range1="${left}" data-range2="${right}">
                        <div class="d-flex justify-content-between px-2">
                            ${[...Array(9)].map((_, i) => `<small>${i + 1}</small>`).join('')}
                        </div>
                    </div>
                </div>
            `);
                }
            }

            $('.flip-btn').off('click').on('click', function() {
                const container = $(this).closest('.range-comparison');
                const leftLabel = container.find('.comparison-left');
                const rightLabel = container.find('.comparison-right');
                const slider = container.find('.range-comparison-slider');

                const tmpText = leftLabel.text();
                leftLabel.text(rightLabel.text());
                rightLabel.text(tmpText);

                const r1 = slider.data('range1');
                const r2 = slider.data('range2');
                slider.data('range1', r2);
                slider.data('range2', r1);

                let value = parseInt(slider.val());
                let inverted = Math.round(1 / value);
                inverted = Math.max(1, Math.min(9, inverted));
                slider.val(inverted);
            });

            setupRangeSliderListeners();
        }


        // 5. Update setupRangeSliderListeners to ensure it works properly
        function setupRangeSliderListeners() {
            $('.range-comparison-slider').off('change input').on('change input', function() {
                const criteriaKey = currentConfig.criteria;
                const range1 = $(this).data('range1');
                const range2 = $(this).data('range2');
                const value = parseInt($(this).val());

                // Ensure the comparisons object exists
                if (!ahpData.subCriteria[criteriaKey].comparisons) {
                    ahpData.subCriteria[criteriaKey].comparisons = {};
                }
                if (!ahpData.subCriteria[criteriaKey].comparisons[range1]) {
                    ahpData.subCriteria[criteriaKey].comparisons[range1] = {};
                }
                if (!ahpData.subCriteria[criteriaKey].comparisons[range2]) {
                    ahpData.subCriteria[criteriaKey].comparisons[range2] = {};
                }

                // Set reciprocal values
                ahpData.subCriteria[criteriaKey].comparisons[range1][range2] = value;
                ahpData.subCriteria[criteriaKey].comparisons[range2][range1] = 1 / value;
            });
        }









        function openEducationComparisonModal() {
            currentConfig.type = 'education';
            const criteriaName = ahpData.mainCriteria[currentConfig.criteria];
            const levels = ahpData.subCriteria.education.levels;
            const comparisons = ahpData.subCriteria.education.comparisons || {};

            let content = `
            <h5 class="mb-4 fw-bold text-primary"><i class="fa-solid fa-scale-balanced"></i> ${criteriaName} Configuration</h5>

    <div class="alert alert-info d-flex align-items-start gap-2">
        <i class="fas fa-info-circle mt-1"></i>
        <div>
            Choose how you want to compare <strong>education levels</strong>. You can either rank them easily or define detailed comparisons manually.
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="fas fa-sliders-h fa-2x text-primary mb-3"></i>
                    <h5 class="card-title fw-bold">Expert Mode</h5>
                    <p class="card-text text-muted">Manually adjust comparison values using sliders (1–9 scale).</p>
                    <button id="expertModeBtn" class="btn btn-outline-primary w-100">Use Expert Mode</button>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="fas fa-magic fa-2x text-success mb-3"></i>
                    <h5 class="card-title fw-bold">Easy Mode</h5>
                    <p class="card-text text-muted">Quickly rank education levels by importance without using sliders.</p>
                    <button id="easyModeBtn" class="btn btn-outline-success w-100">Use Easy Mode</button>
                </div>
            </div>
        </div>
    </div>

    <div id="comparisonContainer" class="mt-4"></div>
`;


            $rangeConfigContent.html(content);

            $('#expertModeBtn').on('click', function() {
                renderEducationExpertMode(levels, comparisons);
            });

            $('#easyModeBtn').on('click', function() {
                renderEducationEasyMode(levels);
            });

            $rangeConfigModal.modal('show');
        }

        function renderEducationExpertMode(levels, comparisons) {
            $('#comparisonContainer').html(`
            <div class="alert alert-info d-flex align-items-start gap-2">
    <i class="fas fa-info-circle mt-1"></i>
    <div>
        <strong>Note:</strong> <br>The slider is interpreted from <strong>left to right</strong>.
        For example, a value of <strong>2</strong> means that the <strong>left label</strong> is slightly more important than the right.
        <br>
        To reverse the comparison direction, click the 
        <i class="fa-solid fa-right-left text-secondary mx-1"></i> icon.
    </div>
</div>

        <div id="educationComparisonSliders"></div>
        <div class="mt-4 text-start">
            <button type="button" id="saveEducationComparison" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Education Comparison
            </button>
        </div>
    `);

            $('#educationComparisonSliders').empty();

            // Loop pairwise combinations
            for (let i = 0; i < levels.length; i++) {
                for (let j = i + 1; j < levels.length; j++) {
                    let level1 = levels[i];
                    let level2 = levels[j];
                    const originalL1 = level1;
                    const originalL2 = level2;

                    let savedValue = 1;
                    let flipLabels = false;

                    if (comparisons[level1] && comparisons[level1][level2] !== undefined) {
                        let val = comparisons[level1][level2];
                        if (val < 1) {
                            savedValue = Math.round(1 / val);
                            flipLabels = true;
                        } else {
                            savedValue = Math.round(val);
                        }
                    } else if (comparisons[level2] && comparisons[level2][level1] !== undefined) {
                        let val = comparisons[level2][level1];
                        if (val >= 1) {
                            savedValue = Math.round(val);
                            flipLabels = true;
                        } else {
                            savedValue = Math.round(1 / val);
                        }
                    }

                    if (flipLabels) {
                        [level1, level2] = [originalL2, originalL1];
                    }

                    $('#educationComparisonSliders').append(`
                <div class="education-comparison mb-3 p-3 border rounded bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold comparison-left">${level1}</span>
                        <button class="btn btn-sm btn-outline-secondary flip-btn mx-2" data-level1="${level1}" data-level2="${level2}">
                            <i class="fa-solid fa-right-left"></i>
                        </button>
                        <span class="fw-bold comparison-right">${level2}</span>
                    </div>
                    <div class="slider-container">
                        <input type="range" class="form-range education-comparison-slider" 
                               min="1" max="9" value="${savedValue}" step="1"
                               data-level1="${level1}" data-level2="${level2}">
                        <div class="d-flex justify-content-between px-2">
                            ${[...Array(9)].map((_, i) => `<small>${i + 1}</small>`).join('')}
                        </div>
                    </div>
                </div>
            `);
                }
            }

            // Flip button behavior
            $('.flip-btn').off('click').on('click', function() {
                const container = $(this).closest('.education-comparison');
                const leftLabel = container.find('.comparison-left');
                const rightLabel = container.find('.comparison-right');
                const slider = container.find('.education-comparison-slider');

                // Swap label text
                const tmpText = leftLabel.text();
                leftLabel.text(rightLabel.text());
                rightLabel.text(tmpText);

                // Swap data attributes
                const l1 = slider.data('level1');
                const l2 = slider.data('level2');
                slider.data('level1', l2);
                slider.data('level2', l1);

                // Invert slider value
                const value = parseInt(slider.val());
                let inverted = Math.round(1 / value);
                inverted = Math.max(1, Math.min(9, inverted)); // Clamp
                slider.val(inverted);
            });

            // Save the comparisons
            $('#saveEducationComparison').on('click', function() {
                const comparisons = {};

                $('.education-comparison-slider').each(function() {
                    const level1 = $(this).data('level1');
                    const level2 = $(this).data('level2');
                    const value = parseInt($(this).val());

                    if (!comparisons[level1]) comparisons[level1] = {};
                    if (!comparisons[level2]) comparisons[level2] = {};

                    comparisons[level1][level2] = value;
                    comparisons[level2][level1] = parseFloat((1 / value).toFixed(4));
                });

                // Save into global AHP config
                ahpData.subCriteria.education.comparisons = comparisons;
                ahpData.subCriteria.education.mode = 'expert';

                $rangeConfigModal.modal('hide');
                updateSubCriteriaConfigButtons();
                updateConfigurationStatus();
            });
        }
        // Function 1: Range Easy Mode with SortableJS
        function renderRangeEasyMode(ranges, existingRanking) {
            // Sort ranges by min value to get default order
            const sortedRanges = [...ranges].sort((a, b) => a.min - b.min);

            // Get range labels
            const rangeLabels = sortedRanges.map(range =>
                range.max === 999 || range.max === 999999999 ?
                `${range.min}+` :
                `${range.min}-${range.max}`
            );

            let orderedRanges;
            if (existingRanking && existingRanking.length > 0) {
                // Use existing ranking if available
                orderedRanges = existingRanking;

                // Add any new ranges that weren't in the existing ranking
                rangeLabels.forEach(label => {
                    if (!orderedRanges.includes(label)) {
                        orderedRanges.push(label);
                    }
                });
            } else {
                // Default order is sorted by min value
                orderedRanges = rangeLabels;
            }

            // Generate sortable list with proper ordering
            let rangeItems = '';
            orderedRanges.forEach((label, index) => {
                rangeItems += `
            <div class="list-group-item range-rank-item" data-range="${label}" data-index="${index}">
                <div class="d-flex align-items-center">
                    <span class="handle me-3">
                        <i class="fas fa-grip-vertical"></i>
                    </span>
                    <span class="rank-number me-3 fw-bold text-primary">${index + 1}.</span>
                    <span class="range-name">${label}</span>
                </div>
            </div>
        `;
            });

            $('#comparisonContainer').html(`
        <h5 class="mb-3 mt-4 border-top pt-3">Easy Mode: Rank Ranges by Importance</h5>
        <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Instructions:</strong> Drag to rank the ranges from most to least important. The system will automatically generate comparison values.
        </div>
        <div id="rangeRankingList" class="list-group mb-4">
            ${rangeItems}
        </div>
        <button id="saveEasyModeBtn" class="btn btn-success">
            <i class="fas fa-save me-2"></i> Save Ranking
        </button>
    `);

            // Initialize SortableJS
            const sortableList = document.getElementById('rangeRankingList');
            const sortable = Sortable.create(sortableList, {
                animation: 150,
                handle: '.handle',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd: function(evt) {
                    updateRankNumbers();
                }
            });

            // Function to update rank numbers
            function updateRankNumbers() {
                const items = document.querySelectorAll('#rangeRankingList .range-rank-item');
                items.forEach((item, index) => {
                    const rankNumber = item.querySelector('.rank-number');
                    rankNumber.textContent = (index + 1) + '.';
                    item.setAttribute('data-index', index);
                });
            }

            // Save button handler
            $('#saveEasyModeBtn').on('click', function() {
                const rankedRanges = [];
                $('#rangeRankingList .range-rank-item').each(function() {
                    rankedRanges.push($(this).data('range'));
                });

                // Generate comparison matrix based on ranking
                const comparisons = generateRangeComparisonFromRanking(rankedRanges);

                // Save easy mode comparisons
                ahpData.subCriteria[currentConfig.criteria].comparisons = comparisons;
                ahpData.subCriteria[currentConfig.criteria].mode = 'easy'; // Mark as easy mode
                ahpData.subCriteria[currentConfig.criteria].ranking = rankedRanges; // Save the ranking

                Swal.fire({
                    title: 'Ranking Saved!',
                    text: 'Ranges have been ranked and comparison values generated automatically.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });

                $rangeConfigModal.modal('hide');
                updateSubCriteriaConfigButtons();
                updateConfigurationStatus();
            });
        }


        function renderEducationEasyMode(levels) {
            // Check if there's existing ranking from previous save
            const existingRanking = ahpData.subCriteria.education.ranking;
            let orderedLevels;

            if (existingRanking && existingRanking.length > 0) {
                // Use existing ranking if available
                orderedLevels = existingRanking;
            } else {
                // Set default ranking: S2 > S1 > D3 > SMK > SMA
                const defaultOrder = ['S2', 'S1', 'D3', 'SMK', 'SMA'];
                orderedLevels = [];

                // Add levels in default order if they exist
                defaultOrder.forEach(defaultLevel => {
                    if (levels.includes(defaultLevel)) {
                        orderedLevels.push(defaultLevel);
                    }
                });

                // Add any remaining levels that weren't in default order
                levels.forEach(level => {
                    if (!orderedLevels.includes(level)) {
                        orderedLevels.push(level);
                    }
                });
            }

            // Generate sortable list with proper ordering
            let levelItems = '';
            orderedLevels.forEach((level, index) => {
                levelItems += `
            <div class="list-group-item level-rank-item" data-level="${level}" data-index="${index}">
                <div class="d-flex align-items-center">
                    <span class="handle me-3">
                        <i class="fas fa-grip-vertical"></i>
                    </span>
                    <span class="rank-number me-3 fw-bold text-primary">${index + 1}.</span>
                    <span class="level-name">${level}</span>
                </div>
            </div>
        `;
            });

            $('#comparisonContainer').html(`
        <h5 class="mb-3 mt-4 border-top pt-3">Easy Mode: Rank Education Levels</h5>
        <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Instructions:</strong> Drag to rank the education levels from most to least important. The system will automatically generate comparison values.
        </div>
        <div id="levelRankingList" class="list-group mb-4">
            ${levelItems}
        </div>
        <button id="saveEasyModeBtn" class="btn btn-success">
            <i class="fas fa-save me-2"></i> Save Ranking
        </button>
    `);

            // Initialize SortableJS
            const sortableList = document.getElementById('levelRankingList');
            const sortable = Sortable.create(sortableList, {
                animation: 150,
                handle: '.handle',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd: function(evt) {
                    updateRankNumbers();
                }
            });

            // Function to update rank numbers
            function updateRankNumbers() {
                const items = document.querySelectorAll('#levelRankingList .level-rank-item');
                items.forEach((item, index) => {
                    const rankNumber = item.querySelector('.rank-number');
                    rankNumber.textContent = (index + 1) + '.';
                    item.setAttribute('data-index', index);
                });
            }

            // Save button handler
            $('#saveEasyModeBtn').on('click', function() {
                const rankedLevels = [];
                $('#levelRankingList .level-rank-item').each(function() {
                    rankedLevels.push($(this).data('level'));
                });

                // Generate comparison matrix based on ranking
                const comparisons = generateComparisonFromRanking(rankedLevels);

                // Save easy mode comparisons
                ahpData.subCriteria.education.comparisons = comparisons;
                ahpData.subCriteria.education.mode = 'easy'; // Mark as easy mode
                ahpData.subCriteria.education.ranking = rankedLevels; // Save the ranking

                Swal.fire({
                    title: 'Ranking Saved!',
                    text: 'Education levels have been ranked and comparison values generated automatically.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });

                $rangeConfigModal.modal('hide');
                updateSubCriteriaConfigButtons();
                updateConfigurationStatus();
            });
        }


        function generateComparisonFromRanking(rankedLevels) {
            const comparisons = {};
            const n = rankedLevels.length;

            // Initialize comparison matrix
            rankedLevels.forEach(level => {
                comparisons[level] = {};
            });

            // Generate comparison values based on ranking
            // Using a progressive scale that ensures consistency
            for (let i = 0; i < n; i++) {
                for (let j = 0; j < n; j++) {
                    if (i === j) {
                        comparisons[rankedLevels[i]][rankedLevels[j]] = 1;
                    } else if (i < j) {
                        // Higher ranked (lower index) is more important
                        // Use a moderate scale to avoid extreme values
                        const rankDiff = j - i;
                        let value;

                        switch (rankDiff) {
                            case 1:
                                value = 2; // Slightly more important
                                break;
                            case 2:
                                value = 3; // Moderately more important
                                break;
                            case 3:
                                value = 5; // Strongly more important
                                break;
                            case 4:
                                value = 7; // Very strongly more important
                                break;
                            default:
                                value = Math.min(9, 2 * rankDiff); // Cap at 9
                        }

                        comparisons[rankedLevels[i]][rankedLevels[j]] = value;
                        comparisons[rankedLevels[j]][rankedLevels[i]] = 1 / value;
                    }
                }
            }

            return comparisons;
        }




        function openExperienceComparisonModal() {
            currentConfig.type = 'experience';
            const criteriaName = ahpData.mainCriteria[currentConfig.criteria];
            const periods = ahpData.subCriteria.experience_duration.periods;
            const comparisons = ahpData.subCriteria.experience_duration.comparisons || {};

            let content = `
            <h5 class="mb-4 fw-bold text-primary"><i class="fa-solid fa-scale-balanced"></i> ${criteriaName} Configuration</h5>

    <div class="alert alert-info d-flex align-items-start gap-2">
        <i class="fas fa-info-circle mt-1"></i>
        <div>
            Choose how you want to compare <strong>experience periods</strong>. You can either rank them quickly or manually set detailed comparison values.
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="fas fa-sliders-h fa-2x text-primary mb-3"></i>
                    <h5 class="card-title fw-bold">Expert Mode</h5>
                    <p class="card-text text-muted">Manually adjust comparison values using sliders (scale of 1–9).</p>
                    <button id="expertModeBtn" class="btn btn-outline-primary w-100">Use Expert Mode</button>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="fas fa-magic fa-2x text-success mb-3"></i>
                    <h5 class="card-title fw-bold">Easy Mode</h5>
                    <p class="card-text text-muted">Quickly rank the experience periods by importance.</p>
                    <button id="easyModeBtn" class="btn btn-outline-success w-100">Use Easy Mode</button>
                </div>
            </div>
        </div>
    </div>

    <div id="comparisonContainer" class="mt-4"></div>
`;

            $rangeConfigContent.html(content);

            $('#expertModeBtn').on('click', function() {
                renderExperienceExpertMode(periods, comparisons);
            });

            $('#easyModeBtn').on('click', function() {
                renderExperienceEasyMode(periods);
            });

            $rangeConfigModal.modal('show');
        }


        function renderExperienceExpertMode(periods, comparisons) {
            $('#comparisonContainer').html(`
            <div class="alert alert-info d-flex align-items-start gap-2">
    <i class="fas fa-info-circle mt-1"></i>
    <div>
        <strong>Note:</strong> <br>The slider is interpreted from <strong>left to right</strong>.
        For example, a value of <strong>2</strong> means that the <strong>left label</strong> is slightly more important than the right.
        <br>
        To reverse the comparison direction, click the 
        <i class="fa-solid fa-right-left text-secondary mx-1"></i> icon.
    </div>
</div>

        <div id="experienceComparisonSliders"></div>
        <div class="mt-4 text-start">
            <button type="button" id="saveExperienceComparison" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Experience Comparison
            </button>
        </div>
    `);

            $('#experienceComparisonSliders').empty();

            for (let i = 0; i < periods.length; i++) {
                for (let j = i + 1; j < periods.length; j++) {
                    let period1 = periods[i];
                    let period2 = periods[j];
                    const originalP1 = period1;
                    const originalP2 = period2;

                    let savedValue = 1;
                    let flipLabels = false;

                    if (comparisons[period1] && comparisons[period1][period2] !== undefined) {
                        let val = comparisons[period1][period2];
                        if (val < 1) {
                            savedValue = Math.round(1 / val);
                            flipLabels = true;
                        } else {
                            savedValue = Math.round(val);
                        }
                    } else if (comparisons[period2] && comparisons[period2][period1] !== undefined) {
                        let val = comparisons[period2][period1];
                        if (val >= 1) {
                            savedValue = Math.round(val);
                            flipLabels = true;
                        } else {
                            savedValue = Math.round(1 / val);
                        }
                    }

                    if (flipLabels) {
                        [period1, period2] = [originalP2, originalP1];
                    }

                    $('#experienceComparisonSliders').append(`
                <div class="experience-comparison mb-3 p-3 border rounded bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold comparison-left">${period1}</span>
                        <button class="btn btn-sm btn-outline-secondary flip-btn mx-2" data-period1="${period1}" data-period2="${period2}"><i class="fa-solid fa-right-left"></i></button>
                        <span class="fw-bold comparison-right">${period2}</span>
                    </div>
                    <div class="slider-container">
                        <input type="range" class="form-range experience-comparison-slider"
                            min="1" max="9" value="${savedValue}" step="1"
                            data-period1="${period1}" data-period2="${period2}">
                        <div class="d-flex justify-content-between px-2">
                            ${[...Array(9)].map((_, i) => `<small>${i + 1}</small>`).join('')}
                        </div>
                    </div>
                </div>
            `);
                }
            }

            $('.flip-btn').off('click').on('click', function() {
                const container = $(this).closest('.experience-comparison');
                const leftLabel = container.find('.comparison-left');
                const rightLabel = container.find('.comparison-right');
                const slider = container.find('.experience-comparison-slider');

                const tmpText = leftLabel.text();
                leftLabel.text(rightLabel.text());
                rightLabel.text(tmpText);

                const p1 = slider.data('period1');
                const p2 = slider.data('period2');
                slider.data('period1', p2);
                slider.data('period2', p1);

                let value = parseInt(slider.val());
                let inverted = Math.round(1 / value);
                inverted = Math.max(1, Math.min(9, inverted));
                slider.val(inverted);
            });

            $('#saveExperienceComparison').on('click', function() {
                const comparisons = {};

                $('.experience-comparison-slider').each(function() {
                    const period1 = $(this).data('period1');
                    const period2 = $(this).data('period2');
                    const value = parseInt($(this).val());

                    if (!comparisons[period1]) comparisons[period1] = {};
                    if (!comparisons[period2]) comparisons[period2] = {};

                    comparisons[period1][period2] = value;
                    comparisons[period2][period1] = parseFloat((1 / value).toFixed(4));
                });

                ahpData.subCriteria.experience_duration.comparisons = comparisons;
                ahpData.subCriteria.experience_duration.mode = 'expert';

                $rangeConfigModal.modal('hide');
                updateSubCriteriaConfigButtons();
                updateConfigurationStatus();
            });
        }


        function renderExperienceEasyMode(periods) {
            // Check if there's existing ranking from previous save
            const existingRanking = ahpData.subCriteria.experience_duration.ranking;
            let orderedPeriods;

            if (existingRanking && existingRanking.length > 0) {
                // Use existing ranking if available
                orderedPeriods = existingRanking;
            } else {
                // Set default ranking: longer experience is more valuable
                // Assuming periods like "5+ years", "3-5 years", "1-3 years", "< 1 year"
                const defaultOrder = ['5+ years', '3-5 years', '1-3 years', '< 1 year', 'No experience'];
                orderedPeriods = [];

                // Add periods in default order if they exist
                defaultOrder.forEach(defaultPeriod => {
                    if (periods.includes(defaultPeriod)) {
                        orderedPeriods.push(defaultPeriod);
                    }
                });

                // Add any remaining periods that weren't in default order
                periods.forEach(period => {
                    if (!orderedPeriods.includes(period)) {
                        orderedPeriods.push(period);
                    }
                });
            }

            // Generate sortable list with proper ordering
            let periodItems = '';
            orderedPeriods.forEach((period, index) => {
                periodItems += `
            <div class="list-group-item period-rank-item" data-period="${period}" data-index="${index}">
                <div class="d-flex align-items-center">
                    <span class="handle me-3">
                        <i class="fas fa-grip-vertical"></i>
                    </span>
                    <span class="rank-number me-3 fw-bold text-primary">${index + 1}.</span>
                    <span class="period-name">${period}</span>
                </div>
            </div>
        `;
            });

            $('#comparisonContainer').html(`
        <h5 class="mb-3 mt-4 border-top pt-3">Easy Mode: Rank Experience Periods</h5>
        <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Instructions:</strong> Drag to rank the experience periods from most to least important. The system will automatically generate comparison values.
        </div>
        <div id="periodRankingList" class="list-group mb-4">
            ${periodItems}
        </div>
        <button id="saveEasyModeBtn" class="btn btn-success">
            <i class="fas fa-save me-2"></i> Save Ranking
        </button>
    `);

            // Initialize SortableJS
            const sortableList = document.getElementById('periodRankingList');
            const sortable = Sortable.create(sortableList, {
                animation: 150,
                handle: '.handle',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd: function(evt) {
                    updateRankNumbers();
                }
            });

            // Function to update rank numbers
            function updateRankNumbers() {
                const items = document.querySelectorAll('#periodRankingList .period-rank-item');
                items.forEach((item, index) => {
                    const rankNumber = item.querySelector('.rank-number');
                    rankNumber.textContent = (index + 1) + '.';
                    item.setAttribute('data-index', index);
                });
            }

            // Save button handler
            $('#saveEasyModeBtn').on('click', function() {
                const rankedPeriods = [];
                $('#periodRankingList .period-rank-item').each(function() {
                    rankedPeriods.push($(this).data('period'));
                });

                // Generate comparison matrix based on ranking
                const comparisons = generateExperienceComparisonFromRanking(rankedPeriods);

                // Save easy mode comparisons
                ahpData.subCriteria.experience_duration.comparisons = comparisons;
                ahpData.subCriteria.experience_duration.mode = 'easy'; // Mark as easy mode
                ahpData.subCriteria.experience_duration.ranking = rankedPeriods; // Save the ranking

                Swal.fire({
                    title: 'Ranking Saved!',
                    text: 'Experience periods have been ranked and comparison values generated automatically.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });

                $rangeConfigModal.modal('hide');
                updateSubCriteriaConfigButtons();
                updateConfigurationStatus();
            });
        }

        function generateExperienceComparisonFromRanking(rankedPeriods) {
            const comparisons = {};
            const n = rankedPeriods.length;

            // Initialize comparison matrix
            rankedPeriods.forEach(period => {
                comparisons[period] = {};
            });

            // Generate comparison values based on ranking
            // Using a progressive scale that ensures consistency
            for (let i = 0; i < n; i++) {
                for (let j = 0; j < n; j++) {
                    if (i === j) {
                        comparisons[rankedPeriods[i]][rankedPeriods[j]] = 1;
                    } else if (i < j) {
                        // Higher ranked (lower index) is more important
                        // Use a moderate scale to avoid extreme values
                        const rankDiff = j - i;
                        let value;

                        switch (rankDiff) {
                            case 1:
                                value = 2; // Slightly more important
                                break;
                            case 2:
                                value = 3; // Moderately more important
                                break;
                            case 3:
                                value = 5; // Strongly more important
                                break;
                            case 4:
                                value = 7; // Very strongly more important
                                break;
                            default:
                                value = Math.min(9, 2 * rankDiff); // Cap at 9
                        }

                        comparisons[rankedPeriods[i]][rankedPeriods[j]] = value;
                        comparisons[rankedPeriods[j]][rankedPeriods[i]] = 1 / value;
                    }
                }
            }

            return comparisons;
        }


        // 8. Updated saveRangeConfig function to handle the two-step process
        $saveRangeConfig.off('click').on('click', function() {
            const criteria = currentConfig.criteria;

            if (currentConfig.type === 'range') {
                // Save range comparisons
                const comparisons = {};
                $('.range-comparison-slider').each(function() {
                    const range1 = $(this).data('range1');
                    const range2 = $(this).data('range2');
                    const value = parseInt($(this).val());

                    if (!comparisons[range1]) comparisons[range1] = {};
                    if (!comparisons[range2]) comparisons[range2] = {};

                    comparisons[range1][range2] = value;
                    comparisons[range2][range1] = 1 / value;
                });

                ahpData.subCriteria[criteria].comparisons = comparisons;
            } else if (currentConfig.type === 'education') {
                // Education configuration handling
                const comparisons = {};
                $('.education-comparison-slider').each(function() {
                    const level1 = $(this).data('level1');
                    const level2 = $(this).data('level2');
                    const value = parseInt($(this).val());

                    if (!comparisons[level1]) comparisons[level1] = {};
                    if (!comparisons[level2]) comparisons[level2] = {};

                    comparisons[level1][level2] = value;
                    comparisons[level2][level1] = 1 / value;
                });

                ahpData.subCriteria.education.comparisons = comparisons;
            } else if (currentConfig.type === 'experience') {
                // Experience configuration handling
                const comparisons = {};
                $('.experience-comparison-slider').each(function() {
                    const period1 = $(this).data('period1');
                    const period2 = $(this).data('period2');
                    const value = parseInt($(this).val());

                    if (!comparisons[period1]) comparisons[period1] = {};
                    if (!comparisons[period2]) comparisons[period2] = {};

                    comparisons[period1][period2] = value;
                    comparisons[period2][period1] = 1 / value;
                });

                ahpData.subCriteria.experience_duration.comparisons = comparisons;
            }

            $rangeConfigModal.modal('hide');
            updateSubCriteriaConfigButtons();
            updateConfigurationStatus();
        });







        // Initialize main criteria comparison with mode selection
        function initializeMainCriteriaComparison() {
            $mainCriteriaSliders.empty();

            const criteria = Object.keys(ahpData.mainCriteria);

            // Show mode selection like education
            showMainCriteriaModeSelection(criteria);
        }

        function showMainCriteriaModeSelection(criteria) {
            const html = `
        <div class="alert alert-info d-flex align-items-start gap-2">
            <i class="fas fa-info-circle mt-1"></i>
            <div>
                Choose how you want to compare <strong>main criteria</strong>. You can either rank them easily or define detailed comparisons manually.
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="fas fa-sliders-h fa-2x text-primary mb-3"></i>
                        <h5 class="card-title fw-bold">Expert Mode</h5>
                        <p class="card-text text-muted">Manually adjust comparison values using sliders (1–9 scale).</p>
                        <button id="mainExpertModeBtn" class="btn btn-outline-primary w-100">Use Expert Mode</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="fas fa-magic fa-2x text-success mb-3"></i>
                        <h5 class="card-title fw-bold">Easy Mode</h5>
                        <p class="card-text text-muted">Quickly rank criteria by importance without using sliders.</p>
                        <button id="mainEasyModeBtn" class="btn btn-outline-success w-100">Use Easy Mode</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="mainComparisonContainer" class="mt-4"></div>
    `;

            $mainCriteriaSliders.html(html);

            $('#mainExpertModeBtn').on('click', function() {
                renderMainCriteriaExpertMode(criteria);
            });

            $('#mainEasyModeBtn').on('click', function() {
                renderMainCriteriaEasyMode(criteria);
            });
        }

        function renderMainCriteriaExpertMode(criteria) {
            const comparisons = ahpData.mainComparisons || {};

            $('#mainComparisonContainer').html(`
        <div class="alert alert-info d-flex align-items-start gap-2">
            <i class="fas fa-info-circle mt-1"></i>
            <div>
                <strong>Note:</strong> <br>The slider is interpreted from <strong>left to right</strong>.
                For example, a value of <strong>2</strong> means that the <strong>left criterion</strong> is slightly more important than the right.
                <br>
                To reverse the comparison direction, click the 
                <i class="fa-solid fa-right-left text-secondary mx-1"></i> icon.
            </div>
        </div>

        <div id="mainCriteriaComparisonSliders"></div>
        <div class="mt-4 text-start">
            <button type="button" id="saveMainCriteriaComparison" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Main Criteria Comparison
            </button>
        </div>
    `);

            $('#mainCriteriaComparisonSliders').empty();

            // Loop pairwise combinations for dynamic criteria (3-7)
            for (let i = 0; i < criteria.length; i++) {
                for (let j = i + 1; j < criteria.length; j++) {
                    let crit1 = criteria[i];
                    let crit2 = criteria[j];
                    const originalC1 = crit1;
                    const originalC2 = crit2;
                    const crit1Name = ahpData.mainCriteria[crit1];
                    const crit2Name = ahpData.mainCriteria[crit2];

                    let savedValue = 1;
                    let flipLabels = false;

                    if (comparisons[crit1] && comparisons[crit1][crit2] !== undefined) {
                        let val = comparisons[crit1][crit2];
                        if (val < 1) {
                            savedValue = Math.round(1 / val);
                            flipLabels = true;
                        } else {
                            savedValue = Math.round(val);
                        }
                    } else if (comparisons[crit2] && comparisons[crit2][crit1] !== undefined) {
                        let val = comparisons[crit2][crit1];
                        if (val >= 1) {
                            savedValue = Math.round(val);
                            flipLabels = true;
                        } else {
                            savedValue = Math.round(1 / val);
                        }
                    }

                    if (flipLabels) {
                        [crit1, crit2] = [originalC2, originalC1];
                    }

                    const displayCrit1Name = ahpData.mainCriteria[crit1];
                    const displayCrit2Name = ahpData.mainCriteria[crit2];

                    $('#mainCriteriaComparisonSliders').append(`
                <div class="main-criteria-comparison mb-3 p-3 border rounded bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold comparison-left">${displayCrit1Name}</span>
                        <button class="btn btn-sm btn-outline-secondary flip-btn mx-2" data-crit1="${crit1}" data-crit2="${crit2}">
                            <i class="fa-solid fa-right-left"></i>
                        </button>
                        <span class="fw-bold comparison-right">${displayCrit2Name}</span>
                    </div>
                    <div class="slider-container">
                        <input type="range" class="form-range main-criteria-comparison-slider" 
                               min="1" max="9" value="${savedValue}" step="1"
                               data-crit1="${crit1}" data-crit2="${crit2}">
                        <div class="d-flex justify-content-between px-2">
                            ${[...Array(9)].map((_, i) => `<small>${i + 1}</small>`).join('')}
                        </div>
                    </div>
                </div>
            `);
                }
            }

            // Flip button behavior
            $('.flip-btn').off('click').on('click', function() {
                const container = $(this).closest('.main-criteria-comparison');
                const leftLabel = container.find('.comparison-left');
                const rightLabel = container.find('.comparison-right');
                const slider = container.find('.main-criteria-comparison-slider');

                // Swap label text
                const tmpText = leftLabel.text();
                leftLabel.text(rightLabel.text());
                rightLabel.text(tmpText);

                // Swap data attributes
                const c1 = slider.data('crit1');
                const c2 = slider.data('crit2');
                slider.data('crit1', c2);
                slider.data('crit2', c1);

                // Update button data attributes
                $(this).data('crit1', c2);
                $(this).data('crit2', c1);

                // Invert slider value
                const value = parseInt(slider.val());
                let inverted = Math.round(1 / value);
                inverted = Math.max(1, Math.min(9, inverted)); // Clamp
                slider.val(inverted);
            });

            // Save the comparisons
            $('#saveMainCriteriaComparison').on('click', function() {
                const comparisons = {};

                $('.main-criteria-comparison-slider').each(function() {
                    const crit1 = $(this).data('crit1');
                    const crit2 = $(this).data('crit2');
                    const value = parseInt($(this).val());

                    if (!comparisons[crit1]) comparisons[crit1] = {};
                    if (!comparisons[crit2]) comparisons[crit2] = {};

                    comparisons[crit1][crit2] = value;
                    comparisons[crit2][crit1] = parseFloat((1 / value).toFixed(4));
                });

                // Save into global AHP config
                ahpData.mainComparisons = comparisons;
                ahpData.mainCriteriaMode = 'expert';

                Swal.fire({
                    title: 'Comparison Saved!',
                    text: 'Main criteria comparisons have been saved successfully.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });

                // Update configuration status if function exists
                if (typeof updateConfigurationStatus === 'function') {
                    updateConfigurationStatus();
                }
            });
        }


        function renderMainCriteriaEasyMode(criteria) {
            // Check if there's existing ranking from previous save
            const existingRanking = ahpData.mainCriteriaRanking;
            let orderedCriteria;

            if (existingRanking && existingRanking.length > 0) {
                orderedCriteria = existingRanking;
            } else {
                orderedCriteria = [...criteria];
            }

            // Generate sortable list
            let criteriaItems = '';
            orderedCriteria.forEach((criterion, index) => {
                const criterionName = ahpData.mainCriteria[criterion];
                criteriaItems += `
            <div class="list-group-item criteria-rank-item" data-criterion="${criterion}" data-index="${index}">
                <div class="d-flex align-items-center">
                    <span class="handle me-3">
                        <i class="fas fa-grip-vertical"></i>
                    </span>
                    <span class="rank-number me-3 fw-bold text-primary">${index + 1}.</span>
                    <span class="criterion-name">${criterionName}</span>
                </div>
            </div>
        `;
            });

            $('#mainComparisonContainer').html(`
        <h5 class="mb-3 mt-4 border-top pt-3">Easy Mode: Rank Main Criteria</h5>
        <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Instructions:</strong> Drag to rank the criteria from most to least important.
        </div>
        <div id="criteriaRankingList" class="list-group mb-4">
            ${criteriaItems}
        </div>
        <button id="saveMainEasyModeBtn" class="btn btn-success">
            <i class="fas fa-save me-2"></i> Save Ranking
        </button>
    `);

            // Initialize SortableJS
            const sortableList = document.getElementById('criteriaRankingList');
            const sortable = Sortable.create(sortableList, {
                animation: 150,
                handle: '.handle',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd: function(evt) {
                    updateRankNumbers();
                }
            });

            // Function to update rank numbers
            function updateRankNumbers() {
                const items = document.querySelectorAll('#criteriaRankingList .criteria-rank-item');
                items.forEach((item, index) => {
                    const rankNumber = item.querySelector('.rank-number');
                    rankNumber.textContent = (index + 1) + '.';
                    item.setAttribute('data-index', index);
                });
            }

            // Save button handler
            $('#saveMainEasyModeBtn').on('click', function() {
                const rankedCriteria = [];
                $('#criteriaRankingList .criteria-rank-item').each(function() {
                    rankedCriteria.push($(this).data('criterion'));
                });

                const comparisons = generateMainCriteriaComparisonFromRanking(rankedCriteria);

                ahpData.mainComparisons = comparisons;
                ahpData.mainCriteriaMode = 'easy';
                ahpData.mainCriteriaRanking = rankedCriteria;

                Swal.fire({
                    title: 'Ranking Saved!',
                    text: 'Main criteria have been ranked successfully.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });

                if (typeof updateConfigurationStatus === 'function') {
                    updateConfigurationStatus();
                }
            });
        }


        function generateMainCriteriaComparisonFromRanking(rankedCriteria) {
            const comparisons = {};
            const n = rankedCriteria.length;

            // Initialize comparison matrix for dynamic criteria
            rankedCriteria.forEach(criterion => {
                comparisons[criterion] = {};
            });

            // Generate comparison values based on ranking
            // Using a progressive scale that ensures consistency for 3-7 criteria
            for (let i = 0; i < n; i++) {
                for (let j = 0; j < n; j++) {
                    if (i === j) {
                        comparisons[rankedCriteria[i]][rankedCriteria[j]] = 1;
                    } else if (i < j) {
                        // Higher ranked (lower index) is more important
                        // Use a moderate scale to avoid extreme values
                        const rankDiff = j - i;
                        let value;

                        switch (rankDiff) {
                            case 1:
                                value = 2; // Slightly more important
                                break;
                            case 2:
                                value = 3; // Moderately more important
                                break;
                            case 3:
                                value = 5; // Strongly more important
                                break;
                            case 4:
                                value = 7; // Very strongly more important
                                break;
                            case 5:
                                value = 8; // Very strongly more important
                                break;
                            case 6:
                                value = 9; // Absolutely more important
                                break;
                            default:
                                value = Math.min(9, 2 * rankDiff); // Cap at 9
                        }

                        comparisons[rankedCriteria[i]][rankedCriteria[j]] = value;
                        comparisons[rankedCriteria[j]][rankedCriteria[i]] = parseFloat((1 / value).toFixed(4));
                    }
                }
            }

            return comparisons;
        }

        // Save main criteria comparisons when slider changes (keep existing functionality)
        $(document).on('input', '.main-comparison-slider', function() {
            const crit1 = $(this).data('crit1');
            const crit2 = $(this).data('crit2');
            const value = parseInt($(this).val());

            if (!ahpData.mainComparisons[crit1]) ahpData.mainComparisons[crit1] = {};
            if (!ahpData.mainComparisons[crit2]) ahpData.mainComparisons[crit2] = {};

            ahpData.mainComparisons[crit1][crit2] = value;
            ahpData.mainComparisons[crit2][crit1] = 1 / value;
        });









        // Calculate button handler
        $calculateBtn.on('click', function() {
            // Show loading state
            $(this).prop('disabled', true);
            $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Calculating...');

            // Prepare only the selected criteria data for AJAX
            const selectedCriteria = Object.keys(ahpData.mainCriteria);

            // Create a filtered version of ahpData that only includes selected criteria
            const filteredAhpData = {
                mainCriteria: {},
                subCriteria: {},
                mainComparisons: {}
            };

            // Copy only selected main criteria
            selectedCriteria.forEach(crit => {
                filteredAhpData.mainCriteria[crit] = ahpData.mainCriteria[crit];
            });

            // Copy only selected sub criteria
            selectedCriteria.forEach(crit => {
                if (ahpData.subCriteria[crit]) {
                    filteredAhpData.subCriteria[crit] = ahpData.subCriteria[crit];
                }
            });

            // Copy only relevant main comparisons
            selectedCriteria.forEach(crit1 => {
                if (ahpData.mainComparisons[crit1]) {
                    filteredAhpData.mainComparisons[crit1] = {};
                    selectedCriteria.forEach(crit2 => {
                        if (ahpData.mainComparisons[crit1][crit2]) {
                            filteredAhpData.mainComparisons[crit1][crit2] = ahpData.mainComparisons[crit1][crit2];
                        }
                    });
                }
            });

            // Prepare data for AJAX
            const formData = {
                demandId: $demandSelect.val(),
                ahpData: filteredAhpData
            };

            // Send AJAX request
            $.ajax({
                url: '/ahp/calculate',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val(),
                    'Accept': 'application/json'
                },
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: function(response) {
                    console.log(response);

                    if (response.success) {
                        displayRankings(response.rankings);
                        navigateToStep(5); // Navigate to the integrated results step
                    } else {
                        Swal.fire('Error', response.message || 'Calculation failed', 'error');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Server error occurred';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', errorMsg, 'error');
                },
                complete: function() {
                    $calculateBtn.prop('disabled', false);
                    $calculateBtn.html('<i class="fas fa-calculator"></i> Calculate Final Ranking');
                }
            });
        });

        // Display rankings in the integrated results section
        function displayRankings(rankings) {
            let html = '';

            if (!rankings || rankings.length === 0) {
                html = '<tr><td colspan="9" class="text-center">No applicants found</td></tr>';
            } else {
                rankings.forEach((applicant, index) => {
                    const scorePercent = (applicant.total_score * 100).toFixed(1);

                    html += `
        <tr>
            <td>${index + 1}</td>
            <td><strong>${applicant.name}</strong></td>
            <td>${applicant.details.age || 'N/A'}</td>
            <td>${applicant.details.expected_salary || 'N/A'}</td>
            <td>${applicant.details.distance || 'N/A'}</td>
            <td>${applicant.details.education || 'N/A'}</td>
            <td>${applicant.details.experience_duration || 'N/A'}</td>
            <td>
                <div class="progress position-relative">
                    <div class="progress-bar bg-success" role="progressbar" 
                        style="width: ${scorePercent}%" 
                        aria-valuenow="${scorePercent}" 
                        aria-valuemin="0" aria-valuemax="100">
                    </div>
                    <span class="position-absolute d-flex w-100 justify-content-center align-items-center h-100" style="left: 0; color: #000000; font-weight: 500;">${scorePercent}%</span>
                </div>
            </td>
       
            <td class="actions-cell">
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-info view-applicant-btn" 
                            data-id="${applicant.id}" 
                            data-name="${applicant.name}">
                        <i class="fas fa-eye me-1"></i> View
                    </button>
                    <button class="btn btn-sm btn-outline-primary schedule-interview" 
                            id="scheduleBtn-${applicant.id}" 
                            data-id="${applicant.id}" 
                            data-name="${applicant.name}">
                        <i class="fas fa-calendar-alt me-1"></i> Schedule
                    </button>
                </div>
            </td>
        </tr>
    `;
                });
            }

            $('#rankingResults').html(html);

            // Initialize or refresh DataTable
            if (!$.fn.DataTable.isDataTable('#rankingTable')) {
                $('#rankingTable').DataTable({
                    responsive: true,
                    columnDefs: [{
                        orderable: false,
                        targets: [8]
                    }], // Disable sorting for actions column
                    order: [
                        [0, 'asc']
                    ] // Default sort by rank
                });
            } else {
                // Destroy and reinitialize for fresh data
                $('#rankingTable').DataTable().destroy();
                $('#rankingTable').DataTable({
                    responsive: true,
                    columnDefs: [{
                        orderable: false,
                        targets: [8]
                    }],
                    order: [
                        [0, 'asc']
                    ]
                });
            }
        }
        // Handle "Start New Process" button
        $startNewBtn.on('click', function() {
            // Reset form and navigate to step 1
            $('#ahpForm')[0].reset();

            // Clear AHP data
            Object.keys(ahpData).forEach(key => {
                ahpData[key] = {};
            });

            // Reset UI elements
            $('#selectedCriteriaContainer').empty();
            $('#configurationStatus').empty();
            $('#subCriteriaConfigBtns').empty();
            $('#mainCriteriaSliders').empty();

            // Clear DataTable
            if ($.fn.DataTable.isDataTable('#rankingTable')) {
                $('#rankingTable').DataTable().clear().draw();
            }

            // Go to first step
            navigateToStep(1);
        });






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

<style>
    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease-in-out;
    }


    /* Pastikan container utama terlihat */
    #container-body {
        opacity: 1;
        display: block;
        animation: fadeIn 0.5s ease;
    }

    /* Animasi untuk muncul perlahan */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    /* Base Styles */
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
        min-height: 100vh;
    }

    /* Animation Keyframes */
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

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    /* Card Styling */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px rgba(0, 0, 0, 0.12);
    }

    .card-header {
        border-bottom: none;
        padding: 1.25rem 1.5rem;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }

    /* Progress Steps - Modern Design */
    .steps-container {
        padding: 30px 0;
        position: relative;
    }

    .step-progress {
        display: flex;
        justify-content: space-between;
        position: relative;
        max-width: 900px;
        margin: 0 auto;
    }

    .step-progress::before {
        content: '';
        position: absolute;
        top: 25px;
        left: 12%;
        right: 12%;
        height: 4px;
        background: #e0e6f5;
        z-index: 0;
        border-radius: 2px;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 1;
        flex: 1;
        text-align: center;
    }

    .step-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #e0e6f5;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px auto;
        font-size: 20px;
        border: 4px solid white;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        position: relative;
    }

    .step-label {
        font-size: 14px;
        color: #6c757d;
        text-align: center;
        font-weight: 500;
        width: 100%;
        padding: 0 10px;
    }

    .step.active .step-icon {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        transform: scale(1.1);
    }

    .step.active .step-label {
        color: #224abe;
        font-weight: 600;
    }

    .step.completed .step-icon {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        color: white;
    }

    .step.completed .step-label {
        color: #13855c;
    }

    .step.active,
    .step.completed {
        transform: none;
    }

    .step-icon-container {
        width: 55px;
        height: 55px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Content Step Display */
    .step-content {
        display: none;
        animation: fadeIn 0.5s ease;
    }

    .step-content.active {
        display: block;
    }

    /* Button Styles */
    .btn {
        border-radius: 8px;
        padding: 0.5rem 1.25rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .btn-success {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #858796 0%, #60616f 100%);
    }

    /* Table Styles */
    .table {
        border-radius: 10px;
        overflow: hidden;
    }

    .table thead th {
        background-color: #4e73df;
        color: white;
        border: none;
        font-weight: 600;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(78, 115, 223, 0.05);
        transform: translateX(5px);
    }

    /* Range Slider Customization */
    .form-range::-webkit-slider-thumb {
        background: #4e73df;
        width: 20px;
        height: 20px;
    }

    .form-range::-moz-range-thumb {
        background: #4e73df;
        width: 20px;
        height: 20px;
    }

    /* Badge Styles */
    .badge {
        border-radius: 20px;
        padding: 0.5em 1em;
        font-weight: 500;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Alert Styles */
    .alert {
        border-radius: 8px;
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    /* Modal Styles */
    .modal-content {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        border-bottom: none;
        padding: 1.5rem;
    }

    .modal-title {
        font-weight: 600;
    }

    .modal-body {
        padding: 2rem;
    }

    .modal-footer {
        background-color: #f8f9fa;
        border-top: none;
        padding: 1.5rem;
        justify-content: flex-end;
    }

    /* Form Elements */
    .form-select,
    .form-control {
        border-radius: 8px;
        padding: 0.5rem 1rem;
        border: 1px solid #e0e6f5;
        transition: all 0.3s ease;
    }

    .form-select:focus,
    .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }

    /* Range Configuration */
    .range-configuration {
        background-color: #f8f9fa;
        padding: 0;

        border-radius: 10px;
        margin-top: 0;
    }

    #comparisonSection {
        background-color: #f8f9fa;
        padding: 0;

        border-radius: 10px;
        margin-top: 1.5rem;
    }


    .range-item {
        background-color: white;
        border-radius: 10px;
        margin-bottom: 15px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .range-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    /* Comparison Sliders */
    .slider-container {
        padding: 15px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .slider-container small {
        color: #6c757d;
        font-size: 0.8rem;
    }

    /* Progress Bar */
    .progress {
        height: 24px;
        border-radius: 12px;
        background-color: #e9ecef;
    }

    .progress-bar {
        border-radius: 12px;
        font-weight: 500;
    }

    /* Button Groups */
    .d-flex.gap-2 {
        gap: 10px !important;
    }

    .calculate-pulse {
        animation: pulse 1.5s infinite;
    }

    /* Range Slider in Modal */
    #rangeSliders .range-item {
        margin-bottom: 1rem;
        padding: 1rem;
    }

    #rangeSliders .input-group-text {
        background-color: #f8f9fa;
        border: 1px solid #e0e6f5;
    }



    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .step-progress {
            flex-wrap: wrap;
        }

        .step {
            flex: 0 0 50%;
            margin-bottom: 20px;
        }

        .step-progress::before {
            display: none;
        }
    }
</style>