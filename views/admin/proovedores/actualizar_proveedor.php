<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_proveedor = $_POST['id_proveedor'];
    $nombre = $_POST['nombre'];
    $contacto = $_POST['contacto'];
    $telefono = $_POST['telefono'];
    $id_estado = $_POST['id_estado'];

    if (empty($id_proveedor) || empty($nombre) || empty($contacto) || empty($telefono)) {
        die("Todos los campos son requeridos.");
    }

    // Prepara la llamada al procedimiento almacenado
    $sql = 'BEGIN FIDE_PROVEEDORES_TB_ACTUALIZAR_PROVEEDOR_SP(:id_proveedor, :nombre, :contacto, :telefono, :id_estados); END;';
    $stid = oci_parse($conn, $sql);

    // Asigna los valores a los parámetros del procedimiento
    oci_bind_by_name($stid, ':id_proveedor', $id_proveedor);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':contacto', $contacto);
    oci_bind_by_name($stid, ':telefono', $telefono);
    oci_bind_by_name($stid, ':id_estado', $id_estado);

    // Ejecuta el procedimiento almacenado
    if (oci_execute($stid)) {
        header('Location: proovedores.php?msg=Proveedor actualizado con éxito');
        exit;
    } else {
        $error = oci_error($stid);
        die("Error al actualizar el proveedor: " . htmlentities($error['message'], ENT_QUOTES));
    }

    oci_free_statement($stid);
    oci_close($conn);
} else {
    die("Método de solicitud no válido.");
}
