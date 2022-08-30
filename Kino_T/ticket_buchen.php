<?php

require_once 'functions.php';

$conn = connectToDB();
$vorstellung = null;

if (isPostRequest()) {
    // Daten aus Formular auslesen
    $formData = [
        'vorstellung_id' => (int) formFieldValue('id', 0),
        'anzahl_tickets' => (int) formFieldValue('anzahl_tickets', 1),
        'vorname' => formFieldValue('vorname', ''),
        'nachname' => formFieldValue('nachname', ''),
        'email' => formFieldValue('email', ''),
        'telefonnummer' => formFieldValue('telefonnummer', ''),
    ];
    
    // TODO: Validierung der eigegebenen Formulardaten
    
    // Vorstellung anhand eines Parameters aus Datenbank auslesen (Vorstellung muss in der Zukunft liegen)
    $sql = "SELECT v.vorstellung_id, v.beginndatum, r.bezeichnung AS raum, r.sitzplaetze, f.titel, f.laufzeit, f.sprache "
     . "FROM vorstellungen AS v "
     . "JOIN raeume AS r ON r.raum_id = v.raum_id "
     . "JOIN filme AS f ON f.film_id = v.film_id "
     . "WHERE v.vorstellung_id = ? AND v.beginndatum > NOW()";

    // Prepared Statement erstellen
    $stmt = $conn->prepare($sql);

    // Parameter (Vorstellungs-ID) an Prepared Statement übergeben
    $stmt->bind_param('i', $formData['vorstellung_id']);

    // Prepared Statement ausführen
    $stmt->execute();
    
    // Resultat des Prepared Statements in der Variable $resultat ablegen
    $resultat = $stmt->get_result();
    
    // Wenn in der Variable $resultat ein korrektes Resultat (!= false) enthalten ist
    if ($resultat) {
        // Vorstellung als Objekt in die Variable $vorstellung ablegen
        $vorstellung = $resultat->fetch_object();
    }
    
    // Kunden in der Datenbank anhand der eingegebenen E-Mail Adresse suchen
    $sql = "SELECT * "
     . "FROM kunden "
     . "WHERE email LIKE ?";

    // Prepared Statement erstellen
    $stmt = $conn->prepare($sql);

    // Parameter (Email) an Prepared Statement übergeben
    $stmt->bind_param('s', $formData['email']);

    // Prepared Statement ausführen
    $stmt->execute();
    
    // Resultat des Prepared Statements in der Variable $resultat ablegen
    $resultat = $stmt->get_result();
    
    $kundeId = 0;
    
    // Wenn ein Datensatz gefunden wurde, dann holen wir uns diesen aus der DB raus
    if ($resultat && $resultat->num_rows == 1) {
        $kunde = $resultat->fetch_object();
        $kundeId = $kunde->kunde_id;
    } else {
        // Ansonsten muss der Kunde in der Datenbank angelegt werden
        $sql = "INSERT INTO kunden (vorname, nachname, email, telefonnummer) VALUES (?, ?, ?, ?)";
        
        // Prepared Statement erstellen
        $stmt = $conn->prepare($sql);

        // Parameter (Vorname, Nachname, Email & Telefonnummer) an Prepared Statement übergeben
        $stmt->bind_param('ssss', $formData['vorname'], $formData['nachname'], $formData['email'], $formData['telefonnummer']);
        
        // Prepared Statement ausführen
        $stmt->execute();
        
        // die ID des neu angelegten Kunden aus der Datenbank abfragen und in die Variable $kundeId ablegen
        $kundeId = $stmt->insert_id;
    }
    
    // Überprüfen der Anzahl der Tickets vom Kunden in der DB
    // Wenn er über die 4 Tickets kommen würde, dann dürfte diese Aktion nicht durchgeführt werden
    $sql = "SELECT COUNT(*) AS kunden_tickets FROM tickets "
         . "WHERE kunde_id = ? AND vorstellung_id = ?";
    
    // Prepared Statement erstellen
    $stmt = $conn->prepare($sql);

    // Parameter (KundeID & VorstellungID) an Prepared Statement übergeben
    $stmt->bind_param('ii', $kundeId, $formData['vorstellung_id']);
    
    // Prepared Statement ausführen
    $stmt->execute();

    // Resultat des Prepared Statements in der Variable $resultat ablegen
    $resultat = $stmt->get_result();

    $anzahlKundenTickets = $resultat->fetch_object()->kunden_tickets;
    
    if ((MAX_CUSTOMER_TICKETS - $anzahlKundenTickets) >= $formData['anzahl_tickets']) {
        // Tickets in der Datenbank einfügen
        $sql = "INSERT INTO tickets (kunde_id, vorstellung_id) VALUES (?, ?)";

        // Prepared Statement erstellen
        $stmt = $conn->prepare($sql);

        // Parameter (KundeID & VorstellungID) an Prepared Statement übergeben
        $stmt->bind_param('ii', $kundeId, $formData['vorstellung_id']);

        // Tickets anlegen
        for ($i = 0; $i < $formData['anzahl_tickets']; $i++) {
            $stmt->execute();
        }
        
        // DB Verbindung vor Weiterleitung schließen
        closeDB($conn);

        // Weiterleitung auf die Danke-Seite
        header('Location: ticket_buchen_danke.php');
    } else {
        $errorMessages['allgemein'][] = 'Sie können nur noch ' . (MAX_CUSTOMER_TICKETS - $anzahlKundenTickets) . ' Ticket(s) bestellen'; 
    }
    
} else if (isGetRequest()) {
    // Auslesen des Parameters id aus dem GET-Request und Konvertierung (int) in einen Integer (ganze Zahl)
    $vorstellungId = (int) getParameter('id', 0);
    
    $sql = "SELECT v.vorstellung_id, v.beginndatum, r.bezeichnung AS raum, r.sitzplaetze, f.titel, f.laufzeit, f.sprache "
     . "FROM vorstellungen AS v "
     . "JOIN raeume AS r ON r.raum_id = v.raum_id "
     . "JOIN filme AS f ON f.film_id = v.film_id "
     . "WHERE v.vorstellung_id = ? AND v.beginndatum > NOW()";

    // Prepared Statement erstellen
    $stmt = $conn->prepare($sql);

    // Parameter (VorstellungID) an Prepared Statement übergeben
    $stmt->bind_param('i', $vorstellungId);

    // Prepared Statement ausführen
    $stmt->execute();
    
    // Resultat des Prepared Statements in der Variable $resultat ablegen
    $resultat = $stmt->get_result();
    
    if ($resultat) {
        $vorstellung = $resultat->fetch_object();
    }
}

