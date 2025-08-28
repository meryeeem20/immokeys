<?php
require 'db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.html");
    exit;
}

// Récupérer les infos de l'utilisateur
$user_stmt = $conn->prepare("SELECT fullname, photo FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

// Récupérer les annonces publiées par l'utilisateur
$annonce_stmt = $conn->prepare("SELECT * FROM annonces WHERE user_id = ? ORDER BY date_publication DESC");
$annonce_stmt->bind_param("i", $user_id);
$annonce_stmt->execute();
$annonces = $annonce_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil - ImmoKeys</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            background: #f9f9f9;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #003366;
            color: white;
            padding: 20px 30px;
        }
        .profile-info {
            display: flex;
            align-items: center;
        }
        .profile-info img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
            border: 2px solid white;
        }
        .settings {
            font-size: 24px;
            cursor: pointer;
            color: white;
        }
        .container {
            padding: 30px;
        }
        .annonce {
    position: relative;
    background: white;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);

    display: flex;
    align-items: center;
    gap: 20px;

    justify-content: space-between; /* <-- pousse le menu complètement à droite */
}

.annonce img {
    width: 120px;
    height: 100px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
}

.annonce-details {
    flex: 1;
    /* margin-left ajouté directement en ligne ou ici */
}

.menu-container {
    position: relative;
    cursor: pointer;
    flex-shrink: 0;
   
}

.menu-icon {
    font-size: 22px;
    color: #555;
    margin-right: 35px;
}

.menu-options {
    display: none;
    position: absolute;
    top: 36px;
    right: 0;
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    min-width: 160px;
    z-index: 1000;
    flex-direction: column;
    font-size: 14px;
    overflow: hidden;
    transform-origin: top right;
    transition: transform 0.2s ease, opacity 0.2s ease;
    opacity: 0;
    pointer-events: none;
    
    width:40vh;
}

.menu-options.show {
    display: flex;
    opacity: 1;
    pointer-events: auto;
    transform: scale(1);
}

.menu-options a {
    display: block;
    padding: 12px 18px;
    color: #333;
    text-decoration: none;
    font-weight: 600;
    transition: background-color 0.2s ease, color 0.2s ease;
    gap:15px;
    justify-content: center;
    
}

.menu-options a:hover {
    background-color: #f0f4ff;
    color: #003366;
}

/* Style spécial pour le bouton supprimer */
.menu-options a.delete {
    color: #e74c3c;
    font-weight: 700;
}

.menu-options a.delete:hover {
    background-color: #fceae9;
    color: #c0392b;
}

.success-message {
    
    color:rgb(36, 39, 36) ;
    border: 1px solidrgb(127, 135, 129);
    padding: 12px 18px;
    border-radius: 8px;
    font-weight: 600;
    margin-bottom: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    transition: opacity 0.5s ease;
}

.settings-panel {
    position: fixed;
    top: 0;
    right: 0;
    width: 280px;
    height: 75vh;
    background-color: #fff;
    box-shadow: -8px 0 20px rgba(0, 0, 0, 0.08);
    transform: translateX(100%);
    transition: transform 0.3s ease-in-out;
    z-index: 10000;
    padding: 24px;
    display: flex;
    flex-direction: column;
    font-family: 'Segoe UI', sans-serif;
}

.settings-panel.show {
    transform: translateX(0);
}

.settings-panel h2 {
    font-size: 18px;
    font-weight: 600;
    color: #222;
    margin-bottom: 40px;
   text-align: center;  
    
}

