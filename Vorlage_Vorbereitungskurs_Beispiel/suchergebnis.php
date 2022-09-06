<?php
require_once 'config.php';
require_once 'functions.php';

$conn = connectToDb();

// SQL Statement
$query = "SELECT film.*, produktionsfirma.bezeichnung AS produktionsfirma "
        . "FROM film "
        . "JOIN produktionsfirma ON film.produktionsfirma_id = produktionsfirma.produktionsfirma_id ";

$formData = [];

// Überprüfen ob ein GetRequest gesendet wurde
if (isGetRequest()) {
    // Übergebene Daten aus GET-Request auslesen und in Array speichern
    $formData = [
        'produktionsfirma' => formFieldValueGet('produktionsfirma', ''),
    ];

    $query = $query . " WHERE produktionsfirma.bezeichnung LIKE ? " . " GROUP BY film.erscheinungsdatum ";

    echo var_dump($query) . '<br><br>';

// Prepared Statement erstellen
    $stmt = $conn->prepare($query);

    $suchString = "%" . $formData['produktionsfirma'] . "%";

// Parameter binden
    $stmt->bind_param('s', $suchString);
}

// Statement ausführen
$stmt->execute();

// Ergebnis des Statements in resultat speichern
$result = $stmt->get_result();

$filme = [];
$produktionsfirma = null;

// Anzahl der Reihen im Resultat überprüfen
if ($result && $result->num_rows > 0) {
    // Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    while ($row = $result->fetch_object()) {
        $filme[] = $row;
        $produktionsfirma = $row->produktionsfirma;
    }
}

closeDb($conn);
?>

<!DOCTYPE html>
<html>    
    <head>
        <meta charset="UTF-8">
        <title>Filmsuche - Suchergebnis</title>
    </head>

    <?php if (count($filme) > 0) : ?>

        <body>
            <h1>Suchergebnis</h1>
            <div>
                <p>Gesuchte Produktionsfirma: <b><?= $formData['produktionsfirma'] ?></b></p>
            </div>
            <div>
                <p>Gefundene Produktionsfirma: <b><?= $produktionsfirma ?></b></p>
            </div>  
            <div>
                <p>Gefundene Filmtitel: <b><?= count($filme) ?></b></p>
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
                                <td><?php echo date('d.m.Y', strtotime($film->erscheinungsdatum)) ?></td>
                                <td><?php echo $film->produktionsfirma ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>   
        </body>  
    <?php else : ?>
        <p>Produktionsfirma nicht gefunden!</p>
    <?php endif; ?>
</html>

