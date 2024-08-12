<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $contacto = $_POST['contacto'];
    $telefono = $_POST['telefono'];

    // Obtener el siguiente valor de la secuencia para ID_PROVEEDOR
    $query = 'SELECT FIDE_PROVEEDOR_SEQ.NEXTVAL AS id_proveedor FROM dual';
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    $row = oci_fetch_assoc($stid);
    $id_proveedor = $row['ID_PROVEEDOR'];
    oci_free_statement($stid);

    // Llamar al procedimiento almacenado
    $proc = 'BEGIN FIDE_PROVEEDORES_TB_INSERTAR_PROVEEDOR_SP(:id_proveedor, :nombre, :contacto, :telefono); END;';
    $stid = oci_parse($conn, $proc);

    oci_bind_by_name($stid, ':id_proveedor', $id_proveedor);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':contacto', $contacto);
    oci_bind_by_name($stid, ':telefono', $telefono);

    $result = oci_execute($stid);

    if ($result) {
        header('Location: proovedores.php');
        exit();
    } else {
        $e = oci_error($stid);
        echo "Error al agregar proveedor: " . htmlentities($e['message'], ENT_QUOTES);
    }

    oci_free_statement($stid);
    oci_close($conn);
}
?>
