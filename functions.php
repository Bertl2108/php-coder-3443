<?php

function connectToDB(&$conn) {
    // Verbindung zur Datenbank aufbauen
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Überprüfen, ob die Verbindung zur Datenbank nicht aufgebaut werden konnte
    if (true <> $conn && !$conn) { //true <> $conn bedeutet das selbe wie !$conn
        // Ausführung beenden und Fehlermeldung ausgeben
        die('Es konnte keine DB-Verbindung hergestellt werden ' . $conn->connect_error);
    }

    // Überprüfen ob der Zeichensatz für die Verbindung zur DB nicht gesetzt werden konnte
    if (!$conn->set_charset(DB_CHARSET)) {
        die('Der Zeichensatz für die Verbindung zur DB konnte nicht gesetzt werden.');
    }

    // return $conn;
}

function closeDB(&$conn) {
    if ($conn) {
        // Datenbankverbindung schließen
        $conn->close();
    }
}

/**
 * Überprüft, ob der Request ein POST Request ist
 * @return bool
 */
function isPostRequest(): bool {
    return ($_SERVER['REQUEST_METHOD'] === 'POST');
}

/**
 * Überprüft, ob der Request ein GET Request ist
 * @return bool
 */
function isGetRequest(): bool {
    return ($_SERVER['REQUEST_METHOD'] === 'GET');
}

/**
 * Liest Parameterwert aus dem Formular aus oder gibt einen Standard-Wert zurück.
 * Zusätzlich kann der Wert auch getrimmt werden.
 * 
 * @param string $fieldName FeldName (Parametername)
 * @param type $defaultValue Standardwert
 * @param bool $doTrim Flag ob der Wert getrimmt werden soll
 * @return type
 */
function formFieldValue(string $fieldName, $defaultValue, bool $doTrim = true) {
    $value = (isset($_POST[$fieldName]) ? $_POST[$fieldName] : $defaultValue);

    if ($doTrim) {
        return trim($value);
    }
    return $value;
}

function formFieldValueGet(string $fieldName, $defaultValue, bool $doTrim = true) {
    $value = (isset($_GET[$fieldName]) ? $_GET[$fieldName] : $defaultValue);

    if ($doTrim) {
        return trim($value);
    }
    return $value;
}

/**
 * Funktion zum Validieren von Formulardaten
 * 
 * @param array $formData Formulardaten
 * @param array $validations Validierungen
 * @param array $valdationErrors Fehlermeldungen
 * @return bool
 */
function validate(array $formData, array $validations, array &$valdationErrors): bool {
    // Durchlaufe alle Felder und deren Validatoren
    foreach ($validations as $fieldName => $fieldValidations) {
        // Durchlaufe alle Validatoren eines einzelnen Feldes
        foreach ($fieldValidations as $validator => $validatorData) {
            switch ($validator) {
                case 'not_empty':
                    if (empty($formData[$fieldName])) {
                        $valdationErrors[$fieldName] = $validatorData['error_msg'];
                    }
                    break;

                case 'min_length':
                    if (strlen($formData[$fieldName]) < $validatorData['min']) {
                        $valdationErrors[$fieldName] = $validatorData['error_msg'];
                    }
                    break;
            }
        }
    }
    return (count($valdationErrors) == 0);
}
