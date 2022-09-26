<?php
require_once 'config.php';
require_once 'functions.php';

// Datenbank Verbindung aufbauen
$conn = connectToDb();

//Leeren Array für GET-Request anlegen
$formData = [];

//query aufbauen
$query = "SHOW DATABASES";
//Kontroll Ausgabe
echo var_dump($query) . '<br><br>';

// Prepared Statement erstellen
$stmt = $conn->prepare($query);

// Statement ausführen
$stmt->execute();

// Ergebnis des Statement in resultat speichern
$result = $stmt->get_result();

// leeres Array erzeugen
$datenbanken = [];

// Anzahl der Reihen im Resultat überprüfen
if ($result && $result->num_rows > 0) {
// Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    while ($row = $result->fetch_object()) {
        $datenbanken[] = $row;
    }
} else {
    // Wenn kein Ergebnis gefunden wurde Fehlermeldung ausgeben und Programm beenden
    die('Datenbanken nicht gefunden!');
}

//Nur wenn GET-Request gesendet wurde, wird prozessiert
if (isGetRequest()) {
    // Übergebene Daten aus GET-Request auslesen und in Array speichern
    $formData = [
        'datenbank' => formFieldValueGET('datenbank', '')
    ];

    // query aufbauen
    $query = "SELECT * FROM information_schema.tables WHERE TABLE_SCHEMA LIKE ?";
    //Kontrolle
    echo var_dump($query) . '<br><br>';
    //Statement vorbereiten
    $stmt = $conn->prepare($query);
    //Parameter binden
    $stmt->bind_param('s', $formData['datenbank']);

    // Statement ausführen
    $stmt->execute();

    // Ergebnis des Statement in resultat speichern
    $result = $stmt->get_result();

    // leeres Array erzeugen
    $tabellen = [];

    // Anzahl der Reihen im Resultat überprüfen
    if ($result && $result->num_rows > 0) {
        // Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
        while ($row = $result->fetch_object()) {
            $tabellen[] = $row;
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
        <title>Datenbanken</title>
    </head>

    <body>
        <h1>Datenbanken</h1>

        <div>
            <form action="index.php" method="GET">
                <label for="datenbanken">Datenbank:</label>
                <select name="datenbank">
                    <option value="">Alle</option>
                    <?php foreach ($datenbanken as $datenbank): ?>
                        <option value="<?= $datenbank->Database ?>"<?= $formData['datenbank'] == $datenbank->Database ? 'selected' : '' ?>><?= $datenbank->Database ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Suchen</button>
            </form>
        </div>

        <?php if ($tabellen && $tabellen != '') : ?>
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>Tabellen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tabellen as $tabelle): ?>
                            <tr>
                                <td><?= $tabelle->TABLE_NAME ?></td>
                                <td><a href="details.php?tabelle=<?= $tabelle->TABLE_NAME ?>">Details</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>     
        <?php endif; ?>
    </body>    
</html>
