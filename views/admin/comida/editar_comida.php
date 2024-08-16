<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de comida no proporcionado.");
}

$id_comida = $_GET['id'];

// Preparar la consulta para obtener los datos de la comida
$sql = 'SELECT * FROM FIDE_COMIDA_TB WHERE ID_COMIDA = :id_comida';
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ':id_comida', $id_comida);
oci_execute($stid);
$comida = oci_fetch_assoc($stid);

if (!$comida) {
    die("No se encontró la comida.");
}

// Obtener la lista de inventarios para el campo select
$query = 'SELECT ID_INVENTARIO, NOMBRE FROM FIDE_INVENTARIO_TB';
$stid_inventario = oci_parse($conn, $query);
oci_execute($stid_inventario);

$inventarios = [];
while (($row = oci_fetch_assoc($stid_inventario)) != false) {
    $inventarios[] = $row;
}

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
oci_free_statement($stid_inventario);
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Editar Comida - Movie Club</title>
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
                <h1 style="color: #333">Editar Comida</h1>
                <form action="actualizar_comida.php" method="POST">
                    <input type="hidden" name="id_comida" value="<?php echo htmlspecialchars($comida['ID_COMIDA'] ?? '', ENT_QUOTES); ?>">
                    <div class="form-group">
                        <label for="id_inventario">Inventario</label>
                        <select id="id_inventario" name="id_inventario" class="form-control" required>
                            <option value="">Seleccione un inventario</option>
                            <?php foreach ($inventarios as $inventario): ?>
                                <option value="<?php echo htmlspecialchars($inventario['ID_INVENTARIO'], ENT_QUOTES); ?>"
                                    <?php echo ($comida['ID_INVENTARIO'] == $inventario['ID_INVENTARIO']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($inventario['NOMBRE'], ENT_QUOTES); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="precio">Precio</label>
                        <input type="number" id="precio" name="precio" class="form-control" step="0.01" value="<?php echo htmlspecialchars($comida['PRECIO'] ?? '', ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select class="form-control" id="estado" name="id_estado" required>
                            <?php foreach ($estados_data as $id_estado => $descripcion_estado): ?>
                                <option value="<?php echo htmlspecialchars($id_estado); ?>" <?php echo $comida['ID_ESTADO'] == $id_estado ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($descripcion_estado); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn" style="background-color: #013e6a; color: white; margin-bottom: 2rem;">Actualizar Comida</button>
                </form>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer_area">
            <p class="footer_text">&copy; 2024 Movie Club. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>
