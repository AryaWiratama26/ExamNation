<?php
namespace App\Models;

use CodeIgniter\Model;

class ExamModel extends Model
{
    protected $table = 'exams';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'description', 'total_questions', 'duration', 'created_by', 'created_at'];
}
