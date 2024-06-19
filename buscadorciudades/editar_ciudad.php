<?php
$host = 'localhost';
$db = 'world';
$user = 'root';
$pass = 'root';

function conectarBaseDatos() {
    global $host, $db, $user, $pass;
    try {
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8";
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        echo "<h1>Error de conexión:</h1> <p>" . $e->getMessage() . "</p>";
        exit;
    }
}

function obtenerDetalleCiudad($pdo, $cityId) {
    try {
        $stmt = $pdo->prepare(
            "SELECT city.*, country.Name as CountryName
             FROM city
             JOIN country ON city.CountryCode = country.Code
             WHERE city.ID = :cityId"
        );
        $stmt->execute(['cityId' => $cityId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<h1>Error en la consulta:</h1> <p>" . $e->getMessage() . "</p>";
        exit;
    }
}

function actualizarCiudad($pdo, $cityId, $nombre, $distrito, $poblacion) {
    try {
        $stmt = $pdo->prepare(
            "UPDATE city SET Name = :nombre, District = :distrito, Population = :poblacion WHERE ID = :cityId"
        );
        $stmt->execute(['nombre' => $nombre, 'distrito' => $distrito, 'poblacion' => $poblacion, 'cityId' => $cityId]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        echo "<h1>Error en la consulta:</h1> <p>" . $e->getMessage() . "</p>";
        exit;
    }
}

$pdo = conectarBaseDatos();
$cityId = isset($_GET['city_id']) ? $_GET['city_id'] : null;

if (!$cityId) {
    echo "<h1>Error:</h1> <p>ID de ciudad no proporcionado.</p>";
    exit;
}

$ciudad = obtenerDetalleCiudad($pdo, $cityId);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_ciudad'])) {
    $nombre = $_POST['nombre'];
    $distrito = $_POST['distrito'];
    $poblacion = $_POST['poblacion'];
    actualizarCiudad($pdo, $cityId, $nombre, $distrito, $poblacion);
    header("Location: buscador.php?country={$ciudad['CountryCode']}");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Ciudad</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: left;
            color: #007BFF;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        input[type="text"], input[type="number"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 20px;
            font-size: 16px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
        }
        button:hover {
            background-color: #0056b3;
        }
        a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Ciudad</h1>
        <form method="post" action="">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($ciudad['Name']) ?>" required>
            <label for="distrito">Distrito:</label>
            <input type="text" id="distrito" name="distrito" value="<?= htmlspecialchars($ciudad['District']) ?>" required>
            <label for="poblacion">Población:</label>
            <input type="number" id="poblacion" name="poblacion" value="<?= htmlspecialchars($ciudad['Population']) ?>" required>
            <button type="submit" name="editar_ciudad">Guardar Cambios</button>
        </form>
        <a href="buscador.php?country=<?= $ciudad['CountryCode'] ?>">Volver a la lista de ciudades</a>
    </div>
</body>
</html>
