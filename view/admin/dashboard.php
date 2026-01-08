<?php
Session::init();
if (!Session::isLoggedIn() || Session::getUserRole() !== 'Admin') {
    header('Location: ../auth/login.php');
    exit();
}

$adminService = new AdminService();
$stats = $adminService->getStatistics();
$appointments = $adminService->getAllAppointments();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Unity Care Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/style.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary) 0%, #0d2652 100%);
            color: white;
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .navbar {
            background-color: white !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: var(--accent);
        }

        .sidebar .nav-link i {
            width: 24px;
            margin-right: 10px;
        }

        .stat-card {
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            color: white;
            transition: transform 0.3s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.9;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 2.2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }

            .sidebar .nav-link span {
                display: none;
            }

            .sidebar .nav-link i {
                margin-right: 0;
            }

            .main-content {
                margin-left: 70px;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column">
        <div class="p-4 text-center border-bottom">
            <h4 class="mb-0">
                <i class="fas fa-hospital me-2"></i>
                <span class="d-none d-md-inline">UCC Admin</span>
            </h4>
        </div>

        <nav class="nav flex-column mt-3">
            <a href="../routes/router.php?route=admin" class="nav-link active">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="../routes/router.php?route=admin&doctors" class="nav-link">
                <i class="fas fa-user-md"></i>
                <span>Médecins</span>
            </a>
            <a href="../routes/router.php?route=admin&patients" class="nav-link">
                <i class="fas fa-users"></i>
                <span>Patients</span>
            </a>
            <a href="../routes/router.php?route=admin&appointments" class="nav-link">
                <i class="fas fa-calendar-check"></i>
                <span>Rendez-vous</span>
            </a>
            <a href="../routes/router.php?route=admin&medications" class="nav-link">
                <i class="fas fa-pills"></i>
                <span>Médicaments</span>
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
                    <h5 class="mb-0 text-primary">Dashboard Admin</h5>
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-3 text-muted">
                        <i class="fas fa-user-circle me-2"></i>
                        <?= htmlspecialchars($_SESSION['user']['nom'] . " " . $_SESSION['user']['prenom']) ?>
                    </span>
                    <div class="language-selector">
                        <select class="form-select form-select-sm">
                            <option value="fr">🇫🇷 Français</option>
                            <option value="en">🇬🇧 English</option>
                        </select>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card card-patients">
                    <i class="fas fa-user-md stat-icon"></i>
                    <div class="stat-number"><?= $stats['doctors'] ?? 0 ?></div>
                    <div class="stat-label">Médecins</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card card-departments">
                    <i class="fas fa-users stat-icon"></i>
                    <div class="stat-number"><?= $stats['patients'] ?? 0 ?></div>
                    <div class="stat-label">Patients</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card card-medecins">
                    <i class="fas fa-calendar-alt stat-icon"></i>
                    <div class="stat-number"><?= $stats['appointments'] ?? 0 ?></div>
                    <div class="stat-label">Rendez-vous</div>
                </div>
            </div>
        </div>

        <!-- Recent Appointments -->
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Rendez-vous Récents</h5>
                <a href="../routes/router.php?route=admin&appointments" class="btn btn-sm btn-light">Voir tous</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Date</th>
                                <th>Heure</th>
                                <th>Patient</th>
                                <th>Médecin</th>
                                <th>Spécialité</th>
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
                                        <td><?= htmlspecialchars($appointment['doctor_name']) ?></td>
                                        <td><?= htmlspecialchars($appointment['specialite']) ?></td>
                                        <td>
                                            <?php
                                            $statusClass = [
                                                'pending' => 'warning',
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
                                            <button class="btn btn-sm btn-primary" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-calendar-alt fa-2x text-muted mb-3"></i>
                                        <p class="text-muted">Aucun rendez-vous trouvé</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Actions Rapides</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <a href="../routes/router.php?route=admin&doctors"
                                    class="btn btn-primary w-100 d-flex align-items-center justify-content-center py-3">
                                    <i class="fas fa-user-md fa-2x me-3"></i>
                                    <div>
                                        <div class="fw-bold">Gérer Médecins</div>
                                        <small>Ajouter, modifier</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="../routes/router.php?route=admin&patients"
                                    class="btn btn-success w-100 d-flex align-items-center justify-content-center py-3">
                                    <i class="fas fa-user-plus fa-2x me-3"></i>
                                    <div>
                                        <div class="fw-bold">Gérer Patients</div>
                                        <small>Ajouter, modifier</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Statistiques Rapides</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="h3 text-primary"><?= $stats['doctors'] ?? 0 ?></div>
                                <small>Médecins actifs</small>
                            </div>
                            <div class="col-4">
                                <div class="h3 text-success"><?= $stats['patients'] ?? 0 ?></div>
                                <small>Patients</small>
                            </div>
                            <div class="col-4">
                                <div class="h3 text-warning"><?= $stats['appointments'] ?? 0 ?></div>
                                <small>RDV ce mois</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light border-top py-3 mt-4" style="margin-left: 250px;">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">
                        <i class="fas fa-hospital text-primary me-2"></i>
                        <strong>Unity Care Clinic</strong> - Système de gestion médicale
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-muted">
                        <i class="fas fa-copyright me-1"></i> <?= date('Y') ?> Tous droits réservés
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Language switcher
        document.querySelector('.language-selector select').addEventListener('change', function () {
            alert('Langue changée en: ' + this.value);
        });

        // Update time
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
                <h5 class="mb-0 text-primary">Dashboard Admin</h5>
                <small class="text-muted">${dateStr} ${timeStr}</small>
            `;
        }
        setInterval(updateTime, 1000);
        updateTime();
    </script>
</body>

</html>