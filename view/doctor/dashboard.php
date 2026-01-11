<?php
Session::init();
if (!Session::isLoggedIn() || Session::getUserRole() !== 'Doctor') {
    header('Location: ../auth/login.php');
    exit();
}

// $doctorId = Session::getUserId();

$doctorService      = new DoctorService();
$appointmentService = new AppointmentService();

// $stats        = $doctorService->getDashboardStats($doctorId);
// $appointments = $appointmentService->getAppointmentsByDoctor($doctorId);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Doctor - Unity Care Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/style.css">
</head>

<body>
<!-- Sidebar -->
<div class="sidebar d-flex flex-column">
    <div class="p-4 text-center border-bottom">
        <h4 class="mb-0">
            <i class="fas fa-user-md me-2"></i>
            <span class="d-none d-md-inline">UCC Doctor</span>
        </h4>
    </div>

    <nav class="nav flex-column mt-3">
        <a href="../routes/router.php?route=doctor" class="nav-link active">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        <a href="../routes/router.php?route=doctor&appointments" class="nav-link">
            <i class="fas fa-calendar-check"></i>
            <span>Mes Rendez-vous</span>
        </a>
        <a href="../routes/router.php?route=doctor&patients" class="nav-link">
            <i class="fas fa-users"></i>
            <span>Mes Patients</span>
        </a>
        <a href="../routes/router.php?route=doctor&medications" class="nav-link">
            <i class="fas fa-pills"></i>
            <span>Prescriptions</span>
        </a>
        <a href="../routes/router.php?route=doctor&profile" class="nav-link">
            <i class="fas fa-user-cog"></i>
            <span>Mon Profil</span>
        </a>
        <a href="../auth/logout.php" class="nav-link text-danger mt-5">
            <i class="fas fa-sign-out-alt"></i>
            <span>Déconnexion</span>
        </a>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light mb-4">
        <div class="container-fluid">
            <div class="navbar-brand">
                <h5 class="mb-0 text-success">Dashboard Doctor</h5>
            </div>
            <div class="d-flex align-items-center">
                <span class="me-3 text-muted">
                    <i class="fas fa-user-circle me-2"></i>
                    <?= htmlspecialchars($_SESSION['user']['nom'] . " " . $_SESSION['user']['prenom']) ?>
                </span>
            </div>
        </div>
    </nav>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card card-patients">
                <i class="fas fa-calendar-day stat-icon"></i>
                <div class="stat-number"><?= $stats['appointments_today'] ?? 0 ?></div>
                <div class="stat-label">RDV Aujourd’hui</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card card-departments">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-number"><?= $stats['total_patients'] ?? 0 ?></div>
                <div class="stat-label">Mes Patients</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card card-medecins">
                <i class="fas fa-file-medical stat-icon"></i>
                <div class="stat-number"><?= $stats['total_appointments'] ?? 0 ?></div>
                <div class="stat-label">Total Consultations</div>
            </div>
        </div>
    </div>

    <!-- Recent Appointments -->
    <div class="card">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Mes Rendez-vous Récents</h5>
            <a href="../routes/router.php?route=doctor&appointments" class="btn btn-sm btn-light">Voir tous</a>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>#ID</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Patient</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($appointments)): ?>
                    <?php foreach (array_slice($appointments, 0, 5) as $appointment): ?>
                        <tr>
                            <td>#<?= $appointment['id'] ?></td>
                            <td><?= date('d/m/Y', strtotime($appointment['date_rdv'])) ?></td>
                            <td><?= $appointment['heure'] ?></td>
                            <td><?= htmlspecialchars($appointment['patient_name']) ?></td>
                            <td>
                                <?php
                                $statusClass = [
                                    'pending'   => 'warning',
                                    'confirmed' => 'success',
                                    'cancelled' => 'danger',
                                    'completed' => 'info'
                                ][$appointment['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $statusClass ?>">
                                    <?= ucfirst($appointment['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="../routes/router.php?route=doctor&appointment_show&id=<?= $appointment['id'] ?>"
                                   class="btn btn-sm btn-success">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Aucun rendez-vous trouvé</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function updateTime() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('fr-FR');
        const dateStr = now.toLocaleDateString('fr-FR', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        document.querySelector('.navbar-brand').innerHTML = `
            <h5 class="mb-0 text-success">Dashboard Doctor</h5>
            <small class="text-muted">${dateStr} ${timeStr}</small>
        `;
    }
    setInterval(updateTime, 1000);
    updateTime();
</script>

</body>
</html>
