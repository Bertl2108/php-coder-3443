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
        <title>Navigation Filmverwaltung</title>
    </head>
    
    <body>
        <h1>Navigation Filmverwaltung</h1>
        <div>
            <nav>
                <ul>
                    <li><a href="navigation.php">Startseite</a></li>
                    <li><a href="suche.php">Suche Filme nach Produktionsfirma</a></li>
                    <li><a href="suche_nach_schauspieler.php">Suche Filme nach Schauspieler</a></li>
                </ul>
            </nav>
        </div>       
    </body>    
</html>