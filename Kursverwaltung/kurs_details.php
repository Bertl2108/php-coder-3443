<?php
require_once 'config.php';
require_once 'functions.php';

$formData = [];

if (isGetRequest()) {
// Übergebene Daten aus POST-Request auslesen und in Array speichern
    $formData = [
        'kurs_id' => formFieldValueGET('kurs_id', '')
    ];
}

$conn = connectToDb();

//SQL Statement 1
$query = "SELECT kurs.*, fachbereich.name as fachbereich FROM kurs "
        . "JOIN fachbereich ON kurs.fachbereich_id = fachbereich.fachbereich_id "
        . "WHERE kurs_id = ? "; //LIKE nur bei STRINGS

//echo var_dump($query) . '<br><br>';

$stmt = $conn->prepare($query);

// Parameter binden
$stmt->bind_param('i', $formData['kurs_id']);
// Statement ausführen
$stmt->execute();

// Ergebnis des Statements in resultat speichern
$result = $stmt->get_result();

$kursdetails = null;

// Anzahl der Reihen im Resultat überprüfen
if ($result && $result->num_rows == 1) {
// Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    $kursdetails = $result->fetch_object();
}



//SQL Statement 2
$query2 = "SELECT termin.*, trainer.nachname as trainer from termin " //KEIN QUERY2 benötigt
        . "JOIN trainer ON trainer.trainer_id = termin.trainer_id "
        . "WHERE termin.kurs_id  = ? ";

//echo var_dump($query2) . '<br><br>';

$stmt2 = $conn->prepare($query2);

// Parameter binden
$stmt2->bind_param('i', $formData['kurs_id']);

// Statement ausführen
$stmt2->execute();

// Ergebnis des Statements in resultat speichern
$result2 = $stmt2->get_result();

$termine = [];

// Anzahl der Reihen im Resultat überprüfen
if ($result2 && $result2->num_rows > 0) {
    // Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    while ($row = $result2->fetch_object()) {
        $termine[] = $row;
    }
}



//SQL Statement 3
$query3 = "SELECT teilnehmer.vorname AS vorname, teilnehmer.nachname AS nachname, "
        . "teilnehmer.email AS email, teilnehmer.geburtsdatum AS geburtsdatum FROM kurs_has_teilnehmer "
        . "JOIN teilnehmer ON kurs_has_teilnehmer.teilnehmer_id = teilnehmer.teilnehmer_id "
        . "WHERE kurs_has_teilnehmer.kurs_id = ? ";

//echo var_dump($query3) . '<br><br>';

$stmt3 = $conn->prepare($query3);

// Parameter binden
$stmt3->bind_param('i', $formData['kurs_id']);

// Statement ausführen
$stmt3->execute();

// Ergebnis des Statements in resultat speichern
$result3 = $stmt3->get_result();

$teilnehmerinnen = [];

// Anzahl der Reihen im Resultat überprüfen
if ($result3 && $result3->num_rows > 0) {
    // Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    while ($row = $result3->fetch_object()) {
        $teilnehmerinnen[] = $row;
    }
}

closeDb($conn);
?>



<html>

    <head>
        <title>Kursdetail</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <body>
        <h1>Kurs: <?php echo $kursdetails->kurs_id ?> <?php echo $kursdetails->name ?> </h1>
        <table border="0">
            <tbody>
                <tr>
                    <td><b>Beginndatum:</b></td>
                    <td><?php echo date('Y-m-d', strtotime($kursdetails->beginndatum)) ?></td>
                </tr>
                <tr>
                    <td><b>Dauer:</b></td>
                    <td><?php echo $kursdetails->dauer ?> Einheiten</td>
                </tr>
                <tr>
                    <td><b>Fachbereich:</b></td>
                    <td><?php echo $kursdetails->fachbereich ?></td>
                </tr>
                <tr>
                    <td><b>Beschreibung:</b></td>
                    <td><?php echo $kursdetails->beschreibung ?></td>
                </tr>
            </tbody>
        </table>

        <h2>Termine</h2>
        <table border="0">
            <thead>
                <tr>
                    <th>Beginn</th>
                    <th>Einheiten</th>
                    <th>Trainer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($termine as $termin): ?>
                    <tr>
                        <td><?php echo $termin->beginn ?></td>
                        <td><?php echo $termin->dauer ?></td>
                        <td><?php echo $termin->trainer ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody> 
        </table>

        <h2>Teilnehmer</h2>
        <table border="0">
            <thead>
                <tr>
                    <th>Vorname</th>
                    <th>Nachname</th>
                    <th>E-Mail</th>
                    <th>Geburtsdatum</th>
                </tr>
            </thead>
            <tbody>                
                <?php foreach ($teilnehmerinnen as $teilnehmer): 
                    //ES MÜSSEN 2 VERSCHIEDENE VARIABLEN SEIN! nicht $teilnehmer as $teilnehmer! ?>  
                    <tr>
                        <td><?php echo $teilnehmer->vorname ?></td>
                        <td><?php echo $teilnehmer->nachname ?></td>
                        <td><?php echo $teilnehmer->email ?></td>
                        <td><?php echo date('Y-m-d', strtotime($teilnehmer->geburtsdatum)) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody> 
        </table>

        <div>
            <br>
            <a href="index.php">Zurück zur Startseite</a>
        </div>

    </body>
</html>
