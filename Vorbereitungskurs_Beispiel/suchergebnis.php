<?php
require_once 'config.php';
require_once 'functions.php';

$conn = connectToDb();

$query = "SELECT film.*, produktionsfirma.bezeichnung AS produktionsfirma "
        . "FROM film "
        . "JOIN produktionsfirma ON film.produktionsfirma_id = produktionsfirma.produktionsfirma_id ";

$formData = [];

// Überprüfen ob ein GetRequest gesendet wurde
if (isGetRequest()) {
    // Übergebene Daten aus GET-Request auslesen und in Array speichern
    $formData = [
        'produktionsfirma' => formFieldValueGET('produktionsfirma', '')
    ];

    echo var_dump($formData) . '<br><br>';
    
    $query = $query . " WHERE produktionsfirma.bezeichnung LIKE ? " . " ORDER BY film.erscheinungsdatum ASC ";

    echo var_dump($query) . '<br><br>';

// Prepared Statement erstellen
    $stmt = $conn->prepare($query);

    
//  string zusammensetzten für wildcardsuche!!!!
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!    
    $string = "%".$formData['produktionsfirma']."%";
//  string zusammensetzten für wildcardsuche!!!!
//  
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! 
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!             
                                                
// ? des prepared Statement binden
    $stmt->bind_param('s', $string);

// mitgegebener Wert wegspeichern
    $SucheProduktionsfirma = null;

    $SucheProduktionsfirma = $formData['produktionsfirma'];
    //echo var_dump($SucheProduktionsfirma) . '<br><br>';
    
} else {
    echo '<p>Bitte verwenden Sie die Steuerung der Website!</p>';
}

// Statement ausführen
$stmt->execute();

// Ergebnis des Statement in resultat speichern
$result = $stmt->get_result();

// leeres Array erzeugen
$filme = [];
$produktionsfirma = null;

// Anzahl der Reihen im Resultat überprüfen
if ($result && $result->num_rows > 0) {
// Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    while ($row = $result->fetch_object()) {
        $filme[] = $row;
        $produktionsfirma = $row->produktionsfirma;
    }
} else {
    // Wenn kein Ergebnis gefunden wurde Fehlermeldung ausgeben und Programm beenden
    die('Produktionsfirma nicht gefunden!');
}

closeDb($conn);
?>

<!DOCTYPE html>
<html>    
    <head>
        <meta charset="UTF-8">
        <title>Suchergebnis</title>
    </head>

    <body>
        <h1>Suchergebnis</h1>

        <div><p>Gesuchte Produktionsfirma: <b><?= $SucheProduktionsfirma ?> </b></p></div>

        <div><p>Gefundene Produktionsfirma: <b><?= $produktionsfirma ?></b></p></div>

        <div><p>Gefundene Filmtitel: <b><?= count($filme) ?></b></p></div> 

        <table>
            <thead>
                <tr>
                    <th>Titel</th>
                    <th>Erscheinungs-Datum</th>
                    <th>Produktionsfirma</th>
                </tr>
            </thead>

            <?php foreach ($filme AS $film) : ?>
                <tbody>
                    <tr>
                        <td><?= $film->titel ?></td>
                        <td><?= date('d.m.Y', strtotime($film->erscheinungsdatum)) ?></td>
                        <td><?= $film->produktionsfirma ?></td>
                    </tr>
                </tbody>
            <?php endforeach; ?>
        </table>
        <br>
        <br>
        <div><a href="suche.php">Zurück zur Suche</a></div>
        <br>
        <div><a href="einstieg.php">Startseite</a></div>
    </body>    
</html>