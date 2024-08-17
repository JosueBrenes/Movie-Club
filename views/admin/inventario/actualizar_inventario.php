<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $id_inventario = isset($_POST['id_inventario']) ? intval($_POST['id_inventario']) : 0;
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;
    $id_proveedor = isset($_POST['proveedor']) ? intval($_POST['proveedor']) : 0; // Asegúrate de que el nombre coincide con el del formulario
    $id_estado = isset($_POST['id_estado']) ? intval($_POST['id_estado']) : 0;

    if (empty($id_inventario) || empty($nombre) || empty($descripcion) || empty($cantidad) || empty($id_proveedor)) {
        die("Todos los campos son requeridos.");
    }

    // Prepara la llamada al procedimiento almacenado
    $sql = 'BEGIN FIDE_INVENTARIO_TB_ACTUALIZAR_INVENTARIO_SP(:P_ID_INVENTARIO, :P_NOMBRE, :P_DESCRIPCION, :P_CANTIDAD, :P_ID_PROVEEDOR, :P_ID_ESTADO); END;';
    $stid = oci_parse($conn, $sql);

    // Asigna los valores a los parámetros del procedimiento
    oci_bind_by_name($stid, ':P_ID_INVENTARIO', $id_inventario);
    oci_bind_by_name($stid, ':P_NOMBRE', $nombre);
    oci_bind_by_name($stid, ':P_DESCRIPCION', $descripcion);
    oci_bind_by_name($stid, ':P_CANTIDAD', $cantidad);
    oci_bind_by_name($stid, ':P_ID_PROVEEDOR', $id_proveedor);
    oci_bind_by_name($stid, ':P_ID_ESTADO', $id_estado);

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
?>
