<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('/register', 'AuthController::register');
$routes->post('/register/store', 'AuthController::store');
$routes->get('/login', 'AuthController::login');
$routes->post('/auth', 'AuthController::auth');
$routes->get('/logout', 'AuthController::logout');
$routes->get('admin/manage-exam', 'Admin::manageExam');
$routes->post('peserta/submitExam', 'Peserta::submitExam');
$routes->get('peserta/exam/(:num)', 'Peserta::exam/$1');
$routes->post('peserta/submitExam', 'Peserta::submitExam');
$routes->get('peserta/result/(:num)', 'Peserta::result/$1');
$routes->get('/peserta/history', 'Peserta::history');



$routes->group('admin', function($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
    $routes->get('manage-exam', 'Admin::manageExam');
    $routes->get('manage-users', 'Admin::manageUsers');
});

$routes->group('peserta', function($routes) {
    $routes->get('dashboard', 'Peserta::dashboard');
    $routes->get('exam', 'Peserta::startExam');
    $routes->get('result', 'Peserta::showResult');
});

$routes->get('admin/get-ujian-harian', 'Admin::get_ujian_harian');

$routes->get('/dashboard', 'Admin::dashboard');
$routes->get('/peserta/dashboard', 'Peserta::dashboard');

$routes->get('/admin/manage_users', 'Admin::manageUsers');
$routes->get('/admin/delete_user/(:num)', 'Admin::deleteUser/$1');


$routes->get('/peserta/start_exam/(:num)', 'Peserta::startExam/$1');
$routes->post('/peserta/submit_exam', 'Peserta::submitExam');

$routes->get('admin/edit_exam/(:num)', 'Admin::editExam/$1');
$routes->post('admin/update_exam/(:num)', 'Admin::updateExam/$1');

$routes->get('admin/manage_exam', 'Admin::manageExam');
$routes->post('admin/update_exam/(:num)', 'Admin::updateExam/$1');


$routes->get('admin/delete_exam/(:num)', 'Admin::delete_exam/$1');
$routes->post('admin/delete_exam/(:num)', 'AdminController::delete_exam/$1');


$routes->get('/admin/add-exam', 'Admin::addExam');
$routes->post('/admin/store-exam', 'Admin::storeExam');


$routes->get('/admin/add-question/(:num)', 'Admin::addQuestion/$1');
$routes->post('/admin/store-question', 'Admin::storeQuestion');

$routes->post('/admin/import-excel', 'Admin::importExcel');
$routes->post('admin/importExcel', 'Admin::importExcel');
