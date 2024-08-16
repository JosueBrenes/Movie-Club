<?php

include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

// Obtener las películas disponibles
$peliculas_query = 'SELECT ID_PELICULA, NOMBRE FROM FIDE_PELICULAS_TB';
$peliculas_stid = oci_parse($conn, $peliculas_query);
oci_execute($peliculas_stid);

// Obtener las salas disponibles
$salas_query = 'SELECT ID_SALA, NOMBRE FROM FIDE_SALAS_TB';
$salas_stid = oci_parse($conn, $salas_query);
oci_execute($salas_stid);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Agregar Función - Movie Club</title>
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
                <h1 style="color: #333">Agregar Nueva Función</h1>
                <form action="insertar_funcion.php" method="POST">
                    <div class="form-group">
                        <label for="fecha">Fecha</label>
                        <input type="date" id="fecha" name="fecha" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="id_pelicula">Película</label>
                        <select id="id_pelicula" name="id_pelicula" class="form-control" required>
                            <?php while ($row = oci_fetch_assoc($peliculas_stid)): ?>
                                <option value="<?php echo htmlspecialchars($row['ID_PELICULA'], ENT_QUOTES); ?>">
                                    <?php echo htmlspecialchars($row['NOMBRE'], ENT_QUOTES); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="id_sala">Sala</label>
                        <select id="id_sala" name="id_sala" class="form-control" required>
                            <?php while ($row = oci_fetch_assoc($salas_stid)): ?>
                                <option value="<?php echo htmlspecialchars($row['ID_SALA'], ENT_QUOTES); ?>">
                                    <?php echo htmlspecialchars($row['NOMBRE'], ENT_QUOTES); ?>
                                </option>
                            <?php endwhile; ?>
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
                    <button type="submit" class="btn" style="background-color: #013e6a; color: white; margin-bottom: 2rem;">Agregar Función</button>
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

<?php
oci_free_statement($peliculas_stid);
oci_free_statement($salas_stid);
oci_close($conn);
?>