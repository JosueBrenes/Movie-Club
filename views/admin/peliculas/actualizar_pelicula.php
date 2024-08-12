<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pelicula = $_POST['id_pelicula'];
    $nombre = $_POST['nombre'];
    $id_genero = $_POST['id_genero'];
    $id_director = $_POST['id_director'];
    $descripcion = $_POST['descripcion'];
    $id_idiomas = $_POST['id_idiomas'];
    $id_estado = $_POST['id_estado'];

    if (empty($id_pelicula) || empty($nombre) || empty($id_genero) || empty($id_director) || empty($descripcion) || empty($id_idiomas) || empty($id_estado)) {
        die("Todos los campos son requeridos.");
    }

    // Prepara la llamada al procedimiento almacenado
    $sql = 'BEGIN FIDE_PELICULAS_TB_ACTUALIZAR_PELICULAS_SP(:id_pelicula, :nombre, :id_genero, :id_director, :descripcion, :id_idiomas, :id_estado); END;';
    $stid = oci_parse($conn, $sql);

    // Asigna los valores a los parámetros del procedimiento
    oci_bind_by_name($stid, ':id_pelicula', $id_pelicula);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':id_genero', $id_genero);
    oci_bind_by_name($stid, ':id_director', $id_director);
    oci_bind_by_name($stid, ':descripcion', $descripcion);
    oci_bind_by_name($stid, ':id_idiomas', $id_idiomas);
    oci_bind_by_name($stid, ':id_estado', $id_estado);

    // Ejecuta el procedimiento almacenado
    if (oci_execute($stid)) {
        header('Location: peliculas.php?msg=Pelicula actualizada con éxito');
        exit;
    } else {
        $error = oci_error($stid);
        die("Error al actualizar la película: " . htmlentities($error['message'], ENT_QUOTES));
    }

    oci_free_statement($stid);
    oci_close($conn);
} else {
    die("Método de solicitud no válido.");
}
?>
