<?php
Session::init();
if (!Session::isLoggedIn() || Session::getUserRole() !== 'Admin') {
    header('Location: ../../auth/login.php');
    exit();
}

$adminService = new AdminService();
$patients = $adminService->getAllPatients();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Patients - Admin</title>
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

        .patient-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--secondary);
        }

        .patient-card {
            border-radius: 10px;
            border: 1px solid #dee2e6;
            transition: all 0.3s;
            margin-bottom: 20px;
        }

        .patient-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
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
            <a href="../routes/router.php?route=admin&patients" class="nav-link active">
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
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-users me-2"></i>Gestion des Patients
                    </h5>
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-3 text-muted">
                        <i class="fas fa-user-circle me-2"></i>
                        <?= htmlspecialchars($_SESSION['user']['nom'] . " " . $_SESSION['user']['prenom']) ?>
                    </span>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPatientModal">
                        <i class="fas fa-plus me-2"></i>Ajouter Patient
                    </button>
                </div>
            </div>
        </nav>

        <!-- Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                <?= htmlspecialchars($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Patients Cards -->
        <div class="row">
            <?php if (!empty($patients)): ?>
                <?php foreach ($patients as $patient): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card patient-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($patient['nom'] . '+' . $patient['prenom']) ?>&background=ED3F27&color=fff&size=60"
                                        alt="Photo" class="patient-img me-3">
                                    <div>
                                        <h6 class="mb-0"><?= htmlspecialchars($patient['nom'] . ' ' . $patient['prenom']) ?>
                                        </h6>
                                        <small class="text-muted">ID: #<?= $patient['id'] ?></small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <p class="mb-1">
                                        <i class="fas fa-envelope text-primary me-2"></i>
                                        <?= htmlspecialchars($patient['email'] ?? '') ?>
                                    </p>
                                    <p class="mb-1">
                                        <i class="fas fa-phone text-success me-2"></i>
                                        <?= htmlspecialchars($patient['telephone'] ?? '') ?>
                                    </p>
                                    <p class="mb-0">
                                        <i class="fas fa-birthday-cake text-warning me-2"></i>
                                        Né(e) le <?= date('d/m/Y', strtotime($patient['date_naissance'] ?? date('Y-m-d'))) ?>
                                    </p>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="?profile=<?= $patient['id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye me-1"></i>Profil
                                    </a>
                                    <div>
                                        <button class="btn btn-sm btn-warning" onclick="editPatient(<?= $patient['id'] ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deletePatient(<?= $patient['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="card text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucun patient trouvé</h5>
                        <p class="text-muted">Cliquez sur "Ajouter Patient" pour créer un nouveau patient</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Patient Modal -->
    <div class="modal fade" id="addPatientModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="../../routes/admin.php?action=create-patient" method="POST">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-user-plus me-2"></i>Ajouter un Patient
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom *</label>
                                <input type="text" class="form-control" name="nom" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prénom *</label>
                                <input type="text" class="form-control" name="prenom" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Téléphone *</label>
                                <input type="tel" class="form-control" name="telephone" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom d'utilisateur *</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mot de passe *</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date de naissance *</label>
                                <input type="date" class="form-control" name="date_naissance" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Genre</label>
                                <select class="form-select" name="genre">
                                    <option value="M">Masculin</option>
                                    <option value="F">Féminin</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light border-top py-3" style="margin-left: 250px;">
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
        function editPatient(id) {
            alert('Modifier le patient #' + id);
        }

        function deletePatient(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce patient ?')) {
                window.location.href = `../../routes/admin.php?action=delete-patient&id=${id}`;
            }
        }
    </script>
</body>

</html>