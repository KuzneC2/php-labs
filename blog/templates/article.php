
<article class="blog-article">

    <h2>
        <?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>
    </h2>

    <p>
        <strong>Автор:</strong>
        <?= htmlspecialchars($nickname, ENT_QUOTES, 'UTF-8') ?>
    </p>

    <hr>

    <p>
        <?= nl2br(htmlspecialchars($article['text'], ENT_QUOTES, 'UTF-8')) ?>
    </p>

    <p>
        <a href="/php-labs/blog/articles">
            Вернуться к статьям
        </a>
    </p>

</article>
