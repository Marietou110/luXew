<?php
require_once '../modeles/ModeleReaction.php';

class ControleurReaction {
    private $modeleReaction;

    public function __construct() {
        $this->modeleReaction = new ModeleReaction();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function ajouterReaction() {
        try {
            if (!isset($_SESSION['utilisateurId'])) {
                throw new Exception('Non autorisé');
            }
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée');
            }
            $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
            $articleId = filter_input(INPUT_POST, 'articleId', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            $commentaireId = filter_input(INPUT_POST, 'commentaireId', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

            if (!in_array($type, ['aimer', 'nePasAimer']) || (!$articleId && !$commentaireId)) {
                throw new Exception('Type ou cible invalide');
            }

            $donnees = [
                'utilisateurId' => $_SESSION['utilisateurId'],
                'articleId' => $articleId,
                'commentaireId' => $commentaireId,
                'type' => $type
            ];

            $reactionId = $this->modeleReaction->ajouterReaction($donnees);
            $this->repondreJson(['succes' => true, 'reactionId' => $reactionId]);
        } catch (Exception $e) {
            $this->repondreJson(['erreur' => $e->getMessage()], 400);
        }
    }

    public function supprimerReaction($reactionId) {
        try {
            if (!isset($_SESSION['utilisateurId'])) {
                throw new Exception('Non autorisé');
            }
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                throw new Exception('Méthode non autorisée');
            }
            $this->modeleReaction->supprimerReaction($reactionId);
            $this->repondreJson(['succes' => true]);
        } catch (Exception $e) {
            $this->repondreJson(['erreur' => $e->getMessage()], 400);
        }
    }

    public function obtenirStatistiquesReactions($articleId = null, $commentaireId = null) {
        try {
            if ($articleId && !filter_var($articleId, FILTER_VALIDATE_INT)) {
                throw new Exception('ID d\'article invalide');
            }
            if ($commentaireId && !filter_var($commentaireId, FILTER_VALIDATE_INT)) {
                throw new Exception('ID de commentaire invalide');
            }
            if (!$articleId && !$commentaireId) {
                throw new Exception('Cible manquante');
            }

            $reactions = $articleId 
                ? $this->modeleReaction->obtenirReactionsParArticle($articleId)
                : $this->modeleReaction->obtenirReactionsParCommentaire($commentaireId);

            $this->repondreJson(['reactions' => $reactions]);
        } catch (Exception $e) {
            $this->repondreJson(['erreur' => $e->getMessage()], 400);
        }
    }

    private function repondreJson($donnees, $codeStatut = 200) {
        http_response_code($codeStatut);
        header('Content-Type: application/json');
        echo json_encode($donnees);
        exit;
    }
}
?>