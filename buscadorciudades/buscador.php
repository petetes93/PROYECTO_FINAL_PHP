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
        $stmt = $pdo->query("SELECT c.Code, c.Name FROM country c ORDER BY c.Name");
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

function obtenerContinentes($pdo) {
    try {
        $stmt = $pdo->query("SELECT DISTINCT Continent FROM country ORDER BY Continent");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<h1>Error en la consulta:</h1> <p>" . $e->getMessage() . "</p>";
        exit;
    }
}

function obtenerPaisesPorContinente($pdo, $continente) {
    try {
        $stmt = $pdo->prepare("SELECT Code, Name FROM country WHERE Continent = :continente ORDER BY Name");
        $stmt->execute(['continente' => $continente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

function editarCiudad($pdo, $cityId, $nombre, $distrito, $poblacion) {
    try {
        $stmt = $pdo->prepare("UPDATE city SET Name = :nombre, District = :distrito, Population = :poblacion WHERE ID = :cityId");
        $stmt->execute(['nombre' => $nombre, 'distrito' => $distrito, 'poblacion' => $poblacion, 'cityId' => $cityId]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        echo "<h1>Error en la consulta:</h1> <p>" . $e->getMessage() . "</p>";
        exit;
    }
}

function obtenerLenguas($pdo, $countryCode) {
    try {
        $stmt = $pdo->prepare("SELECT Language, IsOfficial FROM countrylanguage WHERE CountryCode = :countryCode ORDER BY IsOfficial DESC, Language");
        $stmt->execute(['countryCode' => $countryCode]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<h1>Error en la consulta:</h1> <p>" . $e->getMessage() . "</p>";
        exit;
    }
}

$pdo = conectarBaseDatos();

$paises = obtenerPaises($pdo);

$vista = isset($_GET['vista']) ? $_GET['vista'] : 'ciudades';
$countryCode = isset($_GET['country']) ? $_GET['country'] : (count($paises) ? $paises[0]['Code'] : 'ESP');

if ($vista == 'ciudades') {
    $paisSeleccionado = obtenerNombrePais($pdo, $countryCode);
    $ciudades = obtenerCiudades($pdo, $countryCode);
} elseif ($vista == 'continentes') {
    $continentes = obtenerContinentes($pdo);
    $selectedContinent = isset($_GET['continente']) ? $_GET['continente'] : $continentes[0]['Continent'];
    $paisesContinente = obtenerPaisesPorContinente($pdo, $selectedContinent);
} elseif ($vista == 'lenguas') {
    $paisSeleccionado = obtenerNombrePais($pdo, $countryCode);
    $lenguas = obtenerLenguas($pdo, $countryCode);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrar_ciudad'])) {
    $cityId = $_POST['city_id'];
    borrarCiudad($pdo, $cityId);
    header("Location: buscador.php?vista=ciudades&country={$countryCode}");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_ciudad'])) {
    $nombre = $_POST['nombre'];
    $countryCode = $_POST['countryCode'];
    $distrito = $_POST['distrito'];
    $poblacion = $_POST['poblacion'];
    agregarCiudad($pdo, $nombre, $countryCode, $distrito, $poblacion);
    header("Location: buscador.php?vista=ciudades&country={$countryCode}");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_ciudad'])) {
    $cityId = $_POST['city_id'];
    $nombre = $_POST['nombre'];
    $distrito = $_POST['distrito'];
    $poblacion = $_POST['poblacion'];
    editarCiudad($pdo, $cityId, $nombre, $distrito, $poblacion);
    header("Location: buscador.php?vista=ciudades&country={$countryCode}");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscador de Datos</title>
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
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        input[type="text"], select {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            background-color: #007BFF;
            color: white;
            padding: 10px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .boton-volver {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #007BFF;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .boton-volver:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Buscador de Datos</h1>
    <form action="buscador.php" method="get">
        <label for="vista">Vista:</label>
        <select name="vista" id="vista">
            <option value="ciudades" <?php echo $vista == 'ciudades' ? 'selected' : ''; ?>>Ciudades</option>
            <option value="continentes" <?php echo $vista == 'continentes' ? 'selected' : ''; ?>>Continentes</option>
            <option value="lenguas" <?php echo $vista == 'lenguas' ? 'selected' : ''; ?>>Lenguas</option>
        </select>
        <input type="submit" value="Cambiar Vista">
    </form>
    <?php if ($vista == 'ciudades'): ?>
        <h2>Ciudades de <?php echo htmlspecialchars($paisSeleccionado); ?></h2>
        <form action="buscador.php" method="get">
            <input type="hidden" name="vista" value="ciudades">
            <label for="country">País:</label>
            <select name="country" id="country">
                <?php foreach ($paises as $pais): ?>
                    <option value="<?php echo $pais['Code']; ?>" <?php echo $pais['Code'] == $countryCode ? 'selected' : ''; ?>><?php echo htmlspecialchars($pais['Name']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="Mostrar Ciudades">
        </form>
        <?php if (isset($ciudades)): ?>
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Distrito</th>
                    <th>Población</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($ciudades as $ciudad): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ciudad['ID']); ?></td>
                        <td><?php echo htmlspecialchars($ciudad['Name']); ?></td>
                        <td><?php echo htmlspecialchars($ciudad['District']); ?></td>
                        <td><?php echo htmlspecialchars($ciudad['Population']); ?></td>
                        <td>
                            <form action="buscador.php" method="post" style="display:inline;">
                                <input type="hidden" name="city_id" value="<?php echo $ciudad['ID']; ?>">
                                <input type="hidden" name="countryCode" value="<?php echo $countryCode; ?>">
                                <input type="submit" name="borrar_ciudad" value="Borrar">
                            </form>
                            <form action="editar_ciudad.php" method="get" style="display:inline;">
                                <input type="hidden" name="city_id" value="<?php echo $ciudad['ID']; ?>">
                                <input type="hidden" name="countryCode" value="<?php echo $countryCode; ?>">
                                <input type="submit" value="Editar">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Agregar Nueva Ciudad</h3>
            <form action="buscador.php" method="post">
                <input type="hidden" name="countryCode" value="<?php echo $countryCode; ?>">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" required>
                <label for="distrito">Distrito:</label>
                <input type="text" name="distrito" required>
                <label for="poblacion">Población:</label>
                <input type="text" name="poblacion" required>
                <input type="submit" name="agregar_ciudad" value="Agregar Ciudad">
            </form>
        <?php endif; ?>
    <?php elseif ($vista == 'continentes'): ?>
        <h2>Continentes y Países</h2>
        <form action="buscador.php" method="get">
            <input type="hidden" name="vista" value="continentes">
            <label for="continente">Continente:</label>
            <select name="continente" id="continente">
                <?php foreach ($continentes as $continente): ?>
                    <option value="<?php echo htmlspecialchars($continente['Continent']); ?>" <?php echo $continente['Continent'] == $selectedContinent ? 'selected' : ''; ?>><?php echo htmlspecialchars($continente['Continent']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="Mostrar Países">
        </form>
        <?php if (isset($paisesContinente)): ?>
            <table>
                <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($paisesContinente as $pais): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pais['Code']); ?></td>
                        <td><?php echo htmlspecialchars($pais['Name']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php elseif ($vista == 'lenguas'): ?>
        <h2>Lenguas de <?php echo htmlspecialchars($paisSeleccionado); ?></h2>
        <form action="buscador.php" method="get">
            <input type="hidden" name="vista" value="lenguas">
            <label for="country">País:</label>
            <select name="country" id="country">
                <?php foreach ($paises as $pais): ?>
                    <option value="<?php echo $pais['Code']; ?>" <?php echo $pais['Code'] == $countryCode ? 'selected' : ''; ?>><?php echo htmlspecialchars($pais['Name']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="Mostrar Lenguas">
        </form>
        <?php if (isset($lenguas)): ?>
    <table>
        <thead>
            <tr>
                <th>Lengua</th>
                <th>Oficial</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lenguas as $lengua): ?>
                <tr>
                    <td><?php echo htmlspecialchars($lengua['Language']); ?></td>
                    <td><?php echo ($lengua['IsOfficial'] == 'T') ? 'Sí' : 'No'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

    <?php endif; ?>
    <a href="../home.php" class="boton-volver">Volver al home</a>
</div>
</body>
</html>
