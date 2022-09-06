<?php
require_once 'config.php';
require_once 'functions.php';

$conn = connectToDb();

$query = "SELECT kurs.*, ort.name AS kursort "
        . "FROM kurs "
        . "JOIN ort ON kurs.ort_id = ort.ort_id ";

// Überprüfung ob GET Reuqest gesendet wurde, nur dann wird das Select Statement ausgeführt
if (isGetRequest()) {
// Übergebene Daten aus POST-Request auslesen und in Array speichern
    $formData = [
        'ort_id' => formFieldValueGET('kursort', '')
    ];

    if ($formData['ort_id'] != '') {

        $query = $query . " WHERE kurs.ort_id = ? " . " ORDER BY kurs.beginndatum ASC ";

        echo var_dump($query) . '<br><br>';

        $stmt = $conn->prepare($query);

        // Parameter binden
        $stmt->bind_param('i', $formData['ort_id']);
    } else {
        $query = $query . " ORDER BY kurs.beginndatum ASC ";

        echo var_dump($query) . '<br><br>';

        $stmt = $conn->prepare($query);
    }
}

// Statement ausführen
$stmt->execute();

// Ergebnis des Statements in resultat speichern
$result = $stmt->get_result();

$kurse = [];

// Anzahl der Reihen im Resultat überprüfen
if ($result && $result->num_rows > 0) {
    // Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    while ($row = $result->fetch_object()) {
        $kurse[] = $row;
    }
}

// SQL Statement 2
$query2 = "SELECT * FROM ort";

echo var_dump($query2) . '<br><br>';

$stmt = $conn->prepare($query2);

// Statement ausführen
$stmt->execute();

// Ergebnis des Statements in resultat speichern
$result = $stmt->get_result();

$orte = [];

// Anzahl der Reihen im Resultat überprüfen
if ($result && $result->num_rows > 0) {
    // Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    while ($row = $result->fetch_object()) {
        $orte[] = $row;
    }
}

closeDb($conn);
?>

<!DOCTYPE html>
<html>    
    <head>
        <meta charset="UTF-8">
        <title>Kurse nach Kursort</title>
    </head>

    <body>
        <h1>Kurse nach Kursort</h1>
        <div>
            <form action="kurse_nach_kursort.php?ort=" method="GET">
                <label for="kursort">Kursort</label>
                <select name="kursort">
                    <option value="">Alle</option>
                    <?php foreach ($orte as $ort) : ?>
                        <option value="<?= $ort->ort_id ?>"
                                <?= $ort->ort_id == $formData['ort_id'] ? 'selected' : '' ?>>
                            <?= $ort->name ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Suchen</button>
            </form>

            <?php if (count($kurse) > 0) : ?>
            
            <table>
                <thead>
                    <tr>
                        <th>Kursnummer</th>
                        <th>Name</th>
                        <th>Beginndatum</th>
                        <th>Kursort</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kurse as $kurs): ?>
                        <tr>
                            <td><?= $kurs->kurs_id ?></td>
                            <td><a href="kurs_details.php?kurs=<?= $kurs->kurs_id ?>"><?= $kurs->name ?></a></td>
                            <td><?= date('Y-m-d', strtotime($kurs->beginndatum)) ?></td>
                            <td><?= $kurs->kursort ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php else : ?>
            <p>Keine Kurse gefunden!</p>
            <?php endif ; ?>
            
        </div>   
        <div>
            <a href="index.php">Zurück zur Startseite</a>
        </div>
    </body>    
</html>
