<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']) || (int)$_SESSION['user']['role'] !== 1) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->query("SELECT r.*, m.nom AS materiel_nom, u.nom AS user_nom, u.prenom AS user_prenom
                     FROM reservations r
                     JOIN materiel m ON r.id_materiel = m.id_materiel
                     JOIN utilisateurs u ON r.id_utilisateur = u.id_utilisateur
                     ORDER BY r.id_reservation DESC");
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Réservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<section class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="fw-bold">Gestion des Réservations</h1>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Matériel</th>
                    <th>Réservé par</th>
                    <th>Date de début</th>
                    <th>Date de fin</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reservations)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">Aucune réservation en cours.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reservations as $res): ?>
                        <tr>
                            <td><?= htmlspecialchars($res['id_reservation']) ?></td>
                            <td><?= htmlspecialchars($res['materiel_nom']) ?></td>
                            <td><?= htmlspecialchars($res['user_prenom'] . ' ' . $res['user_nom']) ?></td>
                            <td><?= htmlspecialchars($res['date_debut']) ?></td>
                            <td><?= htmlspecialchars($res['date_fin']) ?></td>
                            <td class="text-center">
                                <a href="modifier_reservation.php?id=<?= $res['id_reservation'] ?>" class="btn btn-secondary btn-sm">Modifier</a>
                                <a href="supprimer_reservation.php?id=<?= $res['id_reservation'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réservation ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
