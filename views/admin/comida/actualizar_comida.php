<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_comida = $_POST['id_comida'];
    $id_inventario = $_POST['id_inventario'];
    $precio = $_POST['precio'];

    if (empty($id_comida) || empty($precio)) {
        die("Los campos ID de Comida y Precio son requeridos.");
    }

    $sql = 'UPDATE FIDE_COMIDA_TB SET ID_INVENTARIO = :id_inventario, PRECIO = :precio WHERE ID_COMIDA = :id_comida';
    $stid = oci_parse($conn, $sql);

    oci_bind_by_name($stid, ':id_comida', $id_comida);
    oci_bind_by_name($stid, ':id_inventario', $id_inventario);
    oci_bind_by_name($stid, ':precio', $precio);

    if (oci_execute($stid)) {
        header('Location: comida.php?msg=Comida actualizada con éxito');
    } else {
        $error = oci_error($stid);
        die("Error al actualizar la comida: " . htmlentities($error['message'], ENT_QUOTES));
    }

    oci_free_statement($stid);
    oci_close($conn);
} else {
    die("Método de solicitud no válido.");
}
