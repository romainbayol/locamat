<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']) || (int)$_SESSION['user']['role'] !== 1) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: comptes.php");
    exit();
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = ?");
$stmt->execute([$id]);
$compte = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$compte) {
    header("Location: comptes.php");
    exit();
}

$errorMessages = [
    'nom' => '',
    'prenom' => '',
    'email' => '',
    'matricule' => '',
    'role' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $matricule = trim($_POST['matricule']);
    $role = isset($_POST['role']) ? (int)$_POST['role'] : 0;

    if (empty($nom)) $errorMessages['nom'] = "Le nom est obligatoire.";
    if (empty($prenom)) $errorMessages['prenom'] = "Le prénom est obligatoire.";
    if (empty($email)) $errorMessages['email'] = "L'email est obligatoire.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errorMessages['email'] = "L'email n'est pas valide.";
    if (empty($matricule)) $errorMessages['matricule'] = "Le matricule est obligatoire.";

    if (!array_filter($errorMessages)) {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, matricule = ?, role = ? WHERE id_utilisateur = ?");
        $stmt->execute([$nom, $prenom, $email, $matricule, $role, $id]);

        header("Location: comptes.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Compte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .edit-container {
            max-width: 600px;
            width: 100%;
            margin: 30px auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 5px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<section class="container mt-5">
    <div class="edit-container">
        <h2 class="text-center mb-4">Modifier Compte</h2>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Nom</label>
                <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($compte['nom']) ?>" required>
                <?php if ($errorMessages['nom']): ?>
                    <div class="text-danger"><?= $errorMessages['nom'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Prénom</label>
                <input type="text" class="form-control" name="prenom" value="<?= htmlspecialchars($compte['prenom']) ?>" required>
                <?php if ($errorMessages['prenom']): ?>
                    <div class="text-danger"><?= $errorMessages['prenom'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($compte['email']) ?>" required>
                <?php if ($errorMessages['email']): ?>
                    <div class="text-danger"><?= $errorMessages['email'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Matricule</label>
                <input type="text" class="form-control" name="matricule" value="<?= htmlspecialchars($compte['matricule']) ?>" required>
                <?php if ($errorMessages['matricule']): ?>
                    <div class="text-danger"><?= $errorMessages['matricule'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Rôle</label>
                <select class="form-select" name="role">
                    <option value="0" <?= ($compte['role'] == 0) ? 'selected' : '' ?>>Utilisateur</option>
                    <option value="1" <?= ($compte['role'] == 1) ? 'selected' : '' ?>>Admin</option>
                </select>
                <?php if ($errorMessages['role']): ?>
                    <div class="text-danger"><?= $errorMessages['role'] ?></div>
                <?php endif; ?>
            </div>

            <div class="d-flex justify-content-between">
                <a href="comptes.php" class="btn btn-secondary">Retour</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
