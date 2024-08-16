<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

// Obtener los datos del formulario
$fecha = $_POST['fecha'];
$id_pelicula = $_POST['id_pelicula'];
$id_sala = $_POST['id_sala'];
$id_estado = $_POST['id_estado'];

// Obtener el siguiente valor de la secuencia
$query = 'SELECT FIDE_FUNCION_SEQ.NEXTVAL AS id_funcion FROM dual';
$stid = oci_parse($conn, $query);
oci_execute($stid);
$row = oci_fetch_assoc($stid);
$id_funcion = $row['ID_FUNCION'];

// Convertir la fecha a formato de Oracle
$fecha_formateada = date('Y-m-d', strtotime($fecha));

// Llamar al procedimiento almacenado
$proc = 'BEGIN FIDE_FUNCION_TB_INSERTAR_FUNCION_SP(:id_funcion, TO_DATE(:fecha, \'YYYY-MM-DD\'), :id_pelicula, :id_sala, :id_estado); END;';
$stid = oci_parse($conn, $proc);

// Enlazar los parámetros
oci_bind_by_name($stid, ':id_funcion', $id_funcion);
oci_bind_by_name($stid, ':fecha', $fecha_formateada);
oci_bind_by_name($stid, ':id_pelicula', $id_pelicula);
oci_bind_by_name($stid, ':id_sala', $id_sala);
oci_bind_by_name($stid, ':id_estado', $id_estado);

$success = oci_execute($stid);

if ($success) {
    header('Location: funciones.php');
    exit();
} else {
    $e = oci_error($stid);
    echo "Error al agregar función: " . htmlentities($e['message'], ENT_QUOTES);
}

oci_free_statement($stid);
oci_close($conn);
?>
