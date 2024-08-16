<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

// Consulta para géneros
$generos = oci_parse($conn, 'SELECT ID_GENERO, NOMBRE FROM FIDE_GENERO_TB');
oci_execute($generos);

// Consulta para directores
$directores = oci_parse($conn, 'SELECT ID_DIRECTOR, NOMBRE FROM FIDE_DIRECTOR_TB');
oci_execute($directores);

// Consulta para idiomas
$idIdiomas = oci_parse($conn, 'SELECT ID_IDIOMAS, NOMBRE FROM FIDE_IDIOMAS_TB');
oci_execute($idIdiomas);

// Consulta para estados
$estados = oci_parse($conn, 'SELECT ID_ESTADO, NOMBRE FROM FIDE_ESTADO_TB');
oci_execute($estados);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Agregar Película - Movie Club</title>
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
                <h1 style="color: #333">Agregar Nueva Película</h1>
                <form action="insertar_pelicula.php" method="POST">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="id_genero">Género</label>
                        <select class="form-control" id="id_genero" name="id_genero" required>
                            <?php while ($row = oci_fetch_assoc($generos)): ?>
                                <option value="<?php echo htmlspecialchars($row['ID_GENERO'], ENT_QUOTES); ?>">
                                    <?php echo htmlspecialchars($row['NOMBRE'], ENT_QUOTES); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="id_director">Director</label>
                        <select class="form-control" id="id_director" name="id_director" required>
                            <?php while ($row = oci_fetch_assoc($directores)): ?>
                                <option value="<?php echo htmlspecialchars($row['ID_DIRECTOR'], ENT_QUOTES); ?>">
                                    <?php echo htmlspecialchars($row['NOMBRE'], ENT_QUOTES); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="id_idiomas">Idioma</label>
                        <select class="form-control" style="color: black;" id="id_idiomas" name="id_idiomas" required>
                            <?php while ($row = oci_fetch_assoc($idIdiomas)): ?>
                                <option value="<?php echo htmlspecialchars($row['ID_IDIOMAS'], ENT_QUOTES); ?>">
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
                    <button type="submit" class="btn" style="background-color: #013e6a; color: white; margin-bottom: 2rem;">Agregar Película</button>
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

    <?php
    oci_free_statement($generos);
    oci_free_statement($directores);
    oci_free_statement($idIdiomas);
    oci_free_statement($estados);
    oci_close($conn);
    ?>
</body>
</html>
