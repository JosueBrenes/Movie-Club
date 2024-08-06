<?php
include '../../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    $contrasena = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : '';

    if (!empty($correo) && !empty($contrasena)) {
        $sql = 'SELECT CONTRASENA FROM FIDE_EMPLEADOS_TB WHERE CORREO_ELECTRONICO = :correo';
        $stid = oci_parse($conn, $sql);
        oci_bind_by_name($stid, ':correo', $correo);

        if (oci_execute($stid)) {
            $row = oci_fetch_assoc($stid);
            if ($row && password_verify($contrasena, $row['CONTRASENA'])) {
                header('Location: ../admin/dashboard.php');
                exit;
            } else {
                $error = 'Correo electrónico o contraseña incorrectos.';
            }
        } else {
            $error = 'Error al ejecutar la consulta: ' . htmlentities(oci_error($stid)['message'], ENT_QUOTES);
        }

        oci_free_statement($stid);
    } else {
        $error = 'Por favor, complete todos los campos.';
    }

    oci_close($conn);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Inicio de sesión - Movie Club</title>
  <link
    rel="stylesheet"
    href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
  />
  <link rel="stylesheet" href="../../public/build/css/stylesLogin.css" />
  <link rel="stylesheet" href="../../public/build/css/styles.css" />
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
      <a href="../../views/auth/login.php" class="btn-contact" style="background-color: #19a4bf">Administrar</a>
    </nav>
  </header>

  <div class="login-container">
    <h2>Inicio de sesión</h2>
    <?php if (isset($error)) : ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div>
    <?php endif; ?>
    <form method="post" action="login.php">
      <div class="form-group">
        <label for="correo">Correo electrónico</label>
        <input type="email" id="correo" name="correo" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="contrasena">Contraseña</label>
        <input type="password" id="contrasena" name="contrasena" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Iniciar sesión</button>
    </form>
  </div>

  <?php include '../templates/footer.php'; ?>

</body>
</html>
