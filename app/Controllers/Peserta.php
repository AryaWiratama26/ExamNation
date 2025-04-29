<?php
namespace App\Controllers;

use App\Models\ExamModel;
use App\Models\QuestionModel;
use App\Models\ResultModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Dompdf\Dompdf;
use App\Libraries\SpreadsheetWriter;

class Peserta extends BaseController
{
    public function dashboard()
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to('/login');
        }

        $userId = $session->get('user_id');
        $examModel = new ExamModel();
        $resultModel = new ResultModel();

        $exams = $examModel->findAll();

        $filteredExams = [];
        foreach ($exams as $exam) {
            $alreadyTaken = $resultModel
                ->where('exam_id', $exam['id'])
                ->where('user_id', $userId)
                ->first();

            if (!$alreadyTaken) {
                $filteredExams[] = $exam;
            }
        }

        return view('peserta/dashboard', [
            'session' => $session,
            'exams' => $filteredExams
        ]);
    }


    public function startExam($examId)
    {
        $examModel = new ExamModel();
        $questionModel = new QuestionModel();

        $exam = $examModel->find($examId);
        if (!$exam) {
            throw PageNotFoundException::forPageNotFound();
        }

        $questions = $questionModel->where('exam_id', $examId)->findAll();

        return view('peserta/exam_view', [
            'exam' => $exam,
            'questions' => $questions
        ]);
    }

    public function exam($id)
    {
        $examModel = new ExamModel();
        $questionModel = new QuestionModel();

        $exam = $examModel->find($id);
        if (!$exam) {
            throw PageNotFoundException::forPageNotFound();
        }

        $questions = $questionModel->where('exam_id', $id)->findAll();

        return view('peserta/exam', [
            'exam' => $exam,
            'questions' => $questions
        ]);
    }

    public function submitExam()
    {

        $questionModel = new QuestionModel();
        $resultModel = new ResultModel();

        $examId = $this->request->getPost('exam_id');
        $answers = $this->request->getPost('answers') ?? [];

        $allQuestions = $questionModel->where('exam_id', $examId)->findAll();

        $correctCount = 0;

        $user = (new \App\Models\UserModel())->find(session()->get('user_id'));


        foreach ($allQuestions as $question) {
            $questionId = $question['id'];
            $selectedOption = $answers[$questionId] ?? null; // null jika tidak dijawab

            if ($selectedOption && strtoupper($selectedOption) === strtoupper($question['correct_option'])) {
                $correctCount++;
            }
        }

        $totalQuestions = count($allQuestions);
        $score = round(($correctCount / $totalQuestions) * 100);

        $resultModel->insert([
            'exam_id' => $examId,
            'user_id' => session()->get('user_id'),
            'score' => $score,
            'taken_at' => date('Y-m-d H:i:s')
        ]);
        $spreadsheet = new SpreadsheetWriter();
        $spreadsheet->appendRow([
            $user['name'],
            (new ExamModel())->find($examId)['title'],
            round($score),
            date('Y-m-d H:i:s')
        ]);

        return view('peserta/result', [
            'examTitle' => (new ExamModel())->find($examId)['title'],
            'score' => $score,
            'correctCount' => $correctCount,
            'totalQuestions' => $totalQuestions,
        ]);
    }


    public function result($examId)
    {
        $resultModel = new ResultModel();
        $session = session();
        $userId = $session->get('user_id');

        $data['results'] = $resultModel->where('exam_id', $examId)
            ->where('user_id', $userId)
            ->findAll();

        return view('peserta/result', $data);
    }
    public function history()
    {
        $resultModel = new ResultModel();
        $examModel = new ExamModel();
        $userId = session()->get('user_id');

        $results = $resultModel->where('user_id', $userId)->findAll();

        foreach ($results as &$result) {
            $exam = $examModel->find($result['exam_id']);
            $result['exam_title'] = $exam ? $exam['title'] : 'Ujian Tidak Ditemukan';
        }

        return view('peserta/history', [
            'results' => $results
        ]);
    }

    public function downloadHistoryPdf()
    {
        // Increase memory limit
        ini_set('memory_limit', '512M');

        $resultModel = new ResultModel();
        $examModel = new ExamModel();
        $userId = session()->get('user_id');
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        // Limit results to the most recent 50 exams to reduce memory usage
        $results = $resultModel->where('user_id', $userId)
            ->orderBy('taken_at', 'DESC')
            ->limit(50)
            ->findAll();

        foreach ($results as &$result) {
            $exam = $examModel->find($result['exam_id']);
            $result['exam_title'] = $exam ? $exam['title'] : 'Ujian Tidak Ditemukan';
        }

        // Calculate statistics if there are results
        $totalExams = 0;
        $avgScore = 0;
        $highestScore = 0;

        if (!empty($results)) {
            $totalExams = count($results);
            $totalScore = array_sum(array_column($results, 'score'));
            $avgScore = round($totalScore / $totalExams, 1);
            $highestScore = max(array_column($results, 'score'));
        }

        // Generate simplified but elegant HTML for PDF
        $html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Transkrip Akademik</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        
        /* Header */
        .header {
            background-color: #6366F1;
            color: white;
            padding: 25px 30px;
            border-bottom: 5px solid #4F46E5;
        }
        
        .logo-text {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 0 0 15px 0;
            padding-left: 8px;
            border-left: 3px solid rgba(255, 255, 255, 0.7);
        }
        
        .document-title {
            font-size: 26px;
            font-weight: bold;
            margin: 0 0 8px 0;
        }
        
        .document-subtitle {
            font-size: 15px;
            margin: 0;
            opacity: 0.9;
        }
        
        /* Content */
        .content {
            padding: 30px;
        }
        
        .section {
            margin-bottom: 30px;
            border-radius: 8px;
            border: 1px solid #E5E7EB;
            overflow: hidden;
        }
        
        .section-header {
            background-color: #F9FAFB;
            padding: 12px 20px;
            border-bottom: 1px solid #E5E7EB;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #4B5563;
            margin: 0;
        }
        
        .section-body {
            padding: 20px;
            background-color: #FFFFFF;
        }
        
        /* User information */
        .user-info {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }
        
        .info-block {
            flex: 1 1 220px;
            margin: 0 10px 10px 0;
            padding: 12px;
            background-color: #F9FAFB;
            border-radius: 6px;
            border-left: 3px solid #6366F1;
        }
        
        .info-label {
            font-size: 12px;
            color: #6B7280;
            text-transform: uppercase;
            margin: 0 0 5px 0;
            font-weight: bold;
        }
        
        .info-value {
            font-size: 14px;
            color: #111827;
            margin: 0;
            font-weight: 500;
        }
        
        /* Stats */
        .stats-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .stat-item {
            flex: 1;
            padding: 15px;
            text-align: center;
            background-color: #F9FAFB;
            margin-right: 10px;
            border-radius: 6px;
            border: 1px solid #E5E7EB;
        }
        
        .stat-item:last-child {
            margin-right: 0;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #4F46E5;
            margin: 0 0 5px 0;
        }
        
        .stat-label {
            font-size: 12px;
            color: #6B7280;
            text-transform: uppercase;
            margin: 0;
            font-weight: bold;
        }
        
        /* Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        
        .data-table th {
            background-color: #F9FAFB;
            color: #374151;
            padding: 12px 15px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #E5E7EB;
        }
        
        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #E5E7EB;
        }
        
        .data-table tr:last-child td {
            border-bottom: none;
        }
        
        .data-table tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        
        /* Score indicators */
        .score {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 13px;
        }
        
        .score-excellent {
            background-color: #D1FAE5;
            color: #065F46;
        }
        
        .score-good {
            background-color: #DBEAFE;
            color: #1E40AF;
        }
        
        .score-average {
            background-color: #FEF3C7;
            color: #92400E;
        }
        
        .score-needs-improvement {
            background-color: #FEE2E2;
            color: #B91C1C;
        }
        
        /* Footer */
        .footer {
            background-color: #1F2937;
            padding: 20px;
            text-align: center;
            color: #D1D5DB;
            font-size: 12px;
        }
        
        .footer-text {
            margin: 0 0 10px 0;
        }
        
        .document-id {
            font-family: monospace;
            background: rgba(255,255,255,0.1);
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
        }
        
        .empty-data {
            text-align: center;
            padding: 30px;
            color: #6B7280;
            font-style: italic;
        }
        
        /* Signature */
        .signature {
            text-align: right;
            margin-top: 30px;
            padding-right: 20px;
        }
        
        .signature-line {
            width: 150px;
            height: 1px;
            background-color: #9CA3AF;
            margin: 30px 0 10px auto;
        }
        
        .signature-name {
            font-size: 14px;
            font-weight: bold;
            margin: 5px 0 2px 0;
        }
        
        .signature-title {
            font-size: 12px;
            color: #6B7280;
            margin: 0;
        }
        
        .page-number {
            text-align: right;
            font-size: 12px;
            color: #9CA3AF;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <p class="logo-text">EDUTECH ACADEMY</p>
        <h1 class="document-title">Transkrip Akademik</h1>
        <p class="document-subtitle">Rekam Jejak Performa dan Pencapaian</p>
    </div>
    
    <div class="content">
        <div class="section">
            <div class="section-header">
                <h2 class="section-title">Informasi Peserta</h2>
            </div>
            <div class="section-body">
                <div class="user-info">
                    <div class="info-block">
                        <p class="info-label">Nama Lengkap</p>
                        <p class="info-value">' . esc($user['name'] ?? 'Peserta') . '</p>
                    </div>
                    <div class="info-block">
                        <p class="info-label">ID Peserta</p>
                        <p class="info-value">' . esc($userId) . '</p>
                    </div>
                    <div class="info-block">
                        <p class="info-label">Email</p>
                        <p class="info-value">' . esc($user['email'] ?? '-') . '</p>
                    </div>
                    <div class="info-block">
                        <p class="info-label">Tanggal Diterbitkan</p>
                        <p class="info-value">' . date('d F Y') . '</p>
                    </div>
                </div>
            </div>
        </div>';

        if (!empty($results)) {
            $html .= '
        <div class="section">
            <div class="section-header">
                <h2 class="section-title">Ringkasan Pencapaian</h2>
            </div>
            <div class="section-body">
                <div class="stats-container">
                    <div class="stat-item">
                        <p class="stat-number">' . $totalExams . '</p>
                        <p class="stat-label">Total Ujian</p>
                    </div>
                    <div class="stat-item">
                        <p class="stat-number">' . $avgScore . '</p>
                        <p class="stat-label">Nilai Rata-rata</p>
                    </div>
                    <div class="stat-item">
                        <p class="stat-number">' . $highestScore . '</p>
                        <p class="stat-label">Nilai Tertinggi</p>
                    </div>
                </div>
            </div>
        </div>';
        }

        $html .= '
        <div class="section">
            <div class="section-header">
                <h2 class="section-title">Riwayat Ujian</h2>
            </div>
            <div class="section-body">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50%;">Judul Ujian</th>
                            <th style="width: 20%;">Nilai</th>
                            <th style="width: 30%;">Tanggal Pengerjaan</th>
                        </tr>
                    </thead>
                    <tbody>';

        if (!empty($results)) {
            foreach ($results as $res) {
                $scoreClass = '';
                $score = $res['score'];
                $scoreText = '';

                if ($score >= 80) {
                    $scoreClass = 'score-excellent';
                    $scoreText = 'Sangat Baik';
                } elseif ($score >= 70) {
                    $scoreClass = 'score-good';
                    $scoreText = 'Baik';
                } elseif ($score >= 60) {
                    $scoreClass = 'score-average';
                    $scoreText = 'Cukup';
                } else {
                    $scoreClass = 'score-needs-improvement';
                    $scoreText = 'Perlu Perbaikan';
                }

                $html .= '<tr>
            <td>' . esc($res['exam_title']) . '</td>
            <td><span class="score ' . $scoreClass . '">' . esc($score) . ' - ' . $scoreText . '</span></td>
            <td>' . date('d F Y H:i', strtotime($res['taken_at'])) . ' WIB</td>
          </tr>';
            }
        } else {
            $html .= '<tr><td colspan="3" class="empty-data">Belum ada riwayat ujian.</td></tr>';
        }

        $html .= '</tbody>
                </table>
            </div>
        </div>
        
        <div class="signature">
            <div class="signature-line"></div>
            <p class="signature-name">Admin Sistem</p>
            <p class="signature-title">Kepala Akademik</p>
        </div>
        
        <div class="page-number">Halaman 1 dari 1</div>
    </div>
    
    <div class="footer">
        <p class="footer-text">Dokumen ini diterbitkan secara elektronik dan sah tanpa tanda tangan basah</p>
        <div class="document-id">ID: EDTC-' . time() . '-' . str_pad($userId, 4, '0', STR_PAD_LEFT) . '</div>
    </div>
</body>
</html>';

        // Create PDF with optimized options
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false); // Disable remote files
        $options->set('defaultFont', 'Arial');
        $options->set('isPhpEnabled', false);    // Disable PHP code execution
        $options->set('debugKeepTemp', false);   // Don't keep temporary files
        $options->set('debugCss', false);        // Disable CSS debugging
        $options->set('debugLayout', false);     // Disable layout debugging

        try {
            // Create PDF with optimized options
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Output PDF for download
            $filename = 'Transkrip_Akademik_' . date('Ymd') . '_' . $userId . '.pdf';
            $dompdf->stream($filename, ['Attachment' => true]);

            // Clean up
            $dompdf = null;
            unset($dompdf);
            unset($html);
            unset($results);
            gc_collect_cycles();
        } catch (\Exception $e) {
            log_message('error', 'PDF Generation Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat PDF. Silakan coba lagi nanti.');
        }
    }
    
    public function showResult()
    {
        return view('peserta/result');
    }
}