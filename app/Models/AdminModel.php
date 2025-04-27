<?php
namespace App\Models;
use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    public function getTotalUsers(): int
    {
        return $this->db->table('users')->countAll();
    }

    public function getTotalExams(): int
    {
        return $this->db->table('exams')->countAll();
    }

    public function getActiveExams(): int
    {
        return $this->db->table('exams')->where('status', 'active')->countAllResults();
    }

    public function getAverageScore(): float
    {
        $result = $this->db->table('results')->selectAvg('score')->get()->getRow();
        return $result ? (float) $result->score : 0.0;
    }

    public function getWeeklyExamStats(): array
    {
        return $this->db->table('results')
            ->select("DATE(taken_at) as date, COUNT(*) as total")
            ->groupBy('DATE(taken_at)')
            ->orderBy('taken_at', 'ASC')
            ->get()
            ->getResultArray(); // array langsung (lebih enak di-loop di view)
    }
}
?>
