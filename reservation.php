<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']['id_utilisateur'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    die("Aucun équipement sélectionné.");
}

$id = intval($_GET['id']);
$id_utilisateur = $_SESSION['user']['id_utilisateur'];

try {
    $stmt = $pdo->prepare("SELECT id_materiel, nom, categorie, description FROM materiel WHERE id_materiel = :id");
    $stmt->execute(['id' => $id]);
    $equipment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$equipment) {
        die("Équipement introuvable.");
    }

    $stmt = $pdo->prepare("SELECT date_debut, date_fin FROM reservations WHERE id_materiel = :id_materiel");
    $stmt->execute(['id_materiel' => $id]);
    $reserved_dates = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    if (empty($date_debut) || empty($date_fin)) {
        $error = "Veuillez sélectionner une date de début et une date de fin.";
    } elseif ($date_debut > $date_fin) {
        $error = "La date de début doit être avant la date de fin.";
    } else {
        try {

            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM reservations 
                WHERE id_utilisateur = :id_utilisateur 
                AND id_materiel = :id_materiel
                AND (
                    (date_debut <= :date_fin AND date_fin >= :date_debut) -- Vérifie un chevauchement
                )
            ");
            $stmt->execute([
                'id_utilisateur' => $id_utilisateur,
                'id_materiel' => $id,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin
            ]);

            if ($stmt->fetchColumn() > 0) {
                $error = "Vous avez déjà une réservation sur cette période.";
            } else {

                $stmt = $pdo->prepare("
                    INSERT INTO reservations (id_materiel, id_utilisateur, date_debut, date_fin) 
                    VALUES (:id_materiel, :id_utilisateur, :date_debut, :date_fin)
                ");
                $stmt->execute([
                    'id_materiel' => $id,
                    'id_utilisateur' => $id_utilisateur,
                    'date_debut' => $date_debut,
                    'date_fin' => $date_fin
                ]);

                header("Location: index.php");
                exit();
            }
        } catch (PDOException $e) {
            $error = "Erreur : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>Réserver - <?= htmlspecialchars($equipment['nom']) ?></title>
    <style>
        .card-img-top {
            max-height: 250px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <img src="img/equipments/<?= $equipment['id_materiel'] ?>.jpg" class="card-img-top" alt="<?= htmlspecialchars($equipment['nom']) ?>" onerror="this.src='img/equipments/default.jpg';">
                    <div class="card-body text-center">
                        <h3><?= htmlspecialchars($equipment['nom']) ?></h3>
                        <p><strong>Catégorie :</strong> <?= htmlspecialchars($equipment['categorie']) ?></p>
                        <p><?= htmlspecialchars($equipment['description']) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php else: ?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="date_debut" class="form-label">Date de début</label>
                            <input type="text" class="form-control" id="date_debut" name="date_debut" required>
                        </div>
                        <div class="mb-3">
                            <label for="date_fin" class="form-label">Date de fin</label>
                            <input type="text" class="form-control" id="date_fin" name="date_fin" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Réserver</button>
                        <a href="index.php" class="btn btn-secondary w-100 mt-2">Retour</a>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let reservedDates = <?= json_encode($reserved_dates) ?>;
            let disabledDates = [];

            reservedDates.forEach(reservation => {
                let start = new Date(reservation.date_debut);
                let end = new Date(reservation.date_fin);
                while (start <= end) {
                    disabledDates.push(start.toISOString().split('T')[0]); 
                    start.setDate(start.getDate() + 1);
                }
            });

            flatpickr("#date_debut, #date_fin", {
                dateFormat: "Y-m-d",
                disable: disabledDates,
                minDate: "today"
            });
        });
    </script>
</body>
</html>
