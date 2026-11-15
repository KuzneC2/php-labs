<?php

if (!defined('BLOG_ENTRY')) {
    http_response_code(403);
    exit('Доступ запрещён');
}

class ArticlesController
{
    public function __construct(private PDO $pdo)
    {
    }

    public function index(): void
    {
        $sql = "SELECT id, title FROM articles ORDER BY id ASC";

        $articles = $this->pdo->query($sql)->fetchAll();

        require __DIR__ . '/../templates/articles-list.php';
    }

    // Открываем конкретную статью
    public function show(int $id): ?string
    {
        $sql = "SELECT * FROM articles WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $article = $stmt->fetch();

        if (!$article) {
            http_response_code(404);
            echo '<h2>Статья не найдена</h2>';
            return null;
        }

        $sql = "SELECT nickname FROM users WHERE id = :author_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'author_id' => $article['author_id']
        ]);

        $nickname = $stmt->fetchColumn();

        if ($nickname === false) {
            $nickname = 'Неизвестный автор';
        }

        require __DIR__ . '/../templates/article.php';
        return $article['title'];
    }
}
