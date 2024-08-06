<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

$id_posicion = isset($_POST['id_posicion']) ? intval($_POST['id_posicion']) : 0;
$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
$descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';

if ($id_posicion <= 0 || empty($nombre)) {
    echo "Datos inválidos.";
    exit;
}

$sql = '
    UPDATE FIDE_POSICION_TB
    SET NOMBRE = :nombre, DESCRIPCION = :descripcion
    WHERE ID_POSICION = :id_posicion
';

$stid = oci_parse($conn, $sql);

oci_bind_by_name($stid, ':id_posicion', $id_posicion);
oci_bind_by_name($stid, ':nombre', $nombre);
oci_bind_by_name($stid, ':descripcion', $descripcion);

if (!$stid) {
    $e = oci_error($conn);
    echo "Error al preparar la consulta: " . $e['message'];
    exit;
}

$success = oci_execute($stid, OCI_COMMIT_ON_SUCCESS);

if (!$success) {
    $e = oci_error($stid);
    echo "Error al ejecutar la consulta: " . $e['message'];
    exit;
}

oci_free_statement($stid);
oci_close($conn);

header('Location: posiciones.php'); 
exit;
?>
