<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

$id_funcion = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_funcion <= 0) {
    echo "ID de función inválido.";
    exit;
}

// Consulta para obtener la función a editar
$sql = 'SELECT * FROM FIDE_FUNCION_TB WHERE ID_FUNCION = :id_funcion';
$stid = oci_parse($conn, $sql);

oci_bind_by_name($stid, ':id_funcion', $id_funcion);

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

$funcion = oci_fetch_assoc($stid);

if (!$funcion) {
    echo "Función no encontrada.";
    oci_free_statement($stid);
    oci_close($conn);
    exit;
}

// Obtener datos para las películas
$peliculas_query = 'SELECT ID_PELICULA, NOMBRE FROM FIDE_PELICULAS_TB';
$peliculas_stid = oci_parse($conn, $peliculas_query);
oci_execute($peliculas_stid);

// Obtener datos para las salas
$salas_query = 'SELECT ID_SALA, NOMBRE FROM FIDE_SALAS_TB';
$salas_stid = oci_parse($conn, $salas_query);
oci_execute($salas_stid);

// Obtener los estados
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

$estados_data = [];
while ($row_estado = oci_fetch_assoc($cursor_estados)) {
    $estados_data[$row_estado['ID_ESTADO']] = $row_estado['NOMBRE'];
}

oci_free_statement($stid_estados);
oci_free_statement($cursor_estados);
oci_free_statement($stid);
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Editar Función - Movie Club</title>
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
                <h1 style="color: #333;">Editar Función</h1>
                <form action="actualizar_funcion.php" method="post">
                    <input type="hidden" name="id_funcion" value="<?php echo htmlspecialchars($funcion['ID_FUNCION'], ENT_QUOTES); ?>">
                    
                    <div class="form-group">
                        <label for="fecha">Fecha</label>
                        <input type="date" id="fecha" name="fecha" class="form-control" value="<?php echo htmlspecialchars($funcion['FECHA'], ENT_QUOTES); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="id_pelicula">Película</label>
                        <select id="id_pelicula" name="id_pelicula" class="form-control">
                            <?php while ($row = oci_fetch_assoc($peliculas_stid)) { ?>
                                <option value="<?php echo htmlspecialchars($row['ID_PELICULA']); ?>" <?php if ($row['ID_PELICULA'] == $funcion['ID_PELICULA']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($row['NOMBRE']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_sala">Sala</label>
                        <select id="id_sala" name="id_sala" class="form-control">
                            <?php while ($row = oci_fetch_assoc($salas_stid)) { ?>
                                <option value="<?php echo htmlspecialchars($row['ID_SALA']); ?>" <?php if ($row['ID_SALA'] == $funcion['ID_SALA']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($row['NOMBRE']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select class="form-control" id="estado" name="id_estado" required>
                            <?php foreach ($estados_data as $id_estado => $descripcion_estado): ?>
                                <option value="<?php echo htmlspecialchars($id_estado); ?>" <?php echo $funcion['ID_ESTADO'] == $id_estado ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($descripcion_estado); ?>
                                </option>
                            <?php endforeach; ?>
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
