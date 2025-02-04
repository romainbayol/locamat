<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']) || (int)$_SESSION['user']['role'] !== 1) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE id_reservation = ?");
    $stmt->execute([$_GET['id']]);
}

header("Location: reservations.php");
exit();
?>
