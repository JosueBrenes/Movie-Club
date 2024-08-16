<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $id_estado = $_POST['id_estado'];

    // Obtener el siguiente valor de la secuencia para ID_POSICION
    $query = 'SELECT FIDE_POSICION_SEQ.NEXTVAL AS id_posicion FROM dual';
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    $row = oci_fetch_assoc($stid);
    $id_posicion = $row['ID_POSICION'];
    oci_free_statement($stid);

    // Llamar al procedimiento almacenado
    $proc = 'BEGIN FIDE_POSICION_TB_INSERTAR_POSICION_SP(:id_posicion, :nombre, :descripcion, :id_estado); END;';
    $stid = oci_parse($conn, $proc);

    oci_bind_by_name($stid, ':id_posicion', $id_posicion);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':descripcion', $descripcion);
    oci_bind_by_name($stid, ':id_estado', $id_estado);

    $result = oci_execute($stid);

    if ($result) {
        header('Location: posiciones.php');
        exit();
    } else {
        $e = oci_error($stid);
        echo "Error al agregar posición: " . htmlentities($e['message'], ENT_QUOTES);
    }

    oci_free_statement($stid);
    oci_close($conn);
}
?>
