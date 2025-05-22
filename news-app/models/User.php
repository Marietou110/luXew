<?php
require_once '../config/database.php';

class User {
    private $db;
    private $id;
    private $pseudo;
    private $email;
    private $mot_de_passe;
    private $dateInscription;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getId() { return $this->id; }
    public function getPseudo() { return $this->pseudo; }
    public function getEmail() { return $this->email; }
    public function getDateInscription() { return $this->dateInscription; }

    public function createUser($data) {
        $query = "INSERT INTO Utilisateur (pseudo, email, mot_de_passe) 
                 VALUES (:pseudo, :email, :mot_de_passe)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':pseudo', $data['pseudo'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
        $stmt->bindParam(':mot_de_passe', $data['mot_de_passe'], PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function getUserById($id) {
        $query = "SELECT * FROM Utilisateur WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByEmail($email) {
        $query = "SELECT * FROM Utilisateur WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByPseudo($pseudo) {
        $query = "SELECT * FROM Utilisateur WHERE pseudo = :pseudo";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUser($id, $data) {
        $query = "UPDATE Utilisateur SET ";
        $params = [];
        
        if (isset($data['pseudo'])) {
            $params[] = "pseudo = :pseudo";
        }
        if (isset($data['email'])) {
            $params[] = "email = :email";
        }
        if (isset($data['mot_de_passe'])) {
            $params[] = "mot_de_passe = :mot_de_passe";
        }
        
        $query .= implode(', ', $params) . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        
        if (isset($data['pseudo'])) {
            $stmt->bindParam(':pseudo', $data['pseudo'], PDO::PARAM_STR);
        }
        if (isset($data['email'])) {
            $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
        }
        if (isset($data['mot_de_passe'])) {
            $stmt->bindParam(':mot_de_passe', $data['mot_de_passe'], PDO::PARAM_STR);
        }
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteUser($id) {
        $this->deleteUserReactions($id);
        
        $this->deleteUserComments($id);
        
        $this->deleteUserArticles($id);
        
        $this->deleteUserRoles($id);
        
        $query = "DELETE FROM Utilisateur WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getUserRoles($userId) {
        $query = "SELECT r.nom FROM Role r 
                 INNER JOIN UtilisateurRole ur ON r.id = ur.role_id 
                 WHERE ur.utilisateur_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function addUserRole($userId, $roleId) {
        $query = "INSERT INTO UtilisateurRole (utilisateur_id, role_id) 
                 VALUES (:user_id, :role_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':role_id', $roleId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function followUser($followerId, $followedId) {
        $query = "INSERT INTO Suivi (suiveur_id, suivi_id) 
                 VALUES (:suiveur_id, :suivi_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':suiveur_id', $followerId, PDO::PARAM_INT);
        $stmt->bindParam(':suivi_id', $followedId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function unfollowUser($followerId, $followedId) {
        $query = "DELETE FROM Suivi 
                 WHERE suiveur_id = :suiveur_id AND suivi_id = :suivi_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':suiveur_id', $followerId, PDO::PARAM_INT);
        $stmt->bindParam(':suivi_id', $followedId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function deleteUserReactions($userId) {
        $query = "DELETE FROM Reaction WHERE utilisateur_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function deleteUserComments($userId) {
        $query = "DELETE FROM Commentaire WHERE utilisateur_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function deleteUserArticles($userId) {
        $query = "DELETE FROM Article WHERE auteur_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function deleteUserRoles($userId) {
        $query = "DELETE FROM UtilisateurRole WHERE utilisateur_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}