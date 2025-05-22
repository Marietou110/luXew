<?php
require_once '../models/Reaction.php';

class ReactionController {
    private $reactionModel;

    public function __construct() {
        $this->reactionModel = new Reaction();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function addReaction() {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['error' => 'unauthorized'], 401);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'method_not_allowed'], 405);
            return;
        }

        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
        $articleId = filter_input(INPUT_POST, 'article_id', FILTER_VALIDATE_INT);
        $commentId = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);

        if (!in_array($type, ['like', 'unlike']) || (!$articleId && !$commentId)) {
            $this->jsonResponse(['error' => 'invalid_input'], 400);
            return;
        }

        $data = [
            'utilisateur_id' => $_SESSION['user_id'],
            'article_id' => $articleId,
            'commentaire_id' => $commentId,
            'type' => $type
        ];

        if ($this->reactionModel->addReaction($data)) {
            $this->jsonResponse(['success' => true]);
        } else {
            $this->jsonResponse(['error' => 'reaction_failed'], 500);
        }
    }

    public function removeReaction() {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['error' => 'unauthorized'], 401);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'method_not_allowed'], 405);
            return;
        }

        $articleId = filter_input(INPUT_POST, 'article_id', FILTER_VALIDATE_INT);
        $commentId = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);

        if (!$articleId && !$commentId) {
            $this->jsonResponse(['error' => 'invalid_input'], 400);
            return;
        }

        if ($this->reactionModel->removeReaction($_SESSION['user_id'], $articleId, $commentId)) {
            $this->jsonResponse(['success' => true]);
        } else {
            $this->jsonResponse(['error' => 'removal_failed'], 500);
        }
    }

    public function getReactions($targetType, $targetId) {
        if (!in_array($targetType, ['article', 'comment'])) {
            $this->jsonResponse(['error' => 'invalid_target_type'], 400);
            return;
        }

        $reactions = $targetType === 'article' 
            ? $this->reactionModel->getArticleReactions($targetId)
            : $this->reactionModel->getCommentReactions($targetId);

        $this->jsonResponse([
            'likes' => count(array_filter($reactions, fn($r) => $r['type'] === 'like')),
            'unlikes' => count(array_filter($reactions, fn($r) => $r['type'] === 'unlike')),
            'userReaction' => $this->getUserReaction($targetType, $targetId)
        ]);
    }

    private function getUserReaction($targetType, $targetId) {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        return $this->reactionModel->getUserReaction(
            $_SESSION['user_id'],
            $targetType === 'article' ? $targetId : null,
            $targetType === 'comment' ? $targetId : null
        );
    }

    private function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}