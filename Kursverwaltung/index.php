<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<?php
require_once 'config.php';
require_once 'functions.php';

$conn = connectToDb();

$query = "SELECT kurs.*, ort.name AS ort FROM kurs "
        . "JOIN ort ON kurs.ort_id = ort.ort_id "
        . "ORDER BY kurs.beginndatum ASC ";

//echo var_dump($query) . '<br><br>';

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

closeDb($conn);
?>

<html>

    <head>
        <title>Kursübersicht</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <body>
        <div>
            <nav> 
                <h2>Navigation</h2>
                <ul>
                    <li><a href="index.php">Startseite</a></li>
                    <li><a href="kurse_nach_fachbereich.php">Kurse nach Fachbereich</a></li>
                    <li><a href="kurse_nach_kursort.php">Kurse nach Kursort</a></li>
                </ul>
            </nav>
        </div>

        <main>
            <div>
                <h1>Kursübersicht</h1>
                <table border="0">
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
                                <td><?php echo $kurs->kurs_id ?></td>
                                <td>
                                    <a href="kurs_details.php?kurs_id=<?= $kurs->kurs_id ?>"><?= $kurs->name ?></a>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($kurs->beginndatum)) ?></td>
                                <td><?php echo $kurs->ort ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody> 
                </table>
            </div>
        </main>

    </body>

</html>
