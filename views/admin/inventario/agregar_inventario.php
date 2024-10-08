<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

$query = 'SELECT ID_PROVEEDOR, NOMBRE FROM FIDE_PROVEEDORES_TB ORDER BY NOMBRE';
$stid = oci_parse($conn, $query);
oci_execute($stid);

$proveedores = [];
while ($row = oci_fetch_assoc($stid)) {
    $proveedores[] = $row;
}

oci_free_statement($stid);
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Agregar Inventario - Movie Club</title>
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
                <h1 style="color: #333">Agregar Nuevo Inventario</h1>
                <form action="insertar_inventario.php" method="POST">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="cantidad">Cantidad</label>
                        <input type="number" id="cantidad" name="cantidad" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="id_proveedor">ID Proveedor</label>
                        <select id="id_proveedor" name="id_proveedor" class="form-control" required>
                            <option value="" disabled selected>Seleccione un proveedor</option>
                            <?php foreach ($proveedores as $proveedor): ?>
                                <option value="<?php echo htmlspecialchars($proveedor['ID_PROVEEDOR'], ENT_QUOTES); ?>">
                                    <?php echo htmlspecialchars($proveedor['NOMBRE'], ENT_QUOTES); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="id_estado">Estado</label>
                        <select id="id_estado" name="id_estado" class="form-control" required>
                            <?php
                            include '../../../includes/database.php';
                            
                            $sql = 'BEGIN FIDE_ESTADO_TB_OBTENER_ESTADO_SP(:p_cursor); END;';
                            $stid = oci_parse($conn, $sql);
                            
                            $cursor = oci_new_cursor($conn);
                            
                            oci_bind_by_name($stid, ":p_cursor", $cursor, -1, OCI_B_CURSOR);
                            
                            oci_execute($stid);
                            
                            oci_execute($cursor);
                            
                            while ($row = oci_fetch_assoc($cursor)) {
                                echo '<option value="' . htmlspecialchars($row['ID_ESTADO'], ENT_QUOTES) . '">' . htmlspecialchars($row['NOMBRE'], ENT_QUOTES) . '</option>';
                            }
                            
                            oci_free_statement($cursor);
                            oci_free_statement($stid);
                            oci_close($conn);
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn" style="background-color: #013e6a; color: white; margin-bottom: 2rem;">Agregar Inventario</button>
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
