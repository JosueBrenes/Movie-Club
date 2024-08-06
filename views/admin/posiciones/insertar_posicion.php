<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];

// Obtener el siguiente valor de la secuencia
$query = 'SELECT FIDE_POSICION_SEQ.NEXTVAL AS id_posicion FROM dual';
$stid = oci_parse($conn, $query);
oci_execute($stid);
$row = oci_fetch_assoc($stid);
$id_posicion = $row['ID_POSICION'];

$sql = 'INSERT INTO FIDE_POSICION_TB (ID_POSICION, NOMBRE, DESCRIPCION) 
        VALUES (:id_posicion, :nombre, :descripcion)';
$stid = oci_parse($conn, $sql);

oci_bind_by_name($stid, ':id_posicion', $id_posicion);
oci_bind_by_name($stid, ':nombre', $nombre);
oci_bind_by_name($stid, ':descripcion', $descripcion);

$success = oci_execute($stid);

if ($success) {
    header('Location: posiciones.php');
    exit();
} else {
    $e = oci_error($stid);
    echo "Error al agregar posición: " . $e['message'];
}

oci_free_statement($stid);
oci_close($conn);
?>
