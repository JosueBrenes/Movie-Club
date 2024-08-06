<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

$nombre = $_POST['nombre'];
$nacionalidad = $_POST['nacionalidad'];

// Obtener el siguiente valor de la secuencia
$query = 'SELECT FIDE_DIRECTOR_SEQ.NEXTVAL AS id_director FROM dual';
$stid = oci_parse($conn, $query);
oci_execute($stid);
$row = oci_fetch_assoc($stid);
$id_director = $row['ID_DIRECTOR'];

$sql = 'INSERT INTO FIDE_DIRECTOR_TB (ID_DIRECTOR, NOMBRE, NACIONALIDAD) 
        VALUES (:id_director, :nombre, :nacionalidad)';
$stid = oci_parse($conn, $sql);

oci_bind_by_name($stid, ':id_director', $id_director);
oci_bind_by_name($stid, ':nombre', $nombre);
oci_bind_by_name($stid, ':nacionalidad', $nacionalidad);

$success = oci_execute($stid);

if ($success) {
    header('Location: directores.php');
    exit();
} else {
    $e = oci_error($stid);
    echo "Error al agregar director: " . $e['message'];
}

oci_free_statement($stid);
oci_close($conn);
?>
