<?php
include '../../includes/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo_electronico = $_POST['correo_electronico'];
    $contrasena = $_POST['contrasena'];
    
    $hashed_password = password_hash($contrasena, PASSWORD_BCRYPT);
    
    function obtener_siguiente_valor_secuencia($sec_name) {
        global $conn;
        $sql = "SELECT $sec_name.NEXTVAL AS NEXTVAL FROM DUAL";
        $stid = oci_parse($conn, $sql);
        oci_execute($stid);
        $row = oci_fetch_assoc($stid);
        oci_free_statement($stid);
        return $row['NEXTVAL'];
    }

    $id_cliente = obtener_siguiente_valor_secuencia('FIDE_CLIENTES_TB_SEQ');

    $sql = 'BEGIN FIDE_CLIENTES_TB_INSERTAR_CLIENTES_SP(:id_cliente, :nombre, :apellido, :correo_electronico, :contrasena); END;';
    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ':id_cliente', $id_cliente);
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':apellido', $apellido);
    oci_bind_by_name($stid, ':correo_electronico', $correo_electronico);
    oci_bind_by_name($stid, ':contrasena', $hashed_password);

    if (oci_execute($stid)) {
        header("Location: funcion.php?id_cliente=$id_cliente");
        exit();
    } else {
        $error = oci_error($stid);
        echo "Error: " . $error['message'];
    }
    oci_free_statement($stid);
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
            <a href="../views/auth/login.php" class="btn-contact" style="background-color: #19a4bf">Administrar</a>
        </nav>
    </header>

    <div class="hero">
        <!-- Contenido del hero -->
    </div>

    <div class="container mt-5 mb-5">
        <h1 class="text-center mb-4">Registrar Cliente</h1>
        <div class="card">
            <div class="card-body">
                <form action="registrar_cliente.php" method="post">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido</label>
                        <input type="text" id="apellido" name="apellido" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="correo_electronico">Correo Electrónico</label>
                        <input type="email" id="correo_electronico" name="correo_electronico" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="contrasena">Contraseña</label>
                        <input type="password" id="contrasena" name="contrasena" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-danger mb-3">Registrar</button>
                    <a href="ingresar_cliente.php" class="btn mb-3" style="background-color: #010323; color: white;">Ya tengo cuenta</a>
                </form>
            </div>
        </div>
    </div>

    <?php include '../templates/footer.php'; ?>
</body>
</html>
