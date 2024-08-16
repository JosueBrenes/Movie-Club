<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_genero = $_POST['id_genero'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $id_estado = $_POST['id_estado'];

    // Validar que los campos no estén vacíos
    if (empty($id_genero) || empty($nombre) || empty($descripcion)) {
        die("Todos los campos son requeridos.");
    }

    // Preparar la llamada al procedimiento almacenado
    $sql = 'BEGIN FIDE_GENERO_TB_ACTUALIZAR_GENERO_SP(:id_genero, :nombre, :descripcion, :id_estado); END;';
    $stid = oci_parse($conn, $sql);

    // Asignar los valores a los parámetros del procedimiento
    oci_bind_by_name($stid, ':id_genero', $id_genero);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':descripcion', $descripcion);
    oci_bind_by_name($stid, ':id_estado', $id_estado);

    // Ejecutar el procedimiento almacenado
    if (oci_execute($stid)) {
        header('Location: genero.php?msg=Género actualizado con éxito');
        exit;
    } else {
        $error = oci_error($stid);
        die("Error al actualizar el género: " . htmlentities($error['message'], ENT_QUOTES));
    }

    // Liberar recursos y cerrar conexión
    oci_free_statement($stid);
    oci_close($conn);
} else {
    die("Método de solicitud no válido.");
}
?>
