<?php
require_once '../modeles/ModeleArticle.php';
require_once '../modeles/ModeleCategorie.php';
require_once '../modeles/ModeleReaction.php';
use Cloudinary\Cloudinary;

class ControleurArticle {
    private $modeleArticle;
    private $modeleCategorie;
    private $modeleReaction;
    private $cloudinary;

    public function __construct() {
        $this->modeleArticle = new ModeleArticle();
        $this->modeleCategorie = new ModeleCategorie();
        $this->modeleReaction = new ModeleReaction();
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => 'votre_cloud_name',
                'api_key' => 'votre_api_key',
                'api_secret' => 'votre_api_secret'
            ]
        ]);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function listerArticles() {
        try {
            $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
            $limite = filter_input(INPUT_GET, 'limite', FILTER_VALIDATE_INT) ?: 10;
            $format = filter_input(INPUT_GET, 'format', FILTER_SANITIZE_STRING) ?: 'json';

            if ($page < 1 || $limite < 1) {
                throw new Exception('Paramètres de pagination invalides');
            }

            $offset = ($page - 1) * $limite;
            $articles = $this->modeleArticle->obtenirArticlesPagine($offset, $limite);
            $categories = $this->modeleCategorie->obtenirToutesCategories();

            foreach ($articles as &$article) {
                $article['reactions'] = $this->modeleReaction->obtenirReactionsParArticle($article['id']);
            }

            if ($format === 'xml') {
                header('Content-Type: application/xml');
                $xml = new SimpleXMLElement('<articles/>');
                foreach ($articles as $article) {
                    $articleXml = $xml->addChild('article');
                    $articleXml->addChild('id', $article['id']);
                    $articleXml->addChild('titre', htmlspecialchars($article['titre']));
                    $articleXml->addChild('contenu', htmlspecialchars($article['contenu']));
                    $articleXml->addChild('dateCreation', $article['dateCreation']);
                    $reactionsXml = $articleXml->addChild('reactions');
                    foreach ($article['reactions'] as $reaction) {
                        $reactionXml = $reactionsXml->addChild('reaction');
                        $reactionXml->addChild('type', $reaction['type']);
                        $reactionXml->addChild('nombre', $reaction['nombre']);
                    }
                }
                echo $xml->asXML();
            } else {
                $this->repondreJson(['articles' => $articles, 'categories' => $categories]);
            }
        } catch (Exception $e) {
            $this->repondreJson(['erreur' => $e->getMessage()], 400);
        }
    }

    public function afficherArticle($id) {
        try {
            $article = $this->modeleArticle->obtenirArticleParId($id);
            if (!$article) {
                throw new Exception('Article non trouvé');
            }
            $commentaires = $this->modeleCommentaire->obtenirCommentairesParArticle($id);
            foreach ($commentaires as &$commentaire) {
                $commentaire['reactions'] = $this->modeleReaction->obtenirReactionsParCommentaire($commentaire['id']);
            }
            $reactions = $this->modeleReaction->obtenirReactionsParArticle($id);
            $this->repondreJson([
                'article' => $article,
                'commentaires' => $commentaires,
                'reactions' => $reactions
            ]);
        } catch (Exception $e) {
            $this->repondreJson(['erreur' => $e->getMessage()], 404);
        }
    }

    public function creerArticle() {
        try {
            if (!isset($_SESSION['utilisateurId']) || !$this->aPermission()) {
                throw new Exception('Non autorisé');
            }
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée');
            }

            $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
            $contenu = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING);
            $categories = $_POST['categories'] ?? [];

            if (empty($titre) || empty($contenu)) {
                throw new Exception('Champs titre ou contenu manquants');
            }

            $articleData = [
                'titre' => $titre,
                'contenu' => $contenu,
                'auteurId' => $_SESSION['utilisateurId']
            ];

            $articleId = $this->modeleArticle->creerArticle($articleData);
            if (!$articleId) {
                throw new Exception('Échec de la création de l\'article');
            }

            foreach ($categories as $categorieId) {
                if (!filter_var($categorieId, FILTER_VALIDATE_INT)) {
                    throw new Exception('ID de catégorie invalide');
                }
                $this->modeleArticle->ajouterCategorieArticle($articleId, $categorieId);
            }

            if (!empty($_FILES['media'])) {
                $this->uploaderMedia($_FILES['media'], $articleId);
            }

            $this->repondreJson(['succes' => true, 'articleId' => $articleId]);
        } catch (Exception $e) {
            $this->repondreJson(['erreur' => $e->getMessage()], 400);
        }
    }

    private function uploaderMedia($fichier, $articleId) {
        try {
            if ($fichier['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Erreur lors de l\'upload du fichier');
            }

            $type = $this->determinerTypeMedia($fichier['type']);
            if (!in_array($type, ['image', 'audio', 'video'])) {
                throw new Exception('Type de média non supporté');
            }

            $resultat = $this->cloudinary->uploadApi()->upload($fichier['tmp_name'], [
                'folder' => 'luxew_medias',
                'public_id' => 'media_' . $articleId . '_' . time()
            ]);

            $mediaData = [
                'articleId' => $articleId,
                'type' => $type,
                'url' => $resultat['secure_url'],
                'description' => $fichier['name']
            ];

            $this->modeleArticle->ajouterMedia($mediaData);
        } catch (Exception $e) {
            throw new Exception('Échec de l\'upload du média : ' . $e->getMessage());
        }
    }

    private function determinerTypeMedia($mimeType) {
        if (str_contains($mimeType, 'image')) return 'image';
        if (str_contains($mimeType, 'audio')) return 'audio';
        if (str_contains($mimeType, 'video')) return 'video';
        return 'image';
    }

    private function aPermission() {
        return isset($_SESSION['rolesUtilisateur']) && 
               in_array('editeur', $_SESSION['rolesUtilisateur']) || 
               in_array('admin', $_SESSION['rolesUtilisateur']);
    }

    private function repondreJson($donnees, $codeStatut = 200) {
        http_response_code($codeStatut);
        header('Content-Type: application/json');
        echo json_encode($donnees);
        exit;
    }
}
?>