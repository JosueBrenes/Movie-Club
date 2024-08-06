<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

$id_inventario = isset($_POST['id_inventario']) ? intval($_POST['id_inventario']) : 0;
$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
$descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
$cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;
$id_proveedor = isset($_POST['proveedor']) ? intval($_POST['proveedor']) : 0;

if ($id_inventario <= 0 || empty($nombre) || $cantidad < 0 || $id_proveedor <= 0) {
    echo "Datos inválidos.";
    exit;
}

$sql = '
    UPDATE FIDE_INVENTARIO_TB
    SET NOMBRE = :nombre, DESCRIPCION = :descripcion, CANTIDAD = :cantidad, ID_PROVEEDOR = :id_proveedor
    WHERE ID_INVENTARIO = :id_inventario
';

$stid = oci_parse($conn, $sql);

oci_bind_by_name($stid, ':id_inventario', $id_inventario);
oci_bind_by_name($stid, ':nombre', $nombre);
oci_bind_by_name($stid, ':descripcion', $descripcion);
oci_bind_by_name($stid, ':cantidad', $cantidad);
oci_bind_by_name($stid, ':id_proveedor', $id_proveedor);

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

// Redireccionar a la página de gestión de inventario
header("Location: inventario.php");
exit;
?>
