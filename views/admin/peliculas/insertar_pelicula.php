<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $id_genero = $_POST['id_genero'];
    $id_director = $_POST['id_director'];
    $descripcion = $_POST['descripcion'];
    $id_idiomas = $_POST['id_idiomas'];
    $id_estado = $_POST['id_estado'];

    $sql = 'INSERT INTO FIDE_PELICULAS_TB (ID_PELICULA, NOMBRE, ID_GENERO, ID_DIRECTOR, DESCRIPCION, ID_IDIOMAS, ID_ESTADO) VALUES (FIDE_PELICULAS_SEQ.NEXTVAL, :nombre, :id_genero, :id_director, :descripcion, :id_idiomas, :id_estado)';
    $stid = oci_parse($conn, $sql);

    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':id_genero', $id_genero);
    oci_bind_by_name($stid, ':id_director', $id_director);
    oci_bind_by_name($stid, ':descripcion', $descripcion);
    oci_bind_by_name($stid, ':id_idiomas', $id_idiomas);
    oci_bind_by_name($stid, ':id_estado', $id_estado);

    $result = oci_execute($stid);

    if ($result) {
        header('Location: peliculas.php?message=success');
    } else {
        echo "Error al insertar la película.";
    }

    oci_free_statement($stid);
    oci_close($conn);
}
?>
