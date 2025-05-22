<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Article</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Créer un Nouvel Article</h1>
        <form action="/articles/store" method="POST">
            <div class="form-group">
                <label for="titre">Titre:</label>
                <input type="text" id="titre" name="titre" required>
            </div>
            <div class="form-group">
                <label for="contenu">Contenu:</label>
                <textarea id="contenu" name="contenu" rows="10" required></textarea>
            </div>
            <button type="submit">Créer l'Article</button>
        </form>
    </div>
</body>
</html>