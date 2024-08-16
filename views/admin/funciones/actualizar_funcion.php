<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $id_funcion = $_POST['id_funcion'];
    $fecha = $_POST['fecha'];
    $id_pelicula = $_POST['id_pelicula'];
    $id_sala = $_POST['id_sala'];
    $id_estado = $_POST['id_estado'];

    // Validar que los campos no estén vacíos
    if (empty($id_funcion) || empty($fecha) || empty($id_pelicula) || empty($id_sala)) {
        die("Todos los campos son requeridos.");
    }

    // Convertir la fecha a formato de Oracle
    $fecha_formateada = date('Y-m-d', strtotime($fecha));

    // Llamar al procedimiento almacenado para actualizar
    $proc = 'BEGIN FIDE_FUNCION_TB_ACTUALIZAR_FUNCION_SP(:id_funcion, TO_DATE(:fecha, \'YYYY-MM-DD\'), :id_pelicula, :id_sala, :id_estado); END;';
    $stid = oci_parse($conn, $proc);

    // Enlazar los parámetros
    oci_bind_by_name($stid, ':id_funcion', $id_funcion);
    oci_bind_by_name($stid, ':fecha', $fecha_formateada);
    oci_bind_by_name($stid, ':id_pelicula', $id_pelicula);
    oci_bind_by_name($stid, ':id_sala', $id_sala);
    oci_bind_by_name($stid, ':id_estado', $id_estado);

    // Ejecutar el procedimiento almacenado
    $success = oci_execute($stid);

    if ($success) {
        header('Location: funciones.php?msg=Función actualizada con éxito');
        exit();
    } else {
        $e = oci_error($stid);
        echo "Error al actualizar la función: " . htmlentities($e['message'], ENT_QUOTES);
    }

    // Liberar recursos y cerrar conexión
    oci_free_statement($stid);
    oci_close($conn);
} else {
    die("Método de solicitud no válido.");
}
?>
