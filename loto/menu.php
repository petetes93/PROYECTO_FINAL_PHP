<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú de Lotto</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
            border-radius: 5px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
        }

        .volver-home {
            text-decoration: none;
            margin-top: 20px;
            font-size: 18px;
            padding: 10px 20px;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            transition: background-color 0.3s ease;
        }

        .volver-home:hover {
            background-color: lightskyblue;
        }

        .loteria-card {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #f8f9fa;
        }

        .loteria-card h5 {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
        }

        .loteria-card p {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .volver-menu {
            display: inline-block;
            text-decoration: none;
            margin-top: 20px;
            font-size: 18px;
            padding: 10px 20px;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            transition: background-color 0.3s ease;
        }

        .volver-menu:hover {
            background-color: lightskyblue;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header>
        <h1 id="page-title">Menú de Lotto</h1>
    </header>
    <main>
        <div class="container" id="content">
            <nav>
                <ul>
                    <li><a href="#" onclick="showPage('españa')">España</a></li>
                    <li><a href="#" onclick="showPage('alemania')">Alemania</a></li>
                    <li><a href="#" onclick="showPage('francia')">Francia</a></li>
                    <li><a href="#" onclick="showPage('italia')">Italia</a></li>
                </ul>
            </nav>
            <div id="menu" class="page">
                <a href="/home.php" class="volver-home">Volver al Home</a>
            </div>
            <div id="españa" class="page" style="display: none;">
                <h1>Lotto España</h1>
                <div class="loteria-card">
                    <h5>Combinación:</h5>
                    <p id="españa-numbers"></p>
                </div>
                <a href="#" class="volver-menu" onclick="showPage('menu')">Volver al menú de Lotto</a>
            </div>
            <div id="alemania" class="page" style="display: none;">
                <h1>Lotto Alemania</h1>
                <div class="loteria-card">
                    <h5>Combinación:</h5>
                    <p id="alemania-numbers"></p>
                </div>
                <a href="#" class="volver-menu" onclick="showPage('menu')">Volver al menú de Lotto</a>
            </div>
            <div id="francia" class="page" style="display: none;">
                <h1>Lotto Francia</h1>
                <div class="loteria-card">
                    <h5>Combinación:</h5>
                    <p id="francia-numbers"></p>
                </div>
                <a href="#" class="volver-menu" onclick="showPage('menu')">Volver al menú de Lotto</a>
            </div>
            <div id="italia" class="page" style="display: none;">
                <h1>Lotto Italia</h1>
                <div class="loteria-card">
                    <h5>Combinación:</h5>
                    <p id="italia-numbers"></p>
                </div>
                <a href="#" class="volver-menu" onclick="showPage('menu')">Volver al menú de Lotto</a>
            </div>
        </div>
    </main>
    <script>
        function generarLotto(max, count) {
            let numbers = Array.from({ length: max }, (_, i) => i + 1);
            for (let i = numbers.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [numbers[i], numbers[j]] = [numbers[j], numbers[i]];
            }
            return numbers.slice(0, count).join(", ");
        }

        function showPage(page) {
            
            document.querySelectorAll('.page').forEach(p => p.style.display = 'none');
           
            document.getElementById(page).style.display = 'block';

            
            let title = '';
            switch (page) {
                case 'españa':
                    title = 'España';
                    document.getElementById('españa-numbers').innerText = generarLotto(49, 6);
                    break;
                case 'alemania':
                    title = 'Alemania';
                    document.getElementById('alemania-numbers').innerText = generarLotto(49, 6);
                    break;
                case 'francia':
                    title = 'Francia';
                    document.getElementById('francia-numbers').innerText = generarLotto(49, 5);
                    break;
                case 'italia':
                    title = 'Italia';
                    document.getElementById('italia-numbers').innerText = generarLotto(90, 6);
                    break;
                default:
                    title = 'Menú de Lotto';
                    break;
            }
            document.getElementById('page-title').innerText = title;
        }
    </script>
</body>
</html>
