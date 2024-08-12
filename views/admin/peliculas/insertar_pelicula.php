<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $id_genero = $_POST['id_genero'];
    $id_director = $_POST['id_director'];
    $descripcion = $_POST['descripcion'];
    $id_idiomas = $_POST['id_idiomas'];
    $id_estado = $_POST['id_estado'];

    // Obtener el siguiente valor de la secuencia para ID_PELICULA
    $query = 'SELECT FIDE_PELICULAS_SEQ.NEXTVAL AS id_pelicula FROM dual';
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    $row = oci_fetch_assoc($stid);
    $id_pelicula = $row['ID_PELICULA'];
    oci_free_statement($stid);

    // Llamar al procedimiento almacenado
    $proc = 'BEGIN FIDE_PELICULAS_TB_INSERTAR_PELICULAS_SP(:id_pelicula, :nombre, :id_genero, :id_director, :descripcion, :id_idiomas, :id_estado); END;';
    $stid = oci_parse($conn, $proc);

    oci_bind_by_name($stid, ':id_pelicula', $id_pelicula);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':id_genero', $id_genero);
    oci_bind_by_name($stid, ':id_director', $id_director);
    oci_bind_by_name($stid, ':descripcion', $descripcion);
    oci_bind_by_name($stid, ':id_idiomas', $id_idiomas);
    oci_bind_by_name($stid, ':id_estado', $id_estado);

    $result = oci_execute($stid);

    if ($result) {
        header('Location: peliculas.php?message=success');
        exit();
    } else {
        $e = oci_error($stid);
        echo "Error al insertar la película: " . htmlentities($e['message'], ENT_QUOTES);
    }

    oci_free_statement($stid);
    oci_close($conn);
}
?>
