<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

// Preparar la llamada a la función almacenada
$stid = oci_parse($conn, 'BEGIN :p_cursor := FIDE_CLIENTES_TB_FILTRAR_ORDEN_ALFABETICO_FN(); END;');

// Crear y asociar el cursor de salida
$cursor = oci_new_cursor($conn);
oci_bind_by_name($stid, ':p_cursor', $cursor, -1, OCI_B_CURSOR);

// Ejecutar la función almacenada
$success = oci_execute($stid);

if (!$success) {
    $e = oci_error($stid);
    die("Error al ejecutar la función almacenada: " . $e['message']);
}

// Ejecutar el cursor para obtener los resultados
$success = oci_execute($cursor);

if (!$success) {
    $e = oci_error($cursor);
    die("Error al ejecutar el cursor: " . $e['message']);
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

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Clientes - Movie Club</title>
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
            <div class="container">
                <h1 style="color: #333">Clientes</h1>
                <a href="agregar_cliente.php" class="button">Agregar Nuevo Cliente</a>
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Correo Electrónico</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = oci_fetch_assoc($cursor)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['ID_CLIENTE'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['NOMBRE'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['APELLIDO'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['CORREO_ELECTRONICO'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($estados[$row['ID_ESTADO']], ENT_QUOTES); ?></td>
                                <td>
                                    <a href="editar_cliente.php?id=<?php echo htmlspecialchars($row['ID_CLIENTE'], ENT_QUOTES); ?>" class="btn" style="background-color: #013e6a; color: white;">Editar</a>
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
