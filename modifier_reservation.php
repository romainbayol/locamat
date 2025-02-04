<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']) || (int)$_SESSION['user']['role'] !== 1) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: reservations.php");
    exit();
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM reservations WHERE id_reservation = ?");
$stmt->execute([$id]);
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    header("Location: reservations.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    if ($date_debut > $date_fin) {
        $error = "La date de début doit être avant la date de fin.";
    } else {
        $stmt = $pdo->prepare("UPDATE reservations SET date_debut = ?, date_fin = ? WHERE id_reservation = ?");
        $stmt->execute([$date_debut, $date_fin, $id]);

        header("Location: reservations.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Réservation</title>
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
        <h2 class="text-center mb-4">Modifier Réservation</h2>

        <?php if (!empty($error)): ?>
            <div class="text-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Date de début</label>
                <input type="date" class="form-control" name="date_debut" value="<?= htmlspecialchars($reservation['date_debut']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Date de fin</label>
                <input type="date" class="form-control" name="date_fin" value="<?= htmlspecialchars($reservation['date_fin']) ?>" required>
            </div>

            <div class="d-flex justify-content-between">
                <a href="reservations.php" class="btn btn-secondary">Retour</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
