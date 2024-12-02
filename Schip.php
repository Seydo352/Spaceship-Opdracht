<?php
// Definiëring van de Schip klasse, die de basis vormt voor andere schepen
class Schip
{
    // Eigenschappen die elk schip heeft
    public bool $isLevend; // Geeft aan of het schip nog leeft
    public int $levenspunten; // Het aantal levenspunten van het schip
    public int $munitie; // Hoeveel munitie het schip heeft

    // Constructor om een nieuw schip object te initialiseren
    public function __construct(
        $munitie = 100, // Standaardwaarde voor munitie
        $levenspunten = 100 // Standaardwaarde voor levenspunten
    ) {
        $this->munitie = $munitie; // Stel de munitie in
        $this->levenspunten = $levenspunten; // Stel de levenspunten in
        $this->isLevend = true; // Zet het schip standaard op levend
    }

    // Functie om damage te verwerken en levenspunten aan te passen
    public function damage($damage)
    {
        if ($this->levenspunten - $damage > 0) {
            // Verminder levenspunten als deze boven 0 blijven
            $this->levenspunten -= $damage;
        } else {
            // Zet isLevend op false als de levenspunten op of onder 0 zijn
            $this->isLevend = false;
        }
    }

    // Functie om de huidige status van het schip op te slaan in een bestand
    public function opslaan($bestandspad)
    {
        $gegevens = [
            'munitie' => $this->munitie, // Huidige hoeveelheid munitie
            'levenspunten' => $this->levenspunten, // Huidige levenspunten
            'isLevend' => $this->isLevend, // Status van leven
        ];

        // Serialize de gegevens en sla ze op in het opgegeven bestand
        file_put_contents($bestandspad, serialize($gegevens));
        echo "Schip opgeslagen.<br>"; // Geef een bevestiging
    }

    // Functie om een eerder opgeslagen status van het schip te laden
    public function laden($bestandspad)
    {
        if (file_exists($bestandspad)) {
            // Haal de gegevens uit het bestand en unserialize ze
            $gegevens = unserialize(file_get_contents($bestandspad));

            // Herstel de eigenschappen van het schip
            $this->isLevend = $gegevens['isLevend'];
            $this->levenspunten = $gegevens['levenspunten'];
            $this->munitie = $gegevens['munitie'];
            echo "Schip geladen.<br>"; // Geef een bevestiging
        } else {
            // Meld als er geen opgeslagen gegevens zijn gevonden
            echo "Geen opgeslagen gegevens gevonden!<br>";
        }
    }

    // Functie om het schip te resetten naar de standaardwaarden
    public function resetten()
    {
        $this->levenspunten = 100; // Zet levenspunten terug naar 100
        $this->munitie = 100; // Zet munitie terug naar 100
        $this->isLevend = true; // Zet de status van leven terug naar true
    }
}
?>
