<?php

// Include het 'Schip.php' bestand om toegang te krijgen tot de basisfunctionaliteit van de Schip klasse
include_once 'Schip.php';

// Definieer de SchipBeta klasse die eigenschappen en methoden overneemt van de Schip klasse
class SchipBeta extends Schip
{
    // Declareer eigenschappen voor kogels en straal
    public int $kogels;
    public int $straal;

    // Constructor: voert code uit bij het aanmaken van een SchipBeta object
    public function __construct($isLevend, $levenspunten, $munitie)
    {
        // Roept de constructor van de ouderklasse (Schip) aan
        parent::__construct($munitie, $levenspunten);

        // Stelt standaardwaarden in voor kogels en straal
        $this->kogels = 20;
        $this->straal = 2;
    }

    // Methode om kogels af te vuren en damage aan te richten
    public function schietKogels(): int
    {
        $damage = 15; // Hoeveel damage een kogel aanricht
        $gebruik = 1; // Hoeveel kogels per keer worden gebruikt

        // Controleert of er genoeg kogels zijn
        if ($this->kogels >= $gebruik) {
            $this->kogels -= $gebruik; // Vermindert het aantal kogels
            return $damage; // Geeft de damage terug
        } else {
            echo "Geen kogels meer!<br>"; // Geeft een melding als er geen kogels zijn
            return 0; // Geen damage als er niet geschoten kan worden
        }
    }

    // Methode om een krachtige straal te gebruiken
    public function gebruikStraal(): int
    {
        $gebruik = 1; // Hoeveel straalvoorraad er per keer wordt gebruikt
        $damage = 40; // Hoeveel damage de straal aanricht

        // Controleert of er genoeg straalvoorraad is
        if ($this->straal >= $gebruik) {
            $this->straal -= $gebruik; // Vermindert de straalvoorraad
            return $damage; // Geeft de damage terug
        } else {
            echo "Geen straal meer!<br>"; // Geeft een melding als er geen straalvoorraad is
            return 0; // Geen damage als de straal niet kan worden gebruikt
        }
    }
}
?>
