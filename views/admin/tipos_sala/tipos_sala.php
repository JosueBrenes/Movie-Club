<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

// Obtener estados
$stid_estado = oci_parse($conn, 'BEGIN FIDE_ESTADO_TB_OBTENER_ESTADO_SP(:p_cursor); END;');
$cursor_estado = oci_new_cursor($conn);
oci_bind_by_name($stid_estado, ':p_cursor', $cursor_estado, -1, OCI_B_CURSOR);

$success = oci_execute($stid_estado);
oci_execute($cursor_estado);

if (!$success) {
    $e = oci_error($stid_estado);
    die("Error al ejecutar el procedimiento almacenado para obtener estados: " . $e['message']);
}

// Crear un array para almacenar los estados
$estados = [];
while ($row_estado = oci_fetch_assoc($cursor_estado)) {
    $estados[$row_estado['ID_ESTADO']] = $row_estado['NOMBRE'];
}

oci_free_statement($stid_estado);
oci_free_statement($cursor_estado);

// Obtener el estado seleccionado para filtrar
$id_estado = isset($_POST['id_estado']) ? intval($_POST['id_estado']) : null;

// Llamar a la función FIDE_TIPO_SALA_TB_FILTRAR_ESTADO_FN para obtener los tipos de sala
$stid_tipo_sala = oci_parse($conn, 'BEGIN :p_cursor := FIDE_TIPO_SALA_TB_FILTRAR_ESTADO_FN(:p_id_estado); END;');
$cursor_tipo_sala = oci_new_cursor($conn);
oci_bind_by_name($stid_tipo_sala, ':p_id_estado', $id_estado, -1, SQLT_INT);
oci_bind_by_name($stid_tipo_sala, ':p_cursor', $cursor_tipo_sala, -1, OCI_B_CURSOR);

$success = oci_execute($stid_tipo_sala);

if (!$success) {
    $e = oci_error($stid_tipo_sala);
    die("Error al ejecutar la función FIDE_TIPO_SALA_TB_FILTRAR_ESTADO_FN: " . $e['message']);
}

$success = oci_execute($cursor_tipo_sala);

if (!$success) {
    $e = oci_error($cursor_tipo_sala);
    die("Error al ejecutar el cursor: " . $e['message']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tipos de Sala - Movie Club</title>
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
                <h1 style="color: #333">Tipos de Sala</h1>

                <!-- Formulario para seleccionar estado -->
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="id_estado">Filtrar por Estado:</label>
                        <select id="id_estado" name="id_estado" class="form-control">
                            <option value="">Todos</option>
                            <?php foreach ($estados as $id => $nombre): ?>
                                <option value="<?php echo htmlspecialchars($id, ENT_QUOTES); ?>" <?php echo ($id_estado == $id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($nombre, ENT_QUOTES); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn button" style="font-weight: bold !important;">Filtrar</button>
                    <a href="agregar_tipo_sala.php" class="button">Agregar Nuevo Tipo de Sala</a>
                </form>

                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = oci_fetch_assoc($cursor_tipo_sala)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['ID_TIPO_SALA'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['NOMBRE'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['DESCRIPCION'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($estados[$row['ID_ESTADO']], ENT_QUOTES); ?></td>
                                <td>
                                    <a href="editar_tipo_sala.php?id=<?php echo htmlspecialchars($row['ID_TIPO_SALA'], ENT_QUOTES); ?>" class="btn" style="background-color: #013e6a; color: white;">Editar</a>
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
    oci_free_statement($stid_tipo_sala);
    oci_free_statement($cursor_tipo_sala);
    oci_close($conn);
    ?>
</body>
</html>
