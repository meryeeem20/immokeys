<?php
session_start();
require 'db.php';

if (!isset($_GET['id'])) {
    echo "Aucune annonce sélectionnée.";
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM annonces WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$annonce = $result->fetch_assoc();

if (!$annonce) {
    echo "Annonce introuvable.";
    exit;
}

$images = json_decode($annonce['images'], true);
if (!is_array($images)) $images = [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails de l'annonce</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap" rel="stylesheet">

    <style>
        .details-container {
            display: flex;
            gap: 0;
            margin: 0;
            width: 100vw;
            min-height: 100vh;
            
        }
        .details-info {
            flex-basis: 50%;
            flex-grow: 0;
            max-width: 50%;
            background: #fff;
            border-radius: 20px 0 0 10px;
            padding: 120px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            line-height:2;
            font-size: 1.2em;
            padding-left:170px;
        }
        .details-info h2 {
            margin-top: 0;
        }
        .details-info p {
            margin: 10px 0;
        }
        .carousel-container {
            flex-basis: 50%;
            flex-grow: 0;
           
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f7f7f7;
            border-radius: 0 10px 10px 0;
        }
        .carousel {
            position: relative;
            width: 90%;
            height: 70vh;
            background: #f7f7f7;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .carousel img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }
        .carousel-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 2.6em;
            color: #2e5885;
            background: rgba(255,255,255,0.7);
            border: none;
            cursor: pointer;
            z-index: 2;
            padding: 0 10px;
            border-radius: 50%;
            transition: background 0.2s;
        }
        .carousel-arrow:hover {
            background: #D4AF37;
            color: #fff;
        }
        .carousel-arrow.left { left: 10px; }
        .carousel-arrow.right { right: 10px; }
        .carousel-indicators {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }
        .carousel-indicators span {
            display: inline-block;
            width: 10px;
            height: 10px;
            background: #bbb;
            border-radius: 50%;
            margin: 0 3px;
            cursor: pointer;
        }
        .carousel-indicators .active {
            background: #2e5885;
        }
        @media (max-width: 800px) {
            .details-container { flex-direction: column; align-items: center; }
            .carousel-container, .details-info { width: 100%; }
            .carousel { width: 100%; max-width: 350px; }
        }
        .price{
            color: #D4AF37;
        }
        .header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 30px;
  background-color: #11113a;
  color: white;
}

.header-left,
.header-center,
.header-icons {
  display: flex;
  align-items: center;
}

.header-center {
  gap: 35px;
}

.menu-items {
  font-size: 20px;
  color: #D4AF37;
  text-decoration: none;
  font-weight: bold;
}

.menu-items:hover {
  background-color: #11113a;
  color: #D4AF37;
}


    .back-button {
      font-size: 25px;
      color: #D4AF37;
      text-decoration: none;
      margin-left: 12px;
    }

    .back-button:hover {
      color: white;
    }

    .header-icons a {
      
      font-size: 20px;
      color: #D4AF37;
      text-decoration: none;
    }

    .header-icons a:hover {
      color: white;
    }
    .menu-items.active {
  border-bottom: 5px solid white;
  padding-bottom: 8px;
    }
    .carousel img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    transition: transform 0.4s ease;
}

.carousel img:hover {
    transform: scale(1.05);
}

.carousel-arrow {
    background: rgba(0,0,0,0.4);
    color: #fff;
}

