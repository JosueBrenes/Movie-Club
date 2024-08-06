<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

$id_tipo_sala = isset($_POST['id_tipo_sala']) ? intval($_POST['id_tipo_sala']) : 0;
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
$id_tipo_sala_nuevo = isset($_POST['id_tipo_sala']) ? intval($_POST['id_tipo_sala']) : 0;

if ($id_tipo_sala <= 0 || empty($nombre) || empty($descripcion)) {
    echo "Datos inválidos.";
    exit;
}

$sql_update = 'UPDATE FIDE_TIPO_SALA_TB
                SET NOMBRE = :nombre,
                    DESCRIPCION = :descripcion,
                    ID_TIPO_SALA = :id_tipo_sala_nuevo
                WHERE ID_TIPO_SALA = :id_tipo_sala';

$stid_update = oci_parse($conn, $sql_update);

oci_bind_by_name($stid_update, ':nombre', $nombre);
oci_bind_by_name($stid_update, ':descripcion', $descripcion);
oci_bind_by_name($stid_update, ':id_tipo_sala_nuevo', $id_tipo_sala_nuevo);
oci_bind_by_name($stid_update, ':id_tipo_sala', $id_tipo_sala);

$success_update = oci_execute($stid_update);

if ($success_update) {
    echo "Tipo de sala actualizado correctamente.";
} else {
    $e = oci_error($stid_update);
    echo "Error al actualizar el tipo de sala: " . $e['message'];
}

oci_free_statement($stid_update);
oci_close($conn);

if ($success_update) {
    header('Location: tipos_sala.php?mensaje=actualizado');
    exit;
} else {
    header('Location: tipos_sala.php?mensaje=error');
    exit;
}
?>
