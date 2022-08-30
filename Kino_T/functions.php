<?php

require_once 'config.php';

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
 * @param \mysqli $conn
 */
function closeDB(\mysqli $conn) {
    $conn->close();
}

/**
 * Überprüft ob ein Request ein GET-Request ist.
 * 
 * @return bool
 */
function isGetRequest() : bool {
    return ($_SERVER['REQUEST_METHOD'] === 'GET');
}

/**
 * Überprüft ob ein Request ein POST-Request ist.
 * 
 * @return bool
 */
function isPostRequest() : bool {
    return ($_SERVER['REQUEST_METHOD'] === 'POST');
}

/**
 * Holt einen Parameter aus einem GET-Request.
 * Wenn der Parameter nicht vorhanden ist, wird der Standard-Wert zurück gegeben.
 * 
 * @param string $name Parameter-Name
 * @param $defaultValue Standard-Wert
 * @return type
 */
function getParameter(string $name, $defaultValue = '') {
    return (isset($_GET[$name]) ? $_GET[$name] : $defaultValue);
}

/**
 * Holt einen Parameter aus einem POST-Request.
 * Wenn der Parameter nicht vorhanden ist, wird der Standard-Wert zurück gegeben.
 * 
 * @param string $name
 * @param $defaultValue
 * @return type
 */
function formFieldValue(string $name, $defaultValue = '') {
    return (isset($_POST[$name]) ? $_POST[$name] : $defaultValue);
}