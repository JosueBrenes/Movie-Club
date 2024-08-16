<?php
include '../../includes/database.php';

$mensaje = '';
$metodo_pago_id = isset($_POST['metodo_pago']) ? intval($_POST['metodo_pago']) : 0;

$id_reserva = isset($_GET['id_reserva']) ? intval($_GET['id_reserva']) : 0;
$id_cliente = isset($_GET['id_cliente']) ? intval($_GET['id_cliente']) : 0;
$id_funcion = isset($_GET['id_funcion']) ? intval($_GET['id_funcion']) : 0;
$cantidad_asientos = isset($_GET['cantidad_asientos']) ? intval($_GET['cantidad_asientos']) : 0;
$fecha_reserva = isset($_GET['fecha_reserva']) ? trim($_GET['fecha_reserva']) : '';

function obtener_metodos_pago() {
    global $conn;
    $sql = 'BEGIN FIDE_METODO_PAGO_TB_OBTENER_METODO_PAGO_SP(:p_cursor); END;';
    $stid = oci_parse($conn, $sql);

    $cursor = oci_new_cursor($conn);
    oci_bind_by_name($stid, ':p_cursor', $cursor, -1, OCI_B_CURSOR);

    oci_execute($stid);
    oci_execute($cursor);

    $metodos_pago = [];
    while (($row = oci_fetch_assoc($cursor)) != false) {
        $metodos_pago[] = $row;
    }

    oci_free_statement($stid);
    oci_free_statement($cursor);

    return $metodos_pago;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $metodo_pago = $_POST['metodo_pago'];

    if ($metodo_pago == 1) {
        $mensaje = "<p class='text-warning'>Tiene 24 horas para realizar el pago, de lo contrario la reserva será cancelada.</p>";
    } elseif ($metodo_pago == 2) {
        // Redirigir a la página con los datos de la reserva
        header('Location: procesando_factura.php?' . http_build_query([
            'id_reserva' => $id_reserva,
            'id_cliente' => $id_cliente,
            'id_funcion' => $id_funcion,
            'cantidad_asientos' => $cantidad_asientos,
            'fecha_reserva' => $fecha_reserva
        ]));
        exit;
    }
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
    <style>
        .additional-info {
            display: none;
        }
    </style>
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
        <h1 class="text-center mb-4">Pago de Reserva</h1>

        <?php if ($mensaje): ?>
            <div class="alert <?php echo strpos($mensaje, 'exitosamente') !== false ? 'alert-success' : (strpos($mensaje, 'complete todos los campos') !== false ? 'alert-danger' : 'alert-warning'); ?> text-center custom-alert mb-3" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post" id="payment-form">
                    <div class="form-group">
                        <label for="metodo_pago">Seleccione un Método de Pago</label>
                        <select id="metodo_pago" name="metodo_pago" class="form-control" required onchange="mostrarCamposTarjeta()">
                            <?php
                            $metodos_pago = obtener_metodos_pago();
                            foreach ($metodos_pago as $metodo) {
                                echo "<option value='" . htmlspecialchars($metodo['ID_METODO_PAGO']) . "' " . ($metodo_pago_id == $metodo['ID_METODO_PAGO'] ? 'selected' : '') . ">" . htmlspecialchars($metodo['NOMBRE']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div id="tarjeta-info" class="additional-info">
                        <h4>Información de la Tarjeta</h4>
                        <div class="form-group">
                            <label for="numero_tarjeta">Número de Tarjeta</label>
                            <input type="text" id="numero_tarjeta" name="numero_tarjeta" class="form-control" value="<?php echo isset($_POST['numero_tarjeta']) ? htmlspecialchars($_POST['numero_tarjeta']) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="nombre_titular">Nombre del Titular</label>
                            <input type="text" id="nombre_titular" name="nombre_titular" class="form-control" value="<?php echo isset($_POST['nombre_titular']) ? htmlspecialchars($_POST['nombre_titular']) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="fecha_vencimiento">Fecha de Vencimiento</label>
                            <input type="text" id="fecha_vencimiento" name="fecha_vencimiento" class="form-control" placeholder="MM/AA" value="<?php echo isset($_POST['fecha_vencimiento']) ? htmlspecialchars($_POST['fecha_vencimiento']) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="codigo_seguridad">Código de Seguridad</label>
                            <input type="text" id="codigo_seguridad" name="codigo_seguridad" class="form-control" value="<?php echo isset($_POST['codigo_seguridad']) ? htmlspecialchars($_POST['codigo_seguridad']) : ''; ?>" required>
                        </div>
                    </div>

                    <!-- Campos ocultos con datos de la reserva -->
                    <input type="hidden" name="id_reserva" value="<?php echo htmlspecialchars($id_reserva); ?>">
                    <input type="hidden" name="id_cliente" value="<?php echo htmlspecialchars($id_cliente); ?>">
                    <input type="hidden" name="id_funcion" value="<?php echo htmlspecialchars($id_funcion); ?>">
                    <input type="hidden" name="cantidad_asientos" value="<?php echo htmlspecialchars($cantidad_asientos); ?>">
                    <input type="hidden" name="fecha_reserva" value="<?php echo htmlspecialchars($fecha_reserva); ?>">

                    <button type="submit" class="btn btn-danger">Confirmar Pago</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function mostrarCamposTarjeta() {
            var metodoPago = document.getElementById('metodo_pago').value;
            var tarjetaInfo = document.getElementById('tarjeta-info');
            if (metodoPago == '2') {
                tarjetaInfo.style.display = 'block';
            } else {
                tarjetaInfo.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            mostrarCamposTarjeta();
        });
    </script>

    <?php include '../templates/footer.php'; ?>

</body>
</html>
