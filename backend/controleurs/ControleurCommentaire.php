<?php
require_once '../modeles/ModeleCommentaire.php';
require_once '../modeles/ModeleReaction.php';

class ControleurCommentaire {
    private $modeleCommentaire;
    private $modeleReaction;

    public function __construct() {
        $this->modeleCommentaire = new ModeleCommentaire();
        $this->modeleReaction = new ModeleReaction();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function obtenirCommentaires($articleId) {
        try {
            if (!filter_var($articleId, FILTER_VALIDATE_INT)) {
                throw new Exception('ID d\'article invalide');
            }
            $commentaires = $this->modeleCommentaire->obtenirCommentairesParArticle($articleId);
            foreach ($commentaires as &$commentaire) {
                $commentaire['reactions'] = $this->modeleReaction->obtenirReactionsParCommentaire($commentaire['id']);
            }
            $this->repondreJson(['commentaires' => $commentaires]);
        } catch (Exception $e) {
            $this->repondreJson(['erreur' => $e->getMessage()], 400);
        }
    }

    public function ajouterCommentaire() {
        try {
            if (!isset($_SESSION['utilisateurId'])) {
                throw new Exception('Non autorisé');
            }
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée');
            }
            $contenu = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING);
            $articleId = filter_input(INPUT_POST, 'articleId', FILTER_VALIDATE_INT);
            $parentId = filter_input(INPUT_POST, 'parentId', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

            if (empty($contenu) || !$articleId) {
                throw new Exception('Contenu ou ID d\'article manquant');
            }

            $donnees = [
                'contenu' => $contenu,
                'utilisateurId' => $_SESSION['utilisateurId'],
                'articleId' => $articleId,
                'parentId' => $parentId
            ];

            $commentaireId = $this->modeleCommentaire->ajouterCommentaire($donnees);
            $this->repondreJson(['succes' => true, 'commentaireId' => $commentaireId]);
        } catch (Exception $e) {
            $this->repondreJson(['erreur' => $e->getMessage()], 400);
        }
    }

    public function modifierCommentaire($commentaireId) {
        try {
            if (!isset($_SESSION['utilisateurId'])) {
                throw new Exception('Non autorisé');
            }
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée');
            }
            $contenu = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING);
            if (empty($contenu)) {
                throw new Exception('Contenu manquant');
            }
            $this->modeleCommentaire->modifierCommentaire($commentaireId, ['contenu' => $contenu]);
            $this->repondreJson(['succes' => true]);
        } catch (Exception $e) {
            $this->repondreJson(['erreur' => $e->getMessage()], 400);
        }
    }

    public function supprimerCommentaire($commentaireId) {
        try {
            if (!isset($_SESSION['utilisateurId']) || !$this->aPermission()) {
                throw new Exception('Non autorisé');
            }
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                throw new Exception('Méthode non autorisée');
            }
            $this->modeleCommentaire->supprimerCommentaire($commentaireId);
            $this->repondreJson(['succes' => true]);
        } catch (Exception $e) {
            $this->repondreJson(['erreur' => $e->getMessage()], 400);
        }
    }

    private function aPermission() {
        return isset($_SESSION['rolesUtilisateur']) && 
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