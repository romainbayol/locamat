<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$id_utilisateur = $_SESSION['user']['id_utilisateur'];
$today = date('Y-m-d');

$stmt = $pdo->prepare("SELECT r.*, m.nom, m.categorie 
                        FROM reservations r 
                        JOIN materiel m ON r.id_materiel = m.id_materiel 
                        WHERE r.id_utilisateur = ? 
                        ORDER BY r.date_debut DESC");
$stmt->execute([$id_utilisateur]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$reservations_en_cours = [];
$reservations_a_venir = [];
$reservations_passees = [];

foreach ($reservations as $res) {
    if ($res['date_debut'] <= $today && $res['date_fin'] >= $today) {
        $reservations_en_cours[] = $res;
    } elseif ($res['date_debut'] > $today) {
        $reservations_a_venir[] = $res;
    } else {
        $reservations_passees[] = $res;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Réservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<section class="container mt-5">
    <h1 class="fw-bold">Mes Réservations</h1>

    <?php if (empty($reservations)): ?>
        <p class="text-center text-muted mt-4">Vous n'avez aucune réservation.</p>
    <?php else: ?>
        <ul class="nav nav-tabs mt-3" id="reservationTabs">
            <li class="nav-item">
                <a class="nav-link active" id="en-cours-tab" data-bs-toggle="tab" href="#en-cours">En cours</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="a-venir-tab" data-bs-toggle="tab" href="#a-venir">À venir</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="passees-tab" data-bs-toggle="tab" href="#passees">Passées</a>
            </li>
        </ul>

        <div class="tab-content mt-3">
            <div class="tab-pane fade show active" id="en-cours">
                <?php if (empty($reservations_en_cours)): ?>
                    <p class="text-center text-muted">Aucune réservation en cours.</p>
                <?php else: ?>
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Matériel</th>
                                <th>Catégorie</th>
                                <th>Date de début</th>
                                <th>Date de fin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations_en_cours as $res): ?>
                                <tr>
                                    <td><?= htmlspecialchars($res['nom']) ?></td>
                                    <td><?= htmlspecialchars($res['categorie']) ?></td>
                                    <td><?= htmlspecialchars($res['date_debut']) ?></td>
                                    <td><?= htmlspecialchars($res['date_fin']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="a-venir">
                <?php if (empty($reservations_a_venir)): ?>
                    <p class="text-center text-muted">Aucune réservation à venir.</p>
                <?php else: ?>
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Matériel</th>
                                <th>Catégorie</th>
                                <th>Date de début</th>
                                <th>Date de fin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations_a_venir as $res): ?>
                                <tr>
                                    <td><?= htmlspecialchars($res['nom']) ?></td>
                                    <td><?= htmlspecialchars($res['categorie']) ?></td>
                                    <td><?= htmlspecialchars($res['date_debut']) ?></td>
                                    <td><?= htmlspecialchars($res['date_fin']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="passees">
                <?php if (empty($reservations_passees)): ?>
                    <p class="text-center text-muted">Aucune réservation passée.</p>
                <?php else: ?>
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Matériel</th>
                                <th>Catégorie</th>
                                <th>Date de début</th>
                                <th>Date de fin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations_passees as $res): ?>
                                <tr>
                                    <td><?= htmlspecialchars($res['nom']) ?></td>
                                    <td><?= htmlspecialchars($res['categorie']) ?></td>
                                    <td><?= htmlspecialchars($res['date_debut']) ?></td>
                                    <td><?= htmlspecialchars($res['date_fin']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
