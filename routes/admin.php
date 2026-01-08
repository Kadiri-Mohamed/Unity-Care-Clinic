<?php
require_once '../autoload.php';

Session::init();
if (!Session::isLoggedIn() || Session::getUserRole() !== 'Admin') {
    header('Location: ../auth/login.php');
    exit();
}

$adminService = new AdminService();
$action = $_GET['action'] ?? '';

switch ($action) {
    // ==================== Doctor Actions ====================
    case 'create-doctor':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $adminService->createDoctor($_POST);
            if ($result['success']) {
                header('Location: router.php?route=admin&doctors&success=' . urlencode($result['message']));
            } else {
                header('Location: router.php?route=admin&doctors&error=' . urlencode($result['message']));
            }
            exit();
        }
        break;

    case 'update-doctor':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $result = $adminService->updateDoctor($id, $_POST);
            if ($result['success']) {
                header('Location: router.php?route=admin&doctors&success=' . urlencode($result['message']));
            } else {
                header('Location: router.php?route=admin&doctors&error=' . urlencode($result['message']));
            }
            exit();
        }
        break;

    case 'delete-doctor':
        $id = $_GET['id'] ?? 0;
        $result = $adminService->deleteDoctor($id);
        if ($result['success']) {
            header('Location: router.php?route=admin&doctors&success=' . urlencode($result['message']));
        } else {
            header('Location: router.php?route=admin&doctors&error=' . urlencode($result['message']));
        }
        exit();
        break;

    // ==================== Patient Actions ====================
    case 'create-patient':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $adminService->createPatient($_POST);
            if ($result['success']) {
                header('Location: router.php?route=admin&patients&success=' . urlencode($result['message']));
            } else {
                header('Location: router.php?route=admin&patients&error=' . urlencode($result['message']));
            }
            exit();
        }
        break;

    case 'update-patient':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $result = $adminService->updatePatient($id, $_POST);
            if ($result['success']) {
                header('Location: router.php?route=admin&patients&success=' . urlencode($result['message']));
            } else {
                header('Location: router.php?route=admin&patients&error=' . urlencode($result['message']));
            }
            exit();
        }
        break;

    case 'delete-patient':
        $id = $_GET['id'] ?? 0;
        $result = $adminService->deletePatient($id);
        if ($result['success']) {
            header('Location: router.php?route=admin&patients&success=' . urlencode($result['message']));
        } else {
            header('Location: router.php?route=admin&patients&error=' . urlencode($result['message']));
        }
        exit();
        break;

    // ==================== Appointment Actions ====================
    case 'delete-appointment':
        $id = $_GET['id'] ?? 0;
        $result = $adminService->deleteAppointment($id);
        if ($result['success']) {
            header('Location: router.php?route=admin&appointments&success=' . urlencode($result['message']));
        } else {
            header('Location: router.php?route=admin&appointments&error=' . urlencode($result['message']));
        }
        exit();
        break;

    case 'update-appointment-status':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $status = $_POST['status'] ?? '';
            $result = $adminService->updateAppointmentStatus($id, $status);
            if ($result['success']) {
                header('Location: router.php?route=admin&appointments&success=' . urlencode($result['message']));
            } else {
                header('Location: router.php?route=admin&appointments&error=' . urlencode($result['message']));
            }
            exit();
        }
        break;

    // ==================== Medication Actions ====================
    case 'create-medication':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $adminService->createMedication($_POST);
            if ($result['success']) {
                header('Location: router.php?route=admin&medications&success=' . urlencode($result['message']));
            } else {
                header('Location: router.php?route=admin&medications&error=' . urlencode($result['message']));
            }
            exit();
        }
        break;

    case 'update-medication':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $result = $adminService->updateMedication($id, $_POST);
            if ($result['success']) {
                header('Location: router.php?route=admin&medications&success=' . urlencode($result['message']));
            } else {
                header('Location: router.php?route=admin&medications&error=' . urlencode($result['message']));
            }
            exit();
        }
        break;

    case 'delete-medication':
        $id = $_GET['id'] ?? 0;
        $result = $adminService->deleteMedication($id);
        if ($result['success']) {
            header('Location: router.php?route=admin&medications&success=' . urlencode($result['message']));
        } else {
            header('Location: router.php?route=admin&medications&error=' . urlencode($result['message']));
        }
        exit();
        break;

    default:
        header('Location: router.php?route=admin');
        exit();
}
