<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$cantidad = $_POST['cantidad'];
$id_proveedor = $_POST['id_proveedor'];
$id_estado = $_POST['id_estado'];

try {
    // Obtener el siguiente valor de la secuencia para ID_INVENTARIO
    $query = 'SELECT FIDE_INVENTARIO_SEQ.NEXTVAL AS id_inventario FROM dual';
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    $row = oci_fetch_assoc($stid);
    $id_inventario = $row['ID_INVENTARIO'];
    oci_free_statement($stid);

    // Preparar la llamada al procedimiento almacenado
    $sql = 'BEGIN FIDE_INVENTARIO_TB_INSERTAR_INVENTARIO_SP(:id_inventario, :nombre, :descripcion, :cantidad, :id_proveedor, :id_estado); END;';
    $stid = oci_parse($conn, $sql);

    // Asociar los parámetros
    oci_bind_by_name($stid, ':id_inventario', $id_inventario);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':descripcion', $descripcion);
    oci_bind_by_name($stid, ':cantidad', $cantidad);
    oci_bind_by_name($stid, ':id_proveedor', $id_proveedor);
    oci_bind_by_name($stid, ':id_estado', $id_estado);

    // Ejecutar el procedimiento
    $success = oci_execute($stid);

    if ($success) {
        // Redirigir a la página de inventario con un mensaje de éxito
        header('Location: inventario.php?msg=Inventario agregado con éxito');
        exit();
    } else {
        // Mostrar el mensaje de error
        $e = oci_error($stid);
        echo "Error al agregar inventario: " . htmlentities($e['message'], ENT_QUOTES);
    }

    // Liberar los recursos
    oci_free_statement($stid);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar la conexión
oci_close($conn);
?>
