<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

$id_sala = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_sala <= 0) {
    echo "ID de sala inválido.";
    exit;
}

// Consulta para obtener la sala específica
$sql_sala = 'SELECT * FROM FIDE_SALAS_TB WHERE ID_SALA = :id_sala';
$stid_sala = oci_parse($conn, $sql_sala);
oci_bind_by_name($stid_sala, ':id_sala', $id_sala);

if (!$stid_sala) {
    $e = oci_error($conn);
    echo "Error al preparar la consulta de sala: " . $e['message'];
    exit;
}

$success_sala = oci_execute($stid_sala);

if (!$success_sala) {
    $e = oci_error($stid_sala);
    echo "Error al ejecutar la consulta de sala: " . $e['message'];
    exit;
}

$sala = oci_fetch_assoc($stid_sala);

if (!$sala) {
    echo "Sala no encontrada.";
    oci_free_statement($stid_sala);
    oci_close($conn);
    exit;
}

// Consulta para obtener todos los tipos de sala
$sql_todos_tipos = 'SELECT * FROM FIDE_TIPO_SALA_TB';
$stid_todos_tipos = oci_parse($conn, $sql_todos_tipos);
oci_execute($stid_todos_tipos);

oci_free_statement($stid_sala);
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Editar Sala - Movie Club</title>
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
                <h1 style="color: #333">Editar Sala</h1>
                <form action="actualizar_sala.php" method="post">
                    <input type="hidden" name="id_sala" value="<?php echo htmlspecialchars($sala['ID_SALA'], ENT_QUOTES); ?>">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($sala['NOMBRE'], ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="numero_asientos">Número de Asientos</label>
                        <input type="number" id="numero_asientos" name="numero_asientos" class="form-control" value="<?php echo htmlspecialchars($sala['NUMERO_ASIENTOS'], ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="id_tipo_sala">Tipo de Sala</label>
                        <select id="id_tipo_sala" name="id_tipo_sala" class="form-control" required>
                            <?php while ($tipo = oci_fetch_assoc($stid_todos_tipos)): ?>
                                <option value="<?php echo htmlspecialchars($tipo['ID_TIPO_SALA'], ENT_QUOTES); ?>" <?php if ($sala['ID_TIPO_SALA'] == $tipo['ID_TIPO_SALA']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($tipo['NOMBRE'], ENT_QUOTES); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="id_estado">Estado</label>
                        <select id="id_estado" name="id_estado" class="form-control" required>
                            <option value="1" <?php if ($sala['ID_ESTADO'] == 1) echo 'selected'; ?>>Activo</option>
                            <option value="2" <?php if ($sala['ID_ESTADO'] == 2) echo 'selected'; ?>>Inactivo</option>
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
