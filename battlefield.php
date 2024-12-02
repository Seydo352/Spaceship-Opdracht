<?php
// Foutmeldingen inschakelen voor debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Zorg dat de class bestanden 'SchipAlpha' en 'SchipBeta' worden geladen voordat de sessie begint
if (!file_exists('SchipAlpha.php') || !file_exists('SchipBeta.php') || !file_exists('Schip.php')) {
    die('Eén of meer benodigde bestanden ontbreken.');
}

include_once 'SchipAlpha.php';
include_once 'SchipBeta.php';

session_start();  // Start de sessie

// Controleer of de sessie correct is gestart
if (session_status() !== PHP_SESSION_ACTIVE) {
    die('Kon geen sessie starten.');
}

// Bestandsnamen voor opslaan en laden
$opslaanPadAlpha = 'schipalpha_opslaan.txt';
$opslaanPadBeta = 'schipbeta_opslaan.txt';

// Initialiseer schepen als ze niet in de sessie staan of als het gevecht voorbij is
function resetBattle()
{
    $_SESSION['schipalpha'] = new SchipAlpha(true, 100, 100);
    $_SESSION['schipbeta'] = new SchipBeta(true, 100, 100);
}

// Controleer of een reset nodig is
if (!isset($_SESSION['schipalpha']) || !isset($_SESSION['schipbeta'])) {
    resetBattle();
}

// Haal beide schepen uit de sessie
$schipAlpha = $_SESSION['schipalpha'];
$schipBeta = $_SESSION['schipbeta'];

// Verwerk acties (schieten, opslaan, laden, resetten)
if (isset($_POST['actie'])) {
    if ($schipAlpha->isLevend && $schipBeta->isLevend) {
        if ($_POST['actie'] == 'alpha_schiet') {
            // SchipAlpha schiet op SchipBeta
            $damage = $schipAlpha->schietKogels();
            $schipBeta->damage($damage);

        } elseif ($_POST['actie'] == 'beta_schiet') {
            // SchipBeta schiet op SchipAlpha
            $damage = $schipBeta->schietKogels();
            $schipAlpha->damage($damage);

        } elseif ($_POST['actie'] == 'alpha_straal') {
            // SchipAlpha gebruikt de straal
            $damage = $schipAlpha->gebruikStraal();
            $schipBeta->damage($damage);

        } elseif ($_POST['actie'] == 'beta_straal') {
            // SchipBeta gebruikt de straal
            $damage = $schipBeta->gebruikStraal();
            $schipAlpha->damage($damage);

        }
    }

    if ($_POST['actie'] == 'opslaan') {
        // Sla de status van beide schepen op
        $schipAlpha->opslaan($opslaanPadAlpha);
        $schipBeta->opslaan($opslaanPadBeta);
        echo "Voortgang opgeslagen!<br>";

    } elseif ($_POST['actie'] == 'laden') {
        // Laad de status van beide schepen
        if (file_exists($opslaanPadAlpha) && file_exists($opslaanPadBeta)) {
            $schipAlpha->laden($opslaanPadAlpha);
            $schipBeta->laden($opslaanPadBeta);
            echo "Voortgang geladen!<br>";
        } else {
            echo "Geen opgeslagen voortgang gevonden!<br>";
        }
    }
}

// Controleer of het spel voorbij is en reset automatisch
if (!$schipAlpha->isLevend || !$schipBeta->isLevend) {
    resetBattle();
    header("Location: battlefield.php");
    exit();
}

// Werk de sessie bij met de nieuwe status
$_SESSION['schipalpha'] = $schipAlpha;
$_SESSION['schipbeta'] = $schipBeta;
?>


<!doctype html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SchipAlpha vs SchipBeta</title>
    <style>
        /* Vernieuwde CSS */
        body {
            margin: 0;
            padding: 0;
            background: radial-gradient(circle, #ff7b54, #ff6b6b);
            font-family: 'Verdana', sans-serif;
            color: #fff;
            text-align: center;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        h2 {
            font-size: 48px;
            margin-bottom: 15px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }

        p {
            font-size: 18px;
            margin: 10px 0;
        }

        hr {
            width: 90%;
            border: 1px solid rgba(255, 255, 255, 0.3);
            margin: 20px auto;
        }

        form {
            margin: 15px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        button {
            background-color: #141e30;
            border: 2px solid #4caf50;
            border-radius: 30px;
            color: #fff;
            padding: 12px 30px;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 10px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.3);
        }

        button:hover {
            background-color: #1e293b;
            border-color: #80ffdb;
            transform: scale(1.05);
        }

        button:disabled {
            background-color: #555;
            cursor: not-allowed;
            border-color: #999;
        }

        .container {
            background: rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.4);
            width: 90%;
            max-width: 600px;
        }

        h3 {
            font-size: 28px;
            color: #ffcbcb;
            margin-top: 20px;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.5);
        }

        p.defeated {
            font-size: 20px;
            color: #ff1744;
            font-weight: bold;
            margin: 10px 0;
        }

        .status {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .status p {
            font-size: 16px;
            color: #f7f7f7;
            margin: 5px 0;
        }

        .status strong {
            font-size: 18px;
            color: #80ffdb;
        }
    </style>
</head>

<body>
    <h2>SchipAlpha vs SchipBeta</h2>
    <div class="container">
        <div class="status">
            <p><strong>Status SchipAlpha:</strong></p>
            <p>Levenspunten: <?= $schipAlpha->levenspunten ?></p>
            <p>Kogels: <?= $schipAlpha->kogels ?></p>
            <p>Straal: <?= $schipAlpha->straal ?></p>
            <p>Levend: <?= $schipAlpha->isLevend ? 'Ja' : 'Nee' ?></p>
        </div>
        <hr>
        <div class="status">
            <p><strong>Status SchipBeta:</strong></p>
            <p>Levenspunten: <?= $schipBeta->levenspunten ?></p>
            <p>Kogels: <?= $schipBeta->kogels ?></p>
            <p>Straal: <?= $schipBeta->straal ?></p>
            <p>Levend: <?= $schipBeta->isLevend ? 'Ja' : 'Nee' ?></p>
        </div>
    </div>

    <form method="post" action="">
        <button type="submit" name="actie" value="alpha_schiet">SchipAlpha Schiet</button>
        <button type="submit" name="actie" value="beta_schiet">SchipBeta Schiet</button>
        <br>
        <button type="submit" name="actie" value="alpha_straal">SchipAlpha Gebruikt Straal</button>
        <button type="submit" name="actie" value="beta_straal">SchipBeta Gebruikt Straal</button>
    </form>

    <form method="post" action="">
        <button type="submit" name="actie" value="opslaan">Opslaan</button>
        <button type="submit" name="actie" value="laden">Laden</button>
    </form>
</body>

</html>
