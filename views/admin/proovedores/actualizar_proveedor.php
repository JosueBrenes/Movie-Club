<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

// Obtener los datos del formulario
$id_proveedor = isset($_POST['id_proveedor']) ? intval($_POST['id_proveedor']) : 0;
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$contacto = isset($_POST['contacto']) ? trim($_POST['contacto']) : '';
$telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
$id_estado = isset($_POST['id_estado']) ? intval($_POST['id_estado']) : 0;

if ($id_proveedor <= 0 || empty($nombre)) {
    echo "Datos inválidos.";
    exit;
}

// Preparar y ejecutar el procedimiento almacenado
$sql = 'BEGIN FIDE_PROVEEDORES_TB_ACTUALIZAR_PROVEEDOR_SP(:P_ID_PROVEEDOR, :P_NOMBRE, :P_CONTACTO, :P_TELEFONO, :P_ID_ESTADO); END;';
$stid = oci_parse($conn, $sql);

// Enlazar las variables
oci_bind_by_name($stid, ':P_ID_PROVEEDOR', $id_proveedor);
oci_bind_by_name($stid, ':P_NOMBRE', $nombre);
oci_bind_by_name($stid, ':P_CONTACTO', $contacto);
oci_bind_by_name($stid, ':P_TELEFONO', $telefono);
oci_bind_by_name($stid, ':P_ID_ESTADO', $id_estado);

// Ejecutar el procedimiento almacenado
$success = oci_execute($stid);

if (!$success) {
    $e = oci_error($stid);
    echo "Error al actualizar el proveedor: " . $e['message'];
}

oci_free_statement($stid);
oci_close($conn);

// Redirigir según el resultado
if ($success) {
    header("Location: proovedores.php?mensaje=actualizado");
    exit;
} else {
    header("Location: proovedores.php?mensaje=error");
    exit;
}
?>
