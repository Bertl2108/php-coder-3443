<?php
require_once 'config.php';
require_once 'functions.php';

//Datenbank Verbindung aufbauen
$conn = connectToDb();

//Nur wenn GET-Request gesendet wurde, wird prozessiert
if (isGetRequest()) {
    // Übergebene Daten aus GET-Request auslesen und in Array speichern
    $formData = [
        'tabelle' => formFieldValueGET('tabelle', ''),
        'schema' => formFieldValueGET('schema', '')
    ];

    //query aufbauen
    $query = "SELECT * FROM information_schema.columns WHERE TABLE_SCHEMA LIKE ? AND TABLE_NAME LIKE ?";
    //Kontrolle
    echo var_dump($query) . '<br><br>';
    //Statement vorbereiten
    $stmt = $conn->prepare($query);
    //Parameter binden
    $stmt->bind_param('ss', $formData['schema'], $formData['tabelle'] );

    // Statement ausführen
    $stmt->execute();

    // Ergebnis des Statement in resultat speichern
    $result = $stmt->get_result();

    // leeres Array erzeugen
    $spalten = [];

    // Anzahl der Reihen im Resultat überprüfen
    if ($result && $result->num_rows > 0) {
        // Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
        while ($row = $result->fetch_object()) {
            $spalten[] = $row;
        }
    } 
    
}

// DB Verbindung beenden
$conn->close();
?>

<!DOCTYPE html>
<html>    
    <head>
        <meta charset="UTF-8">
        <title>Spalten</title>
    </head>

    <body>
        <h1>Spalten</h1>

        <?php if ($spalten && $spalten != '') : ?>
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>Spalten</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($spalten as $spalte): ?>
                            <tr>
                                <td><?= $spalte->COLUMN_NAME ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>   
        <?php else : ?>
            <p>Es wurden keine Spalten in der von Ihnen gewählten Tabelle gefunden!</p>
        <?php endif; ?> 

        <div>
            <br>
            <br>
            <button type="button" onclick="location.href = 'index.php'">Zurück</button>
        </div>
    </body>    
</html>
