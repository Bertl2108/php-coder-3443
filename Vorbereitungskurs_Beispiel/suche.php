<?php
require_once 'config.php';
require_once 'functions.php';

$conn = connectToDb();

// put your code here
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
            <form action="suchergebnis.php" method="GET">
                <label for="produktionsfirma">Suche Film nach Produktionsfirma: </label>
                <input type="text" name="produktionsfirma" id="produktionsfirma" required>
                <br>
                <br>
                <button type="submit">Suchen</button>
                <button type="reset"">Abbrechen</button>
            </form>
        </div> 
        <br>
        <br>
        <a href="einstieg.php">Startseite</a>
    </body>    
</html>