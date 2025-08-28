<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImmoKeys</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap" rel="stylesheet">
</head>

<body>
    <header class="header">
        
       
 <img src="ll.png" alt="Shop Logo" class="shop-logo">
        
        <nav class="menu">
            <a href="./buy.php" class="menu-items">Buy</a>
             <a href="./rent.php" class="menu-items">to rent out</a>
            <a href="./sell.php" class="menu-items">Sell</a>
            <a href="./estimate.php" class="menu-items">estimate</a>
            <a href="./contact.html" class="menu-items">Contact</a>
            <div class="price">
                <a href="./discover.php" class="menu-items gold-btn">
                    <i class="fas fa-arrow-right" style="border-radius: 999px;"></i> discover_ prices
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

    <section class="hero-section">
        <img src="home1.jpg" alt="ImmoKeys Hero" class="hero-bg">
        <div class="hero-overlay">
       <form class="search-form" method="GET" action="buy.php">
    <select name="type" class="search-select" required>
        <option value="" disabled selected hidden>choose..</option>
        <option value="buy">Buy</option>
        <option value="rent">to rent out</option>
    </select>

     

    <div class="search-bar-group">
<select name="ville" class="search-select" style="width:80%;">
  <option value="" selected>Choisir une ville</option>
  <option value="Agadir">Agadir</option>
  <option value="Al Hoceïma">Al Hoceïma</option>
  <option value="Béni Mellal">Béni Mellal</option>
  <option value="Berrechid">Berrechid</option>
  <option value="Casablanca">Casablanca</option>
  <option value="Casablanca">Chefchaouen</option>
  <option value="El Jadida">El Jadida</option>
  <option value="Errachidia">Errachidia</option>
  <option value="Fès">Fès</option>
  <option value="Guelmim">Guelmim</option>
  <option value="Kenitra">Kenitra</option>
  <option value="Khémisset">Khémisset</option>
  <option value="Khouribga">Khouribga</option>
  <option value="Larache">Larache</option>
  <option value="Marrakech">Marrakech</option>
  <option value="Mohammedia">Mohammedia</option>
  <option value="Nador">Nador</option>
  <option value="Ouarzazate">Ouarzazate</option>
  <option value="Oujda">Oujda</option>
  <option value="Rabat">Rabat</option>
  <option value="Safi">Safi</option>
  <option value="Settat">Settat</option>
  <option value="Tanger">Tanger</option>
  <option value="Taza">Taza</option>
  <option value="Tétouan">Tétouan</option>
</select>

    <button type="submit" class="search-btn">Rechercher</button>
    </div>
</form>


        </div>
    </section>

    <section class="vertical-gallery">
        <div class="gallery-row">
            <img src="ph1.jpeg" alt="Bien 1">
            <div class="gallery-desc">
                <h3>Looking to buy?</h3>
                <p>
                    Discover our selection of modern apartments and homes, ready for you to move in.
                    <a href="./buy.php" class="gallery-link">
                        <i class="fas fa-arrow-right"></i> Buy
                    </a>
                </p>
            </div>
        </div>
        <div class="gallery-row">
            <img src="ph2.jpeg" alt="Bien 2">
            <div class="gallery-desc">
                <h3>Want to sell?</h3>
                <p>
                    Trust our team to help you find the right buyer quickly and easily.
                    <a href="./sell.php" class="gallery-link">
                        <i class="fas fa-arrow-right"></i> Sell
                    </a>
                </p>
            </div>
        </div>
        <div class="gallery-row">
            <img src="est1.jpeg" alt="Bien 3">
            <div class="gallery-desc">
                <h3>Need an estimate?</h3>
                <p>
                    Get a free and accurate property valuation by our experts.
                    <a href="./estimate.html" class="gallery-link">
                        <i class="fas fa-arrow-right"></i> Estimate
                    </a>
                </p>
            </div>
        </div>
    </section>

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
        const burger = document.getElementById('burger-menu');
        const menu = document.querySelector('.menu');
        burger?.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.toggle('active');
        });

        // Fermer le menu si on clique ailleurs
        document.addEventListener('click', function(e) {
            if (menu.classList.contains('active') && !menu.contains(e.target) && e.target !== burger) {
                menu.classList.remove('active');
            }
        });
         const form = document.querySelector('.search-form');
  const typeSelect = form.querySelector('select[name="type"]');

  typeSelect.addEventListener('change', () => {
    if (typeSelect.value === 'buy') {
      form.action = 'buy.php';
    } else if (typeSelect.value === 'rent') {
      form.action = 'rent.php';
    } else {
      form.action = 'buy.php'; 
    }
  });
    </script>
</body>
</html>
