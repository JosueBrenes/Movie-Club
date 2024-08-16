<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

// Preparar la llamada al procedimiento almacenado para obtener los datos de comida
$stid_comida = oci_parse($conn, 'BEGIN FIDE_COMIDA_TB_OBTENER_COMIDA_SP(:p_cursor); END;');

// Crear y asociar el cursor de salida
$cursor_comida = oci_new_cursor($conn);
oci_bind_by_name($stid_comida, ':p_cursor', $cursor_comida, -1, OCI_B_CURSOR);

// Ejecutar el procedimiento almacenado para obtener los datos de comida
$success_comida = oci_execute($stid_comida);

if (!$success_comida) {
    $e = oci_error($stid_comida);
    echo "Error al ejecutar el procedimiento almacenado de comida: " . $e['message'];
    exit;
}

// Ejecutar el cursor para obtener los resultados de comida
$success_comida = oci_execute($cursor_comida);

if (!$success_comida) {
    $e = oci_error($cursor_comida);
    echo "Error al ejecutar el cursor de comida: " . $e['message'];
    exit;
}

// Preparar la llamada al procedimiento almacenado para obtener los datos de inventario
$stid_inventario = oci_parse($conn, 'BEGIN FIDE_INVENTARIO_TB_OBTENER_INVENTARIO_SP(:p_cursor); END;');

// Crear y asociar el cursor de salida
$cursor_inventario = oci_new_cursor($conn);
oci_bind_by_name($stid_inventario, ':p_cursor', $cursor_inventario, -1, OCI_B_CURSOR);

// Ejecutar el procedimiento almacenado para obtener los datos de inventario
$success_inventario = oci_execute($stid_inventario);

if (!$success_inventario) {
    $e = oci_error($stid_inventario);
    echo "Error al ejecutar el procedimiento almacenado de inventario: " . $e['message'];
    exit;
}

// Ejecutar el cursor para obtener los resultados de inventario
$success_inventario = oci_execute($cursor_inventario);

if (!$success_inventario) {
    $e = oci_error($cursor_inventario);
    echo "Error al ejecutar el cursor de inventario: " . $e['message'];
    exit;
}

// Almacenar los datos de inventario en un array
$inventario_data = [];
while ($row_inventario = oci_fetch_assoc($cursor_inventario)) {
    $inventario_data[$row_inventario['ID_INVENTARIO']] = $row_inventario['NOMBRE'];
}

// Preparar la llamada al procedimiento almacenado para obtener los estados
$stid_estado = oci_parse($conn, 'BEGIN FIDE_ESTADO_TB_OBTENER_ESTADO_SP(:p_cursor); END;');

// Crear y asociar el cursor de salida para los estados
$cursor_estado = oci_new_cursor($conn);
oci_bind_by_name($stid_estado, ':p_cursor', $cursor_estado, -1, OCI_B_CURSOR);

// Ejecutar el procedimiento almacenado para obtener los estados
$success_estado = oci_execute($stid_estado);

if (!$success_estado) {
    $e = oci_error($stid_estado);
    die("Error al ejecutar el procedimiento almacenado para obtener estados: " . $e['message']);
}

// Ejecutar el cursor para obtener los resultados de estados
$success_estado = oci_execute($cursor_estado);

if (!$success_estado) {
    $e = oci_error($cursor_estado);
    die("Error al ejecutar el cursor de estados: " . $e['message']);
}

// Crear un array para almacenar los estados
$estados = [];
while ($row_estado = oci_fetch_assoc($cursor_estado)) {
    $estados[$row_estado['ID_ESTADO']] = $row_estado['NOMBRE'];
}

oci_free_statement($stid_estado);
oci_free_statement($cursor_estado);
oci_free_statement($stid_inventario);
oci_free_statement($cursor_inventario);
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Comida - Movie Club</title>
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
                <h1 style="color: #333">Comida</h1>
                <a href="agregar_comida.php" class="button" style="background-color: #013e6a; color: white;">Agregar Nueva Comida</a>
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Inventario</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row_comida = oci_fetch_assoc($cursor_comida)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row_comida['ID_COMIDA'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($inventario_data[$row_comida['ID_INVENTARIO']] ?? 'Desconocido', ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row_comida['PRECIO'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($estados[$row_comida['ID_ESTADO']] ?? 'Desconocido', ENT_QUOTES); ?></td>
                                <td>
                                    <a href="editar_comida.php?id=<?php echo htmlspecialchars($row_comida['ID_COMIDA'], ENT_QUOTES); ?>" class="btn" style="background-color: #013e6a; color: white;">Editar</a>
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
    oci_free_statement($stid_comida);
    oci_free_statement($cursor_comida);
    oci_close($conn);
    ?>
</body>
</html>
