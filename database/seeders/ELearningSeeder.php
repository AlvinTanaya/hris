<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\elearning_lesson;
use App\Models\elearning_question;
use App\Models\elearning_schedule;
use App\Models\elearning_invitation;
use App\Models\elearning_answer;
use App\Models\User;
use Carbon\Carbon;

class ELearningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Start seeding data

        // Create e-learning lessons
        $lessons = [
            [
                'name' => 'Safety Protocols in Steel Manufacturing',
                'duration' => 60,
                'passing_grade' => 75,
                'lesson_file' => 'elearning_lesson_material/Tesing_Demo.pdf',
                'created_at' => Carbon::now()->subMonths(6),
                'updated_at' => Carbon::now()->subMonths(6)
            ],
            [
                'name' => 'Quality Control Standards',
                'duration' => 90,
                'passing_grade' => 80,
                'lesson_file' => 'elearning_lesson_material/Tesing_Demo.pdf',
                'created_at' => Carbon::now()->subMonths(5),
                'updated_at' => Carbon::now()->subMonths(5)
            ],
            [
                'name' => 'Operational Efficiency Techniques',
                'duration' => 120,
                'passing_grade' => 70,
                'lesson_file' => 'elearning_lesson_material/Tesing_Demo.pdf',
                'created_at' => Carbon::now()->subMonths(4),
                'updated_at' => Carbon::now()->subMonths(4)
            ],
            [
                'name' => 'Introduction to Steel Production',
                'duration' => 45,
                'passing_grade' => 65,
                'lesson_file' => 'elearning_lesson_material/Tesing_Demo.pdf',
                'created_at' => Carbon::now()->subMonths(3),
                'updated_at' => Carbon::now()->subMonths(3)
            ],
            [
                'name' => 'Workplace Communication Skills',
                'duration' => 75,
                'passing_grade' => 70,
                'lesson_file' => 'elearning_lesson_material/Tesing_Demo.pdf',
                'created_at' => Carbon::now()->subMonths(2),
                'updated_at' => Carbon::now()->subMonths(2)
            ],
            [
                'name' => 'Digital Tools for Steel Industry',
                'duration' => 60,
                'passing_grade' => 75,
                'lesson_file' => 'elearning_lesson_material/Tesing_Demo.pdf',
                'created_at' => Carbon::now()->subMonths(2)->subDays(15),
                'updated_at' => Carbon::now()->subMonths(2)->subDays(15)
            ],
            [
                'name' => 'Environmental Compliance in Manufacturing',
                'duration' => 90,
                'passing_grade' => 80,
                'lesson_file' => 'elearning_lesson_material/Tesing_Demo.pdf',
                'created_at' => Carbon::now()->subMonths(1),
                'updated_at' => Carbon::now()->subMonths(1)
            ],
            [
                'name' => 'Leadership Skills for Team Leads',
                'duration' => 120,
                'passing_grade' => 85,
                'lesson_file' => 'elearning_lesson_material/Tesing_Demo.pdf',
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(20)
            ],
            [
                'name' => 'Supply Chain Management',
                'duration' => 90,
                'passing_grade' => 75,
                'lesson_file' => 'elearning_lesson_material/Tesing_Demo.pdf',
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(15)
            ],
            [
                'name' => 'Industrial Safety Standards',
                'duration' => 60,
                'passing_grade' => 80,
                'lesson_file' => 'elearning_lesson_material/Tesing_Demo.pdf',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10)
            ]
        ];

        $lessonIds = [];
        foreach ($lessons as $lesson) {
            $createdLesson = elearning_lesson::create($lesson);
            $lessonIds[] = $createdLesson->id;
        }

        // Create questions for each lesson (10 questions per lesson)
        foreach ($lessonIds as $lessonId) {
            // Generate 10 questions per lesson
            for ($i = 1; $i <= 10; $i++) {
                // Different questions based on lesson ID to make it more realistic
                switch ($lessonId % 10) {
                    case 0:
                        $questionText = "What is the correct procedure for safety protocol #$i?";
                        $choices = "Follow manual instructions;Contact supervisor immediately;Use protective equipment;All of the above";
                        $answer = "All of the above";
                        break;
                    case 1:
                        $questionText = "Which quality control method is most effective for testing #$i?";
                        $choices = "Visual inspection;Automated testing;Manual sampling;Batch verification;Statistical analysis";
                        $answer = "Automated testing";
                        break;
                    case 2:
                        $questionText = "In operational efficiency technique #$i, what is the first step?";
                        $choices = "Process mapping;Data collection;Team consultation;Resource allocation";
                        $answer = "Process mapping";
                        break;
                    case 3:
                        $questionText = "Which raw material is essential in steel production process #$i?";
                        $choices = "Iron ore;Coal;Limestone;All of the above";
                        $answer = "All of the above";
                        break;
                    case 4:
                        $questionText = "What communication technique works best in scenario #$i?";
                        $choices = "Direct verbal;Written documentation;Visual demonstration;Team meeting";
                        $answer = "Visual demonstration";
                        break;
                    case 5:
                        $questionText = "Which digital tool is recommended for task #$i?";
                        $choices = "ERP System;CAD Software;Production Monitoring App;Inventory Management Tool";
                        $answer = "Production Monitoring App";
                        break;
                    case 6:
                        $questionText = "What is the environmental impact of process #$i?";
                        $choices = "Carbon emissions;Water usage;Energy consumption;All of the above";
                        $answer = "All of the above";
                        break;
                    case 7:
                        $questionText = "Which leadership approach is most effective in situation #$i?";
                        $choices = "Directive;Supportive;Participative;Achievement-oriented;Transformational";
                        $answer = "Participative";
                        break;
                    case 8:
                        $questionText = "What is the key consideration in supply chain stage #$i?";
                        $choices = "Cost efficiency;Delivery time;Quality control;Supplier reliability";
                        $answer = "Supplier reliability";
                        break;
                    case 9:
                        $questionText = "Which safety standard applies to hazard #$i?";
                        $choices = "ISO 9001;OSHA Standard;Company Protocol;Industry Guideline";
                        $answer = "OSHA Standard";
                        break;
                }

                elearning_question::create([
                    'lesson_id' => $lessonId,
                    'question' => $questionText,
                    'multiple_choice' => $choices,
                    'answer_key' => $answer,
                    'grade' => 10, // 10 questions at 10 points each = 100 points total
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }

        // Create schedules for lessons across different years (2023-2025)
        $schedules = [
            // 2023 schedules
            [
                'lesson_id' => $lessonIds[0],
                'start_date' => Carbon::create(2023, 2, 15)->startOfDay(),
                'end_date' => Carbon::create(2023, 3, 15)->startOfDay(),
                'created_at' => Carbon::create(2023, 2, 10),
                'updated_at' => Carbon::create(2023, 2, 10)
            ],
            [
                'lesson_id' => $lessonIds[1],
                'start_date' => Carbon::create(2023, 4, 1)->startOfDay(),
                'end_date' => Carbon::create(2023, 5, 1)->startOfDay(),
                'created_at' => Carbon::create(2023, 3, 25),
                'updated_at' => Carbon::create(2023, 3, 25)
            ],
            [
                'lesson_id' => $lessonIds[2],
                'start_date' => Carbon::create(2023, 6, 10)->startOfDay(),
                'end_date' => Carbon::create(2023, 7, 10)->startOfDay(),
                'created_at' => Carbon::create(2023, 6, 5),
                'updated_at' => Carbon::create(2023, 6, 5)
            ],
            [
                'lesson_id' => $lessonIds[3],
                'start_date' => Carbon::create(2023, 9, 1)->startOfDay(),
                'end_date' => Carbon::create(2023, 10, 1)->startOfDay(),
                'created_at' => Carbon::create(2023, 8, 25),
                'updated_at' => Carbon::create(2023, 8, 25)
            ],
            [
                'lesson_id' => $lessonIds[4],
                'start_date' => Carbon::create(2023, 11, 15)->startOfDay(),
                'end_date' => Carbon::create(2023, 12, 15)->startOfDay(),
                'created_at' => Carbon::create(2023, 11, 10),
                'updated_at' => Carbon::create(2023, 11, 10)
            ],
            
            // 2024 schedules
            [
                'lesson_id' => $lessonIds[5],
                'start_date' => Carbon::create(2024, 1, 15)->startOfDay(),
                'end_date' => Carbon::create(2024, 2, 15)->startOfDay(),
                'created_at' => Carbon::create(2024, 1, 10),
                'updated_at' => Carbon::create(2024, 1, 10)
            ],
            [
                'lesson_id' => $lessonIds[6],
                'start_date' => Carbon::create(2024, 3, 1)->startOfDay(),
                'end_date' => Carbon::create(2024, 4, 1)->startOfDay(),
                'created_at' => Carbon::create(2024, 2, 25),
                'updated_at' => Carbon::create(2024, 2, 25)
            ],
            [
                'lesson_id' => $lessonIds[7],
                'start_date' => Carbon::create(2024, 6, 1)->startOfDay(),
                'end_date' => Carbon::create(2024, 7, 1)->startOfDay(),
                'created_at' => Carbon::create(2024, 5, 25),
                'updated_at' => Carbon::create(2024, 5, 25)
            ],
            [
                'lesson_id' => $lessonIds[8],
                'start_date' => Carbon::create(2024, 9, 15)->startOfDay(),
                'end_date' => Carbon::create(2024, 10, 15)->startOfDay(),
                'created_at' => Carbon::create(2024, 9, 10),
                'updated_at' => Carbon::create(2024, 9, 10)
            ],
            [
                'lesson_id' => $lessonIds[9],
                'start_date' => Carbon::create(2024, 11, 15)->startOfDay(),
                'end_date' => Carbon::create(2024, 12, 15)->startOfDay(),
                'created_at' => Carbon::create(2024, 11, 10),
                'updated_at' => Carbon::create(2024, 11, 10)
            ],
            
            // 2025 schedules
            [
                'lesson_id' => $lessonIds[0],
                'start_date' => Carbon::create(2025, 1, 15)->startOfDay(),
                'end_date' => Carbon::create(2025, 2, 15)->startOfDay(),
                'created_at' => Carbon::create(2025, 1, 10),
                'updated_at' => Carbon::create(2025, 1, 10)
            ],
            [
                'lesson_id' => $lessonIds[1],
                'start_date' => Carbon::create(2025, 3, 1)->startOfDay(),
                'end_date' => Carbon::create(2025, 4, 1)->startOfDay(),
                'created_at' => Carbon::create(2025, 2, 25),
                'updated_at' => Carbon::create(2025, 2, 25)
            ],
            // Special schedule with specific users (21 and 11)
            [
                'lesson_id' => $lessonIds[2],
                'start_date' => Carbon::create(2025, 5, 1)->startOfDay(),
                'end_date' => Carbon::create(2025, 8, 1)->startOfDay(),
                'created_at' => Carbon::create(2025, 4, 25),
                'updated_at' => Carbon::create(2025, 4, 25)
            ]
        ];

        $scheduleIds = [];
        $specialScheduleId = null;
        foreach ($schedules as $index => $schedule) {
            $createdSchedule = elearning_schedule::create($schedule);
            $scheduleIds[] = $createdSchedule->id;
            
            // Save special schedule ID (May 1 - Aug 1, 2025)
            if ($index == count($schedules) - 1) {
                $specialScheduleId = $createdSchedule->id;
            }
        }

        // Get active users
        $users = User::whereIn('employee_status', ['Full Time', 'Contract', 'Part Time'])
                     ->whereNull('exit_date')
                     ->get();

        if ($users->isEmpty()) {
            // If no users found, we'll create invitations without checking join_date
            // This is just a fallback for testing purposes
            $this->command->info('No active users found. Creating invitations without validation.');
            $users = User::limit(10)->get();
        }

        // Process invitations and answers for each schedule
        $completedInvitations = [];

        foreach ($scheduleIds as $index => $scheduleId) {
            // Get associated lesson and schedule
            $schedule = elearning_schedule::find($scheduleId);
            $lessonId = $schedule->lesson_id;
            
            if ($scheduleId == $specialScheduleId) {
                // Special handling for May-August 2025 schedule
                // Make sure users 11 and 21 are included
                $specialUsers = User::whereIn('id', [11, 21])->get();
                
                // If those users exist, create invitations for them
                foreach ($specialUsers as $user) {
                    elearning_invitation::create([
                        'lesson_id' => $lessonId,
                        'schedule_id' => $scheduleId,
                        'users_id' => $user->id,
                        'grade' => null, // Ongoing schedule
                        'created_at' => $schedule->created_at,
                        'updated_at' => $schedule->created_at
                    ]);
                }
                
                // Add 8 more random users (total ~10)
                $otherEligibleUsers = $users->filter(function ($user) use ($schedule, $specialUsers) {
                    // Exclude users 11 and 21 as they've already been added
                    if ($specialUsers->contains('id', $user->id)) {
                        return false;
                    }
                    
                    $joinDate = Carbon::parse($user->join_date);
                    $scheduleStart = Carbon::parse($schedule->start_date);
                    
                    // Check if user joined before schedule start
                    return $joinDate->lt($scheduleStart) && 
                           // Check if contract is valid during schedule (for Contract and Part Time)
                           ($user->employee_status === 'Full Time' || 
                            ($user->employee_status !== 'Full Time' && 
                             (is_null($user->contract_end_date) || 
                              Carbon::parse($user->contract_end_date)->gt($schedule->start_date))));
                });
                
                // Get random users, up to 8 more
                $additionalCount = min(8, $otherEligibleUsers->count());
                if ($additionalCount > 0) {
                    $additionalUsers = $otherEligibleUsers->random($additionalCount);
                    
                    foreach ($additionalUsers as $user) {
                        elearning_invitation::create([
                            'lesson_id' => $lessonId,
                            'schedule_id' => $scheduleId,
                            'users_id' => $user->id,
                            'grade' => null, // Ongoing schedule
                            'created_at' => $schedule->created_at,
                            'updated_at' => $schedule->created_at
                        ]);
                    }
                }
                
                // Skip further processing for this schedule since it's in the future
                continue;
            }
            
            // Regular schedule processing
            // Select users who joined before the schedule start date
            $eligibleUsers = $users->filter(function ($user) use ($schedule) {
                $joinDate = Carbon::parse($user->join_date);
                $scheduleStart = Carbon::parse($schedule->start_date);
                
                // Check if user joined before schedule start
                return $joinDate->lt($scheduleStart) && 
                       // Check if contract is valid during schedule (for Contract and Part Time)
                       ($user->employee_status === 'Full Time' || 
                        ($user->employee_status !== 'Full Time' && 
                         (is_null($user->contract_end_date) || 
                          Carbon::parse($user->contract_end_date)->gt($schedule->start_date))));
            });
            
            // If no eligible users found, skip this schedule
            if ($eligibleUsers->isEmpty()) {
                continue;
            }
            
            // Assign this schedule to around 10 random eligible users
            $userCount = min(rand(8, 12), $eligibleUsers->count());
            $selectedUsers = $eligibleUsers->random($userCount);
            
            foreach ($selectedUsers as $user) {
                // Create invitation
                $grade = null;
                
                // For older schedules (completed ones), assign grades to some users
                $now = Carbon::now();
                $scheduleEndDate = Carbon::parse($schedule->end_date);
                
                if ($scheduleEndDate->lt($now)) {
                    // Completed schedule
                    // 80% chance of attempting the course
                    if (rand(1, 100) <= 80) {
                        // Random grade between 0-100 (includes failing grades)
                        $grade = rand(0, 100);
                        
                        $invitation = elearning_invitation::create([
                            'lesson_id' => $lessonId,
                            'schedule_id' => $scheduleId,
                            'users_id' => $user->id,
                            'grade' => $grade,
                            'created_at' => $schedule->created_at,
                            'updated_at' => $scheduleEndDate->subDays(rand(1, 10))
                        ]);
                        
                        $completedInvitations[] = [
                            'invitation_id' => $invitation->id,
                            'lesson_id' => $lessonId,
                            'schedule_id' => $scheduleId,
                            'users_id' => $user->id,
                            'grade' => $grade
                        ];
                    } else {
                        // Did not attempt the course
                        elearning_invitation::create([
                            'lesson_id' => $lessonId,
                            'schedule_id' => $scheduleId,
                            'users_id' => $user->id,
                            'grade' => null,
                            'created_at' => $schedule->created_at,
                            'updated_at' => $schedule->created_at
                        ]);
                    }
                } else {
                    // Ongoing or future schedule
                    elearning_invitation::create([
                        'lesson_id' => $lessonId,
                        'schedule_id' => $scheduleId,
                        'users_id' => $user->id,
                        'grade' => null,
                        'created_at' => $schedule->created_at,
                        'updated_at' => $schedule->created_at
                    ]);
                }
            }
        }

        // Create answers for completed invitations with variations
        foreach ($completedInvitations as $completedInv) {
            // Get questions for this lesson
            $questions = elearning_question::where('lesson_id', $completedInv['lesson_id'])->get();
            $totalQuestions = $questions->count();
            
            // Decide how many questions the user attempted (variation in completion)
            // Some users may not complete all questions
            $questionsAttempted = rand(ceil($totalQuestions * 0.5), $totalQuestions);
            
            // Calculate how many questions the user got correct based on their final grade
            $userGrade = $completedInv['grade'];
            $correctAnswersNeeded = ceil(($userGrade / 100) * $questionsAttempted);
            
            // Process questions
            $questionCounter = 0;
            foreach ($questions as $question) {
                $questionCounter++;
                
                // Check if this question was attempted
                if ($questionCounter <= $questionsAttempted) {
                    $userAnswer = '';
                    $mark = 0;
                    
                    // Determine if this answer should be correct (to match the final grade)
                    $shouldBeCorrect = $questionCounter <= $correctAnswersNeeded;
                    
                    if ($shouldBeCorrect) {
                        // User answered correctly
                        $userAnswer = $question->answer_key;
                        $mark = $question->grade;
                    } else {
                        // User answered incorrectly - select a wrong option
                        $choices = explode(';', $question->multiple_choice);
                        $wrongChoices = array_diff($choices, [$question->answer_key]);
                        
                        if (!empty($wrongChoices)) {
                            $wrongChoicesArray = array_values($wrongChoices);
                            $userAnswer = $wrongChoicesArray[array_rand($wrongChoicesArray)];
                        } else {
                            // Fallback in case there's only one choice
                            $userAnswer = 'Incorrect answer';
                        }
                        $mark = 0;
                    }
                    
                    // Create the answer record
                    elearning_answer::create([
                        'invitation_id' => $completedInv['invitation_id'],
                        'lesson_id' => $completedInv['lesson_id'],
                        'schedule_id' => $completedInv['schedule_id'],
                        'users_id' => $completedInv['users_id'],
                        'question' => $question->question,
                        'multiple_choice' => $question->multiple_choice,
                        'answer_key' => $question->answer_key,
                        'answer' => $userAnswer,
                        'grade' => $question->grade,
                        'mark' => $mark,
                        'created_at' => Carbon::parse(elearning_invitation::find($completedInv['invitation_id'])->updated_at)->subMinutes(rand(5, 30)),
                        'updated_at' => Carbon::parse(elearning_invitation::find($completedInv['invitation_id'])->updated_at)->subMinutes(rand(1, 5))
                    ]);
                }
                // Unattempted questions don't get an answer record
            }
        }
    }
}