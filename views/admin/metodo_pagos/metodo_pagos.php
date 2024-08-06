<?php
include '../../../includes/database.php';

if (!$conn) {
    echo "No se pudo conectar a la base de datos.";
    exit;
}

// Consulta para métodos de pago, incluyendo el nombre del estado
$sql = '
    SELECT m.ID_METODO_PAGO, m.NOMBRE AS NOMBRE_METODO, m.DESCRIPCION, e.NOMBRE AS NOMBRE_ESTADO
    FROM FIDE_METODO_PAGO_TB m
    LEFT JOIN FIDE_ESTADO_TB e ON m.ID_ESTADO = e.ID_ESTADO
';

$stid = oci_parse($conn, $sql);

if (!$stid) {
    $e = oci_error($conn);
    echo "Error al preparar la consulta: " . $e['message'];
    exit;
}

$success = oci_execute($stid);

if (!$success) {
    $e = oci_error($stid);
    echo "Error al ejecutar la consulta: " . $e['message'];
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Métodos de Pago - Movie Club</title>
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
                <h1 style="color: #333">Métodos de Pago</h1>
                <a href="agregar_metodo_pago.php" class="button">Agregar Nuevo Método de Pago</a>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID Método de Pago</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = oci_fetch_assoc($stid)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['ID_METODO_PAGO'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['NOMBRE_METODO'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['DESCRIPCION'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['NOMBRE_ESTADO'], ENT_QUOTES); ?></td>
                                <td>
                                    <a href="editar_metodo_pago.php?id=<?php echo htmlspecialchars($row['ID_METODO_PAGO'], ENT_QUOTES); ?>" class="btn" style="background-color: #013e6a; color: white;">Editar</a>
                                    <a href="eliminar_metodo_pago.php?id=<?php echo htmlspecialchars($row['ID_METODO_PAGO'], ENT_QUOTES); ?>" class="btn btn-danger" style="background-color: #d9534f; color: white;">Eliminar</a>
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
    oci_free_statement($stid);
    oci_close($conn);
    ?>
</body>
</html>
