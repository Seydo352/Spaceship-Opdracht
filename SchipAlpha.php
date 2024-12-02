<?php

// Includen van het 'Schip.php' bestand om toegang te krijgen tot de Schip klasse
include_once 'Schip.php';

// Definieert de SchipAlpha klasse die eigenschappen en methoden van de Schip klasse overneemt
class SchipAlpha extends Schip
{
    // Declareren van eigenschappen voor kogels en straal
    public int $kogels;
    public int $straal;

    // Constructor: wordt uitgevoerd wanneer een object van deze klasse wordt gemaakt
    public function __construct($isLevend, $levenspunten, $munitie)
    {
        // Roept de constructor van de ouderklasse (Schip) aan
        parent::__construct($munitie, $levenspunten);

        // Stelt standaardwaarden in voor kogels en straal
        $this->kogels = 10;
        $this->straal = 1;
    }

    // Methode om kogels af te vuren en damage aan te richten
    public function schietKogels(): int
    {
        $damage = 10; // Hoeveel damage een kogel aanricht
        $gebruik = 1; // Hoeveel kogels per keer worden gebruikt

        // Controleert of er genoeg kogels zijn om te schieten
        if ($this->kogels >= $gebruik) {
            $this->kogels -= $gebruik; // Vermindert het aantal kogels
            return $damage; // Geeft de damage terug
        } else {
            echo "Geen kogels meer<br>"; // Geeft een melding als er geen kogels meer zijn
            return 0; // Geen damage als er niet geschoten kan worden
        }
    }

    // Methode om een krachtige straal te gebruiken
    public function gebruikStraal(): int
    {
        $gebruik = 1; // Hoeveel stralen per keer worden gebruikt
        $damage = 50; // Hoeveel damage de straal aanricht

        // Controleert of er genoeg straalvoorraad is om te gebruiken
        if ($this->straal >= $gebruik) {
            $this->straal -= $gebruik; // Vermindert de straalvoorraad
            return $damage; // Geeft de damage terug
        } else {
            echo "Geen straal meer<br>"; // Geeft een melding als er geen straal meer is
            return 0; // Geen damage als de straal niet gebruikt kan worden
        }
    }
}
?>
