<?php

require_once 'functions.php';

$conn = connectToDB();

$sql = "SELECT v.vorstellung_id, v.beginndatum, r.bezeichnung AS raum, r.sitzplaetze, f.titel, f.laufzeit, f.sprache, r.sitzplaetze - IFNULL(count(t.ticket_id), 0) AS verfuegbare_plaetze "
     . "FROM vorstellungen AS v "
     . "JOIN raeume AS r ON r.raum_id = v.raum_id "
     . "JOIN filme AS f ON f.film_id = v.film_id "
     . "LEFT JOIN tickets AS t ON t.vorstellung_id = v.vorstellung_id "
     . "WHERE v.beginndatum > NOW() "   // vergangene Vorstellungen ausschließen
     . "GROUP BY v.vorstellung_id "
     . "ORDER BY v.beginndatum ASC";    // sortieren nach Beginndatum

// Prepared Statement erstellen
$stmt = $conn->prepare($sql);

// wenn Parameter enthalten wären müsste hier noch ein $stmt->bind_param($sql, $format) eingefügt werden

// Prepared Statement ausführen
$stmt->execute();
// Ergebnis des Prepared Statements in Variable $result ablegen
$resultat = $stmt->get_result();

$vorstellungen = [];

if ($resultat) {
    while ($eintrag = $resultat->fetch_object()) {
        $vorstellungen[] = $eintrag;
    }
}

closeDB($conn);

?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Film Vorstellungen</title>
    </head>
    <body>
        <h1>Film Vorstellungen</h1>
        
        <?php if (count($vorstellungen) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Film</th>
                        <th>Raum</th>
                        <th>Beginn</th>
                        <th>Plätze</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vorstellungen as $vorstellung): ?>
                        <tr>
                            <td><?= $vorstellung->titel ?></td>
                            <td><?= $vorstellung->raum ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($vorstellung->beginndatum)) ?></td>
                            <td><?= $vorstellung->verfuegbare_plaetze ?></td>
                            <td>
                                <?php if ($vorstellung->verfuegbare_plaetze > 0): ?>
                                    <a href="ticket_buchen.php?id=<?= $vorstellung->vorstellung_id ?>">
                                        buchen
                                    </a>
                                <?php else: ?>
                                    ausgebucht
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Keine Vorstellungen gefunden.</p>
        <?php endif; ?>
    </body>
</html>
