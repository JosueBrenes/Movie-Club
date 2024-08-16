<?php
include '../../includes/database.php';

function obtener_peliculas() {
    global $conn;

    $sql = 'BEGIN FIDE_PELICULAS_TB_OBTENER_PELICULAS_SP(:p_cursor); END;';
    $stid = oci_parse($conn, $sql);
    $cursor = oci_new_cursor($conn);
    oci_bind_by_name($stid, ':p_cursor', $cursor, -1, OCI_B_CURSOR);
    oci_execute($stid);
    oci_execute($cursor);
    
    $peliculas = [];
    while ($row = oci_fetch_assoc($cursor)) {
        $peliculas[] = $row;
    }

    oci_free_statement($stid);
    oci_free_statement($cursor);

    return $peliculas;
}

function obtener_datos_adicionales() {
    global $conn;

    // Obtener Género
    $sql_genero = 'BEGIN FIDE_GENERO_TB_OBTENER_GENERO_SP(:p_cursor); END;';
    $stid_genero = oci_parse($conn, $sql_genero);
    $cursor_genero = oci_new_cursor($conn);
    oci_bind_by_name($stid_genero, ':p_cursor', $cursor_genero, -1, OCI_B_CURSOR);
    oci_execute($stid_genero);
    oci_execute($cursor_genero);
    $generos = [];
    while ($row = oci_fetch_assoc($cursor_genero)) {
        $generos[$row['ID_GENERO']] = $row['NOMBRE'];
    }
    oci_free_statement($stid_genero);
    oci_free_statement($cursor_genero);

    // Obtener Director
    $sql_director = 'BEGIN FIDE_DIRECTOR_TB_OBTENER_DIRECTOR_SP(:p_cursor); END;';
    $stid_director = oci_parse($conn, $sql_director);
    $cursor_director = oci_new_cursor($conn);
    oci_bind_by_name($stid_director, ':p_cursor', $cursor_director, -1, OCI_B_CURSOR);
    oci_execute($stid_director);
    oci_execute($cursor_director);
    $directores = [];
    while ($row = oci_fetch_assoc($cursor_director)) {
        $directores[$row['ID_DIRECTOR']] = $row['NOMBRE'];
    }
    oci_free_statement($stid_director);
    oci_free_statement($cursor_director);

    // Obtener Idiomas
    $sql_idiomas = 'BEGIN FIDE_IDIOMAS_TB_OBTENER_IDIOMAS_SP(:p_cursor); END;';
    $stid_idiomas = oci_parse($conn, $sql_idiomas);
    $cursor_idiomas = oci_new_cursor($conn);
    oci_bind_by_name($stid_idiomas, ':p_cursor', $cursor_idiomas, -1, OCI_B_CURSOR);
    oci_execute($stid_idiomas);
    oci_execute($cursor_idiomas);
    $idiomas = [];
    while ($row = oci_fetch_assoc($cursor_idiomas)) {
        $idiomas[$row['ID_IDIOMAS']] = $row['NOMBRE'];
    }
    oci_free_statement($stid_idiomas);
    oci_free_statement($cursor_idiomas);

    // Obtener Estado
    $sql_estado = 'BEGIN FIDE_ESTADO_TB_OBTENER_ESTADO_SP(:p_cursor); END;';
    $stid_estado = oci_parse($conn, $sql_estado);
    $cursor_estado = oci_new_cursor($conn);
    oci_bind_by_name($stid_estado, ':p_cursor', $cursor_estado, -1, OCI_B_CURSOR);
    oci_execute($stid_estado);
    oci_execute($cursor_estado);
    $estados = [];
    while ($row = oci_fetch_assoc($cursor_estado)) {
        $estados[$row['ID_ESTADO']] = $row['NOMBRE'];
    }
    oci_free_statement($stid_estado);
    oci_free_statement($cursor_estado);

    return [
        'generos' => $generos,
        'directores' => $directores,
        'idiomas' => $idiomas,
        'estados' => $estados
    ];
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
      <a href="../../public/index.php">Inicio</a>
      <a href="../../views/paginas/cartelera.php" class="btn-contact" style="background-color: #19a4bf">Cartelera</a>
      <a href="../../views/auth/login.php" class="btn-contact" style="background-color: #19a4bf">Administrar</a>
    </nav>
  </header>

  <div class="hero">
    <!-- Contenido del hero -->
  </div>

  <div class="container mt-5">
    <h1 class="text-center mb-4">Cartelera de Películas</h1>
    <div class="row">
        <?php
        $peliculas = obtener_peliculas();
        $datos_adicionales = obtener_datos_adicionales();
        
        foreach ($peliculas as $pelicula) {
            $id_genero = $pelicula['ID_GENERO'];
            $id_director = $pelicula['ID_DIRECTOR'];
            $id_idiomas = $pelicula['ID_IDIOMAS'];
            $id_estado = $pelicula['ID_ESTADO'];

            if ($id_estado == '1') {
                echo '<div class="col-md-4 mb-4">';
                echo '    <div class="movie-card">';
                echo '        <div class="movie-info">';
                echo '            <h3 class="movie-title">' . htmlspecialchars($pelicula['NOMBRE'], ENT_QUOTES) . '</h3>';
                echo '            <p class="movie-details"><strong>Director:</strong> ' . htmlspecialchars($datos_adicionales['directores'][$id_director] ?? 'Desconocido', ENT_QUOTES) . '</p>';
                echo '            <p class="movie-details"><strong>Género:</strong> ' . htmlspecialchars($datos_adicionales['generos'][$id_genero] ?? 'Desconocido', ENT_QUOTES) . '</p>';
                echo '            <p class="movie-details"><strong>Idioma:</strong> ' . htmlspecialchars($datos_adicionales['idiomas'][$id_idiomas] ?? 'Desconocido', ENT_QUOTES) . '</p>';
                echo '            <p class="movie-details"><strong>Descripción:</strong> ' . htmlspecialchars($pelicula['DESCRIPCION'], ENT_QUOTES) . '</p>';
                echo '            <a href="../paginas/registrar_cliente.php" class="btn btn-primary">Comprar Boletos</a>';
                echo '        </div>';
                echo '    </div>';
                echo '</div>';
            }
        }
        ?>
    </div>
  </div>

  <?php include '../templates/footer.php'; ?>
</body>
</html>