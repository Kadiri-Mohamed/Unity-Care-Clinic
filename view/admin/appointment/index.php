<?php
Session::init();
if (!Session::isLoggedIn() || Session::getUserRole() !== 'Admin') {
    header('Location: ../../auth/login.php');
    exit();
}

$adminService = new AdminService();
$appointments = $adminService->getAllAppointments();

// Statistiques pour les graphiques
$stats = [
    'total' => count($appointments),
    'status_counts' => [
        'pending' => 0,
        'confirmed' => 0,
        'cancelled' => 0,
        'completed' => 0
    ],
    'monthly_counts' => [],
    'doctor_counts' => []
];

foreach ($appointments as $appointment) {
    // Compter par statut
    $status = strtolower($appointment['status']);
    if (isset($stats['status_counts'][$status])) {
        $stats['status_counts'][$status]++;
    }

    // Compter par mois
    $month = date('Y-m', strtotime($appointment['date_rdv']));
    if (!isset($stats['monthly_counts'][$month])) {
        $stats['monthly_counts'][$month] = 0;
    }
    $stats['monthly_counts'][$month]++;

    // Compter par médecin
    $doctorName = $appointment['doctor_name'];
    if (!isset($stats['doctor_counts'][$doctorName])) {
        $stats['doctor_counts'][$doctorName] = 0;
    }
    $stats['doctor_counts'][$doctorName]++;
}

// Trier les données pour les graphiques
arsort($stats['doctor_counts']);
$doctor_counts = array_slice($stats['doctor_counts'], 0, 5, true); // Top 5 médecins

