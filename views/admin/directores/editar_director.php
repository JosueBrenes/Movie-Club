<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if (!isset($_GET['id_director']) || empty($_GET['id_director'])) {
    die("ID del director no proporcionado.");
}

$id_director = $_GET['id_director'];

// Preparar la llamada al procedimiento almacenado
$stid = oci_parse($conn, 'BEGIN FIDE_DIRECTOR_TB_OBTENER_DIRECTOR_SP(:p_cursor); END;');

// Crear y asociar el cursor de salida
$cursor = oci_new_cursor($conn);
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

if (!$director) {
    die("No se encontró el director.");
}

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
                    <input type="hidden" name="id_director" value="<?php echo htmlspecialchars($director['ID_DIRECTOR'] ?? '', ENT_QUOTES); ?>">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($director['NOMBRE'] ?? '', ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido</label>
                        <input type="text" id="apellido" name="apellido" class="form-control" value="<?php echo htmlspecialchars($director['APELLIDO'] ?? '', ENT_QUOTES); ?>" required>
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
