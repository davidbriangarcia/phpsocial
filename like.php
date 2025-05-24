<?php
session_start();
date_default_timezone_set('America/Lima');
$conexion = new mysqli("sql107.infinityfree.com", "if0_38993730", "N2H2vWw7E", "if0_38993730_redsocial");
$conexion->query("SET time_zone = '-05:00'");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["publicacion_id"])) {
    $usuario_id = $_SESSION["usuario_id"];
    $publicacion_id = intval($_POST["publicacion_id"]);

    if (isset($_POST["dar"])) {
        $conexion->query("INSERT IGNORE INTO likes (usuario_id, publicacion_id) VALUES ($usuario_id, $publicacion_id)");
    } elseif (isset($_POST["quitar"])) {
        $conexion->query("DELETE FROM likes WHERE usuario_id = $usuario_id AND publicacion_id = $publicacion_id");
    }
}

header("Location: muro.php");
exit;