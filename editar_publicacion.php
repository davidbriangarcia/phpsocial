<?php
session_start();
date_default_timezone_set('America/Lima');
$conexion = new mysqli("sql107.infinityfree.com", "if0_38993730", "N2H2vWw7E", "if0_38993730_redsocial");
$conexion->query("SET time_zone = '-05:00'");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["publicacion_id"])) {
    $id = intval($_POST["publicacion_id"]);

    // Si se recibió nueva edición
    if (isset($_POST["nuevo_contenido"])) {
        $contenido = $conexion->real_escape_string($_POST["nuevo_contenido"]);
        $usuario_id = $_SESSION["usuario_id"];
        $set_imagen = "";

        // Procesar nueva imagen si se subió
        if (isset($_FILES["nueva_imagen"]) && $_FILES["nueva_imagen"]["error"] == 0) {
            $ext = pathinfo($_FILES["nueva_imagen"]["name"], PATHINFO_EXTENSION);
            $nuevo_nombre = uniqid("img_") . "." . $ext;
            $ruta_destino = "uploads/" . $nuevo_nombre;
            move_uploaded_file($_FILES["nueva_imagen"]["tmp_name"], $ruta_destino);
            $set_imagen = ", imagen = '$nuevo_nombre'";
        }

        $conexion->query("UPDATE publicaciones SET contenido = '$contenido' $set_imagen WHERE id = $id AND usuario_id = $usuario_id");
        header("Location: muro.php");
        exit;
    }

    // Obtener contenido actual
    $res = $conexion->query("SELECT * FROM publicaciones WHERE id = $id AND usuario_id = " . $_SESSION["usuario_id"]);
    $pub = $res->fetch_assoc();
} else {
    header("Location: muro.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["publicacion_id"], $_POST["eliminar_imagen"])) {
    $id = intval($_POST["publicacion_id"]);
    $usuario_id = $_SESSION["usuario_id"];
    // Obtener imagen actual
    $res = $conexion->query("SELECT imagen FROM publicaciones WHERE id = $id AND usuario_id = $usuario_id");
    if ($res && $res->num_rows > 0) {
        $fila = $res->fetch_assoc();
        if (!empty($fila["imagen"])) {
            $ruta = "uploads/" . $fila["imagen"];
            if (file_exists($ruta)) {
                unlink($ruta);
            }
            $conexion->query("UPDATE publicaciones SET imagen = NULL WHERE id = $id AND usuario_id = $usuario_id");
        }
    }
    // Recargar para ver el cambio
    header("Location: editar_publicacion.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Publicación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <h2 class="card-title mb-4">Editar Publicación</h2>

            <?php if (!empty($pub["imagen"])): ?>
                <p>Imagen actual:</p>
                <img src="uploads/<?php echo htmlspecialchars($pub["imagen"]); ?>" class="img-fluid mb-2" style="max-width: 200px;"><br>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="publicacion_id" value="<?php echo $pub["id"]; ?>">
                    <input type="hidden" name="eliminar_imagen" value="1">
                    <input type="submit" value="Eliminar solo imagen" class="btn btn-danger btn-sm mb-3">
                </form>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="nuevo_contenido" class="form-label">Contenido:</label>
                    <textarea id="nuevo_contenido" name="nuevo_contenido" rows="4" class="form-control" required><?php echo htmlspecialchars($pub["contenido"]); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="nueva_imagen" class="form-label">Reemplazar imagen:</label>
                    <input type="file" id="nueva_imagen" name="nueva_imagen" accept="image/*" class="form-control">
                </div>
                <input type="hidden" name="publicacion_id" value="<?php echo $pub["id"]; ?>">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <a href="muro.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
