<?php
include '../../../includes/database.php';

if (!$conn) {
    die("No se pudo conectar a la base de datos: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

// Preparar y ejecutar la llamada al procedimiento almacenado para obtener funciones
$stid_funciones = oci_parse($conn, 'BEGIN FIDE_FUNCION_TB_OBTENER_FUNCIONES_SP(:p_cursor); END;');
$cursor_funciones = oci_new_cursor($conn);
oci_bind_by_name($stid_funciones, ':p_cursor', $cursor_funciones, -1, OCI_B_CURSOR);
$success_funciones = oci_execute($stid_funciones);
if (!$success_funciones) {
    $e = oci_error($stid_funciones);
    die("Error al ejecutar el procedimiento almacenado de funciones: " . $e['message']);
}
$success_funciones = oci_execute($cursor_funciones);
if (!$success_funciones) {
    $e = oci_error($cursor_funciones);
    die("Error al ejecutar el cursor de funciones: " . $e['message']);
}

// Preparar y ejecutar la llamada al procedimiento almacenado para obtener películas
$stid_peliculas = oci_parse($conn, 'BEGIN FIDE_PELICULAS_TB_OBTENER_PELICULAS_SP(:p_cursor); END;');
$cursor_peliculas = oci_new_cursor($conn);
oci_bind_by_name($stid_peliculas, ':p_cursor', $cursor_peliculas, -1, OCI_B_CURSOR);
$success_peliculas = oci_execute($stid_peliculas);
if (!$success_peliculas) {
    $e = oci_error($stid_peliculas);
    die("Error al ejecutar el procedimiento almacenado de películas: " . $e['message']);
}
$success_peliculas = oci_execute($cursor_peliculas);
if (!$success_peliculas) {
    $e = oci_error($cursor_peliculas);
    die("Error al ejecutar el cursor de películas: " . $e['message']);
}

// Almacenar los datos de películas en un array
$peliculas_data = [];
while ($row_pelicula = oci_fetch_assoc($cursor_peliculas)) {
    $peliculas_data[$row_pelicula['ID_PELICULA']] = $row_pelicula['NOMBRE'];
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
oci_free_statement($stid_peliculas);
oci_free_statement($cursor_peliculas);
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
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Película</th>
                            <th>Sala</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = oci_fetch_assoc($cursor_funciones)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['ID_FUNCION'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($row['FECHA'])), ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($peliculas_data[$row['ID_PELICULA']] ?? 'Desconocida', ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['ID_SALA'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($estados[$row['ID_ESTADO']], ENT_QUOTES); ?></td>
                                <td>
                                    <a href="editar_funcion.php?id=<?php echo htmlspecialchars($row['ID_FUNCION'], ENT_QUOTES); ?>" class="btn" style="background-color: #013e6a; color: white;">Editar</a>
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
    oci_free_statement($stid_funciones);
    oci_free_statement($cursor_funciones);
    oci_close($conn);
    ?>
</body>
</html>
