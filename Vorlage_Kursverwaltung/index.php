<?php
require_once 'config.php';
require_once 'functions.php';

$conn = connectToDb();

$query = "SELECT kurs.*, ort.name AS kursort "
        . "FROM kurs "
        . "JOIN ort ON kurs.ort_id = ort.ort_id "
        . "ORDER BY kurs.beginndatum ASC ";

echo var_dump($query) . '<br><br>';

$stmt = $conn->prepare($query);

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

closeDB($conn);

?>

<!DOCTYPE html>
<html>    
    <head>
        <meta charset="UTF-8">
        <title>Kursübersicht</title>
    </head>
    
    <body>
        <h2>Navigation</h2>
        <div>
            <ul>
                <li><a href="index.php">Startseite</a></li>
                <li><a href="kurse_nach_fachbereich.php">Kurse nach Fachbereich</a></li>
                <li><a href="kurse_nach_kursort.php">Kurse nach Kursort</a></li>
            </ul>
        </div>
        
        <h1>Kursübersicht</h1>
        <div>
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

        </div>       
    </body>    
</html>