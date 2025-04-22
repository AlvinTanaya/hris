<?php

namespace App\Exports;

use App\Models\User;
use App\Models\EvaluationPerformance;
use App\Models\EvaluationPerformanceMessage;
use App\Models\RuleEvaluationWeightPerformance;
use App\Models\RuleEvaluationReductionPerformance;
use App\Models\WarningLetter;
use App\Models\RulePerformanceGrade;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;

class PerformanceEvaluationExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithCustomStartCell, WithEvents
{
    protected $userId;
    protected $year;
    protected $rowCount;
    protected $criteriaCount;
    protected $warningLetterCount;

    public function __construct($userId, $year)
    {
        $this->userId = $userId;
        $this->year = $year;
        $this->rowCount = 0;
        $this->criteriaCount = 0;
        $this->warningLetterCount = 0;
    }

    public function collection()
    {
        // Get user with position and department
        $user = User::with(['position', 'department'])->findOrFail($this->userId);
        
        // Month names for display
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        // Get all active criteria types and weights
        $criteria = RuleEvaluationWeightPerformance::with('criteria')
            ->where('Status', "Active")
            ->get()
            ->groupBy('criteria.type')
            ->map(function ($items) {
                return [
                    'id' => $items->first()->id,
                    'name' => $items->first()->criteria->type,
                    'weight' => $items->first()->weight
                ];
            })
            ->values();

        $criteriaList = $criteria->toArray();
        $this->criteriaCount = count($criteriaList);
        
        // Initialize monthly data structure - using 1-based indexing for consistency
        $monthlyData = [];
        foreach (range(1, 12) as $monthNumber) {
            $monthlyData[$monthNumber] = [
                'scores' => [],
                'rawScore' => 0,
                'finalScore' => 0,
                'deductions' => 0
            ];
        }
        
        // Get all evaluations for the user in the selected year
        $evaluations = EvaluationPerformance::with(['details.weightPerformance.criteria', 'reductions.warningLetter'])
            ->where('user_id', $user->id)
            ->whereYear('date', $this->year)
            ->get();
            
        // Process each month
        foreach (range(1, 12) as $monthNumber) {
            $monthEvaluations = $evaluations->filter(function ($eval) use ($monthNumber) {
                // Ensure date is parsed as Carbon instance
                $date = is_string($eval->date) ? Carbon::parse($eval->date) : $eval->date;
                return $date->month == $monthNumber;
            });

            if ($monthEvaluations->isNotEmpty()) {
                // Process criteria scores
                foreach ($criteriaList as $criterion) {
                    $values = [];
                    $scores = [];

                    foreach ($monthEvaluations as $eval) {
                        $details = $eval->details->filter(function ($detail) use ($criterion) {
                            return $detail->weightPerformance->criteria->type == $criterion['name'];
                        });

                        foreach ($details as $detail) {
                            $values[] = $detail->value;
                            $scores[] = $detail->value * $criterion['weight'];
                        }
                    }

                    if (!empty($values)) {
                        $avgValue = array_sum($values) / count($values);
                        $avgScore = array_sum($scores) / count($scores);

                        $monthlyData[$monthNumber]['scores'][] = [
                            'name' => $criterion['name'],
                            'value' => $avgValue,
                            'score' => $avgScore
                        ];

                        $monthlyData[$monthNumber]['rawScore'] += $avgScore;
                    }
                }

                // Calculate deductions for this month
                $monthlyData[$monthNumber]['deductions'] = $monthEvaluations->sum(function ($eval) {
                    return $eval->reductions->sum('reduction_amount');
                });

                $monthlyData[$monthNumber]['finalScore'] = max(
                    0,
                    $monthlyData[$monthNumber]['rawScore'] - $monthlyData[$monthNumber]['deductions']
                );
            }
        }
        
        // Calculate overall averages for individual criteria
        $averageValues = [];
        $averageTotals = [];

        foreach ($criteriaList as $criterion) {
            $sumValues = 0;
            $sumScores = 0;
            $count = 0;

            foreach ($monthlyData as $month) {
                $criterionData = collect($month['scores'])->firstWhere('name', $criterion['name']);
                if ($criterionData) {
                    $sumValues += $criterionData['value'];
                    $sumScores += $criterionData['score'];
                    $count++;
                }
            }

            $averageValues[$criterion['name']] = $count ? $sumValues / $count : 0;
            $averageTotals[$criterion['name']] = $count ? $sumScores / $count : 0;
        }
        
        // Process yearly reductions
        $reductionRules = RuleEvaluationReductionPerformance::where('Status', "Active")->get();
        $maxPossibleDeductions = $reductionRules->sum('weight');
        $yearlyReductions = [];
        $totalDeductions = 0;

        foreach ($reductionRules as $rule) {
            // Initialize the rule data structure with months 1-12
            $ruleData = [
                'id' => $rule->id,
                'name' => $rule->warningLetterRule->name ?? $rule->name,
                'weight' => $rule->weight,
                'monthly' => array_fill(1, 12, ['count' => 0, 'reduction' => 0]),
                'total_count' => 0,
                'total_reduction' => 0
            ];

            // Get warning letters that have actually been applied to evaluations
            $warningLetters = WarningLetter::where('user_id', $user->id)
                ->where('type_id', $rule->type_id)
                ->whereYear('created_at', $this->year)
                ->whereHas('evaluationReductions')
                ->get();

            foreach ($warningLetters as $letter) {
                // Ensure we're getting the correct month number
                $monthNumber = $letter->date ? $letter->date->month : ($letter->created_at ? $letter->created_at->month : now()->month);

                // Sum only the actual reductions applied
                $reductionAmount = $letter->evaluationReductions->sum('reduction_amount');

                $ruleData['monthly'][$monthNumber]['count']++;
                $ruleData['monthly'][$monthNumber]['reduction'] += $reductionAmount;
                $ruleData['total_count']++;
                $ruleData['total_reduction'] += $reductionAmount;
            }

            $yearlyReductions[$rule->id] = $ruleData;
            $totalDeductions += $ruleData['total_reduction'];
        }
        
        $this->warningLetterCount = count($yearlyReductions);
        
        // Calculate total possible score
        $totalPossible = $criteria->sum('weight') * 3; // Assuming max score per criterion is 3
        
        // Calculate the overall raw average and final score
        $overallRawAverage = 0;
        $totalMonthsWithData = 0;
        
        foreach ($monthlyData as $month) {
            if ($month['rawScore'] > 0) {
                $overallRawAverage += $month['rawScore'];
                $totalMonthsWithData++;
            }
        }
        
        // If there are months with data, calculate the average
        if ($totalMonthsWithData > 0) {
            $overallRawAverage = $overallRawAverage / $totalMonthsWithData;
        }
        
        // Calculate the final score as the sum of all criteria averages minus total deductions
        $overallAverage = max(0, $overallRawAverage - $totalDeductions);
        
        // Get the grade
        $finalScore = $overallAverage;
        $grade = RulePerformanceGrade::query()
            ->where(function ($q) use ($finalScore) {
                $q->where(function ($sub) use ($finalScore) {
                    $sub->whereNotNull('min_score')
                        ->whereNotNull('max_score')
                        ->where('min_score', '<=', $finalScore)
                        ->where('max_score', '>=', $finalScore);
                })->orWhere(function ($sub) use ($finalScore) {
                    $sub->whereNotNull('min_score')
                        ->whereNull('max_score')
                        ->where('min_score', '<=', $finalScore);
                })->orWhere(function ($sub) use ($finalScore) {
                    $sub->whereNull('min_score')
                        ->whereNotNull('max_score')
                        ->where('max_score', '>=', $finalScore);
                });
            })
            ->orderBy('min_score', 'asc')
            ->first();

        // Fallback if no grade matches
        if (!$grade) {
            $grade = (object)[
                'grade' => '?',
                'description' => 'Undefined performance'
            ];
        }
        
        // Create header row for company info
        $companyHeaderRow = [
            'PT. TIMUR JAYA INDOSTEEL - PERFORMANCE EVALUATION', '', '', '', '', '', 
            'YEAR:', $this->year, '', '', '', '', '', '', ''
        ];
        
        // Create employee info rows
        $employeeInfoRows = [
            ['Employee:', $user->name, '', '', '', '', 'Final Score:', number_format($overallAverage, 0)],
            ['Position:', $user->position ? $user->position->position : 'N/A', '', '', '', '', 'Grade:', $grade->grade],
            ['Department:', $user->department ? $user->department->department : 'N/A']
        ];
        
        // Create criteria heading rows
        $criteriaHeadingRow1 = ['#', 'CRITERIA', 'WEIGHT'];
        $criteriaHeadingRow2 = ['', '', ''];
        
        foreach ($monthNames as $month) {
            $criteriaHeadingRow1[] = strtoupper($month);
            $criteriaHeadingRow1[] = '';
            $criteriaHeadingRow2[] = 'SCORE';
            $criteriaHeadingRow2[] = 'TOTAL';
        }
        
        $criteriaHeadingRow1[] = 'FINAL';
        $criteriaHeadingRow1[] = '';
        $criteriaHeadingRow2[] = 'SCORE';
        $criteriaHeadingRow2[] = 'TOTAL';
        
        // Create data rows for criteria
        $dataRows = [];
        
        foreach ($criteriaList as $index => $criterion) {
            $row = [
                $index + 1,
                $criterion['name'],
                $criterion['weight']
            ];
            
            // Add data for each month
            foreach (range(1, 12) as $monthNumber) {
                $criteriaScore = collect($monthlyData[$monthNumber]['scores'])->firstWhere('name', $criterion['name']);
                $value = $criteriaScore['value'] ?? null;
                $total = $criteriaScore['score'] ?? null;
                
                $row[] = $value !== null ? number_format($value, 1) : '';
                $row[] = $total !== null ? number_format($total, 0) : '';
            }
            
            // Add final values
            $finalValue = $averageValues[$criterion['name']] ?? 0;
            $finalTotal = $averageTotals[$criterion['name']] ?? 0;
            
            $row[] = number_format($finalValue, 1);
            $row[] = number_format($finalTotal, 0);
            
            $dataRows[] = $row;
        }
        
        // Total evaluation score row
        $totalEvalRow = [
            '',
            'TOTAL EVALUATION SCORE',
            $totalPossible
        ];
        
        foreach (range(1, 12) as $monthNumber) {
            $totalEvalRow[] = '';
            $totalEvalRow[] = $monthlyData[$monthNumber]['rawScore'] ? number_format($monthlyData[$monthNumber]['rawScore'], 0) : '';
        }
        
        $totalEvalRow[] = '';
        $totalEvalRow[] = number_format($overallRawAverage, 0);
        
        $dataRows[] = $totalEvalRow;
        
        // Warning letters header
        $warningLetterHeader = ['WARNING LETTERS & DEDUCTIONS'];
        // Fill the rest of the columns with empty strings
        for ($i = 0; $i < count($criteriaHeadingRow1) - 1; $i++) {
            $warningLetterHeader[] = '';
        }
        $dataRows[] = $warningLetterHeader;
        
        // Warning letter rows
        foreach ($yearlyReductions as $ruleId => $ruleData) {
            $warningRow = [
                '',
                $ruleData['name'],
                '-' . $ruleData['weight']
            ];
            
            foreach (range(1, 12) as $monthNumber) {
                $monthData = $ruleData['monthly'][$monthNumber] ?? ['count' => 0, 'reduction' => 0];
                $hasDeduction = $monthData['count'] > 0;
                
                $warningRow[] = $hasDeduction ? $monthData['count'] : '';
                $warningRow[] = $hasDeduction ? '-' . $monthData['reduction'] : '';
            }
            
            $warningRow[] = $ruleData['total_count'] > 0 ? $ruleData['total_count'] : '';
            $warningRow[] = $ruleData['total_count'] > 0 ? '-' . $ruleData['total_reduction'] : '';
            
            $dataRows[] = $warningRow;
        }
        
        // Total deductions row
        $totalDeductionRow = [
            '',
            'TOTAL DEDUCTIONS',
            '-' . $maxPossibleDeductions
        ];
        
        foreach (range(1, 12) as $monthNumber) {
            $monthDeduction = $monthlyData[$monthNumber]['deductions'] ?? 0;
            $totalDeductionRow[] = '';
            $totalDeductionRow[] = $monthDeduction > 0 ? '-' . number_format($monthDeduction, 0) : '';
        }
        
        $totalDeductionRow[] = '';
        $totalDeductionRow[] = $totalDeductions > 0 ? '-' . number_format($totalDeductions, 0) : '';
        
        $dataRows[] = $totalDeductionRow;
        
        // Final score row
        $finalScoreRow = [
            '',
            'FINAL SCORE',
            ''
        ];
        
        foreach (range(1, 12) as $monthNumber) {
            $finalScoreRow[] = '';
            $finalScoreRow[] = $monthlyData[$monthNumber]['finalScore'] ? number_format($monthlyData[$monthNumber]['finalScore'], 0) : '';
        }
        
        $finalScoreRow[] = '';
        $finalScoreRow[] = number_format($overallAverage, 0);
        
        $dataRows[] = $finalScoreRow;
        
        // Get evaluation messages
        $evaluationMessages = EvaluationPerformanceMessage::whereIn(
            'evaluation_id',
            EvaluationPerformance::where('user_id', $user->id)
                ->whereYear('date', $this->year)
                ->pluck('id')
        )
            ->select(['id', 'message', 'created_at', 'evaluation_id'])
            ->orderBy('created_at', 'asc')
            ->get();
            
        // Comments section if there are any messages
        $commentsRows = [];
        if ($evaluationMessages->count() > 0) {
            $commentsRows[] = ['EVALUATOR\'S COMMENTS:'];
            $commentsRows[] = ['#', 'COMMENT', 'DATE'];
            
            foreach ($evaluationMessages as $index => $message) {
                $commentsRows[] = [
                    $index + 1,
                    $message->message,
                    $message->created_at->format('d M Y H:i')
                ];
            }
        }
        
        // Combine all rows
        $allRows = array_merge(
            [$companyHeaderRow],
            $employeeInfoRows,
            [['']],  // Empty row for spacing
            [$criteriaHeadingRow1],
            [$criteriaHeadingRow2],
            $dataRows,
            [['']],  // Empty row for spacing
            $commentsRows
        );
        
        $this->rowCount = count($allRows);
        
        return new Collection($allRows);
    }
    
