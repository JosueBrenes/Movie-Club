<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$id_estado = $_POST['id_estado'];

// Obtener el siguiente valor de la secuencia
$query = 'SELECT FIDE_METODO_PAGO_SEQ.NEXTVAL AS id_metodo_pago FROM dual';
$stid = oci_parse($conn, $query);
oci_execute($stid);
$row = oci_fetch_assoc($stid);
$id_metodo_pago = $row['ID_METODO_PAGO'];

$sql = 'INSERT INTO FIDE_METODO_PAGO_TB (ID_METODO_PAGO, NOMBRE, DESCRIPCION, ID_ESTADO) 
        VALUES (:id_metodo_pago, :nombre, :descripcion, :id_estado)';
$stid = oci_parse($conn, $sql);

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
    echo "Error al agregar método de pago: " . $e['message'];
}

oci_free_statement($stid);
oci_close($conn);
?>
