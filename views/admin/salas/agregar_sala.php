<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Agregar Sala - Movie Club</title>
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
                <h1 style="color: #333">Agregar Nueva Sala</h1>
                <form action="insertar_sala.php" method="POST">
                    <div class="form-group">
                        <label for="nombre">Nombre de la Sala</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="numero_asientos">Número de Asientos</label>
                        <input type="number" id="numero_asientos" name="numero_asientos" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="id_tipo_sala">Tipo de Sala</label>
                        <select id="id_tipo_sala" name="id_tipo_sala" class="form-control" required>
                            <?php
                            // Cargar tipos de sala desde la base de datos
                            include '../../../includes/database.php';
                            $sql = 'SELECT ID_TIPO_SALA, NOMBRE FROM FIDE_TIPO_SALA_TB';
                            $stid = oci_parse($conn, $sql);
                            oci_execute($stid);
                            while ($row = oci_fetch_assoc($stid)) {
                                echo '<option value="' . htmlspecialchars($row['ID_TIPO_SALA'], ENT_QUOTES) . '">' . htmlspecialchars($row['NOMBRE'], ENT_QUOTES) . '</option>';
                            }
                            oci_free_statement($stid);
                            oci_close($conn);
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="id_estado">Estado</label>
                        <select id="id_estado" name="id_estado" class="form-control" required>
                            <?php
                            // Cargar estados desde la base de datos
                            include '../../../includes/database.php';
                            $sql = 'SELECT ID_ESTADO, NOMBRE FROM FIDE_ESTADO_TB';
                            $stid = oci_parse($conn, $sql);
                            oci_execute($stid);
                            while ($row = oci_fetch_assoc($stid)) {
                                echo '<option value="' . htmlspecialchars($row['ID_ESTADO'], ENT_QUOTES) . '">' . htmlspecialchars($row['NOMBRE'], ENT_QUOTES) . '</option>';
                            }
                            oci_free_statement($stid);
                            oci_close($conn);
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn" style="background-color: #013e6a; color: white; margin-bottom: 2rem;">Agregar Sala</button>
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
