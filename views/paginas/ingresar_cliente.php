<?php
include '../../includes/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo_electronico = $_POST['correo_electronico'];
    $contrasena = $_POST['contrasena'];

    $cursor = oci_new_cursor($conn);

    $sql = 'BEGIN FIDE_CLIENTES_TB_OBTENER_CLIENTES_SP(:p_cursor); END;';
    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ':p_cursor', $cursor, -1, OCI_B_CURSOR);
    oci_execute($stid);
    oci_execute($cursor);

    $cliente_encontrado = false;
    while (($row = oci_fetch_assoc($cursor)) !== false) {
        if ($row['CORREO_ELECTRONICO'] === $correo_electronico) {
            if (password_verify($contrasena, $row['CONTRASENA'])) {
                $cliente_encontrado = true;
                $id_cliente = $row['ID_CLIENTE'];
                break;
            }
        }
    }
    oci_free_statement($stid);
    oci_free_statement($cursor);

    if ($cliente_encontrado) {
        header("Location: funcion.php?id_cliente=" . $id_cliente);
        exit();
    } else {
        echo "Correo electrónico o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Movie Club</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../../public/build/css/stylesCartelera.css" />
    <link rel="stylesheet" href="../../public/build/css/styles.css" />
    <link rel="icon" href="../../public/build/img/icon.png" type="image/x-icon" />
    <link rel="shortcut icon" href="../../public/build/img/icon.png" type="image/x-icon" />
</head>
<body>
    <header class="navbar">
        <a href="../../public/index.php" class="text-dark">
            <h1 class="font-weight-bold">Movie Club</h1>
        </a>
        <nav class="menu">
            <a href="../../views/paginas/cartelera.php" class="btn-contact" style="background-color: #19a4bf">Cartelera</a>
            <a href="../../views/auth/login.php" class="btn-contact" style="background-color: #19a4bf">Administrar</a>
        </nav>
    </header>

    <div class="hero">
        <!-- Contenido del hero -->
    </div>

    <div class="container mt-5 mb-5">
        <h1 class="text-center mb-4">Iniciar Sesión</h1>
        <div class="card">
            <div class="card-body">
                <form action="ingresar_cliente.php" method="post">
                    <div class="form-group">
                        <label for="correo_electronico">Correo Electrónico</label>
                        <input type="email" id="correo_electronico" name="correo_electronico" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="contrasena">Contraseña</label>
                        <input type="password" id="contrasena" name="contrasena" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-danger mb-3">Iniciar Sesión</button>
                    <a href="registrar_cliente.php" class="btn mb-3" style="background-color: #010323; color: white;">Registrarme</a>
                </form>
            </div>
        </div>
    </div>

    <?php include '../templates/footer.php'; ?>
</body>
</html>
