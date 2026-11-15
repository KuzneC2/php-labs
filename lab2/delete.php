
<?php

if (!defined('LAB2_ENTRY')) {
    http_response_code(403);
    exit('Доступ запрещён');
}

$success = '';
$error = '';

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['delete_contact'])
) {
    $id = filter_var(
        $_POST['contact_id'] ?? null,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['lab2_csrf'], $token) ||
        !$id
    ) {
        $error = 'Ошибка: неверный запрос на удаление.';
    } else {
        try {
            $stmt = $pdo->prepare(
                "SELECT surname FROM contacts WHERE id = :id"
            );

            $stmt->execute([':id' => $id]);
            $surname = $stmt->fetchColumn();

            if ($surname === false) {
                $error = 'Контакт не найден.';
            } else {
                $stmt = $pdo->prepare(
                    "DELETE FROM contacts WHERE id = :id"
                );

                $stmt->execute([':id' => $id]);

                if ($stmt->rowCount() === 1) {
                    $success = 'Запись с фамилией ' .
                        $surname . ' удалена';
                } else {
                    $error = 'Ошибка: запись не удалена.';
                }
            }

        } catch (PDOException $e) {
            $error = 'Ошибка: запись не удалена.';
        }
    }
}

$contacts = $pdo->query(
    "SELECT id, surname, name, lastname
     FROM contacts
     ORDER BY surname ASC, name ASC, id ASC"
)->fetchAll(PDO::FETCH_ASSOC);

$selectedId = $_GET['id'] ?? '';

if (!is_string($selectedId) || !ctype_digit($selectedId)) {
    $selectedId = '';
}

$selectedContact = null;

foreach ($contacts as $contact) {
    if ((string) $contact['id'] === $selectedId) {
        $selectedContact = $contact;
        break;
    }
}

$escape = static function ($value): string {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
};
?>

<h2>Удаление записи</h2>

<?php if ($success !== ''): ?>
    <div class="form-message success">
        <?= $escape($success) ?>
    </div>
<?php endif; ?>

<?php if ($error !== ''): ?>
    <div class="form-message error">
        <?= $escape($error) ?>
    </div>
<?php endif; ?>

<?php if (empty($contacts)): ?>

    <p>Контактов пока нет.</p>

<?php else: ?>

    <p>Выберите контакт для удаления:</p>

    <div class="delete-contact-list">

        <?php foreach ($contacts as $contact): ?>

            <?php
                $initials = mb_substr($contact['name'], 0, 1) . '.';

                if ($contact['lastname'] !== '') {
                    $initials .= mb_substr($contact['lastname'], 0, 1) . '.';
                }

                $fullName = $contact['surname'] . ' ' . $initials;
            ?>

            <a
                href="index.php?page=delete&amp;id=<?= (int) $contact['id'] ?>"
                class="<?= (int) $contact['id'] === (int) $selectedId && $selectedId !== '' ? 'is-active' : '' ?>"
            >
                <?= $escape($fullName) ?>
            </a>

        <?php endforeach; ?>

    </div>

    <?php if ($selectedContact): ?>

        <div class="delete-confirmation">

            <p>
                Вы действительно хотите удалить контакт
                <strong>
                    <?= $escape(
                        $selectedContact['surname'] . ' ' .
                        $selectedContact['name']
                    ) ?>
                </strong>?
            </p>

            <form
                action="index.php?page=delete"
                method="POST"
            >
                <input
                    type="hidden"
                    name="contact_id"
                    value="<?= (int) $selectedContact['id'] ?>"
                >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= $escape($_SESSION['lab2_csrf']) ?>"
                >

                <button
                    type="submit"
                    name="delete_contact"
                    value="1"
                    class="delete-btn"
                >
                    Удалить контакт
                </button>

            </form>

        </div>

    <?php elseif ($selectedId !== ''): ?>

        <p class="form-message error">Контакт не найден.</p>

    <?php endif; ?>

<?php endif; ?>
