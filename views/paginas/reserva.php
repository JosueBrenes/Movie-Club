<?php
include '../../includes/database.php';

$id_funcion = isset($_GET['id_funcion']) ? $_GET['id_funcion'] : null;
$id_cliente = isset($_GET['id_cliente']) ? $_GET['id_cliente'] : null;
$fecha_reserva = isset($_GET['fecha']) ? $_GET['fecha'] : '';

$mensaje = '';

function obtener_clientes() {
    global $conn;
    $sql = 'BEGIN FIDE_CLIENTES_TB_OBTENER_CLIENTES_SP(:p_cursor); END;';
    $stid = oci_parse($conn, $sql);

    $cursor = oci_new_cursor($conn);
    oci_bind_by_name($stid, ':p_cursor', $cursor, -1, OCI_B_CURSOR);

    oci_execute($stid);
    oci_execute($cursor);
    
    $clientes = [];
    while (($row = oci_fetch_assoc($cursor)) != false) {
        $clientes[] = $row;
    }

    oci_free_statement($stid);
    oci_free_statement($cursor);

    return $clientes;
}

function obtener_nombre_cliente($id_cliente) {
    global $conn;
    $clientes = obtener_clientes();
    foreach ($clientes as $cliente) {
        if ($cliente['ID_CLIENTE'] == $id_cliente) {
            return $cliente['NOMBRE'] . ' ' . $cliente['APELLIDO'];
        }
    }
    return 'Nombre no disponible';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_cliente = $_POST['id_cliente'];
    $id_funcion = $_POST['id_funcion'];
    $fecha_reserva = $_POST['fecha_reserva'];
    $cantidad_asientos = $_POST['cantidad_asientos'];

    $fecha_reserva_formateada = date('Y-m-d', strtotime($fecha_reserva));

    function obtener_siguiente_valor_secuencia($sec_name) {
        global $conn;
        $sql = "SELECT $sec_name.NEXTVAL AS NEXTVAL FROM DUAL";
        $stid = oci_parse($conn, $sql);
        oci_execute($stid);
        $row = oci_fetch_assoc($stid);
        oci_free_statement($stid);
        return $row['NEXTVAL'];
    }

    $id_reserva = obtener_siguiente_valor_secuencia('FIDE_RESERVAS_TB_SEQ');

    $sql = 'BEGIN FIDE_RESERVAS_TB_INSERTAR_RESERVA_SP(:id_reserva, :id_cliente, :id_funcion, TO_DATE(:fecha_reserva, \'YYYY-MM-DD\'), :cantidad_asientos); END;';
    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ':id_reserva', $id_reserva);
    oci_bind_by_name($stid, ':id_cliente', $id_cliente);
    oci_bind_by_name($stid, ':id_funcion', $id_funcion);
    oci_bind_by_name($stid, ':fecha_reserva', $fecha_reserva_formateada);
    oci_bind_by_name($stid, ':cantidad_asientos', $cantidad_asientos);

    if (oci_execute($stid)) {
        $mensaje = "<p class='text-success'>Reserva creada exitosamente.</p>";
        $redireccionar = true; 
    } else {
        $error = oci_error($stid);
        $mensaje = "<p class='text-danger'>Error: " . $error['message'] . "</p>";
    }
    oci_free_statement($stid);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Movie Club</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../../public/build/css/stylesCartelera.css" />
    <link rel="stylesheet" href="../../public/build/css/styles.css" />
    <link rel="icon" href="../../public/build/img/icon.png" type="image/x-icon" />
    <link rel="shortcut icon" href="../../public/build/img/icon.png" type="image/x-icon" />
    
    <?php if (isset($redireccionar) && $redireccionar): ?>
        <meta http-equiv="refresh" content="5;url=../../public/index.php" />
    <?php endif; ?>
</head>
<body>
    <header class="navbar">
        <a href="../../public/index.php" class="text-dark">
            <h1 class="font-weight-bold">Movie Club</h1>
        </a>
        <nav class="menu">
            <a href="../../views/paginas/cartelera.php" class="btn-contact" style="background-color: #19a4bf">Cartelera</a>
            <a href="../views/auth/login.php" class="btn-contact" style="background-color: #19a4bf">Administrar</a>
        </nav>
    </header>

    <div class="hero">
        <!-- Contenido del hero -->
    </div>

    <div class="container mt-5 mb-5">
        <h1 class="text-center mb-4">Generar Reserva</h1>
        
        <!-- Mostrar el mensaje de éxito o error -->
        <?php if ($mensaje): ?>
            <div class="alert <?php echo strpos($mensaje, 'Éxito') !== false ? 'alert-success' : 'alert-success'; ?> text-center custom-alert mb-3" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="id_funcion" value="<?php echo htmlspecialchars($id_funcion); ?>">
                    <input type="hidden" name="id_cliente" value="<?php echo htmlspecialchars($id_cliente); ?>">
                    <div class="form-group">
                        <label for="nombre_cliente">Nombre del Cliente</label>
                        <input type="text" id="nombre_cliente" class="form-control" value="<?php echo htmlspecialchars(obtener_nombre_cliente($id_cliente)); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="fecha_reserva">Fecha de Reserva</label>
                        <input type="date" id="fecha_reserva" name="fecha_reserva" class="form-control" value="<?php echo htmlspecialchars($fecha_reserva); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="cantidad_asientos">Cantidad de Asientos</label>
                        <input type="number" id="cantidad_asientos" name="cantidad_asientos" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-danger">Reservar</button>
                </form>
            </div>
        </div>
    </div>

    <?php include '../templates/footer.php'; ?>

</body>
</html>
