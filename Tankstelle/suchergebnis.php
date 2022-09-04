<?php
require_once 'config.php';
require_once 'functions.php';

$formData = [];

// DB Connection aufbauen
$conn = connectToDb();
if (!$conn) {
    die('Es konnte keine DB-Verbindung hergestellt werden ' . $conn->connect_error);
}

if (isGetRequest()) {
// Übergebene Daten aus POST-Request auslesen und in Array speichern
    $formData = [
        'kunde_id' => formFieldValueGET('kunde_id', '')
    ];
}

//SQL Statement 
$query = "SELECT verbrauch.*, kunde.*, SUM(verbrauch.menge) AS menge, "
        . "SUM(verbrauch.preis) AS preis, treibstoff.* FROM verbrauch "
        . "JOIN kunde ON verbrauch.kunde_id = kunde.kunde_id "
        . "JOIN treibstoff ON verbrauch.treibstoff_id = treibstoff.treibstoff_id "
        . "WHERE verbrauch.kunde_id = ? "; //LIKE nur bei STRINGS

//echo var_dump($query) . '<br><br>';

$stmt = $conn->prepare($query);

// Parameter binden
$stmt->bind_param('i', $formData['kunde_id']);

// Statement ausführen
$stmt->execute();

// Ergebnis des Statements in resultat speichern
$result = $stmt->get_result();

$kunde = [];

// Anzahl der Reihen im Resultat überprüfen
if ($result && $result->num_rows == 1) {
// Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    $kunde = $result->fetch_object();
}

if ($kunde && $kunde->kunde_id == '') {
    die('Der Kunde wurde nicht gefunden oder ungültige Eingabe!');
}

// DB Verbindung beenden
$conn->close();
?>

<html>
    
    <head>
        <title>Tankstellenverwaltung - Kundensuche</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    
    <body>
        <h1>Suchergebnis</h1>

        <div>
            <table>
                <tbody>
                    <tr>
                        <th>Kundennummer:</th>
                        <td><?php echo $kunde->kunde_id ?></td>
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
                        <th>Straße:</th>
                        <td><?= $kunde->straße ?></td>
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
                        <td><br></td>
                        <td><br></td>
                    </tr>
                    <tr>
                        <th>Treibstoffverbrauch:</th>
                        <td><?= number_format($kunde->menge, 15, '.', ',') ?> Liter.</td>
                    </tr>
                    <tr>
                        <th>Gesamtpreis:</th>
                        <td><?= number_format($kunde->preis, 15, '.', ',') ?> €.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
    </body>
    
</html>
