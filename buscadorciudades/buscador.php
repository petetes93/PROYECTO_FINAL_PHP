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

function obtenerPaises($pdo) {
    try {
        $stmt = $pdo->query("SELECT c.Code, c.Name, GROUP_CONCAT(l.Language SEPARATOR ', ') AS Languages
                             FROM country c
                             LEFT JOIN countrylanguage l ON c.Code = l.CountryCode AND l.IsOfficial = 'T'
                             GROUP BY c.Code, c.Name
                             ORDER BY c.Name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<h1>Error en la consulta:</h1> <p>" . $e->getMessage() . "</p>";
        exit;
    }
}

function obtenerCiudades($pdo, $countryCode) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM city WHERE CountryCode = :countryCode");
        $stmt->execute(['countryCode' => $countryCode]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<h1>Error en la consulta:</h1> <p>" . $e->getMessage() . "</p>";
        exit;
    }
}

function obtenerNombrePais($pdo, $countryCode) {
    try {
        $stmt = $pdo->prepare("SELECT Name FROM country WHERE Code = :countryCode");
        $stmt->execute(['countryCode' => $countryCode]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['Name'];
    } catch (PDOException $e) {
        echo "<h1>Error en la consulta:</h1> <p>" . $e->getMessage() . "</p>";
        exit;
    }
}

function borrarCiudad($pdo, $cityId) {
    try {
        $stmt = $pdo->prepare("DELETE FROM city WHERE ID = :cityId");
        $stmt->execute(['cityId' => $cityId]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        echo "<h1>Error en la consulta:</h1> <p>" . $e->getMessage() . "</p>";
        exit;
    }
}

function agregarCiudad($pdo, $nombre, $countryCode, $distrito, $poblacion) {
    try {
        $stmt = $pdo->prepare("INSERT INTO city (Name, CountryCode, District, Population) VALUES (:nombre, :countryCode, :distrito, :poblacion)");
        $stmt->execute(['nombre' => $nombre, 'countryCode' => $countryCode, 'distrito' => $distrito, 'poblacion' => $poblacion]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        echo "<h1>Error en la consulta:</h1> <p>" . $e->getMessage() . "</p>";
        exit;
    }
}

$pdo = conectarBaseDatos();

$paises = obtenerPaises($pdo);

if (isset($_GET['country'])) {
    $countryCode = $_GET['country'];
} else {
    if (!empty($paises)) {
        $countryCode = $paises[0]['Code'];
    } else {
        $countryCode = 'ESP'; 
    }
}

$paisSeleccionado = obtenerNombrePais($pdo, $countryCode);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrar_ciudad'])) {
    $cityId = $_POST['city_id'];
    borrarCiudad($pdo, $cityId);
    header("Location: buscador.php?country={$countryCode}");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_ciudad'])) {
    $nombre = $_POST['nombre'];
    $countryCode = $_POST['countryCode'];
    $distrito = $_POST['distrito'];
    $poblacion = $_POST['poblacion'];
    agregarCiudad($pdo, $nombre, $countryCode, $distrito, $poblacion);
    header("Location: buscador.php?country={$countryCode}");
    exit;
}

$ciudades = obtenerCiudades($pdo, $countryCode);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ciudades de <?php echo htmlspecialchars($paisSeleccionado) ?></title>
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
            position: relative; 
        }
        h1 {
            text-align: left;
            color: #007BFF;
        }
        h2 {
            color: #007BFF;
        }
        form {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        select {
            padding: 8px;
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
            margin-top: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .acciones button {
            background-color: red;
        }
        .acciones button:hover {
            background-color: darkred;
        }
        .acciones a {
            display: inline-block;
            padding: 8px 12px;
            background-color: orange;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
        }
        .acciones a:hover {
            background-color: darkorange;
        }
        .bottom-right {
            position: absolute;
            bottom: 20px; 
            right: 20px; 
        }
        .bottom-right a {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .bottom-right a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ciudades de <?php echo htmlspecialchars($paisSeleccionado) ?></h1>

        <form method="get" action="">
            <label for="country">Seleccionar país:</label>
            <select name="country" id="country" onchange="this.form.submit()">
                <?php foreach ($paises as $pais): ?>
                    <option value="<?php echo $pais['Code'] ?>" <?php echo ($pais['Code'] == $countryCode) ? 'selected' : '' ?>>
                        <?php echo htmlspecialchars($pais['Name']) ?> (<?php echo htmlspecialchars($pais['Languages']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if (!empty($ciudades)): ?>
            <h2>Listado de Ciudades</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Distrito</th>
                    <th>Población</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach ($ciudades as $ciudad): ?>
                    <tr>
                        <td><?php echo $ciudad['ID'] ?></td>
                        <td><?php echo $ciudad['Name'] ?></td>
                        <td><?php echo $ciudad['District'] ?></td>
                        <td><?php echo $ciudad['Population'] ?></td>
                        <td class="acciones">
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="city_id" value="<?php echo $ciudad['ID'] ?>">
                                <button type="submit" name="borrar_ciudad" onclick="return confirm('¿Está seguro de que desea eliminar esta ciudad?');">Borrar</button>
                            </form>
                            <a href="editar_ciudad.php?city_id=<?php echo $ciudad['ID'] ?>">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No se encontraron ciudades para este país.</p>
        <?php endif; ?>

        <h2>Agregar Nueva Ciudad</h2>
        <form method="post" action="">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
            <input type="hidden" id="countryCode" name="countryCode" value="<?php echo htmlspecialchars($countryCode) ?>">
            <label for="distrito">Distrito:</label>
            <input type="text" id="distrito" name="distrito" required>
            <label for="poblacion">Población:</label>
            <input type="number" id="poblacion" name="poblacion" required>
            <button type="submit" name="agregar_ciudad">Agregar Ciudad</button>
        </form>
        
        
        <div class="bottom-right">
            <a href="/home.php">Volver al Home</a>
        </div>
    </div>
</body>
</html>
