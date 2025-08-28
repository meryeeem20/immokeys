<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];

// Requête pour récupérer les annonces enregistrées
$sql = "
    SELECT a.*, u.fullname, u.photo
    FROM favoris f
    JOIN annonces a ON f.annonce_id = a.id
    JOIN users u ON a.user_id = u.id
    WHERE f.user_id = $user_id
    ORDER BY f.date_enregistrement DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Annonces enregistrées</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
           
        }

        h2 {
           padding: 38px;
            margin-bottom: 20px;
            color:black;
        }

        .biens-container {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;

      margin: 0 auto;
      width: 100%;
      box-sizing: border-box;
      margin-bottom: 60px; 
    }

        .bien-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .bien-card:hover {
            transform: translateY(-5px);
        }

        .bien-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .bien-info {
            padding: 15px;
        }

        .bien-info h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .bien-info p {
            margin-bottom: 8px;
        }

        .prix {
            color: #2e5885;
            font-weight: bold;
            font-size: 16px;
        }

        .date-annonce {
            color: #888;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .icone {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: rgb(21, 45, 72);
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            margin-top: 10px;
        }

        .save-btn i {
            font-size: 22px;
            color: #D4AF37;
        }

        .message {
            text-align: center;
            font-size: 20px;
            color: #444;
            margin-top: 60px;
        }
    </style>
</head>
<body>
<header class="header">
        <img src="ll.png" alt="Shop Logo" class="shop-logo">
        <nav class="menu">
            <a href="home.php" class="menu-items" >home</a>
            <a href="./buy.php" class="menu-items">Buy</a>
             <a href="./rent.php" class="menu-items">to rent out</a>
            <a href="./sell.php" class="menu-items">Sell</a>
            <a href="./estimate.html" class="menu-items">estimate</a>
            <a href="./contact.html" class="menu-items">Contact</a>
            <div class="price">
                <a href="./discover.php" class="menu-items gold-btn">
                    <i class="fas fa-arrow-right"></i> discover_ prices
                </a>
            </div>
        </nav>
        <div class="header-icons">
            <a href="./saved.php"><i class="fas fa-heart"></i></a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php"><i class="fas fa-user"></i></a>
            <?php else: ?>
                <a href="login.html"><i class="fas fa-user"></i></a>
            <?php endif; ?>
        </div>
        <button class="burger" id="burger-menu" aria-label="Menu">
            <i class="fas fa-bars"></i>
        </button>
    </header>
<h2>Enregistrements</h2>

<div class="biens-container" >
<?php if ($result->num_rows === 0): ?>
    <div class="message">Vous n'avez enregistré aucune annonce.</div>
<?php endif; ?>

<?php while($annonce = $result->fetch_assoc()):
    $images = json_decode($annonce['images'], true);
    $img = isset($images[0]) ? $images[0] : 'default.jpg';
    $profilePhoto = !empty($annonce['photo']) ? 'profiles/' . htmlspecialchars($annonce['photo']) : 'profiles/default_profile.jpeg';
    $fullname = htmlspecialchars($annonce['fullname']);
?>

    <div class="bien-card">
        <div class="user-info" style="display:flex;align-items:center;gap:10px;padding:10px 15px 0 15px;margin-bottom:12px;">
            <img src="<?= $profilePhoto ?>" alt="Profil" style="width:38px;height:38px;border-radius:50%;object-fit:cover;">
            <span style="font-weight:600;"><?= $fullname ?></span>
        </div>
        <img src="<?= htmlspecialchars($img) ?>" alt="Annonce">
        <div class="bien-info">
            <h3><?= htmlspecialchars($annonce['titre']) ?></h3>
            <p>Type : <?= htmlspecialchars($annonce['type']) ?> | Statut : <?= htmlspecialchars($annonce['statut']) ?></p>
            <p>Surface : <?= $annonce['superficie'] ?> m² | <?= $annonce['pieces'] ?> pièces</p>
            <p class="prix"><?= number_format($annonce['prix'], 0, ',', ' ') ?> MAD</p>
            <p class="date-annonce">Publié le : <?= date('d/m/Y', strtotime($annonce['date_publication'])) ?></p>

            <div class="icone">
                <a href="details.php?id=<?= $annonce['id'] ?>" class="btn">Voir plus..</a>
                <a href="save.php?annonce_id=<?= $annonce['id'] ?>" class="save-btn" title="Supprimer des favoris">
                    <i class="fas fa-bookmark"></i>
                </a>
            </div>
        </div>
    </div >

<?php endwhile; ?>
</div>
<footer>
        <div class="footerContainer">
            <div class="socialIcons">
                <a href=""><i class="fa-brands fa-facebook"></i></a>
                <a href=""><i class="fa-brands fa-instagram"></i></a>
                <a href=""><i class="fa-brands fa-twitter"></i></a>
                <a href=""><i class="fa-brands fa-google-plus"></i></a>
                
            </div>
            <div class="footerNav">
                <ul><li><a href="home.html">home</a></li>
            
                    <li><a href="./me.html">me</a></li>
                    <li><a href="./contact.html">Contact</a></li>
                    
                </ul>
            </div>
            
        </div>
        <div class="footerBottom">
            <p>Copyright &copy;2025; Designed by <span class="designer">meryem</span></p>
        </div>
    </footer>
</body>
</html>
