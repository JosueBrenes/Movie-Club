<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];

    if (empty($cliente_id) || empty($nombre) || empty($apellido)) {
        die("Los campos Nombre y Apellido son requeridos.");
    }

    $sql = 'UPDATE FIDE_CLIENTES_TB SET NOMBRE = :nombre, APELLIDO = :apellido, CORREO_ELECTRONICO = :email WHERE ID_CLIENTE = :cliente_id';
    $stid = oci_parse($conn, $sql);

    oci_bind_by_name($stid, ':cliente_id', $cliente_id);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':apellido', $apellido);
    oci_bind_by_name($stid, ':email', $email);

    if (oci_execute($stid)) {
        header('Location: clientes.php?msg=Cliente actualizado con éxito');
    } else {
        $error = oci_error($stid);
        die("Error al actualizar el cliente: " . htmlentities($error['message'], ENT_QUOTES));
    }

    oci_free_statement($stid);
    oci_close($conn);
} else {
    die("Método de solicitud no válido.");
}
