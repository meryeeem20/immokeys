<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Changer le mot de passe</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            width: 400px;
        }
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 15px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        button {
            width: 100%;
            background: #274564;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background: #3c7ac7;
        }
        .error {
            color: #b00020;
            margin: 10px 0;
        }
        .success {
            color: #2e7d32;
            margin: 10px 0;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Changer le mot de passe</h2>

    <?php
    if (isset($_GET['error'])) {
        echo '<div class="error">'.htmlspecialchars($_GET['error']).'</div>';
    }
    if (isset($_GET['success'])) {
        echo '<div class="success">Mot de passe changé avec succès !</div>';
    }
    ?>

    <form action="update_password.php" method="post">
        <label for="old_password">Ancien mot de passe :</label>
        <input type="password" id="old_password" name="old_password" required />

        <label for="new_password">Nouveau mot de passe :</label>
        <input type="password" id="new_password" name="new_password" required />

        <label for="confirm_password">Confirmer le nouveau mot de passe :</label>
        <input type="password" id="confirm_password" name="confirm_password" required />

        <button type="submit">Modifier</button>
    </form>
</div>

</body>
</html>
