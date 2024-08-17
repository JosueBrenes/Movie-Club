<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id_empleado = $_POST['id_empleado'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $id_posicion = $_POST['id_posicion'];
    $contrasena = isset($_POST['contrasena']) && !empty($_POST['contrasena']) ? $_POST['contrasena'] : NULL;
    $id_estado = $_POST['id_estado'];

    $stid = oci_parse($conn, 'BEGIN FIDE_EMPLEADOS_TB_ACTUALIZAR_EMPLEADO_SP(:P_ID_EMPLEADO, :P_NOMBRE, :P_APELLIDO, :P_CORREO_ELECTRONICO, :P_TELEFONO, :P_ID_POSICION, :P_CONTRASENA, :P_ID_ESTADO); END;');

    // Bind de parámetros
    oci_bind_by_name($stid, ':P_ID_EMPLEADO', $id_empleado);
    oci_bind_by_name($stid, ':P_NOMBRE', $nombre);
    oci_bind_by_name($stid, ':P_APELLIDO', $apellido);
    oci_bind_by_name($stid, ':P_CORREO_ELECTRONICO', $correo);
    oci_bind_by_name($stid, ':P_TELEFONO', $telefono);
    oci_bind_by_name($stid, ':P_ID_POSICION', $id_posicion);
    oci_bind_by_name($stid, ':P_CONTRASENA', $contrasena, -1, SQLT_CHR);
    oci_bind_by_name($stid, ':P_ID_ESTADO', $id_estado);

    $success = oci_execute($stid);

    if ($success) {
        header('Location: empleados.php?msg=Empleado actualizado con éxito');
        exit;
    } else {
        $e = oci_error($stid);
        echo "Error al actualizar el empleado: " . $e['message'];
    }

    oci_free_statement($stid);
    oci_close($conn);
} else {
    die("No se recibieron datos del formulario.");
}
?>
