<?php


if (!defined('LAB2_ENTRY')) {
    http_response_code(403);
    exit('Доступ запрещён');
}

function showContacts(PDO $pdo, string $sort = 'id', int $pageNumber = 1): string
{
    $sortTypes = [
        'id' => 'id ASC',
        'surname' => 'surname ASC, name ASC, id ASC',
        'date' => '`date` ASC, id ASC'
    ];

    $orderBy = $sortTypes[$sort] ?? $sortTypes['id'];

    $limit = 10;

    $total = (int) $pdo->query(
        "SELECT COUNT(*) FROM contacts"
    )->fetchColumn();

    $totalPages = max(1, (int) ceil($total / $limit));

    $pageNumber = max(1, min($pageNumber, $totalPages));

    $offset = ($pageNumber - 1) * $limit;

    $sql = "SELECT * FROM contacts
            ORDER BY $orderBy
            LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($contacts)) {
        return '<p>Контактов пока нет.</p>';
    }

    $html = '<table class="contacts-table">';

    $html .= '<thead>
        <tr>
            <th>Фамилия</th>
            <th>Имя</th>
            <th>Телефон</th>
            <th>Дата рождения</th>
        </tr>
    </thead>';

    $html .= '<tbody>';

    foreach ($contacts as $contact) {
        $html .= '<tr>';

        foreach (['surname', 'name', 'phone', 'date'] as $field) {
            $value = htmlspecialchars(
                (string) ($contact[$field] ?? ''),
                ENT_QUOTES,
                'UTF-8'
            );

            $html .= '<td>' . $value . '</td>';
        }

        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    if ($totalPages > 1) {
        $html .= '<nav class="pagination">';

        for ($i = 1; $i <= $totalPages; $i++) {
            $active = ($i === $pageNumber) ? 'is-active' : '';

            $html .= '<a class="' . $active .
                '" href="index.php?page=view&amp;sort=' .
                urlencode($sort) .
                '&amp;p=' . $i . '">' .
                $i . '</a>';
        }

        $html .= '</nav>';
    }

    return $html;
}
