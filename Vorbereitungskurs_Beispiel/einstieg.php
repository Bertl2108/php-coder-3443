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
        <title>Filmverwaltung</title>
    </head>
    
    <body>
        <h1>Filmverwaltung</h1>
        <div>
            <nav>
                <ul>
                    <li><a href="suche.php">Filmsuche</a></li>
                    <li><a href="suche_schauspieler.php">Filmsuche nach Schauspieler</a></li>
                    <li><a href="einstieg.php">Startseite</a></li>
                </ul>

            </nav>
        </div>       
    </body>    
</html>