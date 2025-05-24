<?php
session_start();
date_default_timezone_set('America/Lima');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conexion = new mysqli("sql107.infinityfree.com", "if0_38993730", "N2H2vWw7E", "if0_38993730_redsocial");
    $conexion->query("SET time_zone = '-05:00'");

    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    $email = $conexion->real_escape_string($_POST["email"]);
    $password = $_POST["password"];

    $sql = "SELECT * FROM usuarios WHERE email='$email'";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows == 1) {
        $usuario = $resultado->fetch_assoc();
        if (password_verify($password, $usuario["password"])) {
            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["nombre"] = $usuario["nombre"];
            header("Location: muro.php");
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }

    $conexion->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #b3e0ff;
        }
    </style>
</head>
<body>
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow" style="min-width: 350px;">
        <div class="card-body">
            <h2 class="card-title text-center mb-4">Iniciar Sesión</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña:</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
            </form>
            <div class="mt-3 text-center">
                <p>¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a></p>
                <p><a href="index.php">Volver al inicio</a></p>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>