<?php
require 'db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.html");
    exit;
}

// Récupérer l'id de l'annonce à éditer
$annonce_id = $_GET['id'] ?? null;
if (!$annonce_id || !is_numeric($annonce_id)) {
    header("Location: profile.php");
    exit;
}

// Récupérer l'annonce et vérifier qu'elle appartient à l'utilisateur
$stmt = $conn->prepare("SELECT * FROM annonces WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $annonce_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Annonce non trouvée ou pas la sienne
    header("Location: profile.php");
    exit;
}

$annonce = $result->fetch_assoc();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyer et récupérer les données
    $titre = trim($_POST['titre'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $prix = trim($_POST['prix'] ?? '');
    $statut = trim($_POST['statut'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    // Validation simple
    if ($titre === '') {
        $errors[] = "Le titre est obligatoire.";
    }
    if ($ville === '') {
        $errors[] = "La ville est obligatoire.";
    }
    if (!is_numeric($prix) || $prix <= 0) {
        $errors[] = "Le prix doit être un nombre positif.";
    }
    if (!in_array($statut, ['À vendre', 'À louer'])) {
        $errors[] = "Le statut est invalide.";
    }
 // Si pas d'erreurs, mise à jour
if (empty($errors)) {
    $update_stmt = $conn->prepare("UPDATE annonces SET titre = ?, ville = ?, prix = ?, statut = ?, description = ? WHERE id = ? AND user_id = ?");
    $update_stmt->bind_param("ssdssii", $titre, $ville, $prix, $statut, $description, $annonce_id, $user_id);
    if ($update_stmt->execute()) {
        // Redirection après mise à jour réussie
        header("Location: profile.php?updated=1");
        exit;
    } else {
        $errors[] = "Erreur lors de la mise à jour. Veuillez réessayer.";
    }
}

}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Modifier l'annonce - ImmoKeys</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f9f9f9;
            margin: 0; padding: 0;
        }
        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            
        }
        h1 {
            color: #003366;
            margin-bottom: 25px;
            font-weight: 600;
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }
        input[type="text"], input[type="number"], select, textarea {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus, input[type="number"]:focus, select:focus, textarea:focus {
            border-color: #003366;
            outline: none;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        button {
            background-color:#D4AF37;
            color: white;
            border: none;
            padding: 14px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            font-size: 16px;
            transition: background-color 0.3s ease;
            display: block;
            width: 100%;
        }
        button:hover {
            background-color:#D4AF38;
        }
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .error {
            background: #fceae9;
            color: #c0392b;
            border: 1px solid #e74c3c;
        }
        .success {
            background: #e6f4ea;
            color: #2d7a2d;
            border: 1px solid #2ecc71;
        }
        
    </style>
</head>
<body>
<h1>Modifier</h1>
<div class="container">
    

    <?php if (!empty($errors)): ?>
        <div class="message error">
            <ul style="margin:0; padding-left: 20px;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <label for="titre">Titre</label>
        <input type="text" id="titre" name="titre" required value="<?= htmlspecialchars($annonce['titre']) ?>" />
        <label for="type">Type</label>
        <select id="type" name="type" required>
      <option value="Appartement" <?= $annonce['type'] === 'Appartement' ? 'selected' : '' ?>>Appartement</option>
      <option value="Maison" <?= $annonce['type'] === 'Maison' ? 'selected' : '' ?>>Maison</option>
      <option value="Terrain" <?= $annonce['type'] === 'Terrain' ? 'selected' : '' ?>>Terrain</option>
      <option value="Local commercial" <?= $annonce['type'] === 'Local commercial' ? 'selected' : '' ?>>Local commercial</option>
    </select>
        <label for="ville">Ville</label>
        <input type="text" id="ville" name="ville" required value="<?= htmlspecialchars($annonce['ville']) ?>" />

        <label for="prix">Prix (DH)</label>
        <input type="number" id="prix" name="prix" min="0" step="1000" required value="<?= htmlspecialchars($annonce['prix']) ?>" />

        <label for="statut">Statut</label>
        <select id="statut" name="statut" required>
            <option value="À vendre" <?= $annonce['statut'] === 'À vendre' ? 'selected' : '' ?>>À vendre</option>
            <option value="À louer" <?= $annonce['statut'] === 'À louer' ? 'selected' : '' ?>>À louer</option>
        </select>

        <label for="description">Description</label>
        <textarea id="description" name="description"><?= htmlspecialchars($annonce['description']) ?></textarea>

        <button type="submit">Mettre à jour</button>
        
    </form>
           


</div>

</body>
</html>
