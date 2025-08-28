<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['annonce_id'])) {
    header('Location: buy.php');
    exit;
}

$user_id = intval($_SESSION['user_id']);
$annonce_id = intval($_GET['annonce_id']);

// Vérifie si l'annonce est déjà enregistrée
$stmt = $conn->prepare("SELECT * FROM favoris WHERE user_id = ? AND annonce_id = ?");
$stmt->bind_param("ii", $user_id, $annonce_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Supprimer
    $delete = $conn->prepare("DELETE FROM favoris WHERE user_id = ? AND annonce_id = ?");
    $delete->bind_param("ii", $user_id, $annonce_id);
    $delete->execute();
} else {
    // Ajouter
    $insert = $conn->prepare("INSERT INTO favoris (user_id, annonce_id) VALUES (?, ?)");
    $insert->bind_param("ii", $user_id, $annonce_id);
    $insert->execute();
}

// Retour à la page précédente
$redirect = $_SERVER['HTTP_REFERER'] ?? 'buy.php';
header("Location: $redirect");
exit;
