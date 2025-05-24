<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conexion = new mysqli("sql107.infinityfree.com", "if0_38993730", "N2H2vWw7E", "if0_38993730_redsocial");
    $conexion->query("SET time_zone = '-05:00'");

    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    $nombre = $conexion->real_escape_string($_POST["nombre"]);
    $email = $conexion->real_escape_string($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre, email, password) VALUES ('$nombre', '$email', '$password')";

    if ($conexion->query($sql) === TRUE) {
        $mensaje = "Usuario registrado correctamente. <a href='login.php' class='alert-link'>Iniciar sesión</a>";
    } else {
        $mensaje = "Error: " . $conexion->error;
    }

    $conexion->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
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
            <h2 class="card-title text-center mb-4">Registro de Usuario</h2>
            <?php if (isset($mensaje)): ?>
                <div class="alert <?php echo strpos($mensaje, 'Error') === false ? 'alert-success' : 'alert-danger'; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña:</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Registrarse</button>
            </form>
            <div class="mt-3 text-center">
                <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
                <p><a href="index.php">Volver al inicio</a></p>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>