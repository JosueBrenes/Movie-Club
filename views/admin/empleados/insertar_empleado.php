<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$correo = $_POST['correo'];
$telefono = $_POST['telefono'];
$id_posicion = $_POST['id_posicion'];
$contrasena = $_POST['contrasena'];

$hashed_contrasena = password_hash($contrasena, PASSWORD_DEFAULT);

$query = 'SELECT FIDE_EMPLEADOS_SEQ.NEXTVAL AS id_empleado FROM dual';
$stid = oci_parse($conn, $query);
oci_execute($stid);
$row = oci_fetch_assoc($stid);
$id_empleado = $row['ID_EMPLEADO'];

$sql = 'INSERT INTO FIDE_EMPLEADOS_TB (ID_EMPLEADO, NOMBRE, APELLIDO, CORREO_ELECTRONICO, TELEFONO, ID_POSICION, CONTRASENA) 
        VALUES (:id_empleado, :nombre, :apellido, :correo, :telefono, :id_posicion, :contrasena)';
$stid = oci_parse($conn, $sql);

oci_bind_by_name($stid, ':id_empleado', $id_empleado);
oci_bind_by_name($stid, ':nombre', $nombre);
oci_bind_by_name($stid, ':apellido', $apellido);
oci_bind_by_name($stid, ':correo', $correo);
oci_bind_by_name($stid, ':telefono', $telefono);
oci_bind_by_name($stid, ':id_posicion', $id_posicion);
oci_bind_by_name($stid, ':contrasena', $hashed_contrasena);

$success = oci_execute($stid);

if ($success) {
    header('Location: empleados.php');
    exit();
} else {
    $e = oci_error($stid);
    echo "Error al agregar empleado: " . $e['message'];
}

oci_free_statement($stid);
oci_close($conn);
?>
