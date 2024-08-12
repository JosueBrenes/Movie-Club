<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

// Obtener los datos del formulario
$id_idiomas = isset($_POST['id_idiomas']) ? intval($_POST['id_idiomas']) : 0;
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';

if ($id_idiomas <= 0 || empty($nombre)) {
    echo "Datos inválidos.";
    exit;
}

// Preparar y ejecutar el procedimiento almacenado
$sql = 'BEGIN FIDE_IDIOMAS_TB_ACTUALIZAR_IDIOMAS_SP(:id_idiomas, :nombre, :descripcion); END;';
$stid = oci_parse($conn, $sql);

// Enlazar las variables
oci_bind_by_name($stid, ':id_idiomas', $id_idiomas);
oci_bind_by_name($stid, ':nombre', $nombre);
oci_bind_by_name($stid, ':descripcion', $descripcion);

// Ejecutar el procedimiento almacenado
$success = oci_execute($stid);

oci_free_statement($stid);
oci_close($conn);

// Redirigir según el resultado
if ($success) {
    header("Location: idiomas.php?mensaje=actualizado");
    exit;
} else {
    header("Location: idiomas.php?mensaje=error");
    exit;
}
?>
