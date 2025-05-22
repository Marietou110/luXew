<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles - News App</title>
    <link rel="stylesheet" href="/luXew/news-app/public/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Liste des Articles</h1>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/luXew/news-app/public/articles/create" class="btn btn-primary">Nouvel Article</a>
            <?php endif; ?>
        </header>

        <main>
            <?php if (!empty($articles)): ?>
                <div class="articles-grid">
                    <?php foreach ($articles as $article): ?>
                        <article class="article-card">
                            <h2><?= htmlspecialchars($article['titre']) ?></h2>
                            <div class="article-meta">
                                <span>Par <?= htmlspecialchars($article['auteur_pseudo'] ?? 'Anonyme') ?></span>
                                <span>Le <?= date('d/m/Y', strtotime($article['dateCreation'])) ?></span>
                            </div>
                            <div class="article-actions">
                                <a href="/luXew/news-app/public/articles/show/<?= $article['id'] ?>" 
                                   class="btn btn-view">Lire</a>
                                <?php if (isset($_SESSION['user_id']) && 
                                        $_SESSION['user_id'] == $article['auteur_id']): ?>
                                    <a href="/luXew/news-app/public/articles/edit/<?= $article['id'] ?>" 
                                       class="btn btn-edit">Modifier</a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-articles">Aucun article disponible pour le moment.</p>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>