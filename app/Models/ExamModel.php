<?php
namespace App\Models;

use CodeIgniter\Model;

class ExamModel extends Model
{
    protected $table = 'exams';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'description', 'total_questions', 'duration', 'created_by', 'created_at', 'exam_stat'];
    
    // Helper method to get published exams
    public function getPublishedExams()
    {
        return $this->where('exam_stat', 'published')->findAll();
    }
    
    // Helper method to get draft exams
    public function getDraftExams()
    {
        return $this->where('exam_stat', 'draft')->findAll();
    }
    
    // Helper method to toggle exam status
    public function toggleStatus($id, $status)
    {
        return $this->update($id, ['exam_stat' => $status]);
    }
}
