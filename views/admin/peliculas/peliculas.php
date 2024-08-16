<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

// Preparar la llamada al procedimiento almacenado para obtener las películas
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

// Preparar la llamada al procedimiento almacenado para obtener los géneros
$stid_generos = oci_parse($conn, 'BEGIN FIDE_GENERO_TB_OBTENER_GENERO_SP(:p_cursor); END;');
$cursor_generos = oci_new_cursor($conn);
oci_bind_by_name($stid_generos, ':p_cursor', $cursor_generos, -1, OCI_B_CURSOR);
$success_generos = oci_execute($stid_generos);

if (!$success_generos) {
    $e = oci_error($stid_generos);
    die("Error al ejecutar el procedimiento almacenado de géneros: " . $e['message']);
}

$success_generos = oci_execute($cursor_generos);

if (!$success_generos) {
    $e = oci_error($cursor_generos);
    die("Error al ejecutar el cursor de géneros: " . $e['message']);
}

// Almacenar los datos de géneros en un array
$generos_data = [];
while ($row_genero = oci_fetch_assoc($cursor_generos)) {
    $generos_data[$row_genero['ID_GENERO']] = $row_genero['NOMBRE'];
}

oci_free_statement($stid_generos);
oci_free_statement($cursor_generos);

// Preparar la llamada al procedimiento almacenado para obtener los directores
$stid_directores = oci_parse($conn, 'BEGIN FIDE_DIRECTOR_TB_OBTENER_DIRECTOR_SP(:p_cursor); END;');
$cursor_directores = oci_new_cursor($conn);
oci_bind_by_name($stid_directores, ':p_cursor', $cursor_directores, -1, OCI_B_CURSOR);
$success_directores = oci_execute($stid_directores);

if (!$success_directores) {
    $e = oci_error($stid_directores);
    die("Error al ejecutar el procedimiento almacenado de directores: " . $e['message']);
}

$success_directores = oci_execute($cursor_directores);

if (!$success_directores) {
    $e = oci_error($cursor_directores);
    die("Error al ejecutar el cursor de directores: " . $e['message']);
}

// Almacenar los datos de directores en un array
$directores_data = [];
while ($row_director = oci_fetch_assoc($cursor_directores)) {
    $directores_data[$row_director['ID_DIRECTOR']] = $row_director['NOMBRE'];
}

oci_free_statement($stid_directores);
oci_free_statement($cursor_directores);

// Preparar la llamada al procedimiento almacenado para obtener los idiomas
$stid_idiomas = oci_parse($conn, 'BEGIN FIDE_IDIOMAS_TB_OBTENER_IDIOMAS_SP(:p_cursor); END;');
$cursor_idiomas = oci_new_cursor($conn);
oci_bind_by_name($stid_idiomas, ':p_cursor', $cursor_idiomas, -1, OCI_B_CURSOR);
$success_idiomas = oci_execute($stid_idiomas);

if (!$success_idiomas) {
    $e = oci_error($stid_idiomas);
    die("Error al ejecutar el procedimiento almacenado de idiomas: " . $e['message']);
}

$success_idiomas = oci_execute($cursor_idiomas);

if (!$success_idiomas) {
    $e = oci_error($cursor_idiomas);
    die("Error al ejecutar el cursor de idiomas: " . $e['message']);
}

// Almacenar los datos de idiomas en un array
$idiomas_data = [];
while ($row_idioma = oci_fetch_assoc($cursor_idiomas)) {
    $idiomas_data[$row_idioma['ID_IDIOMAS']] = $row_idioma['NOMBRE'];
}

oci_free_statement($stid_idiomas);
oci_free_statement($cursor_idiomas);

// Preparar la llamada al procedimiento almacenado para obtener los estados
$stid_estados = oci_parse($conn, 'BEGIN FIDE_ESTADO_TB_OBTENER_ESTADO_SP(:p_cursor); END;');
$cursor_estados = oci_new_cursor($conn);
oci_bind_by_name($stid_estados, ':p_cursor', $cursor_estados, -1, OCI_B_CURSOR);
$success_estados = oci_execute($stid_estados);

if (!$success_estados) {
    $e = oci_error($stid_estados);
    die("Error al ejecutar el procedimiento almacenado de estados: " . $e['message']);
}

$success_estados = oci_execute($cursor_estados);

if (!$success_estados) {
    $e = oci_error($cursor_estados);
    die("Error al ejecutar el cursor de estados: " . $e['message']);
}

// Almacenar los datos de estados en un array
$estados_data = [];
while ($row_estado = oci_fetch_assoc($cursor_estados)) {
    $estados_data[$row_estado['ID_ESTADO']] = $row_estado['NOMBRE'];
}

oci_free_statement($stid_estados);
oci_free_statement($cursor_estados);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Películas - Movie Club</title>
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
                <h1 style="color: #333">Películas</h1>
                <a href="agregar_pelicula.php" class="button">Agregar Nueva Película</a>
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Género</th>
                            <th>Director</th>
                            <th>Descripción</th>
                            <th>Idioma</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row_pelicula = oci_fetch_assoc($cursor_peliculas)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row_pelicula['ID_PELICULA'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row_pelicula['NOMBRE'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($generos_data[$row_pelicula['ID_GENERO']] ?? 'Desconocido', ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($directores_data[$row_pelicula['ID_DIRECTOR']] ?? 'Desconocido', ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row_pelicula['DESCRIPCION'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($idiomas_data[$row_pelicula['ID_IDIOMAS']] ?? 'Desconocido'); ?>
                                <td><?php echo htmlspecialchars($estados_data[$row_pelicula['ID_ESTADO']] ?? 'Desconocido', ENT_QUOTES); ?></td>
                                <td>
                                    <a href="editar_pelicula.php?id=<?php echo htmlspecialchars($row_pelicula['ID_PELICULA'], ENT_QUOTES); ?>" class="btn" style="background-color: #013e6a; color: white;">Editar</a>
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
    oci_free_statement($stid_peliculas);
    oci_free_statement($cursor_peliculas);
    oci_close($conn);
    ?>
</body>
</html>
