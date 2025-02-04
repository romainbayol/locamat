<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']) || (int)$_SESSION['user']['role'] !== 1) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {

    if ($_GET['id'] == $_SESSION['user']['id_utilisateur']) {
        header("Location: comptes.php?error=self-delete");
        exit();
    }
    
    $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id_utilisateur = ?");
    $stmt->execute([$_GET['id']]);
}

header("Location: comptes.php");
exit();