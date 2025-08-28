<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Carte des prix au Maroc</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


  <style>
    body {
      margin: 0;
      font-family: 'Montserrat', sans-serif;
      background: #f9f9f9;
    }

    /* ---------- Header ---------- */
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

    /* ---------- Container Layout ---------- */
    .container {
      display: flex;
      height: calc(100vh - 70px); /* header height = 70px */
    }

    /* Left side: average section */
    .average {
      width: 35%;
      background-color: white;
      padding: 40px;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
      overflow-y: auto;
    }

    .average h2 {
      color: #D4AF37;
      margin-bottom: 20px;
    }

    .average p {
      font-size: 16px;
      line-height: 1.6;
    }

    /* Right side: map */
    .map-container {
      width: 65%;
      height: 100%;
    }

    #map {
      width: 100%;
      height: 100%;
    }
    .menu-items.active {
  border-bottom: 6px solid white;
  padding-bottom: 10px;
}
.left-panel {
  width: 50%;
  padding: 10px;
  font-family: 'Montserrat', sans-serif;
  color: white;
}

#city-select {
  width: 100%;
  padding: 12px;
  font-size: 16px;
  margin-top: 20px;
  border: 2px solid black; /* Bordure noire */
  border-radius: 6px;
  background-color: white;
  color: black;
  padding-left: 15px;
}

.price-display {
  margin-top: 30px;
  font-size: 18px;
  background-color: rgba(255, 255, 255, 0.1);
  padding: 15px;
  border-radius: 8px;
  align-items: center;
  text-align: center;
  line-height: 2.2;
}
#price-info h3,
#price-info h4,
#price-info h2,
#price-info p {
  margin: 5px 0;
  color: #11113a;
}

.colorbar-container {
  margin-top: 25px;
  width: 100%;
  max-width: 350px;
  margin: 25px auto 0 auto; /* marge en haut + centrage horizontal automatique */
   width: 90%; /* largeur responsive */
}

.colorbar-gradient {
  height: 20px;
  border-radius: 10px;
  background: linear-gradient(to right, green, yellow, red);
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
  
}

.colorbar-labels {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
  color: #11113a;
  font-weight: 600;
  margin-top: 5px;
}
.button-container {
  text-align: center;
  margin-top: 25px;
}

.egg-button {
  display: inline-block;
  background-color: #11113a;
  color: white;
  padding: 15px 30px;
  font-size: 16px;
  font-weight: 600;
  text-decoration: none;
  border: none;
  border-radius: 999px; /* forme ovale/œuf */
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease;
}

