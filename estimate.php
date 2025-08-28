<?php
session_start();  
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db.php';

$resultat = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération des champs
    $type      = $_POST['type']      ?? '';
    $surface   = $_POST['surface']   ?? 0;
    $chambres  = $_POST['chambres']  ?? 0;
    $sdb       = $_POST['sdb']       ?? 0;
    $ville     = $_POST['ville']     ?? '';
    $etage     = $_POST['etage']     ?? 0;
    $ascenseur = $_POST['ascenseur'] ?? 'Non';
    $etat      = $_POST['etat']      ?? '';
    $exterieur = $_POST['exterieur'] ?? 'Aucun';
    $parking   = $_POST['parking']   ?? 'Non';
    $annee     = $_POST['annee']     ?? 0;

    // Prix de base par ville
    $prix_ville = [
        'Casablanca' => 15000, 'Rabat' => 14500, 'Marrakech' => 12200,
        'Tanger' => 13200, 'Agadir' => 12300, 'Fès' => 5019,
        'Salé' => 7560, 'Kenitra' => 6282, 'Meknès' => 5031,
        'Tétouan' => 6447, 'Mohammedia' => 8192, 'El Jadida' => 6550,
        'Beni Mellal' => 7537, 'Skhirate' => 5003, 'Autre' => 5000
    ];

    $ville_key = ucfirst(strtolower(trim($ville)));
    $base_price = $prix_ville[$ville_key] ?? $prix_ville['Autre'];

    // Ajustement selon type de bien
    if ($type === 'Appartement' || $type === 'Terrain') $base_price -= 2000;
    if ($type === 'Local commercial') $base_price += 500;

    $estimation = $surface * $base_price;

    // Bonus/malus
    if ($etat === 'Neuf') $estimation *= 1.15;
    if ($etat === 'À rénover') $estimation *= 0.8;
    if ($exterieur === 'Jardin') $estimation += 10000;
    if ($parking === 'Oui') $estimation += 5000;
    if ($ascenseur === 'Oui' && $type === 'Appartement') $estimation += 3000;

    // Contenu HTML du résultat
    $resultat = '
    <div class="result-container">
        <div class="estimation-result">
            <h2 class="result-title">Résultat de l\'estimation</h2>
            <p>Type de bien : <strong>' . htmlspecialchars($type) . '</strong></p>
            <p>Surface : <strong>' . htmlspecialchars($surface) . ' m²</strong></p>
            <p>Ville/Quartier : <strong>' . htmlspecialchars($ville) . '</strong></p>
            <p>Nombre de chambres : <strong>' . htmlspecialchars($chambres) . '</strong></p>
            <p>Nombre de salles de bain : <strong>' . htmlspecialchars($sdb) . '</strong></p>
            <p>État : <strong>' . htmlspecialchars($etat) . '</strong></p>
            <p>Extérieur : <strong>' . htmlspecialchars($exterieur) . '</strong></p>
            <p>Parking : <strong>' . htmlspecialchars($parking) . '</strong></p>
            <p>Année de construction : <strong>' . htmlspecialchars($annee) . '</strong></p>
            <h3>Estimation : <span>' . number_format($estimation, 0, ',', ' ') . ' MAD</span></h3>
        </div>
        <div class="center-btn">
            <a href="estimate.php">Faire une nouvelle estimation</a>
        </div>
    </div>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Estimation Immokeys</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body {
  font-family: 'Montserrat', sans-serif;
  background: #f0f0f0;
  margin: 0;
  padding: 0;
}

/* Conteneur global pour tout le contenu */
.main-container {
  width: 90%;
  max-width: 900px;
  margin: 0 auto;
  padding: 30px;
}
.form-section-wrapper { 
  width:60%; display:flex; flex-direction:column; padding:70px;  }
