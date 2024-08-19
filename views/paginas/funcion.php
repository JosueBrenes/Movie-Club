<?php
include '../../includes/database.php';

function obtener_peliculas() {
    global $conn;
    $sql = "BEGIN FIDE_PELICULAS_TB_OBTENER_PELICULAS_SP(:p_cursor); END;";
    $stid = oci_parse($conn, $sql);
    
    $cursor = oci_new_cursor($conn);
    oci_bind_by_name($stid, ':p_cursor', $cursor, -1, OCI_B_CURSOR);
    
    oci_execute($stid);
    oci_execute($cursor);
    
    $peliculas = array();
    while (($row = oci_fetch_assoc($cursor)) != false) {
        $peliculas[] = $row;
    }
    
    oci_free_statement($stid);
    oci_free_statement($cursor);
    return $peliculas;
}

function obtener_salas() {
    global $conn;
    $sql = "BEGIN FIDE_SALAS_TB_OBTENER_SALAS_SP(:p_cursor); END;";
    $stid = oci_parse($conn, $sql);
    
    $cursor = oci_new_cursor($conn);
    oci_bind_by_name($stid, ':p_cursor', $cursor, -1, OCI_B_CURSOR);
    
    oci_execute($stid);
    oci_execute($cursor);
    
    $salas = array();
    while (($row = oci_fetch_assoc($cursor)) != false) {
        if ($row['ID_ESTADO'] == 1) { 
            $salas[] = $row;
        }
    }
    
    oci_free_statement($stid);
    oci_free_statement($cursor);
    return $salas;
}


function obtener_siguiente_valor_secuencia($sec_name) {
    global $conn;
    $sql = "SELECT $sec_name.NEXTVAL AS NEXTVAL FROM DUAL";
    $stid = oci_parse($conn, $sql);
    oci_execute($stid);
    $row = oci_fetch_assoc($stid);
    oci_free_statement($stid);
    return $row['NEXTVAL'];
}

$id_funcion = obtener_siguiente_valor_secuencia('FIDE_FUNCION_SEQ');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha = $_POST['fecha'];
    $id_pelicula = $_POST['id_pelicula'];
    $id_sala = $_POST['id_sala'];
    $id_cliente = $_POST['id_cliente'];
    $id_estado = 1;

    $fecha_formateada = date('Y-m-d', strtotime($fecha));

    $id_funcion = obtener_siguiente_valor_secuencia('FIDE_FUNCION_SEQ');

    $sql = 'BEGIN FIDE_FUNCION_TB_INSERTAR_FUNCION_SP(:id_funcion, TO_DATE(:fecha, \'YYYY-MM-DD\'), :id_pelicula, :id_sala, :id_estado); END;';
    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ':id_funcion', $id_funcion);
    oci_bind_by_name($stid, ':fecha', $fecha_formateada);
    oci_bind_by_name($stid, ':id_pelicula', $id_pelicula);
    oci_bind_by_name($stid, ':id_sala', $id_sala);
    oci_bind_by_name($stid, ':id_estado', $id_estado);

    if (oci_execute($stid)) {
        header("Location: reserva.php?id_funcion=$id_funcion&id_cliente=$id_cliente&fecha=$fecha_formateada");
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
            <a href="../../views/auth/login.php" class="btn-contact" style="background-color: #19a4bf">Administrar</a>
        </nav>
    </header>

    <div class="hero">
        <!-- Contenido del hero -->
    </div>

    <div class="container mt-5 mb-5">
        <h1 class="text-center mb-4">Crear Nueva Función</h1>
        <div class="card">
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="id_cliente" value="<?php echo htmlspecialchars($_GET['id_cliente'] ?? ''); ?>">
                    <div class="form-group">
                        <label for="fecha">Fecha</label>
                        <input type="date" id="fecha" name="fecha" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="id_pelicula">Película</label>
                        <select id="id_pelicula" name="id_pelicula" class="form-control" required>
                            <?php
                            $peliculas = obtener_peliculas();
                            foreach ($peliculas as $pelicula) {
                                echo "<option value='{$pelicula['ID_PELICULA']}'>{$pelicula['NOMBRE']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="id_sala">Sala</label>
                        <select id="id_sala" name="id_sala" class="form-control" required>
                            <?php
                            $salas = obtener_salas();
                            foreach ($salas as $sala) {
                                echo "<option value='{$sala['ID_SALA']}'>{$sala['NOMBRE']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-danger">Crear Función</button>
                </form>
            </div>
        </div>
    </div>

    <?php include '../templates/footer.php'; ?>

</body>
</html>
