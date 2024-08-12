<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

// Obtener los datos del formulario
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$id_estado = $_POST['id_estado'];

// Obtener el siguiente valor de la secuencia
$query = 'SELECT FIDE_METODO_PAGO_SEQ.NEXTVAL AS id_metodo_pago FROM dual';
$stid = oci_parse($conn, $query);
oci_execute($stid);
$row = oci_fetch_assoc($stid);
$id_metodo_pago = $row['ID_METODO_PAGO'];
oci_free_statement($stid);

// Llamar al procedimiento almacenado
$proc = 'BEGIN FIDE_METODO_PAGO_TB_INSERTAR_METODO_PAGO(:id_metodo_pago, :nombre, :descripcion, :id_estado); END;';
$stid = oci_parse($conn, $proc);

oci_bind_by_name($stid, ':id_metodo_pago', $id_metodo_pago);
oci_bind_by_name($stid, ':nombre', $nombre);
oci_bind_by_name($stid, ':descripcion', $descripcion);
oci_bind_by_name($stid, ':id_estado', $id_estado);

$success = oci_execute($stid);

if ($success) {
    header('Location: metodo_pagos.php');
    exit();
} else {
    $e = oci_error($stid);
    echo "Error al agregar método de pago: " . htmlentities($e['message'], ENT_QUOTES);
}

oci_free_statement($stid);
oci_close($conn);
?>
