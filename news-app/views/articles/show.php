<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['titre']) ?> - News App</title>
    <link rel="stylesheet" href="/luXew/news-app/public/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <a href="/luXew/news-app/public/articles" class="btn btn-back">← Retour aux articles</a>
        </header>

        <main class="article-detail">
            <?php if($article['titre'] != null): ?>
                <article>
                    <h1><?= htmlspecialchars($article['titre']) ?></h1>
                    
                    <div class="article-meta">
                        <span>Par <?= htmlspecialchars($article['auteur_pseudo'] ?? 'Anonyme') ?></span>
                        <span>Le <?= date('d/m/Y', strtotime($article['dateCreation'])) ?></span>
                        <?php if($article['dateModification'] !== $article['dateCreation']): ?>
                            <span>(Modifié le <?= date('d/m/Y', strtotime($article['dateModification'])) ?>)</span>
                        <?php endif; ?>
                    </div>

                    <div class="article-content">
                        <?= nl2br(htmlspecialchars($article['contenu'])) ?>
                    </div>

                    <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $article['auteur_id']): ?>
                        <div class="article-actions">
                            <a href="/luXew/news-app/public/articles/edit/<?= $article['id']?>" 
                               class="btn btn-edit">Modifier</a>
                        </div>
                    <?php endif; ?>
                </article>

                    <!-- Comments section -->
                <?php if(isset($comments) && !empty($comments)): ?>
                    <section class="comments">
                        <h2>Commentaires</h2>
                        <?php foreach($comments as $comment): ?>
                            <div class="comment">
                                <p><?= htmlspecialchars($comment['contenu']) ?></p>
                                <div class="comment-meta">
                                    <span>Par <?= htmlspecialchars($comment['auteur_pseudo']) ?></span>
                                    <span>Le <?= date('d/m/Y', strtotime($comment['dateCreation'])) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </section>
                <?php endif; ?>

                <?php if(isset($_SESSION['user_id'])): ?>
                    <form action="/luXew/news-app/public/comments/create" method="post" class="comment-form">
                        <input type="hidden" name="article_id" value="<?= $article['id']?>">
                        <textarea name="contenu" required placeholder="Votre commentaire..."></textarea>
                        <button type="submit" class="btn btn-primary">Commenter</button>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <p class="error">Article non trouvé.</p>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>