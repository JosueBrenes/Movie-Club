<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

$sql = 'SELECT * FROM FIDE_CLIENTES_TB';
$stid = oci_parse($conn, $sql);

oci_execute($stid);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Clientes - Movie Club</title>
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
            <div class="container">
                <h1 style="color: #333">Clientes</h1>
                <a href="agregar_cliente.php" class="button">Agregar Nuevo Cliente</a>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Correo Electrónico</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = oci_fetch_assoc($stid)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['ID_CLIENTE'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['NOMBRE'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['APELLIDO'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['CORREO_ELECTRONICO'], ENT_QUOTES); ?></td>
                                <td>
                                    <a href="editar_cliente.php?id=<?php echo htmlspecialchars($row['ID_CLIENTE'], ENT_QUOTES); ?>" class="btn" style="background-color: #013e6a; color: white;" >Editar</a>
                                    <a href="eliminar_cliente.php?id=<?php echo htmlspecialchars($row['ID_CLIENTE'], ENT_QUOTES); ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este cliente?');">Eliminar</a>
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
