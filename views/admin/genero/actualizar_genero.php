<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

$id_genero = isset($_POST['id_genero']) ? intval($_POST['id_genero']) : 0;
$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
$descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';

if ($id_genero <= 0 || empty($nombre)) {
    echo "Datos inválidos.";
    exit;
}

$sql = '
    UPDATE FIDE_GENERO_TB
    SET NOMBRE = :nombre, DESCRIPCION = :descripcion
    WHERE ID_GENERO = :id_genero
';

$stid = oci_parse($conn, $sql);

oci_bind_by_name($stid, ':id_genero', $id_genero);
oci_bind_by_name($stid, ':nombre', $nombre);
oci_bind_by_name($stid, ':descripcion', $descripcion);

if (!$stid) {
    $e = oci_error($conn);
    echo "Error al preparar la consulta: " . $e['message'];
    exit;
}

$success = oci_execute($stid);

if (!$success) {
    $e = oci_error($stid);
    echo "Error al ejecutar la consulta: " . $e['message'];
    exit;
}

// Redireccionar a la página de gestión de géneros
header("Location: genero.php");
exit;
?>
