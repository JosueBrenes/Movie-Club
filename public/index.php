<?php
include '../includes/database.php'; 

if (!$conn) {
    $e = oci_error();
    echo htmlentities($e['message'], ENT_QUOTES);
    exit;
}

$sql = "SELECT * FROM FIDE_PELICULAS_TB";
$stid = oci_parse($conn, $sql);

if (!oci_execute($stid)) {
    $e = oci_error($stid);
    echo htmlentities($e['message'], ENT_QUOTES);
    exit;
}
?>

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
      <a href="../public/index.php">Inicio</a>
      <a href="../views/paginas/boleteria.php">Boletería</a>
      <a href="../views/paginas/dulceria.php">Dulcería</a>
      <a href="../views/paginas/cartelera.php" class="btn-contact" style="background-color: #19a4bf">Cartelera</a>
    </nav>
  </header>

  <div class="hero">
  </div>

  <div class="container mt-5">
    <div class="row">
      <?php
      while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
        echo '<div class="col-lg-4 col-md-6 mb-4">';
        echo '<div class="card">';
        $imagen = isset($row['IMAGEN']) ? $row['IMAGEN'] : '../public/build/img/default.jpg'; 
        echo '<img src="' . $imagen . '" class="card-img-top" alt="' . htmlspecialchars($row['NOMBRE'], ENT_QUOTES) . '">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">' . htmlspecialchars($row['NOMBRE'], ENT_QUOTES) . '</h5>';
        echo '<p class="card-text">' . htmlspecialchars($row['DESCRIPCION'], ENT_QUOTES) . '</p>';
        echo '<a href="#" class="btn btn-primary">Ver más</a>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
      }
      ?>
    </div>
  </div>

  <?php include '../views/templates/footer.php'; ?>

</body>
</html>

<?php
oci_close($conn);
?>
