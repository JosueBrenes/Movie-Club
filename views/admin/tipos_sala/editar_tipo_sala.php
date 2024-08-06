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
    <nav class="sidebar">
        <h2>Opciones</h2>
        <a href="../clientes/clientes.php">Gestionar Clientes</a>
        <a href="../comida/comida.php">Gestionar Comida</a>
        <a href="../empleados/empleados.php">Gestionar Empleados</a>
        <a href="../metodo_pagos/metodo_pagos.php">Gestionar Métodos de Pago</a>
        <a href="../peliculas/peliculas.php">Gestionar Películas</a>
        <a href="../genero/genero.php">Gestionar Generos</a>
        <a href="../directores/directores.php">Gestionar Directores</a>
        <a href="../proovedores/proovedores.php">Gestionar Proveedores</a>
        <a href="../inventario/inventario.php">Gestionar Inventario</a>
        <a href="../posiciones/posiciones.php">Gestionar Posiciones</a>
        <a href="../tipos_sala/tipos_sala.php">Gestionar Tipos de Sala</a>
    </nav>

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
