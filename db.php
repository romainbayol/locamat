<?php

$dbHost = 'localhost'; // Adresse du serveur MySQL
$dbUser = 'root'; // Nom d'utilisateur
$dbPassword = 'Rom@in190900'; // Mot de passe
$dbName = 'Locamat'; // Nom de la base de données

try {

    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPassword);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
