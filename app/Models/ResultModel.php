<?php
namespace App\Models;

use CodeIgniter\Model;

class ResultModel extends Model
{
    protected $table = 'results';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'exam_id', 'score', 'taken_at'];
}