.settings-panel nav {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.settings-panel nav a {
    display: flex;
    align-items: center;
    gap: 30px;
    text-decoration: none;
    color: #333;
    font-size: 15px;
    font-weight: 500;
    padding: 10px;
    border-radius: 6px;
    transition: background-color 0.2s;
}

.settings-panel nav a i {
    font-size: 20px;
    color: #555;
    min-width: 18px;
}

.settings-panel nav a:hover {
    background-color: #f2f2f2;
}

.close-settings {
    position: absolute;
    top: 16px;
    right: 16px;
    font-size: 18px;
    background: none;
    border: none;
    color: #888;
    cursor: pointer;
    transition: color 0.2s ease;
}

.close-settings:hover {
    color: #000;
}


    </style>
</head>
<body>

<div class="header">
    <div class="profile-info">
        <?php
$photo = !empty($user['photo']) ? 'profiles/' . $user['photo'] : 'profiles/default_profile.jpeg';
?>
<img src="<?php echo htmlspecialchars($photo); ?>" alt="Photo de profil">
        <div>
            <h2><?php echo htmlspecialchars($user['fullname']); ?></h2>
           
        </div>
    </div>
   <div>
    <i class="fas fa-cog settings" title="Réglages" id="openSettings"></i>
</div>

</div>
<?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
    <div class="success-message" id="deleteMessage">
        Annonce supprimée avec succès.
    </div>
<?php endif; ?>
<div class="container">
    <h2>Mes annonces</h2>
   
    <?php if ($annonces->num_rows > 0): ?>
        <?php while ($annonce = $annonces->fetch_assoc()): 
            $images = json_decode($annonce['images'], true);
            $firstImage = $images[0] ?? 'default.jpg';
        ?>
            <div class="annonce" data-id="<?php echo $annonce['id']; ?>">
                <img src="<?php echo htmlspecialchars($firstImage); ?>" alt="Image">
                <div class="annonce-details">
                    <h3><?php echo htmlspecialchars($annonce['titre']); ?> (<?php echo htmlspecialchars($annonce['statut']); ?>)</h3>
                    <p>📍 Ville : <?php echo htmlspecialchars($annonce['ville']); ?></p>
                    <p>💰 Prix : <?php echo number_format($annonce['prix'], 0, ',', ' '); ?> DH</p>
                    <p>🕒 Publiée le : <?php echo date('d/m/Y', strtotime($annonce['date_publication'])); ?></p>
                </div>
                <div class="menu-container">
        <i class="fas fa-ellipsis-v menu-icon"></i>
        <div class="menu-options">
<a href="edit_annonce.php?id=<?php echo $annonce['id']; ?>">
    <i class="fas fa-pen-to-square"></i>  Éditer la publication
</a>
<a href="delete_annonce.php?id=<?php echo $annonce['id']; ?>" class="delete" onclick="return confirm('Supprimer cette annonce ?');">
    <i class="fas fa-trash-alt"></i>  Supprimer la publication
</a>

        </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Aucune annonce trouvée.</p>
    <?php endif; ?>
</div>
<!-- Panneau des réglages (slide-in) -->
<aside class="settings-panel" id="settingsPanel">
    <button class="close-settings" id="closeSettings" aria-label="Fermer le panneau">
        <i class="fas fa-times"></i>
    </button>
    <h2>Réglages</h2>
    <nav>
        <a href="edit_profile.php"><i class="fas fa-user-edit"></i> Modifier le profil</a>
        <a href="change_password.php"><i class="fas fa-key"></i> Changer le mot de passe</a>
        <a href="saved.php"><i class="fas fa-heart"></i> Mes favoris</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Se déconnecter</a>
    </nav>
</aside>



<script>
document.querySelectorAll('.menu-icon').forEach(icon => {
    icon.addEventListener('click', function(e) {
        e.stopPropagation();
        const menu = this.nextElementSibling;
        // Fermer les autres menus
        document.querySelectorAll('.menu-options').forEach(m => {
            if(m !== menu) m.classList.remove('show');
        });
        menu.classList.toggle('show');
    });
});

document.addEventListener('click', () => {
    document.querySelectorAll('.menu-options').forEach(menu => {
        menu.classList.remove('show');
    });
});
window.addEventListener('DOMContentLoaded', () => {
    const message = document.getElementById('deleteMessage');
    if (message) {
        setTimeout(() => {
            message.style.opacity = '0';
            setTimeout(() => message.remove(), 500); // attend transition avant de retirer le message
        }, 2000);
    }
});
document.querySelectorAll('.annonce').forEach(annonce => {
    annonce.addEventListener('click', (e) => {
        // Si le clic est sur ou dans le menu (menu-container), ne rien faire (ne pas rediriger)
        if (e.target.closest('.menu-container')) return;

        // Sinon, récupérer l'id de l'annonce depuis un data-attribute
        const annonceId = annonce.getAttribute('data-id');
        if (annonceId) {
            window.location.href = 'details.php?id=' + annonceId;
        }
    });
});
const openSettings = document.getElementById('openSettings');
const closeSettings = document.getElementById('closeSettings');
const settingsPanel = document.getElementById('settingsPanel');

openSettings.addEventListener('click', () => {
    settingsPanel.classList.add('show');
});

closeSettings.addEventListener('click', () => {
    settingsPanel.classList.remove('show');
});

window.addEventListener('click', (e) => {
    if (!settingsPanel.contains(e.target) && !openSettings.contains(e.target)) {
        settingsPanel.classList.remove('show');
    }
});


</script>


</body>
</html>
