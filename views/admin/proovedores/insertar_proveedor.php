<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

$nombre = $_POST['nombre'];
$contacto = $_POST['contacto'];
$telefono = $_POST['telefono'];

// Obtener el siguiente valor de la secuencia
$query = 'SELECT FIDE_PROVEEDOR_SEQ.NEXTVAL AS id_proveedor FROM dual';
$stid = oci_parse($conn, $query);
oci_execute($stid);
$row = oci_fetch_assoc($stid);
$id_proveedor = $row['ID_PROVEEDOR'];

$sql = 'INSERT INTO FIDE_PROVEEDORES_TB (ID_PROVEEDOR, NOMBRE, CONTACTO, TELEFONO) 
        VALUES (:id_proveedor, :nombre, :contacto, :telefono)';
$stid = oci_parse($conn, $sql);

oci_bind_by_name($stid, ':id_proveedor', $id_proveedor);
oci_bind_by_name($stid, ':nombre', $nombre);
oci_bind_by_name($stid, ':contacto', $contacto);
oci_bind_by_name($stid, ':telefono', $telefono);

$success = oci_execute($stid);

if ($success) {
    header('Location: proveedores.php');
    exit();
} else {
    $e = oci_error($stid);
    echo "Error al agregar proveedor: " . $e['message'];
}

oci_free_statement($stid);
oci_close($conn);
?>
