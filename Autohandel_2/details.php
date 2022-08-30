<?php
require_once 'config.php';
require_once 'functions.php';

connectToDB($conn);

// Überprüfen ob ein GetRequest gesendet wurde
if (isGetRequest()) {
    // Übergebene Daten aus GET-Request auslesen und in Array speichern
    $formData = [
        'autoid' => formFieldValueGet('id', ''),
    ];
}

// SQL Statement
$query = "SELECT autos.*, marken.bezeichnung as marke, antriebsarten.bezeichnung as antriebsart, leistungen.leistung_ps as leistung, mitarbeiter.* FROM autos "
        . "JOIN marken ON autos.marke_id = marken.marke_id "
        . "JOIN antriebsarten ON autos.antriebsart_id = antriebsarten.antriebsart_id "
        . "JOIN leistungen ON autos.leistung_id = leistungen.leistung_id "
        . "JOIN mitarbeiter ON autos.mitarbeiter_id = mitarbeiter.mitarbeiter_id "
        . "WHERE autos.auto_id = ? ";

echo var_dump($query) . '<br><br>';

// Prepared Statement erstellen
$stmt = $conn->prepare($query);

// Parameter binden
$stmt->bind_param('i', $formData['autoid']);

// Statement ausführen
$stmt->execute();

// Ergebnis des Statements in resultat speichern
$result = $stmt->get_result();

// leeren Array erzeugen
$autodetails = null;
$noResults = false;

// Anzahl der Reihen im Resultat überprüfen
if ($result && $result->num_rows == 1) {
    // Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    $autodetails = $result->fetch_object();
} else {
    $noResults = true;
}

closeDB($conn);
?>

<html>
    <head>
        <title><?php echo $autodetails->modelbezeichnung ?></title>
        <meta charset="UTF-8">
    </head>

    <?php if (!$noResults) : ?>
        <body>
            <h1><?php echo $autodetails->modelbezeichnung ?></h1>

            <table style="text-align: left">
                <tbody>
                    <tr>
                        <th>Marke</th>
                        <td><?php echo $autodetails->marke ?></td>
                    </tr>
                    <tr>
                        <th>Baujahr</th>
                        <td><?php echo $autodetails->baujahr ?></td>
                    </tr>
                    <tr>
                        <th>Antriebsart</th>
                        <td><?php echo $autodetails->antriebsart ?></td>
                    </tr>
                    <tr>
                        <th>Leistung</th>
                        <td><?php echo $autodetails->leistung . ' ' . 'PS'?></td>
                    </tr>
                    <tr>
                        <th>Laufleistung</th>
                        <td><?php echo $autodetails->laufleistung ?> km</td>
                    </tr>
                    <tr>
                        <th>Erstzulassung</th>
                        <td><?php echo date("d.m.Y", strtotime($autodetails->erstzulassung)) ?></td>
                    </tr>
                    <tr>
                        <th>Preis</th>
                        <td><?php echo number_format($autodetails->preis, 2, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <th>Kontaktperson<br><br><br></th>
                        <td><?php echo $autodetails->vorname . ' ' . $autodetails->nachname ?><br><?php echo $autodetails->telefonnummer ?><br><a href="mailto:<?php echo $autodetails->email ?>"><?php echo $autodetails->email ?></a></td> 
                    </tr>
                    <tr>
                        <th>Beschreibung<br><br><br></th>
                        <td><?php echo $autodetails->beschreibung ?></td>
                    </tr>
                </tbody>
            </table>      
        </body>

        <br>

    <?php else: ?>
        <p>Das Auto wurde nicht gefunden!</p>
    <?php endif; ?>

    <nav><a href="index.php">Zurück zur Übersicht</a></nav>

</html>