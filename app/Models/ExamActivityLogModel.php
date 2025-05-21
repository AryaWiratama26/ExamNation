<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamActivityLogModel extends Model
{
    protected $table = 'exam_activity_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'exam_id', 'activity', 'logged_at'];
    protected $useTimestamps = true;
    protected $createdField = 'logged_at';
    protected $updatedField = '';

    public function logActivity($userId, $examId, $activity)
    {
        return $this->insert([
            'user_id' => $userId,
            'exam_id' => $examId,
            'activity' => $activity
        ]);
    }

    public function getExamLogs($examId)
    {
        return $this->select('exam_activity_logs.*, users.name as user_name')
                    ->join('users', 'users.id = exam_activity_logs.user_id')
                    ->where('exam_id', $examId)
                    ->orderBy('logged_at', 'DESC')
                    ->findAll();
    }

    public function getDetailedExamLogs($examId)
    {
        return $this->select('exam_activity_logs.*, users.name as user_name, users.email as user_email, exams.title as exam_title')
                    ->join('users', 'users.id = exam_activity_logs.user_id')
                    ->join('exams', 'exams.id = exam_activity_logs.exam_id')
                    ->where('exam_activity_logs.exam_id', $examId)
                    ->orderBy('exam_activity_logs.logged_at', 'DESC')
                    ->findAll();
    }

    public function getRecentViolations($limit = 10)
    {
        return $this->select('exam_activity_logs.*, users.name as user_name, exams.title as exam_title')
                    ->join('users', 'users.id = exam_activity_logs.user_id')
                    ->join('exams', 'exams.id = exam_activity_logs.exam_id')
                    ->orderBy('logged_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function getViolationSummary($examId)
    {
        return $this->select('
                COUNT(*) as total_violations,
                SUM(CASE WHEN activity LIKE "%Maximum%" THEN 1 ELSE 0 END) as serious_violations,
                COUNT(DISTINCT user_id) as unique_violators
            ')
            ->where('exam_id', $examId)
            ->get()
            ->getRow();
    }
} 