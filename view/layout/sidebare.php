<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<div class="col-md-3 col-lg-2 sidebar bg-primary text-white min-vh-100 p-0">
    <div class="sidebar-sticky pt-3">
        <div class="text-center py-4 border-bottom">
            <h5><i class="fas fa-user-md me-2"></i>Administration</h5>
            <small class="text-light">Gestion complète</small>
        </div>
        
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>" 
                   href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'doctor') ? 'active' : '' ?>" 
                   href="?doctors">
                    <i class="fas fa-user-md me-2"></i>Médecins
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'patient') ? 'active' : '' ?>" 
                   href="?patients">
                    <i class="fas fa-users me-2"></i>Patients
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'appointment') ? 'active' : '' ?>" 
                   href="?appointments">
                    <i class="fas fa-calendar-check me-2"></i>Rendez-vous
                </a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link text-danger" href="../../auth/logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                </a>
            </li>
        </ul>
    </div>
</div>