.egg-button:hover {
  background-color:#11113a;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
  transform: translateY(-2px);
}

 
.container-graphe{

  width:90%;
  display:flex;
  justify-content: space-between; 
   margin: 50px auto; /* centrer horizontalement et marges verticales */
 
  margin-right: 40px;
}
.graphecircu{
  width:600;
  box-shadow: 0 4px 30px rgba(0,0,0,0.3);
  height:65vh;
  padding-left:20px;
  margin-top: 45px;
  
  
}
.chart-container {
    width: 80%;
    background: white;
    box-shadow: 0 4px 30px rgba(0,0,0,0.3);
   height:75vh;
    margin-left: 120px;
    padding-left:40px;
        
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
    <a href="./discover.php" class="menu-items active">prix de vente au m² </a>
    <a href="./estimate.php" class="menu-items">Estimation immobilière</a>
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


<div class="container">
  <div class="left-panel">
  
  <select id="city-select">
  <option value="Maroc" selected>Maroc</option>
  <option value="Agadir">Agadir</option>
  <option value="Beni_Mellal">Beni Mellal</option>
  <option value="Casablanca">Casablanca</option>
  <option value="Dakhla">Dakhla</option>
  <option value="El_Jadida">El Jadida</option>
  <option value="Errachidia">Errachidia</option>
  <option value="Essaouira">Essaouira</option>
  <option value="Fès">Fès</option>
  <option value="Kénitra">Kénitra</option>
  <option value="Khouribga">Khouribga</option>
  <option value="Laâyoune">Laâyoune</option>
  <option value="Larache">Larache</option>
  <option value="Marrakech">Marrakech</option>
  <option value="Meknès">Meknès</option>
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


  <div id="price-info" class="price-display" style="display: none;">
  <h3>Real estate prices per m²</h3>
  <h2 id="city-name">Casablanca</h2>
  <p style="font-weight:100; color:grey;">Immokeys data August 2025</p>
  <h4 style="align-items:center;">Average price per m²</h4>
  <p id="price-value" style="font-size: 24px; font-weight: bold; margin-top: 10px;color:#D4AF37;">12,000 MAD</p>
  <div class="colorbar-container" aria-label="Barre des prix immobiliers">
  <div class="colorbar-gradient"></div>
  <div class="colorbar-labels" style="color:grey;">
    <span>Low: 1,733 MAD</span>
    <span>High: 4,980 MAD</span>
  </div>
</div>
<div class="button-container">
  <a href="estimate.html" class="egg-button">Estimer mon bien</a>
</div>
<div class="text-container">
  <p  class="text" style="padding:30px;background-color:rgba(135, 206, 235, 0.2); ">🤔 N’oubliez pas, le prix dépend aussi de son état ! </a>
</div>
</div>

</div>


  <div class="map-container">
    <div id="map"></div>
  </div>
</div>
<div class="container-graphe">
  <div class="graphecircu">
    <h2 style="margin-top:0px;margin-bottom: 30px; padding:25px;">Répartition des types de biens</h2>

<canvas id="pieChart"  ></canvas>
    </div>
<div class="chart-container">
  <h2 style="margin-top:0px;margin-bottom: 10px; padding:20px;">Evolution des prix immobiliers au maroc</h2>
    <canvas id="priceChart"></canvas>
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
   <script> 
const select = document.getElementById("city-select");
const display = document.getElementById("price-info");
const cityName = document.getElementById("city-name");
const priceValue = document.getElementById("price-value");
const colorbarLabels = document.querySelectorAll(".colorbar-labels span");

const cityPrices = {
  Maroc: { low: 3000, high: 15000, avg: 9000 },
  Casablanca: { low: 8000, high: 20000, avg: 14000 },
  Rabat: { low: 7000, high: 18000, avg: 12500 },
  Marrakech: { low: 6000, high: 15000, avg: 10500 },
  Tanger: { low: 5000, high: 13000, avg: 9000 },
  Fès: { low: 4000, high: 10000, avg: 7000 },
  Meknès: { low: 3500, high: 9000, avg: 6250 },
  Agadir: { low: 5000, high: 13000, avg: 9000 },
  Oujda: { low: 3000, high: 7000, avg: 5000 },
  Nador: { low: 3500, high: 8000, avg: 5750 },
  Tétouan: { low: 4000, high: 8500, avg: 6250 },
  El_Jadida: { low: 4000, high: 9000, avg: 6500 },
  Kénitra: { low: 4500, high: 10000, avg: 7250 },
  Mohammedia: { low: 6000, high: 13000, avg: 9500 },
  Settat: { low: 3000, high: 8000, avg: 5500 },
  Khouribga: { low: 2500, high: 7000, avg: 4750 },
  Safi: { low: 3500, high: 8500, avg: 6000 },
  Beni_Mellal: { low: 3000, high: 7500, avg: 5250 },
  Errachidia: { low: 2500, high: 6500, avg: 4500 },
  Laâyoune: { low: 3000, high: 7000, avg: 5000 },
  Dakhla: { low: 2800, high: 6500, avg: 4650 },
  Essaouira: { low: 4000, high: 9500, avg: 6750 },
  Ouarzazate: { low: 3000, high: 7000, avg: 5000 },
  Taza: { low: 2500, high: 6000, avg: 4250 },
  Larache: { low: 3000, high: 7500, avg: 5250 }
};

function updateDisplay(city) {
  if (cityPrices[city]) {
    const prices = cityPrices[city];
    cityName.textContent = city;
    
    display.style.display = "block";
    colorbarLabels[0].textContent = `Low: ${prices.low.toLocaleString()} MAD`;
    colorbarLabels[1].textContent = `High: ${prices.high.toLocaleString()} MAD`;
  }
}

// Affichage initial (Maroc par défaut)
updateDisplay("Maroc");

// Événement lors du changement de ville
select.addEventListener("change", function () {
  updateDisplay(this.value);
});

select.addEventListener("change", function () {
  const city = this.value;
  if (cityPrices[city]) {
    const prices = cityPrices[city];
    cityName.textContent = city;
    priceValue.textContent = prices.avg.toLocaleString() + " MAD";
    display.style.display = "block";
    colorbarLabels[0].textContent = `Low: ${prices.low.toLocaleString()} MAD`;
    
    colorbarLabels[1].textContent = `High: ${prices.high.toLocaleString()} MAD`;
  } else {
    display.style.display = "none";
  }
});
const carte = L.map('map').setView([31.5, -7.09], 6); // Vue générale du Maroc

  // Ajout fond de carte
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(carte);

const prixParRegion = {
  "Chaouia-Ouardigha": 5000,
  "Doukkala-Abda": 5000,
  "Fès-Boulemane": 7800,
  "Gharb-Chrarda-BéniHssen": 5500,
  "GrandCasablanca": 12000,
  "Guelmim-Es-Semara": 4500,
  "Laâyoune-Boujdour-SakiaElH": 3000,
  "Marrakech-Tensift-AlHaouz": 7500,
  "Meknès-Tafilalet": 6800,
  "Oriental": 5200,
  "Rabat-Salé-Zemmour-Zaer": 11000,
  "Souss-Massa-Draâ": 8000,
  "Tadla-Azilal": 5700,
  "Tanger-Tétouan":9100,
  "Taza-AlHoceima-Taounate": 5400
};
function getCouleur(prix) {
  return prix > 10000 ? "#D73027" :
         prix > 9000  ? "#FC8D59" :
         prix > 7000  ? "#FEE08B" :
         prix > 5000  ? "#D9EF8B" :
                        "#91CF60";



}



  // Fonction de style pour chaque région
  function styleRegion(feature) {
    const region = feature.properties.NAME_1;
    const prix = prixParRegion[region] || 0;
    return {
      fillColor: getCouleur(prix),
      weight: 2,
      opacity: 1,
      color: 'white',
      fillOpacity: 0.8
    };
  }

  // Affichage info-bulle au survol
  function surChaqueFeature(feature, layer) {
  const region = feature.properties.NAME_1;
  console.log("Nom détecté :", region); // ✅ Affiche les noms dans la console

  const prix = prixParRegion[region] || "N/A";

  // Tu peux aussi voir s'il y a une erreur ici
  if (prix === "N/A") {
    console.warn("Aucun prix trouvé pour :", region);
  }

  layer.bindTooltip(`<strong>${region}</strong><br>Prix moyen : ${prix} MAD/m²`);
}


  // Charger ton fichier GeoJSON
  fetch('/ImmoKeys/gadm41_MAR_1.json/gadm41_MAR_1.json')
  .then(res => res.json())
  .then(geojson => {
    geojson.features.forEach(feature => {
      console.log(feature.properties.NAME_1);
    });
    L.geoJson(geojson, {
      style: styleRegion,
      onEachFeature: surChaqueFeature
    }).addTo(carte);
  });

  //graphe 

  const ctx = document.getElementById('priceChart').getContext('2d');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['2020', '2021', '2022', '2023', '2024', '2025'],
        datasets: [{
            label: 'Indice des prix immobiliers',
            data: [-7.12, 2.25, 2.60, 0.80, 0.00, 0.09],
            borderColor: '#11113a',
            backgroundColor: '#11113a',
            tension: 0.3,
            fill: false,
            pointBackgroundColor: '#11113a',
            pointRadius: 6,
            pointHoverRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: false,
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: {
                grid: { color: 'rgba(0,0,0,0.05)' }
            }
        }
    }
});


//graphe cerculaire 


new Chart(document.getElementById('pieChart'), {
    type: 'pie',
    data: {
        labels: ['Appartements', 'Maisons', 'Autres'],
        datasets: [{
            data: [45, 35, 20], 
            backgroundColor: ['#D73027', '#91CF60', '#FEE08B'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

</script>

</body>
</html>