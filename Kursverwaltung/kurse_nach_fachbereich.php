<?php
require_once 'config.php';
require_once 'functions.php';

$conn = connectToDb();

$formData = [];

$query = "SELECT kurs.*, ort.name AS ort, fachbereich.name AS fachbereich FROM kurs "
        . "JOIN ort ON kurs.ort_id = ort.ort_id "
        . "JOIN fachbereich ON kurs.fachbereich_id = fachbereich.fachbereich_id ";

// Überprüfen ob ein GetRequest gesendet wurde
if (isGetRequest()) {
    // Übergebene Daten aus GET-Request auslesen und in Array speichern
    $formData = [
        'fachbereich_id' => formFieldValueGet('fachbereich_id', ''),
    ];
    if ($formData['fachbereich_id'] == '') {
        $query = $query . " ORDER BY kurs.beginndatum ASC";
        echo var_dump($query) . '<br><br>';
        $stmt = $conn->prepare($query);
    } else {
        $query = $query . " WHERE kurs.fachbereich_id = ?" . " ORDER BY kurs.beginndatum ASC";
        echo var_dump($query) . '<br><br>';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $formData['fachbereich_id']);
    }
}

// Statement ausführen
$stmt->execute();

// Ergebnis des Statements in resultat speichern
$result = $stmt->get_result();

$kurse = []; //ergebnis array anlegen
$noResults = false; //fehlerbehandlungs bool -> immer false außer es konnten keine kurse gefundne werden

echo var_dump($result) . '<br><br>';

// Anzahl der Reihen im Resultat überprüfen
if ($result && $result->num_rows > 0) {
    // Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    while ($row = $result->fetch_object()) {
        $kurse[] = $row;
    }
} else {
    $noResults = true;
}

closeDb($conn);
?>

<html>

    <head>
        <title>Kurse nach Fachbereich</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <body>
        <main>
            <div>
                <h1>Kurse nach Fachbereich</h1>
                <div>
                    <form action="kurse_nach_fachbereich.php" method="GET"> <?php  //action -> hier werden die Daten hingesendet -> MUSS IMMER angegeben werden?>
                        <label for="fachbereich_id">Fachbereich</label> <!-- FOR="" bezieht sich auf das select attribut -> NAME="" -->
                        <select name="fachbereich_id" id="fachbereich_id"> <?php // name = MUSS verwendet werden -> sonst würde GET Requ. nicht wisen welches Attribut /// ID = Für CSS Selektor?>
                            <option value="" disabled selected>Alle</option>
                            <?php foreach ($kurse as $kurs): ?>
                                <option value="<?php echo $kurs->fachbereich_id ?>"><?php echo $kurs->fachbereich ?></option> <?php //value für GET Request!? ?>
                            <?php endforeach; ?> 
                        </select>
                        <button type="submit">Suchen</button> <?php //Für FORM wird IMMER ein Submit benötigt -> sonst kein absenden der Daten ?>
                    </form>
                </div>

                <?php if (!$noResults) : //keine Kurse gefunden?> 
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
                    <div>
                    <?php else: ?>
                        <p>Keine Kurse gefunden!</p>
                    <?php endif; ?>

                    <div>
                        <br>
                        <a href="index.php">Zurück zur Startseite</a>
                    </div>
                </div>
        </main>

    </body>

</html>
