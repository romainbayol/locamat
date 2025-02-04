<?php
session_start();
require_once 'db.php';

$isLoggedIn = isset($_SESSION['user']);
$isAdmin = $isLoggedIn && isset($_SESSION['user']['role']) && (int)$_SESSION['user']['role'] === 1;

if (!$isAdmin) {
    header("Location: index.php");
    exit();
}

// Clé secrète pour AES-256-GCM
define('AES_KEY', 'e3f3a7b1c91d45a2843c78b2df3e902f');

$errorMessages = [
    'nom' => '',
    'prenom' => '',
    'email' => '',
    'matricule' => '',
    'password' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $matricule = trim($_POST['matricule']);
    $role = isset($_POST['role']) ? (int)$_POST['role'] : 0;
    $password = trim($_POST['password']);

    if (empty($nom)) $errorMessages['nom'] = "Le nom est obligatoire.";
    if (empty($prenom)) $errorMessages['prenom'] = "Le prénom est obligatoire.";
    if (empty($email)) $errorMessages['email'] = "L'email est obligatoire.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errorMessages['email'] = "L'email n'est pas valide.";
    if (empty($matricule)) $errorMessages['matricule'] = "Le matricule est obligatoire.";
    if (empty($password)) $errorMessages['password'] = "Le mot de passe est obligatoire.";
    elseif (strlen($password) < 8) $errorMessages['password'] = "Le mot de passe doit contenir au moins 8 caractères.";

    if (!array_filter($errorMessages)) {
        $iv = openssl_random_pseudo_bytes(12);
        $encryptedPassword = openssl_encrypt($password, 'aes-256-gcm', AES_KEY, 0, $iv, $tag);

        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, matricule, role, password, iv, tag) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $email, $matricule, $role, base64_encode($encryptedPassword), base64_encode($iv), base64_encode($tag)]);

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
    <title>Ajouter un Compte</title>
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

<div class="edit-container">
    <h2 class="text-center mb-4">Ajouter un Compte</h2>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($nom ?? '') ?>" required>
            <?php if ($errorMessages['nom']): ?>
                <div class="text-danger"><?= $errorMessages['nom'] ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">Prénom</label>
            <input type="text" class="form-control" name="prenom" value="<?= htmlspecialchars($prenom ?? '') ?>" required>
            <?php if ($errorMessages['prenom']): ?>
                <div class="text-danger"><?= $errorMessages['prenom'] ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
            <?php if ($errorMessages['email']): ?>
                <div class="text-danger"><?= $errorMessages['email'] ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">Matricule</label>
            <input type="text" class="form-control" name="matricule" value="<?= htmlspecialchars($matricule ?? '') ?>" required>
            <?php if ($errorMessages['matricule']): ?>
                <div class="text-danger"><?= $errorMessages['matricule'] ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" class="form-control" name="password" required>
            <?php if ($errorMessages['password']): ?>
                <div class="text-danger"><?= $errorMessages['password'] ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">Rôle</label>
            <select class="form-select" name="role">
                <option value="0">Utilisateur</option>
                <option value="1">Admin</option>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <a href="comptes.php" class="btn btn-secondary">Retour</a>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </div>
    </form>
</div>

</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
