<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

$id_posicion = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_posicion <= 0) {
    echo "ID de posición inválido.";
    exit;
}

$sql = '
    SELECT * FROM FIDE_POSICION_TB WHERE ID_POSICION = :id_posicion
';
$stid = oci_parse($conn, $sql);

oci_bind_by_name($stid, ':id_posicion', $id_posicion);

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

$posicion = oci_fetch_assoc($stid);

if (!$posicion) {
    echo "Posición no encontrada.";
    oci_free_statement($stid);
    oci_close($conn);
    exit;
}

oci_free_statement($stid);
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Editar Posición - Movie Club</title>
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
                <h1>Editar Posición</h1>
                <form action="actualizar_posicion.php" method="post">
                    <input type="hidden" name="id_posicion" value="<?php echo htmlspecialchars($posicion['ID_POSICION'], ENT_QUOTES); ?>">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($posicion['NOMBRE'], ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control"><?php echo htmlspecialchars($posicion['DESCRIPCION'], ENT_QUOTES); ?></textarea>
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
