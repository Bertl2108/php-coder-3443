<?php
require_once 'config.php';
require_once 'functions.php';

$conn = connectToDb();

$query = "SELECT kurs.*, fachbereich.name AS fachbereich "
        . "FROM kurs "
        . "JOIN fachbereich ON kurs.fachbereich_id = fachbereich.fachbereich_id ";

// Überprüfung ob GET Reuqest gesendet wurde, nur dann wird das Select Statement ausgeführt
if (isGetRequest()) {
// Übergebene Daten aus POST-Request auslesen und in Array speichern
    $formData = [
        'fachbereich_id' => formFieldValueGET('fachbereich', '')
    ];

    if ($formData['fachbereich_id'] != '') {

        $query = $query . " WHERE kurs.fachbereich_id = ? " . " ORDER BY kurs.beginndatum ASC ";

        echo var_dump($query) . '<br><br>';

        $stmt = $conn->prepare($query);

        // Parameter binden
        $stmt->bind_param('i', $formData['fachbereich_id']);
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
$query2 = "SELECT * FROM fachbereich";

echo var_dump($query2) . '<br><br>';

$stmt = $conn->prepare($query2);

// Statement ausführen
$stmt->execute();

// Ergebnis des Statements in resultat speichern
$result = $stmt->get_result();

$fachbereiche = [];

// Anzahl der Reihen im Resultat überprüfen
if ($result && $result->num_rows > 0) {
    // Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    while ($row = $result->fetch_object()) {
        $fachbereiche[] = $row;
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
            <form action="kurse_nach_fachbereich.php?fachbereich=" method="GET">
                <label for="fachbereich">Kursort</label>
                <select name="fachbereich">
                    <option value="">Alle</option>
                    <?php foreach ($fachbereiche as $fachbereich) : ?>
                        <option value="<?= $fachbereich->fachbereich_id ?>"
                                <?= $fachbereich->fachbereich_id == $formData['fachbereich_id'] ? 'selected' : '' ?>>
                            <?= $fachbereich->name ?></option>
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
                        <th>Fachbereich</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kurse as $kurs): ?>
                        <tr>
                            <td><?= $kurs->kurs_id ?></td>
                            <td><a href="kurs_details.php?kurs=<?= $kurs->kurs_id ?>"><?= $kurs->name ?></a></td>
                            <td><?= date('Y-m-d', strtotime($kurs->beginndatum)) ?></td>
                            <td><?= $kurs->fachbereich ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php else : ?>
            <p>Keine Kurse gefunden!</p>
            <?php endif ; ?>
            
        </div>   
        <br>
        <br>
        <div>
            <a href="index.php">Zurück zur Startseite</a>
        </div>
    </body>    
</html>
