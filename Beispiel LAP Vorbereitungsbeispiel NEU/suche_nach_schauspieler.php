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
            <form action="suchergebnis_schauspieler.php" method="GET">
                <label for="schauspieler">Suche Film nach Schauspieler:</label>
                <input type="text" name="schauspieler" required>
                <br><br><br>
                <button type="submit">Suchen</button>                                
                <button type="reset">Abbrechen</button> <!-- TYPE = RESET OK? -> oder onclick? -->
            </form>            
        </div>  
                <br>
        <div>
            <a href="navigation.php">Startseite</a>
            <br>
            <a href="suche.php">Suche Filme nach Produktionsfirma</a>
        </div>
    </body>    
</html>