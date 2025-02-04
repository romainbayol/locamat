<?php
session_start();
require_once 'db.php';

$isLoggedIn = isset($_SESSION['user']);
$isAdmin = $isLoggedIn && isset($_SESSION['user']['role']) && (int)$_SESSION['user']['role'] === 1;

$query = "SELECT * FROM materiel";
$stmt = $pdo->query($query);
$equipements = $stmt->fetchAll(PDO::FETCH_ASSOC);

$queryCategories = "SELECT DISTINCT categorie FROM materiel";
$stmtCategories = $pdo->query($queryCategories);
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

function getNextAvailableDate($pdo, $id_materiel) {
    $query = "
        SELECT date_debut, date_fin 
        FROM reservations 
        WHERE id_materiel = :id_materiel 
        ORDER BY date_debut ASC
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id_materiel' => $id_materiel]);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $today = new DateTime();
    $lastEnd = null;
    $isCurrentlyReserved = false;

    foreach ($reservations as $reservation) {
        $start = new DateTime($reservation['date_debut']);
        $end = new DateTime($reservation['date_fin']);

        if ($today >= $start && $today <= $end) {
            $isCurrentlyReserved = true;
            $lastEnd = (clone $end)->modify('+1 day');
        }

        if ($lastEnd !== null && $lastEnd < $start) {
            return "Disponible à partir du " . $lastEnd->format("d/m/Y");
        }

        $lastEnd = (clone $end)->modify('+1 day');
    }

    if ($lastEnd === null) {
        return "Disponible";
    }

    if ($isCurrentlyReserved) {
        return "Disponible à partir du " . $lastEnd->format("d/m/Y");
    }

    return "Disponible à partir du " . $lastEnd->format("d/m/Y");
}

foreach ($equipements as &$equipement) {
    $equipement['disponibilite'] = getNextAvailableDate($pdo, $equipement['id_materiel']);
}

function truncateDescription($text, $limit = 100) {
    return mb_strimwidth($text, 0, $limit, "...");
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LocaMat - Liste des équipements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
</head>
<body>

<?php include 'navbar.php'; ?>

<section class="title-section text-center my-4">
    <h1>Liste des équipements</h1>
    <p>Vous trouverez ci-dessous la liste des équipements disponibles à la location.</p>
</section>

<main>
    <section class="dropdown">
        <button class="btn btn-secondary w-100 mb-2" id="toggleFilters">Afficher/Masquer les Filtres</button>
    </section>

    <aside class="filter-container">
        <section class="filter-box">
            <h3>Rechercher</h3>
            <input type="text" id="searchBar" class="form-control" placeholder="Rechercher un équipement">
        </section>

        <section class="filter-box">
            <h3>Catégories</h3>
            <?php foreach ($categories as $categorie): ?>
                <div class="filter-option">
                    <label for="category<?= htmlspecialchars($categorie['categorie']) ?>"><?= htmlspecialchars($categorie['categorie']) ?></label>
                    <input class="form-check-input category-filter" type="checkbox" value="<?= htmlspecialchars($categorie['categorie']) ?>">
                </div>
            <?php endforeach; ?>
        </section>

        <section class="filter-box">
            <h3>Disponibilité</h3>
            <div class="filter-option">
                <label for="filterAvailable">Disponible</label>
                <input class="form-check-input availability-filter" type="checkbox" id="filterAvailable" value="Disponible">
            </div>
            <div class="filter-option">
                <label for="filterUnavailable">Indisponible</label>
                <input class="form-check-input availability-filter" type="checkbox" id="filterUnavailable" value="Indisponible">
            </div>
        </section>
    </aside>

    <section class="equipment-box">
        <div class="album py-3">
            <div class="container">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3" id="equipmentList">
                    <?php foreach ($equipements as $equipement): 
                        $disponibilite = $equipement['disponibilite'];
                        $disponible = ($disponibilite === "Disponible");
                    ?>
                        <div class="col equipment-item" data-category="<?= htmlspecialchars($equipement['categorie']) ?>" data-available="<?= $disponible ? '1' : '0' ?>">
                            <div class="card">
                                <img src="img/equipments/<?= htmlspecialchars($equipement['id_materiel']) ?>.jpg" 
                                     onerror="this.src='img/default.jpg';" class="card-img-top">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($equipement['nom']) ?></h5>
                                    <p class="material-category"><?= htmlspecialchars($equipement['categorie']) ?></p>
                                    <p class="card-description" title="<?= htmlspecialchars($equipement['description']) ?>">
                                        <?= strlen($equipement['description']) > 80 ? substr(htmlspecialchars($equipement['description']), 0, 80) . '...' : htmlspecialchars($equipement['description']) ?>
                                    </p>

                                    <div class="availability mt-2">
                                        <span class="badge <?= $disponible ? 'bg-success' : 'bg-danger' ?>">
                                            <?= htmlspecialchars($disponibilite) ?>
                                        </span>
                                    </div>
                                    <?php if ($isLoggedIn): ?>
                                        <a href="reservation.php?id=<?= $equipement['id_materiel'] ?>" class="btn btn-primary mt-2">Réserver</a>
                                    <?php else: ?>
                                        <p class="text-muted mt-2">Connectez-vous pour réserver.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    const toggleButton = document.getElementById("toggleFilters");
    const filterContainer = document.querySelector(".filter-container");

    if (toggleButton && filterContainer) {
        toggleButton.addEventListener("click", function () {
            filterContainer.classList.toggle("active");

            toggleButton.textContent = filterContainer.classList.contains("active") 
                ? "Masquer les Filtres" 
                : "Afficher les Filtres";
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const searchBar = document.getElementById("searchBar");
    const categoryFilters = document.querySelectorAll(".category-filter");
    const availabilityFilters = document.querySelectorAll(".availability-filter");

    function filterEquipments() {
        const query = searchBar.value.toLowerCase();
        const selectedCategories = Array.from(categoryFilters)
            .filter(input => input.checked)
            .map(input => input.value);

        const availableChecked = document.getElementById("filterAvailable").checked;
        const unavailableChecked = document.getElementById("filterUnavailable").checked;

        document.querySelectorAll(".equipment-item").forEach(item => {
            const name = item.querySelector(".card-title").textContent.toLowerCase();
            const category = item.dataset.category;
            const available = item.dataset.available === "1";

            let matchesSearch = name.includes(query);
            let matchesCategory = selectedCategories.length === 0 || selectedCategories.includes(category);
            let matchesAvailability =
                (!availableChecked && !unavailableChecked) ||
                (availableChecked && available) ||
                (unavailableChecked && !available);

            item.style.display = (matchesSearch && matchesCategory && matchesAvailability) ? "block" : "none";
        });
    }

    searchBar.addEventListener("input", filterEquipments);
    categoryFilters.forEach(filter => filter.addEventListener("change", filterEquipments));
    availabilityFilters.forEach(filter => filter.addEventListener("change", filterEquipments));

    document.getElementById("categoryAll").addEventListener("change", function () {
        const allChecked = this.checked;
        categoryFilters.forEach(filter => filter.checked = allChecked);
        filterEquipments();
    });
});


</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
