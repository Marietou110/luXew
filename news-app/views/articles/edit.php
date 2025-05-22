<form action="/news-app/controllers/ArticleController.php?action=edit" method="POST">
    <h2>Modification </h2>
    <label for="title">Titre:</label>
    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($article->titre); ?>" required>

    <label for="content">Contenu:</label>
    <textarea id="content" name="content" required><?php echo htmlspecialchars($article->contenu); ?></textarea>

    <input type="hidden" name="id" value="<?php echo $article->id; ?>">
    <button type="submit">Mise à jour d'un article</button>
</form>