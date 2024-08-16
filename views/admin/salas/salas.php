<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

// Preparar y ejecutar la llamada al procedimiento almacenado para obtener salas
$stid_salas = oci_parse($conn, 'BEGIN FIDE_SALAS_TB_OBTENER_SALAS_SP(:p_cursor); END;');
$cursor_salas = oci_new_cursor($conn);
oci_bind_by_name($stid_salas, ':p_cursor', $cursor_salas, -1, OCI_B_CURSOR);
$success_salas = oci_execute($stid_salas);
if (!$success_salas) {
    $e = oci_error($stid_salas);
    die("Error al ejecutar el procedimiento almacenado de salas: " . $e['message']);
}
$success_salas = oci_execute($cursor_salas);
if (!$success_salas) {
    $e = oci_error($cursor_salas);
    die("Error al ejecutar el cursor de salas: " . $e['message']);
}

// Preparar y ejecutar la llamada al procedimiento almacenado para obtener estados
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

// Preparar y ejecutar la llamada al procedimiento almacenado para obtener tipos de sala
$stid_tipos = oci_parse($conn, 'BEGIN FIDE_TIPO_SALA_TB_OBTENER_TIPO_SALA_SP(:p_cursor); END;');
$cursor_tipos = oci_new_cursor($conn);
oci_bind_by_name($stid_tipos, ':p_cursor', $cursor_tipos, -1, OCI_B_CURSOR);
$success_tipos = oci_execute($stid_tipos);
if (!$success_tipos) {
    $e = oci_error($stid_tipos);
    die("Error al ejecutar el procedimiento almacenado de tipos de sala: " . $e['message']);
}
$success_tipos = oci_execute($cursor_tipos);
if (!$success_tipos) {
    $e = oci_error($cursor_tipos);
    die("Error al ejecutar el cursor de tipos de sala: " . $e['message']);
}

// Almacenar los datos de estados en un array
$estados_data = [];
while ($row_estado = oci_fetch_assoc($cursor_estados)) {
    $estados_data[$row_estado['ID_ESTADO']] = $row_estado['NOMBRE'];
}

// Almacenar los datos de tipos de sala en un array
$tipos_data = [];
while ($row_tipo = oci_fetch_assoc($cursor_tipos)) {
    $tipos_data[$row_tipo['ID_TIPO_SALA']] = $row_tipo['NOMBRE'];
}

oci_free_statement($stid_estados);
oci_free_statement($cursor_estados);
oci_free_statement($stid_tipos);
oci_free_statement($cursor_tipos);

// Mostrar las salas
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Salas - Movie Club</title>
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
                <h1 style="color: #333">Salas</h1>
                <a href="agregar_sala.php" class="button">Agregar Nueva Sala</a>
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Sala</th>
                            <th>Nombre</th>
                            <th>Número de Asientos</th>
                            <th>Tipo de Sala</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = oci_fetch_assoc($cursor_salas)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['ID_SALA'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['NOMBRE'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['NUMERO_ASIENTOS'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($tipos_data[$row['ID_TIPO_SALA']] ?? 'Desconocido', ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($estados_data[$row['ID_ESTADO']] ?? 'Desconocido', ENT_QUOTES); ?></td>
                                <td>
                                    <a href="editar_sala.php?id=<?php echo htmlspecialchars($row['ID_SALA'], ENT_QUOTES); ?>" class="btn" style="background-color: #013e6a; color: white;">Editar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer_area">
            <p class="footer_text">
                &copy; 2024 Movie Club. Todos los derechos reservados.
            </p>
        </footer>
    </div>

    <?php
    oci_free_statement($stid_salas);
    oci_free_statement($cursor_salas);
    oci_close($conn);
    ?>
</body>
</html>
