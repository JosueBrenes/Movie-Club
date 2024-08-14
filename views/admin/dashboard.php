<?php
include '../../includes/database.php';

$totalPeliculas = 0;
$totalClientes = 0;
$totalEmpleados = 0;

$sqlPeliculas = 'SELECT COUNT(*) AS total FROM FIDE_PELICULAS_TB';
$stidPeliculas = oci_parse($conn, $sqlPeliculas);
oci_execute($stidPeliculas);
$rowPeliculas = oci_fetch_assoc($stidPeliculas);
if ($rowPeliculas) {
    $totalPeliculas = $rowPeliculas['TOTAL'];
}
oci_free_statement($stidPeliculas);

$sqlClientes = 'SELECT COUNT(*) AS total FROM FIDE_CLIENTES_TB';
$stidClientes = oci_parse($conn, $sqlClientes);
oci_execute($stidClientes);
$rowClientes = oci_fetch_assoc($stidClientes);
if ($rowClientes) {
    $totalClientes = $rowClientes['TOTAL'];
}
oci_free_statement($stidClientes);

$sqlEmpleados = 'SELECT COUNT(*) AS total FROM FIDE_EMPLEADOS_TB';
$stidEmpleados = oci_parse($conn, $sqlEmpleados);
oci_execute($stidEmpleados);
$rowEmpleados = oci_fetch_assoc($stidEmpleados);
if ($rowEmpleados) {
    $totalEmpleados = $rowEmpleados['TOTAL'];
}
oci_free_statement($stidEmpleados);

oci_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Administrativa - Movie Club</title>
  <link
    rel="stylesheet"
    href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
  />
  <link rel="stylesheet" href="../../public/build/css/stylesDash.css" />
  <link rel="icon" href="../public/build/img/icon.png" type="image/x-icon" />
  <link
    rel="shortcut icon"
    href="../public/build/img/icon.png"
    type="image/x-icon"
  />
</head>
<body>

  <!-- Sidebar -->
  <nav class="sidebar">
    <h2>Opciones</h2>
    <a href="clientes/clientes.php">Gestionar Clientes</a>
    <a href="comida/comida.php">Gestionar Comida</a>
    <a href="empleados/empleados.php">Gestionar Empleados</a>
    <a href="metodo_pagos/metodo_pagos.php">Gestionar Métodos de Pago</a>
    <a href="peliculas/peliculas.php">Gestionar Películas</a>
    <a href="genero/genero.php">Gestionar Generos</a>
    <a href="idiomas/idiomas.php">Gestionar Idiomas</a>
    <a href="directores/directores.php">Gestionar Directores</a>
    <a href="salas/salas.php">Gestionar Salas</a>
    <a href="funciones/funciones.php">Gestionar Funciones</a>
    <a href="proovedores/proovedores.php">Gestionar Proveedores</a>
    <a href="inventario/inventario.php">Gestionar Inventario</a>
    <a href="posiciones/posiciones.php">Gestionar Puesto de Trabajo</a>
    <a href="tipos_sala/tipos_sala.php">Gestionar Tipos de Sala</a>
    <div class="sidebar-footer">
    <a href="../../../public/index.php" class="btn btn-danger btn-logout">Salir</a>
    </div>
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
        <div class="center-cards">
          <!-- Tarjeta de Total de Películas -->
          <div class="card-container">
            <div class="card border-primary bg-primary text-white">
              <div class="card-body text-center">
                <h1 class="card-title display-4"><?php echo $totalPeliculas; ?></h1>
                <h3>Películas</h3>
              </div>
            </div>
          </div>
          
          <!-- Tarjeta de Total de Clientes -->
          <div class="card-container">
            <div class="card border-primary bg-primary text-white">
              <div class="card-body text-center">
                <h1 class="card-title display-4"><?php echo $totalClientes; ?></h1>
                <h3>Clientes</h3>
              </div>
            </div>
          </div>

          <!-- Tarjeta de Total de Empleados -->
          <div class="card-container">
            <div class="card border-primary bg-primary text-white">
              <div class="card-body text-center">
                <h1 class="card-title display-4"><?php echo $totalEmpleados; ?></h1>
                <h3>Empleados</h3>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer_area">
      <p class="footer_text">&copy; 2024 Movie Club. Todos los derechos reservados.</p>
    </footer>
  </div>

</body>
</html>
