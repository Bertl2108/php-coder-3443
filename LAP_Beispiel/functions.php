<?php
/**
 * Überprüft, ob der Request ein GET Request ist
 * @return bool
 */
function isGetRequest(): bool {
    return ($_SERVER['REQUEST_METHOD'] === 'GET');
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
