<?php
require 'db.php';
session_start();
$user_id = $_SESSION['user_id'] ?? 0;

// Récupère les favoris de l'utilisateur connecté
$favoris = [];
if ($user_id) {
    $res = $conn->query("SELECT annonce_id FROM favoris WHERE user_id = $user_id");
    while ($row = $res->fetch_assoc()) {
        $favoris[] = $row['annonce_id'];
    }
}

$ville = $_GET['ville'] ?? '';
$prix_min = $_GET['prix_min'] ?? '';
$prix_max = $_GET['prix_max'] ?? '';
$surface_min = $_GET['surface_min'] ?? '';
$surface_max = $_GET['surface_max'] ?? '';

// Requête de base avec filtre sur statut "À vendre"
$sql = "
    SELECT a.*, u.fullname, u.photo 
    FROM annonces a 
    JOIN users u ON a.user_id = u.id 
    WHERE a.statut = 'À louer'
";

// Ajout des filtres dynamiquement
if (!empty($ville)) {
    $ville = $conn->real_escape_string($ville);
    $sql .= " AND a.ville = '$ville'";
}
if (!empty($prix_min)) {
    $sql .= " AND a.prix >= " . intval($prix_min);
}
if (!empty($prix_max)) {
    $sql .= " AND a.prix <= " . intval($prix_max);
}
if (!empty($surface_min)) {
    $sql .= " AND a.superficie >= " . intval($surface_min);
}
if (!empty($surface_max)) {
    $sql .= " AND a.superficie <= " . intval($surface_max);
}

// Tri par date décroissante
$sql .= " ORDER BY a.date_publication DESC";

// Exécution de la requête
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>rent</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
       body {
        font-family: 'Montserrat', sans-serif;
    
    }
    .top-bar {
  
  background-color:  #11113a;
  color: #D4AF37;
  padding: 36px;
  height: 45px;
   position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 25px;
}

.top-bar div {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  width: 60%;
  text-align: center;
  opacity: 0;
  transition: opacity 0.4s;
  margin: 0 auto;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
}
.top-bar div h4 {
  font-size: 1,4em;
  font-weight: 600;
  letter-spacing: 1px;
  margin: 0;
}
.top-bar div.active {
  opacity: 1;
}
main {
      padding: 30px;
    }

    h2 {
      margin-bottom: 20px;
    }

    .biens-container {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      
      margin: 0 auto;
      width: 100%;
      box-sizing: border-box;
    }

    @media (max-width: 900px) {
      .biens-container {
        grid-template-columns: repeat(2, 1fr);
      }
    }
    @media (max-width: 600px) {
      .biens-container {
        grid-template-columns: 1fr;
      }
    }

    .bien-card {
      background-color: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
      display: flex;
      flex-direction: column;
      height: 100%;
      min-width: 0;
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

    .btn {
      display: inline-block;
      padding: 8px 15px;
      background-color:rgb(21, 45, 72);
      color: white;
      border-radius: 5px;
      text-decoration: none;
      font-size: 14px;
      margin-top: 10px;
    }
    .date-annonce {
  color: #888;
  font-size: 13px;
  margin-bottom: 6px;
}
.icone{
    display:flex;
    justify-content:space-between;
    
}
.filtre-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 15px;
    justify-content: center;
}

.filtre-bar select,
.filtre-bar button {
    flex: 1 1 calc(25% - 10px); /* 4 éléments par ligne avec gap */
    padding: 12px 16px;
    font-size: 19px;
    border-radius: 10px;
    border: 1px solid #ccc;
    background-color: ;
  
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
    transition: 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight:bold;
}

.filtre-bar select:focus,
.filtre-bar button:hover {
    border-color:rgb(19, 2, 87);
    outline: none;
    
}
.modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: #fff;
    padding: 25px 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 400px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    position: relative;
    animation: fadeIn 0.3s ease-in-out;
}

.modal-content h3 {
    margin-top: 0;
    font-size: 20px;
    margin-bottom: 15px;
    color: #2e5885;
}

.modal-content input {
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 16px;
    box-sizing: border-box;
}

.modal-content button {
    width: 100%;
    padding: 10px;
    background-color: #2e5885;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
}

.modal-content button:hover {
    background-color: #244b72;
}

.modal-content .close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 22px;
    cursor: pointer;
    color: #aaa;
    transition: color 0.3s;
}

.modal-content .close:hover {
    color: #333;
}

@keyframes fadeIn {
    from {opacity: 0; transform: translateY(-20px);}
    to {opacity: 1; transform: translateY(0);}
}

