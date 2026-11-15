
<h2>Все статьи</h2>

<?php if (empty($articles)): ?>

    <p>Статей пока нет.</p>

<?php else: ?>

    <?php foreach ($articles as $article): ?>

        <div class="article-preview">
            <h3>
                <a href="/php-labs/blog/articles/<?= (int) $article['id'] ?>">
                    <?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            </h3>
        </div>

    <?php endforeach; ?>

<?php endif; ?>
