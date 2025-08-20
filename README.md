<h1 align="center">Examination</h1>

<p align="center">
  <strong>Aplikasi Ujian Online Berbasis Web</strong><br>
  <i>Dibangun menggunakan CodeIgniter 4 untuk kebutuhan ujian modern, cepat, dan aman.</i>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/CI4-Framework-blue?style=flat-square" />
  <img src="https://img.shields.io/badge/PHP-8.1%2B-green?style=flat-square" />
  <img src="https://img.shields.io/badge/MySQL-Database-orange?style=flat-square" />
  <img src="https://img.shields.io/badge/Bootstrap-Frontend-purple?style=flat-square" />
</p>

---

## Fitur Utama

- **Autentikasi** dengan fitur register dan login
- **Dark Mode** pada login & register
- **Admin Panel** untuk kelola user, ujian, dan soal
- **Tambah & Edit Soal Ujian**
- **Timer Otomatis** untuk setiap ujian
- **Rekap Nilai** Peserta yang sudah selesai otomatis terekap pada Spreadsheet
- **Multiple Choice Questions (MCQ)**

---

## Teknologi yang Digunakan

| Backend  | PHP 8.1+, CodeIgniter 4 |
|------------|--------------------------|
| Frontend| HTML5, CSS, JavaScript, Bootstrap |
| Database| MySQL                    |
| Server  | Apache (XAMPP) |
| Spreadsheet  | Google API |


---

## Tampilan Aplikasi

### Homepage
![Homepage](/ss/homepage.png)

---

### Login & Register (dengan dark mode)
<div align="center">
  <img src="/ss/login-1.png" width="48%">
  <img src="/ss/register-1.png" width="48%">
</div>

---

## Halaman Admin

| Fitur                     | Gambar |
|--------------------------|--------|
| Dashboard Admin        | ![Dashboard Admin](/ss/admin_dashboard.png) |
| Kelola User            | ![Kelola User](/ss/kelola-user.png) |
| Kelola Ujian           | ![Kelola Ujian](/ss/kelola-ujian.png) |
| Tambah Ujian           | ![Tambah Ujian](/ss/tambah-ujian.png) |
| Tambah Soal            | ![Tambah Soal](/ss/tambah-soal.png) |
| Edit Ujian             | ![Edit Ujian](/ss/edit-ujian.png) |


---

## Halaman Peserta

| Fitur                  | Gambar |
|-----------------------|--------|
| Dashboard Peserta   | ![Dashboard Peserta](/ss/peserta_dashboard.png) |
| History Peserta   | ![Dashboard Peserta](/ss/history.png) |

---


## Tim Pengembang

| Nama                         | NIM           | Role |
|------------------------------|---------------|----- |
| Arya Wiratama                | 312310224     | Backend & Frontend |
| Farel Aryaduta Daniswara     | 312310810     | Frontend & Backend | 
| Andrian Lusmana              | 312310199     | Examination Designer |
| Yudi Gunawan                 | 312310179     | Project Manager |

## Struktur Database

```sql
CREATE DATABASE examnation_db;
USE examnation_db;

-- Tabel users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'peserta') NOT NULL DEFAULT 'peserta',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel exams
CREATE TABLE exams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    total_questions INT NOT NULL,
    duration INT NOT NULL, -- dalam menit
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    exam_stat ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel questions
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT NOT NULL,
    question_text TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_option ENUM('A', 'B', 'C', 'D') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
);

-- Tabel results
CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    exam_id INT NOT NULL,
    score INT NOT NULL,
    taken_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
);

-- Tabel log aktivitas peserta saat ujian
CREATE TABLE exam_activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    exam_id INT NOT NULL,
    activity VARCHAR(255) NOT NULL,
    logged_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
);
```
