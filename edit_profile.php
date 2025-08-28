<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT fullname, photo FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$fullname = htmlspecialchars($user['fullname']);
$photo = $user['photo'];
$has_custom_photo = $photo && $photo !== 'default_profile.jpeg';
$photo_to_display = $has_custom_photo ? $photo : 'default_profile.jpeg';
echo "<!-- PHOTO CHARGÉE : $photo -->";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le profil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: white;
            padding: 120px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 920px;
            text-align: center;
            height:90vh;
            align-items:center;
        }

        .profile-wrapper {
            position: relative;
            width: 180px;
            height: 180px;
            margin: 0 auto 85px;
            
        }

        .profile-wrapper img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #ddd;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .camera-btn {
            position: absolute;
            bottom: 5px;
            right: 9px;
            background: #11113a;
            color: white;
            border-radius: 50%;
            padding: 12px;
            cursor: pointer;
            border: 2px solid white;
        }

        .photo-options {
            display: none;
            margin-bottom: 2px;
            text-align: left;
        }

        .photo-options button, 
        .photo-options input[type="file"] {
            display: block;
            width: 100%;
            margin: 10px 0;
        }

        .photo-options button {
            background: #eee;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
        }

        .photo-options button:hover {
            background: #ddd;
        }

        form input[type="text"] {
            padding: 12px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 16px;
        }

        form button[type="submit"] {
           
            
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
        }

        form button[type="submit"]:hover {
             background: #ddd;
        }
        form label {
    display: block;              
    font-weight: 700;            
    color: black;
    margin-bottom: 8px;          
    font-size: 17px;
    text-align: left;            
    cursor: pointer;             
    user-select: none;
    transition: color 0.3s ease; 
}
.container label {
    display: block;              
    font-weight: 700;            
    color: black;
    margin-bottom: 8px;          
    font-size: 17px;
    text-align: left;            
    cursor: pointer;             
    user-select: none;
    transition: color 0.3s ease; 
    gap:55px;
}

    </style>
</head>
<body>
    
<div class="container">
 <label for="fullname">changer la photo de profile :</label>
<div class="profile-wrapper">
           
<img src="profiles/<?= htmlspecialchars($photo_to_display) ?>" alt="Photo de profil">
        <div class="camera-btn" onclick="toggleOptions()">
            <i class="fas fa-camera"></i>
        </div>
    </div>
    <form action="update_profile.php" method="post" enctype="multipart/form-data">
    <div class="photo-options" id="photoOptions" style="display: none;">
        <input type="file" id="fileInput" name="new_photo" accept="image/*" style="display: none;">
        <button type="button" onclick="document.getElementById('fileInput').click()">Changer la photo</button>
        <?php if ($has_custom_photo): ?>
        <button type="submit" name="reset_photo" value="1">Supprimer la photo</button>
        <?php endif; ?>
    </div>

    <label for="fullname">changer le nom :</label>
    <input type="text" name="fullname" value="<?= $fullname ?>" required>
    <button type="submit" style="background: #11113a;color:white";>Enregistrer</button>
</form>

</div>

<script>
function toggleOptions() {
    const options = document.getElementById('photoOptions');
    options.style.display = options.style.display === 'block' ? 'none' : 'block';
}
</script>


</body>
</html>
