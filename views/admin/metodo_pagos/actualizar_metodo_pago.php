<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

$id_metodo_pago = isset($_POST['id_metodo_pago']) ? intval($_POST['id_metodo_pago']) : 0;
$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
$descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
$id_estado = isset($_POST['estado']) ? intval($_POST['estado']) : 0;

if ($id_metodo_pago <= 0 || empty($nombre) || $id_estado <= 0) {
    echo "Datos inválidos.";
    exit;
}

$sql = '
    UPDATE FIDE_METODO_PAGO_TB
    SET NOMBRE = :nombre, DESCRIPCION = :descripcion, ID_ESTADO = :id_estado
    WHERE ID_METODO_PAGO = :id_metodo_pago
';

$stid = oci_parse($conn, $sql);

oci_bind_by_name($stid, ':id_metodo_pago', $id_metodo_pago);
oci_bind_by_name($stid, ':nombre', $nombre);
oci_bind_by_name($stid, ':descripcion', $descripcion);
oci_bind_by_name($stid, ':id_estado', $id_estado);

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

header("Location: metodo_pagos.php");
exit;
?>
