<?php
namespace App\Controllers;

use App\Models\ExamModel;
use App\Models\QuestionModel;
use App\Models\ResultModel;
use CodeIgniter\Exceptions\PageNotFoundException;
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



    public function showResult()
    {
        return view('peserta/result');
    }
}