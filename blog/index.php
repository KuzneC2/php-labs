<?php

define('BLOG_ENTRY', true);

require_once __DIR__ . '/controllers/HelloController.php';
require_once __DIR__ . '/controllers/ArticlesController.php';

$route = $_GET['route'] ?? '';

if (!is_string($route)) {
    $route = '';
}

$route = trim($route, '/');

$controller = new HelloController();

ob_start();

if ($route === '') {

    echo '<h2>Главная страница блога</h2>';
    echo '<p>Добро пожаловать в мой блог!</p>';

} elseif (preg_match('~^hello/([^/]+)$~u', $route, $matches)) {

    $title = 'Страница приветствия';

    $controller->sayHello(rawurldecode($matches[1]));

} elseif (preg_match('~^bye/([^/]+)$~u', $route, $matches)) {

    $title = 'Страница прощания';

    $controller->sayBye(rawurldecode($matches[1]));

} elseif ($route === 'default') {

    echo '<h2>Значение по умолчанию</h2>';
    echo '<p>Заголовок страницы не задан, поэтому используется "Мой блог".</p>';


} elseif ($route === 'articles') {

    $title = 'Все статьи';

    require_once __DIR__ . '/db.php';

    $articlesController = new ArticlesController($pdo);
    $articlesController->index();

} elseif (preg_match('~^articles/([1-9][0-9]*)$~', $route, $matches)) {

    require_once __DIR__ . '/db.php';

    $articlesController = new ArticlesController($pdo);
    $title = $articlesController->show((int) $matches[1]);

} else {

    http_response_code(404);
    echo '<h2>Страница не найдена</h2>';

}

$content = ob_get_clean();

require __DIR__ . '/templates/main.php';
