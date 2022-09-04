<?php
require_once 'config.php';
require_once 'functions.php';

// DB Connection aufbauen
$conn = connectToDb();

if (!$conn) {
    die('Es konnte keine DB-Verbindung hergestellt werden ' . $conn->connect_error);
}

// DB Verbindung beenden
$conn->close();
?>

<html>
    <head>
        <title>Tankstellenverwaltung - Kundensuche</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <h1>Suche nach Kundennummer</h1>
                       
        <div>
            <form action="suchergebnis.php" method="GET">
                <label for="kunde_id">Kundennummer:</label>
                <input type="text" name="kunde_id" required>
                
                <button type="submit">Suchen</button>                                
                <button type="reset">Leeren</button> <!-- TYPE = RESET OK? -> oder onclick? -->
            </form>
        </div>
        
        
    </body>
</html>
