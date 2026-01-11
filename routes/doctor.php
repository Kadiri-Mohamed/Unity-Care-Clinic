<?php
session_start();

require_once __DIR__ . '/../autoload.php';


// Vérifier authentification
if (!isset($_SESSION['user'])) {
    header("Location: /auth/login.php");
    exit;
}

// Vérifier rôle Doctor
if ($_SESSION['user']['role'] !== 'doctor') {
    header("Location: /unauthorized.php");
    exit;
}

$doctorService      = new DoctorService();
$appointmentService = new AppointmentService();
$patientService     = new PatientService();

$action = $_GET['action'] ?? 'dashboard';

switch ($action) {

    // ================= Dashboard =================
    case 'dashboard':
        $doctorId = $_SESSION['user']['id'];

        $stats = $doctorService->getDashboardStats($doctorId);
        require_once __DIR__ . '/../view/doctor/dashboard.php';
        break;

    // ================= Appointments =================
    case 'appointments':
        $doctorId = $_SESSION['user']['id'];

        $appointments = $appointmentService->getAppointmentsByDoctor($doctorId);
        require_once __DIR__ . '/../view/doctor/appointments/index.php';
        break;

    case 'appointment_show':
        $id       = $_GET['id'];
        $doctorId = $_SESSION['user']['id'];

        $appointment = $appointmentService->getAppointmentByIdAndDoctor($id, $doctorId);
        require_once __DIR__ . '/../view/doctor/appointments/show.php';
        break;

    case 'appointment_status':
        $id       = $_POST['id'];
        $status   = $_POST['status'];
        $doctorId = $_SESSION['user']['id'];

        $appointmentService->updateStatusByDoctor($id, $status, $doctorId);
        header("Location: doctor.php?action=appointments");
        break;

    // ================= Patients =================
    case 'patients':
        $doctorId = $_SESSION['user']['id'];

        $patients = $patientService->getPatientsByDoctor($doctorId);
        require_once __DIR__ . '/../view/doctor/patients/index.php';
        break;

    case 'patient_show':
        $id       = $_GET['id'];
        $doctorId = $_SESSION['user']['id'];

        $patient = $patientService->getPatientByIdAndDoctor($id, $doctorId);
        require_once __DIR__ . '/../view/doctor/patients/show.php';
        break;

    // ================= Profile =================
    case 'profile':
        $doctorId = $_SESSION['user']['id'];

        $doctor = $doctorService->getDoctorById($doctorId);
        require_once __DIR__ . '/../view/doctor/profile.php';
        break;

    case 'profile_update':
        $doctorId = $_SESSION['user']['id'];

        $doctorService->updateProfile($doctorId, $_POST);
        header("Location: doctor.php?action=profile");
        break;

    // ================= Logout =================
    // case 'logout':
    //     AuthService::logout();
    //     header("Location: /auth/login.php");
    //     break;

    default:
        require_once __DIR__ . '/../view/doctor/dashboard.php';
        break;
}
