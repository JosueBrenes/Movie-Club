<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Movie Club</title>
  <link
    rel="stylesheet"
    href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
  />
  <link rel="stylesheet" href="../public/build/css/styles.css" />
  <link rel="icon" href="../public/build/img/icon.png" type="image/x-icon" />
  <link rel="shortcut icon" href="../public/build/img/icon.png" type="image/x-icon" />
</head>

<body>
  <header class="navbar">
    <a href="../../public/index.php" class="text-dark">
      <h1 class="font-weight-bold">Movie Club</h1>
    </a>
    <nav class="menu">
      <a href="../../public/index.php">Inicio</a>
      <a href="../../views/paginas/boleteria.php">Boletería</a>
      <a href="../../views/paginas/dulceria.php">Dulcería</a>
      <a href="../../views/paginas/cartelera.php" class="btn-contact" style="background-color: #19a4bf">Cartelera</a>
      <a href="../../views/admin/dashboard.php" class="btn-contact" style="background-color: #19a4bf">Administrar</a>
    </nav>
  </header>

  <div class="hero">
    <!-- Contenido del hero -->
  </div>

  <div class="section">
    <h2 class="text-center section-header">Estrenos</h2>
    <div class="container">
      <div class="row">
        <div class="col-md-3">
          <div class="card movie-card">
            <div class="movie-card-container">
              <img src="../public/build/img/criaturas-asombrosas.jpeg" alt="Película 1">
              <div class="btn-container">
                <a href="#" class="btn btn-red btn-block">Ver Horarios</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card movie-card">
            <div class="movie-card-container">
              <img src="../public/build/img/deadpool.png" alt="Película 2">
              <div class="btn-container">
                <a href="#" class="btn btn-red btn-block">Ver Horarios</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card movie-card">
            <div class="movie-card-container">
              <img src="../public/build/img/mi-villano-favorito.jpg" alt="Película 3">
              <div class="btn-container">
                <a href="#" class="btn btn-red btn-block">Ver Horarios</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card movie-card">
            <div class="movie-card-container">
              <img src="../public/build/img/intensamente.jpg" alt="Película 3">
              <div class="btn-container">
                <a href="#" class="btn btn-red btn-block">Ver Horarios</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="section text-center">
    <h2>Contáctanos</h2>
    <p>Para más información, no dudes en ponerte en contacto con nosotros.</p>
    <a href="mailto:contacto@movieclub.com" class="btn btn-primary">Enviar Correo</a>
  </div>

  <?php include '../views/templates/footer.php'; ?>

  <!-- Scripts de Bootstrap -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
