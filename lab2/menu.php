<?php


if (!defined('LAB2_ENTRY')) {
    http_response_code(403);
    exit('Доступ запрещён');
}

function showMenu(): string
{
    $pages = [
        'view' => 'Просмотр',
        'add' => 'Добавление записи',
        'edit' => 'Редактирование записи',
        'delete' => 'Удаление записи'
    ];

    $page = $_GET['page'] ?? 'view';

    if (!is_string($page) || !isset($pages[$page])) {
        $page = 'view';
    }

    $html = '<nav class="lab2-menu">';

    foreach ($pages as $key => $title) {
        $active = ($page === $key) ? 'is-active' : '';

        $html .= '<a class="' . $active .
            '" href="index.php?page=' . $key . '">' .
            $title . '</a>';
    }

    $html .= '</nav>';

    if ($page === 'view') {
        $sortTypes = [
            'id' => 'По порядку',
            'surname' => 'По фамилии',
            'date' => 'По дате рождения'
        ];

        $sort = $_GET['sort'] ?? 'id';

        if (!is_string($sort) || !isset($sortTypes[$sort])) {
            $sort = 'id';
        }

        $html .= '<nav class="lab2-submenu">';

        foreach ($sortTypes as $key => $title) {
            $active = ($sort === $key) ? 'is-active' : '';

            $html .= '<a class="' . $active .
                '" href="index.php?page=view&amp;sort=' .
                $key . '">' . $title . '</a>';
        }

        $html .= '</nav>';
    }

    return $html;
}
