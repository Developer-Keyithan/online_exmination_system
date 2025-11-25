<?php

class QuestionAPI
{
    private $db;
    public function __construct()
    {
        $this->db = db();
    }
    function validateRequiredFields($post)
    {
        if (empty($post['question'])) {
            throw new Exception('Question is required');
        }
        if (empty($post['answer'])) {
            throw new Exception('Answer is required');
        }

        // $options = ['A', 'B', 'C', 'D'];
        // foreach ($options as $opt) {
        //     $hasText = !empty($post[$opt]);
        //     $hasFile = isset($_FILES[$opt]) && $_FILES[$opt]['error'] != 4;

        //     if (!$hasText && !$hasFile) {
        //         throw new Exception("Option $opt is required (text or image)");
        //     }
        // }|
    }

    public function addQuestion()
    {
        try {
            $this->validateRequiredFields($_POST);

            $questionText = $_POST['question'];
            $answer = $_POST['answer'];
            $marks = $_POST['marks'];
            $examID = $_POST['exam_id'];
            $A = !empty($_POST['A']) ? $_POST['A'] : null;
            $B = !empty($_POST['B']) ? $_POST['B'] : null;
            $C = !empty($_POST['C']) ? $_POST['C'] : null;
            $D = !empty($_POST['D']) ? $_POST['D'] : null;

            $statment = $this->db->prepare("INSERT INTO questions (question, exam_id, answer, marks, a, b, c, d, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $statment->execute([$questionText, $examID, $answer, $marks, $A, $B, $C, $D, user_id()]);
            $questionId = $this->db->lastInsertId();

            // // Question image
            // $questionImagePath = isset($_FILES['questionImage']) ? uploadFile($_FILES['questionImage'], 'uploads/questions/' . $questionId, $questionId) : null;

            // // Options A/B/C/D
            // $optionsData = [];
            // foreach (['A', 'B', 'C', 'D'] as $opt) {
            //     $text = $_POST[$opt] ?? '';
            //     $filePath = isset($_FILES[$opt . 'img']) ? uploadFile($_FILES[$opt . 'img'], 'uploads/questions/' . $questionId, $questionId . '_' . $opt) : null;

            //     if (!$text && !$filePath) {
            //         throw new Exception("Option $opt is required (text or image)");
            //     }

            //     $optionsData[$opt] = ['text' => $text, 'image' => $filePath];
            // }

            // $statment = $this->db->prepare("UPDATE questions SET q_img = ?, a_img = ? , b_img = ?, c_img =? , d_img = ? WHERE id = ?");
            // $statment->execute([$questionImagePath, $optionsData['A']['image'], $optionsData['B']['image'], $optionsData['C']['image'], $optionsData['D']['image'], $questionId]);

            // $question['options'] = $optionsData; 
            // print_r($questionId);

            $question = []; // initialize array
            $question['text'] = $questionText;
            $question['answer'] = $answer;
            $question['marks'] = $marks;
            $question['options'] = [
                'A' => ['text' => $A],
                'B' => ['text' => $B],
                'C' => ['text' => $C],
                'D' => ['text' => $D]
            ];
            $question['id'] = $questionId;
            $question['created_at'] = date('Y-m-d H:i:s');
            $question['examID'] = $examID;


            return json_encode([
                'status' => 'success',
                'msg' => 'Question added successfully',
                'question' => $question
            ]);

        } catch (Exception $e) {
            return json_encode([
                'msg' => $e->getMessage(),
                'status' => 'error'
            ]);
        }
    }

}