<?php
namespace App\Controllers;

use App\Models\ExamModel;
use App\Models\UserModel;
use App\Models\QuestionModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Config\Database;
use App\Libraries\SpreadsheetWriter;

class Admin extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }
    public function dashboard()
    {
        $spreadsheet = new SpreadsheetWriter();
        $rows = $spreadsheet->getAllRows();

        // Tambahkan ke data yang dilempar ke view
        return view('admin/dashboard', [
            // data lain kamu tetap sertakan
            'rows' => $rows
        ]);
    }

    public function manageExam()
    {
        $examModel = new ExamModel();
        $data['exams'] = $examModel->findAll(); // Ambil semua data ujian dari database

        return view('admin/manage_exam', $data);
    }

    public function editExam($id)
    {
        $examModel = new ExamModel();
        $data['exams'] = $examModel->find($id);

        if (!$data['exams']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Ujian tidak ditemukan.');
        }

        return view('admin/edit_exam', $data);
    }

    public function updateExam($id)
    {
        $examModel = new ExamModel();

        // Validasi input
        if (
            !$this->validate([
                'title' => 'required',
                'description' => 'required',
                'duration' => 'required|integer'
            ])
        ) {
            return redirect()->back()->with('error', 'Semua field harus diisi dengan benar.');
        }

        // Update data ujian
        $examModel->update($id, [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'duration' => $this->request->getPost('duration'),
        ]);

        return redirect()->to('/admin/manage_exam')->with('success', 'Ujian berhasil diperbarui.');
    }


    public function manageUsers()
    {
        $userModel = new UserModel();
        $data['users'] = $userModel->findAll(); // Ambil semua pengguna dari database

        return view('admin/manage_users', $data);
    }

    public function deleteUser($id)
    {
        $userModel = new UserModel();
        $userModel->delete($id);

        return redirect()->to('/admin/manage_users');
    }

    public function delete_exam($id)
    {
        $examModel = new ExamModel();
        if ($examModel->delete($id)) {
            return redirect()->to('admin/manage_exam')->with('success', 'Ujian berhasil dihapus');
        } else {
            return redirect()->to('admin/manage_exam')->with('error', 'Gagal menghapus ujian');
        }
    }

    public function addExam()
    {
        return view('admin/add_exam'); // Tampilkan form tambah ujian
    }

    public function storeExam()
    {
        $examModel = new ExamModel();

        // Validasi input
        if (
            !$this->validate([
                'title' => 'required',
                'description' => 'required',
                'duration' => 'required|integer'
            ])
        ) {
            return redirect()->back()->with('error', 'Semua field harus diisi dengan benar.');
        }

        // Pastikan sesi user_id ada
        $createdBy = session()->get('user_id');

        if (!$createdBy) {
            return redirect()->back()->with('error', 'User belum login.');
        }

        // Simpan data ujian
        $examModel->insert([
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'duration' => $this->request->getPost('duration'),
            'created_by' => $createdBy,
        ]);

        return redirect()->to('/admin/manage-exam')->with('success', 'Ujian berhasil ditambahkan.');
    }

    public function addQuestion($exam_id)
    {
        $data['exam_id'] = $exam_id;
        return view('admin/add_question', $data);
    }

    public function storeQuestion()
    {
        $questionModel = new QuestionModel();


        if (
            !$this->validate([
                'question_text' => 'required',
                'option_a' => 'required',
                'option_b' => 'required',
                'option_c' => 'required',
                'option_d' => 'required',
                'correct_option' => 'required|in_list[A,B,C,D]'
            ])
        ) {
            return redirect()->back()->with('error', 'Semua field harus diisi dengan benar.');
        }

        // Simpan data soal
        $questionModel->insert([
            'exam_id' => $this->request->getPost('exam_id'),
            'question_text' => $this->request->getPost('question_text'),
            'option_a' => $this->request->getPost('option_a'),
            'option_b' => $this->request->getPost('option_b'),
            'option_c' => $this->request->getPost('option_c'),
            'option_d' => $this->request->getPost('option_d'),
            'correct_option' => $this->request->getPost('correct_option'),
        ]);

        return redirect()->to('/admin/manage-exam')->with('success', 'Soal berhasil ditambahkan.');
    }

    public function get_ujian_harian()
    {
        $db = \Config\Database::connect();
        $query = $this->db->query("
        SELECT DAYNAME(created_at) AS hari, COUNT(*) AS jumlah 
        FROM exams
        WHERE WEEK(created_at) = WEEK(NOW())
        GROUP BY DAYOFWEEK(created_at)
    ");

        $result = $query->getResultArray();

        $hariMap = [
            'Monday' => 0,
            'Tuesday' => 1,
            'Wednesday' => 2,
            'Thursday' => 3,
            'Friday' => 4,
            'Saturday' => 5,
            'Sunday' => 6,
        ];

        $jumlahUjianPerHari = array_fill(0, 7, 0);

        foreach ($result as $row) {
            $index = $hariMap[$row['hari']];
            $jumlahUjianPerHari[$index] = (int) $row['jumlah'];
        }

        return $this->response->setJSON($jumlahUjianPerHari);
    }

    public function importExcel()
    {
        $file = $this->request->getFile('excel_file');
        $examId = $this->request->getPost('exam_id');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid.');
        }

        $filePath = WRITEPATH . 'uploads/' . $file->getRandomName();
        $file->move(WRITEPATH . 'uploads', basename($filePath));

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $questionModel = new \App\Models\QuestionModel();

        for ($i = 1; $i < count($rows); $i++) {
            $data = [
                'exam_id' => $examId,
                'question_text' => $rows[$i][0],
                'option_a' => $rows[$i][1],
                'option_b' => $rows[$i][2],
                'option_c' => $rows[$i][3],
                'option_d' => $rows[$i][4],
                'correct_option' => strtoupper($rows[$i][5]),
            ];
            $questionModel->insert($data);
        }

        return redirect()->to('/admin/manage-exam')->with('success', 'Soal berhasil diimpor.');
    }



}


