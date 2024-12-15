<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "formulario";

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Error de conexión. Vuelva a intentar: " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) !== TRUE) {
    die("Error al crear la base de datos: " . $conn->error);
}

$conn->select_db($dbname);

$sql = "CREATE TABLE IF NOT EXISTS viviendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    correo VARCHAR(255) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    educacion VARCHAR(3) NOT NULL
)";
if ($conn->query($sql) !== TRUE) {
    die("Error al crear la tabla: " . $conn->error);
}

$nombre = $correo = $direccion = $educacion = "";
$nombreError = $correoError = $direccionError = $educacionError = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $direccion = trim($_POST['direccion']);
    $educacion = trim($_POST['educacion']);

    if (empty($nombre)) {
        $nombreError = "El nombre es obligatorio.";
    }
    if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $correoError = "El correo no es válido.";
    }
    if (empty($direccion)) {
        $direccionError = "La dirección es obligatoria.";
    }
    if (empty($educacion)) {
        $educacionError = "La trayectoria educativa es obligatoria.";
    } else if (!in_array($educacion, ['A', 'PI', 'PC', 'SI', 'SC', 'UI', 'UC'])) {
        $educacionError = "Valor inválido para trayectoria educativa.";
    }

    if (empty($nombreError) && empty($correoError) && empty($direccionError) && empty($educacionError)) {
        $stmt = $conn->prepare("INSERT INTO viviendas (nombre, correo, direccion, educacion) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $correo, $direccion, $educacion);

        if ($stmt->execute()) {
            echo "<p class='success'>Datos guardados correctamente.</p>";
        } else {
            echo "<p class='error'>Error al guardar los datos: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RELEVAMIENTO BARRIAL - GOBIERNO POPULAR</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(243, 218, 106);
            padding: 20px;
        }

        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: 0 auto;
        }

        h2 {
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            font-size: 0.9em;
        }

        .success {
            color: green;
            font-size: 1.1em;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Formulario de Contacto</h2>

        <form method="POST" action="">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>">
            <span class="error"><?php echo $nombreError; ?></span>

            <label for="correo">Correo Electrónico:</label>
            <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($correo); ?>">
            <span class="error"><?php echo $correoError; ?></span>

            <label for="direccion">Dirección de la vivienda:</label>
            <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($direccion); ?>">
            <span class="error"><?php echo $direccionError; ?></span>

            <label for="educacion">Trayectoria educativa (A, PI, PC, SI, SC, UI, UC):</label>
            <input type="text" id="educacion" name="educacion" value="<?php echo htmlspecialchars($educacion); ?>">
            <span class="error"><?php echo $educacionError; ?></span>

            <input type="submit" value="Enviar">
        </form>
    </div>

</body>
</html>
