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
                <label for="produktionsfirma">Suche Film nach Produktionsfirma:</label>
                <input type="text" name="produktionsfirma" required>
                <br>                <br>                <br>
                <button type="submit">Suchen</button>                                
                <button type="reset">Abbrechen</button> 
            </form>            
        </div>     
        <br>
        <div>
            <a href="navigation.php">Startseite</a>
            <br>
            <a href="suche_nach_schauspieler.php">Suche Filme nach Schauspieler</a>
        </div>
    </body>    
</html>
