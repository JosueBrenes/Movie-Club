<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID del empleado no proporcionado.");
}

$id_empleado = $_GET['id'];

// Consulta para obtener la información del empleado
$sql = 'SELECT e.ID_EMPLEADO, e.NOMBRE, e.APELLIDO, e.CORREO_ELECTRONICO, e.TELEFONO, e.ID_POSICION, p.NOMBRE AS NOMBRE_POSICION
        FROM FIDE_EMPLEADOS_TB e
        LEFT JOIN FIDE_POSICION_TB p ON e.ID_POSICION = p.ID_POSICION
        WHERE e.ID_EMPLEADO = :id_empleado';
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
                <h1 style="color: #333">Editar Empleado</h1>
                <form action="actualizar_empleado.php" method="POST">
                    <input type="hidden" name="id_empleado" value="<?php echo htmlspecialchars($empleado['ID_EMPLEADO'], ENT_QUOTES); ?>">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($empleado['NOMBRE'], ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido</label>
                        <input type="text" id="apellido" name="apellido" class="form-control" value="<?php echo htmlspecialchars($empleado['APELLIDO'], ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo Electrónico</label>
                        <input type="email" id="correo" name="correo" class="form-control" value="<?php echo htmlspecialchars($empleado['CORREO_ELECTRONICO'], ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" class="form-control" value="<?php echo htmlspecialchars($empleado['TELEFONO'], ENT_QUOTES); ?>">
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
                    <div class="form-group">
                        <label for="contrasena">Contraseña</label>
                        <input type="password" id="contrasena" name="contrasena" class="form-control" placeholder="Dejar en blanco si no se desea cambiar">
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
