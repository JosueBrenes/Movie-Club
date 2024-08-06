<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];

// Obtener el siguiente valor de la secuencia
$query = 'SELECT FIDE_TIPO_SALA_SEQ.NEXTVAL AS id_tipo_sala FROM dual';
$stid = oci_parse($conn, $query);
oci_execute($stid);
$row = oci_fetch_assoc($stid);
$id_tipo_sala = $row['ID_TIPO_SALA'];

$sql = 'INSERT INTO FIDE_TIPO_SALA_TB (ID_TIPO_SALA, NOMBRE, DESCRIPCION) 
        VALUES (:id_tipo_sala, :nombre, :descripcion)';
$stid = oci_parse($conn, $sql);

oci_bind_by_name($stid, ':id_tipo_sala', $id_tipo_sala);
oci_bind_by_name($stid, ':nombre', $nombre);
oci_bind_by_name($stid, ':descripcion', $descripcion);

$success = oci_execute($stid);

if ($success) {
    header('Location: tipos_sala.php');
    exit();
} else {
    $e = oci_error($stid);
    echo "Error al agregar tipo de sala: " . $e['message'];
}

oci_free_statement($stid);
oci_close($conn);
?>
