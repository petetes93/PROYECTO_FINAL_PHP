<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .img-center {
            display: block;
            margin: 0 auto;
        }
        .proverbio-card {
            margin-top: 20px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #f8f9fa;
        }
        .proverbio-card h5 {
            font-size: 18px;
            color: #333;
        }
        .proverbio-card p {
            font-style: italic;
            margin-top: 10px;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
            text-align: center;
        }
        nav ul li {
            display: inline-block;
            margin: 10px;
        }
        nav ul li a {
            text-decoration: none;
            font-size: 20px;
            color: black;
            padding: 10px 20px;
            border-radius: 5px;
            background-color: lightblue;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }
        nav ul li a:hover {
            background-color: lightcoral;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <h1>Bienvenido a Nuestro Sitio Web</h1>
    </header>
    <main>
        <div class="container text-center">
            <img src="https://picsum.photos/200/300" alt="Imagen de bienvenida" class="img-fluid rounded shadow-sm mb-3 img-center">
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Et sint repellendus nulla veniam commodi consequatur ad optio, aperiam modi mollitia voluptates ratione deserunt temporibus quidem esse cupiditate eos, distinctio veritatis! Eum, tempore. Perspiciatis exercitationem consequuntur accusantium ea aliquam pariatur explicabo optio debitis fugiat vitae quo laborum esse commodi voluptate blanditiis aut suscipit tempora quisquam quam, cumque, inventore accusamus. Suscipit, at? Maiores quod, totam magni, quia dolores cupiditate et rerum voluptate officiis possimus accusantium sapiente minus adipisci sit ab eos tenetur quis alias eligendi hic, deleniti facere labore non quae? Necessitatibus. Accusantium quod ducimus assumenda maxime! Labore consequuntur blanditiis possimus corporis iste odit incidunt consectetur iusto quisquam quas odio esse expedita, aliquam autem. Vitae deleniti, modi praesentium delectus veniam iure cupiditate! Numquam facere totam quas accusantium, sapiente eaque saepe obcaecati labore? Dolore perspiciatis obcaecati pariatur? Harum ipsa voluptatum eius ut consequuntur repellat, et officia distinctio, quisquam ullam eligendi cumque dolor aliquam.</p>
            <div class="card proverbio-card">
                <div class="card-body">
                    <h5 class="card-title">Proverbio del JEFE Chino</h5>
                    <p class="card-text"><?php echo obtenerProverbio(); ?></p>
                </div>
            </div>
            <nav>
                <ul>
                    <li><a href="/loto/menu.php">Generador de Lotto</a></li>
                    <li><a href="/libros/menu.php">Lista de Libros</a></li>
                    <li><a href="/buscadorciudades/buscador.php">Buscador de Ciudades</a></li>
                </ul>
            </nav>
        </div>
    </main>
</body>
</html>

<?php
function obtenerProverbio() {
    $proverbios = [
        "Jefe, necesito un descanso.",
        "No puedo con más reuniones, Jefe.",
        "La bandeja de entrada está llena, Jefe.",
        "El agua más clara puede contener los peces más grandes.",
        "¿Otra vez un informe urgente, Jefe?",
        "Jefe, el café se acabó.",
        "No temas ir despacio, teme no avanzar.",
        "El proyecto está creciendo, Jefe.",
        "Cada paso deja una huella.",
        "Las grandes almas tienen voluntades; las débiles solo deseos.",
        "Estoy cansado,Jefe.",
        "Jefe, estoy cansado.",
        "No puedo con mas tareas, Jefe."
    ];
    return $proverbios[array_rand($proverbios)];
}
?>
