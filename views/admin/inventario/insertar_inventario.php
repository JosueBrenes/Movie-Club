<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$cantidad = $_POST['cantidad'];
$id_proveedor = $_POST['id_proveedor'];

$query = 'SELECT FIDE_INVENTARIO_SEQ.NEXTVAL AS id_inventario FROM dual';
$stid = oci_parse($conn, $query);
oci_execute($stid);
$row = oci_fetch_assoc($stid);
$id_inventario = $row['ID_INVENTARIO'];

$sql = 'INSERT INTO FIDE_INVENTARIO_TB (ID_INVENTARIO, NOMBRE, DESCRIPCION, CANTIDAD, ID_PROVEEDOR) 
        VALUES (:id_inventario, :nombre, :descripcion, :cantidad, :id_proveedor)';
$stid = oci_parse($conn, $sql);

oci_bind_by_name($stid, ':id_inventario', $id_inventario);
oci_bind_by_name($stid, ':nombre', $nombre);
oci_bind_by_name($stid, ':descripcion', $descripcion);
oci_bind_by_name($stid, ':cantidad', $cantidad);
oci_bind_by_name($stid, ':id_proveedor', $id_proveedor);

$success = oci_execute($stid);

if ($success) {
    header('Location: inventario.php');
    exit();
} else {
    $e = oci_error($stid);
    echo "Error al agregar inventario: " . $e['message'];
}

oci_free_statement($stid);
oci_close($conn);
?>
