<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

// Obtener los datos del formulario
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];

try {
    // Obtener el siguiente valor de la secuencia para ID_GENERO
    $query = 'SELECT FIDE_GENERO_SEQ.NEXTVAL AS id_genero FROM dual';
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    $row = oci_fetch_assoc($stid);
    $id_genero = $row['ID_GENERO'];
    oci_free_statement($stid);

    // Preparar la llamada al procedimiento almacenado
    $sql = 'BEGIN FIDE_GENERO_TB_INSERTAR_GENERO_SP(:id_genero, :nombre, :descripcion); END;';
    $stid = oci_parse($conn, $sql);

    // Asociar los parámetros
    oci_bind_by_name($stid, ':id_genero', $id_genero);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':descripcion', $descripcion);

    // Ejecutar el procedimiento
    $success = oci_execute($stid);

    if ($success) {
        // Redirigir a la página de géneros con un mensaje de éxito
        header('Location: genero.php?msg=Género agregado con éxito');
        exit();
    } else {
        // Mostrar el mensaje de error
        $e = oci_error($stid);
        echo "Error al agregar género: " . htmlentities($e['message'], ENT_QUOTES);
    }

    // Liberar los recursos
    oci_free_statement($stid);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar la conexión
oci_close($conn);
?>
