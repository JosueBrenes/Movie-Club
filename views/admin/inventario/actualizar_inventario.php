<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_inventario = $_POST['id_inventario'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $cantidad = $_POST['cantidad'];
    $id_proveedor = $_POST['id_proveedor'];
    $id_estado = $_POST['id_estado'];

    if (empty($id_inventario) || empty($nombre) || empty($descripcion) || empty($cantidad) || empty($id_proveedor)) {
        die("Todos los campos son requeridos.");
    }

    // Prepara la llamada al procedimiento almacenado
    $sql = 'BEGIN FIDE_INVENTARIO_TB_ACTUALIZAR_INVENTARIO_SP(:id_inventario, :nombre, :descripcion, :cantidad, :id_proveedor, :id_estado); END;';
    $stid = oci_parse($conn, $sql);

    // Asigna los valores a los parámetros del procedimiento
    oci_bind_by_name($stid, ':id_inventario', $id_inventario);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':descripcion', $descripcion);
    oci_bind_by_name($stid, ':cantidad', $cantidad);
    oci_bind_by_name($stid, ':id_proveedor', $id_proveedor);
    oci_bind_by_name($stid, ':id_estado', $id_estado);

    // Ejecuta el procedimiento almacenado
    if (oci_execute($stid)) {
        header('Location: inventario.php?msg=Inventario actualizado con éxito');
        exit;
    } else {
        $error = oci_error($stid);
        die("Error al actualizar el inventario: " . htmlentities($error['message'], ENT_QUOTES));
    }

    oci_free_statement($stid);
    oci_close($conn);
} else {
    die("Método de solicitud no válido.");
}
