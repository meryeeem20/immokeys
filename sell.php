<?php 
require 'db.php';
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
if (!$user_id) {
    header("Location: login.html");
    exit;
}
$resume_annonce = null;
if (isset($_SESSION['resume_annonce'])) {
    $resume_annonce = $_SESSION['resume_annonce'];
    unset($_SESSION['resume_annonce']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Vendre un bien - Immokeys</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap" rel="stylesheet">
 <style>
  .form-tips-container {
    display: flex;
    align-items: flex-start;
    gap: 40px;
    max-width: 1400px;
    margin: 40px 0 0 45px;
    margin-bottom: 60px; 
  }
  .form-container {
    flex: 1 1 800px;
    max-width: 800px;
    
  }
  .tips-box {
   

    width: 1300px;
    border-radius: 12px;
    padding: 160px 24px;
    margin-left: 52px;
    font-size: 1.05em;
   
  }
  .tips-box h3 {
    margin-bottom: 18px;
    color:goldenrod;
    font-size: 1.2em;
    display: flex;
    align-items: center;
    gap: 8px;
   
  }
  .tips-box ul {
    padding-left: 18px;
    margin: 0;
  }
  .tips-box li {
    margin-bottom: 16px;
    line-height: 1.5;
  }
  .form-container {
    display: flex;
    justify-content: flex-start; 
    width: 100%;
    max-width: 1200px;
    margin: 45px 0 0 45px;
    
   
  }
  form {
    background: #fff;
    padding: 35px ;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    width: 800px; /* plus grand */
    min-width: 800px;
    margin: 0; /* enlève le centrage automatique */
    
  }
  label {
    display: block;
    margin-top: 15px;
  }
  input, select, textarea {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 5px;
    border: 1px solid #ccc;
  }
  .submit-btn {
    background-color: #003366;
    color: white;
    padding: 10px;
    margin-top: 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
  }
  .submit-btn:hover {
    background-color: #001f3f;
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
            <a href="sell.php" class="menu-items" >Sell</a>
            
            <a href="./estimate.php" class="menu-items">estimate</a>
            <a href="./contact.html" class="menu-items">Contact</a>
            <div class="price">
            <a href="./discover.php" class="menu-items gold-btn">
              <i class="fas fa-arrow-right"></i> discover_ prices
            </a>
            </div>
        </nav>
        <div class="header-icons">
        <a href="saved.php"><i class="fas fa-heart"></i></a>
        <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php"><i class="fas fa-user"></i></a>
            <?php else: ?>
                <a href="login.html"><i class="fas fa-user"></i></a>
            <?php endif; ?>
    </div>
 </header>


  <h2 style="text-align:left; margin-left:60px;"></h2>
  <div class="form-tips-container">
  <div class="form-container">
  <form action="sell.php" method="POST" enctype="multipart/form-data">
    <label>Titre :</label>
    <input type="text" name="titre" required>

    <label>Type de bien :</label>
    <select name="type" required>
      <option value="Appartement">Appartement</option>
      <option value="Maison">Maison</option>
      <option value="Terrain">Terrain</option>
      <option value="Local commercial">Local commercial</option>
    </select>

    <label>Statut :</label>
    <select name="statut" required>
      <option value="À vendre">À vendre</option>
      <option value="À louer">À louer</option>
    </select>

    <label>Ville / Quartier :</label>
    <input type="text" name="ville" required>

    <label>Superficie (m²) :</label>
    <input type="number" name="superficie" required>

    <label>Nombre de pièces :</label>
    <input type="number" name="pieces" required>

    <label>Prix (en DH) :</label>
    <input type="number" name="prix" required>

    <label>Description :</label>
    <textarea name="description" rows="4" required></textarea>

    <label>Contact (téléphone ou email) :</label>
    <input type="text" name="contact" required>

    <label>Photos :</label>
    <input type="file" name="images[]" multiple accept="image/*">

    <button type="submit" class="submit-btn">📩 Publier l’annonce</button>
  </form>
  </div>
  <div class="tips-box">
    <h3>💡 5 Tips to Sell Your Property Faster with Immokeys</h3>
    <ul>
      <li>
        <b>📸 Use High-Quality Photos</b><br>
        Take clear, bright pictures with natural light from multiple angles. Great visuals attract 3x more visitors.
      </li>
      <li>
        <b>📍 Highlight the Location</b><br>
        Mention nearby schools, transport, shops, and safety. Location is a key selling point for buyers.
      </li>
      <li>
        <b>💬 Be Honest and Detailed</b><br>
        Include clear info: size, number of rooms, condition, and amenities. Transparency builds trust.
      </li>
      <li>
        <b>💸 Set a Realistic Price</b><br>
        Overpricing can turn people away. Compare with similar properties in your area to stay competitive.
        for help use <a href="./estimate.html">our estimation tool</a> to get a fair price.
      </li>
      <li>
        <b>🕓 Stay Available and Responsive</b><br>
        Answer messages quickly and be flexible with visit times. Being responsive increases buyer interest.
      </li>
    </ul>
  </div>
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
                <ul><li><a href="home.php">home</a></li>
            
                    <li><a href="./profile.php">me</a></li>
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
<?php


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $type = trim($_POST['type']);
    $statut = trim($_POST['statut']);
    $ville = trim($_POST['ville']);
    $superficie = intval($_POST['superficie']);
    $pieces = intval($_POST['pieces']);
    $prix = floatval($_POST['prix']);
    $description = trim($_POST['description']);
    $contact = trim($_POST['contact']);

    // Gestion des images
    $uploadedFiles = [];
    if (!empty($_FILES['images']['name'][0])) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $fileName = basename($_FILES['images']['name'][$key]);
            $targetFile = $uploadDir . time() . '_' . $fileName;
            if (move_uploaded_file($tmp_name, $targetFile)) {
                $uploadedFiles[] = $targetFile;
            }
        }
    }
    $images = json_encode($uploadedFiles);

    // Insertion dans la base de données
$stmt = $conn->prepare("INSERT INTO annonces (titre, type, statut, ville, superficie, pieces, prix, description, contact, images, user_id, date_publication) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("ssssiidsssi", $titre, $type, $statut, $ville, $superficie, $pieces, $prix, $description, $contact, $images, $user_id);

    if ($stmt->execute()) {
         $resume_annonce = [
        'titre' => $titre,
        'statut' => $statut,
        'ville' => $ville,
        'superficie' => $superficie,
        'pieces' => $pieces,
        'prix' => $prix,
        'contact' => $contact
    ];

}
}
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Annonce publiée</title>
            <link rel="stylesheet" href="style.css">
            <style>
                .annonce-resume {
                    background: #fff;
                    border-radius: 12px;
                    box-shadow: 0 0 10px rgba(0,0,0,0.07);
                    padding: 90px 28px;
                    max-width: 600px;
                    margin: 40px auto 0 auto;
                    font-size: 1.2em;
                    color: #222;
                    text-align: center;
                }
                .annonce-resume div { margin-bottom: 10px; }
                .buttons {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
        }

        .buttons button {
            background-color: #003366;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
            </style>
        </head>
        <body>
            <?php if (isset($resume_annonce)) : ?>
    <div class="popup-overlay" style="position:fixed;top:0;left:0;width:100%;height:100%;background-color:rgba(0,0,0,0.5);z-index:999;"></div>
    <div class="popup" style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;padding:40px;border-radius:10px;z-index:1000;max-width:500px;width:90%;box-shadow:0 0 15px rgba(0,0,0,0.3);text-align:center;">
        <h3 style="color:green;">✅ Annonce publiée avec succès !</h3>
        <p><strong><?php echo htmlspecialchars($resume_annonce['titre']); ?> - <?php echo htmlspecialchars($resume_annonce['statut']); ?></strong></p>
        <p>📍 <?php echo htmlspecialchars($resume_annonce['ville']); ?> | <?php echo $resume_annonce['superficie']; ?> m² | <?php echo $resume_annonce['pieces']; ?> pièces</p>
        <p>💰 <?php echo number_format($resume_annonce['prix'], 0, ',', ' '); ?> DH</p>
        <p>📞 <?php echo htmlspecialchars($resume_annonce['contact']); ?></p>
        <div class="buttons">
        <button onclick="window.location.href='profile.php'">voir la publication</button>
        <button onclick="closePopup()">Fermer</button>
            </div>
    <script>
        function closePopup() {
            document.querySelector('.popup').remove();
            document.querySelector('.popup-overlay').remove();
        }
    </script>
<?php endif; ?>

        </body>
        </html>
        