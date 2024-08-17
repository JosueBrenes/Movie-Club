<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

// Obtener estados
$stid_estado = oci_parse($conn, 'BEGIN FIDE_ESTADO_TB_OBTENER_ESTADO_SP(:p_cursor); END;');
$cursor_estado = oci_new_cursor($conn);
oci_bind_by_name($stid_estado, ':p_cursor', $cursor_estado, -1, OCI_B_CURSOR);

$success = oci_execute($stid_estado);
oci_execute($cursor_estado);

if (!$success) {
    $e = oci_error($stid_estado);
    die("Error al ejecutar el procedimiento almacenado para obtener estados: " . $e['message']);
}

// Crear un array para almacenar los estados
$estados = [];
while ($row_estado = oci_fetch_assoc($cursor_estado)) {
    $estados[$row_estado['ID_ESTADO']] = $row_estado['NOMBRE'];
}

oci_free_statement($stid_estado);
oci_free_statement($cursor_estado);

// Obtener posiciones
$stid_posiciones = oci_parse($conn, 'BEGIN FIDE_POSICION_TB_OBTENER_POSICION_SP(:p_cursor); END;');
$cursor_posiciones = oci_new_cursor($conn);
oci_bind_by_name($stid_posiciones, ':p_cursor', $cursor_posiciones, -1, OCI_B_CURSOR);

$success = oci_execute($stid_posiciones);
oci_execute($cursor_posiciones);

if (!$success) {
    $e = oci_error($stid_posiciones);
    die("Error al ejecutar el procedimiento almacenado para obtener posiciones: " . $e['message']);
}

// Crear un array para almacenar las posiciones
$posiciones_data = [];
while ($row_posicion = oci_fetch_assoc($cursor_posiciones)) {
    $posiciones_data[$row_posicion['ID_POSICION']] = $row_posicion['NOMBRE'];
}

oci_free_statement($stid_posiciones);
oci_free_statement($cursor_posiciones);

// Obtener la posición seleccionada para filtrar
$id_posicion = isset($_POST['id_posicion']) ? intval($_POST['id_posicion']) : null;

// Llamar a la función FILTRAR_POR_POSICION para obtener los empleados filtrados
$stid_empleados = oci_parse($conn, 'BEGIN :p_cursor := FIDE_EMPLEADOS_TB_FILTRAR_POSICION_FN(:p_id_posicion); END;');
$cursor_empleados = oci_new_cursor($conn);
oci_bind_by_name($stid_empleados, ':p_id_posicion', $id_posicion, -1, SQLT_INT);
oci_bind_by_name($stid_empleados, ':p_cursor', $cursor_empleados, -1, OCI_B_CURSOR);

$success = oci_execute($stid_empleados);

if (!$success) {
    $e = oci_error($stid_empleados);
    die("Error al ejecutar la función FILTRAR_POR_POSICION: " . $e['message']);
}

$success = oci_execute($cursor_empleados);

if (!$success) {
    $e = oci_error($cursor_empleados);
    die("Error al ejecutar el cursor: " . $e['message']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Empleados - Movie Club</title>
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
                <h1 style="color: #333">Empleados</h1>

                <!-- Formulario para seleccionar posición -->
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="id_posicion">Filtrar por Posición:</label>
                        <select id="id_posicion" name="id_posicion" class="form-control">
                            <option value="">Todos</option>
                            <?php foreach ($posiciones_data as $id => $nombre): ?>
                                <option value="<?php echo htmlspecialchars($id, ENT_QUOTES); ?>" <?php echo ($id_posicion == $id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($nombre, ENT_QUOTES); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn button" style="font-weight: bold !important;">Filtrar</button>
                    <a href="agregar_empleado.php" class="button">Agregar Nuevo Empleado</a>
                </form>

                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Correo Electrónico</th>
                            <th>Teléfono</th>
                            <th>Puesto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = oci_fetch_assoc($cursor_empleados)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['ID_EMPLEADO'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['NOMBRE'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['APELLIDO'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['CORREO_ELECTRONICO'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['TELEFONO'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($posiciones_data[$row['ID_POSICION']] ?? 'Desconocido', ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($estados[$row['ID_ESTADO']] ?? 'Desconocido', ENT_QUOTES); ?></td>
                                <td>
                                    <a href="editar_empleado.php?id=<?php echo htmlspecialchars($row['ID_EMPLEADO'], ENT_QUOTES); ?>" class="btn" style="background-color: #013e6a; color: white;">Editar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer_area">
            <p class="footer_text">
                &copy; 2024 Movie Club. Todos los derechos reservados.
            </p>
        </footer>
    </div>

    <?php
    oci_free_statement($stid_empleados);
    oci_free_statement($cursor_empleados);
    oci_close($conn);
    ?>
</body>
</html>
