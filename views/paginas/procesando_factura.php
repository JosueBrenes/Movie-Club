<?php
include '../../includes/database.php';

$id_reserva = isset($_GET['id_reserva']) ? intval($_GET['id_reserva']) : 0;
$id_cliente = isset($_GET['id_cliente']) ? intval($_GET['id_cliente']) : 0;
$id_funcion = isset($_GET['id_funcion']) ? intval($_GET['id_funcion']) : 0;
$cantidad_asientos = isset($_GET['cantidad_asientos']) ? intval($_GET['cantidad_asientos']) : 0;
$fecha_reserva = isset($_GET['fecha_reserva']) ? trim($_GET['fecha_reserva']) : '';

$fecha_reserva_formateada = date('Y-m-d', strtotime($fecha_reserva));

$total = $cantidad_asientos * 3500;

$sequence_sql = 'SELECT FIDE_FACTURA_SEQ.NEXTVAL AS next_id FROM dual';
$stid = oci_parse($conn, $sequence_sql);
oci_execute($stid);
$row = oci_fetch_assoc($stid);
$id_factura = $row['NEXT_ID'];

$id_empleado = 1; 
$id_estado = 1; 

$procedure = 'BEGIN FIDE_FACTURA_TB_INSERTAR_FACTURA_SP(:p_id_factura, TO_DATE(:p_fecha_factura, \'YYYY-MM-DD\'), :p_id_cliente, :p_id_funcion, :p_id_empleado, :p_total, :p_id_estado); END;';
$stid = oci_parse($conn, $procedure);

oci_bind_by_name($stid, ':p_id_factura', $id_factura);
oci_bind_by_name($stid, ':p_fecha_factura', $fecha_reserva_formateada);
oci_bind_by_name($stid, ':p_id_cliente', $id_cliente);
oci_bind_by_name($stid, ':p_id_funcion', $id_funcion);
oci_bind_by_name($stid, ':p_id_empleado', $id_empleado);
oci_bind_by_name($stid, ':p_total', $total);
oci_bind_by_name($stid, ':p_id_estado', $id_estado);

if (oci_execute($stid)) {
    header('Location: factura.php?id_factura=' . urlencode($id_factura) . '&fecha_factura=' . urlencode($fecha_reserva_formateada) . '&id_cliente=' . urlencode($id_cliente) . '&id_funcion=' . urlencode($id_funcion) . '&id_empleado=' . urlencode($id_empleado) . '&total=' . urlencode($total));
    exit;
} else {
    $error = oci_error($stid);
    echo "<h3 class='text-center text-danger'>Error al procesar la factura: " . htmlspecialchars($error['message']) . "</h3>";
}

oci_free_statement($stid);
oci_close($conn);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Procesando Factura</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../../public/build/css/styles.css" />
</head>
<body>

    <header class="navbar">
        <a href="../../public/index.php" class="text-dark">
            <h1 class="font-weight-bold">Movie Club</h1>
        </a>
        <nav class="menu">
            <a href="../../public/index.php">Inicio</a>
            <a href="../../views/paginas/cartelera.php" class="btn-contact" style="background-color: #19a4bf">Cartelera</a>
            <a href="../../views/auth/login.php" class="btn-contact" style="background-color: #19a4bf">Administrar</a>
        </nav>
    </header>

    <div class="hero">
        <!-- Contenido del hero -->
    </div>

    <div class="container mt-5 mb-5">
        <h1 class="text-center mb-4">Procesando Factura</h1>
        <div class="card">
            <div class="card-body">
                <p><strong>ID Reserva:</strong> <?php echo htmlspecialchars($id_reserva); ?></p>
                <p><strong>ID Cliente:</strong> <?php echo htmlspecialchars($id_cliente); ?></p>
                <p><strong>ID Función:</strong> <?php echo htmlspecialchars($id_funcion); ?></p>
                <p><strong>Cantidad de Asientos:</strong> <?php echo htmlspecialchars($cantidad_asientos); ?></p>
                <p><strong>Fecha Reserva:</strong> <?php echo htmlspecialchars($fecha_reserva); ?></p>
            </div>
        </div>
    </div>

    <?php include '../templates/footer.php'; ?>

</body>
</html>
