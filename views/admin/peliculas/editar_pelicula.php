<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

// Obtener el ID de la película desde la URL
$id_pelicula = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_pelicula <= 0) {
    echo "ID de película inválido.";
    exit;
}

// Consulta para obtener los datos de la película
$sql = '
    SELECT * FROM FIDE_PELICULAS_TB WHERE ID_PELICULA = :id_pelicula
';
$stid = oci_parse($conn, $sql);

oci_bind_by_name($stid, ':id_pelicula', $id_pelicula);

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

$pelicula = oci_fetch_assoc($stid);

if (!$pelicula) {
    echo "Película no encontrada.";
    oci_free_statement($stid);
    oci_close($conn);
    exit;
}

$sql_generos = 'SELECT ID_GENERO, NOMBRE FROM FIDE_GENERO_TB';
$stid_generos = oci_parse($conn, $sql_generos);
oci_execute($stid_generos);

$sql_directores = 'SELECT ID_DIRECTOR, NOMBRE FROM FIDE_DIRECTOR_TB';
$stid_directores = oci_parse($conn, $sql_directores);
oci_execute($stid_directores);

$sql_idiomas = 'SELECT ID_IDIOMAS, NOMBRE FROM FIDE_IDIOMAS_TB';
$stid_idiomas = oci_parse($conn, $sql_idiomas);
oci_execute($stid_idiomas);

$sql_estados = 'SELECT ID_ESTADO, NOMBRE FROM FIDE_ESTADO_TB';
$stid_estados = oci_parse($conn, $sql_estados);
oci_execute($stid_estados);

oci_free_statement($stid);
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Editar Película - Movie Club</title>
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
                <h1>Editar Película</h1>
                <form action="actualizar_pelicula.php" method="post">
                    <input type="hidden" name="id_pelicula" value="<?php echo htmlspecialchars($pelicula['ID_PELICULA'], ENT_QUOTES); ?>">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($pelicula['NOMBRE'], ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" required><?php echo htmlspecialchars($pelicula['DESCRIPCION'], ENT_QUOTES); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="genero">Género</label>
                        <select id="genero" name="genero" class="form-control" required>
                            <?php while ($genero = oci_fetch_assoc($stid_generos)): ?>
                                <option value="<?php echo htmlspecialchars($genero['ID_GENERO'], ENT_QUOTES); ?>" <?php if ($genero['ID_GENERO'] == $pelicula['ID_GENERO']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($genero['NOMBRE'], ENT_QUOTES); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="director">Director</label>
                        <select id="director" name="director" class="form-control" required>
                            <?php while ($director = oci_fetch_assoc($stid_directores)): ?>
                                <option value="<?php echo htmlspecialchars($director['ID_DIRECTOR'], ENT_QUOTES); ?>" <?php if ($director['ID_DIRECTOR'] == $pelicula['ID_DIRECTOR']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($director['NOMBRE'], ENT_QUOTES); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="idioma">Idioma</label>
                        <select id="idioma" name="idioma" class="form-control" required>
                            <?php while ($idioma = oci_fetch_assoc($stid_idiomas)): ?>
                                <option value="<?php echo htmlspecialchars($idioma['ID_IDIOMAS'], ENT_QUOTES); ?>" <?php if ($idioma['ID_IDIOMAS'] == $pelicula['ID_IDIOMAS']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($idioma['NOMBRE'], ENT_QUOTES); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado" class="form-control" required>
                            <?php while ($estado = oci_fetch_assoc($stid_estados)): ?>
                                <option value="<?php echo htmlspecialchars($estado['ID_ESTADO'], ENT_QUOTES); ?>" <?php if ($estado['ID_ESTADO'] == $pelicula['ID_ESTADO']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($estado['NOMBRE'], ENT_QUOTES); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn" style="background-color: #013e6a; color: white; margin-bottom: 2rem;">Actualizar</button>
                </form>
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
