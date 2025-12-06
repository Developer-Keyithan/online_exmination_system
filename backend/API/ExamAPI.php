<?php
use Backend\Modal\Auth;

class ExamAPI
{
    private $db;
    public function __construct()
    {
        $this->db = db();
    }

    public function saveExamBasicInfo()
    {
        try {
            // Retrieve and validate input data
            $examTitle = $_POST['examTitle'] ?? null;
            $examCode = $_POST['examCode'] ?? null;
            $examDuration = $_POST['examDuration'] ?? null;
            $totalMarks = $_POST['totalMarks'] ?? null;
            $passingMarks = $_POST['passingMarks'] ?? null;
            $instructions = $_POST['instructions'] ?? '';
            $examStatus = $_POST['examStatus'] ?? 0;
            $totalQuestions = $_POST['totalQuestions'] ?? null;

            if (!$examTitle || !$examCode || !$examDuration || !$totalMarks || !$passingMarks || !$totalQuestions) {
                throw new Exception("All required fields must be filled.");
            }

            if ($examStatus == 'sheduled') {
                $examStatus = 2;
            } else if ($examStatus == 'published') {
                $examStatus = 1;
            } else if ($examStatus == 'draft') {
                $examStatus = 0;
            } else {
                $examStatus = 0; // default to draft
            }

            // Save exam basic info to the database
            $stmt = $this->db->prepare("INSERT INTO exam_info (title, code, total_marks, duration, total_num_of_ques, passing_marks, instructions, created_by, status) VALUES (? , ? , ? , ? , ? , ? , ? , ?)");
            $stmt->execute([
                $examTitle,
                $examCode,
                $totalMarks,
                $examDuration,
                $totalQuestions,
                $passingMarks,
                $instructions,
                user_id(),
                $examStatus
            ]);
            $examId = $this->db->lastInsertId();
            $status = 'draft';
            if ($examStatus == 1) {
                $status = 'published';
            } else if ($examStatus == 2) {
                $status = 'sheduled';
            }
            $exam = [
                'id' => $examId,
                'title' => $examTitle,
                'code' => $examCode,
                'totalMarks' => $totalMarks,
                'totalQuestions' => $totalQuestions,
                'duration' => $examDuration,
                'passingMarks' => $passingMarks,
                'instructions' => $instructions,
                'status' => $status,
            ];

            return json_encode([
                'status' => 'success',
                'msg' => 'Exam basic information saved successfully.',
                'exam_id' => $examId,
                'exam' => $exam
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function editExamBasicInfo($id)
    {
        try {

            $fields = [];
            $params = [];

            if (!empty($_POST['examTitle'])) {
                $fields[] = "title = ?";
                $params[] = $_POST['examTitle'];
            }

            if (!empty($_POST['examCode'])) {
                $fields[] = "code = ?";
                $params[] = $_POST['examCode'];
            }

            if (!empty($_POST['examDuration'])) {
                $fields[] = "duration = ?";
                $params[] = $_POST['examDuration'];
            }

            if (!empty($_POST['totalMarks'])) {
                $fields[] = "total_marks = ?";
                $params[] = $_POST['totalMarks'];
            }

            if (!empty($_POST['passingMarks'])) {
                $fields[] = "passing_marks = ?";
                $params[] = $_POST['passingMarks'];
            }

            if (isset($_POST['examInstructions'])) {
                $fields[] = "instructions = ?";
                $params[] = $_POST['examInstructions'];
            }

            if (isset($_POST['examStatus'])) {
                $fields[] = "status = ?";
                $params[] = $_POST['examStatus'];
            }

            if (!empty($_POST['totalQuestions'])) {
                $fields[] = "total_num_of_ques = ?";
                $params[] = $_POST['totalQuestions'];
            }

            $exam = [
                'id' => $id,
                'title' => $_POST['examTitle'],
                'code' => $_POST['examCode'],
                'totalMarks' => $_POST['totalMarks'],
                'totalQuestions' => $_POST['totalQuestions'],
                'duration' => $_POST['examDuration'],
                'passingMarks' => $_POST['passingMarks'],
                'instructions' => $_POST['examInstructions'],
                'status' => $_POST['examStatus'],
            ];

            if (empty($fields)) {
                return json_encode([
                    'status' => 'error',
                    'msg' => 'No fields to update',
                    'exam' => $exam
                ]);
            }

            // Build SQL safely
            $sql = "UPDATE exam_info SET " . implode(', ', $fields) . " WHERE id = ?";

            // Add ID to param list
            $params[] = $id;

            // Execute
            $statement = $this->db->prepare($sql);
            $statement->execute($params);

            return json_encode([
                'status' => 'success',
                'msg' => 'Exam updated successfully',
                'exam' => $exam
            ]);

        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function getExamData($id)
    {
        $sql = "SELECT id, title, duration, code, total_marks, passing_marks, instructions, status, total_num_of_ques as total_questions 
            FROM exam_info WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $exam = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$exam) {
            return json_encode([
                'status' => 'error',
                'msg' => 'Exam not found.'
            ]);
        }

        // Fix status
        switch ($exam['status']) {
            case '0':
                $exam['status'] = 'draft';
                break;
            case '1':
                $exam['status'] = 'published';
                break;
            default:
                $exam['status'] = 'scheduled';
                break;
        }
        $exam['duration'] = $exam['duration'] + 0;

        $questions = [];
        $questionIds = []; // prevent duplicates
        $sections = [];
        $optionMap = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];
        $options = [];

        // Fetch all questions that already contain this exam
        $statment = $this->db->prepare("SELECT * FROM questions WHERE JSON_CONTAINS(exam_ids, JSON_QUOTE(?))");
        $statment->execute([$id]);
        $existingQuestions = $statment->fetchAll(PDO::FETCH_ASSOC);

        foreach ($existingQuestions as $question) {
            $options = [];
            $question['isSaved'] = true;
            foreach ($optionMap as $key => $order) {
                $options[] = [
                    'text' => $question[$key] ?? '',
                    'image' => $question[$key . '_img'] ?? '',
                    'order' => $order,
                    'op' => strtoupper($key)
                ];
            }
            $question['options'] = $options;
            $question['marks'] = $question['marks'] + 0;
            // Decode exam_ids
            $examIDs = !empty($question['exam_ids']) ? json_decode($question['exam_ids'], true) : [];
            $sectionIds = [];
            if (!empty($question['section_ids'])) {
                $decoded = json_decode($question['section_ids'], true);
                if (is_array($decoded)) {
                    $sectionIds = array_map('intval', $decoded);
                }
            }
            $question['assignedSections'] = $sectionIds;
            $question['exam_ids'] = $examIDs;
            $questions[$question['id']] = $question; // key by id
            $questionIds[] = $question['id'];
        }

        // Fetch sections
        $statment = $this->db->prepare("SELECT * FROM sections WHERE exam_id = ?");
        $statment->execute([$id]);
        $scts = $statment->fetchAll(PDO::FETCH_ASSOC);

        foreach ($scts as $sct) {

            // Fetch questions that belong to this section
            $stmt = $this->db->prepare("SELECT * FROM questions WHERE JSON_CONTAINS(section_ids, JSON_QUOTE(?))");
            $stmt->execute([$sct['id']]);
            $sectionQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($sectionQuestions as $q) {
                $options = [];
                // Decode exam_ids
                $examIDs = !empty($q['exam_ids']) ? json_decode($q['exam_ids'], true) : [];

                // Push this section's exam_id if missing
                if (!in_array($sct['exam_id'], $examIDs)) {
                    $examIDs[] = $sct['exam_id'];
                    $examIDsJson = json_encode(array_values(array_unique($examIDs)));

                    $updateStmt = $this->db->prepare("UPDATE questions SET exam_ids = ? WHERE id = ?");
                    $updateStmt->execute([$examIDsJson, $q['id']]);
                }

                $q['exam_ids'] = $examIDs;

                // Attach options
                foreach ($optionMap as $key => $order) {
                    $options[] = [
                        'text' => $q[$key] ?? '',
                        'image' => $q[$key . '_img'] ?? '',
                        'order' => $order,
                        'op' => strtoupper($key)
                    ];
                }
                $q['options'] = $options;
                $q['isSaved'] = true;
                $q['marks'] = $q['marks'] + 0;

                $sectionIds = [];
                if (!empty($q['section_ids'])) {
                    $decoded = json_decode($q['section_ids'], true);
                    if (is_array($decoded)) {
                        $sectionIds = array_map('intval', $decoded);
                    }
                }
                $q['assignedSections'] = $sectionIds;

                // Add to final questions if not already added
                if (!in_array($q['id'], $questionIds)) {
                    $questions[$q['id']] = $q;
                    $questionIds[] = $q['id'];
                }
            }

            // Build section info
            $sections[] = [
                'id' => $sct['id'],
                'title' => $sct['title'],
                'description' => $sct['s_des'],
                'secondDescription' => $sct['s_s_des'],
                'question_count' => $sct['num_of_ques'],
                'examID' => $sct['exam_id'],
                'assignedQuestions' => count($sectionQuestions)
            ];
        }

        $statment = $this->db->prepare("SELECT id, schedule_type, start_time, shuffle_questions, shuffle_options, immediate_results, retake, max_attempts, enable_proctoring, full_screen_mode, disable_copy_paste, disable_right_click FROM exam_settings WHERE exam_id = ?");
        $statment->execute([$id]);
        $settings = $statment->fetch(PDO::FETCH_ASSOC);

        $settings_data = [];
        if ($settings) {
            $settings_data = [
                'id' => $settings['id'] + 0,
                'schedule_type' => $settings['schedule_type'],
                'start_time' => $settings['start_time'] != null ? str_replace(" ", "T", $settings['start_time']) : null,
                'shuffle_questions' => $settings['shuffle_questions'] == 1 ? true : false,
                'shuffle_options' => $settings['shuffle_options'] == 1 ? true : false,
                'immediate_results' => $settings['immediate_results'] == 1 ? true : false,
                'retake' => $settings['retake'] == 1 ? true : false,
                'max_attempts' => $settings['max_attempts'] != null ? $settings['max_attempts'] + 0 : 1,
                'enable_proctoring' => $settings['enable_proctoring'] == 1 ? true : false,
                'full_screen_mode' => $settings['full_screen_mode'] == 1 ? true : false,
                'disable_copy_paste' => $settings['disable_copy_paste'] == 1 ? true : false,
                'disable_right_click' => $settings['disable_right_click'] == 1 ? true : false,
                'isDone' => true
            ];
        }

        usort($questions, function ($a, $b) {
            return strtotime($a['created_at']) - strtotime($b['created_at']);
        });

        return json_encode([
            'status' => 'success',
            'exam' => $exam,
            'questions' => array_values($questions), // reindex
            'sections' => $sections,
            'exam_settings' => $settings_data
        ]);
    }

    public function saveExamSettings()
    {
        try {
            $exam_id = $_POST['exam_id'];
            $schedule_type = $_POST['scheduleType'];
            $start_date_time = str_replace("T", " ", $_POST['startDateTime']);

            $shuffle_questions = isset($_POST['shuffleQuestions']) ? 1 : 0;
            $shuffle_options = isset($_POST['shuffleOptions']) ? 1 : 0;
            $immediate_results = isset($_POST['showResultsImmediately']) ? 1 : 0;
            $retake = isset($_POST['allowRetake']) ? 1 : 0;

            $max_attempts = $_POST['maxAttempts'];

            $enable_proctoring = isset($_POST['enableProctoring']) ? 1 : 0;
            $full_screen_mode = isset($_POST['fullScreenMode']) ? 1 : 0;
            $disable_copy_paste = isset($_POST['disableCopyPaste']) ? 1 : 0;
            $disable_right_click = isset($_POST['disableRightClick']) ? 1 : 0;

            $statement = $this->db->prepare("INSERT INTO exam_settings (exam_id, schedule_type, start_time, shuffle_questions, shuffle_options, immediate_results, retake, max_attempts, enable_proctoring, full_screen_mode, disable_copy_paste, disable_right_click) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ,?, ? )");
            $statement->execute([$exam_id, $schedule_type, $start_date_time, $shuffle_questions, $shuffle_options, $immediate_results, $retake, $max_attempts, $enable_proctoring, $full_screen_mode, $disable_copy_paste, $disable_right_click]);
            $setting_id = $this->db->lastInsertId();

            $settings_data = [
                'id' => $setting_id + 0,
                'exam_id' => $exam_id + 0,
                'schedule_type' => $schedule_type,
                'start_time' => str_replace(" ", "T", $start_date_time),
                'shuffle_questions' => $shuffle_questions == 1 ? true : false,
                'shuffle_options' => $shuffle_options == 1 ? true : false,
                'immediate_results' => $immediate_results == 1 ? true : false,
                'retake' => $retake == 1 ? true : false,
                'max_attempts' => $max_attempts,
                'enable_proctoring' => $enable_proctoring == 1 ? true : false,
                'full_screen_mode' => $full_screen_mode == 1 ? true : false,
                'disable_copy_paste' => $disable_copy_paste == 1 ? true : false,
                'disable_right_click' => $disable_right_click == 1 ? true : false
            ];

            return json_encode([
                'status' => 'success',
                'msg' => 'Exam settings added successfully',
                'exam_settings' => $settings_data
            ]);

        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function editExamSettings($id)
    {
        try {
            $data = $_POST;

            $fields = [];
            $values = [];

            // Optional fields
            if (isset($data['exam_id'])) {
                $fields[] = "exam_id = ?";
                $values[] = $data['exam_id'];
            }

            if (isset($data['scheduleType'])) {
                $fields[] = "schedule_type = ?";
                $values[] = $data['scheduleType'];
            }

            if (isset(($data['scheduleType'])) && ($data['scheduleType']) == 'scheduled' && isset($data['startDateTime'])) {
                $start_time = str_replace("T", " ", $data['startDateTime']);
            } else {
                $start_time = null;
            }
            $fields[] = "start_time = ?";
            $values[] = $start_time;


            // Boolean fields
            $booleanFields = [
                'shuffleQuestions' => 'shuffle_questions',
                'shuffleOptions' => 'shuffle_options',
                'showResultsImmediately' => 'immediate_results',
                'allowRetake' => 'retake',
                'enableProctoring' => 'enable_proctoring',
                'fullScreenMode' => 'full_screen_mode',
                'disableCopyPaste' => 'disable_copy_paste',
                'disableRightClick' => 'disable_right_click',
            ];

            foreach ($booleanFields as $key => $column) {
                $fields[] = "$column = ?";
                $values[] = isset($data[$key]) && $data[$key] ? 1 : 0;
            }

            $fields[] = "max_attempts = ?";
            if (isset($data['allowRetake']) && $data['allowRetake'] && isset($data['maxAttempts'])) {
                $values[] = $data['maxAttempts'];
            } else {
                $values[] = null;
            }

            if (!empty($fields)) {
                $values[] = $id;
                $sql = "UPDATE exam_settings SET " . implode(", ", $fields) . " WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($values);
            }

            // Build settings_data for response
            $settings_data = [
                'id' => $id + 0,
                'exam_id' => isset($data['exam_id']) ? $data['exam_id'] + 0 : null,
                'schedule_type' => $data['scheduleType'] ?? null,
                'start_time' => isset($start_time) ? str_replace(" ", "T", $start_time) : null,
                'isDone' => true
            ];

            foreach ($booleanFields as $key => $column) {
                $settings_data[$column] = isset($data[$key]) ? ($data[$key] ? true : false) : false;
            }

            $settings_data['max_attempts'] = isset($data['maxAttempts']) ? $data['maxAttempts'] + 0 : 1;

            return json_encode([
                'status' => 'success',
                'msg' => 'Exam settings updated successfully',
                'exam_settings' => $settings_data
            ]);

        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function getExamDataForAttempt($id)
    {
        try {
            $statement = $this->db->prepare("SELECT * FROM exam_info WHERE id = ?");
            $statement->execute([$id]);
            $exam = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$exam) {
                throw new Exception("Exam not found");
            }

            $statement = $this->db->prepare("SELECT * FROM questions WHERE JSON_CONTAINS(exam_ids, JSON_QUOTE(?))");
            $statement->execute([$id]);
            $questions = $statement->fetchAll(PDO::FETCH_ASSOC);

            if (!$questions) {
                throw new Exception('No questions found for this exam');
            }

            $statement = $this->db->prepare("SELECT * FROM sections WHERE exam_id = ?");
            $statement->execute([$id]);
            $sections = $statement->fetchAll(PDO::FETCH_ASSOC);

            $statement = $this->db->prepare("SELECT * FROM exam_settings WHERE exam_id = ?");
            $statement->execute([$id]);
            $settings = $statement->fetch(PDO::FETCH_ASSOC);

            $finalSections = [];

            // Preload existing sections from DB
            foreach ($sections as $section) {
                $finalSections[$section['id']] = [
                    'id' => $section['id'],
                    'examID' => $id + 0,
                    'description' => $section['s_des'] ?? '',
                    'secondDescription' => $section['s_s_des'] ?? '',
                    'questions' => []
                ];
            }

            // Sort questions by created_at
            usort($questions, function ($a, $b) {
                return strtotime($a['created_at']) - strtotime($b['created_at']);
            });

            foreach ($questions as $question) {
                $section_ids = $question['section_ids'] ? json_decode($question['section_ids'], true) : [];
                $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

                $optionsKeys = ['a', 'b', 'c', 'd'];
                $options = [];
                foreach ($optionsKeys as $order => $key) {
                    $options[] = [
                        'text' => $question[$key] ?? '',
                        'image' => $question[$key . '_img'] ?? null,
                        'order' => $order + 1,
                        'op' => strtoupper($key)
                    ];
                }

                $qData = [
                    'id' => $question['id'],
                    'question' => $question['question'],
                    'marks' => $question['marks'],
                    'created_at' => $question['created_at'],
                    'created_by' => $question['created_by'],
                    'options' => $options,
                    'answer' => $question['answer'] ?? null
                ];

                if (!empty($section_ids)) {
                    foreach ($section_ids as $secId) {
                        if (isset($finalSections[$secId])) {
                            $finalSections[$secId]['questions'][] = $qData;
                        } else {
                            $tempId = 'temp_' . $code;
                            $finalSections[$tempId] = [
                                'id' => $tempId,
                                'questions' => [$qData],
                                'examID' => $id + 0,
                                'description' => '',
                                'secondDescription' => '',
                            ];
                        }
                    }
                } else {
                    $tempId = 'temp_' . $code;
                    $finalSections[$tempId] = [
                        'id' => $tempId,
                        'questions' => [$qData],
                        'examID' => $id + 0,
                        'description' => '',
                        'secondDescription' => '',
                    ];
                }
            }

            $exam_info = [
                'id' => $id + 0,
                'code' => $exam['code'],
                'title' => $exam['title'],
                'total_marks' => $exam['total_marks'] + 0,
                'total_questions' => $exam['total_num_of_ques'] + 0,
                'duration' => $exam['duration'] + 0,
                'instructions' => $exam['instructions'],
                'passing_marks' => $exam['passing_marks'] + 0,
                'status' => $exam['status'] == 0 ? 'draft' : 'published',
            ];

            $settings_info = [
                'id' => $settings['id'] + 0,
                'schedule_type' => $settings['schedule_type'],
                'start_time' => $settings['start_time'] != null ? str_replace(" ", "T", $settings['start_time']) : null,
                'shuffle_questions' => $settings['shuffle_questions'] == 1 ? true : false,
                'shuffle_options' => $settings['shuffle_options'] == 1 ? true : false,
                'immediate_results' => $settings['immediate_results'] == 1 ? true : false,
                'retake' => $settings['retake'] == 1 ? true : false,
                'max_attempts' => $settings['max_attempts'] != null ? $settings['max_attempts'] + 0 : 1,
                'enable_proctoring' => $settings['enable_proctoring'] == 1 ? true : false,
                'full_screen_mode' => $settings['full_screen_mode'] == 1 ? true : false,
                'disable_copy_paste' => $settings['disable_copy_paste'] == 1 ? true : false,
                'disable_right_click' => $settings['disable_right_click'] == 1 ? true : false
            ];

            $finalSections = array_values($finalSections);
            return json_encode([
                'status' => 'success',
                'exam_info' => $exam_info,
                'sections' => $finalSections,
                'settings' => $settings_info
            ]);

        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function getExamDataForPreview($id)
    {
        try {
            $statement = $this->db->prepare("SELECT * FROM exam_info WHERE id = ?");
            $statement->execute([$id]);
            $exam = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$exam) {
                throw new Exception("Exam not found");
            }

            $statement = $this->db->prepare("SELECT * FROM questions WHERE JSON_CONTAINS(exam_ids, JSON_QUOTE(?))");
            $statement->execute([$id]);
            $questions = $statement->fetchAll(PDO::FETCH_ASSOC);

            if (!$questions) {
                throw new Exception('No questions found for this exam');
            }

            $statement = $this->db->prepare("SELECT * FROM sections WHERE exam_id = ?");
            $statement->execute([$id]);
            $sections = $statement->fetchAll(PDO::FETCH_ASSOC);

            $statement = $this->db->prepare("SELECT * FROM exam_settings WHERE exam_id = ?");
            $statement->execute([$id]);
            $settings = $statement->fetch(PDO::FETCH_ASSOC);

            $finalSections = [];

            foreach ($sections as $index => $section) {
                $finalSections[] = [
                    'id' => (int) $section['id'],
                    'exam_id' => (int) $id,
                    'title' => $section['title'] ?? '',
                    'description' => $section['s_des'] ?? '',
                    'second_description' => $section['s_s_des'] ?? '',
                    'order' => $index + 1,
                    'questions' => []
                ];
            }

            // Create a mapping of section IDs for quick lookup
            $sectionIds = array_column($finalSections, 'id');

            $finalQuestions = [];
            foreach ($questions as $question) {
                $optionsKeys = ['a', 'b', 'c', 'd'];
                $options = [];
                foreach ($optionsKeys as $order => $key) {
                    $options[] = [
                        'id' => $key, // Use lowercase for consistency
                        'text' => $question[$key] ?? '',
                        'image' => $question[$key . '_img'] ?? null,
                        'order' => $order + 1,
                        'op' => strtoupper($key)
                    ];
                }

                // Get section IDs from the question (assuming there's a section_ids field)
                // If not, you may need to query a separate table for question-section relationships
                $sectionIdsForQuestion = [];

                // If your questions table has a section_ids JSON field:
                if (isset($question['section_ids'])) {
                    $sectionIdsJson = json_decode($question['section_ids'], true);
                    if (is_array($sectionIdsJson)) {
                        $sectionIdsForQuestion = array_map('intval', $sectionIdsJson);
                    }
                }

                // OR if you have a separate question_sections table, query it:
                // $stmt = $this->db->prepare("SELECT section_id FROM question_sections WHERE question_id = ?");
                // $stmt->execute([$question['id']]);
                // $sectionIdsForQuestion = $stmt->fetchAll(PDO::FETCH_COLUMN);

                $qData = [
                    'id' => (int) $question['id'],
                    'question' => $question['question'],
                    'marks' => (int) $question['marks'],
                    'created_at' => $question['created_at'],
                    'created_by' => $question['created_by'],
                    'options' => $options,
                    'correctAnswer' => strtolower($question['answer'] ?? 'a'), // Default to 'a' if empty
                    'sectionIds' => $sectionIdsForQuestion, // Add section IDs
                    'type' => $question['type'] ?? 'multiple_choice',
                    'difficulty' => $question['difficulty'] ?? 'medium',
                    'image' => $question['image'] ?? null
                ];

                $finalQuestions[] = $qData;
            }

            $exam_info = [
                'id' => (int) $id,
                'code' => $exam['code'],
                'title' => $exam['title'],
                'total_marks' => (int) $exam['total_marks'],
                'total_questions' => (int) $exam['total_num_of_ques'],
                'duration' => (int) $exam['duration'],
                'instructions' => $exam['instructions'],
                'passing_marks' => (int) $exam['passing_marks'],
                'status' => $exam['status'] == 0 ? 'draft' : 'published',
            ];

            $settings_info = [
                'id' => (int) $settings['id'],
                'schedule_type' => $settings['schedule_type'],
                'start_time' => $settings['start_time'] ? str_replace(" ", "T", $settings['start_time']) : null,
                'end_time' => $settings['start_time'] ? str_replace(" ", "T", $settings['start_time']) : null,
                'shuffle_questions' => $settings['shuffle_questions'] == 1,
                'shuffle_options' => $settings['shuffle_options'] == 1,
                'show_results_immediately' => $settings['immediate_results'] == 1, // Note: renamed for consistency
                'allow_retake' => $settings['retake'] == 1,
                'max_attempts' => $settings['max_attempts'] ? (int) $settings['max_attempts'] : 1,
                'enable_proctoring' => $settings['enable_proctoring'] == 1,
                'full_screen_mode' => $settings['full_screen_mode'] == 1,
                'disable_copy_paste' => $settings['disable_copy_paste'] == 1,
                'disable_right_click' => $settings['disable_right_click'] == 1,
                'allow_navigation' =>  true // Add if exists
            ];

            return json_encode([
                'status' => 'success',
                'exam_info' => $exam_info,
                'sections' => $finalSections,
                'questions' => $finalQuestions,
                'settings' => $settings_info
            ]);

        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }
}