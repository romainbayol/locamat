<?php

require_once 'db.php';

$response = [
    'status' => 'error',
    'message' => '',
    'data' => []
];

try {
    $query = "
        SELECT 
            m.id_materiel AS id,
            m.nom,
            m.categorie,
            m.description,
            CASE 
                WHEN EXISTS (
                    SELECT 1 FROM reservations r
                    WHERE r.id_materiel = m.id_materiel 
                    AND r.date_debut <= CURDATE() 
                    AND r.date_fin >= CURDATE()
                ) 
                THEN 'Indisponible'
                ELSE 'Disponible'
            END AS disponibilite
        FROM materiel m
    ";

    $stmt = $pdo->query($query);

    $equipments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['status'] = 'success';
    $response['data'] = $equipments;

} catch (PDOException $e) {
    $response['message'] = 'Erreur lors de la récupération des équipements : ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>
