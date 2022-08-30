<?php
require_once 'config.php';
require_once 'functions.php';

$conn = connectToDb();

$query = "SELECT filme.*, vorstellungen.beginnzeit AS beginnzeit "
        . "FROM vorstellungen "
        . "JOIN filme ON vorstellungen.film_id = filme.film_id "
        . "WHERE vorstellungen.vorstellung_id = ? ";

// Überprüfen ob ein GetRequest gesendet wurde
if (isGetRequest()) {
    // Übergebene Daten aus GET-Request auslesen und in Array speichern
    $formData = [
        'vorstellung_id' => formFieldValueGet('vorstellung_id', ''),
    ];
}

echo var_dump($query);

$stmt = $conn->prepare($query);

// Parameter (Vorstellungs-ID) an Prepared Statement übergeben
$stmt->bind_param('i', $formData['vorstellung_id']);

// Statement ausführen
$stmt->execute();

// Ergebnis des Statements in resultat speichern
$result = $stmt->get_result();

$film = null;
// Wenn in der Variable $resultat ein korrektes Resultat (!= false) enthalten ist
if ($result) {
    // Vorstellung als Objekt in die Variable $vorstellung ablegen
    $film = $result->fetch_object();
}


if (isPostRequest()) {
    // Daten aus Formular auslesen
    $formData = [
        'vorstellung_id' => (int) formFieldValuePOST('id', 0),
        'anzahl_tickets' => (int) formFieldValuePOST('anzahl_tickets', 1),
        'vorname' => formFieldValuePOST('vorname', ''),
        'nachname' => formFieldValuePOST('nachname', ''),
        'email' => formFieldValuePOST('email', ''),
        'telefon' => formFieldValuePOST('telefon', ''),
    ];

    // Ansonsten muss der Kunde in der Datenbank angelegt werden
    $query = "INSERT INTO kunden (vorname, nachname, email, telefon) VALUES (?, ?, ?, ?)";

    // Prepared Statement erstellen
    $stmt = $conn->prepare($query);

    // Parameter (Vorname, Nachname, Email & Telefonnummer) an Prepared Statement übergeben
    $stmt->bind_param('ssss', $formData['vorname'], $formData['nachname'], $formData['email'], $formData['telefon']);

    // Prepared Statement ausführen
    $stmt->execute();

    // Weiterleitung auf die Danke-Seite
    header('Location: ticket_buchen_danke.php');
}

// put your code here
closeDb($conn);
?>

<!DOCTYPE html>
<html>    
    <head>
        <meta charset="UTF-8">
        <title>Ticket kaufen</title>
    </head>

    <body>
        <h1>Ticket kaufen</h1>
        <h2>Film: <?= $film->titel ?></h2>
        <p>Beginn: <?= date('H:i', strtotime($film->beginnzeit)) ?> Uhr</p>

        <div>
            <form action="ticket_buchen.php" method="POST">

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
                    <input type="text" id="email" name="email" value="" required>
                </div>
                <div class="form-field">
                    <label for="telefon">Telefonnummer</label>
                    <input type="text" id="telefon" name="telefon" value="" >
                </div>
                <br>
                <button type="button" onclick="location.href = 'index.php'">Zurück</button>
                <button type="submit">Kaufen</button>

            </form>    
        </div>       
    </body>    
</html>