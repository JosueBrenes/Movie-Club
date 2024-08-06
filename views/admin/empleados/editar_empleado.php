<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if (!isset($_GET['id_empleado']) || empty($_GET['id_empleado'])) {
    die("ID del empleado no proporcionado.");
}

$id_empleado = $_GET['id_empleado'];

$sql = 'SELECT * FROM FIDE_EMPLEADOS_TB WHERE ID_EMPLEADO = :id_empleado';
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ':id_empleado', $id_empleado);
oci_execute($stid);
$empleado = oci_fetch_assoc($stid);

if (!$empleado) {
    die("No se encontró el empleado.");
}

// Cierre de recursos
oci_free_statement($stid);
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Editar Empleado - Movie Club</title>
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
                <h1 style="color: #333">Editar Empleado</h1>
                <form action="actualizar_empleado.php" method="POST">
                    <input type="hidden" name="id_empleado" value="<?php echo htmlspecialchars($empleado['ID_EMPLEADO'] ?? '', ENT_QUOTES); ?>">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($empleado['NOMBRE'] ?? '', ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido</label>
                        <input type="text" id="apellido" name="apellido" class="form-control" value="<?php echo htmlspecialchars($empleado['APELLIDO'] ?? '', ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo Electrónico</label>
                        <input type="email" id="correo" name="correo" class="form-control" value="<?php echo htmlspecialchars($empleado['CORREO_ELECTRONICO'] ?? '', ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" class="form-control" value="<?php echo htmlspecialchars($empleado['TELEFONO'] ?? '', ENT_QUOTES); ?>">
                    </div>
                    <div class="form-group">
                        <label for="id_posicion">Posición</label>
                        <select id="id_posicion" name="id_posicion" class="form-control" required>
                            <?php
                            include '../../../includes/database.php'; 
                            $sql_posiciones = 'SELECT * FROM FIDE_POSICION_TB';
                            $stid_posiciones = oci_parse($conn, $sql_posiciones);
                            oci_execute($stid_posiciones);

                            while ($posicion = oci_fetch_assoc($stid_posiciones)) {
                                $selected = ($posicion['ID_POSICION'] == $empleado['ID_POSICION']) ? 'selected' : '';
                                echo "<option value=\"{$posicion['ID_POSICION']}\" $selected>{$posicion['NOMBRE']}</option>";
                            }

                            oci_free_statement($stid_posiciones);
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn" style="background-color: #013e6a; color: white; margin-bottom: 2rem;">Actualizar Empleado</button>
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
