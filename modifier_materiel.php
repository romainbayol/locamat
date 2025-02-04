<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']) || (int)$_SESSION['user']['role'] !== 1) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: materiels.php");
    exit();
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM materiel WHERE id_materiel = ?");
$stmt->execute([$id]);
$materiel = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$materiel) {
    header("Location: materiels.php");
    exit();
}

$imagePath = "img/equipments/" . $materiel['id_materiel'] . ".jpg";
$imageDefault = "img/default.jpg";
$errorMessages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $version = trim($_POST['version']);
    $ref = trim($_POST['ref']);
    $categorie = $_POST['categorie'] ?? '';
    $description = trim($_POST['description']);

    if (empty($nom)) {
        $errorMessages['nom'] = "Le nom est obligatoire.";
    } elseif (strlen($nom) > 30) {
        $errorMessages['nom'] = "Le nom ne doit pas dépasser 30 caractères.";
    }

    if (empty($version)) {
        $errorMessages['version'] = "La version est obligatoire.";
    } elseif (strlen($version) > 15) {
        $errorMessages['version'] = "La version ne doit pas dépasser 15 caractères.";
    }

    if (empty($ref)) {
        $errorMessages['ref'] = "La référence est obligatoire.";
    } elseif (!preg_match('/^[A-Za-z0-9]{1,6}$/', $ref)) {
        $errorMessages['ref'] = "La référence doit être alphanumérique (max 6 caractères).";
    }

    if (empty($categorie) || !in_array($categorie, ['PC', 'Téléphone', 'Tablette', 'Écran', 'Disque Dur', 'Autres'])) {
        $errorMessages['categorie'] = "Veuillez sélectionner une catégorie valide.";
    }

    if (empty($description)) {
        $errorMessages['description'] = "La description est obligatoire.";
    } elseif (strlen($description) > 500) {
        $errorMessages['description'] = "La description ne doit pas dépasser 500 caractères.";
    }

    if (empty($errorMessages)) {
        $stmt = $pdo->prepare("UPDATE materiel SET 
            nom = ?, 
            version = ?, 
            ref = ?, 
            categorie = ?, 
            description = ? 
            WHERE id_materiel = ?");

        $stmt->execute([$nom, $version, $ref, $categorie, $description, $id]);

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $fileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if ($fileType !== "jpg" && $fileType !== "jpeg") {
                $errorMessages['image'] = "Seules les images JPG/JPEG sont acceptées.";
            } else {
                $targetFile = "img/equipments/" . $materiel['id_materiel'] . ".jpg";
                move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
            }
        }

        header("Location: materiels.php");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Matériel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>

        .edit-container {
            max-width: 1100px;
            width: 90%;
            margin: 30px auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 5px 12px rgba(0, 0, 0, 0.1);
        }

        .image-container {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        .image-preview {
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
            transition: opacity 0.3s ease-in-out;
        }

        .edit-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 10px 15px;
            border-radius: 50px;
            font-size: 1.2rem;
            display: none;
            transition: all 0.3s ease;
        }

        .image-container:hover .edit-icon {
            display: block;
        }

        #image-upload {
            display: none;
        }

        .btn-container {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }

        @media (max-width: 768px) {
            .edit-container {
                margin-top: 15px;
                padding: 15px;
            }

            .row {
                flex-direction: column;
                align-items: center;
            }

            .btn-container {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<section class="container">
    <div class="edit-container">
        <h2 class="text-center mb-4">Modifier Matériel</h2>

        <form method="post" enctype="multipart/form-data">
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <div class="image-container">
                        <img id="current-image" src="<?= file_exists($imagePath) ? $imagePath : $imageDefault ?>" 
                             class="image-preview mb-3">
                        <span class="edit-icon">📷 Changer</span>
                    </div>
                    <input type="file" id="image-upload" class="form-control" name="image" accept=".jpg,.jpeg">
                </div>
                <div class="col-md-8">
                    <!-- Champ Nom -->
                    <div class="mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($materiel['nom']) ?>" required>
                        <?php if (!empty($errorMessages['nom'])): ?>
                            <div class="text-danger"><?= $errorMessages['nom'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Version</label>
                        <input type="text" class="form-control" name="version" value="<?= htmlspecialchars($materiel['version']) ?>" required>
                        <?php if (!empty($errorMessages['version'])): ?>
                            <div class="text-danger"><?= $errorMessages['version'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Référence</label>
                        <input type="text" class="form-control" name="ref" value="<?= htmlspecialchars($materiel['ref']) ?>" required>
                        <?php if (!empty($errorMessages['ref'])): ?>
                            <div class="text-danger"><?= $errorMessages['ref'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catégorie</label>
                        <select class="form-select" name="categorie">
                            <option value="PC" <?= ($materiel['categorie'] == 'PC') ? 'selected' : '' ?>>PC</option>
                            <option value="Téléphone" <?= ($materiel['categorie'] == 'Téléphone') ? 'selected' : '' ?>>Téléphone</option>
                            <option value="Tablette" <?= ($materiel['categorie'] == 'Tablette') ? 'selected' : '' ?>>Tablette</option>
                            <option value="Écran" <?= ($materiel['categorie'] == 'Écran') ? 'selected' : '' ?>>Écran</option>
                            <option value="Disque Dur" <?= ($materiel['categorie'] == 'Disque Dur') ? 'selected' : '' ?>>Disque Dur</option>
                            <option value="Autres" <?= ($materiel['categorie'] == 'Autres') ? 'selected' : '' ?>>Autres</option>
                        </select>
                        <?php if (!empty($errorMessages['categorie'])): ?>
                            <div class="text-danger"><?= $errorMessages['categorie'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description"><?= htmlspecialchars($materiel['description']) ?></textarea>
                        <?php if (!empty($errorMessages['description'])): ?>
                            <div class="text-danger"><?= $errorMessages['description'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="btn-container">
                        <a href="materiels.php" class="btn btn-secondary">Retour</a>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
    document.querySelector(".image-container").addEventListener("click", function() {
        document.getElementById("image-upload").click();
    });

    document.getElementById("image-upload").addEventListener("change", function(event) {
        let reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById("current-image").src = e.target.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    });
</script>

</body>
</html>
