<?php

namespace App\Controllers;

use App\Models\ExamModel;
use App\Models\ExamActivityLogModel;
use CodeIgniter\Controller;

class ExamController extends Controller
{
    protected $examModel;
    protected $activityLogModel;

    public function __construct()
    {
        $this->examModel = new ExamModel();
        $this->activityLogModel = new ExamActivityLogModel();
    }

    // ... existing methods ...

    public function viewLogs($examId = null)
    {
        // Check if user is admin
        if (!session()->get('logged_in') || 
            (session()->get('user_role') !== 'admin' && !session()->get('is_admin'))) {
            return redirect()->to('/login')->with('error', 'Please login as admin to access this page');
        }

        // Common data for admin layout
        $data = [
            'title' => 'Monitoring Pelanggaran Ujian',
            'active_menu' => 'violations'
        ];

        // If no exam ID provided, show all exams with violation counts
        if ($examId === null) {
            $data['exams'] = $this->examModel->select('exams.*, users.name as creator_name, 
                (SELECT COUNT(*) FROM exam_activity_logs WHERE exam_id = exams.id) as violation_count')
                ->join('users', 'users.id = exams.created_by')
                ->findAll();
            
            return view('admin/exam_violations', $data);
        }

        // Get specific exam details and its logs
        $data['exam'] = $this->examModel->find($examId);
        if (!$data['exam']) {
            return redirect()->to('/admin/violations')->with('error', 'Exam not found');
        }

        $data['logs'] = $this->activityLogModel->getExamLogs($examId);
        return view('admin/exam_logs', $data);
    }

    public function logActivity()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $userId = session()->get('user_id');
        $examId = $this->request->getPost('exam_id');
        $activity = $this->request->getPost('activity');

        if (!$userId || !$examId || !$activity) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required data']);
        }

        try {
            $this->activityLogModel->logActivity($userId, $examId, $activity);
            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to log activity']);
        }
    }

    public function exportActivityLogs($examId)
    {
        // Check if user is admin
        if (!session()->get('logged_in') || 
            (session()->get('user_role') !== 'admin' && !session()->get('is_admin'))) {
            return redirect()->to('/login')->with('error', 'Please login as admin to access this page');
        }

        // Get exam details
        $exam = $this->examModel->find($examId);
        if (!$exam) {
            return redirect()->to('/admin/violations')->with('error', 'Exam not found');
        }

        // Get detailed logs with user information
        $logs = $this->activityLogModel->getDetailedExamLogs($examId);
        
        // Create CSV file
        $filename = 'pelanggaran_ujian_' . url_title($exam['title'], '_', true) . '_' . date('Y-m-d_His') . '.csv';
        
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');
        
        // Add UTF-8 BOM for proper character encoding in Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Add report header
        fputcsv($output, ['LAPORAN PELANGGARAN UJIAN']);
        fputcsv($output, ['']);
        fputcsv($output, ['Judul Ujian:', $exam['title']]);
        fputcsv($output, ['Tanggal Export:', date('d F Y H:i:s')]);
        fputcsv($output, ['Total Pelanggaran:', count($logs)]);
        fputcsv($output, ['']);
        
        // Add CSV headers
        fputcsv($output, [
            'No.',
            'Nama Peserta',
            'Email Peserta',
            'Jenis Pelanggaran',
            'Waktu Kejadian',
            'Tingkat Pelanggaran'
        ]);
        
        // Add data rows
        $no = 1;
        foreach ($logs as $log) {
            $warningLevel = strpos($log['activity'], 'Maximum') !== false ? 'Serius' : 'Peringatan';
            
            fputcsv($output, [
                $no++,
                $log['user_name'],
                $log['user_email'],
                $log['activity'],
                date('d/m/Y H:i:s', strtotime($log['logged_at'])),
                $warningLevel
            ]);
        }
        
        // Add footer
        fputcsv($output, ['']);
        fputcsv($output, ['Laporan ini digenerate secara otomatis oleh sistem ExamNation']);
        
        fclose($output);
        exit;
    }
} 