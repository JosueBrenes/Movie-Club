<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$correo_electronico = $_POST['email'];

try {
    $query = 'SELECT FIDE_CLIENTES_SEQ.NEXTVAL AS id_cliente FROM dual';
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    $row = oci_fetch_assoc($stid);
    $id_cliente = $row['ID_CLIENTE'];
    
    $sql = 'INSERT INTO FIDE_CLIENTES_TB (ID_CLIENTE, NOMBRE, APELLIDO, CORREO_ELECTRONICO) 
            VALUES (:id_cliente, :nombre, :apellido, :correo_electronico)';
    $stid = oci_parse($conn, $sql);

    oci_bind_by_name($stid, ':id_cliente', $id_cliente);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':apellido', $apellido);
    oci_bind_by_name($stid, ':correo_electronico', $correo_electronico);

    $success = oci_execute($stid);

    if ($success) {
        header('Location: clientes.php');
        exit();
    } else {
        $e = oci_error($stid);
        echo "Error al agregar cliente: " . $e['message'];
    }

    oci_free_statement($stid);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

oci_close($conn);
?>
