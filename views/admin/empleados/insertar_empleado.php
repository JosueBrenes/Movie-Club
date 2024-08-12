<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

// Obtener los datos del formulario
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$correo_electronico = $_POST['correo'];
$telefono = $_POST['telefono'];
$id_posicion = $_POST['id_posicion'];
$contrasena = $_POST['contrasena'];

try {
    // Obtener el siguiente valor de la secuencia para ID_EMPLEADO
    $query = 'SELECT FIDE_EMPLEADOS_SEQ.NEXTVAL AS id_empleado FROM dual';
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    $row = oci_fetch_assoc($stid);
    $id_empleado = $row['ID_EMPLEADO'];
    oci_free_statement($stid);

    // Preparar la llamada al procedimiento almacenado
    $sql = 'BEGIN FIDE_EMPLEADOS_TB_INSERTAR_EMPLEADO_SP(:id_empleado, :nombre, :apellido, :correo_electronico, :telefono, :id_posicion); END;';
    $stid = oci_parse($conn, $sql);

    // Asociar los parámetros
    oci_bind_by_name($stid, ':id_empleado', $id_empleado);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':apellido', $apellido);
    oci_bind_by_name($stid, ':correo_electronico', $correo_electronico);
    oci_bind_by_name($stid, ':telefono', $telefono);
    oci_bind_by_name($stid, ':id_posicion', $id_posicion);

    // Ejecutar el procedimiento
    $success = oci_execute($stid);

    if ($success) {
        // Redirigir a la página de empleados con un mensaje de éxito
        header('Location: empleados.php?msg=Empleado agregado con éxito');
        exit();
    } else {
        // Mostrar el mensaje de error
        $e = oci_error($stid);
        echo "Error al agregar empleado: " . htmlentities($e['message'], ENT_QUOTES);
    }

    // Liberar los recursos
    oci_free_statement($stid);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar la conexión
oci_close($conn);
?>
