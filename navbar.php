<?php
session_start();

$isLoggedIn = isset($_SESSION['user']);
$isAdmin = $isLoggedIn && isset($_SESSION['user']['role']) && (int)$_SESSION['user']['role'] === 1;

$currentPage = basename($_SERVER['PHP_SELF']);

$comptesPages = ['comptes.php', 'ajouter_comptes.php', 'modifier_comptes.php', 'supprimer_comptes.php'];
$reservationsPages = ['reservations.php', 'modifier_reservation.php', 'supprimer_reservation.php'];
$materielsPages = ['materiels.php', 'ajouter_materiel.php', 'modifier_materiel.php', 'supprimer_materiel.php'];
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">

        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="img/logo.png" alt="Logo" height="40">
            <span class="ms-2">LocaMat</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav text-center">
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage == 'index.php') ? 'active' : ''; ?>" href="index.php">
                        Accueil
                    </a>
                </li>

                <?php if ($isLoggedIn): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage == 'mes_reservations.php') ? 'active' : ''; ?>" href="mes_reservations.php">
                            Mes réservations
                        </a>
                    </li>

                    <?php if ($isAdmin): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= (in_array($currentPage, $comptesPages)) ? 'active' : ''; ?>" href="comptes.php">
                                Comptes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (in_array($currentPage, $reservationsPages)) ? 'active' : ''; ?>" href="reservations.php">
                                Réservations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (in_array($currentPage, $materielsPages)) ? 'active' : ''; ?>" href="materiels.php">
                                Matériels
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link btn-logout" href="logout.php">
                            Se déconnecter
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link btn-login" href="login.php">
                            Se connecter
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
