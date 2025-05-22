<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <title>luXew</title>
</head>
<body>
    <header>
        <h1>Bienvenu sur luXew!</h1>
        <nav>
            <ul>
                <li><a href="/articles/index.php">Articles</a></li>
                <li><a href="/auth/login.php">Connexion</a></li>
                <li><a href="/auth/register.php">Inscription</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php include($view); ?>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> luXew. All rights reserved.</p>
    </footer>
</body>
</html>