<?php
include '../../../includes/database.php';

if (!$conn) {
    die("Conexión fallida: " . htmlentities(oci_error()['message'], ENT_QUOTES));
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID del cliente no proporcionado.");
}

$cliente_id = $_GET['id'];

$sql = 'SELECT * FROM FIDE_CLIENTES_TB WHERE ID_CLIENTE = :cliente_id';
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ':cliente_id', $cliente_id);
oci_execute($stid);
$cliente = oci_fetch_assoc($stid);

if (!$cliente) {
    die("No se encontró el cliente.");
}

oci_free_statement($stid);
oci_close($conn);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Editar Cliente - Movie Club</title>
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
                <h1 style="color: #333">Editar Cliente</h1>
                <form action="actualizar_cliente.php" method="POST">
                    <input type="hidden" name="cliente_id" value="<?php echo htmlspecialchars($cliente['ID_CLIENTE'] ?? '', ENT_QUOTES); ?>">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($cliente['NOMBRE'] ?? '', ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido</label>
                        <input type="text" id="apellido" name="apellido" class="form-control" value="<?php echo htmlspecialchars($cliente['APELLIDO'] ?? '', ENT_QUOTES); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($cliente['CORREO_ELECTRONICO'] ?? '', ENT_QUOTES); ?>" required>
                    </div>
                    <button type="submit" class="btn" style="background-color: #013e6a; color: white; margin-bottom: 2rem;">Actualizar Cliente</button>
                </form>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer_area">
            <p class="footer_text">&copy; 2024 Movie Club. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>
