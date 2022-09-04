<?php
require_once 'config.php';
require_once 'functions.php';

$conn = connectToDb();

$query = "SELECT film.*, produktionsfirma.bezeichnung AS produktionsfirma "
        . "FROM film "
        . "JOIN produktionsfirma ON film.produktionsfirma_id = produktionsfirma.produktionsfirma_id ";

// Leeren Array für GET Request erzeugen
$formData = [];

// Überprüfung ob GET Reuqest gesendet wurde, nur dann wird das Select Statement ausgeführt
if (isGetRequest()) {
// Übergebene Daten aus POST-Request auslesen und in Array speichern
    $formData = [
        'produktionsfirma' => formFieldValueGET('produktionsfirma', '')
    ];

    $query = $query . " WHERE produktionsfirma.bezeichnung LIKE ? " . " ORDER BY film.erscheinungsdatum ASC ";

    echo var_dump($query) . '<br><br>';

    $stmt = $conn->prepare($query);

    //Übergebener Wert mit % für Wildcard suche versehen
    $suchstring = "%".$formData['produktionsfirma']."%";
    
    // Kontrolle ob SuchString korrekt ist
    echo var_dump($suchstring) . '<br><br>';    
    
    // Parameter binden
    $stmt->bind_param('s', $suchstring);  
    
} else {
    die('Bitte Verwenden Sie die Steuerung der Website!');
}

// Statement ausführen
$stmt->execute();

// Ergebnis des Statements in resultat speichern
$result = $stmt->get_result();

// leeren Array anlegen für das Ergebnis
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
    die('Produktionsfirma nicht gefunden!');
}

echo var_dump($filme) . '<br><br>';
echo var_dump($produktionsfirma) . '<br><br>';

// put your code here
closeDb($conn);
?>

<!DOCTYPE html>
<html>    
    <head>
        <meta charset="UTF-8">
        <title>Suchergebnis</title>
    </head>

    <body>
        <div>
            <h1>Suchergebnis</h1>
            <div>Gesuchte Produktionsfirma: <b><?= $formData['produktionsfirma'] ?></b></div>       
            <br>
            <div>Gefundene Produktionsfirma: <b><?= $produktionsfirma ?></b></div>
            <br>
            <div>Gefundene Filmtitel: <b><?= count($filme) ?></b></div>
            <br>
        </div>

        <div>
            <table>
                <thead>
                    <tr>
                        <th>Titel</th>
                        <th>Erscheinungs-Datum</th>
                        <th>Produktionsfirma</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filme as $film): ?>
                        <tr>
                            <td><?= $film->titel ?></td>
                            <td><?= date('d.m.Y', strtotime($film->erscheinungsdatum)) ?></td>
                            <td><?= $film->produktionsfirma ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </body>    
</html>