.carousel-arrow:hover {
    background: #D4AF37;
}
.details-info {
    background: linear-gradient(135deg, #ffffff, #f9f9f9);
    border-radius: 20px 0 0 20px;
    padding: 80px 60px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    line-height: 1.8;
}

.details-info h2 {
    color: #051e38ff;
    margin-bottom: 15px;
    font-size: 2em;
}

.details-info p strong {
    color:#D4AF37;
    font-weight: 600;
}

.details-info .price {
    font-size: 1.6em;
    font-weight: bold;
}
.carousel-indicators span {
    width: 12px;
    height: 12px;
    background: #ccc;
    margin: 0 5px;
    transition: all 0.3s;
}

.carousel-indicators .active {
    background: #2e5885;
    transform: scale(1.3);
    border: 2px solid #D4AF37;
}
.details-container {
    animation: fadeIn 0.8s ease;
}

@keyframes fadeIn {
    from {opacity: 0; transform: translateY(20px);}
    to {opacity: 1; transform: translateY(0);}
}

        
    </style>
</head>
<body>
    <header class="header">
 
  <div class="header-left">
    <a href="home.php" class="back-button" title="Retour à l'accueil">
      <i class="fa-solid fa-arrow-left"></i>
    </a>
  </div>

  <div class="header-center">
    <a href="./details.php" class="menu-items active">Details </a>
    <a href="./discover.php" class="menu-items">Average prices
    </a>
  </div>

  <div class="header-icons">
    <a href="saved.php"><i class="fas fa-heart"></i></a>
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="profile.php"><i class="fas fa-user"></i></a>
    <?php else: ?>
      <a href="login.html"><i class="fas fa-user"></i></a>
    <?php endif; ?>
  </div>
</header>
    <div class="details-container">
         
        <div class="details-info">
            <h2><?php echo htmlspecialchars($annonce['titre']); ?></h2>
            <p><strong>Type :</strong> <?php echo htmlspecialchars($annonce['type']); ?></p>
            <p><strong>Ville :</strong> <?php echo htmlspecialchars($annonce['ville']); ?></p>
            <p><strong>Statut :</strong> <?php echo htmlspecialchars($annonce['statut']); ?></p>
            <p><strong>Surface :</strong> <?php echo $annonce['superficie']; ?> m²</p>
            <p><strong>Pièces :</strong> <?php echo $annonce['pieces']; ?></p>
            <p><strong>Description :</strong><br><?php echo nl2br(htmlspecialchars($annonce['description'])); ?></p>
             <p><strong>Date de publication :</strong> <?php echo date('d/m/Y', strtotime($annonce['date_publication'])); ?></p>
            <p class="price"><strong>Prix :</strong> <?php echo number_format($annonce['prix'], 0, ',', ' '); ?> MAD</p>
           
            
            <p><strong>Contact :</strong> <?php echo htmlspecialchars($annonce['contact']); ?></p>
        </div>
       <div class="carousel-container">
            <div class="carousel" id="carousel">
                <button class="carousel-arrow left" onclick="prevImage()" aria-label="Précédent">&lt;</button>
                <img id="carousel-img" src="<?php echo htmlspecialchars($images[0] ?? 'default.jpg'); ?>" alt="Photo">
                <button class="carousel-arrow right" onclick="nextImage()" aria-label="Suivant">&gt;</button>
            </div>
            <div class="carousel-indicators" id="carousel-indicators">
                <?php foreach ($images as $idx => $img): ?>
                    <span class="<?php echo $idx === 0 ? 'active' : ''; ?>" onclick="goToImage(<?php echo $idx; ?>)"></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <footer>
        <div class="footerContainer">
            <div class="socialIcons">
                <a href="#"><i class="fa-brands fa-facebook"></i></a>
                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                <a href="#"><i class="fa-brands fa-twitter"></i></a>
                <a href="#"><i class="fa-brands fa-google-plus"></i></a>
            </div>
            <div class="footerNav">
                <ul>
                    <li><a href="./">home</a></li>
                    <li><a href="./profile.php">me</a></li>
                    <li><a href="./contact.html">Contact</a></li>
                </ul>
            </div>
        </div>
        <div class="footerBottom">
            <p>Copyright &copy;2025; Designed by <span class="designer">meryem</span></p>
        </div>
    </footer>
    <script>
        // Carousel JS
        const images = <?php echo json_encode($images); ?>;
        let current = 0;
        const imgEl = document.getElementById('carousel-img');
        const indicators = document.getElementById('carousel-indicators').children;

        function updateCarousel() {
            imgEl.src = images[current] || 'default.jpg';
            for (let i = 0; i < indicators.length; i++) {
                indicators[i].classList.toggle('active', i === current);
            }
        }
        function prevImage() {
            current = (current - 1 + images.length) % images.length;
            updateCarousel();
        }
        function nextImage() {
            current = (current + 1) % images.length;
            updateCarousel();
        }
        function goToImage(idx) {
            current = idx;
            updateCarousel();
        }
    </script>
</body>
</html>