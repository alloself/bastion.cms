<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Страница не найдена</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
            color: #333;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            max-width: 500px;
        }
        h1 {
            font-size: 4rem;
            margin: 0;
            color: #e74c3c;
        }
        p {
            font-size: 1.2rem;
            margin: 1rem 0;
        }
        a {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.5rem 1rem;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        a:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>404</h1>
        <p>Страница не найдена</p>
        <p>Запрашиваемая вами страница не существует или была перемещена.</p>
        <a href="/">Вернуться на главную</a>
    </div>
</body>
</html>
