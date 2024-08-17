<?php
// Datos de conexión
$username = 'AB_User';
$password = 'AB_User';
$dbname = 'Moviee_Club'; 
$hostname = 'localhost:1521/orcl';

// Establecer la conexión
$conn = oci_connect($username, $password, $hostname);

if (!$conn) {
    $e = oci_error();
    echo htmlentities($e['message'], ENT_QUOTES);
    exit;
}
?>
