<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

// Consulta para obtener empleados y posiciones
$sql = '
    SELECT e.ID_EMPLEADO, e.NOMBRE, e.APELLIDO, e.CORREO_ELECTRONICO, e.TELEFONO, 
           p.NOMBRE AS NOMBRE_POSICION
    FROM FIDE_EMPLEADOS_TB e
    LEFT JOIN FIDE_POSICION_TB p ON e.ID_POSICION = p.ID_POSICION
';

$stid = oci_parse($conn, $sql);

if (!$stid) {
    $e = oci_error($conn);
    echo "Error al preparar la consulta: " . $e['message'];
    exit;
}

$success = oci_execute($stid);

if (!$success) {
    $e = oci_error($stid);
    echo "Error al ejecutar la consulta: " . $e['message'];
    exit;
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
                <a href="agregar_empleado.php" class="button">Agregar Nuevo Empleado</a>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID Empleado</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Correo Electrónico</th>
                            <th>Teléfono</th>
                            <th>Posición</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = oci_fetch_assoc($stid)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['ID_EMPLEADO'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['NOMBRE'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['APELLIDO'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['CORREO_ELECTRONICO'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['TELEFONO'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['NOMBRE_POSICION'], ENT_QUOTES); ?></td>
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
    oci_free_statement($stid);
    oci_close($conn);
    ?>
</body>
</html>
