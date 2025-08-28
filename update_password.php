<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupérer les champs POST
$old_password = $_POST['old_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Vérifications basiques
if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
    header("Location: change_password.php?error=Tous les champs sont obligatoires");
    exit;
}

if ($new_password !== $confirm_password) {
    header("Location: change_password.php?error=Les nouveaux mots de passe ne correspondent pas");
    exit;
}

if (strlen($new_password) < 6) {
    header("Location: change_password.php?error=Le nouveau mot de passe doit faire au moins 6 caractères");
    exit;
}

// Récupérer le hash du mot de passe actuel
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: change_password.php?error=Utilisateur introuvable");
    exit;
}

$user = $result->fetch_assoc();
$hash_password = $user['password'];

// Vérifier ancien mot de passe
if (!password_verify($old_password, $hash_password)) {
    header("Location: change_password.php?error=Ancien mot de passe incorrect");
    exit;
}

// Hasher le nouveau mot de passe
$new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);

// Mettre à jour dans la base
$update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$update_stmt->bind_param("si", $new_password_hashed, $user_id);
$update_stmt->execute();

header("Location: profile.php?success=1");
exit;
