
<?php

if (!defined('LAB2_ENTRY')) {
    http_response_code(403);
    exit('Доступ запрещён');
}

$contacts = $pdo->query(
    "SELECT id, surname, name
     FROM contacts
     ORDER BY surname ASC, name ASC, id ASC"
)->fetchAll(PDO::FETCH_ASSOC);

$selectedId = $_GET['id'] ?? '';

if (!is_string($selectedId) || !ctype_digit($selectedId)) {
    $selectedId = '';
}

$id = $selectedId !== ''
    ? (int) $selectedId
    : (int) ($contacts[0]['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = :id");
$stmt->execute([':id' => $id]);
$contact = $stmt->fetch(PDO::FETCH_ASSOC);

$errors = [];
$success = false;
$data = $contact ?: [];

$escape = static function ($value): string {
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
};

if (
    $contact &&
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['save_contact'])
) {
    $submittedId = $_POST['contact_id'] ?? '';

    if (
        !is_string($submittedId) ||
        !ctype_digit($submittedId) ||
        (int) $submittedId !== $id
    ) {
        $errors[] = 'Неверный идентификатор контакта.';
    } else {
        $fields = [
            'surname', 'name', 'lastname',
            'gender', 'date', 'phone',
            'location', 'email', 'comment'
        ];

        foreach ($fields as $field) {
            $value = $_POST[$field] ?? '';
            $data[$field] = is_string($value) ? trim($value) : '';
        }

        foreach (['surname' => 'Фамилия', 'name' => 'Имя'] as $field => $title) {
            $length = mb_strlen($data[$field]);

            if ($length < 2 || $length > 100) {
                $errors[] = "$title: от 2 до 100 символов.";
            }
        }

        if (mb_strlen($data['lastname']) > 100) {
            $errors[] = 'Отчество слишком длинное.';
        }

        if (!in_array(
            $data['gender'],
            ['', 'мужской', 'женский'],
            true
        )) {
            $errors[] = 'Некорректный пол.';
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
                $errors[] = 'Некорректная дата рождения.';
            }
        }

        $digits = preg_replace('/\D/', '', $data['phone']);

        if (
            preg_match('/^\+?[0-9() -]+$/', $data['phone']) !== 1 ||
            strlen($digits) < 10 ||
            strlen($digits) > 15 ||
            strlen($data['phone']) > 30
        ) {
            $errors[] = 'Телефон должен содержать от 10 до 15 цифр.';
        }

        if (
            strlen($data['email']) > 255 ||
            filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false
        ) {
            $errors[] = 'Некорректный email.';
        }

        if (mb_strlen($data['location']) > 255) {
            $errors[] = 'Адрес слишком длинный.';
        }

        if (mb_strlen($data['comment']) > 2000) {
            $errors[] = 'Комментарий слишком длинный.';
        }

        if (empty($errors)) {
            try {
                $sql = "UPDATE contacts SET
                    surname = :surname,
                    name = :name,
                    lastname = :lastname,
                    gender = :gender,
                    `date` = :date,
                    phone = :phone,
                    location = :location,
                    email = :email,
                    `comment` = :comment
                    WHERE id = :id";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ':surname' => $data['surname'],
                    ':name' => $data['name'],
                    ':lastname' => $data['lastname'],
                    ':gender' => $data['gender'],
                    ':date' => $data['date'] ?: null,
                    ':phone' => $data['phone'],
                    ':location' => $data['location'],
                    ':email' => $data['email'],
                    ':comment' => $data['comment'],
                    ':id' => $id
                ]);

                $success = true;

            } catch (PDOException $e) {
                $errors[] = 'Ошибка сохранения в базе данных.';
            }
        }
    }
}
?>

<h2>Редактирование записи</h2>

<?php if (empty($contacts)): ?>

    <p>Контактов пока нет.</p>

<?php elseif (!$contact): ?>

    <p class="form-message error">Контакт не найден.</p>
    <a href="index.php?page=edit">Выбрать первый контакт</a>

<?php else: ?>

    <div class="edit-contact-list">
        <?php foreach ($contacts as $item): ?>
            <a
                href="index.php?page=edit&amp;id=<?= (int) $item['id'] ?>"
                class="<?= (int) $item['id'] === $id ? 'is-active' : '' ?>"
            >
                <?= $escape($item['surname'] . ' ' . $item['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if ($success): ?>
        <div class="form-message success">
            Запись изменена
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="form-message error">
            <strong>Ошибка: запись не изменена</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= $escape($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form
        action="index.php?page=edit&amp;id=<?= $id ?>"
        method="POST"
        class="contact-form"
    >
        <input type="hidden" name="contact_id" value="<?= $id ?>">

        <label for="surname">Фамилия *</label>
        <input
            type="text"
            id="surname"
            name="surname"
            minlength="2"
            maxlength="100"
            value="<?= $escape($data['surname']) ?>"
            required
        >

        <label for="name">Имя *</label>
        <input
            type="text"
            id="name"
            name="name"
            minlength="2"
            maxlength="100"
            value="<?= $escape($data['name']) ?>"
            required
        >

        <label for="lastname">Отчество</label>
        <input
            type="text"
            id="lastname"
            name="lastname"
            maxlength="100"
            value="<?= $escape($data['lastname']) ?>"
        >

        <label for="gender">Пол</label>
        <select id="gender" name="gender">
            <option value="">Не выбран</option>
            <option value="мужской"
                <?= ($data['gender'] ?? '') === 'мужской' ? 'selected' : '' ?>>
                Мужской
            </option>
            <option value="женский"
                <?= ($data['gender'] ?? '') === 'женский' ? 'selected' : '' ?>>
                Женский
            </option>
        </select>

        <label for="date">Дата рождения</label>
        <input
            type="date"
            id="date"
            name="date"
            max="<?= date('Y-m-d') ?>"
            value="<?= $escape($data['date']) ?>"
        >

        <label for="phone">Телефон *</label>
        <input
            type="tel"
            id="phone"
            name="phone"
            maxlength="30"
            value="<?= $escape($data['phone']) ?>"
            required
        >

        <label for="location">Адрес</label>
        <input
            type="text"
            id="location"
            name="location"
            maxlength="255"
            value="<?= $escape($data['location']) ?>"
        >

        <label for="email">Email *</label>
        <input
            type="email"
            id="email"
            name="email"
            maxlength="255"
            value="<?= $escape($data['email']) ?>"
            required
        >

        <label for="comment">Комментарий</label>
        <textarea
            id="comment"
            name="comment"
            rows="4"
            maxlength="2000"
        ><?= $escape($data['comment']) ?></textarea>

        <button type="submit" name="save_contact" value="1">
            Сохранить изменения
        </button>
    </form>

<?php endif; ?>
