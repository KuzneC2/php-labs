<?php

if (!defined('LAB2_ENTRY')) {
    http_response_code(403);
    exit('Доступ запрещён');
}

$fields = [
    'surname',
    'name',
    'lastname',
    'gender',
    'date',
    'phone',
    'location',
    'email',
    'comment'
];

$data = array_fill_keys($fields, '');

$errors = [];
$success = false;

$escape = static function ($value): string {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
};

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_contact'])) {

    foreach ($fields as $field) {
        $value = $_POST[$field] ?? '';
        $data[$field] = is_string($value) ? trim($value) : '';
    }

    if (
        mb_strlen($data['surname']) < 2 ||
        mb_strlen($data['surname']) > 100
    ) {
        $errors[] = 'Фамилия должна содержать от 2 до 100 символов.';
    }

    if (
        mb_strlen($data['name']) < 2 ||
        mb_strlen($data['name']) > 100
    ) {
        $errors[] = 'Имя должно содержать от 2 до 100 символов.';
    }

    if (mb_strlen($data['lastname']) > 100) {
        $errors[] = 'Отчество слишком длинное.';
    }

    if (
        !in_array(
            $data['gender'],
            ['', 'мужской', 'женский'],
            true
        )
    ) {
        $errors[] = 'Выбран некорректный пол.';
    }

    if ($data['date'] !== '') {

        $date = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $data['date']
        );

        if (
            $date === false ||
            $date->format('Y-m-d') !== $data['date'] ||
            $data['date'] > date('Y-m-d')
        ) {
            $errors[] = 'Укажите корректную дату рождения.';
        }
    }

    $phoneDigits = preg_replace('/\D/', '', $data['phone']);

    $validPhone =
        preg_match('/^\+?[0-9() -]+$/', $data['phone']) === 1 &&
        strlen($phoneDigits) >= 10 &&
        strlen($phoneDigits) <= 15 &&
        strlen($data['phone']) <= 30;

    if (!$validPhone) {
        $errors[] = 'Телефон должен содержать от 10 до 15 цифр.';
    }

    if (
        $data['email'] === '' ||
        strlen($data['email']) > 255 ||
        filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false
    ) {
        $errors[] = 'Укажите корректный email.';
    }

    if (mb_strlen($data['location']) > 255) {
        $errors[] = 'Адрес не должен превышать 255 символов.';
    }

    if (mb_strlen($data['comment']) > 2000) {
        $errors[] = 'Комментарий не должен превышать 2000 символов.';
    }

    if (empty($errors)) {

        try {
            $sql = "INSERT INTO contacts
                (surname, name, lastname, gender, `date`,
                 phone, location, email, `comment`)
                VALUES
                (:surname, :name, :lastname, :gender, :date,
                 :phone, :location, :email, :comment)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':surname' => $data['surname'],
                ':name' => $data['name'],
                ':lastname' => $data['lastname'],
                ':gender' => $data['gender'],
                ':date' => $data['date'] !== '' ? $data['date'] : null,
                ':phone' => $data['phone'],
                ':location' => $data['location'],
                ':email' => $data['email'],
                ':comment' => $data['comment']
            ]);

            $success = true;

            $data = array_fill_keys($fields, '');

        } catch (PDOException $e) {
            $errors[] = 'Не удалось сохранить контакт в базе данных.';
        }
    }
}
?>

<h2>Добавление записи</h2>

<?php if ($success): ?>

    <div class="form-message success">
        Запись добавлена
    </div>

<?php endif; ?>

<?php if (!empty($errors)): ?>

    <div class="form-message error">
        <strong>Ошибка: запись не добавлена</strong>

        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= $escape($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

<?php endif; ?>

<form action="index.php?page=add" method="POST" class="contact-form">

    <label for="surname">Фамилия *</label>
    <input type="text" id="surname" name="surname" minlength="2" maxlength="100"
        value="<?= $escape($data['surname']) ?>" required>

    <label for="name">Имя *</label>
    <input type="text" id="name" name="name" minlength="2" maxlength="100" value="<?= $escape($data['name']) ?>"
        required>

    <label for="lastname">Отчество</label>
    <input type="text" id="lastname" name="lastname" maxlength="100" value="<?= $escape($data['lastname']) ?>">

    <label for="gender">Пол</label>
    <select id="gender" name="gender">
        <option value="">Не выбран</option>

        <option value="мужской" <?= $data['gender'] === 'мужской' ? 'selected' : '' ?>>
            Мужской
        </option>

        <option value="женский" <?= $data['gender'] === 'женский' ? 'selected' : '' ?>>
            Женский
        </option>
    </select>

    <label for="date">Дата рождения</label>
    <input type="date" id="date" name="date" max="<?= date('Y-m-d') ?>" value="<?= $escape($data['date']) ?>">

    <label for="phone">Телефон *</label>
    <input type="tel" id="phone" name="phone" placeholder="+79991234567" maxlength="30"
        value="<?= $escape($data['phone']) ?>" required>

    <label for="location">Адрес</label>
    <input type="text" id="location" name="location" maxlength="255" value="<?= $escape($data['location']) ?>">

    <label for="email">Email *</label>
    <input type="email" id="email" name="email" maxlength="255" value="<?= $escape($data['email']) ?>" required>

    <label for="comment">Комментарий</label>
    <textarea id="comment" name="comment" rows="4" maxlength="2000"><?= $escape($data['comment']) ?></textarea>

    <button type="submit" name="add_contact" value="1">
        Добавить контакт
    </button>

    <p class="required-note">
        * — обязательные поля
    </p>

</form>