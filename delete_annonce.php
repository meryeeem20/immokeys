<?php
require 'db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.html");
    exit;
}

// Vérifier que l'ID de l'annonce est passé en paramètre
if (!isset($_GET['id'])) {
    header("Location: profil.php");
    exit;
}

$annonce_id = (int)$_GET['id'];

// Vérifier que cette annonce appartient bien à l'utilisateur connecté
$check_stmt = $conn->prepare("SELECT id FROM annonces WHERE id = ? AND user_id = ?");
$check_stmt->bind_param("ii", $annonce_id, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    // Annonce non trouvée ou appartient à un autre utilisateur
    header("Location: profil.php");
    exit;
}

// Supprimer l'annonce
$delete_stmt = $conn->prepare("DELETE FROM annonces WHERE id = ?");
$delete_stmt->bind_param("i", $annonce_id);
$delete_stmt->execute();

// Récupérer les images associées à l'annonce
$img_stmt = $conn->prepare("SELECT images FROM annonces WHERE id = ?");
$img_stmt->bind_param("i", $annonce_id);
$img_stmt->execute();
$img_result = $img_stmt->get_result();
if ($img_row = $img_result->fetch_assoc()) {
    $images = json_decode($img_row['images'], true);
    foreach ($images as $img) {
        $path = 'uploads/' . $img; // adapte le chemin si besoin
        if (file_exists($path)) {
            unlink($path); // supprimer le fichier
        }
    }
}
header("Location: profile.php?deleted=1");
exit;
?>
