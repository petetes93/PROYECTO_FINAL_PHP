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

function agregarLengua($pdo, $countryCode, $lengua, $oficial) {
    try {
        $stmt = $pdo->prepare("INSERT INTO countrylanguage (CountryCode, Language, IsOfficial) VALUES (:countryCode, :lengua, :oficial)");
        $stmt->execute(['countryCode' => $countryCode, 'lengua' => $lengua, 'oficial' => $oficial]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        echo "<h1>Error en la consulta:</h1> <p>" . $e->getMessage() . "</p>";
        exit;
    }
}

function agregarPais($pdo, $codigo, $nombre, $continente) {
    try {
        $stmt = $pdo->prepare("INSERT INTO country (Code, Name, Continent) VALUES (:codigo, :nombre, :continente)");
        $stmt->execute(['codigo' => $codigo, 'nombre' => $nombre, 'continente' => $continente]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        echo "<h1>Error en la consulta:</h1> <p>" . $e->getMessage() . "</p>";
        exit;
    }
}

function editarPais($pdo, $codigo, $nombre, $continente) {
    try {
        $stmt = $pdo->prepare("UPDATE country SET Name = :nombre, Continent = :continente WHERE Code = :codigo");
        $stmt->execute(['nombre' => $nombre, 'continente' => $continente, 'codigo' => $codigo]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        echo "<h1>Error en la consulta:</h1> <p>" . $e->getMessage() . "</p>";
        exit;
    }
}

$pdo = conectarBaseDatos();
$paises = obtenerPaises($pdo);

$vista = isset($_GET['vista']) ? $_GET['vista'] : 'ciudades';
$countryCode = isset($_GET['country']) ? $_GET['country'] : (count($paises) ? $paises[0]['Code'] : 'ESP');

$ciudadParaEditar = null;
if ($vista == 'ciudades') {
    $paisSeleccionado = obtenerNombrePais($pdo, $countryCode);
    $ciudades = obtenerCiudades($pdo, $countryCode);

    if (isset($_POST['editar_ciudad_form'])) {
        $cityId = $_POST['city_id'];
        foreach ($ciudades as $ciudad) {
            if ($ciudad['ID'] == $cityId) {
                $ciudadParaEditar = $ciudad;
                break;
            }
        }
    }
} elseif ($vista == 'continentes') {
    $continentes = obtenerContinentes($pdo);
    $selectedContinent = isset($_GET['continente']) ? $_GET['continente'] : $continentes[0]['Continent'];
    $paisesContinente = obtenerPaisesPorContinente($pdo, $selectedContinent);
} elseif ($vista == 'lenguas') {
    $paisSeleccionado = obtenerNombrePais($pdo, $countryCode);
    $lenguas = obtenerLenguas($pdo, $countryCode);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['borrar_ciudad'])) {
        $cityId = $_POST['city_id'];
        borrarCiudad($pdo, $cityId);
        header("Location: buscador.php?vista=ciudades&country={$countryCode}");
        exit;
    }

    if (isset($_POST['agregar_ciudad'])) {
        $nombre = $_POST['nombre'];
        $distrito = $_POST['distrito'];
        $poblacion = $_POST['poblacion'];
        agregarCiudad($pdo, $nombre, $countryCode, $distrito, $poblacion);
        header("Location: buscador.php?vista=ciudades&country={$countryCode}");
        exit;
    }

    if (isset($_POST['editar_ciudad'])) {
        $cityId = $_POST['city_id'];
        $nombre = $_POST['nombre'];
        $distrito = $_POST['distrito'];
        $poblacion = $_POST['poblacion'];
        editarCiudad($pdo, $cityId, $nombre, $distrito, $poblacion);
        header("Location: buscador.php?vista=ciudades&country={$countryCode}");
        exit;
    }

    if (isset($_POST['agregar_lengua'])) {
        $lengua = $_POST['lengua'];
        $oficial = $_POST['oficial'] == 'SI' ? 'T' : 'F';
        agregarLengua($pdo, $countryCode, $lengua, $oficial);
        header("Location: buscador.php?vista=lenguas&country={$countryCode}");
        exit;
    }

    if (isset($_POST['agregar_pais'])) {
        $codigo = $_POST['codigo'];
        $nombre = $_POST['nombre'];
        $continente = $_POST['continente'];
        agregarPais($pdo, $codigo, $nombre, $continente);
        header("Location: buscador.php?vista=continentes&continente={$continente}");
        exit;
    }

    if (isset($_POST['editar_pais'])) {
        $codigo = $_POST['codigo'];
        $nombre = $_POST['nombre'];
        $continente = $_POST['continente'];
        editarPais($pdo, $codigo, $nombre, $continente);
        header("Location: buscador.php?vista=continentes&continente={$continente}");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscador de Ciudades, Continentes y Lenguas</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        nav {
            background-color: #333;
            color: #fff;
            padding: 1em;
            text-align: center;
        }
        nav a {
            color: #fff;
            margin: 0 1em;
            text-decoration: none;
        }
        h1, h2, h3 {
            color: #333;
            text-align: center;
        }
        form {
            max-width: 600px;
            margin: 2em auto;
            padding: 1em;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        label {
            display: block;
            margin-bottom: .5em;
        }
        input, select {
            width: 100%;
            padding: .5em;
            margin-bottom: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            display: inline-block;
            padding: .5em 1em;
            color: #fff;
            background-color: #333;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #555;
        }
        ul {
            list-style: none;
            padding: 0;
            max-width: 600px;
            margin: 2em auto;
        }
        li {
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 1em;
            margin-bottom: .5em;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        li form {
            display: inline;
        }
    </style>
</head>
<body>
    <nav>
        <a href="../home.php">Home</a>
        <a href="buscador.php?vista=ciudades&country=<?= $countryCode ?>">Ciudades</a>
        <a href="buscador.php?vista=continentes">Continentes</a>
        <a href="buscador.php?vista=lenguas&country=<?= $countryCode ?>">Lenguas</a>
    </nav>
    <h1>Buscador de Ciudades, Continentes y Lenguas</h1>
    <?php if ($vista == 'ciudades'): ?>
        <h2>Ciudades de <?= $paisSeleccionado ?></h2>
        <form method="GET" action="buscador.php">
            <input type="hidden" name="vista" value="ciudades">
            <label for="country">Selecciona un país:</label>
            <select name="country" id="country" onchange="this.form.submit()">
                <?php foreach ($paises as $pais): ?>
                    <option value="<?= $pais['Code'] ?>" <?= $pais['Code'] == $countryCode ? 'selected' : '' ?>><?= $pais['Name'] ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <ul>
            <?php foreach ($ciudades as $ciudad): ?>
                <li>
                    <?= $ciudad['Name'] ?> (<?= $ciudad['District'] ?>) - <?= $ciudad['Population'] ?>
                    <form method="POST" action="buscador.php?vista=ciudades&country=<?= $countryCode ?>" style="display:inline;">
                        <input type="hidden" name="city_id" value="<?= $ciudad['ID'] ?>">
                        <button type="submit" name="borrar_ciudad">Borrar</button>
                    </form>
                    <form method="POST" action="buscador.php?vista=ciudades&country=<?= $countryCode ?>" style="display:inline;">
                        <input type="hidden" name="city_id" value="<?= $ciudad['ID'] ?>">
                        <button type="submit" name="editar_ciudad_form">Editar</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($ciudadParaEditar): ?>
            <h3>Editar Ciudad</h3>
            <form method="POST" action="buscador.php?vista=ciudades&country=<?= $countryCode ?>">
                <input type="hidden" name="city_id" value="<?= $ciudadParaEditar['ID'] ?>">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" value="<?= $ciudadParaEditar['Name'] ?>" required>
                <label for="distrito">Distrito:</label>
                <input type="text" name="distrito" id="distrito" value="<?= $ciudadParaEditar['District'] ?>" required>
                <label for="poblacion">Población:</label>
                <input type="number" name="poblacion" id="poblacion" value="<?= $ciudadParaEditar['Population'] ?>" required>
                <button type="submit" name="editar_ciudad">Guardar Cambios</button>
            </form>
        <?php else: ?>
            <h3>Agregar Ciudad</h3>
            <form method="POST" action="buscador.php?vista=ciudades&country=<?= $countryCode ?>">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" required>
                <label for="distrito">Distrito:</label>
                <input type="text" name="distrito" id="distrito" required>
                <label for="poblacion">Población:</label>
                <input type="number" name="poblacion" id="poblacion" required>
                <button type="submit" name="agregar_ciudad">Agregar Ciudad</button>
            </form>
        <?php endif; ?>
    <?php elseif ($vista == 'continentes'): ?>
        <h2>Continentes y Países</h2>
        <form method="GET" action="buscador.php">
            <input type="hidden" name="vista" value="continentes">
            <label for="continente">Selecciona un continente:</label>
            <select name="continente" id="continente" onchange="this.form.submit()">
                <?php foreach ($continentes as $continente): ?>
                    <option value="<?= $continente['Continent'] ?>" <?= $continente['Continent'] == $selectedContinent ? 'selected' : '' ?>><?= $continente['Continent'] ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <ul>
            <?php foreach ($paisesContinente as $pais): ?>
                <li>
                    <?= $pais['Name'] ?> (<?= $pais['Code'] ?>)
                </li>
            <?php endforeach; ?>
        </ul>
    <?php elseif ($vista == 'lenguas'): ?>
        <h2>Lenguas de <?= $paisSeleccionado ?></h2>
        <form method="GET" action="buscador.php">
            <input type="hidden" name="vista" value="lenguas">
            <label for="country">Selecciona un país:</label>
            <select name="country" id="country" onchange="this.form.submit()">
                <?php foreach ($paises as $pais): ?>
                    <option value="<?= $pais['Code'] ?>" <?= $pais['Code'] == $countryCode ? 'selected' : '' ?>><?= $pais['Name'] ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <ul>
            <?php foreach ($lenguas as $lengua): ?>
                <li>
                    <?= $lengua['Language'] ?> (<?= $lengua['IsOfficial'] == 'T' ? 'Oficial' : 'No Oficial' ?>)
                </li>
            <?php endforeach; ?>
        </ul>
        <h3>Agregar Lengua</h3>
        <form method="POST" action="buscador.php?vista=lenguas&country=<?= $countryCode ?>">
            <label for="lengua">Lengua:</label>
            <input type="text" name="lengua" id="lengua" required>
            <label for="oficial">¿Es oficial?</label>
            <select name="oficial" id="oficial">
                <option value="SI">Sí</option>
                <option value="NO">No</option>
            </select>
            <button type="submit" name="agregar_lengua">Agregar Lengua</button>
        </form>
    <?php endif; ?>

    <h2>Agregar País</h2>
    <form method="POST" action="buscador.php?vista=continentes">
        <label for="codigo">Código:</label>
        <input type="text" name="codigo" id="codigo" required>
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" required>
        <label for="continente">Continente:</label>
        <select name="continente" id="continente">
            <?php foreach ($continentes as $continente): ?>
                <option value="<?= $continente['Continent'] ?>"><?= $continente['Continent'] ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="agregar_pais">Agregar País</button>
    </form>
</body>
</html>
