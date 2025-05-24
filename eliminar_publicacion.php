<?php
session_start();
date_default_timezone_set('America/Lima');
$conexion = new mysqli("sql107.infinityfree.com", "if0_38993730", "N2H2vWw7E", "if0_38993730_redsocial");
$conexion->query("SET time_zone = '-05:00'");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["publicacion_id"])) {
    $id = intval($_POST["publicacion_id"]);
    $usuario_id = $_SESSION["usuario_id"];

    // Obtener ruta de imagen
    $res = $conexion->query("SELECT imagen FROM publicaciones WHERE id = $id AND usuario_id = $usuario_id");
    if ($res && $res->num_rows > 0) {
        $fila = $res->fetch_assoc();
        if (!empty($fila["imagen"])) {
            $ruta_imagen = "uploads/" . $fila["imagen"];
            if (file_exists($ruta_imagen)) {
                unlink($ruta_imagen);
            }
        }

        // Borrar la publicación
        $conexion->query("DELETE FROM publicaciones WHERE id = $id AND usuario_id = $usuario_id");

        // Opcional: borrar también los comentarios asociados
        $conexion->query("DELETE FROM comentarios WHERE publicacion_id = $id");
        $conexion->query("DELETE FROM likes WHERE publicacion_id = $id");
    }
}

header("Location: muro.php");
exit;