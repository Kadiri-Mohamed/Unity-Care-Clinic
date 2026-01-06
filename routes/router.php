<?php
require_once '../autoload.php';
Session::init();
$user = Session::get('user');

$route = $_GET['route'] ;

// echo $route;
switch ($route) {

    case 'login':
        echo "<script>location.href = '../auth/login.php';</script>";
        break;

    case 'logout':
        echo "<script>location.href = '../auth/login.php';</script>";
        break;

    case 'admin':
        if (!$user || $user['role'] !== 'Admin') {
            header('Location: ../index.php?route=login');
            exit;
        }
        require '../view/admin/dashboard.php';
        break;

    case 'doctor':
        if (!$user || $user['role'] !== 'Doctor') {
            header('Location: ../index.php?route=login');
            exit;
        }
        require '../view/doctor/dashboard.php';
        break;

    case 'patient':
        if (!$user || $user['role'] !== 'Patient') {
            header('Location: ../index.php?route=login');
            exit;
        }
        require '../view/patient/dashboard.php';
        break;

    default:
        http_response_code(404);
        echo "Page not found";
}
