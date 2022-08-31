<?php
require_once 'config.php';
require_once 'functions.php';

$conn = connectToDb();

$query = "SELECT film.*, produktionsfirma.bezeichnung AS produktionsfirma, schauspieler.* "
        . "FROM rolle "
        . "JOIN film ON rolle.film_id = film.film_id "
        . "JOIN produktionsfirma ON film.produktionsfirma_id = produktionsfirma.produktionsfirma_id "
        . "JOIN schauspieler ON rolle.schauspieler_id = schauspieler.schauspieler_id ";

$formData = [];

// Überprüfen ob ein GetRequest gesendet wurde
if (isGetRequest()) {
    // Übergebene Daten aus GET-Request auslesen und in Array speichern
    $formData = [
        'schauspieler' => formFieldValueGET('schauspieler', '')
    ];

    $query = $query . " WHERE schauspieler.vorname LIKE ? " . " ORDER BY film.erscheinungsdatum ASC ";

    //echo var_dump($query) . '<br><br>';
// Prepared Statement erstellen
    $stmt = $conn->prepare($query);

// ? des prepared Statement binden
    $stmt->bind_param("s", $formData['schauspieler']);

// mitgegebener Wert wegspeichern
    $Sucheschauspieler = null;

    $SucheSchauspieler = $formData['schauspieler'];
    //echo var_dump($SucheSchauspieler) . '<br><br>';
} else {
    echo '<p>Bitte verwenden Sie die Steuerung der Website!</p>';
}

// Statement ausführen
$stmt->execute();

// Ergebnis des Statement in resultat speichern
$result = $stmt->get_result();

// leeres Array erzeugen
$filme = [];
$schauspieler = null;

// Anzahl der Reihen im Resultat überprüfen
if ($result && $result->num_rows > 0) {
// Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    while ($row = $result->fetch_object()) {
        $filme[] = $row;
        $SchauspielerVorname = $row->vorname;
        $SchauspielerNachname = $row->nachname;
    }
} else {
    // Wenn kein Ergebnis gefunden wurde Fehlermeldung ausgeben und Programm beenden
    die('Schauspieler nicht gefunden!');
}

//echo var_dump($SchauspielerVorname) . '<br><br>';
//echo var_dump($SchauspielerNachname) . '<br><br>';

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

        <div><p>Gesuchter Schauspieler: <b><?= $SucheSchauspieler ?> </b></p></div>

        <div><p>Gefundener Schauspieler: <b><?= $SchauspielerVorname ?> <?= $SchauspielerNachname ?> </b></p></div>

        <div><p>Gefundene Filmtitel: <b><?= count($filme) ?> </b></p></div> 

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