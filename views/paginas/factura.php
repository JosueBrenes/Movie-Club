<?php
include '../../includes/database.php';

$factura_id = isset($_GET['id_factura']) ? intval($_GET['id_factura']) : 0;
$funcion_id = isset($_GET['id_funcion']) ? intval($_GET['id_funcion']) : 0;
$mensaje = '';

function ejecutar_procedimiento($procedimiento) {
    global $conn;
    $cursor = oci_new_cursor($conn);
    $sql = "BEGIN $procedimiento(:p_cursor); END;";
    $stid = oci_parse($conn, $sql);

    if (!$stid) {
        return 'Error al preparar la consulta: ' . oci_error($conn)['message'];
    }

    oci_bind_by_name($stid, ':p_cursor', $cursor, -1, OCI_B_CURSOR);
    $r = oci_execute($stid);

    if (!$r) {
        return 'Error al ejecutar la consulta: ' . oci_error($stid)['message'];
    }

    $r = oci_execute($cursor);
    if (!$r) {
        return 'Error al ejecutar el cursor: ' . oci_error($cursor)['message'];
    }

    $resultados = [];
    while (($row = oci_fetch_assoc($cursor)) != false) {
        $resultados[] = $row;
    }

    oci_free_statement($stid);
    oci_free_statement($cursor);

    return $resultados;
}

function obtener_factura($factura_id) {
    global $conn;
    $sql = 'SELECT * FROM FIDE_FACTURA_TB WHERE ID_FACTURA = :id_factura';
    $stid = oci_parse($conn, $sql);

    if (!$stid) {
        return 'Error al preparar la consulta: ' . oci_error($conn)['message'];
    }

    oci_bind_by_name($stid, ':id_factura', $factura_id);
    $r = oci_execute($stid);

    if (!$r) {
        return 'Error al ejecutar la consulta: ' . oci_error($stid)['message'];
    }

    $factura = oci_fetch_assoc($stid);
    oci_free_statement($stid);

    return $factura;
}

$factura = obtener_factura($factura_id);

if (is_string($factura)) {
    $mensaje = $factura;
}

$clientes = ejecutar_procedimiento('FIDE_CLIENTES_TB_OBTENER_CLIENTES_SP');
$empleados = ejecutar_procedimiento('FIDE_EMPLEADOS_TB_OBTENER_EMPLEADOS_SP');

$cliente_nombre = 'Nombre no disponible';
$empleado_nombre = 'Nombre no disponible';

if ($factura) {
    foreach ($clientes as $cliente) {
        if ($cliente['ID_CLIENTE'] == $factura['ID_CLIENTE']) {
            $cliente_nombre = $cliente['NOMBRE'] . ' ' . $cliente['APELLIDO']; 
            break;
        }
    }

    foreach ($empleados as $empleado) {
        if ($empleado['ID_EMPLEADO'] == $factura['ID_EMPLEADO']) {
            $empleado_nombre = $empleado['NOMBRE'];
            break;
        }
    }
}

oci_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Factura - Movie Club</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../../public/build/css/styles.css" />
    <link rel="icon" href="../../public/build/img/icon.png" type="image/x-icon" />
    <link rel="shortcut icon" href="../../public/build/img/icon.png" type="image/x-icon" />
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
        <h1 class="text-center mb-4">Detalles de la Factura</h1>

        <?php if ($mensaje): ?>
            <div class="alert alert-danger text-center mb-3" role="alert">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <?php if ($factura): ?>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Factura: <?php echo htmlspecialchars($factura['ID_FACTURA']); ?></h4>
                    <p><strong>Fecha:</strong> <?php echo htmlspecialchars($factura['FECHA_FACTURA']); ?></p>
                    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($cliente_nombre); ?></p>
                    <p><strong>Total:</strong> $<?php echo htmlspecialchars(number_format($factura['TOTAL'], 2)); ?></p>
                    <p><strong>Estado:</strong> <?php echo htmlspecialchars($factura['ID_ESTADO'] == 1 ? 'Pagada' : 'Pendiente'); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../templates/footer.php'; ?>

</body>
</html>
