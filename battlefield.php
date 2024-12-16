<?php
// Inclusie van de benodigde klassen
include_once 'SchipAlpha.php'; // SchipAlpha klasse
include_once 'SchipBeta.php';  // SchipBeta klasse
include_once  'Schip.php';

session_start(); // Start de sessie

// Functie om de vloten te initialiseren
function initializeFleets() {
    return [
        'fleetAlpha' => [ // Alpha vloot bestaat uit 2 schepen
            new SchipAlpha(true, 100, 100),
            new SchipAlpha(true, 100, 100)
        ],
        'fleetBeta' => [ // Beta vloot bestaat uit 2 schepen
            new SchipBeta(true, 100, 100),
            new SchipBeta(true, 100, 100)
        ]
    ];
}

// Controleer of er al een vloot in de sessie bestaat, anders maak een nieuwe
if (!isset($_SESSION['fleets'])) {
    $_SESSION['fleets'] = initializeFleets(); // Zet nieuwe vloten in de sessie
}

// Haal de vloten op uit de sessie
$fleets = $_SESSION['fleets'];

// Functie om aan te vallen, alleen de tegenstander wordt beschadigd
function attack($attackerFleet, &$defenderFleet) {
    foreach ($attackerFleet as $attacker) { // Doorloop elk schip in de aanvallende vloot
        if ($attacker->isLevend) { // Controleer of het schip nog leeft
            foreach ($defenderFleet as &$defender) { // Zoek een levend schip in de verdedigende vloot
                if ($defender->isLevend) { 
                    $damage = 0; // Start met geen schade

                    // Gebruik kogels als het schip deze nog heeft
                    if ($attacker->kogels > 0) {
                        $damage += $attacker->schietKogels(); // Voeg kogelschade toe
                    }

                    // Gebruik de straal als deze beschikbaar is
                    if ($attacker->straal > 0) {
                        $damage += $attacker->gebruikStraal(); // Voeg straalschade toe
                    }

                    // Breng schade aan het verdedigende schip toe
                    $defender->damage($damage);

                    // Controleer of het schip nu "dood" is
                    if ($defender->levenspunten <= 0) {
                        $defender->levenspunten = 0; // Zorg ervoor dat de HP niet negatief wordt
                        $defender->isLevend = false; // Markeer het schip als dood
                    }

                    break; // Stop na het beschadigen van één verdediger
                }
            }
        }
    }
}

// Verwerk de actie op basis van de knop die is ingedrukt
if (isset($_POST['actie'])) {
    if ($_POST['actie'] == 'alpha_attack') {
        attack($fleets['fleetAlpha'], $fleets['fleetBeta']); // Alpha valt Beta aan
    } elseif ($_POST['actie'] == 'beta_attack') {
        attack($fleets['fleetBeta'], $fleets['fleetAlpha']); // Beta valt Alpha aan
    } elseif ($_POST['actie'] == 'reset') {
        $_SESSION['fleets'] = initializeFleets(); // Reset de vloten naar de originele status
        $fleets = $_SESSION['fleets']; // Werk de sessie bij
    }
}

// Controleer of schepen "dood" zijn en markeer deze correct
foreach ($fleets as &$fleet) {
    foreach ($fleet as &$ship) {
        if ($ship->levenspunten <= 0) {
            $ship->isLevend = false; // Markeer schepen met 0 HP als dood
        }
    }
}

// Sla de bijgewerkte vlootstatus terug op in de sessie
$_SESSION['fleets'] = $fleets;

// Bereken de totale levenspunten van elke vloot voor de grafiek
$alphaHealth = array_sum(array_map(fn($ship) => max(0, $ship->levenspunten), $fleets['fleetAlpha']));
$betaHealth = array_sum(array_map(fn($ship) => max(0, $ship->levenspunten), $fleets['fleetBeta']));
?>

<!doctype html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Vlootgevecht</title>
    <style>
        /* Algemene stijl voor de pagina */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: radial-gradient(circle, #2d89ef, #1e1e1e);
            color: #fff;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        h2 {
            font-size: 36px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }

        .fleet-container {
            display: flex;
            justify-content: center;
            gap: 50px;
            margin-top: 20px;
        }

        .fleet {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            width: 300px;
            text-align: left;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .fleet h3 {
            font-size: 24px;
            color: #00d4ff;
            text-align: center;
        }

        .ship-status {
            margin: 10px 0;
        }

        button {
            background: #00d4ff;
            border: none;
            color: #000;
            padding: 10px 20px;
            font-size: 16px;
            margin: 10px;
            cursor: pointer;
            border-radius: 5px;
            transition: background 0.3s;
        }

        button:hover {
            background: #009fbb;
        }

        canvas {
            margin-top: 30px;
            background: #1e1e1e;
            border: 1px solid #00d4ff;
            border-radius: 5px;
            padding: 10px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h2>Vlootgevecht: 2v2</h2>
    <div class="fleet-container">
        <div class="fleet">
            <h3>Vloot Alpha</h3>
            <?php foreach ($fleets['fleetAlpha'] as $index => $ship): ?>
                <div class="ship-status">
                    <p><strong>Schip <?= $index + 1 ?>:</strong></p>
                    <p>HP: <?= max(0, $ship->levenspunten) ?></p>
                    <p>Status: <?= $ship->isLevend ? "Levend" : "Gehavend" ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="fleet">
            <h3>Vloot Beta</h3>
            <?php foreach ($fleets['fleetBeta'] as $index => $ship): ?>
                <div class="ship-status">
                    <p><strong>Schip <?= $index + 1 ?>:</strong></p>
                    <p>HP: <?= max(0, $ship->levenspunten) ?></p>
                    <p>Status: <?= $ship->isLevend ? "Levend" : "Gehavend" ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <form method="post" action="">
        <button type="submit" name="actie" value="alpha_attack">Alpha valt aan</button>
        <button type="submit" name="actie" value="beta_attack">Beta valt aan</button>
        <button type="submit" name="actie" value="reset">Reset gevecht</button>
    </form>

    <canvas id="battleChart" width="400" height="200"></canvas>

    <script>
        // JavaScript voor de grafiek met Chart.js
        const ctx = document.getElementById('battleChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar', // Grafiektype: staafdiagram
            data: {
                labels: ['Vloot Alpha', 'Vloot Beta'], // Labels voor de vloten
                datasets: [{
                    label: 'Totale levenspunten',
                    data: [<?= $alphaHealth ?>, <?= $betaHealth ?>], // Gegevens voor de grafiek
                    backgroundColor: ['rgba(54, 162, 235, 0.5)', 'rgba(255, 99, 132, 0.5)'], // Kleuren
                    borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 99, 132, 1)'], // Randkleuren
                    borderWidth: 2 // Dikte van de randen
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true // Zorg dat de Y-as begint bij 0
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#fff', // Kleur van de labels
                            font: { size: 16 } // Grootte van de labels
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
