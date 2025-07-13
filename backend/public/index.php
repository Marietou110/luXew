<?php
require_once '../controleurs/ControleurArticle.php';
require_once '../controleurs/ControleurCategorie.php';
require_once '../controleurs/ControleurCommentaire.php';
require_once '../controleurs/ControleurAuthentification.php';
require_once '../controleurs/ControleurReaction.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$requete = $_SERVER['REQUEST_URI'];
$methode = $_SERVER['REQUEST_METHOD'];

$controleurArticle = new ControleurArticle();
$controleurCategorie = new ControleurCategorie();
$controleurCommentaire = new ControleurCommentaire();
$controleurAuthentification = new ControleurAuthentification();
$controleurReaction = new ControleurReaction();

try {
    switch (true) {
        case preg_match('#^/articles$#', $requete) && $methode === 'GET':
            $controleurArticle->listerArticles();
            break;
        case preg_match('#^/articles/(\d+)$#', $requete, $matches) && $methode === 'GET':
            $controleurArticle->afficherArticle($matches[1]);
            break;
        case preg_match('#^/articles/create$#', $requete) && $methode === 'POST':
            $controleurArticle->creerArticle();
            break;
        case preg_match('#^/categories$#', $requete) && $methode === 'GET':
            $controleurCategorie->listerCategories();
            break;
        case preg_match('#^/categories/(\d+)/articles$#', $requete, $matches) && $methode === 'GET':
            $controleurCategorie->obtenirArticlesParCategorie($matches[1]);
            break;
        case preg_match('#^/categories/create$#', $requete) && $methode === 'POST':
            $controleurCategorie->creerCategorie();
            break;
        case preg_match('#^/comments/(\d+)$#', $requete, $matches) && $methode === 'GET':
            $controleurCommentaire->obtenirCommentaires($matches[1]);
            break;
        case preg_match('#^/comments/create$#', $requete) && $methode === 'POST':
            $controleurCommentaire->ajouterCommentaire();
            break;
        case preg_match('#^/comments/(\d+)$#', $requete, $matches) && $methode === 'POST':
            $controleurCommentaire->modifierCommentaire($matches[1]);
            break;
        case preg_match('#^/comments/(\d+)$#', $requete, $matches) && $methode === 'DELETE':
            $controleurCommentaire->supprimerCommentaire($matches[1]);
            break;
        case preg_match('#^/login$#', $requete) && $methode === 'POST':
            $controleurAuthentification->connecter();
            break;
        case preg_match('#^/logout$#', $requete) && $methode === 'POST':
            $controleurAuthentification->deconnecter();
            break;
        case preg_match('#^/register$#', $requete) && $methode === 'POST':
            $controleurAuthentification->inscrire();
            break;
        case preg_match('#^/reactions/create$#', $requete) && $methode === 'POST':
            $controleurReaction->ajouterReaction();
            break;
        case preg_match('#^/reactions/(\d+)$#', $requete, $matches) && $methode === 'DELETE':
            $controleurReaction->supprimerReaction($matches[1]);
            break;
        case preg_match('#^/reactions/article/(\d+)$#', $requete, $matches) && $methode === 'GET':
            $controleurReaction->obtenirStatistiquesReactions($matches[1], null);
            break;
        case preg_match('#^/reactions/commentaire/(\d+)$#', $requete, $matches) && $methode === 'GET':
            $controleurReaction->obtenirStatistiquesReactions(null, $matches[1]);
            break;
        default:
            throw new Exception('Route non trouvée');
    }
} catch (Exception $e) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['erreur' => $e->getMessage()]);
}
?>