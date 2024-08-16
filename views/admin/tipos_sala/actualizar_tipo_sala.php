<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_tipo_sala = $_POST['id_tipo_sala'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $id_estado = $_POST['id_estado'];

    // Validar que los campos no estén vacíos
    if (empty($id_tipo_sala) || empty($nombre) || empty($descripcion)) {
        die("Todos los campos son requeridos.");
    }

    // Preparar la llamada al procedimiento almacenado
    $sql = 'BEGIN FIDE_TIPO_SALA_TB_ACTUALIZAR_TIPO_SALA_SP(:id_tipo_sala, :nombre, :descripcion, :id_estado); END;';
    $stid = oci_parse($conn, $sql);

    // Asignar los valores a los parámetros del procedimiento
    oci_bind_by_name($stid, ':id_tipo_sala', $id_tipo_sala);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':descripcion', $descripcion);
    oci_bind_by_name($stid, ':id_estado', $id_estado);

    // Ejecutar el procedimiento almacenado
    if (oci_execute($stid)) {
        header('Location: tipos_sala.php?msg=Tipo de sala actualizado con éxito');
        exit;
    } else {
        $error = oci_error($stid);
        die("Error al actualizar el tipo de sala: " . htmlentities($error['message'], ENT_QUOTES));
    }

    // Liberar recursos y cerrar conexión
    oci_free_statement($stid);
    oci_close($conn);
} else {
    die("Método de solicitud no válido.");
}
?>