.back-button {
    position: absolute;
    left: 30px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 25px;
    color: #D4AF37;
    text-decoration: none;
    z-index: 10;
}

.back-button:hover {
    color: white;
}





    </style>
</head>
<body>
    <header>
        <div class="top-bar">
             <a href="home.php" class="back-button" title="Retour à l'accueil">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
            <div id="bar1"><h4> "Trouvez la maison de vos rêves avec Immokeys" </h4></div>
            <div id="bar2"><h4> "Des biens immobiliers exclusifs à des prix compétitifs !"</h4></div>
            <div id="bar3"><h4> "Investissez aujourd’hui, profitez demain"</h4></div>
        </div>
    </header>
<section class="filtre-bar">
   
    <select id="filtre-ville">
  <option value="">Ville</option>
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



    <button type="button" id="btn-prix">
        <i class="fa-solid fa-dollar-sign" style="margin-right:8px;"></i> 
        <span id="label-prix">Prix</span>
    </button>
    <button type="button" id="btn-surface">
        <i class="fa-solid fa-ruler-combined" style="margin-right:8px;"></i> 
        <span id="label-surface">surface</span>
    </button>
    <button id="filtrer-btn" style="background-color:  #D4AF37; ">
        <i class="fa-solid fa-magnifying-glass"  style="margin-right:8px;"></i> Rechercher
    </button>
</section>


    <main>
        <div class="biens-container">
            <?php if ($result->num_rows === 0): ?>
<div style="
    height: 200px;             
    display: flex;             
    justify-content: center;
    align-items: center;       
    text-align: center;        
    color: #444;
    font-weight: bold;
    font-size: 22px;
    margin: 0 auto;
    width: 100%;
">
    Aucune annonce trouvée selon vos critères.
</div>

<?php endif; ?>
            <?php while($annonce = $result->fetch_assoc()): 
                $images = json_decode($annonce['images'], true);
                $img = isset($images[0]) ? $images[0] : 'default.jpg';
               $profilePhoto = !empty($annonce['photo']) ? 'profiles/' . htmlspecialchars($annonce['photo']) : 'profiles/default_profile.jpeg';
             $fullname = htmlspecialchars($annonce['fullname']);
            ?>
            <?php
    $est_enregistre = in_array($annonce['id'], $favoris);
    $style_bookmark = $est_enregistre ? 'color:#D4AF37' : '';
    $titre_bookmark = $est_enregistre ? 'Retirer des enregistrements' : 'Enregistrer';
?>

            <div class="bien-card">
                
                <div class="user-info" style="display:flex;align-items:center;gap:10px;padding:10px 15px 0 15px;margin-bottom:12px;">
                    <img src="<?php echo $profilePhoto; ?>" alt="Profil" style="width:38px;height:38px;border-radius:50%;object-fit:cover;">
                    <span style="font-weight:600;"><?php echo $fullname; ?></span>
            

            
    </div>
                <img src="<?php echo htmlspecialchars($img); ?>" alt="Photo">
                <div class="bien-info">
                    <h3><?php echo htmlspecialchars($annonce['titre']); ?></h3>
                    <p>Type : <?php echo htmlspecialchars($annonce['type']); ?> | Statut : <?php echo htmlspecialchars($annonce['statut']); ?></p>
                    <p>Surface : <?php echo $annonce['superficie']; ?> m² | <?php echo $annonce['pieces']; ?> pièces</p>
                    <p class="prix"><?php echo number_format($annonce['prix'], 0, ',', ' '); ?> MAD</p>
                    
                    <p class="date-annonce">Publié le : <?php echo date('d/m/Y', strtotime($annonce['date_publication'])); ?></p>
                    
                    <div class="icone">
                    <a href="details.php?id=<?php echo $annonce["id"]; ?>" class="btn voir-plus-btn">Voir plus..</a>
           <?php if ($user_id == 0): ?>
    <!-- Si pas connecté → redirection login -->
    <a href="login.html" class="save-btn" title="Connectez-vous pour enregistrer">
        <i class="far fa-bookmark" style="font-size:22px;"></i>
    </a>
<?php else: ?>
    <!-- Si connecté → toggle favoris -->
    <a href="save.php?annonce_id=<?= $annonce['id'] ?>" class="save-btn" title="<?= $titre_bookmark ?>">
        <i class="fa<?= $est_enregistre ? 's' : 'r' ?> fa-bookmark" style="font-size:22px; <?= $style_bookmark ?>"></i>
    </a>
<?php endif; ?>

            </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </main>
    <!-- Modal Prix -->
