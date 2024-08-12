<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_posicion = $_POST['id_posicion'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];

    // Validar que los campos no estén vacíos
    if (empty($id_posicion) || empty($nombre) || empty($descripcion)) {
        die("Todos los campos son requeridos.");
    }

    // Preparar la llamada al procedimiento almacenado
    $sql = 'BEGIN FIDE_POSICION_TB_ACTUALIZAR_POSICION_SP(:id_posicion, :nombre, :descripcion); END;';
    $stid = oci_parse($conn, $sql);

    // Asignar los valores a los parámetros del procedimiento
    oci_bind_by_name($stid, ':id_posicion', $id_posicion);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':descripcion', $descripcion);

    // Ejecutar el procedimiento almacenado
    if (oci_execute($stid)) {
        header('Location: posiciones.php?msg=Posición actualizada con éxito');
        exit;
    } else {
        $error = oci_error($stid);
        die("Error al actualizar la posición: " . htmlentities($error['message'], ENT_QUOTES));
    }

    // Liberar recursos y cerrar conexión
    oci_free_statement($stid);
    oci_close($conn);
} else {
    die("Método de solicitud no válido.");
}
?>
