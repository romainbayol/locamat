<?php
session_start();
require_once 'db.php';

$isLoggedIn = isset($_SESSION['user']);
$isAdmin = $isLoggedIn && isset($_SESSION['user']['role']) && (int)$_SESSION['user']['role'] === 1;

if (!$isAdmin) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->query("SELECT id_utilisateur, nom, prenom, email, matricule, role FROM utilisateurs ORDER BY id_utilisateur ASC");
$comptes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Comptes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<section class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="fw-bold">Gestion des Comptes</h1>
        <a href="ajouter_comptes.php" class="btn btn-primary">+ Ajouter un compte</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Matricule</th>
                    <th>Rôle</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comptes as $compte): ?>
                <tr>
                    <td><?= htmlspecialchars($compte['id_utilisateur']) ?></td>
                    <td><?= htmlspecialchars($compte['nom']) ?></td>
                    <td><?= htmlspecialchars($compte['prenom']) ?></td>
                    <td><?= htmlspecialchars($compte['email']) ?></td>
                    <td><?= htmlspecialchars($compte['matricule']) ?></td>
                    <td><?= ($compte['role'] == 1) ? 'Admin' : 'Utilisateur' ?></td>
                      <td class="text-center">
                        <a href="modifier_comptes.php?id=<?= $compte['id_utilisateur'] ?>" class="btn btn-secondary btn-sm">Modifier</a>
                        <?php if ($compte['id_utilisateur'] != $_SESSION['user']['id_utilisateur']): ?>
                            <a href="supprimer_comptes.php?id=<?= $compte['id_utilisateur'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce compte ?');">Supprimer</a>
                        <?php endif; ?>
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