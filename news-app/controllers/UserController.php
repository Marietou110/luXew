<?php
require_once '../models/User.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function profile($userId = null) {
        $userId = $userId ?? $_SESSION['user_id'];

        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php?error=unauthorized');
            exit();
        }

        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            header('Location: index.php?error=user_not_found');
            exit();
        }

        $articles = $this->userModel->getUserArticles($userId);
        $comments = $this->userModel->getUserComments($userId);
        $followers = $this->userModel->getFollowers($userId);
        $following = $this->userModel->getFollowing($userId);

        require_once '../views/users/profile.php';
    }

    public function updateProfile() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php?error=unauthorized');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            
            $pseudo = filter_input(INPUT_POST, 'pseudo', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            $existingUser = $this->userModel->getUserByEmail($email);
            if ($existingUser && $existingUser['id'] !== $userId) {
                header('Location: profile.php?error=email_exists');
                exit();
            }

            $existingUser = $this->userModel->getUserByPseudo($pseudo);
            if ($existingUser && $existingUser['id'] !== $userId) {
                header('Location: profile.php?error=pseudo_exists');
                exit();
            }

            $updateData = [
                'pseudo' => $pseudo,
                'email' => $email
            ];

            if (!empty($currentPassword)) {
                $user = $this->userModel->getUserById($userId);
                
                if (!password_verify($currentPassword, $user['mot_de_passe'])) {
                    header('Location: profile.php?error=invalid_password');
                    exit();
                }

                if ($newPassword !== $confirmPassword) {
                    header('Location: profile.php?error=password_mismatch');
                    exit();
                }

                $updateData['mot_de_passe'] = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            if ($this->userModel->updateUser($userId, $updateData)) {
                header('Location: profile.php?success=profile_updated');
            } else {
                header('Location: profile.php?error=update_failed');
            }
            exit();
        }

        require_once '../views/users/edit_profile.php';
    }

    public function deleteUser($userId) {
        if (!isset($_SESSION['user_id']) || 
            ($_SESSION['user_id'] !== $userId && 
             !in_array('admin', $_SESSION['user_roles']))) {
            header('Location: index.php?error=unauthorized');
            exit();
        }

        if ($this->userModel->deleteUser($userId)) {
            if ($_SESSION['user_id'] === $userId) {
                session_destroy();
                header('Location: index.php?success=account_deleted');
            } else {
                header('Location: users.php?success=user_deleted');
            }
        } else {
            header('Location: profile.php?error=delete_failed');
        }
        exit();
    }

    public function follow($userId) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php?error=unauthorized');
            exit();
        }

        $followerId = $_SESSION['user_id'];

        if ($userId === $followerId) {
            header('Location: profile.php?error=cannot_self_follow');
            exit();
        }

        if ($this->userModel->followUser($followerId, $userId)) {
            header('Location: ' . $_SERVER['HTTP_REFERER'] . '?success=user_followed');
        } else {
            header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=follow_failed');
        }
        exit();
    }

    public function unfollow($userId) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php?error=unauthorized');
            exit();
        }

        $followerId = $_SESSION['user_id'];

        if ($this->userModel->unfollowUser($followerId, $userId)) {
            header('Location: ' . $_SERVER['HTTP_REFERER'] . '?success=user_unfollowed');
        } else {
            header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=unfollow_failed');
        }
        exit();
    }
}