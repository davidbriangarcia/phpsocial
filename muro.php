<?php
session_start();
date_default_timezone_set('America/Lima');
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conexion = new mysqli("sql107.infinityfree.com", "if0_38993730", "N2H2vWw7E", "if0_38993730_redsocial");
$conexion->query("SET time_zone = '-05:00'");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Publicar un nuevo post
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nueva_publicacion"])) {
    $contenido = $conexion->real_escape_string($_POST["contenido"]);
    $usuario_id = $_SESSION["usuario_id"];
    $foto_nombre = null;

    // Procesar imagen si se subió
    if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0) {
        $ext = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
        $foto_nombre = uniqid("img_") . "." . $ext;
        $ruta_destino = "uploads/" . $foto_nombre;
        move_uploaded_file($_FILES["foto"]["tmp_name"], $ruta_destino);
    }

    // Guarda la publicación con o sin foto
    if ($foto_nombre) {
        $conexion->query("INSERT INTO publicaciones (usuario_id, contenido, imagen) VALUES ($usuario_id, '$contenido', '$foto_nombre')");
    } else {
        $conexion->query("INSERT INTO publicaciones (usuario_id, contenido) VALUES ($usuario_id, '$contenido')");
    }
}

// Agregar un comentario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nuevo_comentario"])) {
    $contenido = $conexion->real_escape_string($_POST["comentario"]);
    $usuario_id = $_SESSION["usuario_id"];
    $publicacion_id = intval($_POST["publicacion_id"]);
    $conexion->query("INSERT INTO comentarios (publicacion_id, usuario_id, contenido) VALUES ($publicacion_id, $usuario_id, '$contenido')");
}

// Obtener publicaciones con autores
$publicaciones = $conexion->query("
    SELECT publicaciones.*, usuarios.nombre 
    FROM publicaciones 
    JOIN usuarios ON publicaciones.usuario_id = usuarios.id 
    ORDER BY publicaciones.fecha DESC
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Muro</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Bienvenido, <?php echo ucwords($_SESSION["nombre"]); ?></h2>
        <a href="logout.php" class="btn btn-danger">Cerrar sesión</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title">Publicar algo:</h4>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="mb-3">
                    <textarea class="form-control" name="contenido" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <input type="file" class="form-control" name="foto" accept="image/*">
                </div>
                <input type="submit" name="nueva_publicacion" value="Publicar" class="btn btn-primary">
            </form>
        </div>
    </div>

    <hr>

    <h3>Muro:</h3>

    <?php while ($pub = $publicaciones->fetch_assoc()): ?>
        <div class="card mb-4">
            <div class="card-body">
                <strong><?php echo htmlspecialchars($pub["nombre"]); ?></strong><br>
                <p><?php echo nl2br(htmlspecialchars($pub["contenido"])); ?></p>
                <?php if (!empty($pub["imagen"])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($pub["imagen"]); ?>" alt="Foto" class="img-fluid mb-2" style="max-width:300px;"><br>
                <?php endif; ?>
                <small class="text-muted"><?php echo $pub["fecha"]; ?></small>
                <div class="mt-2">
                <?php if ($_SESSION["usuario_id"] == $pub["usuario_id"]): ?>
                    <form method="POST" action="editar_publicacion.php" style="display:inline;">
                        <input type="hidden" name="publicacion_id" value="<?php echo $pub['id']; ?>">
                        <input type="submit" value="Editar" class="btn btn-sm btn-warning">
                    </form>
                    <form method="POST" action="eliminar_publicacion.php" style="display:inline;" onsubmit="return confirm('¿Estás seguro de eliminar esta publicación?');">
                        <input type="hidden" name="publicacion_id" value="<?php echo $pub['id']; ?>">
                        <input type="submit" value="Eliminar" class="btn btn-sm btn-danger">
                    </form>
                <?php endif; ?>
                </div>
                <?php
                // Verificar si el usuario ya dio like
                $usuario_id = $_SESSION["usuario_id"];
                $pid = $pub["id"];
                $tieneLike = $conexion->query("SELECT * FROM likes WHERE usuario_id = $usuario_id AND publicacion_id = $pid")->num_rows > 0;

                // Contar likes totales
                $totalLikes = $conexion->query("SELECT COUNT(*) as total FROM likes WHERE publicacion_id = $pid")->fetch_assoc()["total"];
                ?>
                <form method="POST" action="like.php" style="display:inline;">
                    <input type="hidden" name="publicacion_id" value="<?php echo $pid; ?>">
                    <input type="submit" name="<?php echo $tieneLike ? 'quitar' : 'dar'; ?>" value="<?php echo $tieneLike ? 'Quitar Me gusta' : 'Me gusta'; ?>" class="btn btn-sm btn-outline-primary">
                </form>
                <small>(<?php echo $totalLikes; ?>)</small>

               <!-- Mostrar comentarios -->
                <div class="mt-3 ps-3 border-start">
                    <strong>Comentarios:</strong><br>
                    <?php
                    $pid = $pub["id"];
                    $comentarios = $conexion->query("
                        SELECT comentarios.*, usuarios.nombre 
                        FROM comentarios 
                        JOIN usuarios ON comentarios.usuario_id = usuarios.id 
                        WHERE comentarios.publicacion_id = $pid 
                        ORDER BY comentarios.fecha ASC
                    ");
                    while ($com = $comentarios->fetch_assoc()) {
                        echo "<div class='mb-2'><strong>" . htmlspecialchars($com["nombre"]) . ":</strong> " . nl2br(htmlspecialchars($com["contenido"])) . "<br><small class='text-muted'>" . $com["fecha"] . "</small>";

                        // Mostrar la imagen del comentario si existe
                        if (!empty($com["imagen"])) {
                            echo "<br><img src='uploads/" . htmlspecialchars($com["imagen"]) . "' class='img-fluid' style='max-width:200px;'>";
                        }

                        // Mostrar botones de editar/eliminar solo si el comentario es del usuario actual
                        if ($_SESSION["usuario_id"] == $com["usuario_id"]) {
                            echo '
                                <br>
                                <form method="POST" action="editar_comentario.php" style="display:inline;">
                                    <input type="hidden" name="comentario_id" value="' . $com['id'] . '">
                                    <input type="submit" value="Editar" class="btn btn-sm btn-warning">
                                </form>
                                <form method="POST" action="eliminar_comentario.php" style="display:inline;" onsubmit="return confirm(\'¿Eliminar comentario?\');">
                                    <input type="hidden" name="comentario_id" value="' . $com['id'] . '">
                                    <input type="submit" value="Eliminar" class="btn btn-sm btn-danger">
                                </form>
                            ';
                        }

                        echo "</div>";
                    }
                    ?>

                    <!-- Formulario de comentario -->
                    <form method="POST" action="comentar.php" enctype="multipart/form-data" class="mt-2">
                        <input type="hidden" name="publicacion_id" value="<?php echo $pub['id']; ?>">
                        <textarea name="comentario" rows="2" cols="50" class="form-control mb-2" placeholder="Escribe un comentario..." required></textarea>
                        <input type="file" name="imagen" accept="image/*" class="form-control mb-2">
                        <input type="submit" value="Comentar" class="btn btn-success btn-sm">
                    </form>
                </div>
            </div>
        </div>
    <?php endwhile; ?>

</div>
<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
