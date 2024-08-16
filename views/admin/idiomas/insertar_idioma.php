<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$id_estado = $_POST['id_estado'];

// Obtener el siguiente valor de la secuencia
$query = 'SELECT FIDE_IDIOMAS_SEQ.NEXTVAL AS id_idiomas FROM dual';
$stid = oci_parse($conn, $query);
oci_execute($stid);
$row = oci_fetch_assoc($stid);
$id_idiomas = $row['ID_IDIOMAS'];

// Llamar al procedimiento almacenado
$proc = 'BEGIN FIDE_IDIOMAS_TB_INSERTAR_IDIOMAS_SP(:id_idiomas, :nombre, :descripcion, :id_estado); END;';
$stid = oci_parse($conn, $proc);

oci_bind_by_name($stid, ':id_idiomas', $id_idiomas);
oci_bind_by_name($stid, ':nombre', $nombre);
oci_bind_by_name($stid, ':descripcion', $descripcion);
oci_bind_by_name($stid, ':id_estado', $id_estado);

$success = oci_execute($stid);

if ($success) {
    header('Location: idiomas.php');
    exit();
} else {
    $e = oci_error($stid);
    echo "Error al agregar idioma: " . htmlentities($e['message'], ENT_QUOTES);
}

oci_free_statement($stid);
oci_close($conn);
?>
