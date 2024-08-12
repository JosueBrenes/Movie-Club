<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

$id_inventario = $_POST['id_inventario'];
$precio = $_POST['precio'];

try {
    // Obtener el siguiente valor de la secuencia para ID_COMIDA
    $query = 'SELECT FIDE_COMIDA_SEQ.NEXTVAL AS id_comida FROM dual';
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    $row = oci_fetch_assoc($stid);
    $id_comida = $row['ID_COMIDA'];
    oci_free_statement($stid); 

    // Llamar al procedimiento almacenado para insertar en FIDE_COMIDA_TB
    $sql = 'BEGIN FIDE_COMIDA_TB_INSERTAR_COMIDA_SP(:id_comida, :id_inventario, :precio); END;';
    $stid = oci_parse($conn, $sql);

    // Vincular los parámetros
    oci_bind_by_name($stid, ':id_comida', $id_comida);
    oci_bind_by_name($stid, ':id_inventario', $id_inventario);
    oci_bind_by_name($stid, ':precio', $precio);

    // Ejecutar el procedimiento almacenado
    $success = oci_execute($stid);

    if ($success) {
        header('Location: comida.php?msg=Comida agregada con éxito');
        exit();
    } else {
        $e = oci_error($stid);
        echo "Error al agregar comida: " . htmlentities($e['message'], ENT_QUOTES);
    }

    oci_free_statement($stid);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar la conexión
oci_close($conn);
?>
