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
            padding: 5px;
            font-size: 16px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin: 5px 0 10px 0;
            border: 1px solid #ddd;
            box-sizing: border-box;
        }
        button {
            padding: 10px 15px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        nav {
            background-color: #007BFF;
            padding: 10px 20px;
            text-align: center;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
            font-size: 18px;
        }
        nav a:hover {
            text-decoration: underline;
        }
        .acciones form {
            display: inline;
        }
    </style>
</head>
<body>
    <nav>
        <a href="?vista=ciudades">Ciudades</a>
        <a href="?vista=continentes">Continentes</a>
        <a href="?vista=lenguas">Lenguas</a>
    </nav>
    <div class="container">
        <?php if ($vista == 'ciudades'): ?>
            <h1>Ciudades de <?php echo htmlspecialchars($paisSeleccionado); ?></h1>
            <form method="GET" action="">
                <input type="hidden" name="vista" value="ciudades">
                <label for="country">Seleccionar país:</label>
                <select id="country" name="country" onchange="this.form.submit()">
                    <?php foreach ($paises as $pais): ?>
                        <option value="<?php echo htmlspecialchars($pais['Code']); ?>" <?php if ($countryCode == $pais['Code']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($pais['Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
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
                            <td class="acciones">
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="city_id" value="<?php echo htmlspecialchars($ciudad['ID']); ?>">
                                    <button type="submit" name="borrar_ciudad">Eliminar</button>
                                </form>
                                <button onclick="document.getElementById('editar_<?php echo htmlspecialchars($ciudad['ID']); ?>').style.display='block'">Editar</button>
                                <div id="editar_<?php echo htmlspecialchars($ciudad['ID']); ?>" style="display:none;">
                                    <form method="POST">
                                        <input type="hidden" name="city_id" value="<?php echo htmlspecialchars($ciudad['ID']); ?>">
                                        <label for="nombre">Nombre:</label><br>
                                        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($ciudad['Name']); ?>" required><br>
                                        <label for="distrito">Distrito:</label><br>
                                        <input type="text" id="distrito" name="distrito" value="<?php echo htmlspecialchars($ciudad['District']); ?>" required><br>
                                        <label for="poblacion">Población:</label><br>
                                        <input type="number" id="poblacion" name="poblacion" value="<?php echo htmlspecialchars($ciudad['Population']); ?>" required><br>
                                        <button type="submit" name="editar_ciudad">Guardar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h2>Agregar Ciudad</h2>
            <form method="POST">
                <label for="nombre">Nombre:</label><br>
                <input type="text" id="nombre" name="nombre" required><br>
                <input type="hidden" name="countryCode" value="<?php echo htmlspecialchars($countryCode); ?>">
                <label for="distrito">Distrito:</label><br>
                <input type="text" id="distrito" name="distrito" required><br>
                <label for="poblacion">Población:</label><br>
                <input type="number" id="poblacion" name="poblacion" required><br>
                <button type="submit" name="agregar_ciudad">Agregar</button>
            </form>
        <?php elseif ($vista == 'continentes'): ?>
            <h1>Continentes y Países</h1>
            <form method="GET" action="">
                <input type="hidden" name="vista" value="continentes">
                <label for="continente">Seleccionar continente:</label>
                <select id="continente" name="continente" onchange="this.form.submit()">
                    <?php foreach ($continentes as $continente): ?>
                        <option value="<?php echo htmlspecialchars($continente['Continent']); ?>" <?php if ($selectedContinent == $continente['Continent']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($continente['Continent']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
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
        <?php elseif ($vista == 'lenguas'): ?>
            <h1>Lenguas de <?php echo htmlspecialchars($paisSeleccionado); ?></h1>
            <form method="GET" action="">
                <input type="hidden" name="vista" value="lenguas">
                <label for="country">Seleccionar país:</label>
                <select id="country" name="country" onchange="this.form.submit()">
                    <?php foreach ($paises as $pais): ?>
                        <option value="<?php echo htmlspecialchars($pais['Code']); ?>" <?php if ($countryCode == $pais['Code']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($pais['Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>Lengua</th>
                        <th>Es Oficial</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lenguas as $lengua): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($lengua['Language']); ?></td>
                            <td><?php echo $lengua['IsOfficial'] === 'T' ? 'Sí' : 'No'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