// Anzahl der freien Plätze ermitteln

$freiePlaetze = 0;
if ($vorstellung) {
    // Anzahl der bisher verkauften Tickets auslesen
    $sql = "SELECT COUNT(*) AS anzahl_verkaufte_tickets FROM tickets "
         . "WHERE vorstellung_id = ?";

    // Prepared Statement erstellen
    $stmt = $conn->prepare($sql);

    // Parameter (VorstellungID) an Prepared Statement übergeben
    $stmt->bind_param('i', $vorstellung->vorstellung_id);

    // Prepared Statement ausführen
    $stmt->execute();

    // Resultat des Prepared Statements in der Variable $resultat ablegen
    $resultat = $stmt->get_result();

    $anzahlVerkaufteTickets = $resultat->fetch_object()->anzahl_verkaufte_tickets;
    $freiePlaetze = $vorstellung->sitzplaetze - $anzahlVerkaufteTickets;
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
        <title>Ticket kaufen</title>
    </head>
    <body>
        <h1>Ticket kaufen</h1>
        <?php if ($vorstellung): ?>
            <h2>Film: <?= $vorstellung->titel ?></h2>
            <p>
                Beginn: <?= date('d.m.Y H:i', strtotime($vorstellung->beginndatum)) ?>
            </p>
            
            <?php if (isset($errorMessages['allgemein'])): ?>
                <?php foreach($errorMessages['allgemein'] as $errorMessage): ?>
                    <div class="error-message"><?= $errorMessage; ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <?php if ($freiePlaetze > 0): ?>
                <form action="ticket_buchen.php" method="POST">
                    <div class="form-field">
                        <label for="anzahl_tickets">Anzahl Tickets*</label>
                        <select id="anzahl_tickets" name="anzahl_tickets" required>
                            <?php for($i = 1; $i <= min(4, $freiePlaetze); $i++): ?>
                                <option><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-field">
                        <label for="vorname">Vorname*</label>
                        <input type="text" id="vorname" name="vorname" value="" required>
                    </div>

                    <div class="form-field">
                        <label for="nachname">Nachname*</label>
                        <input type="text" id="nachname" name="nachname" value="" required>
                    </div>

                    <div class="form-field">
                        <label for="email">Email*</label>
                        <input type="email" id="email" name="email" value="" required>
                    </div>

                    <div class="form-field">
                        <label for="telefonnummer">Telefonnummer</label>
                        <input type="text" id="telefonnummer" name="telefonnummer" value="">
                    </div>

                    <input type="hidden" name="id" value="<?= $vorstellung->vorstellung_id ?>">

                    <button type="button" onclick="location.href='index.php'">Zurück</button>
                    <button type="submit">Kaufen</button>
                </form>
            <?php else: ?>
                <p>Die Vorstellung ist bereits ausgebucht</p>
            <?php endif; ?>
        <?php else: ?>
            <p>Die Vorstellung ist nicht mehr verfügbar</p>
        <?php endif; ?>
    </body>
</html>