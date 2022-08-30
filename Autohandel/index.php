<?php
require_once 'config.php';
require_once 'functions.php';

$conn = connectToDb();

$query = "SELECT auto.*, antriebsart.bezeichnung AS antriebsart, marke.name AS marke, baujahr.jahrgang AS baujahr, modell.bezeichnung AS modell "
        . "FROM auto "
        . "JOIN antriebsart ON auto.antriebsart_id = antriebsart.antriebsart_id "
        . "JOIN marke ON auto.marke_id = marke.marke_id "
        . "JOIN baujahr ON auto.baujahr_id = baujahr.baujahr_id "
        . "JOIN modell ON auto.modell_id = modell.modell_id ";

//echo var_dump($query) . '<br><br>';

$formData = [];

// Überprüfen ob ein GetRequest gesendet wurde
if (isGetRequest()) {
    // Übergebene Daten aus GET-Request auslesen und in Array speichern
    $formData = [
        'antriebsart_id' => formFieldValueGet('antriebsart_id', ''),
        'marke_id' => formFieldValueGet('marke_id', ''),
    ];
    if ($formData['antriebsart_id'] == '' && $formData['marke_id'] == '') {
        $query = $query . " ORDER BY auto.letzter_aktualisierungszeitpunkt DESC ";
        //echo var_dump($query) . '<br><br>';
        $stmt = $conn->prepare($query);
    } elseif (!$formData['antriebsart_id'] == '' && $formData['marke_id'] == '') {
        $query = $query . " WHERE auto.antriebsart_id = ? " . " ORDER BY auto.letzter_aktualisierungszeitpunkt DESC ";
        //echo var_dump($query) . '<br><br>';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $formData['antriebsart_id']);
    } elseif ($formData['antriebsart_id'] == '' && !$formData['marke_id'] == '') {
        $query = $query . " WHERE auto.marke_id = ? " . " ORDER BY auto.letzter_aktualisierungszeitpunkt DESC ";
        //echo var_dump($query) . '<br><br>';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $formData['marke_id']);
    } else {
        $query = $query . " WHERE auto.antriebsart_id = ? AND auto.marke_id = ? " . " ORDER BY auto.letzter_aktualisierungszeitpunkt DESC ";
        //echo var_dump($query) . '<br><br>';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $formData['antriebsart_id'], $formData['marke_id']);
    }
}

// Statement ausführen
$stmt->execute();

// Ergebnis des Statements in resultat speichern
$result = $stmt->get_result();

$autos = [];

// Anzahl der Reihen im Resultat überprüfen
if ($result && $result->num_rows > 0) {
// Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    while ($row = $result->fetch_object()) {
        $autos[] = $row;
    }
}

//
//
//
//Filter Werte
$query = "SELECT * FROM antriebsart";

$stmt = $conn->prepare($query);

// Statement ausführen
$stmt->execute();

// Ergebnis des Statements in resultat speichern
$result = $stmt->get_result();

$antriebsarten = [];

// Anzahl der Reihen im Resultat überprüfen
if ($result && $result->num_rows > 0) {
// Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    while ($row = $result->fetch_object()) {
        $antriebsarten[] = $row;
    }
}

//
//
//
// Filter Werte 2
$query = "SELECT * FROM marke";

$stmt = $conn->prepare($query);

// Statement ausführen
$stmt->execute();

// Ergebnis des Statements in resultat speichern
$result = $stmt->get_result();

$marken = [];

// Anzahl der Reihen im Resultat überprüfen
if ($result && $result->num_rows > 0) {
// Durchlaufen aller Datensätze und auslesen eines Datensatzes als assoziativen Arrays
    while ($row = $result->fetch_object()) {
        $marken[] = $row;
    }
}

closeDb($conn);
?>

<!doctype html>
<html>    
    <head>
        <meta charset="UTF-8">
        <title>Verfügbare Gebrauchtwagen</title>
    </head>

    <body>
        <h1>Verfügbare Gebrauchtwagen</h1>
        <div>
            <form action="index.php" method="GET"> 
                <label for="antriebsart_id">Nach Antriebsart</label>
                <select name="antriebsart_id" id="antriebsart_id"> 
                    <option value="" selected>Alle</option>
                    <?php foreach ($antriebsarten as $antriebsart) : ?>
                        <option value="<?= $antriebsart->antriebsart_id //= mitgegebener GET Request Wert ?>"
                            <?= ($formData['antriebsart_id']  == $antriebsart->antriebsart_id ? 'selected' : '') // gefilterter Wert vor selektieren ?>>
                            <?= $antriebsart->bezeichnung //= Ausgabewert im Dropdown?> 
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="marke_id">Marke</label>
                <select name="marke_id" id="marke_id"> 
                    <option value="" selected>Alle</option>                  
                    <?php foreach ($marken as $marke) : ?>
                        <option value="<?= $marke->marke_id //= mitgegebener GET Request Wert ?>"
                            <?= ($formData['marke_id']  == $marke->marke_id  ? 'selected' : '') // gefilterter Wert vor selektieren ?>>
                            <?= $marke->name  //= Ausgabewert im Dropdown?> 
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Filtern</button>
            </form>
        </div>

        <div>
            <?php if (count($autos) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Modell</th>
                        <th>Marke</th>
                        <th>Antriebsart</th>
                        <th>Bauajahr</th>
                        <th>Preis</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($autos as $auto): ?>
                        <tr>
                            <td><?= $auto->auto_id ?></td>
                            <td><?= $auto->modell ?></td>
                            <td><?= $auto->marke ?></td>
                            <td><?= $auto->antriebsart ?></td>
                            <td><?= $auto->baujahr ?></td>
                            <td><?= $auto->preis ?> €</td>
                            <td>
                                <a href="details.php?auto_id=<?= $auto->auto_id ?>">Details</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>  
            <?php else : ?>
            <p>Keine Autos gefunden.</p>
            <?php endif ; ?>
        </div>
    </body>    
</html>
