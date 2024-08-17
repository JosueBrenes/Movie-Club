<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

// Obtener géneros
$stid_genero = oci_parse($conn, 'BEGIN FIDE_GENERO_TB_OBTENER_GENERO_SP(:p_cursor); END;');
$cursor_genero = oci_new_cursor($conn);
oci_bind_by_name($stid_genero, ':p_cursor', $cursor_genero, -1, OCI_B_CURSOR);
oci_execute($stid_genero);
oci_execute($cursor_genero);

// Crear un array para almacenar los géneros
$generos = [];
while ($row_genero = oci_fetch_assoc($cursor_genero)) {
    $generos[$row_genero['ID_GENERO']] = $row_genero['NOMBRE'];
}

oci_free_statement($stid_genero);
oci_free_statement($cursor_genero);

// Obtener el género seleccionado para filtrar
$id_genero = isset($_POST['id_genero']) ? intval($_POST['id_genero']) : null;

// Llamar a la función FILTRAR_PELICULAS_TB_FILTRAR_GENERO_FN para obtener las películas
$stid_peliculas = oci_parse($conn, 'BEGIN :p_cursor := FIDE_PELICULAS_TB_FILTRAR_GENERO_FN(:p_id_genero); END;');
$cursor_peliculas = oci_new_cursor($conn);
oci_bind_by_name($stid_peliculas, ':p_id_genero', $id_genero, -1, SQLT_INT);
oci_bind_by_name($stid_peliculas, ':p_cursor', $cursor_peliculas, -1, OCI_B_CURSOR);
oci_execute($stid_peliculas);
oci_execute($cursor_peliculas);

// Consulta directa para obtener los directores
$stid_directores = oci_parse($conn, 'SELECT ID_DIRECTOR, NOMBRE FROM FIDE_DIRECTOR_TB');
oci_execute($stid_directores);

// Almacenar los datos de directores en un array
$directores_data = [];
while ($row_director = oci_fetch_assoc($stid_directores)) {
    $directores_data[$row_director['ID_DIRECTOR']] = $row_director['NOMBRE'];
}

oci_free_statement($stid_directores);

// Obtener idiomas
$stid_idiomas = oci_parse($conn, 'BEGIN FIDE_IDIOMAS_TB_OBTENER_IDIOMAS_SP(:p_cursor); END;');
$cursor_idiomas = oci_new_cursor($conn);
oci_bind_by_name($stid_idiomas, ':p_cursor', $cursor_idiomas, -1, OCI_B_CURSOR);
oci_execute($stid_idiomas);
oci_execute($cursor_idiomas);

// Almacenar los datos de idiomas en un array
$idiomas_data = [];
while ($row_idioma = oci_fetch_assoc($cursor_idiomas)) {
    $idiomas_data[$row_idioma['ID_IDIOMAS']] = $row_idioma['NOMBRE'];
}

oci_free_statement($stid_idiomas);
oci_free_statement($cursor_idiomas);

// Obtener estados
$stid_estados = oci_parse($conn, 'BEGIN FIDE_ESTADO_TB_OBTENER_ESTADO_SP(:p_cursor); END;');
$cursor_estados = oci_new_cursor($conn);
oci_bind_by_name($stid_estados, ':p_cursor', $cursor_estados, -1, OCI_B_CURSOR);
oci_execute($stid_estados);
oci_execute($cursor_estados);

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

                <!-- Formulario para seleccionar género -->
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="id_genero">Filtrar por Género:</label>
                        <select id="id_genero" name="id_genero" class="form-control">
                            <option value="">Todos</option>
                            <?php foreach ($generos as $id => $nombre): ?>
                                <option value="<?php echo htmlspecialchars($id, ENT_QUOTES); ?>" <?php echo ($id_genero == $id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($nombre, ENT_QUOTES); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn button" style="font-weight: bold !important;">Filtrar</button>
                    <a href="agregar_pelicula.php" class="button">Agregar Nueva Película</a>
                </form>

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
                                <td><?php echo htmlspecialchars($generos[$row_pelicula['ID_GENERO']] ?? 'Desconocido', ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($directores_data[$row_pelicula['ID_DIRECTOR']] ?? 'Desconocido', ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row_pelicula['DESCRIPCION'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($idiomas_data[$row_pelicula['ID_IDIOMAS']] ?? 'Desconocido'); ?></td>
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
</body>
</html>
