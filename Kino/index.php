<?php
require_once 'config.php';
require_once 'functions.php';

$conn = connectToDb();

$query = "SELECT filme.*, raeume.*, vorstellungen.* "
        . "FROM vorstellungen "
        . "JOIN filme ON vorstellungen.film_id = filme.film_id "
        . "JOIN raeume ON vorstellungen.raum_id = raeume.raum_id "
        . "ORDER BY vorstellungen.beginnzeit DESC ";

echo var_dump($query) . '<br><br>';

$stmt = $conn->prepare($query);

// Statement ausführen
$stmt->execute();

// Ergebnis des Statements in resultat speichern
$result = $stmt->get_result();

$filme = [];

// Anzahl der Reihen im Resultat überprüfen
if ($result && $result->num_rows > 0) {
    // Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    while ($row = $result->fetch_object()) {
        $filme[] = $row;
    }
}

// put your code here
closeDb($conn);
?>

<!DOCTYPE html>
<html>    
    <head>
        <meta charset="UTF-8">
        <title>Film Vorstellungen</title>
    </head>

    <body>
        <h1>Film Vorstellungen</h1>
        <div>
            <table>
                <thead>
                    <tr>
                        <th>Film</th>
                        <th>Raum</th>
                        <th>Beginn</th>
                        <th>Plätze</th>
                        <th></th>
                    </tr>
                </thead>
                <?php foreach ($filme as $film) : ?>
                    <tbody>
                        <tr>
                            <td><?= $film->titel ?></td>
                            <td>Raum: <?= $film->raum_id ?></td>
                            <td><?= date('H:i', strtotime($film->beginnzeit)) ?> Uhr</td> 
                            <td><?= $film->sitzplaetze ?></td>
                            <?php if ($film->sitzplaetze > 0) : ?>
                                <td><a href="ticket_buchen.php?vorstellung_id=<?= $film->vorstellung_id ?>">buchen</a></td>
                            <?php else : ?>
                                <td>ausgebucht</td>
                            <?php endif; ?>
                        </tr>
                    </tbody>
                <?php endforeach; ?>
            </table>
        </div>       
    </body>    
</html>
