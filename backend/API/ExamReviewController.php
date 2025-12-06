<?php
namespace App\Controllers;

class ExamReviewController
{
    public function index($examId = null)
    {
        $examModel = new \App\Models\ExamModel();
        
        if (!$examId) {
            $examId = session()->get('review_exam_id');
            if (!$examId) {
                return redirect()->to('exams');
            }
        }
        
        $exam = $examModel->find($examId);
        if (!$exam) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        $data = [
            'exam' => $exam,
            'examId' => $examId,
            'page_title' => 'Exam Review - ' . $exam['title']
        ];
        
        return view('exam_review', $data);
    }
    
    // API: Get complete exam review data
    public function getReviewData($examId)
    {
        $this->response->setHeader('Content-Type', 'application/json');
        
        $examModel = new \App\Models\ExamModel();
        $sectionModel = new \App\Models\SectionModel();
        $questionModel = new \App\Models\QuestionModel();
        $userModel = new \App\Models\UserModel();
        
        $exam = $examModel->find($examId);
        if (!$exam) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Exam not found'
            ]);
        }
        
        // Get creator name
        $creator = $userModel->find($exam['created_by']);
        $exam['created_by_name'] = $creator ? $creator['name'] : 'Unknown';
        
        // Get sections for this exam
        $sections = $sectionModel->where('exam_id', $examId)
                                 ->orderBy('order', 'ASC')
                                 ->findAll();
        
        // Get all questions for this exam
        $questions = $questionModel->where('exam_id', $examId)
                                   ->orderBy('order', 'ASC')
                                   ->findAll();
        
        // Format questions
        $formattedQuestions = [];
        foreach ($questions as $question) {
            // Parse section_ids from JSON
            $sectionIds = json_decode($question['section_ids'] ?? '[]', true) ?: [];
            
            $formattedQuestions[] = [
                'id' => $question['id'],
                'question' => $question['question'],
                'marks' => $question['marks'],
                'image' => $question['image'] ? base_url('uploads/questions/' . $question['image']) : null,
                'options' => $this->formatOptions($question['options']),
                'correctAnswer' => $question['answer'],
                'section_ids' => $sectionIds,
                'type' => $question['type'] ?? 'multiple_choice',
                'difficulty' => $question['difficulty'] ?? 'medium',
                'order' => $question['order']
            ];
        }
        
        // Format sections
        $formattedSections = [];
        foreach ($sections as $section) {
            $formattedSections[] = [
                'id' => $section['id'],
                'exam_id' => $section['exam_id'],
                'title' => $section['title'],
                'description' => $section['description'],
                'order' => $section['order'],
                'question_count' => $section['question_count'] ?? 0,
                'marks_per_question' => $section['marks_per_question'] ?? 1
            ];
        }
        
        return $this->response->setJSON([
            'success' => true,
            'exam' => $exam,
            'sections' => $formattedSections,
            'questions' => $formattedQuestions
        ]);
    }
    
    private function formatOptions($optionsJson)
    {
        $options = json_decode($optionsJson, true);
        $formatted = [];
        
        if (is_array($options)) {
            foreach ($options as $key => $value) {
                $formatted[] = [
                    'id' => $key,
                    'text' => $value['text'] ?? $value,
                    'image' => isset($value['image']) ? base_url('uploads/options/' . $value['image']) : null
                ];
            }
        }
        
        return $formatted;
    }
    
    // API: Publish exam
    public function publishExam($examId)
    {
        $this->response->setHeader('Content-Type', 'application/json');
        
        $examModel = new \App\Models\ExamModel();
        
        $exam = $examModel->find($examId);
        if (!$exam) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Exam not found'
            ]);
        }
        
        // Check if exam has questions
        $questionModel = new \App\Models\QuestionModel();
        $questionCount = $questionModel->where('exam_id', $examId)->countAllResults();
        
        if ($questionCount === 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot publish exam without questions'
            ]);
        }
        
        // Update exam status
        $data = [
            'status' => 'published',
            'published_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        try {
            $examModel->update($examId, $data);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Exam published successfully',
                'published_at' => $data['published_at']
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error publishing exam: ' . $e->getMessage()
            ]);
        }
    }
    
    // API: Unpublish exam
    public function unpublishExam($examId)
    {
        $this->response->setHeader('Content-Type', 'application/json');
        
        $examModel = new \App\Models\ExamModel();
        
        $exam = $examModel->find($examId);
        if (!$exam) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Exam not found'
            ]);
        }
        
        // Update exam status
        $data = [
            'status' => 'draft',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        try {
            $examModel->update($examId, $data);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Exam unpublished successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error unpublishing exam: ' . $e->getMessage()
            ]);
        }
    }
    
    // API: Generate preview link
    public function generatePreviewLink($examId)
    {
        $this->response->setHeader('Content-Type', 'application/json');
        
        $examModel = new \App\Models\ExamModel();
        
        $exam = $examModel->find($examId);
        if (!$exam) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Exam not found'
            ]);
        }
        
        // Generate unique preview token
        $token = bin2hex(random_bytes(16));
        
        // Save token to database (you would need a preview_tokens table)
        // For now, return a preview link
        $previewLink = base_url("exam/preview/{$examId}?token={$token}");
        
        return $this->response->setJSON([
            'success' => true,
            'previewLink' => $previewLink,
            'token' => $token
        ]);
    }
    
    // API: Export exam
    public function exportExam($examId, $format = 'pdf')
    {
        // Implement export functionality as shown in previous examples
        // Return appropriate file download
    }
}