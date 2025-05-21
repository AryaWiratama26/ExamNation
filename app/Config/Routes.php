<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Auth routes
$routes->get('/register', 'AuthController::register');
$routes->post('/register/store', 'AuthController::store');
$routes->get('/login', 'AuthController::login');
$routes->post('/auth', 'AuthController::auth');
$routes->get('/logout', 'AuthController::logout');

// Admin routes group with auth filter
$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Admin::dashboard');
    $routes->get('dashboard', 'Admin::dashboard');
    $routes->get('manage-exam', 'Admin::manageExam');
    $routes->get('manage-users', 'Admin::manageUsers');
    $routes->get('add-exam', 'Admin::addExam');
    $routes->post('store-exam', 'Admin::storeExam');
    $routes->get('edit-exam/(:num)', 'Admin::editExam/$1');
    $routes->post('update-exam/(:num)', 'Admin::updateExam/$1');
    $routes->get('delete-exam/(:num)', 'Admin::deleteExam/$1');
    $routes->post('toggle-exam-status/(:num)', 'Admin::toggleExamStatus/$1');
    
    // Violations monitoring routes
    $routes->get('violations', 'ExamController::viewLogs');
    $routes->get('violations/(:num)', 'ExamController::viewLogs/$1');
    $routes->get('exportViolations/(:num)', 'ExamController::exportActivityLogs/$1');
});

// Keep this route outside for AJAX calls from exam page
$routes->post('exam/logActivity', 'ExamController::logActivity');

// Participant routes
$routes->group('peserta', function($routes) {
    $routes->get('dashboard', 'Peserta::dashboard');
    $routes->get('exam/(:num)', 'Peserta::exam/$1');
    $routes->post('submitExam', 'Peserta::submitExam');
    $routes->get('result/(:num)', 'Peserta::result/$1');
    $routes->get('history', 'Peserta::history');
    $routes->get('history/pdf', 'Peserta::downloadHistoryPdf');
});

$routes->get('admin/get-ujian-harian', 'Admin::get_ujian_harian');

$routes->get('/admin/manage_users', 'Admin::manageUsers');
$routes->get('/admin/delete_user/(:num)', 'Admin::deleteUser/$1');

$routes->get('/peserta/start_exam/(:num)', 'Peserta::startExam/$1');
$routes->post('/peserta/submit_exam', 'Peserta::submitExam');

$routes->get('/admin/add-question/(:num)', 'Admin::addQuestion/$1');
$routes->post('/admin/store-question', 'Admin::storeQuestion');

$routes->post('/admin/import-excel', 'Admin::importExcel');
$routes->post('admin/importExcel', 'Admin::importExcel');

$routes->get('/peserta/history/pdf', 'Peserta::downloadHistoryPdf');
$routes->get('peserta/downloadHistoryPdf', 'Peserta::downloadHistoryPdf');

$routes->get('admin/add_exam', 'Admin::addExam');
