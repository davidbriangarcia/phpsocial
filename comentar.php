<?php
session_start();
date_default_timezone_set('America/Lima');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$conexion = new mysqli("sql107.infinityfree.com", "if0_38993730", "N2H2vWw7E", "if0_38993730_redsocial");
$conexion->query("SET time_zone = '-05:00'");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["publicacion_id"], $_POST["comentario"])) {
    $publicacion_id = intval($_POST["publicacion_id"]);
    $usuario_id = $_SESSION["usuario_id"];
    $contenido = $conexion->real_escape_string($_POST["comentario"]);
    $imagen_nombre = null;

    // Procesar imagen si se subió
    if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
        $ext = pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
        $imagen_nombre = uniqid("comimg_") . "." . $ext;
        $ruta_destino = "uploads/" . $imagen_nombre;
        if (!is_dir("uploads")) {
            mkdir("uploads", 0777, true);
        }
        move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta_destino);
    }

    if ($imagen_nombre) {
        $conexion->query("INSERT INTO comentarios (publicacion_id, usuario_id, contenido, imagen) VALUES ($publicacion_id, $usuario_id, '$contenido', '$imagen_nombre')");
    } else {
        $conexion->query("INSERT INTO comentarios (publicacion_id, usuario_id, contenido) VALUES ($publicacion_id, $usuario_id, '$contenido')");
    }
}

header("Location: muro.php");
exit;