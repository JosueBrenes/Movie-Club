<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

$id_tipo_sala = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_tipo_sala <= 0) {
    echo "ID de tipo de sala inválido.";
    exit;
}

$sql_tipo_sala = 'SELECT * FROM FIDE_TIPO_SALA_TB WHERE ID_TIPO_SALA = :id_tipo_sala';
$stid_tipo_sala = oci_parse($conn, $sql_tipo_sala);
oci_bind_by_name($stid_tipo_sala, ':id_tipo_sala', $id_tipo_sala);

if (!$stid_tipo_sala) {
    $e = oci_error($conn);
    echo "Error al preparar la consulta de tipo de sala: " . $e['message'];
    exit;
}

$success_tipo_sala = oci_execute($stid_tipo_sala);

if (!$success_tipo_sala) {
    $e = oci_error($stid_tipo_sala);
    echo "Error al ejecutar la consulta de tipo de sala: " . $e['message'];
    exit;
}

$tipo_sala = oci_fetch_assoc($stid_tipo_sala);

if (!$tipo_sala) {
    echo "Tipo de sala no encontrado.";
    oci_free_statement($stid_tipo_sala);
    oci_close($conn);
    exit;
}

$sql_todos_tipos = 'SELECT * FROM FIDE_TIPO_SALA_TB';
$stid_todos_tipos = oci_parse($conn, $sql_todos_tipos);
oci_execute($stid_todos_tipos);

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
oci_free_statement($stid_tipo_sala);
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Editar Tipo de Sala - Movie Club</title>
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
                <h1 style="color: #333">Editar Tipo de Sala</h1>
                <form action="actualizar_tipo_sala.php" method="post">
                    <input type="hidden" name="id_tipo_sala" value="<?php echo htmlspecialchars($tipo_sala['ID_TIPO_SALA'], ENT_QUOTES); ?>">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($tipo_sala['NOMBRE'], ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="4" required><?php echo htmlspecialchars($tipo_sala['DESCRIPCION'], ENT_QUOTES); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="id_tipo_sala">Tipo de Sala</label>
                        <select id="id_tipo_sala" name="id_tipo_sala" class="form-control" required>
                            <?php while ($tipo = oci_fetch_assoc($stid_todos_tipos)): ?>
                                <option value="<?php echo htmlspecialchars($tipo['ID_TIPO_SALA'], ENT_QUOTES); ?>" <?php if ($tipo_sala['ID_TIPO_SALA'] == $tipo['ID_TIPO_SALA']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($tipo['NOMBRE'], ENT_QUOTES); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select class="form-control" id="estado" name="id_estado" required>
                            <?php foreach ($estados_data as $id_estado => $descripcion_estado): ?>
                                <option value="<?php echo htmlspecialchars($id_estado); ?>" <?php echo $tipo_sala['ID_ESTADO'] == $id_estado ? 'selected' : ''; ?>>
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
