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
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';

    if (empty($id_empleado) || empty($nombre) || empty($apellido) || empty($correo) || empty($id_posicion)) {
        die("Todos los campos son requeridos.");
    }

    // Prepara la llamada al procedimiento almacenado
    $sql = 'BEGIN FIDE_EMPLEADOS_TB_ACTUALIZAR_EMPLEADO_SP(:id_empleado, :nombre, :apellido, :correo, :telefono, :id_posicion); END;';
    $stid = oci_parse($conn, $sql);

    // Asigna los valores a los parámetros del procedimiento
    oci_bind_by_name($stid, ':id_empleado', $id_empleado);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':apellido', $apellido);
    oci_bind_by_name($stid, ':correo', $correo);
    oci_bind_by_name($stid, ':telefono', $telefono);
    oci_bind_by_name($stid, ':id_posicion', $id_posicion);

    // Ejecuta el procedimiento almacenado
    if (oci_execute($stid)) {
        header('Location: empleados.php?msg=Empleado actualizado con éxito');
        exit;
    } else {
        $error = oci_error($stid);
        die("Error al actualizar el empleado: " . htmlentities($error['message'], ENT_QUOTES));
    }

    oci_free_statement($stid);
    oci_close($conn);
} else {
    die("Método de solicitud no válido.");
}
