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
        <title>Filmsuche - Navigation</title>
    </head>

    <body>
        <h1>Filmsuche - Navigation</h1>
        <div>
            <nav>
                <ul>
                    <li><a href="index.php">Suche Filme nach Produktionsfirma</a></li>
                    <li><a href="suche_nach_schauspieler.php">Suche Filme nach Schauspieler</a></li>
                    <li><a href="navigation.php">Navigation</a></li>
                </ul>
            </nav>
        </div>       
    </body>    
</html>
