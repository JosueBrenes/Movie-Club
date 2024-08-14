<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

// Preparar la llamada al procedimiento almacenado para obtener los datos de empleados
$stid_empleados = oci_parse($conn, 'BEGIN FIDE_EMPLEADOS_TB_OBTENER_EMPLEADOS_SP(:p_cursor); END;');

// Crear y asociar el cursor de salida
$cursor_empleados = oci_new_cursor($conn);
oci_bind_by_name($stid_empleados, ':p_cursor', $cursor_empleados, -1, OCI_B_CURSOR);

// Ejecutar el procedimiento almacenado para obtener los datos de empleados
$success_empleados = oci_execute($stid_empleados);

if (!$success_empleados) {
    $e = oci_error($stid_empleados);
    die("Error al ejecutar el procedimiento almacenado de empleados: " . $e['message']);
}

// Ejecutar el cursor para obtener los resultados de empleados
$success_empleados = oci_execute($cursor_empleados);

if (!$success_empleados) {
    $e = oci_error($cursor_empleados);
    die("Error al ejecutar el cursor de empleados: " . $e['message']);
}

// Preparar la llamada al procedimiento almacenado para obtener los datos de posiciones
$stid_posiciones = oci_parse($conn, 'BEGIN FIDE_POSICION_TB_OBTENER_POSICION_SP(:p_cursor); END;');

// Crear y asociar el cursor de salida
$cursor_posiciones = oci_new_cursor($conn);
oci_bind_by_name($stid_posiciones, ':p_cursor', $cursor_posiciones, -1, OCI_B_CURSOR);

// Ejecutar el procedimiento almacenado para obtener los datos de posiciones
$success_posiciones = oci_execute($stid_posiciones);

if (!$success_posiciones) {
    $e = oci_error($stid_posiciones);
    die("Error al ejecutar el procedimiento almacenado de posiciones: " . $e['message']);
}

// Ejecutar el cursor para obtener los resultados de posiciones
$success_posiciones = oci_execute($cursor_posiciones);

if (!$success_posiciones) {
    $e = oci_error($cursor_posiciones);
    die("Error al ejecutar el cursor de posiciones: " . $e['message']);
}

// Almacenar los datos de posiciones en un array
$posiciones_data = [];
while ($row_posicion = oci_fetch_assoc($cursor_posiciones)) {
    $posiciones_data[$row_posicion['ID_POSICION']] = $row_posicion['NOMBRE'];
}

oci_free_statement($stid_posiciones);
oci_free_statement($cursor_posiciones);
oci_close($conn);
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
                <a href="agregar_empleado.php" class="button">Agregar Nuevo Empleado</a>
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>ID Empleado</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Correo Electrónico</th>
                            <th>Teléfono</th>
                            <th>Puesto</th>
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
                                <td>
                                    <a href="editar_empleado.php?id=<?php echo htmlspecialchars($row['ID_EMPLEADO'], ENT_QUOTES); ?>" class="btn" style="background-color: #013e6a; color: white;">Editar</a>
                                    <a href="eliminar_empleado.php?id=<?php echo htmlspecialchars($row['ID_EMPLEADO'], ENT_QUOTES); ?>" class="btn btn-danger" style="background-color: #d9534f; color: white;">Eliminar</a>
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
