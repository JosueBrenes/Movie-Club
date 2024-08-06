<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];

$query = 'SELECT FIDE_GENERO_SEQ.NEXTVAL AS id_genero FROM dual';
$stid = oci_parse($conn, $query);
oci_execute($stid);
$row = oci_fetch_assoc($stid);
$id_genero = $row['ID_GENERO'];

$sql = 'INSERT INTO FIDE_GENERO_TB (ID_GENERO, NOMBRE, DESCRIPCION) 
        VALUES (:id_genero, :nombre, :descripcion)';
$stid = oci_parse($conn, $sql);

oci_bind_by_name($stid, ':id_genero', $id_genero);
oci_bind_by_name($stid, ':nombre', $nombre);
oci_bind_by_name($stid, ':descripcion', $descripcion);

$success = oci_execute($stid);

if ($success) {
    header('Location: genero.php');
    exit();
} else {
    $e = oci_error($stid);
    echo "Error al agregar género: " . $e['message'];
}

oci_free_statement($stid);
oci_close($conn);
?>
