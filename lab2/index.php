<?php


session_start();

if (empty($_SESSION['lab2_csrf'])) {
    $_SESSION['lab2_csrf'] = bin2hex(random_bytes(32));
}

define('LAB2_ENTRY', true);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/viewer.php';
require_once __DIR__ . '/menu.php';

$pages = ['view', 'add', 'edit', 'delete'];

$page = $_GET['page'] ?? 'view';

if (!is_string($page) || !in_array($page, $pages, true)) {
    $page = 'view';
}


$sort = $_GET['sort'] ?? 'id';

if (!is_string($sort)) {
    $sort = 'id';
}


$pageNumber = $_GET['p'] ?? '1';

if (!is_string($pageNumber) || !ctype_digit($pageNumber)) {
    $pageNumber = '1';
}

$pageNumber = max(1, (int) $pageNumber);


?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Лабораторная №2 — Записная книжка</title>

    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/lab2.css">
</head>

<body>


    <header class="site-header">
        <img src="/php-labs/assets/images/logo.png" alt="Московский Политех" class="site-logo">

        <div class="header-content">
            <h1>Лабораторная работа №2</h1>
            <p>Записная книжка</p>
        </div>
    </header>


    <main>

        <?= showMenu() ?>

        <?php
        switch ($page) {
            case 'view':
                echo '<h2>Просмотр контактов</h2>';
                echo showContacts($pdo, $sort, $pageNumber);
                break;


            case 'add':
                require __DIR__ . '/add.php';
                break;



            case 'edit':
                require __DIR__ . '/edit.php';
                break;



            case 'delete':
                require __DIR__ . '/delete.php';
                break;

        }
        ?>

        <p>
            <a href="../index.php">На главную</a>
        </p>

    </main>


    <footer>
        <p>Задание для самостоятельной работы</p>
        <p>Выполнил: Кузнецов Дмитрий Владиславович, 253-321</p>
    </footer>


</body>

</html>