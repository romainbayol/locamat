<?php
session_start();
require_once 'db.php';

$isLoggedIn = isset($_SESSION['user']);
$isAdmin = $isLoggedIn && isset($_SESSION['user']['role']) && (int)$_SESSION['user']['role'] === 1;

if (!$isAdmin) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM materiel ORDER BY id_materiel ASC");
$materiels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des matériels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<section class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="fw-bold">Gestion du Matériel</h1>
        <a href="ajouter_materiel.php" class="btn btn-primary">+ Ajouter un matériel</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Version</th>
                    <th>Référence</th>
                    <th>Catégorie</th>
                    <th>Description</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materiels as $materiel): ?>
                <tr>
                    <td><?= htmlspecialchars($materiel['id_materiel']) ?></td>
                    <td><?= htmlspecialchars($materiel['nom']) ?></td>
                    <td><?= htmlspecialchars($materiel['version']) ?></td>
                    <td><?= htmlspecialchars($materiel['ref']) ?></td>
                    <td><?= htmlspecialchars($materiel['categorie']) ?></td>
                    <td><?= htmlspecialchars($materiel['description']) ?></td>
                    <td class="text-center">
                        <a href="modifier_materiel.php?id=<?= $materiel['id_materiel'] ?>" class="btn btn-secondary btn-sm">Modifier</a>
                        <a href="supprimer_materiel.php?id=<?= $materiel['id_materiel'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce matériel ?');">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
