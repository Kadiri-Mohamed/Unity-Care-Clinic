<?php
require_once '../autoload.php';
Session::init();
$user = Session::get('user');

// Check if 'route' parameter exists, otherwise check for other parameters
$route = $_GET['route'] ?? null;

// If no route parameter but we have other admin parameters, set route to 'admin'
if (!$route && (isset($_GET['doctors']) || isset($_GET['patients']) || isset($_GET['appointments']) || isset($_GET['dashboard']))) {
    $route = 'admin';
}

switch ($route) {
    case 'login':
        echo "<script>location.href = '../auth/login.php';</script>";
        break;

    case 'logout':
        echo "<script>location.href = '../auth/login.php';</script>";
        break;

    case 'admin':
        if (!$user || $user['role'] !== 'Admin') {
            header('Location: ../auth/login.php');
            exit;
        }

        // Handle different admin pages
        if (isset($_GET['doctors'])) {
            require '../view/admin/doctor/index.php';
        } elseif (isset($_GET['patients'])) {
            require '../view/admin/patient/index.php';
        } elseif (isset($_GET['appointments'])) {
            require '../view/admin/appointment/index.php';
        } elseif (isset($_GET['medications'])) {
            require '../view/admin/medication/index.php';
        } else {
            require '../view/admin/dashboard.php';
        }
        break;

    case 'doctor':
        if (!$user || $user['role'] !== 'Doctor') {
            header('Location: ../auth/login.php');
            exit;
        }
        require '../view/doctor/dashboard.php';
        break;

    case 'patient':
        if (!$user || $user['role'] !== 'Patient') {
            header('Location: ../auth/login.php');
            exit;
        }
        require '../view/patient/dashboard.php';
        break;

    default:
        // If no valid route, redirect to login or show 404
        if ($user && $user['role'] === 'Admin') {
            header('Location: router.php?route=admin');
            exit;
        } elseif ($user) {
            header('Location: router.php?route=' . strtolower($user['role']));
            exit;
        } else {
            header('Location: ../auth/login.php');
            exit;
        }
}