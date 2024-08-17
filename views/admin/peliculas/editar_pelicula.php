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

// Preparar y ejecutar el procedimiento almacenado para obtener la película
$sql = 'BEGIN FIDE_PELICULAS_TB_OBTENER_PELICULAS_SP(:p_cursor); END;';
$stid = oci_parse($conn, $sql);

$cursor = oci_new_cursor($conn);
oci_bind_by_name($stid, ':p_cursor', $cursor, -1, OCI_B_CURSOR);

if (!$stid) {
    $e = oci_error($conn);
    echo "Error al preparar el procedimiento: " . $e['message'];
    exit;
}

$success = oci_execute($stid);

if (!$success) {
    $e = oci_error($stid);
    echo "Error al ejecutar el procedimiento: " . $e['message'];
    exit;
}

oci_execute($cursor);

// Buscar la película específica
$pelicula = null;
while ($row = oci_fetch_assoc($cursor)) {
    if ($row['ID_PELICULA'] == $id_pelicula) {
        $pelicula = $row;
        break;
    }
}

if (!$pelicula) {
    echo "Película no encontrada.";
    oci_free_statement($stid);
    oci_free_statement($cursor);
    oci_close($conn);
    exit;
}

// Preparar y ejecutar el procedimiento almacenado para obtener géneros
$sql_generos = 'BEGIN FIDE_GENERO_TB_OBTENER_GENERO_SP(:p_cursor); END;';
$stid_generos = oci_parse($conn, $sql_generos);

$cursor_generos = oci_new_cursor($conn);
oci_bind_by_name($stid_generos, ':p_cursor', $cursor_generos, -1, OCI_B_CURSOR);

if (!$stid_generos) {
    $e = oci_error($conn);
    echo "Error al preparar el procedimiento de géneros: " . $e['message'];
    exit;
}

$success_generos = oci_execute($stid_generos);

if (!$success_generos) {
    $e = oci_error($stid_generos);
    echo "Error al ejecutar el procedimiento de géneros: " . $e['message'];
    exit;
}

oci_execute($cursor_generos);

// Obtener los géneros
$generos = [];
while ($row_genero = oci_fetch_assoc($cursor_generos)) {
    $generos[] = $row_genero;
}

// Obtener directores usando un SELECT directo
$sql_directores = 'SELECT ID_DIRECTOR, NOMBRE FROM FIDE_DIRECTOR_TB';
$stid_directores = oci_parse($conn, $sql_directores);

if (!$stid_directores) {
    $e = oci_error($conn);
    echo "Error al preparar el SELECT de directores: " . $e['message'];
    exit;
}

$success_directores = oci_execute($stid_directores);

if (!$success_directores) {
    $e = oci_error($stid_directores);
    echo "Error al ejecutar el SELECT de directores: " . $e['message'];
    exit;
}

// Obtener los directores
$directores = [];
while ($row_director = oci_fetch_assoc($stid_directores)) {
    $directores[] = $row_director;
}

// Preparar y ejecutar el SELECT directo para obtener idiomas
$sql_idiomas = 'SELECT ID_IDIOMAS, NOMBRE FROM FIDE_IDIOMAS_TB';
$stid_idiomas = oci_parse($conn, $sql_idiomas);

if (!$stid_idiomas) {
    $e = oci_error($conn);
    echo "Error al preparar el SELECT de idiomas: " . $e['message'];
    exit;
}

$success_idiomas = oci_execute($stid_idiomas);

if (!$success_idiomas) {
    $e = oci_error($stid_idiomas);
    echo "Error al ejecutar el SELECT de idiomas: " . $e['message'];
    exit;
}

// Obtener los idiomas
$idiomas = [];
while ($row_idioma = oci_fetch_assoc($stid_idiomas)) {
    $idiomas[] = $row_idioma;
}

// Preparar y ejecutar el procedimiento almacenado para obtener estados
$sql_estados = 'BEGIN FIDE_ESTADO_TB_OBTENER_ESTADO_SP(:p_cursor); END;';
$stid_estados = oci_parse($conn, $sql_estados);

$cursor_estados = oci_new_cursor($conn);
oci_bind_by_name($stid_estados, ':p_cursor', $cursor_estados, -1, OCI_B_CURSOR);

if (!$stid_estados) {
    $e = oci_error($conn);
    echo "Error al preparar el procedimiento de estados: " . $e['message'];
    exit;
}

$success_estados = oci_execute($stid_estados);

if (!$success_estados) {
    $e = oci_error($stid_estados);
    echo "Error al ejecutar el procedimiento de estados: " . $e['message'];
    exit;
}

oci_execute($cursor_estados);

// Obtener los estados
$estados = [];
while ($row_estado = oci_fetch_assoc($cursor_estados)) {
    $estados[] = $row_estado;
}

oci_free_statement($stid);
oci_free_statement($stid_generos);
oci_free_statement($stid_directores);
oci_free_statement($stid_idiomas);
oci_free_statement($stid_estados);
oci_free_statement($cursor);
oci_free_statement($cursor_generos);
oci_free_statement($cursor_estados);
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
                        <select id="genero" name="id_genero" class="form-control" required>
                            <?php foreach ($generos as $genero): ?>
                                <option value="<?php echo htmlspecialchars($genero['ID_GENERO'], ENT_QUOTES); ?>" <?php if ($genero['ID_GENERO'] == $pelicula['ID_GENERO']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($genero['NOMBRE'], ENT_QUOTES); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="director">Director</label>
                        <select id="director" name="id_director" class="form-control" required>
                            <?php foreach ($directores as $director): ?>
                                <option value="<?php echo htmlspecialchars($director['ID_DIRECTOR'], ENT_QUOTES); ?>" <?php if ($director['ID_DIRECTOR'] == $pelicula['ID_DIRECTOR']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($director['NOMBRE'], ENT_QUOTES); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="idioma">Idioma</label>
                        <select id="idioma" name="id_idioma" class="form-control" required>
                            <?php foreach ($idiomas as $idioma): ?>
                                <option value="<?php echo htmlspecialchars($idioma['ID_IDIOMAS'], ENT_QUOTES); ?>" <?php if ($idioma['ID_IDIOMAS'] == $pelicula['ID_IDIOMAS']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($idioma['NOMBRE'], ENT_QUOTES); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select id="estado" name="id_estado" class="form-control" required>
                            <?php foreach ($estados as $estado): ?>
                                <option value="<?php echo htmlspecialchars($estado['ID_ESTADO'], ENT_QUOTES); ?>" <?php if ($estado['ID_ESTADO'] == $pelicula['ID_ESTADO']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($estado['NOMBRE'], ENT_QUOTES); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn button">Actualizar</button>
                </form>
            </div>
        </section>
    </div>
</body>
</html>