    public function headings(): array
    {
        // We're using custom cells, so we return an empty array
        return [];
    }
    
    public function title(): string
    {
        return 'Performance Evaluation';
    }
    
    public function startCell(): string
    {
        return 'A1';
    }
    
    public function styles(Worksheet $sheet)
    {
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(10);
        
        // Set column widths for months (pairs of score/total columns)
        for ($i = 0; $i < 12; $i++) {
            $scoreCol = chr(68 + ($i * 2));  // D, F, H, etc.
            $totalCol = chr(69 + ($i * 2));  // E, G, I, etc.
            
            $sheet->getColumnDimension($scoreCol)->setWidth(7);
            $sheet->getColumnDimension($totalCol)->setWidth(7);
        }
        
        // Set column widths for final columns
        $finalScoreCol = chr(68 + (12 * 2));
        $finalTotalCol = chr(69 + (12 * 2));
        $sheet->getColumnDimension($finalScoreCol)->setWidth(7);
        $sheet->getColumnDimension($finalTotalCol)->setWidth(7);
        
        return [
            // No specific styles here as we will apply them in the afterSheet event
        ];
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Style the company header
                $sheet->mergeCells('A1:F1');
                $sheet->getStyle('A1:F1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                
                // Style employee info section
                $sheet->getStyle('A2:B4')->getFont()->setBold(true);
                $sheet->getStyle('G2:G4')->getFont()->setBold(true);
                $sheet->getStyle('H2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('H3')->getFont()->setBold(true)->setSize(12);
                
                // Style criteria headings
                $criteriaStartRow = 6;
                $criteriaEndCol = chr(67 + (12 * 2)); // Column after the final total
                
                // First heading row
                $sheet->getStyle("A{$criteriaStartRow}:{$criteriaEndCol}{$criteriaStartRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '000000']
                    ],
                    'font' => [
                        'color' => ['rgb' => 'FFFFFF'],
                        'bold' => true
                    ]
                ]);
                
                // Merge month heading cells
                for ($i = 0; $i < 12; $i++) {
                    $startCol = chr(68 + ($i * 2));
                    $endCol = chr(69 + ($i * 2));
                    $sheet->mergeCells("{$startCol}{$criteriaStartRow}:{$endCol}{$criteriaStartRow}");
                }
                
                // Merge final heading cells
                $finalStartCol = chr(68 + (12 * 2));
                $finalEndCol = chr(69 + (12 * 2));
                $sheet->mergeCells("{$finalStartCol}{$criteriaStartRow}:{$finalEndCol}{$criteriaStartRow}");
                
                // Second heading row
                $secondHeadingRow = $criteriaStartRow + 1;
                $sheet->getStyle("A{$secondHeadingRow}:{$criteriaEndCol}{$secondHeadingRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '000000']
                    ],
                    'font' => [
                        'color' => ['rgb' => 'FFFFFF'],
                        'bold' => true
                    ]
                ]);
                
                // Style data rows
                $dataStartRow = $secondHeadingRow + 1;
                $dataEndRow = $dataStartRow + $this->criteriaCount - 1;
                
                $sheet->getStyle("A{$dataStartRow}:C{$dataEndRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN
                        ]
                    ]
                ]);
                
                // Style the totals row
                $totalRow = $dataEndRow + 1;
                $sheet->getStyle("A{$totalRow}:{$criteriaEndCol}{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F0F0F0']
                    ]
                ]);
                
                // Style the warning letter header
                $warningHeaderRow = $totalRow + 1;
                $sheet->mergeCells("A{$warningHeaderRow}:{$criteriaEndCol}{$warningHeaderRow}");
                $sheet->getStyle("A{$warningHeaderRow}:{$criteriaEndCol}{$warningHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FF0000']
                    ],
                    'font' => [
                        'color' => ['rgb' => 'FFFFFF'],
                        'bold' => true
                    ]
                ]);
                
                // Style warning letter rows
                $warningStartRow = $warningHeaderRow + 1;
                $warningEndRow = $warningStartRow + $this->warningLetterCount - 1;
                
                // Style the total deductions row
                $deductionsRow = $warningEndRow + 1;
                $sheet->getStyle("A{$deductionsRow}:{$criteriaEndCol}{$deductionsRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F0F0F0']
                    ]
                ]);
                
                // Style the final score row
                $finalScoreRow = $deductionsRow + 1;
                $sheet->getStyle("A{$finalScoreRow}:{$criteriaEndCol}{$finalScoreRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F0F0F0']
                    ],
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_MEDIUM
                        ]
                    ]
                ]);
                
                // Center align all cells in the criteria table
                $sheet->getStyle("A{$criteriaStartRow}:{$criteriaEndCol}{$finalScoreRow}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                
                // Left align criteria names
                $sheet->getStyle("B{$dataStartRow}:B{$finalScoreRow}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                
                // Comments section styling if present
                if ($this->rowCount > $finalScoreRow + 2) {
                    $commentsHeaderRow = $finalScoreRow + 2;
                    $sheet->getStyle("A{$commentsHeaderRow}")->getFont()->setBold(true);
                    
                    $commentsTableHeaderRow = $commentsHeaderRow + 1;
                    $sheet->getStyle("A{$commentsTableHeaderRow}:C{$commentsTableHeaderRow}")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '000000']
                        ],
                        'font' => [
                            'color' => ['rgb' => 'FFFFFF'],
                            'bold' => true
                        ]
                    ]);
                    
                    // Apply border to comments table
                    $commentsDataStartRow = $commentsTableHeaderRow + 1;
                    $commentsDataEndRow = $this->rowCount;
                    $sheet->getStyle("A{$commentsTableHeaderRow}:C{$commentsDataEndRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN
                            ]
                        ]
                    ]);
                    
                    // Set column widths for comments
                    $sheet->getColumnDimension('A')->setWidth(5);
                    $sheet->getColumnDimension('B')->setWidth(80);
                    $sheet->getColumnDimension('C')->setWidth(20);
                    
                    // Left align comments text
                    $sheet->getStyle("B{$commentsDataStartRow}:B{$commentsDataEndRow}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                        ->setWrapText(true);
                }
                
                // Set print area to include all content
                $sheet->getPageSetup()->setPrintArea("A1:{$criteriaEndCol}{$this->rowCount}");
                
                // Set page to landscape and fit to width
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                
                // Add page headers and footers
                $sheet->getHeaderFooter()
                    ->setOddHeader('&C&B Performance Evaluation Report - ' . $this->year)
                    ->setOddFooter('&L&B' . date('d-m-Y') . '&R&BPage &P of &N');
            }
        ];
    }
}