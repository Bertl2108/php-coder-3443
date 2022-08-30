<?php
require_once 'config.php';
require_once 'functions.php';

$formData = [];

if (isGetRequest()) {
// Übergebene Daten aus POST-Request auslesen und in Array speichern
    $formData = [
        'auto_id' => formFieldValueGET('auto_id', '')
    ];
}

$conn = connectToDb();

//SQL Statement 1
$query = "SELECT auto.*, marke.name AS marke, baujahr.jahrgang AS baujahr, "
        . "modell.bezeichnung AS modell, antriebsart.bezeichnung AS antriebsart, "
        . "mitarbeiter.*, leistung.ps AS ps "
        . "FROM auto "
        . "JOIN antriebsart ON auto.antriebsart_id = antriebsart.antriebsart_id "
        . "JOIN leistung ON auto.leistung_id = leistung.leistung_id "
        . "JOIN mitarbeiter ON auto.mitarbeiter_id = mitarbeiter.mitarbeiter_id "
        . "JOIN marke ON auto.marke_id = marke.marke_id "
        . "JOIN baujahr ON auto.baujahr_id = baujahr.baujahr_id "
        . "JOIN modell ON auto.modell_id = modell.modell_id "
        . "WHERE auto_id = ? ";

//echo var_dump($query) . '<br><br>';

$stmt = $conn->prepare($query);

// Parameter binden
$stmt->bind_param('i', $formData['auto_id']);
// Statement ausführen
$stmt->execute();

// Ergebnis des Statements in resultat speichern
$result = $stmt->get_result();

$auto_details = [];
$noResult = false;

// Anzahl der Reihen im Resultat überprüfen
if ($result && $result->num_rows == 1) {
// Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    $auto_details = $result->fetch_object();
} else {
    $noResult = true;
}
?>

<html> 
    <?php if ($noResult == false) : ?>
        <?php //foreach ($auto_details as $auto_detail): ?>  
        <head>
            <meta charset="UTF-8">
            <title><?= $auto_details->modell ?></title>
        </head>

        <body>
            <h1><?= $auto_details->modell ?></h1>
            <div>
                <table>
                    <tr>
                        <th>Marke</th>
                        <td><?= $auto_details->marke ?></td>
                    </tr>
                    <tr>
                        <th>Baujahr</th>
                        <td><?= $auto_details->baujahr ?></td>
                    </tr>
                    <tr>
                        <th>Antriebsart</th>
                        <td><?= $auto_details->antriebsart ?></td>
                    </tr>
                    <tr>
                        <th>Leistung</th>
                        <td><?= $auto_details->ps ?> PS</td>
                    </tr>
                    <tr>
                        <th>Laufleistung</th>
                        <td><?= $auto_details->laufleistung ?> km</td>
                    </tr>
                    <tr>
                        <th>Erstzulassung</th>
                        <td><?= date('d.m.Y', strtotime($auto_details->erstzulassung)) ?></td>
                    </tr>
                    <tr>
                        <th>Preis</th>
                        <td><?= $auto_details->preis ?> €</td>
                    </tr>
                    <tr>
                        <th>Kontaktperson</th>
                        <td><?= $auto_details->vorname, $auto_details->nachname ?> </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td><?= $auto_details->telefonnummer ?></td>
                    <tr>
                        <th></th>
                        <td><a href = "mailto:<?= $auto_details->email ?>"><?= $auto_details->email ?></a></td>
                    </tr>
                    <tr>
                        <th>Beschreibung</th>
                        <td><?= $auto_details->beschreibung ?></td>
                    </tr>
                </table>
            </div>    
            <br><br>


        <?php else : ?>
            <p>Das Auto wurde nicht gefunden.</p>

        <?php endif; ?>
        <a href="index.php">Zurück zur Übersicht</a>
    </body>    
</html>