// Données pour les 6 derniers mois
$last6months = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $last6months[$month] = $stats['monthly_counts'][$month] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Rendez-vous - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 30px;
        }

        .appointment-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            margin: 0 2px;
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
            <a href="../routes/router.php?route=admin" class="nav-link">
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
            <a href="../routes/router.php?route=admin&appointments" class="nav-link active">
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
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-calendar-check me-2"></i>Gestion des Rendez-vous
                    </h5>
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
            <div class="col-md-3">
                <div class="stat-card card-patients">
                    <i class="fas fa-calendar-alt stat-icon"></i>
                    <div class="stat-number"><?= $stats['total'] ?></div>
                    <div class="stat-label">Total RDV</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);">
                    <i class="fas fa-check-circle stat-icon"></i>
                    <div class="stat-number"><?= $stats['status_counts']['confirmed'] ?></div>
                    <div class="stat-label">Confirmés</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card"
                    style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: #134686;">
                    <i class="fas fa-clock stat-icon"></i>
                    <div class="stat-number"><?= $stats['status_counts']['pending'] ?></div>
                    <div class="stat-label">En attente</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                    <i class="fas fa-times-circle stat-icon"></i>
                    <div class="stat-number"><?= $stats['status_counts']['cancelled'] ?></div>
                    <div class="stat-label">Annulés</div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Statut des Rendez-vous</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Évolution (6 derniers mois)</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doctor Statistics -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-user-md me-2"></i>Top 5 Médecins (par RDV)</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="doctorChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments Table -->
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Tous les Rendez-vous</h5>
                <div>
                    <select class="form-select form-select-sm d-inline-block w-auto me-2" id="filterStatus">
                        <option value="all">Tous les statuts</option>
                        <option value="pending">En attente</option>
                        <option value="confirmed">Confirmé</option>
                        <option value="cancelled">Annulé</option>
                        <option value="completed">Terminé</option>
                    </select>
                    <input type="date" class="form-control form-control-sm d-inline-block w-auto" id="filterDate">
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="appointmentsTable">
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
                                <?php foreach ($appointments as $appointment): ?>
                                    <tr data-status="<?= strtolower($appointment['status']) ?>"
                                        data-date="<?= $appointment['date_rdv'] ?>">
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
                                            ][strtolower($appointment['status'])] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $statusClass ?> appointment-badge">
                                                <?= ucfirst($appointment['status']) ?>
                                            </span>
                                        </td>
                                        <td class="action-buttons">
                                            <button class="btn btn-sm btn-primary"
                                                onclick="viewAppointment(<?= $appointment['id'] ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning"
                                                onclick="updateStatus(<?= $appointment['id'] ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger"
                                                onclick="deleteAppointment(<?= $appointment['id'] ?>)">
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
        // Données pour les graphiques
        const statusData = {
            labels: ['Confirmés', 'En attente', 'Annulés', 'Terminés'],
            datasets: [{
                data: [
                    <?= $stats['status_counts']['confirmed'] ?>,
                    <?= $stats['status_counts']['pending'] ?>,
                    <?= $stats['status_counts']['cancelled'] ?>,
                    <?= $stats['status_counts']['completed'] ?>
                ],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545',
                    '#17a2b8'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        };

        const monthlyData = {
            labels: <?= json_encode(array_map(function ($month) {
                return date('M Y', strtotime($month . '-01'));
            }, array_keys($last6months))) ?>,
            datasets: [{
                label: 'Nombre de RDV',
                data: <?= json_encode(array_values($last6months)) ?>,
                backgroundColor: 'rgba(19, 70, 134, 0.2)',
                borderColor: 'rgba(19, 70, 134, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        };

        const doctorData = {
            labels: <?= json_encode(array_keys($doctor_counts)) ?>,
            datasets: [{
                label: 'Nombre de RDV',
                data: <?= json_encode(array_values($doctor_counts)) ?>,
                backgroundColor: [
                    'rgba(19, 70, 134, 0.7)',
                    'rgba(237, 63, 39, 0.7)',
                    'rgba(254, 178, 26, 0.7)',
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(108, 117, 125, 0.7)'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        };

        // Initialiser les graphiques
        document.addEventListener('DOMContentLoaded', function () {
            // Graphique des statuts (Pie)
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'pie',
                data: statusData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Graphique mensuel (Line)
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            new Chart(monthlyCtx, {
                type: 'line',
                data: monthlyData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    }
                }
            });

            // Graphique des médecins (Bar)
            const doctorCtx = document.getElementById('doctorChart').getContext('2d');
            new Chart(doctorCtx, {
                type: 'bar',
                data: doctorData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });

        // Filtrage du tableau
        document.getElementById('filterStatus').addEventListener('change', filterTable);
        document.getElementById('filterDate').addEventListener('change', filterTable);

        function filterTable() {
            const statusFilter = document.getElementById('filterStatus').value;
            const dateFilter = document.getElementById('filterDate').value;
            const rows = document.querySelectorAll('#appointmentsTable tbody tr');

            rows.forEach(row => {
                const status = row.getAttribute('data-status');
                const date = row.getAttribute('data-date');
                let showRow = true;

                if (statusFilter !== 'all' && status !== statusFilter) {
                    showRow = false;
                }

                if (dateFilter && date !== dateFilter) {
                    showRow = false;
                }

                row.style.display = showRow ? '' : 'none';
            });
        }

        // Fonctions d'actions
        function viewAppointment(id) {
            alert('Voir le rendez-vous #' + id);
        }

        function updateStatus(id) {
            const newStatus = prompt('Changer le statut (pending/confirmed/cancelled/completed):');
            if (newStatus) {
                window.location.href = `../../routes/admin.php?action=update-appointment-status&id=${id}&status=${newStatus}`;
            }
        }

        function deleteAppointment(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce rendez-vous ?')) {
                window.location.href = `../../routes/admin.php?action=delete-appointment&id=${id}`;
            }
        }

        // Mise à jour de l'heure
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
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-calendar-check me-2"></i>Gestion des Rendez-vous
                    <small class="text-muted d-block">${dateStr} ${timeStr}</small>
                </h5>
            `;
        }
        setInterval(updateTime, 1000);
        updateTime();
    </script>
</body>

</html>