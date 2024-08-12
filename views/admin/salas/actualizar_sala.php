<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_sala = $_POST['id_sala'];
    $nombre = $_POST['nombre'];
    $numero_asientos = $_POST['numero_asientos'];
    $id_tipo_sala = $_POST['id_tipo_sala'];
    $id_estado = $_POST['id_estado'];

    // Validar que los campos no estén vacíos
    if (empty($id_sala) || empty($nombre) || empty($numero_asientos) || empty($id_tipo_sala) || empty($id_estado)) {
        die("Todos los campos son requeridos.");
    }

    // Preparar la llamada al procedimiento almacenado
    $sql = 'BEGIN FIDE_SALAS_TB_ACTUALIZAR_SALAS_SP(:id_sala, :nombre, :numero_asientos, :id_tipo_sala, :id_estado); END;';
    $stid = oci_parse($conn, $sql);

    // Asignar los valores a los parámetros del procedimiento
    oci_bind_by_name($stid, ':id_sala', $id_sala);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':numero_asientos', $numero_asientos);
    oci_bind_by_name($stid, ':id_tipo_sala', $id_tipo_sala);
    oci_bind_by_name($stid, ':id_estado', $id_estado);

    // Ejecutar el procedimiento almacenado
    if (oci_execute($stid)) {
        header('Location: salas.php?msg=Sala actualizada con éxito');
        exit;
    } else {
        $error = oci_error($stid);
        die("Error al actualizar la sala: " . htmlentities($error['message'], ENT_QUOTES));
    }

    // Liberar recursos y cerrar conexión
    oci_free_statement($stid);
    oci_close($conn);
} else {
    die("Método de solicitud no válido.");
}
?>
