<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

// Verificar que el ID del director se proporciona en la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID del director no proporcionado.");
}

$id_director = intval($_GET['id']); // Asegúrate de que el ID sea un entero

// Mostrar el ID del director para depuración
echo "ID del director: " . htmlspecialchars($id_director, ENT_QUOTES) . "<br>";

// Preparar la llamada al procedimiento almacenado para obtener el director
$stid = oci_parse($conn, 'BEGIN FIDE_DIRECTOR_TB_OBTENER_DIRECTOR_SP(:p_id_director, :p_cursor); END;');
$cursor = oci_new_cursor($conn);

// Asegúrate de que los tipos y tamaños de los parámetros sean correctos
oci_bind_by_name($stid, ':p_id_director', $id_director, -1, SQLT_INT);
oci_bind_by_name($stid, ':p_cursor', $cursor, -1, OCI_B_CURSOR);

// Ejecutar el procedimiento almacenado
$success = oci_execute($stid);

if (!$success) {
    $e = oci_error($stid);
    die("Error al ejecutar el procedimiento almacenado: " . $e['message']);
}

// Ejecutar el cursor para obtener los resultados
$success = oci_execute($cursor);

if (!$success) {
    $e = oci_error($cursor);
    die("Error al ejecutar el cursor: " . $e['message']);
}

// Buscar el director en el cursor
$director = null;
while (($row = oci_fetch_assoc($cursor)) !== false) {
    if ($row['ID_DIRECTOR'] == $id_director) {
        $director = $row;
        break;
    }
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
oci_free_statement($stid);
oci_free_statement($cursor);
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Editar Director - Movie Club</title>
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
                <h1 style="color: #333">Editar Director</h1>
                <form action="actualizar_director.php" method="POST">
                    <input type="hidden" name="id_director" value="<?php echo htmlspecialchars($id_director, ENT_QUOTES); ?>">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($director['NOMBRE'] ?? '', ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nacionalidad">Nacionalidad</label>
                        <input type="text" id="nacionalidad" name="nacionalidad" class="form-control" value="<?php echo htmlspecialchars($director['NACIONALIDAD'] ?? '', ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select class="form-control" id="estado" name="id_estado" required>
                            <?php foreach ($estados_data as $id_estado => $descripcion_estado): ?>
                                <option value="<?php echo htmlspecialchars($id_estado); ?>" <?php echo isset($director['ID_ESTADO']) && $director['ID_ESTADO'] == $id_estado ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($descripcion_estado); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn" style="background-color: #013e6a; color: white; margin-bottom: 2rem;">Actualizar Director</button>
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
