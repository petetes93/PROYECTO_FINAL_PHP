<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libros por Género</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-top: 20px;
        }

        .container table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .container table th, .container table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .container table th {
            background-color: #f2f2f2;
        }

        .volver-home {
            text-decoration: none;
            font-size: 18px;
            padding: 10px 20px;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .volver-home:hover {
            background-color: lightskyblue;
        }
    </style>
</head>
<body>
    <header>
        <h1>Libros por Género</h1>
    </header>
    <main>
        <div class="container">
            <h2>Libros del género: <?php echo htmlspecialchars($_GET['genero']); ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Ejemplares</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Incluir archivo de datos de libros
                    include 'libros.php';
                    
                    foreach ($libros as $libro) {
                        if ($libro['genero'] === $_GET['genero']) {
                            echo '<tr>';
                            echo '<td>' . $libro['titulo'] . '</td>';
                            echo '<td>' . $libro['autor'] . '</td>';
                            echo '<td>' . $libro['ejemplares'] . '</td>';
                            echo '<td><a href="libro.php?id=' . $libro['id'] . '">Ver detalles</a></td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
            <a href="menu.php" class="volver-home">Volver al Menú Principal</a>
        </div>
    </main>
</body>
</html>
