<?php
require_once 'config.php';
require_once 'functions.php';

$conn = connectToDb();

$formData = []; //Array für query anlegen FORMDATA = Daten aus Formular
$query = "SELECT kurs.*, ort.name AS ort FROM kurs "
        . "JOIN ort ON kurs.ort_id = ort.ort_id "; //query für 2 verschiedene zsenarien anlegen -> Alle und gefiltert
//. "WHERE kurs.ort_id = 1"; // WHERE CONDITION deshalb -> nur wenn kurs.ort_id gefüllt ist?

// Überprüfen ob ein GetRequest gesendet wurde
if (isGetRequest()) {
    // Übergebene Daten aus GET-Request auslesen und in Array speichern
    $formData = [
        'ort_id' => formFieldValueGet('ort_id', ''),
    ];
    if ($formData['ort_id'] == '') { //ort_id == '' bezieht sich auf VALUE von SELECT OPTION
        $query = $query . " ORDER BY kurs.beginndatum ASC";
        echo var_dump($query) . '<br><br>';
        $stmt = $conn->prepare($query);
    } else {
        $query = $query . " WHERE kurs.ort_id = ?" . " ORDER BY kurs.beginndatum ASC";
        echo var_dump($query) . '<br><br>';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $formData['ort_id']);
    }
}

// Statement ausführen
$stmt->execute();

// Ergebnis des Statements in resultat speichern
$result = $stmt->get_result();

$kurse = []; //ergebnis array anlegen
$noResults = false; //fehlerbehandlungs bool -> immer false außer es konnten keine kurse gefundne werden

// Anzahl der Reihen im Resultat überprüfen
if ($result && $result->num_rows > 0) { // ->num_rows = objektorientiert JA
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
        <title>Kurse nach Kursort</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <body>
        <main>
            <div>
                <h1>Kurse nach Kursort</h1>
                <div>
                    <form action="kurse_nach_kursort.php" method="GET"> <?php  //action -> hier werden die Daten hingesendet -> MUSS IMMER angegeben werden?>
                        <label for="ort_id">Kursort</label> <!-- FOR="" bezieht sich auf das select attribut -> NAME="" -->
                        <select name="ort_id" id="ort_id"> <?php // name = MUSS verwendet werden -> sonst würde GET Requ. nicht wisen welches Attribut /// ID = Für CSS Selektor?>
                            <option value="" selected>Alle</option> <?php //value"" weil ALLE vorselektiert sein sollen = JA /// -> selected? = default wert ?>
                            <?php foreach ($kurse as $kurs): ?>
                                <option value="<?php echo $kurs->ort_id ?>"><?php echo $kurs->ort ?></option> <?php //value für GET Request!? ?>
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
