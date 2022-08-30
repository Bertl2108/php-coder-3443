<?php
require_once 'config.php';
require_once 'functions.php';

connectToDB($conn);

/*
 * 
 * ------Autos holen---------
 * 
 */

// SQL Statement
$query = "SELECT autos.*, marken.bezeichnung as marke, antriebsarten.bezeichnung as antriebsart FROM autos "
        . "JOIN marken ON autos.marke_id = marken.marke_id "
        . "JOIN antriebsarten ON autos.antriebsart_id = antriebsarten.antriebsart_id ";

if (isGetRequest()) {

//    // Übergebene Daten aus GET-Request auslesen und in Array speichern
//    $formData = [
//        'antriebsartid' => formFieldValueGet('antriebsartid', 'all'),
//        'markeid' => formFieldValueGet('markeid', 'all'),
//    ];
//
//    if ($formData['antriebsartid'] == 'all' && $formData['markeid'] != 'all') {
//        $formData['antriebsartid'] = null;
//    }
//    if ($formData['markeid'] == 'all' && $formData['antriebsartid'] != 'all')  {
//        $formData['markeid'] = null;
//    }
//    if ($formData['antriebsartid'] == 'all' && $formData['markeid'] == 'all') {
//        $query = $query . "ORDER BY autos.aktualisierungszeitpunkt DESC ";
//        $stmt = $conn->prepare($query);
//    } else {
//        $query = $query . " WHERE autos.antriebsart_id = ? AND autos.marke_id = ?" . " ORDER BY autos.aktualisierungszeitpunkt DESC ";
//        echo var_dump($query) . '<br><br>';
//        $stmt = $conn->prepare($query);
//        $stmt->bind_param('ii', $formData['antriebsartid'], $formData['markeid']);
//    }
    // Übergebene Daten aus GET-Request auslesen und in Array speichern
    $formData = [
        'antriebsartid' => formFieldValueGet('antriebsartid', 'all'),
        'markeid' => formFieldValueGet('markeid', 'all'),
    ];

    if ($formData['antriebsartid'] == 'all' && $formData['markeid'] == 'all') {
        $query = $query . "ORDER BY autos.aktualisierungszeitpunkt DESC ";
        $stmt = $conn->prepare($query);
    } elseif ($formData['antriebsartid'] <> 'all' && $formData['markeid'] == 'all') {
        $query = $query . " WHERE autos.antriebsart_id = ? " . " ORDER BY autos.aktualisierungszeitpunkt DESC ";
        echo var_dump($query) . '<br><br>';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $formData['antriebsartid']);
    } elseif ($formData['markeid'] <> 'all' && $formData['antriebsartid'] == 'all') {
        $query = $query . " WHERE autos.marke_id = ? " . " ORDER BY autos.aktualisierungszeitpunkt DESC ";
        echo var_dump($query) . '<br><br>';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $formData['markeid']);
    } else {
        $query = $query . " WHERE (autos.antriebsart_id = ? AND autos.marke_id = ?)" . " ORDER BY autos.aktualisierungszeitpunkt DESC ";
        echo var_dump($query) . '<br><br>';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $formData['antriebsartid'], $formData['markeid']);
    }
}

// Überprüfen ob ein GetRequest gesendet wurde

echo var_dump($query) . '<br><br>';

// Statement ausführen
$stmt->execute();

// Ergebnis des Statements in resultat speichern
$result = $stmt->get_result();

// leeren Arrays erzeugen
$autos = [];
$marken = [];
$antriebsart = [];
$noResults = false;

// Anzahl der Reihen im Resultat überprüfen
if ($result && $result->num_rows > 0) {
    // Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    while ($row = $result->fetch_object()) {
        $autos[] = $row;
        $marken[$row->marke_id] = $row->marke;
        $antriebsarten[$row->antriebsart_id] = $row->antriebsart;
    }
} else {
    $noResults = true;
}

var_dump($marken);
var_dump($antriebsart);
closeDB($conn);
?>


<html>
    <head>
        <meta charset="UTF-8">
        <title>Verfügbare Gebrauchtwagen</title>
    </head>
    <body>
        <h1>Verfügbare Gebrauchtwagen</h1>

        <div>
            <form action="index.php" method="GET">
                <label>Nach Antriebsart</label>
                <select name="antriebsartid" id="antriebsartid">
                    <option value="all">Alle</option>
                    <?php foreach (array_unique($antriebsarten) as $antriebsart): ?>
                        <option value = "<?= $antriebsart ?>" <?= ($formData['antriebsartid'] == $auto->antriebsart_id ? 'selected' : '')?>>
                            <?= $auto->antriebsart ?></option>
                    <?php endforeach; ?> 
                </select>
                <label>Marke</label>
                <select name="markeid" id="markeid">
                    <option value="all" >Alle</option>
                    <?php foreach (array_unique($marken) as $marke): ?>
                        <option value="<?= $auto->marke_id ?>" <?= ($formData['markeid'] == $auto->marke_id ? 'selected' : '') ?>><?= $auto->marke ?></option>
                    <?php endforeach; ?> 
                </select>
                <button type="submit">filtern</button> 
            </form>
        </div>

        <?php if (!$noResults) : ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Modell</th>
                        <th>Marke</th>
                        <th>Antriebsart</th>
                        <th>Baujahr</th>
                        <th>Preis</th>
                        <th><?php "&nbsp;" ?></th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($autos as $auto): ?>
                        <tr>
                            <td><?php echo $auto->auto_id ?></td>
                            <td><?php echo $auto->modelbezeichnung ?></td>
                            <td><?php echo $auto->marke ?></td>
                            <td><?php echo $auto->antriebsart ?></td>
                            <td><?php echo $auto->baujahr ?></td>
                            <td><?php echo number_format($auto->preis, 2, ',', '.') ?></td>
                            <td><a href="details.php?id=<?php echo $auto->auto_id ?>">Details</a></td>                      
                        </tr>
                    <?php endforeach; ?>
                </tbody> 
            </table>
        <?php else: ?>
            <p>Keine Autos gefunden!</p>
        <?php endif; ?>
    </body>
</html>
