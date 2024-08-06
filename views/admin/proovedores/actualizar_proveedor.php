<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

$id_proveedor = isset($_POST['id_proveedor']) ? intval($_POST['id_proveedor']) : 0;
$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
$contacto = isset($_POST['contacto']) ? $_POST['contacto'] : '';
$telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';

if ($id_proveedor <= 0 || empty($nombre)) {
    echo "Datos inválidos.";
    exit;
}

$sql = '
    UPDATE FIDE_PROVEEDORES_TB
    SET NOMBRE = :nombre, CONTACTO = :contacto, TELEFONO = :telefono
    WHERE ID_PROVEEDOR = :id_proveedor
';

$stid = oci_parse($conn, $sql);

oci_bind_by_name($stid, ':id_proveedor', $id_proveedor);
oci_bind_by_name($stid, ':nombre', $nombre);
oci_bind_by_name($stid, ':contacto', $contacto);
oci_bind_by_name($stid, ':telefono', $telefono);

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

header('Location: proovedores.php'); 
exit;
?>
