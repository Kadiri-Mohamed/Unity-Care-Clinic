<?php
Session::init();
if (!Session::isLoggedIn() || Session::getUserRole() !== 'Admin') {
    header('Location: ../../auth/login.php');
    exit();
}

// Simuler des données de médicaments (à remplacer par ton Repository)
$medications = [
    ['id' => 1, 'nom' => 'Paracétamol', 'dosage' => '500mg', 'forme' => 'Comprimé', 'stock' => 150, 'categorie' => 'Antalgique'],
    ['id' => 2, 'nom' => 'Amoxicilline', 'dosage' => '1g', 'forme' => 'Gélule', 'stock' => 80, 'categorie' => 'Antibiotique'],
    ['id' => 3, 'nom' => 'Ibuprofène', 'dosage' => '400mg', 'forme' => 'Comprimé', 'stock' => 120, 'categorie' => 'Anti-inflammatoire'],
    ['id' => 4, 'nom' => 'Metformine', 'dosage' => '850mg', 'forme' => 'Comprimé', 'stock' => 95, 'categorie' => 'Antidiabétique'],
    ['id' => 5, 'nom' => 'Atorvastatine', 'dosage' => '20mg', 'forme' => 'Comprimé', 'stock' => 60, 'categorie' => 'Hypolipémiant'],
    ['id' => 6, 'nom' => 'Salbutamol', 'dosage' => '100mcg', 'forme' => 'Aérosol', 'stock' => 45, 'categorie' => 'Bronchodilatateur'],
    ['id' => 7, 'nom' => 'Oméprazole', 'dosage' => '20mg', 'forme' => 'Gélule', 'stock' => 110, 'categorie' => 'Anti-ulcéreux'],
    ['id' => 8, 'nom' => 'Loratadine', 'dosage' => '10mg', 'forme' => 'Comprimé', 'stock' => 75, 'categorie' => 'Antihistaminique'],
];

$lowStock = array_filter($medications, function($med) {
    return $med['stock'] < 50;
});

