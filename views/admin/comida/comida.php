<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

// Preparar la llamada al procedimiento almacenado
$stid = oci_parse($conn, 'BEGIN FIDE_COMIDA_TB_OBTENER_COMIDA_SP(:p_cursor); END;');

// Crear y asociar el cursor de salida
$cursor = oci_new_cursor($conn);
oci_bind_by_name($stid, ':p_cursor', $cursor, -1, OCI_B_CURSOR);

// Ejecutar el procedimiento almacenado
$success = oci_execute($stid);

if (!$success) {
    $e = oci_error($stid);
    echo "Error al ejecutar el procedimiento almacenado: " . $e['message'];
    exit;
}

// Ejecutar el cursor para obtener los resultados
$success = oci_execute($cursor);

if (!$success) {
    $e = oci_error($cursor);
    echo "Error al ejecutar el cursor: " . $e['message'];
    exit;
}
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
                            <th>ID Comida</th>
                            <th>ID Inventario</th>
                            <th>Precio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = oci_fetch_assoc($cursor)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['ID_COMIDA'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['ID_INVENTARIO'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['PRECIO'], ENT_QUOTES); ?></td>
                                <td>
                                    <a href="editar_comida.php?id=<?php echo htmlspecialchars($row['ID_COMIDA'], ENT_QUOTES); ?>" class="btn" style="background-color: #013e6a; color: white;">Editar</a>
                                    <a href="eliminar_comida.php?id=<?php echo htmlspecialchars($row['ID_COMIDA'], ENT_QUOTES); ?>" class="btn btn-danger" style="background-color: #d9534f; color: white;" onclick="return confirm('¿Estás seguro de que deseas eliminar esta comida?');">Eliminar</a>
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
    oci_free_statement($stid);
    oci_free_statement($cursor);
    oci_close($conn);
    ?>
</body>
</html>
