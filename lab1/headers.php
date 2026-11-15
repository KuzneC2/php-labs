<?php
$url = "https://httpbin.org/get";

$headers = @get_headers($url);

if ($headers === false) {
    $result = "Ошибка: не удалось получить HTTP-заголовки.";
} else {
    $result = implode("\n", $headers);
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Лабораторная №1 — HTTP-заголовки</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header>
        <img src="../assets/images/logo.png" alt="Московский Политех" class="university-logo">

        <h1>Лабораторная работа №1</h1>
    </header>

    <main>
        <h2>HTTP-заголовки</h2>

        <p>Результат функции get_headers():</p>

        <textarea class="headers-output" readonly><?= htmlspecialchars($result, ENT_QUOTES, 'UTF-8') ?></textarea>

        <p>
            <a href="index.php" class="back-button">Вернуться к форме</a>
        </p>

        <a href="/php-labs/index.php" class="back-button">
            ← На главную портфолио
        </a>

    </main>

    <footer>
        <p>Задание для самостоятельной работы</p>
    </footer>

</body>

</html>