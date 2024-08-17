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
    $contrasena = $_POST['contrasena']; 
    $id_estado = $_POST['id_estado'];

    // Validar que los campos no estén vacíos
    if (empty($cliente_id) || empty($nombre) || empty($apellido) || empty($email) || empty($contrasena) || empty($id_estado)) {
        die("Todos los campos son requeridos.");
    }

    // Preparar la llamada al procedimiento almacenado
    $sql = 'BEGIN FIDE_CLIENTES_TB_ACTUALIZAR_CLIENTES_SP(:cliente_id, :nombre, :apellido, :email, :contrasena, :id_estado); END;';
    $stid = oci_parse($conn, $sql);

    // Asignar los valores a los parámetros del procedimiento
    oci_bind_by_name($stid, ':cliente_id', $cliente_id, -1, SQLT_INT);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':apellido', $apellido);
    oci_bind_by_name($stid, ':email', $email);
    oci_bind_by_name($stid, ':contrasena', $contrasena);
    oci_bind_by_name($stid, ':id_estado', $id_estado, -1, SQLT_INT);

    // Ejecutar el procedimiento almacenado
    if (oci_execute($stid)) {
        header('Location: clientes.php?msg=Cliente actualizado con éxito');
        exit;
    } else {
        $error = oci_error($stid);
        die("Error al actualizar el cliente: " . htmlentities($error['message'], ENT_QUOTES));
    }

    // Liberar recursos y cerrar conexión
    oci_free_statement($stid);
    oci_close($conn);
} else {
    die("Método de solicitud no válido.");
}
?>
