<?php
session_start();
$conexion = new mysqli("sql107.infinityfree.com", "if0_38993730", "N2H2vWw7E", "if0_38993730_redsocial");
$conexion->query("SET time_zone = '-05:00'");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["comentario_id"])) {
    $id = intval($_POST["comentario_id"]);
    $usuario_id = $_SESSION["usuario_id"];

    // Obtener ruta de imagen
    $res = $conexion->query("SELECT imagen FROM comentarios WHERE id = $id AND usuario_id = $usuario_id");
    if ($res && $res->num_rows > 0) {
        $fila = $res->fetch_assoc();
        if (!empty($fila["imagen"])) {
            $ruta_imagen = "uploads/" . $fila["imagen"];
            if (file_exists($ruta_imagen)) {
                unlink($ruta_imagen);
            }
        }

        // Borrar comentario
        $conexion->query("DELETE FROM comentarios WHERE id = $id AND usuario_id = $usuario_id");
    }
}

header("Location: muro.php");
exit;
