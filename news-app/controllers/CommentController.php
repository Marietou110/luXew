<?php
require_once '../models/Comment.php';

class CommentController {
    private $commentModel;

    public function __construct() {
        $this->commentModel = new Comment();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?error=unauthorized');
            exit();
        }

        $comments = $this->commentModel->getCommentsByUser($_SESSION['user_id']);
        require_once '../views/comments/index.php';
    }

    public function show($id) {
        $comment = $this->commentModel->getCommentById($id);
        
        if (!$comment) {
            header('Location: index.php?error=comment_not_found');
            exit();
        }

        $replies = $this->commentModel->getReplies($id);
        require_once '../views/comments/show.php';
    }

    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?error=unauthorized');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING);
            $articleId = filter_input(INPUT_POST, 'article_id', FILTER_VALIDATE_INT);
            $parentId = filter_input(INPUT_POST, 'parent_id', FILTER_VALIDATE_INT) ?: null;

            if (empty($content) || !$articleId) {
                header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=missing_fields');
                exit();
            }

            $commentData = [
                'contenu' => $content,
                'utilisateur_id' => $_SESSION['user_id'],
                'article_id' => $articleId,
                'parent_id' => $parentId
            ];

            if ($this->commentModel->createComment($commentData)) {
                header('Location: ' . $_SERVER['HTTP_REFERER'] . '?success=comment_added');
            } else {
                header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=comment_failed');
            }
            exit();
        }
    }

    public function edit($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?error=unauthorized');
            exit();
        }

        $comment = $this->commentModel->getCommentById($id);

        if (!$comment || $comment['utilisateur_id'] !== $_SESSION['user_id']) {
            header('Location: index.php?error=unauthorized');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING);

            if (empty($content)) {
                header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=missing_content');
                exit();
            }

            if ($this->commentModel->updateComment($id, $content)) {
                header('Location: ' . $_SERVER['HTTP_REFERER'] . '?success=comment_updated');
            } else {
                header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=update_failed');
            }
            exit();
        }

        require_once '../views/comments/edit.php';
    }

    public function delete($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?error=unauthorized');
            exit();
        }

        $comment = $this->commentModel->getCommentById($id);

        if (!$comment || 
            ($comment['utilisateur_id'] !== $_SESSION['user_id'] && 
             !isset($_SESSION['user_role']) && 
             $_SESSION['user_role'] !== 'admin')) {
            header('Location: index.php?error=unauthorized');
            exit();
        }

        if ($this->commentModel->deleteComment($id)) {
            header('Location: ' . $_SERVER['HTTP_REFERER'] . '?success=comment_deleted');
        } else {
            header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=delete_failed');
        }
        exit();
    }
}