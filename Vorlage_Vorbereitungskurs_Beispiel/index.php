<?php
require_once 'config.php';
require_once 'functions.php';

$conn = connectToDb();


closeDb($conn);
?>

<!DOCTYPE html>
<html>    
    <head>
        <meta charset="UTF-8">
        <title>Filmsuche</title>
    </head>
    
    <body>
        <h1>Filmsuche</h1>
        <div>
            <form action="suchergebnis.php?produktionsfirma=" method="GET">
                <label for="produktionsfirma">Suche Film nach Produktionsfirma: </label>
                <input type="text" name="produktionsfirma" required>
                <br>
                <br>
                <button type="submit">Suchen</button>
                <button type="button" onclick="location.href='navigation.php';">Abbrechen</button>
            </form>
        </div>       
    </body>    
</html>
