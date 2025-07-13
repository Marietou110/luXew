<?php
require_once '../models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                header('Location: http://localhost/luXew/news-app/public/login?error=missing_fields');
                exit();
            }

            $user = $this->userModel->getUserByEmail($email);

            if ($user && password_verify($password, $user['mot_de_passe'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_pseudo'] = $user['pseudo'];
                
                $roles = $this->userModel->getUserRoles($user['id']);
                $_SESSION['user_roles'] = $roles;
                
                header('Location: index.php?success=login');
                exit();
            } else {
                header('Location: http://localhost/luXew/news-app/public/login?error=invalid_credentials');
                exit();
            }
        }

        require_once '../views/auth/login.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pseudo = filter_input(INPUT_POST, 'pseudo', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($pseudo) || empty($email) || empty($password)) {
                header('Location: http://localhost/luXew/news-app/public/register?error=missing_fields');
                exit();
            }

            if ($password !== $confirmPassword) {
                header('Location: http://localhost/luXew/news-app/public/register?error=password_mismatch');
                exit();
            }

            if ($this->userModel->getUserByEmail($email)) {
                header('Location: http://localhost/luXew/news-app/public/register?error=email_exists');
                exit();
            }

            if ($this->userModel->getUserByPseudo($pseudo)) {
                header('Location: http://localhost/luXew/news-app/public/register?error=pseudo_exists');
                exit();
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $userData = [
                'pseudo' => $pseudo,
                'email' => $email,
                'mot_de_passe' => $hashedPassword
            ];

            if ($this->userModel->createUser($userData)) {
                header('Location: login.php?success=registration');
                exit();
            } else {
                header('Location: http://localhost/luXew/news-app/public/register?error=registration_failed');
                exit();
            }
        }

        require_once '../views/auth/register.php';
    }

    public function logout() {
        session_start();
        session_destroy();
        
        header('Location: login.php?success=logout');
        exit();
    }

    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

            if (empty($email)) {
                header('Location: forgot-password.php?error=missing_email');
                exit();
            }

            $token = bin2hex(random_bytes(32));
            
            if ($this->userModel->setResetToken($email, $token)) {

                header('Location: forgot-password.php?success=reset_sent');
                exit();
            } else {
                header('Location: forgot-password.php?error=invalid_email');
                exit();
            }
        }

        require_once '../views/auth/forgot-password.php';
    }
}