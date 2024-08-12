<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_metodo_pago = $_POST['id_metodo_pago'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $estado = $_POST['estado'];

    // Validar que los campos no estén vacíos
    if (empty($id_metodo_pago) || empty($nombre) || empty($descripcion) || empty($estado)) {
        die("Todos los campos son requeridos.");
    }

    // Preparar la llamada al procedimiento almacenado
    $sql = 'BEGIN FIDE_METODO_PAGO_TB_ACTUALIZAR_METODO_PAGO_SP(:id_metodo_pago, :nombre, :descripcion, :estado); END;';
    $stid = oci_parse($conn, $sql);

    // Asignar los valores a los parámetros del procedimiento
    oci_bind_by_name($stid, ':id_metodo_pago', $id_metodo_pago);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':descripcion', $descripcion);
    oci_bind_by_name($stid, ':estado', $estado);

    // Ejecutar el procedimiento almacenado
    if (oci_execute($stid)) {
        header('Location: metodo_pagos.php?msg=Método de pago actualizado con éxito');
        exit;
    } else {
        $error = oci_error($stid);
        die("Error al actualizar el método de pago: " . htmlentities($error['message'], ENT_QUOTES));
    }

    // Liberar recursos y cerrar conexión
    oci_free_statement($stid);
    oci_close($conn);
} else {
    die("Método de solicitud no válido.");
}
?>
