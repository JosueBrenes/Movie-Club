<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

// Preparar y ejecutar la llamada al procedimiento almacenado para obtener inventario
$stid_inventario = oci_parse($conn, 'BEGIN FIDE_INVENTARIO_TB_OBTENER_INVENTARIO_SP(:p_cursor); END;');
$cursor_inventario = oci_new_cursor($conn);
oci_bind_by_name($stid_inventario, ':p_cursor', $cursor_inventario, -1, OCI_B_CURSOR);
$success_inventario = oci_execute($stid_inventario);
if (!$success_inventario) {
    $e = oci_error($stid_inventario);
    die("Error al ejecutar el procedimiento almacenado de inventario: " . $e['message']);
}
$success_inventario = oci_execute($cursor_inventario);
if (!$success_inventario) {
    $e = oci_error($cursor_inventario);
    die("Error al ejecutar el cursor de inventario: " . $e['message']);
}

// Preparar y ejecutar la llamada al procedimiento almacenado para obtener proveedores
$stid_proveedores = oci_parse($conn, 'BEGIN FIDE_PROVEEDORES_TB_OBTENER_PROVEEDORES_SP(:p_cursor); END;');
$cursor_proveedores = oci_new_cursor($conn);
oci_bind_by_name($stid_proveedores, ':p_cursor', $cursor_proveedores, -1, OCI_B_CURSOR);
$success_proveedores = oci_execute($stid_proveedores);
if (!$success_proveedores) {
    $e = oci_error($stid_proveedores);
    die("Error al ejecutar el procedimiento almacenado de proveedores: " . $e['message']);
}
$success_proveedores = oci_execute($cursor_proveedores);
if (!$success_proveedores) {
    $e = oci_error($cursor_proveedores);
    die("Error al ejecutar el cursor de proveedores: " . $e['message']);
}

// Almacenar los datos de proveedores en un array
$proveedores_data = [];
while ($row_proveedor = oci_fetch_assoc($cursor_proveedores)) {
    $proveedores_data[$row_proveedor['ID_PROVEEDOR']] = $row_proveedor['NOMBRE'];
}

// Preparar la llamada al procedimiento almacenado para obtener los estados
$stid_estado = oci_parse($conn, 'BEGIN FIDE_ESTADO_TB_OBTENER_ESTADO_SP(:p_cursor); END;');

// Crear y asociar el cursor de salida para los estados
$cursor_estado = oci_new_cursor($conn);
oci_bind_by_name($stid_estado, ':p_cursor', $cursor_estado, -1, OCI_B_CURSOR);

// Ejecutar el procedimiento almacenado para obtener los estados
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
oci_free_statement($stid_proveedores);
oci_free_statement($cursor_proveedores);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inventario - Movie Club</title>
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
                <h1 style="color: #333">Inventario</h1>
                <a href="agregar_inventario.php" class="button" style="background-color: #013e6a; color: white;">Agregar Nuevo Inventario</a>
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Cantidad</th>
                            <th>Proveedor</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = oci_fetch_assoc($cursor_inventario)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['ID_INVENTARIO'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['NOMBRE'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['DESCRIPCION'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['CANTIDAD'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($proveedores_data[$row['ID_PROVEEDOR']] ?? 'Desconocido', ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($estados[$row['ID_ESTADO']], ENT_QUOTES); ?></td>
                                <td>
                                    <a href="editar_inventario.php?id=<?php echo htmlspecialchars($row['ID_INVENTARIO'], ENT_QUOTES); ?>" class="btn" style="background-color: #013e6a; color: white;">Editar</a>
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
    oci_free_statement($stid_inventario);
    oci_free_statement($cursor_inventario);
    oci_close($conn);
    ?>
</body>
</html>
