<?php    
    function connectToDB() {
        // Verbindung zur Datenbank aufbauen
        $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        // Überprüfen, ob die Verbindung zur Datenbank nicht aufgebaut werden konnte
        if (!$conn) {
            // Ausführung beenden und Fehlermeldung ausgeben
            die('Es konnte keine DB-Verbindung hergestellt werden ' . mysqli_connect_error());
        }

        // Überprüfen ob der Zeichensatz für die Verbindung zur DB nicht gesetzt werden konnte
        if (!mysqli_set_charset($conn, 'utf8mb4')) {
            die('Der Zeichensatz für die Verbindung zur DB konnte nicht gesetzt werden.');
        }
        
        return $conn;
    }
    
    function closeDB(&$conn) {
        if ($conn) {
            // Datenbankverbindung schließen
            mysqli_close($conn);
        }
    }
    
    /**
     * Überprüft, ob der Request ein POST Request ist
     * @return bool
     */
    function isPostRequest() : bool {
        return ($_SERVER['REQUEST_METHOD'] === 'POST');
    }
    
    /**
     * Überprüft, ob der Request ein GET Request ist
     * @return bool
     */
    function isGetRequest() : bool {
        return ($_SERVER['REQUEST_METHOD'] === 'GET');
    }
    
    /**
     * Liest Parameterwert aus dem POST Formular aus oder gibt einen Standard-Wert zurück.
     * Zusätzlich kann der Wert auch getrimmt werden.
     * 
     * @param string $fieldName FeldName (Parametername)
     * @param type $defaultValue Standardwert
     * @param bool $doTrim Flag ob der Wert getrimmt werden soll
     * @return type
     */
    function formFieldValuePOST(string $fieldName, $defaultValue, bool $doTrim = true) {
        $value = (isset($_POST[$fieldName]) ? $_POST[$fieldName] : $defaultValue);
        
        if ($doTrim) {
            return trim($value);
        }
        return $value;
    }
    
    /**
     * Liest Parameterwert aus dem GET Formular aus oder gibt einen Standard-Wert zurück.
     * Zusätzlich kann der Wert auch getrimmt werden.
     * 
     * @param string $fieldName FeldName (Parametername)
     * @param type $defaultValue Standardwert
     * @param bool $doTrim Flag ob der Wert getrimmt werden soll
     * @return type
     */
    function formFieldValueGET(string $fieldName, $defaultValue, bool $doTrim = true) {
        $value = (isset($_GET[$fieldName]) ? $_GET[$fieldName] : $defaultValue);
        
        if ($doTrim) {
            return trim($value);
        }
        return $value;
    }