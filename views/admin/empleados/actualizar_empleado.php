<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_empleado = $_POST['id_empleado'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $id_posicion = $_POST['id_posicion'];

    if (empty($id_empleado) || empty($nombre) || empty($apellido) || empty($correo) || empty($id_posicion)) {
        die("Todos los campos son requeridos.");
    }

    $sql = 'UPDATE FIDE_EMPLEADOS_TB SET NOMBRE = :nombre, APELLIDO = :apellido, CORREO_ELECTRONICO = :correo, TELEFONO = :telefono, ID_POSICION = :id_posicion WHERE ID_EMPLEADO = :id_empleado';
    $stid = oci_parse($conn, $sql);

    oci_bind_by_name($stid, ':id_empleado', $id_empleado);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':apellido', $apellido);
    oci_bind_by_name($stid, ':correo', $correo);
    oci_bind_by_name($stid, ':telefono', $telefono);
    oci_bind_by_name($stid, ':id_posicion', $id_posicion);

    if (oci_execute($stid)) {
        header('Location: empleados.php?msg=Empleado actualizado con éxito');
    } else {
        $error = oci_error($stid);
        die("Error al actualizar el empleado: " . htmlentities($error['message'], ENT_QUOTES));
    }

    oci_free_statement($stid);
    oci_close($conn);
} else {
    die("Método de solicitud no válido.");
}