.progress-container {
   width:100%; background-color:#ccc; height:6px; margin-bottom:25px; border-radius:4px; overflow:hidden; }
.progress-bar {
   height:100%; width:33.33%; background-color:#D4AF37; transition:width 0.4s ease; }
h2 {
   text-align:center; margin-bottom:30px; }
.form-container {
   background:white; padding:25px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1);
  align-items:center; }
label { display:block; margin-top:15px; }
input, select { width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:5px; }
.form-step { display:none; }
.form-step.active { display:block; }
.action-buttons { margin-top:20px; display:flex; justify-content:flex-end; gap:15px; }
.action-buttons button { border:none; border-radius:25px; background-color:#11113a; color:white; font-weight:bold; font-size:19px; padding:10px 25px; cursor:pointer; transition:background-color 0.3s ease; }
.action-buttons button:hover { background-color:#0b0b2a; }

/* Résultat */
.result-container { min-height:100vh; display:flex; flex-direction:column; justify-content:center; align-items:center; background:#f7f7fa; }
.result-title { text-align:center; color:#11113a; font-family:Montserrat, Arial, sans-serif; margin-top:40px; margin-bottom:0; font-size:2em; }
.estimation-result { width:60vw; max-width:900px; min-width:320px; background:#fff; border-radius:12px; box-shadow:0 2px 12px #0001; padding:52px 40px; font-family:Montserrat, Arial, sans-serif; color:#222; margin-top:24px; }
.estimation-result h3 { color:#D4AF37; margin-bottom:24px; text-align:center; }
.estimation-result p { margin:8px 0; font-size:1.08em; }
.center-btn { display:flex; justify-content:center; margin-top:24px; }
.center-btn a { color:#fff; background:#D4AF37; padding:10px 22px; border-radius:6px; text-decoration:none; font-weight:bold; transition:background 0.2s; }
.center-btn a:hover { background:#b89c2c; }
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
  gap: 25px;
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
    .main-content {
  display: flex;
  flex-direction: column;
  justify-content: center; /* vertical */
  align-items: center;     /* horizontal */
  min-height: calc(100vh - 80px); /* ajuster selon la hauteur du header */
  padding: 30px;
  box-sizing: border-box;
  width: 100%;
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

  

  <div class="header-icons">
    <a href="saved.php"><i class="fas fa-heart"></i></a>
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="profile.php"><i class="fas fa-user"></i></a>
    <?php else: ?>
      <a href="login.html"><i class="fas fa-user"></i></a>
    <?php endif; ?>
  </div>
</header>

<?php
if ($resultat) {
    
    echo $resultat;
} else {
    
?>
<div class="main-content">
<form action="estimate.php" method="post" class="form-section-wrapper">

    <div class="progress-container">
        <div class="progress-bar" id="progressBar"></div>
    </div>

    <h2>Estimez votre bien immobilier <i class="fas fa-calculator" style="margin-left:10px;color:#11113a;"></i></h2>

    <div class="form-container">
        <!-- Étape 1 -->
        <div class="form-step active" id="step1">
            <label>Type de bien :</label>
            <select name="type" required>
                <option>Appartement</option>
                <option>Maison</option>
                <option>Terrain</option>
                <option>Local commercial</option>
            </select>
            <label>Surface habitable (m²) :</label>
            <input type="number" name="surface" required>
            <label>Nombre de chambres :</label>
            <input type="number" name="chambres" required>
            <label>Nombre de salles de bain :</label>
            <input type="number" name="sdb" required>
        </div>
        <!-- Étape 2 -->
        <div class="form-step" id="step2">
            <label>Ville / Quartier :</label>
            <input type="text" name="ville" required>
            <label>Étage (si appartement) :</label>
            <input type="number" name="etage">
            <label>Ascenseur :</label>
            <select name="ascenseur"><option>Oui</option><option>Non</option></select>
            <label>État du bien :</label>
            <select name="etat" required><option>Neuf</option><option>Bon</option><option>À rénover</option></select>
        </div>
        <!-- Étape 3 -->
        <div class="form-step" id="step3">
            <label>Extérieur :</label>
            <select name="exterieur"><option>Jardin</option><option>Terrasse</option><option>Balcon</option><option>Aucun</option></select>
            <label>Place de parking :</label>
            <select name="parking"><option>Oui</option><option>Non</option></select>
            <label>Année de construction :</label>
            <input type="number" name="annee" required>
        </div>
    </div>

    <div class="action-buttons">
        <button type="button" id="btnRetour" onclick="goToStep(currentStep-1)" style="display:none;">Retour</button>
        <button type="button" id="btnContinuer" onclick="goToStep(currentStep+1)">Continuer</button>
        <button type="submit" id="btnEstimer" style="display:none;">Estimer</button>
    </div>
</form>
<?php } ?>
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
<script>
let currentStep = 1;
function updateProgressBar(step) {
    const progressBar = document.getElementById("progressBar");
    const progressValues = ["33.33%","66.66%","100%"];
    progressBar.style.width = progressValues[step-1];
}
function goToStep(step) {
    if(step<1||step>3) return;
    document.getElementById(`step${currentStep}`).classList.remove("active");
    document.getElementById(`step${step}`).classList.add("active");
    currentStep = step;
    updateProgressBar(step);
    document.getElementById("btnRetour").style.display = step===1?"none":"inline-block";
    document.getElementById("btnContinuer").style.display = step<3?"inline-block":"none";
    document.getElementById("btnEstimer").style.display = step===3?"inline-block":"none";
}
updateProgressBar(currentStep);
</script>

</body>
</html>
