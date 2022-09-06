<?php

/**
 * Erstellt eine Datenbankverbindung
 * 
 * @return \mysqli
 */
function connectToDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    
    if (!$conn) {
        die('Es konnte keine Verbindung zur Datenbank erstellt werden.');
    }
    
    $conn->set_charset(DB_CHARSET);
    
    return $conn;
}

/**
 * Schließt eine Datenbankverbindung
 * 
 * @param $conn
 */
function closeDB($conn) {
    $conn->close();
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
 * Holt einen Parameter aus einem POST-Request.
 * Wenn der Parameter nicht vorhanden ist, wird der Standard-Wert zurück gegeben.
 * 
 * @param string $name
 * @param $defaultValue
 * @return type
 */
function formFieldValuePost(string $name, $defaultValue = '') {
    return (isset($_POST[$name]) ? $_POST[$name] : $defaultValue);
}

/**
 * Holt einen Parameter aus einem GET-Request.
 * Wenn der Parameter nicht vorhanden ist, wird der Standard-Wert zurück gegeben.
 * 
 * @param string $name Parameter-Name
 * @param $defaultValue Standard-Wert
 * @return type
 */
function formFieldValueGET(string $name, $defaultValue = '') {
    return (isset($_GET[$name]) ? $_GET[$name] : $defaultValue);
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