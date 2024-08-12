<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_director = $_POST['id_director'];
    $nombre = $_POST['nombre'];
    $nacionalidad = $_POST['nacionalidad'];

    // Validar que los campos no estén vacíos
    if (empty($id_director) || empty($nombre) || empty($nacionalidad)) {
        die("Todos los campos son requeridos.");
    }

    // Preparar la llamada al procedimiento almacenado
    $sql = 'BEGIN FIDE_DIRECTOR_TB_ACTUALIZAR_DIRECTOR_SP(:id_director, :nombre, :nacionalidad); END;';
    $stid = oci_parse($conn, $sql);

    // Asignar los valores a los parámetros del procedimiento
    oci_bind_by_name($stid, ':id_director', $id_director);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':nacionalidad', $nacionalidad);

    // Ejecutar el procedimiento almacenado
    if (oci_execute($stid)) {
        header('Location: directores.php?msg=Director actualizado con éxito');
        exit;
    } else {
        $error = oci_error($stid);
        die("Error al actualizar el director: " . htmlentities($error['message'], ENT_QUOTES));
    }

    // Liberar recursos y cerrar conexión
    oci_free_statement($stid);
    oci_close($conn);
} else {
    die("Método de solicitud no válido.");
}
?>
