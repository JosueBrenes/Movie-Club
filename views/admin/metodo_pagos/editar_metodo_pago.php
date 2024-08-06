<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

// Obtener el ID del método de pago desde la URL
$id_metodo_pago = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_metodo_pago <= 0) {
    echo "ID de método de pago inválido.";
    exit;
}

// Consulta para obtener los datos del método de pago
$sql = 'SELECT * FROM FIDE_METODO_PAGO_TB WHERE ID_METODO_PAGO = :id_metodo_pago';
$stid = oci_parse($conn, $sql);

oci_bind_by_name($stid, ':id_metodo_pago', $id_metodo_pago);

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

$metodo_pago = oci_fetch_assoc($stid);

if (!$metodo_pago) {
    echo "Método de pago no encontrado.";
    oci_free_statement($stid);
    oci_close($conn);
    exit;
}

// Obtener estados para el menú desplegable
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
    <title>Editar Método de Pago - Movie Club</title>
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
                <h1>Editar Método de Pago</h1>
                <form action="actualizar_metodo_pago.php" method="post">
                    <input type="hidden" name="id_metodo_pago" value="<?php echo htmlspecialchars($metodo_pago['ID_METODO_PAGO'], ENT_QUOTES); ?>">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($metodo_pago['NOMBRE'], ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control"><?php echo htmlspecialchars($metodo_pago['DESCRIPCION'], ENT_QUOTES); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado" class="form-control" required>
                            <?php while ($estado = oci_fetch_assoc($stid_estados)): ?>
                                <option value="<?php echo htmlspecialchars($estado['ID_ESTADO'], ENT_QUOTES); ?>" <?php if ($estado['ID_ESTADO'] == $metodo_pago['ID_ESTADO']) echo 'selected'; ?>>
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
