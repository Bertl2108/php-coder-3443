<?php
require_once 'functions.php';

$formData = [];
$qrcode = null;
$checkint = null;

// Nur wenn GET Request gesendet wurde, wird auch prozessiert
if (isGetRequest()) {

// Übergebene Daten aus GET-Request auslesen und in Array speichern
    $formData = [
        'anrede' => formFieldValueGET('anrede', ''),
        'vorname' => formFieldValueGET('vorname', ''),
        'nachname' => formFieldValueGET('nachname', ''),
        'svnr' => formFieldValueGET('svnr', '')
    ];

    // Variable für Überprüfung ob nur Numerische Werte enthalten sind
    $checkint = $formData['svnr'];

    //Wenn SVNR gefüllt und nur Ziffern enthalten sind, wird prozessiert, sonst Fehlermeldung ausgeben
    if ($checkint != '' && is_numeric($checkint) == true) {

        // Array aufspliten für Multiplikation    
        $svnr = str_split($formData['svnr']);
        //Kontroll Ausgabe
        echo var_dump($svnr) . '<br> <br>';

        //Multiplikation der einzelnen Ziffern
        $sum = ($svnr[0] * 3) + ($svnr[1] * 7) + ($svnr[2] * 9) + ($svnr[4] * 5) + ($svnr[5] * 8) + ($svnr[6] * 4) + ($svnr[7] * 2) + ($svnr[8] * 1) + ($svnr[9] * 6);
        //Kontroll Ausgabe
        echo var_dump($sum) . '<br> <br>';

        // Summe durch 11 dividieren
        $ergebnis = $sum / 11;
        //Kontroll Ausgabe
        echo var_dump($ergebnis) . '<br> <br>';
        
        //Split bei .
        $check = explode(".", $ergebnis);
        //Kontroll Ausgabe
        echo var_dump($check[0]) . '<br> <br>';

        //Modulu 11 berechnen
        $pruefziffer = $sum - ($check[0] * 11);
        //Kontroll Ausgabe
        echo var_dump($pruefziffer) . '<br> <br>';

        //Wenn die errechnete Prüfziffer ident der gegebenen ->
        if ($svnr[3] == $pruefziffer) {
            // -> Dann wird im QR-Code "Richtig" ausgegeben
            $qrcode = 'Richtig';
        } else {
            //-> sonst wird "Falsch" ausgegeben
            $qrcode = 'Falsch';
        }
        //Kontroll Ausgabe
        echo var_dump($qrcode) . '<br> <br>';
        
    } elseif ($checkint != '' && is_numeric($checkint) == false){
        echo 'Bitte geben Sie nur Zahlen im Feld SV-Nr ein!'; 
    }
}
?>

<!DOCTYPE html>
<html>    
    <head>
        <meta charset="UTF-8">
        <title>Sozialnummern Überprüfung</title>
    </head>

    <body>
        <h1>Sozialnummern Überprüfung</h1>
        <br>
        <div>
            <form action="index.php" method="GET">
                <div>
                    <label for="anrede">Anrede</label>
                    <br>
                    <select id="anrede" name="anrede" >
                        <option value="Frau" <?= $formData['anrede'] == 'Frau' ? 'selected' : '' ?>>Frau</option>
                        <option value="Herr" <?= $formData['anrede'] == 'Herr' ? 'selected' : '' ?>>Herr</option>
                    </select>
                </div>       
                <br>
                <div>
                    <label for="vorname">Vorname</label>
                    <br>
                    <input type="text" name="vorname" placeholder="Max"value="<?= $formData['vorname'] ?>">
                </div>
                <br>
                <div>
                    <label for="nachname">Nachname</label>
                    <br>
                    <input type="text" name="nachname" placeholder="Mustermann"value="<?= $formData['nachname'] ?>">
                </div>
                <br>
                <div>
                    <label for="svnr">SV-Nr</label>
                    <br>
                    <input type="text" minlength="10" maxlength="10" placeholder="4245010199" name="svnr" value="<?= $formData['svnr'] ?>">
                </div>
                <br>
                <br>
                <div>
                    <button type="submit">Prüfen</button>
                </div>
            </form>

            <?php if ($qrcode != '') : ?>
            <br>
                <img src="https://api.qrserver.com/v1/create-qr-code/?data=<?= $qrcode ?>&amp;size=175x175" />
            <?php endif; ?>

        </div>
    </body>    
</html>
