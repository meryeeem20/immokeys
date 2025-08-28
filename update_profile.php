<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$new_fullname = trim($_POST['fullname'] ?? '');



if (!$new_fullname) {
    // nom vide, on peut gérer l’erreur ici si besoin
    header("Location: edit_profile.php?error=emptyname");
    exit;
}

// Récupérer l’ancienne photo (pour suppression si besoin)
$stmt = $conn->prepare("SELECT photo FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$photo = $stmt->get_result()->fetch_assoc()['photo'] ?? 'default_profile.jpeg';

// 1. Si bouton "supprimer photo" cliqué
if (isset($_POST['reset_photo'])) {
    if ($photo && $photo !== 'default_profile.jpeg') {
        $path = 'profiles/' . $photo;
        if (file_exists($path)) unlink($path);
    }
    // Mettre à jour photo par défaut
    $stmt = $conn->prepare("UPDATE users SET photo = 'default_profile.jpeg' WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Mettre à jour nom aussi (sinon on ne fait rien pour nom)
    $stmt = $conn->prepare("UPDATE users SET fullname = ? WHERE id = ?");
    $stmt->bind_param("si", $new_fullname, $user_id);
    $stmt->execute();

    header("Location: profile.php?success=photo_deleted");
    exit;
}

// 2. Sinon on regarde si une nouvelle photo est uploadée
if (isset($_FILES['new_photo']) && $_FILES['new_photo']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['new_photo']['tmp_name'];
    $fileName = basename($_FILES['new_photo']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($fileExt, $allowedExts)) {
        header("Location: profile.php?error=invalidformat");
        exit;
    }

    // Générer un nom unique pour éviter conflit
    $newFileName = uniqid('profile_', true) . '.' . $fileExt;
    $destPath = 'profiles/' . $newFileName;

    if (move_uploaded_file($fileTmpPath, $destPath)) {
        // Supprimer l’ancienne photo si custom
        if ($photo && $photo !== 'default_profile.jpeg') {
            $oldPath = 'profiles/' . $photo;
            if (file_exists($oldPath)) unlink($oldPath);
        }

        // Mettre à jour la BDD avec nouvelle photo
        $stmt = $conn->prepare("UPDATE users SET photo = ?, fullname = ? WHERE id = ?");
        $stmt->bind_param("ssi", $newFileName, $new_fullname, $user_id);
        $stmt->execute();

        header("Location: profile.php?success=updated");
        exit;
    } else {
        header("Location: profile.php?error=uploadfail");
        exit;
    }
}

// 3. Sinon juste mettre à jour le nom (pas de photo uploadée ni reset)
$stmt = $conn->prepare("UPDATE users SET fullname = ? WHERE id = ?");
$stmt->bind_param("si", $new_fullname, $user_id);
$stmt->execute();

header("Location: profile.php?success=updated");
exit;
?>
