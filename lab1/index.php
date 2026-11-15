<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Лабораторная работа №1</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header>
        <img src="../assets/images/logo.png" alt="Московский Политех" class="university-logo">

        <h1>Лабораторная работа №1</h1>
    </header>

    <main>
        <h2>Форма обратной связи</h2>

        <form action="https://httpbin.org/post" method="POST">

            <label for="username">Имя пользователя:</label>
            <input type="text" id="username" name="username" required>

            <br><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <br><br>

            <label for="type">Тип обращения:</label>
            <select id="type" name="type">
                <option value="Жалоба">Жалоба</option>
                <option value="Предложение">Предложение</option>
                <option value="Благодарность">Благодарность</option>
            </select>

            <br><br>

            <label for="message">Текст обращения:</label>
            <br>
            <textarea id="message" name="message" rows="5" cols="40" required></textarea>

            <br><br>

            <p>Вариант ответа:</p>

            <label>
                <input type="checkbox" name="answer[]" value="SMS">
                SMS
            </label>

            <label>
                <input type="checkbox" name="answer[]" value="Email">
                Email
            </label>

            <br><br>

            <button type="submit">Отправить</button>

        </form>

        <p>
            <a href="headers.php">Перейти на страницу №2</a>
        </p>
    </main>

    
<footer>
    <p>Задание для самостоятельной работы</p>
    <p>Выполнил: Кузнецов Дмитрий Владиславович, 253-321</p>
</footer>


</body>

</html>