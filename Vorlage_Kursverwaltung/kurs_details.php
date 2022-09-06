<?php
require_once 'config.php';
require_once 'functions.php';

$conn = connectToDb();

// SQL Statement 1
$query = "SELECT kurs.*, ort.name AS kursort, fachbereich.name AS fachbereich "
        . "FROM kurs "
        . "JOIN ort ON kurs.ort_id = ort.ort_id "
        . "JOIN fachbereich ON kurs.fachbereich_id = fachbereich.fachbereich_id ";

// Leeren Array für GET Request erzeugen
$formData = [];

// Überprüfung ob GET Reuqest gesendet wurde, nur dann wird das Select Statement ausgeführt
if (isGetRequest()) {
// Übergebene Daten aus POST-Request auslesen und in Array speichern
    $formData = [
        'kurs_id' => formFieldValueGET('kurs', '')
    ];

    $query = $query . " WHERE kurs.kurs_id = ? ";

    echo var_dump($query) . '<br><br>';

    $stmt = $conn->prepare($query); 
    
    // Parameter binden
    $stmt->bind_param('i', $formData['kurs_id']); 
    
} else {
    die('Bitte Verwenden Sie die Steuerung der Website!');
}

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

// SQL Statement 2
$query2 = "SELECT termin.*, trainer.nachname AS trainer "
        . "FROM termin "
        . "JOIN trainer ON termin.trainer_id = trainer.trainer_id "
        . "JOIN kurs ON termin.kurs_id = kurs.kurs_id ";

// Überprüfung ob GET Reuqest gesendet wurde, nur dann wird das Select Statement ausgeführt
if (isGetRequest()) {
// Übergebene Daten aus POST-Request auslesen und in Array speichern
    $formData = [
        'kurs_id' => formFieldValueGET('kurs', '')
    ];

    $query2 = $query2 . " WHERE kurs.kurs_id = ? ";

    echo var_dump($query2) . '<br><br>';

    $stmt = $conn->prepare($query2); 
    
    // Parameter binden
    $stmt->bind_param('i', $formData['kurs_id']); 
    
} else {
    die('Bitte Verwenden Sie die Steuerung der Website!');
}

// Statement ausführen
$stmt->execute();

// Ergebnis des Statements in resultat speichern
$result = $stmt->get_result();

$termine = [];
        
// Anzahl der Reihen im Resultat überprüfen
if ($result && $result->num_rows > 0) {
    // Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    while ($row = $result->fetch_object()) {
        $termine[] = $row;
    }
}


// SQL Statement 3
$query3 = "SELECT teilnehmer.* "
        . "FROM kurs_has_teilnehmer "
        . "JOIN kurs ON kurs_has_teilnehmer.kurs_id = kurs.kurs_id "
        . "JOIN teilnehmer ON kurs_has_teilnehmer.teilnehmer_id = teilnehmer.teilnehmer_id ";

// Überprüfung ob GET Reuqest gesendet wurde, nur dann wird das Select Statement ausgeführt
if (isGetRequest()) {
// Übergebene Daten aus POST-Request auslesen und in Array speichern
    $formData = [
        'kurs_id' => formFieldValueGET('kurs', '')
    ];

    $query3 = $query3 . " WHERE kurs.kurs_id = ? ";

    echo var_dump($query3) . '<br><br>';

    $stmt = $conn->prepare($query3); 
    
    // Parameter binden
    $stmt->bind_param('i', $formData['kurs_id']); 
    
} else {
    die('Bitte Verwenden Sie die Steuerung der Website!');
}

// Statement ausführen
$stmt->execute();

// Ergebnis des Statements in resultat speichern
$result = $stmt->get_result();

$teilnehmerinnen = [];
        
// Anzahl der Reihen im Resultat überprüfen
if ($result && $result->num_rows > 0) {
    // Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    while ($row = $result->fetch_object()) {
        $teilnehmerinnen[] = $row;
    }
}

// put your code here
closeDb($conn);
?>

<!DOCTYPE html>
<html>    
    <head>
        <meta charset="UTF-8">
        <title>Kursdetails</title>
    </head>

    <body>
        <h1>Kurs: <?= $kursdetails->kurs_id ?> <?= $kursdetails->name ?></h1>
        <div>
            <table>
                <tr>
                    <th>Beginndatum:</th>
                    <td><?= date('Y-m-d', strtotime($kursdetails->beginndatum))?></td>
                </tr>
                <tr>
                    <th>Dauer:</th>
                    <td><?= $kursdetails->dauer ?></td>
                </tr>
                <tr>
                    <th>Fachbereich:</th>
                    <td><?= $kursdetails->fachbereich ?></td>
                </tr>
                <tr>
                    <th>Beschreibung:</th>
                    <td><?= $kursdetails->beschreibung ?></td>
                </tr>
            </table>
        </div>      

        <div>
            <h2>Termine</h2>
            <table>
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
                            <td><?= $termin->beginn ?></td>
                            <td><?= $termin->dauer ?></td>
                            <td><?= $termin->trainer ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div>
            <h2>Teilnehmer</h2>
            
            <table>
                <thead>
                    <tr>
                        <th>Vorname</th>
                        <th>Nachname</th>
                        <th>E-Mail</th>
                        <th>Geburtsdatum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teilnehmerinnen as $teilnehmer): ?>
                        <tr>
                            <td><?= $teilnehmer->vorname ?></td>
                            <td><?= $teilnehmer->nachname ?></td>
                            <td><?= $teilnehmer->email ?></td>
                            <td><?= date('Y-m-d', strtotime($teilnehmer->geburtsdatum))?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <br>
        <br>
        <div>
            <a href="index.php">Zurück zur Startseite</a>
        </div>
        
    </body>    
</html>