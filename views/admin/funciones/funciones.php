<?php
include '../../../includes/database.php';

if (!$conn) {
    die("No se pudo conectar a la base de datos: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

// Consultar todas las funciones
$sql = 'SELECT * FROM FIDE_FUNCION_TB';
$stid = oci_parse($conn, $sql);
oci_execute($stid);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Funciones - Movie Club</title>
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
                <h1 style="color: #333">Funciones</h1>
                <a href="agregar_funcion.php" class="button" style="background-color: #013e6a; color: white;">Agregar Nueva Función</a>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID Función</th>
                            <th>Fecha</th>
                            <th>ID Película</th>
                            <th>ID Sala</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = oci_fetch_assoc($stid)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['ID_FUNCION'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($row['FECHA'])), ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['ID_PELICULA'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['ID_SALA'], ENT_QUOTES); ?></td>
                                <td>
                                    <a href="editar_funcion.php?id=<?php echo htmlspecialchars($row['ID_FUNCION'], ENT_QUOTES); ?>" class="btn" style="background-color: #013e6a; color: white;">Editar</a>
                                    <a href="eliminar_funcion.php?id=<?php echo htmlspecialchars($row['ID_FUNCION'], ENT_QUOTES); ?>" class="btn btn-danger">Eliminar</a>
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
</body>
</html>

<?php
oci_free_statement($stid);
oci_close($conn);
?>
