<?php
require_once 'config.php';
require_once 'functions.php';

$conn = connectToDb();

if (isPostRequest()) {
    // Daten aus Formular auslesen
    $formData = [
        'vorstellung_id' => (int) formFieldValuePOST('id', 0),
        'vorname' => formFieldValuePOST('vorname', ''),
        'nachname' => formFieldValuePOST('nachname', ''),
        'email' => formFieldValuePOST('email', ''),
        'telefonnummer' => formFieldValuePOST('telefonnummer', ''),
    ];

    $query = "SELECT filme.*, vorstellungen.beginnzeit AS beginnzeit "
            . "FROM vorstellungen "
            . "JOIN filme ON vorstellungen.film_id = filme.film_id "
            . "WHERE vorstellungen.beginnzeit < NOW() "
            . "AND vorstellungen.vorstellung_id = ? ";

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
        $sql = "INSERT INTO kunden (vorname, nachname, email, telefon) VALUES (?, ?, ?, ?)";

        // Prepared Statement erstellen
        $stmt = $conn->prepare($sql);

        // Parameter (Vorname, Nachname, Email & Telefonnummer) an Prepared Statement übergeben
        $stmt->bind_param('ssss', $formData['vorname'], $formData['nachname'], $formData['email'], $formData['telefonnummer']);

        // Prepared Statement ausführen
        $stmt->execute();

        // die ID des neu angelegten Kunden aus der Datenbank abfragen und in die Variable $kundeId ablegen
        $kundeId = $stmt->insert_id;
    }
    // Tickets in der Datenbank einfügen
    $sql = "INSERT INTO tickets (kunde_id, vorstellung_id) VALUES (?, ?)";

    // Prepared Statement erstellen
    $stmt = $conn->prepare($sql);

    // Parameter (KundeID & VorstellungID) an Prepared Statement übergeben
    $stmt->bind_param('ii', $kundeId, $formData['vorstellung_id']);

    // Tickets anlegen
    $stmt->execute();

    // DB Verbindung vor Weiterleitung schließen
    closeDB($conn);

    // Weiterleitung auf die Danke-Seite
    header('Location: ticket_buchen_danke.php');
    
} else if (isGetRequest()) {
    // Auslesen des Parameters id aus dem GET-Request und Konvertierung (int) in einen Integer (ganze Zahl)
    $vorstellungId = (int) formFieldValueGET('id', 0);

    $query = "SELECT filme.*, vorstellungen.beginnzeit AS beginnzeit "
            . "FROM vorstellungen "
            . "JOIN filme ON vorstellungen.film_id = filme.film_id "
            . "WHERE vorstellungen.beginnzeit < NOW() "
            . "AND vorstellungen.vorstellung_id = ? ";

    // Prepared Statement erstellen
    $stmt = $conn->prepare($query);

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
        <div>
            <h1>Ticket kaufen</h1>
            <h2>Film: <?= $vorstellung->titel ?> </h2> 
            <p><b>Beginn:</b><?= $vorstellung->beginnzeit ?></p>

            <form action="ticket_buchen.php" method="POST">
                <div>
                    <label for="vorname"><b>Vorname*</b></label>
                    <input type="text" name="vorname" required></input>                
                </div>
                <div>
                    <label for="nachname"><b>Nachname*</b></label>
                    <input type="text" name="nachname" required></input>                
                </div>
                <div>
                    <label for="email"><b>Email*</b></label>
                    <input type="email" name="email" required></input>                
                </div>
                <div>
                    <label for="telefonnummer"><b>Telefonnummer</b></label>
                    <input type="text" name="telefonnummer"></input>                
                </div>
                <div>
                    <input type="hidden" name="id" value="<?= $vorstellung->vorstellung_id ?>">
                </div>
                <br>
                <button type="button" onclick="location.href = 'index.php';">Zurück</button>
                <button type="submit">Kaufen</button>
            </form>
        </div>       
    </body>    
</html>
