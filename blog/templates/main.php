<?php

$pageTitle = isset($title) && is_string($title) && trim($title) !== ''
    ? $title
    : 'Мой блог';

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>

    <link rel="stylesheet" href="/php-labs/assets/css/main.css">
    <link rel="stylesheet" href="/php-labs/assets/css/blog.css">
</head>

<body>


    <header class="site-header">
        <img src="/php-labs/assets/images/logo.png" alt="Московский Политех" class="site-logo">

        <div class="header-content">
            <h1>Мой блог</h1>
        </div>
    </header>

    <main>
        <div class="blog-layout">

            <section class="blog-content">
                <?= $content ?>
            </section>

            <aside class="blog-sidebar">
                <h3>Меню</h3>

                <ul>
                    <li>
                        <a href="/php-labs/blog/">
                            Главная страница
                        </a>
                    </li>
                    <li>
                        <a href="/php-labs/blog/hello/Dima">
                            Приветствие
                        </a>
                    </li>
                    <li>
                        <a href="/php-labs/blog/bye/Dima">
                            Прощание
                        </a>
                    </li>

                    <li>
                        <a href="/php-labs/blog/articles">
                            Статьи
                        </a>
                    </li>

                    <li>
                        <a href="/php-labs/blog/default">
                            Значение по умолчанию
                        </a>
                    </li>

                </ul>
            </aside>

        </div>

        <p>

            <a href="/php-labs/index.php" class="back-button">
                ← На главную портфолио
            </a>

        </p>
    </main>

    
<footer>
    <p>Задание для самостоятельной работы</p>
    <p>Выполнил: Кузнецов Дмитрий Владиславович, 253-321</p>
</footer>


</body>

</html>