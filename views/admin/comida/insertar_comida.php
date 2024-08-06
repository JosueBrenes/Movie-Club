<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

$id_inventario = $_POST['id_inventario'];
$precio = $_POST['precio'];

// Obtener el siguiente valor de la secuencia
$query = 'SELECT FIDE_COMIDA_SEQ.NEXTVAL AS id_comida FROM dual';
$stid = oci_parse($conn, $query);
oci_execute($stid);
$row = oci_fetch_assoc($stid);
$id_comida = $row['ID_COMIDA'];

$sql = 'INSERT INTO FIDE_COMIDA_TB (ID_COMIDA, ID_INVENTARIO, PRECIO) 
        VALUES (:id_comida, :id_inventario, :precio)';
$stid = oci_parse($conn, $sql);

oci_bind_by_name($stid, ':id_comida', $id_comida);
oci_bind_by_name($stid, ':id_inventario', $id_inventario);
oci_bind_by_name($stid, ':precio', $precio);

$success = oci_execute($stid);

if ($success) {
    header('Location: comida.php');
    exit();
} else {
    $e = oci_error($stid);
    echo "Error al agregar comida: " . $e['message'];
}

oci_free_statement($stid);
oci_close($conn);
?>
