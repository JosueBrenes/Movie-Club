<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_director = $_POST['id_director'];
    $nombre = $_POST['nombre'];
    $nacionalidad = $_POST['nacionalidad'];

    if (empty($id_director) || empty($nombre)) {
        die("Los campos ID de Director y Nombre son requeridos.");
    }

    $sql = 'UPDATE FIDE_DIRECTOR_TB SET NOMBRE = :nombre, NACIONALIDAD = :nacionalidad WHERE ID_DIRECTOR = :id_director';
    $stid = oci_parse($conn, $sql);

    oci_bind_by_name($stid, ':id_director', $id_director);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':nacionalidad', $nacionalidad);

    if (oci_execute($stid)) {
        header('Location: directores.php?msg=Director actualizado con éxito');
    } else {
        $error = oci_error($stid);
        die("Error al actualizar el director: " . htmlentities($error['message'], ENT_QUOTES));
    }

    oci_free_statement($stid);
    oci_close($conn);
} else {
    die("Método de solicitud no válido.");
}
