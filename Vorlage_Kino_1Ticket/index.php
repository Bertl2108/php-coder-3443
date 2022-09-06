<?php
require_once 'config.php';
require_once 'functions.php';

$conn = connectToDb();

$query = "SELECT vorstellungen.*, filme.*, raeume.* "
        . "FROM vorstellungen "
        . "JOIN filme ON vorstellungen.film_id = filme.film_id "
        . "JOIN raeume ON vorstellungen.raum_id = raeume.raum_id "
        . "WHERE vorstellungen.beginnzeit < NOW() "
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
                <tbody>
                    <?php foreach ($filme as $film): ?>
                        <tr>
                            <td><?= $film->titel ?></td>
                            <td>Raum <?= $film->raum_id ?></a></td>
                            <td><?= date('H:m', strtotime($film->beginnzeit)) ?></td>
                            <td><?= $film->sitzplaetze ?></td>
                            <?php if ($film->sitzplaetze > 0) : ?> 
                                <td><a href="ticket_buchen.php?id=<?=$film->film_id?>">buchen</a></td>
                            <?php else : ?>
                                <td>ausgebucht</td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>       
    </body>    
</html>
