<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID del cliente no proporcionado.");
}

$cliente_id = $_GET['id'];

// Preparar la llamada al procedimiento almacenado
$stid = oci_parse($conn, 'BEGIN FIDE_CLIENTES_TB_OBTENER_CLIENTES_SP(:p_cursor); END;');

// Crear y asociar el cursor de salida
$cursor = oci_new_cursor($conn);
oci_bind_by_name($stid, ':p_cursor', $cursor, -1, OCI_B_CURSOR);

// Ejecutar el procedimiento almacenado
$success = oci_execute($stid);

if (!$success) {
    $e = oci_error($stid);
    die("Error al ejecutar el procedimiento almacenado: " . $e['message']);
}

// Ejecutar el cursor para obtener los resultados
$success = oci_execute($cursor);

if (!$success) {
    $e = oci_error($cursor);
    die("Error al ejecutar el cursor: " . $e['message']);
}

// Buscar el cliente en el cursor
$cliente = null;
while (($row = oci_fetch_assoc($cursor)) != false) {
    if ($row['ID_CLIENTE'] == $cliente_id) {
        $cliente = $row;
        break;
    }
}

if (!$cliente) {
    die("No se encontró el cliente.");
}

// Obtener los estados
$stid_estados = oci_parse($conn, 'BEGIN FIDE_ESTADO_TB_OBTENER_ESTADO_SP(:p_cursor); END;');
$cursor_estados = oci_new_cursor($conn);
oci_bind_by_name($stid_estados, ':p_cursor', $cursor_estados, -1, OCI_B_CURSOR);
$success_estados = oci_execute($stid_estados);

if (!$success_estados) {
    $e = oci_error($stid_estados);
    die("Error al ejecutar el procedimiento almacenado de estados: " . $e['message']);
}

$success_estados = oci_execute($cursor_estados);

if (!$success_estados) {
    $e = oci_error($cursor_estados);
    die("Error al ejecutar el cursor de estados: " . $e['message']);
}

$estados_data = [];
while ($row_estado = oci_fetch_assoc($cursor_estados)) {
    $estados_data[$row_estado['ID_ESTADO']] = $row_estado['NOMBRE'];
}

oci_free_statement($stid_estados);
oci_free_statement($cursor_estados);
oci_free_statement($stid);
oci_free_statement($cursor);
oci_close($conn);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Editar Cliente - Movie Club</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../../../public/build/css/stylesDash.css" />
    <link rel="icon" href="../../../public/build/img/icon.png" type="image/x-icon" />
    <link rel="shortcut icon" href="../../../public/build/img/icon.png" type="image/x-icon" />
</head>
<body>
    <!-- Sidebar -->
    <?php include '../../templates/sidebar.php'; ?>
    
    <!-- Content -->
    <div class="content">
        <!-- Header -->
        <header class="header_area">
            <a href="../dashboard.php" class="header_link">
                <h1>Movie Club</h1>
            </a>
        </header>

        <!-- Main Content -->
        <section class="options_area">
            <div class="container mt-5">
                <h1 style="color: #333">Editar Cliente</h1>
                <form action="actualizar_cliente.php" method="POST">
                    <input type="hidden" name="cliente_id" value="<?php echo htmlspecialchars($cliente['ID_CLIENTE'] ?? '', ENT_QUOTES); ?>">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($cliente['NOMBRE'] ?? '', ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido</label>
                        <input type="text" id="apellido" name="apellido" class="form-control" value="<?php echo htmlspecialchars($cliente['APELLIDO'] ?? '', ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($cliente['CORREO_ELECTRONICO'] ?? '', ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select class="form-control" id="estado" name="id_estado" required>
                            <?php foreach ($estados_data as $id_estado => $descripcion_estado): ?>
                                <option value="<?php echo htmlspecialchars($id_estado); ?>" <?php echo $cliente['ID_ESTADO'] == $id_estado ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($descripcion_estado); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn" style="background-color: #013e6a; color: white; margin-bottom: 2rem;">Actualizar Cliente</button>
                </form>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer_area">
            <p class="footer_text">&copy; 2024 Movie Club. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>
