<?php
session_start();
date_default_timezone_set('America/Lima');
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}
?>

<h2>Bienvenido, <?php echo $_SESSION["nombre"]; ?>!</h2>
<p>Has iniciado sesión correctamente.</p>
<a href="muro.php">Ir al muro</a><br>
<a href="logout.php">Cerrar sesión</a>