$categories = array_unique(array_column($medications, 'categorie'));
$totalStock = array_sum(array_column($medications, 'stock'));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Médicaments - Admin</title>
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
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .navbar {
            background-color: white !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
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
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
        
        .medication-card {
            border-radius: 10px;
            border: 1px solid #dee2e6;
            transition: all 0.3s;
            margin-bottom: 20px;
        }
        
        .medication-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .stock-indicator {
            height: 8px;
            border-radius: 4px;
            margin-top: 5px;
        }
        
        .stock-low { background-color: #dc3545; }
        .stock-medium { background-color: #ffc107; }
        .stock-good { background-color: #28a745; }
        
        .chart-container {
            height: 250px;
            margin-bottom: 20px;
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
            <a href="../routes/router.php?route=admin&appointments" class="nav-link">
                <i class="fas fa-calendar-check"></i>
                <span>Rendez-vous</span>
            </a>
            <a href="../routes/router.php?route=admin&medications" class="nav-link active">
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
                        <i class="fas fa-pills me-2"></i>Pharmacie & Médicaments
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

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card card-patients">
                    <i class="fas fa-pills stat-icon"></i>
                    <div class="stat-number"><?= count($medications) ?></div>
                    <div class="stat-label">Médicaments</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);">
                    <i class="fas fa-boxes stat-icon"></i>
                    <div class="stat-number"><?= $totalStock ?></div>
                    <div class="stat-label">Stock total</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: #134686;">
                    <i class="fas fa-exclamation-triangle stat-icon"></i>
                    <div class="stat-number"><?= count($lowStock) ?></div>
                    <div class="stat-label">Stock bas</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);">
                    <i class="fas fa-tags stat-icon"></i>
                    <div class="stat-number"><?= count($categories) ?></div>
                    <div class="stat-label">Catégories</div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Répartition par Catégorie</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Top 5 Médicaments</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="topMedicationsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Alerts -->
        <?php if (!empty($lowStock)): ?>
        <div class="alert alert-warning alert-dismissible fade show mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Alertes de stock bas !</strong> <?= count($lowStock) ?> médicament(s) nécessite(nt) un réapprovisionnement.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <div class="mt-2">
                <?php foreach($lowStock as $med): ?>
                <span class="badge bg-danger me-2"><?= $med['nom'] ?> (<?= $med['stock'] ?> unités)</span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Medications Grid -->
        <div class="row mb-4">
            <?php foreach($medications as $medication): ?>
            <?php
            $stockClass = 'stock-good';
            if ($medication['stock'] < 30) {
                $stockClass = 'stock-low';
            } elseif ($medication['stock'] < 50) {
                $stockClass = 'stock-medium';
            }
            ?>
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card medication-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="card-title mb-1"><?= htmlspecialchars($medication['nom']) ?></h6>
                                <span class="badge bg-info"><?= htmlspecialchars($medication['categorie']) ?></span>
                            </div>
                            <span class="badge bg-primary">#<?= $medication['id'] ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <p class="mb-1">
                                <i class="fas fa-prescription-bottle-alt text-primary me-2"></i>
                                <strong>Dosage:</strong> <?= htmlspecialchars($medication['dosage']) ?>
                            </p>
                            <p class="mb-1">
                                <i class="fas fa-capsules text-success me-2"></i>
                                <strong>Forme:</strong> <?= htmlspecialchars($medication['forme']) ?>
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-box text-warning me-2"></i>
                                <strong>Stock:</strong> <?= $medication['stock'] ?> unités
                            </p>
                            <div class="stock-indicator <?= $stockClass ?>"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <div>
                                <button class="btn btn-sm btn-primary" onclick="updateStock(<?= $medication['id'] ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="viewDetails(<?= $medication['id'] ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div>
                                <?php if ($medication['stock'] < 50): ?>
                                <button class="btn btn-sm btn-success" onclick="orderStock(<?= $medication['id'] ?>)">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-danger" onclick="deleteMedication(<?= $medication['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Table View -->
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Liste Complète</h5>
                <div class="input-group w-auto">
                    <input type="text" class="form-control form-control-sm" placeholder="Rechercher..." id="searchMedication">
                    <button class="btn btn-sm btn-light" onclick="searchMedications()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="medicationsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Dosage</th>
                                <th>Forme</th>
                                <th>Catégorie</th>
                                <th>Stock</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($medications as $medication): ?>
                            <?php
                            $statusBadge = 'success';
                            $statusText = 'Bon';
                            if ($medication['stock'] < 30) {
                                $statusBadge = 'danger';
                                $statusText = 'Critique';
                            } elseif ($medication['stock'] < 50) {
                                $statusBadge = 'warning';
                                $statusText = 'Bas';
                            }
                            ?>
                            <tr>
                                <td>#<?= $medication['id'] ?></td>
                                <td><strong><?= htmlspecialchars($medication['nom']) ?></strong></td>
                                <td><?= htmlspecialchars($medication['dosage']) ?></td>
                                <td><?= htmlspecialchars($medication['forme']) ?></td>
                                <td><?= htmlspecialchars($medication['categorie']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2"><?= $medication['stock'] ?></span>
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-<?= $statusBadge ?>" 
                                                 style="width: <?= min(100, ($medication['stock'] / 150) * 100) ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $statusBadge ?>"><?= $statusText ?></span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="viewDetails(<?= $medication['id'] ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" onclick="updateStock(<?= $medication['id'] ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="deleteMedication(<?= $medication['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
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
                <form id="addMedicationForm">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-pills me-2"></i>Ajouter un Médicament
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom commercial *</label>
                                <input type="text" class="form-control" name="nom" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Dénomination commune (DCI)</label>
                                <input type="text" class="form-control" name="dci">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Dosage *</label>
                                <input type="text" class="form-control" name="dosage" required placeholder="ex: 500mg">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Forme galénique *</label>
                                <select class="form-select" name="forme" required>
                                    <option value="">Sélectionner</option>
                                    <option value="Comprimé">Comprimé</option>
                                    <option value="Gélule">Gélule</option>
                                    <option value="Sirop">Sirop</option>
                                    <option value="Aérosol">Aérosol</option>
                                    <option value="Crème">Crème</option>
                                    <option value="Pommade">Pommade</option>
                                    <option value="Injectable">Injectable</option>
                                    <option value="Suppositoire">Suppositoire</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Catégorie *</label>
                                <select class="form-select" name="categorie" required>
                                    <option value="">Sélectionner</option>
                                    <option value="Antalgique">Antalgique</option>
                                    <option value="Antibiotique">Antibiotique</option>
                                    <option value="Anti-inflammatoire">Anti-inflammatoire</option>
                                    <option value="Antidiabétique">Antidiabétique</option>
                                    <option value="Cardiovasculaire">Cardiovasculaire</option>
                                    <option value="Psychotrope">Psychotrope</option>
                                    <option value="Digestif">Digestif</option>
                                    <option value="Dermatologique">Dermatologique</option>
                                    <option value="Vitamines">Vitamines</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Stock initial *</label>
                                <input type="number" class="form-control" name="stock" required min="0" value="50">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Seuil d'alerte</label>
                                <input type="number" class="form-control" name="seuil_alerte" min="0" value="30">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Prix unitaire (€)</label>
                                <input type="number" step="0.01" class="form-control" name="prix" min="0" value="0.00">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Laboratoire</label>
                                <input type="text" class="form-control" name="laboratoire">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Numéro de lot</label>
                                <input type="text" class="form-control" name="numero_lot">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date de péremption</label>
                                <input type="date" class="form-control" name="date_peremption">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Conditionnement</label>
                                <input type="text" class="form-control" name="conditionnement" placeholder="ex: Boîte de 20">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Indications</label>
                                <textarea class="form-control" name="indications" rows="2"></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Contre-indications</label>
                                <textarea class="form-control" name="contre_indications" rows="2"></textarea>
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
    <footer class="bg-light border-top py-3 mt-4" style="margin-left: 250px;">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">
                        <i class="fas fa-hospital text-primary me-2"></i>
                        <strong>Unity Care Clinic</strong> - Gestion pharmacie
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-muted">
                        <i class="fas fa-pills me-1"></i> <?= count($medications) ?> médicaments enregistrés
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Données pour les graphiques
        const categoryCounts = {};
        <?php foreach($medications as $med): ?>
        const cat = "<?= $med['categorie'] ?>";
        categoryCounts[cat] = (categoryCounts[cat] || 0) + 1;
        <?php endforeach; ?>

        // Trier les médicaments par stock (croissant)
        const sortedMeds = <?= json_encode($medications) ?>.sort((a, b) => b.stock - a.stock);
        const topMeds = sortedMeds.slice(0, 5);

        // Initialiser les graphiques
        document.addEventListener('DOMContentLoaded', function() {
            // Graphique des catégories
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            new Chart(categoryCtx, {
                type: 'pie',
                data: {
                    labels: Object.keys(categoryCounts),
                    datasets: [{
                        data: Object.values(categoryCounts),
                        backgroundColor: [
                            'rgba(19, 70, 134, 0.8)',
                            'rgba(237, 63, 39, 0.8)',
                            'rgba(254, 178, 26, 0.8)',
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(108, 117, 125, 0.8)',
                            'rgba(23, 162, 184, 0.8)',
                            'rgba(111, 66, 193, 0.8)',
                            'rgba(253, 126, 20, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });

            // Graphique des top médicaments
            const topMedsCtx = document.getElementById('topMedicationsChart').getContext('2d');
            new Chart(topMedsCtx, {
                type: 'bar',
                data: {
                    labels: topMeds.map(m => m.nom),
                    datasets: [{
                        label: 'Stock disponible',
                        data: topMeds.map(m => m.stock),
                        backgroundColor: 'rgba(40, 167, 69, 0.7)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });

        // Fonctions d'actions
        function viewDetails(id) {
            alert('Détails du médicament #' + id);
            // À implémenter: ouverture d'un modal avec les détails complets
        }

        function updateStock(id) {
            const newStock = prompt('Nouvelle quantité en stock:');
            if (newStock !== null && !isNaN(newStock)) {
                alert(`Stock du médicament #${id} mis à jour: ${newStock} unités`);
                // À implémenter: appel AJAX pour mettre à jour la base de données
            }
        }

        function orderStock(id) {
            const quantity = prompt('Quantité à commander:');
            if (quantity !== null && !isNaN(quantity)) {
                alert(`Commande passée pour le médicament #${id}: ${quantity} unités`);
                // À implémenter: logique de commande
            }
        }

        function deleteMedication(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce médicament ?')) {
                alert(`Médicament #${id} supprimé`);
                // À implémenter: appel AJAX pour suppression
            }
        }

        // Recherche
        function searchMedications() {
            const searchTerm = document.getElementById('searchMedication').value.toLowerCase();
            const rows = document.querySelectorAll('#medicationsTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        }

        // Gestion du formulaire
        document.getElementById('addMedicationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Médicament ajouté avec succès!');
            // À implémenter: soumission AJAX
            const modal = bootstrap.Modal.getInstance(document.getElementById('addMedicationModal'));
            modal.hide();
        });

        // Recherche en temps réel
        document.getElementById('searchMedication').addEventListener('input', searchMedications);
    </script>
</body>
</html>