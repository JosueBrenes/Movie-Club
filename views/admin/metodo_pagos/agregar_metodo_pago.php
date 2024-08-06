<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

// Consulta para estados
$estados = oci_parse($conn, 'SELECT ID_ESTADO, NOMBRE FROM FIDE_ESTADO_TB');
oci_execute($estados);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Agregar Método de Pago - Movie Club</title>
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
                <h1 style="color: #333">Agregar Nuevo Método de Pago</h1>
                <form action="insertar_metodo_pago.php" method="POST">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="id_estado">Estado</label>
                        <select class="form-control" id="id_estado" name="id_estado" required>
                            <?php while ($row = oci_fetch_assoc($estados)): ?>
                                <option value="<?php echo htmlspecialchars($row['ID_ESTADO'], ENT_QUOTES); ?>">
                                    <?php echo htmlspecialchars($row['NOMBRE'], ENT_QUOTES); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn" style="background-color: #013e6a; color: white; margin-bottom: 2rem;">Agregar Método de Pago</button>
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
