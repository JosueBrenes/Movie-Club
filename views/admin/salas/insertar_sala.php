<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $numero_asientos = $_POST['numero_asientos'];
    $id_tipo_sala = $_POST['id_tipo_sala'];
    $id_estado = $_POST['id_estado'];

    // Obtener el siguiente valor de la secuencia para ID_SALA
    $query = 'SELECT FIDE_SALA_SEQ.NEXTVAL AS id_sala FROM dual';
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    $row = oci_fetch_assoc($stid);
    $id_sala = $row['ID_SALA'];
    oci_free_statement($stid);

    // Llamar al procedimiento almacenado
    $proc = 'BEGIN FIDE_SALAS_TB_INSERTAR_SALAS_SP(:id_sala, :nombre, :numero_asientos, :id_tipo_sala, :id_estado); END;';
    $stid = oci_parse($conn, $proc);

    oci_bind_by_name($stid, ':id_sala', $id_sala);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':numero_asientos', $numero_asientos);
    oci_bind_by_name($stid, ':id_tipo_sala', $id_tipo_sala);
    oci_bind_by_name($stid, ':id_estado', $id_estado);

    $result = oci_execute($stid);

    if ($result) {
        header('Location: salas.php');
        exit();
    } else {
        $e = oci_error($stid);
        echo "Error al agregar sala: " . htmlentities($e['message'], ENT_QUOTES);
    }

    oci_free_statement($stid);
    oci_close($conn);
}
?>
