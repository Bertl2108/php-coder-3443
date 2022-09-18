<?php
require_once 'functions.php';

// Hier kein Query weil wir nix mit SQL machen!!!!
$formData = [];

if (isGetRequest()) {
// Übergebene Daten aus GET-Request auslesen und in Array speichern
    $formData = [
        'anrede' => formFieldValueGET('anrede', ''),
        'vorname' => formFieldValueGET('vorname', ''),
        'nachname' => formFieldValueGET('nachname', ''),
        'svnr' => formFieldValueGET('svnr', '')
    ];
}

$svnr = str_split($formData['svnr']);

echo var_dump($svnr) . '<br> <br>';

$sum = ($svnr[0] * 3) + ($svnr[1] * 7) + ($svnr[2] * 9) + ($svnr[3] * 5) + ($svnr[4] * 8) + ($svnr[5] * 4) + ($svnr[6] * 2) + ($svnr[7] * 1) + ($svnr[8] * 6);

echo var_dump($sum) . '<br> <br>';

$ergebnis = [];

$ergebnis = $sum / 11;

echo var_dump($ergebnis) . '<br> <br>';

$ergebnispruefziffer = ;

echo var_dump($ergebnispruefziffer) . '<br> <br>';

$pruefsumme = null;

$pruefsumme = $ergebnispruefziffer[1] && $ergebnispruefziffer[2];

echo var_dump($pruefsumme) . '<br> <br>';

$pruefsumme = $ergebnis[2] * 11;

$pruefziffer = $sum - $ergebnispruefziffer;
echo var_dump($pruefziffer) . '<br> <br>';

$qrcode = null;

If ($pruefziffer && $pruefziffer < 10) {
    $qrcode = 'Richtig';
} else {
    $qrcode = 'Falsch';
};


echo var_dump($qrcode) . '<br> <br>';

//Die Ziffernfolge wird von links nach rechts mit 3, 7, 9, 5, 8, 4, 2, 1, 6 gewichtet.
//Die Produkte werden summiert.
//Von der Summe wird der volle Rest zur nächst niedrigeren durch 11 teilbaren Zahl (modulo 11) bestimmt.
//Ist der Rest 10, wird die Nummer nicht vergeben.
?>

<!DOCTYPE html>
<html>    
    <head>
        <meta charset="UTF-8">
        <title>SV NR Prüfung</title>
    </head>

    <body>
        <h1>SV Nr Prüfung</h1>
        <div>
            <form action="index.php" method="GET"> <?php // Hier GET Request!!!!       ?>
                <label for="anrede">Anrede</label>
                <br>




                <select name="anrede">
                    <option value="">Keine Angabe</option>
                    <option value="Herr"
                            <?= $formData['anrede'] == 'Herr' ? 'selected' : '' ?>>
                        Herr</option>
                    <option value="Frau"
                            <?= $formData['anrede'] == 'Frau' ? 'selected' : '' ?>>
                        Frau</option>
                </select>

                <br>
                <br>
                <label for="vorname">Vorname</label>
                <br>
                <input  for="vorname" name="vorname" value="<?= $formData['vorname'] ?>"></input>
                <br>
                <br>
                <label for="nachname">Nachname</label>
                <br>
                <input for="nachname" name="nachname" value="<?= $formData['nachname'] ?>"></input>
                <br>
                <br>
                <label for="svnr">SV-Nummer</label>
                <br>
                <input for="svnr" name="svnr" value="<?= $formData['svnr'] ?>"></input>
                <br>
                <br>
                <button type="submit">Prüfen</button>
            </form>
        </div>   

    </body>    
</html>
