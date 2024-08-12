<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_comida = $_POST['id_comida'];
    $id_inventario = $_POST['id_inventario'];
    $precio = $_POST['precio'];

    // Validar que los campos no estén vacíos
    if (empty($id_comida) || empty($id_inventario) || empty($precio)) {
        die("Todos los campos son requeridos.");
    }

    // Preparar la llamada al procedimiento almacenado
    $sql = 'BEGIN FIDE_COMIDA_TB_ACTUALIZAR_COMIDA_SP(:id_comida, :id_inventario, :precio); END;';
    $stid = oci_parse($conn, $sql);

    // Asignar los valores a los parámetros del procedimiento
    oci_bind_by_name($stid, ':id_comida', $id_comida);
    oci_bind_by_name($stid, ':id_inventario', $id_inventario);
    oci_bind_by_name($stid, ':precio', $precio);

    // Ejecutar el procedimiento almacenado
    if (oci_execute($stid)) {
        header('Location: comida.php?msg=Comida actualizada con éxito');
        exit;
    } else {
        $error = oci_error($stid);
        die("Error al actualizar la comida: " . htmlentities($error['message'], ENT_QUOTES));
    }

    // Liberar recursos y cerrar conexión
    oci_free_statement($stid);
    oci_close($conn);
} else {
    die("Método de solicitud no válido.");
}
?>
