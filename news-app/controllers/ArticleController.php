<?php
require_once '../models/Article.php';
require_once '../models/Category.php';

class ArticleController {
    private $articleModel;
    private $categoryModel;

    public function __construct() {
        $this->articleModel = new Article();
        $this->categoryModel = new Category();
    }

    public function index() {
        $articles = $this->articleModel->getAllArticles();
        $categories = $this->categoryModel->getAllCategories();
        
        require_once '../views/articles/index.php';
    }

    public function show($id) {
        $article = $this->articleModel->getArticleById($id);
        
        if (!$article) {
            header('Location: index.php?error=article_not_found');
            exit();
        }
        
        $comments = $this->articleModel->getArticleComments($id);
        
        require_once '../views/articles/show.php';
    }

    public function create() {
        if (!isset($_SESSION['user_id']) || !$this->hasPermission()) {
            header('Location: index.php?error=unauthorized');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
            $content = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING);
            $categoryIds = $_POST['categories'] ?? [];

            if (empty($title) || empty($content)) {
                header('Location: index.php?error=missing_fields');
                exit();
            }

            $articleId = $this->articleModel->createArticle([
                'titre' => $title,
                'contenu' => $content,
                'auteur_id' => $_SESSION['user_id']
            ]);

            if ($articleId) {
                foreach ($categoryIds as $categoryId) {
                    $this->articleModel->addArticleCategory($articleId, $categoryId);
                }
                header('Location: index.php?success=article_created');
                exit();
            }
        }

        $categories = $this->categoryModel->getAllCategories();
        
        require_once '../views/articles/create.php';
    }

    public function edit($id) {
        if (!isset($_SESSION['user_id']) || !$this->hasPermission()) {
            header('Location: index.php?error=unauthorized');
            exit();
        }

        $article = $this->articleModel->getArticleById($id);
        
        if (!$article) {
            header('Location: index.php?error=article_not_found');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
            $content = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING);
            $categoryIds = $_POST['categories'] ?? [];

            if (empty($title) || empty($content)) {
                header('Location: index.php?error=missing_fields');
                exit();
            }

            $success = $this->articleModel->updateArticle($id, [
                'titre' => $title,
                'contenu' => $content,
                'dateModification' => date('Y-m-d H:i:s')
            ]);

            if ($success) {
                $this->articleModel->deleteArticleCategories($id);
                foreach ($categoryIds as $categoryId) {
                    $this->articleModel->addArticleCategory($id, $categoryId);
                }
                header('Location: index.php?success=article_updated');
                exit();
            }
        }

        $categories = $this->categoryModel->getAllCategories();
        $articleCategories = $this->articleModel->getArticleCategories($id);
        
        require_once '../views/articles/edit.php';
    }

    public function delete($id) {
        if (!isset($_SESSION['user_id']) || !$this->hasPermission()) {
            header('Location: index.php?error=unauthorized');
            exit();
        }

        if ($this->articleModel->deleteArticle($id)) {
            header('Location: index.php?success=article_deleted');
        } else {
            header('Location: index.php?error=delete_failed');
        }
        exit();
    }

    private function hasPermission() {
        return isset($_SESSION['user_role']) && 
               in_array($_SESSION['user_role'], ['admin', 'author']);
    }
}