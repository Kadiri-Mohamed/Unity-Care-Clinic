<?php
require_once '../autoload.php';

$user = Session::get('user');

$route = $_GET['route'] ;

// echo $route;
switch ($route) {

    case 'login':
        require '../auth/login.php';
        break;

    case 'logout':
        require 'auth/logout.php';
        break;

    case 'admin.dashboard':
        if (!$user || $user['role'] !== 'Admin') {
            header('Location: index.php?route=login');
            exit;
        }
        require 'view/admin/dashboard.php';
        break;

    case 'doctor.dashboard':
        if (!$user || $user['role'] !== 'Doctor') {
            header('Location: index.php?route=login');
            exit;
        }
        require 'view/doctor/dashboard.php';
        break;

    case 'patient.dashboard':
        if (!$user || $user['role'] !== 'Patient') {
            header('Location: index.php?route=login');
            exit;
        }
        require 'view/patient/dashboard.php';
        break;

    default:
        http_response_code(404);
        echo "Page not found";
}
