<?php
require_once 'config.php';
require_once 'functions.php';

connectToDB($conn);

// Überprüfen ob ein GetRequest gesendet wurde
if (isGetRequest()) {
    // Übergebene Daten aus GET-Request auslesen und in Array speichern
    $formData = [
        'kunde_id' => formFieldValueGet('kunde', ''),
    ];
}

// SQL Statement
$query = "SELECT * FROM kunde "
        . " WHERE kunde.kunde_id = ? ";

// Prepared Statement erstellen
$stmt = $conn->prepare($query);

$stmt->bind_param('i', $formData['kunde_id']);

// Statement ausführen
$stmt->execute();

// Ergebnis des Statements in resultat speichern
$result = $stmt->get_result();

// leeren Array erzeugen
$kunde = null;

// Überprüfen vom Resultat (ob es erfolgreich war und ob die Anzahl der zurückgelieferten Zeilen == 1 ist)
if ($result && $result->num_rows == 1) {
    // Datensatz aus Ergebnis auslesen
    $kunde = $result->fetch_object();
} else {
    die('Der Kunde wurde nicht gefunden oder ungültige Eingabe! ');
}

// SQL Statement 2
$query = "SELECT SUM(verbrauch.menge) as menge, SUM(verbrauch.preis) as preis FROM verbrauch "
        . " WHERE kunde_id = ? ";

// Prepared Statement erstellen
$stmt = $conn->prepare($query);

$stmt->bind_param('i', $formData['kunde_id']);

// Statement ausführen
$stmt->execute();

// Ergebnis des Statements in resultat speichern
$result = $stmt->get_result();

// leeren Array erzeugen
$menge = [];
$preis = [];

// Anzahl der Reihen im Resultat überprüfen
if ($result && $result->num_rows > 0) {
    // Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    while ($row = $result->fetch_object()) {
        $menge = $row->menge;
        $preis = $row->preis;
    }
}

closeDB($conn);
?>



<html>
    
    <head>
        <meta charset="UTF-8">
        <title>Tankstellenverwaltung - Kundensuche</title>
    </head>
    
        <body>
            <h1>Suchergebnis</h1>
            <div>
                <table>
                    <tbody>
                        <tr>
                            <th>Kundennummer:</th>
                            <td><?= $kunde->kunde_id ?></td>
                        </tr>
                        <tr>
                            <th>Vorname:</th>
                            <td><?= $kunde->vorname ?></td>
                        </tr>
                        <tr>
                            <th>Nachname:</th>
                            <td><?= $kunde->nachname ?></td>
                        </tr>
                        <tr>
                            <th>Strasse:</th>
                            <td><?= $kunde->strasse ?></td>
                        </tr>
                        <tr>
                            <th>PLZ:</th>
                            <td><?= $kunde->plz ?></td>
                        </tr>
                        <tr>
                            <th>Ort:</th>
                            <td><?= $kunde->ort ?></td>
                        </tr>
                        <tr>
                            <th>Geburtsdatum:</th>
                            <td><?= $kunde->geburtsdatum ?></td>
                        </tr>
                        <tr>
                            <th><br></th>
                            <td><br></td>
                        </tr>
                        <tr>
                            <th>Treibstoffverbrauch:</th>
                            <td><?= $menge ?></td>
                        </tr>
                        <tr>
                            <th>Gesamtpreis:</th>
                            <td><?= $preis ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </body>
        
        <br>
        <br>
        
        <nav><a href="suche.php">Zurück zur Kundensuche</a></nav>
        
</html>