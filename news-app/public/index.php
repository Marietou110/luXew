<?php
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/ArticleController.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/CommentController.php';
require_once __DIR__ . '/../controllers/UserController.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $articleController = new ArticleController();
    $authController = new AuthController();
    $commentController = new CommentController();
    $userController = new UserController();

    $basePath = '/luXew/news-app/public';
    $requestUri = str_replace($basePath, '', $_SERVER['REQUEST_URI']);
    $requestUri = strtok($requestUri, '?');

    switch (true) {
        case preg_match('/^\/articles$/', $requestUri):
            $articleController->index();
            break;

        case preg_match('/^\/articles\/create$/', $requestUri):
            $articleController->create();
            break;

        case preg_match('/^\/articles\/edit\/(\d+)$/', $requestUri, $matches):
            $articleController->edit($matches[1]);
            break;

        case preg_match('/^\/articles\/show\/(\d+)$/', $requestUri, $matches):
            $articleController->show($matches[1]);
            break;

        case preg_match('/^\/login$/', $requestUri):
            $authController->login();
            break;

        case preg_match('/^\/register$/', $requestUri):
            $authController->register();
            break;

        case preg_match('/^\/logout$/', $requestUri):
            $authController->logout();
            break;

        default:
            $articleController->index();
            break;
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    include __DIR__ . '/../views/errors/500.php';
}
?>