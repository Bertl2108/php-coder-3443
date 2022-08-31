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
        <h1>Suche nach Schauspieler</h1>
        <div>
            <form action="suchergebnis_schauspieler.php" method="GET">
                <label for="schauspieler">Suche Film nach Schauspieler: </label>
                <input type="text" name="schauspieler" required>
                <br>
                <br>
                <button type="submit">Suchen</button>
                <button type="reset"">Abbrechen</button>
            </form>
        </div> 
        <br>
        <a href="einstieg.php">Startseite</a>
    </body>    
</html>