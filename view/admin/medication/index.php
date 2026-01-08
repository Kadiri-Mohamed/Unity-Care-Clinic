<?php
Session::init();
if (!Session::isLoggedIn() || Session::getUserRole() !== 'Admin') {
    header('Location: ../../auth/login.php');
    exit();
}

$adminService = new AdminService();
$medications = $adminService->getAllMedications();
$prescriptions = $adminService->getAllPrescriptions();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Médicaments - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../public/style.css">
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

        .medication-badge {
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
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
            <a href="../../routes/router.php?route=admin" class="nav-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="../../routes/router.php?route=admin&doctors" class="nav-link">
                <i class="fas fa-user-md"></i>
                <span>Médecins</span>
            </a>
            <a href="../../routes/router.php?route=admin&patients" class="nav-link">
                <i class="fas fa-users"></i>
                <span>Patients</span>
            </a>
            <a href="../../routes/router.php?route=admin&appointments" class="nav-link">
                <i class="fas fa-calendar-check"></i>
                <span>Rendez-vous</span>
            </a>
            <a href="../../routes/router.php?route=admin&medications" class="nav-link active">
                <i class="fas fa-pills"></i>
                <span>Médicaments</span>
            </a>
            <a href="../../auth/logout.php" class="nav-link text-danger mt-5">
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
                        <i class="fas fa-pills me-2"></i>Gestion des Médicaments
                    </h5>
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-3 text-muted">
                        <i class="fas fa-user-circle me-2"></i>
                        <?= htmlspecialchars($_SESSION['user']['nom'] . " " . $_SESSION['user']['prenom']) ?>
                    </span>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMedicationModal">
                        <i class="fas fa-plus me-2"></i>Ajouter Médicament
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

        <!-- Medications Table -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Liste des Médicaments</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>ID</th>
                                <th>Nom du Médicament</th>
                                <th>Dosage</th>
                                <th>Patient</th>
                                <th>Médecin Prescripteur</th>
                                <th>Date Prescription</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($medications)): ?>
                                <?php foreach ($medications as $medication): ?>
                                    <tr>
                                        <td>#
                                            <?= $medication['id'] ?>
                                        </td>
                                        <td>
                                            <strong><i class="fas fa-capsules text-primary me-2"></i>
                                                <?= htmlspecialchars($medication['nom']) ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <span class="medication-badge bg-info text-white">
                                                <?= htmlspecialchars($medication['dosage']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($medication['patient_name'] ?? 'N/A') ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($medication['doctor_name'] ?? 'N/A') ?>
                                        </td>
                                        <td>
                                            <?php if ($medication['date_prescription']): ?>
                                                <i class="fas fa-calendar-alt text-muted me-1"></i>
                                                <?= date('d/m/Y', strtotime($medication['date_prescription'])) ?>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary"
                                                onclick="editMedication(<?= $medication['id'] ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger"
                                                onclick="deleteMedication(<?= $medication['id'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info"
                                                onclick="viewMedication(<?= $medication['id'] ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-pills fa-2x text-muted mb-3"></i>
                                        <p class="text-muted">Aucun médicament trouvé</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Medication Modal -->
    <div class="modal fade" id="addMedicationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="../../../routes/admin.php?action=create-medication" method="POST">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-pills me-2"></i>Ajouter un Médicament
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Prescription *</label>
                                <select class="form-select" name="prescription_id" required>
                                    <option value="">Sélectionner une prescription</option>
                                    <?php foreach ($prescriptions as $prescription): ?>
                                        <option value="<?= $prescription['id'] ?>">
                                            #
                                            <?= $prescription['id'] ?> -
                                            <?= htmlspecialchars($prescription['patient_name']) ?>
                                            (Dr.
                                            <?= htmlspecialchars($prescription['doctor_name']) ?>) -
                                            <?= date('d/m/Y', strtotime($prescription['date_prescription'])) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Sélectionnez la prescription à laquelle ce médicament est
                                    associé</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom du Médicament *</label>
                                <input type="text" class="form-control" name="nom" placeholder="Ex: Paracétamol"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Dosage *</label>
                                <input type="text" class="form-control" name="dosage" placeholder="Ex: 500mg, 2x/jour"
                                    required>
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

    <!-- Edit Medication Modal -->
    <div class="modal fade" id="editMedicationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="../../../routes/admin.php?action=update-medication" method="POST" id="editMedicationForm">
                    <input type="hidden" name="id" id="edit_medication_id">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-edit me-2"></i>Modifier le Médicament
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom du Médicament *</label>
                                <input type="text" class="form-control" name="nom" id="edit_nom" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Dosage *</label>
                                <input type="text" class="form-control" name="dosage" id="edit_dosage" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Mettre à jour
                        </button>
                    </div>
                </form>
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
                        <i class="fas fa-copyright me-1"></i>
                        <?= date('Y') ?> Tous droits réservés
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const medicationsData = <?= json_encode($medications) ?>;

        function editMedication(id) {
            const medication = medicationsData.find(m => m.id == id);
            if (medication) {
                document.getElementById('edit_medication_id').value = medication.id;
                document.getElementById('edit_nom').value = medication.nom;
                document.getElementById('edit_dosage').value = medication.dosage;

                const editModal = new bootstrap.Modal(document.getElementById('editMedicationModal'));
                editModal.show();
            }
        }

        function deleteMedication(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce médicament ?')) {
                window.location.href = `../../../routes/admin.php?action=delete-medication&id=${id}`;
            }
        }

        function viewMedication(id) {
            const medication = medicationsData.find(m => m.id == id);
            if (medication) {
                alert(`Médicament: ${medication.nom}\nDosage: ${medication.dosage}\nPatient: ${medication.patient_name || 'N/A'}\nMédecin: ${medication.doctor_name || 'N/A'}`);
            }
        }
    </script>
</body>

</html>