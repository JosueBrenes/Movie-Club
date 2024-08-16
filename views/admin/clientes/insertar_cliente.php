<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

// Obtener los datos del formulario
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$correo_electronico = $_POST['email'];
$id_estado = $_POST['id_estado'];

try {
    // Obtener el siguiente valor de la secuencia para ID_CLIENTE
    $query = 'SELECT FIDE_CLIENTES_SEQ.NEXTVAL AS id_cliente FROM dual';
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    $row = oci_fetch_assoc($stid);
    $id_cliente = $row['ID_CLIENTE'];
    oci_free_statement($stid);

    // Preparar la llamada al procedimiento almacenado
    $sql = 'BEGIN FIDE_CLIENTES_TB_INSERTAR_CLIENTES_SP(:id_cliente, :nombre, :apellido, :correo_electronico, :id_estado); END;';
    $stid = oci_parse($conn, $sql);

    // Asociar los parámetros
    oci_bind_by_name($stid, ':id_cliente', $id_cliente);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':apellido', $apellido);
    oci_bind_by_name($stid, ':correo_electronico', $correo_electronico);
    oci_bind_by_name($stid, ':id_estado', $id_estado);

    // Ejecutar el procedimiento
    $success = oci_execute($stid);

    if ($success) {
        // Redirigir a la página de clientes con un mensaje de éxito
        header('Location: clientes.php?msg=Cliente agregado con éxito');
        exit();
    } else {
        // Mostrar el mensaje de error
        $e = oci_error($stid);
        echo "Error al agregar cliente: " . htmlentities($e['message'], ENT_QUOTES);
    }

    // Liberar los recursos
    oci_free_statement($stid);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar la conexión
oci_close($conn);
?>
