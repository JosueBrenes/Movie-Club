<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

// Obtener los datos del formulario
$nombre = $_POST['nombre'];
$nacionalidad = $_POST['nacionalidad'];

try {
    // Obtener el siguiente valor de la secuencia para ID_DIRECTOR
    $query = 'SELECT FIDE_DIRECTOR_SEQ.NEXTVAL AS id_director FROM dual';
    $stid = oci_parse($conn, $query);

    if (!oci_execute($stid)) {
        $e = oci_error($stid);
        die("Error al ejecutar la consulta de secuencia: " . htmlentities($e['message'], ENT_QUOTES));
    }

    $row = oci_fetch_assoc($stid);
    if (!$row) {
        die("No se pudo obtener el ID del director.");
    }
    $id_director = $row['ID_DIRECTOR'];
    oci_free_statement($stid);

    // Preparar la llamada al procedimiento almacenado
    $sql = 'BEGIN FIDE_DIRECTOR_TB_INSERTAR_DIRECTOR_SP(:id_director, :nombre, :nacionalidad); END;';
    $stid = oci_parse($conn, $sql);

    // Asociar los parámetros
    oci_bind_by_name($stid, ':id_director', $id_director);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':nacionalidad', $nacionalidad);

    // Ejecutar el procedimiento
    $success = oci_execute($stid);

    if ($success) {
        // Redirigir a la página de directores con un mensaje de éxito
        header('Location: directores.php?msg=Director agregado con éxito');
        exit();
    } else {
        // Mostrar el mensaje de error
        $e = oci_error($stid);
        echo "Error al agregar director: " . htmlentities($e['message'], ENT_QUOTES);
    }

    // Liberar los recursos
    oci_free_statement($stid);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar la conexión
oci_close($conn);
?>
