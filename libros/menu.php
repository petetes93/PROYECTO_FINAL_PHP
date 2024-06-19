<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú de Libros</title>
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

       
        .genero-link {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
            cursor: pointer;
        }

        .genero-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>Menú de Libros</h1>
    </header>
    <main>
        <div class="container">
            <table>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Ejemplares</th>
                        <th>Género</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    
                    include 'libros.php';
                    
                    foreach ($libros as $libro) {
                        echo '<tr>';
                        echo '<td>' . $libro['titulo'] . '</td>';
                        echo '<td>' . $libro['autor'] . '</td>';
                        echo '<td>' . $libro['ejemplares'] . '</td>';
                        
                        echo '<td><a href="libros_por_genero.php?genero=' . urlencode($libro['genero']) . '" class="genero-link">' . $libro['genero'] . '</a></td>';
                        echo '<td><a href="libro.php?id=' . $libro['id'] . '">Ver detalles</a></td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
            <a href="../home.php" class="volver-home">Volver al Home</a>
        </div>
    </main>
</body>
</html>
