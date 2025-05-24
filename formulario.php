<!DOCTYPE html>
<html>
<head>
    <title>Formulario con MySQL</title>
</head>
<body>
    <h2>Formulario de Contacto</h2>
    <form method="POST" action="">
        <label>Nombre:</label>
        <input type="text" name="nombre" required><br><br>

        <label>Mensaje:</label><br>
        <textarea name="mensaje" rows="4" cols="40" required></textarea><br><br>

        <input type="submit" name="enviar" value="Enviar">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Conexión a la base de datos
        $conexion = new mysqli("sql107.infinityfree.com", "if0_38993730", "N2H2vWw7E", "if0_38993730_redsocial");
        $conexion->query("SET time_zone = '-05:00'");

        if ($conexion->connect_error) {
            die("Conexión fallida: " . $conexion->connect_error);
        }

        // Escapar los datos para evitar inyecciones
        $nombre = $conexion->real_escape_string($_POST["nombre"]);
        $mensaje = $conexion->real_escape_string($_POST["mensaje"]);

        // Insertar en la tabla
        $sql = "INSERT INTO mensajes (nombre, mensaje) VALUES ('$nombre', '$mensaje')";

        if ($conexion->query($sql) === TRUE) {
            echo "<p><strong>Mensaje guardado correctamente.</strong></p>";
        } else {
            echo "Error: " . $conexion->error;
        }

        $conexion->close();
    }
    ?>
</body>
</html>
