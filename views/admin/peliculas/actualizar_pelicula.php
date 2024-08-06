<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

$id_pelicula = isset($_POST['id_pelicula']) ? intval($_POST['id_pelicula']) : 0;
$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
$descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
$id_genero = isset($_POST['genero']) ? intval($_POST['genero']) : 0;
$id_director = isset($_POST['director']) ? intval($_POST['director']) : 0;
$id_idiomas = isset($_POST['idioma']) ? intval($_POST['idioma']) : 0;
$id_estado = isset($_POST['estado']) ? intval($_POST['estado']) : 0;

if ($id_pelicula <= 0 || empty($nombre) || $id_genero <= 0 || $id_director <= 0 || $id_idiomas <= 0 || $id_estado <= 0) {
    echo "Datos inválidos.";
    exit;
}

$sql = '
    UPDATE FIDE_PELICULAS_TB
    SET NOMBRE = :nombre, DESCRIPCION = :descripcion, ID_GENERO = :id_genero, ID_DIRECTOR = :id_director, ID_IDIOMAS = :id_idiomas, ID_ESTADO = :id_estado
    WHERE ID_PELICULA = :id_pelicula
';

$stid = oci_parse($conn, $sql);

oci_bind_by_name($stid, ':id_pelicula', $id_pelicula);
oci_bind_by_name($stid, ':nombre', $nombre);
oci_bind_by_name($stid, ':descripcion', $descripcion);
oci_bind_by_name($stid, ':id_genero', $id_genero);
oci_bind_by_name($stid, ':id_director', $id_director);
oci_bind_by_name($stid, ':id_idiomas', $id_idiomas);
oci_bind_by_name($stid, ':id_estado', $id_estado);

if (!$stid) {
    $e = oci_error($conn);
    echo "Error al preparar la consulta: " . $e['message'];
    exit;
}

$success = oci_execute($stid, OCI_COMMIT_ON_SUCCESS);

if (!$success) {
    $e = oci_error($stid);
    echo "Error al ejecutar la consulta: " . $e['message'];
    exit;
}

oci_free_statement($stid);
oci_close($conn);

header('Location: peliculas.php');
exit;
?>