<div class="modal" id="modal-prix">
  <div class="modal-content">
    <span class="close" data-close="modal-prix">&times;</span>
    <h3>Filtrer par prix</h3>
    <input type="number" id="prix-min" placeholder="Prix min (MAD)">
    <input type="number" id="prix-max" placeholder="Prix max (MAD)">
    <button onclick="fermerModal('modal-prix')">Valider</button>
  </div>
</div>

<!-- Modal Surface -->
<div class="modal" id="modal-surface">
  <div class="modal-content">
    <span class="close" data-close="modal-surface">&times;</span>
    <h3>Filtrer par surface</h3>
    <input type="number" id="surface-min" placeholder="Surface min (m²)">
    <input type="number" id="surface-max" placeholder="Surface max (m²)">
    <button onclick="fermerModal('modal-surface')">Valider</button>
  </div>
</div>
 


    <footer>
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
   
    <script>
        let bars = [document.getElementById('bar1'), document.getElementById('bar2'), document.getElementById('bar3')];
let TopcurrentIndex = 0;

function barslider() {
    bars.forEach(bar => bar.classList.remove('active')); 
    bars[TopcurrentIndex].classList.add('active'); 
    TopcurrentIndex = (TopcurrentIndex + 1) % bars.length;
}

setInterval(barslider, 3000); 

barslider();


// Ouvrir les modales
document.getElementById('btn-prix').addEventListener('click', function() {
    ouvrirModal('modal-prix');
});
document.getElementById('btn-surface').addEventListener('click', function() {
    ouvrirModal('modal-surface');
});

// Fermer les modales
document.querySelectorAll('.close').forEach(function(btn) {
    btn.addEventListener('click', function() {
        let modalId = this.getAttribute('data-close');
        fermerModal(modalId);
    });
});

function ouvrirModal(id) {
    document.getElementById(id).style.display = 'flex';
}
function fermerModal(id) {
    document.getElementById(id).style.display = 'none';

    if (id === 'modal-prix') {
        let min = parseInt(document.getElementById('prix-min').value);
        let max = parseInt(document.getElementById('prix-max').value);

        if (!isNaN(min) && !isNaN(max)) {
            if (min > max) [min, max] = [max, min]; // inversion si nécessaire
            document.getElementById('label-prix').textContent = `Prix : ${formatNumber(min)} - ${formatNumber(max)}`;
        } else if (!isNaN(min)) {
            document.getElementById('label-prix').textContent = `Prix ≥ ${formatNumber(min)}`;
        } else if (!isNaN(max)) {
            document.getElementById('label-prix').textContent = `Prix ≤ ${formatNumber(max)}`;
        } else {
            document.getElementById('label-prix').textContent = `Prix`;
        }
    }

    if (id === 'modal-surface') {
        let min = parseInt(document.getElementById('surface-min').value);
        let max = parseInt(document.getElementById('surface-max').value);

        if (!isNaN(min) && !isNaN(max)) {
            if (min > max) [min, max] = [max, min]; // inversion si nécessaire
            document.getElementById('label-surface').textContent = `Surface : ${min} - ${max} m²`;
        } else if (!isNaN(min)) {
            document.getElementById('label-surface').textContent = `Surface ≥ ${min} m²`;
        } else if (!isNaN(max)) {
            document.getElementById('label-surface').textContent = `Surface ≤ ${max} m²`;
        } else {
            document.getElementById('label-surface').textContent = `Surface`;
        }
    }
}


// Format des nombres avec séparateur de milliers
function formatNumber(value) {
    return Number(value).toLocaleString('fr-FR');
}


// Fermer si on clique en dehors du contenu
document.querySelectorAll('.modal').forEach(function(modal) {
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});
document.getElementById('filtrer-btn').addEventListener('click', function () {
    const ville = document.getElementById('filtre-ville').value;
    const prixMin = document.getElementById('prix-min').value;
    const prixMax = document.getElementById('prix-max').value;
    const surfaceMin = document.getElementById('surface-min').value;
    const surfaceMax = document.getElementById('surface-max').value;

    // Construire les paramètres URL
    const params = new URLSearchParams();

    if (ville) params.append('ville', ville);
    if (prixMin) params.append('prix_min', prixMin);
    if (prixMax) params.append('prix_max', prixMax);
    if (surfaceMin) params.append('surface_min', surfaceMin);
    if (surfaceMax) params.append('surface_max', surfaceMax);

    // Rediriger vers buy.php avec les paramètres
    window.location.href = 'rent.php?' + params.toString();
});


    </script>
</body>
</html>