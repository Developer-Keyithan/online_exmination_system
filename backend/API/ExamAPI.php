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
        usort($questions, function ($a, $b) {
            return strtotime($a['created_at']) - strtotime($b['created_at']);
        });

        return json_encode([
            'status' => 'success',
            'exam' => $exam,
            'questions' => array_values($questions), // reindex
            'sections' => $sections,
        ]);
    }
}