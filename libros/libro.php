<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Libro</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .container {
            text-align: center;
            padding: 20px;
        }

        h1 {
            margin-bottom: 10px;
        }

        p {
            margin-bottom: 20px;
        }

        a {
            text-decoration: none;
            font-size: 18px;
            padding: 10px 20px;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: lightskyblue;
        }
    </style>
</head>
<body>
    <header>
        <h1>Detalles del Libro</h1>
    </header>
    <main>
        <div class="container">
            <?php
           
            include 'libros.php';
            
            
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
              
                foreach ($libros as $libro) {
                    if ($libro['id'] == $id) {
                        echo '<h1>' . $libro['titulo'] . '</h1>';
                        echo '<p><strong>Autor:</strong> ' . $libro['autor'] . '</p>';
                        echo '<p><strong>Ejemplares:</strong> ' . $libro['ejemplares'] . '</p>';
                        echo '<p><strong>Género:</strong> ' . $libro['genero'] . '</p>';
                        break;
                    }
                }
            } else {
                echo '<p>Libro no encontrado.</p>';
            }
            ?>
            <a href="menu.php">Volver al Menú de Libros</a>
        </div>
    </main>
</body>
</html>
