<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']) || (int)$_SESSION['user']['role'] !== 1) {
    header("Location: index.php");
    exit();
}

$errorMessages = [];
$nom = $version = $ref = $categorie = $description = "";
$imagePath = "img/default_image.png";

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

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
    $errorMessages['image'] = "L'image est obligatoire.";
} else {
    $fileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if ($fileType !== "jpg" && $fileType !== "jpeg") {
        $errorMessages['image'] = "Seules les images JPG/JPEG sont acceptées.";
    }
}


    if (empty($errorMessages)) {
        $stmt = $pdo->prepare("INSERT INTO materiel (nom, version, ref, categorie, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $version, $ref, $categorie, $description]);
        $lastId = $pdo->lastInsertId();
        
        $targetFile = "img/equipments/" . $lastId . ".jpg";
        move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);

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
    <title>Ajouter Matériel</title>
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
    max-height: 250px; 
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

.text-danger {
    color: red;
    font-size: 0.9rem;
    margin-top: 3px;
    display: block;
}

    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<section class="container">
    <div class="edit-container">
        <h2 class="text-center mb-4">Ajouter Matériel</h2>

<form method="post" enctype="multipart/form-data">
    <div class="row g-4">
        <div class="col-md-4 text-center">
            <div class="image-container">
                <img id="current-image" src="img/default_image.png" class="image-preview mb-3">
                <span class="edit-icon">📷 Ajouter</span>
            </div>
            <input type="file" id="image-upload" class="form-control" name="image" accept=".jpg,.jpeg" required>
            <?php if (!empty($errorMessages['image'])): ?>
                <span class="text-danger"><?= $errorMessages['image'] ?></span>
            <?php endif; ?>

        </div>

        <div class="col-md-8">
            <!-- Champ Nom -->
            <div class="mb-3">
                <label class="form-label">Nom</label>
                <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($nom) ?>" required>
                <?php if (!empty($errorMessages['nom'])): ?>
                    <span class="text-danger"><?= $errorMessages['nom'] ?></span>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Version</label>
                <input type="text" class="form-control" name="version" value="<?= htmlspecialchars($version) ?>" required>
                <?php if (!empty($errorMessages['version'])): ?>
                    <span class="text-danger"><?= $errorMessages['version'] ?></span>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Référence</label>
                <input type="text" class="form-control" name="ref" value="<?= htmlspecialchars($ref) ?>" required>
                <?php if (!empty($errorMessages['ref'])): ?>
                    <span class="text-danger"><?= $errorMessages['ref'] ?></span>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Catégorie</label>
                <select class="form-select" name="categorie">
                    <option value="PC" <?= ($categorie == 'PC') ? 'selected' : '' ?>>PC</option>
                    <option value="Téléphone" <?= ($categorie == 'Téléphone') ? 'selected' : '' ?>>Téléphone</option>
                    <option value="Tablette" <?= ($categorie == 'Tablette') ? 'selected' : '' ?>>Tablette</option>
                    <option value="Écran" <?= ($categorie == 'Écran') ? 'selected' : '' ?>>Écran</option>
                    <option value="Disque Dur" <?= ($categorie == 'Disque Dur') ? 'selected' : '' ?>>Disque Dur</option>
                    <option value="Autres" <?= ($categorie == 'Autres') ? 'selected' : '' ?>>Autres</option>
                </select>
                <?php if (!empty($errorMessages['categorie'])): ?>
                    <span class="text-danger"><?= $errorMessages['categorie'] ?></span>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description"><?= htmlspecialchars($description) ?></textarea>
                <?php if (!empty($errorMessages['description'])): ?>
                    <span class="text-danger"><?= $errorMessages['description'] ?></span>
                <?php endif; ?>
            </div>

            <div class="btn-container">
                <a href="materiels.php" class="btn btn-secondary">Retour</a>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </div>
        </div>
    </div>
</form>


    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let imageContainer = document.querySelector(".image-container");
        let imageUpload = document.getElementById("image-upload");
        let currentImage = document.getElementById("current-image");

        imageContainer.addEventListener("click", function() {
            imageUpload.click();
        });

        imageUpload.addEventListener("change", function(event) {
            let reader = new FileReader();
            let errorSpan = document.getElementById("image-error");

            if (imageUpload.files.length === 0) {
                return;
            }

            let file = imageUpload.files[0];
            let fileType = file.type.toLowerCase();

            if (fileType !== "image/jpeg" && fileType !== "image/jpg") {
                event.target.value = ""; // Reset le champ
                if (!errorSpan) {
                    errorSpan = document.createElement("span");
                    errorSpan.id = "image-error";
                    errorSpan.className = "text-danger";
                    errorSpan.innerText = "Seules les images JPG/JPEG sont acceptées.";
                    imageUpload.parentNode.appendChild(errorSpan);
                }
                return;
            }

            if (errorSpan) {
                errorSpan.remove();
            }

            reader.onload = function(e) {
                currentImage.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });

        document.querySelector("form").addEventListener("submit", function(event) {
            let errorSpan = document.getElementById("image-error");

            if (imageUpload.files.length === 0) {
                event.preventDefault();

                if (!errorSpan) {
                    errorSpan = document.createElement("span");
                    errorSpan.id = "image-error";
                    errorSpan.className = "text-danger";
                    errorSpan.innerText = "L'image est obligatoire.";
                    imageUpload.parentNode.appendChild(errorSpan);
                }
            }
        });
    });
</script>

</body>
</html>
