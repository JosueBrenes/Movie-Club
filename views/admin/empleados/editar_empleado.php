<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID del empleado no proporcionado.");
}

$id_empleado = $_GET['id'];

// Llamada al procedimiento almacenado para obtener el empleado
$stid = oci_parse($conn, 'BEGIN FIDE_EMPLEADOS_TB_OBTENER_EMPLEADOS_SP(:p_cursor); END;');
$cursor = oci_new_cursor($conn);
oci_bind_by_name($stid, ':p_cursor', $cursor, -1, OCI_B_CURSOR);
oci_execute($stid);
oci_execute($cursor);

$empleado = null;
while (($row = oci_fetch_assoc($cursor)) !== false) {
    if ($row['ID_EMPLEADO'] == $id_empleado) {
        $empleado = $row;
        break;
    }
}

// Obtener los estados
$stid_estados = oci_parse($conn, 'BEGIN FIDE_ESTADO_TB_OBTENER_ESTADO_SP(:p_cursor); END;');
$cursor_estados = oci_new_cursor($conn);
oci_bind_by_name($stid_estados, ':p_cursor', $cursor_estados, -1, OCI_B_CURSOR);
oci_execute($stid_estados);
oci_execute($cursor_estados);

$estados_data = [];
while ($row_estado = oci_fetch_assoc($cursor_estados)) {
    $estados_data[$row_estado['ID_ESTADO']] = $row_estado['NOMBRE'];
}

// Obtener las posiciones
$stid_posiciones = oci_parse($conn, 'BEGIN FIDE_POSICION_TB_OBTENER_POSICION_SP(:p_cursor); END;');
$cursor_posiciones = oci_new_cursor($conn);
oci_bind_by_name($stid_posiciones, ':p_cursor', $cursor_posiciones, -1, OCI_B_CURSOR);
oci_execute($stid_posiciones);
oci_execute($cursor_posiciones);

$posiciones_data = [];
while ($row_posicion = oci_fetch_assoc($cursor_posiciones)) {
    $posiciones_data[$row_posicion['ID_POSICION']] = $row_posicion['NOMBRE'];
}

// Libera los recursos y cierra la conexión
oci_free_statement($stid);
oci_free_statement($cursor);
oci_free_statement($stid_estados);
oci_free_statement($cursor_estados);
oci_free_statement($stid_posiciones);
oci_free_statement($cursor_posiciones);
oci_close($conn);

if (!$empleado) {
    die("No se encontró el empleado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Editar Empleado - Movie Club</title>
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
                <h1 style="color: #333">Editar Empleado</h1>
                <form action="actualizar_empleado.php" method="POST">
                    <input type="hidden" name="id_empleado" value="<?php echo htmlspecialchars($empleado['ID_EMPLEADO'], ENT_QUOTES); ?>">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($empleado['NOMBRE'], ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido</label>
                        <input type="text" id="apellido" name="apellido" class="form-control" value="<?php echo htmlspecialchars($empleado['APELLIDO'], ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo Electrónico</label>
                        <input type="email" id="correo" name="correo" class="form-control" value="<?php echo htmlspecialchars($empleado['CORREO_ELECTRONICO'], ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" class="form-control" value="<?php echo htmlspecialchars($empleado['TELEFONO'], ENT_QUOTES); ?>">
                    </div>
                    <div class="form-group">
                        <label for="id_posicion">Puesto</label>
                        <select id="id_posicion" name="id_posicion" class="form-control" required>
                            <?php foreach ($posiciones_data as $id_posicion => $nombre_posicion): ?>
                                <option value="<?php echo htmlspecialchars($id_posicion); ?>" <?php echo $empleado['ID_POSICION'] == $id_posicion ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($nombre_posicion); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="contrasena">Contraseña</label>
                        <input type="password" id="contrasena" name="contrasena" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select class="form-control" id="estado" name="id_estado" required>
                            <?php foreach ($estados_data as $id_estado => $descripcion_estado): ?>
                                <option value="<?php echo htmlspecialchars($id_estado); ?>" <?php echo $empleado['ID_ESTADO'] == $id_estado ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($descripcion_estado); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn" style="background-color: #013e6a; color: white; margin-bottom: 2rem;">Actualizar</button>
                </form>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer_area">
            <p class="footer_text">
                &copy; 2024 Movie Club. Todos los derechos reservados.
            </p>
        </footer>
    </div>
</body>
</html>
