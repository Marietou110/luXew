<?php
require_once '../config/database.php';

class Comment {
    private $db;
    private $id;
    private $contenu;
    private $utilisateur_id;
    private $article_id;
    private $parent_id;
    private $dateCreation;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getId() { return $this->id; }
    public function getContenu() { return $this->contenu; }
    public function getUtilisateurId() { return $this->utilisateur_id; }
    public function getArticleId() { return $this->article_id; }
    public function getParentId() { return $this->parent_id; }
    public function getDateCreation() { return $this->dateCreation; }

    public function setContenu($contenu) { $this->contenu = $contenu; }
    public function setUtilisateurId($utilisateur_id) { $this->utilisateur_id = $utilisateur_id; }
    public function setArticleId($article_id) { $this->article_id = $article_id; }
    public function setParentId($parent_id) { $this->parent_id = $parent_id; }

    public function createComment($data) {
        $query = "INSERT INTO Commentaire (contenu, utilisateur_id, article_id, parent_id) 
                 VALUES (:contenu, :utilisateur_id, :article_id, :parent_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':contenu', $data['contenu'], PDO::PARAM_STR);
        $stmt->bindParam(':utilisateur_id', $data['utilisateur_id'], PDO::PARAM_INT);
        $stmt->bindParam(':article_id', $data['article_id'], PDO::PARAM_INT);
        $stmt->bindParam(':parent_id', $data['parent_id'], PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function getCommentById($id) {
        $query = "SELECT c.*, u.pseudo as auteur_pseudo 
                 FROM Commentaire c 
                 LEFT JOIN Utilisateur u ON c.utilisateur_id = u.id 
                 WHERE c.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateComment($id, $contenu) {
        $query = "UPDATE Commentaire SET contenu = :contenu WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':contenu', $contenu, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function deleteComment($id) {
        $this->deleteCommentReactions($id);
        
        $this->deleteCommentReplies($id);
        
        $query = "DELETE FROM Commentaire WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getReplies($commentId) {
        $query = "SELECT c.*, u.pseudo as auteur_pseudo 
                 FROM Commentaire c 
                 LEFT JOIN Utilisateur u ON c.utilisateur_id = u.id 
                 WHERE c.parent_id = :parent_id 
                 ORDER BY c.dateCreation ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':parent_id', $commentId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function deleteCommentReplies($commentId) {
        $query = "DELETE FROM Commentaire WHERE parent_id = :parent_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':parent_id', $commentId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getCommentReactions($commentId) {
        $query = "SELECT r.*, u.pseudo as utilisateur_pseudo 
                 FROM Reaction r 
                 LEFT JOIN Utilisateur u ON r.utilisateur_id = u.id 
                 WHERE r.commentaire_id = :commentaire_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':commentaire_id', $commentId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function deleteCommentReactions($commentId) {
        $query = "DELETE FROM Reaction WHERE commentaire_id = :commentaire_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':commentaire_id', $commentId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getUserComments($userId) {
        $query = "SELECT c.*, a.titre as article_titre 
                 FROM Commentaire c 
                 LEFT JOIN Article a ON c.article_id = a.id 
                 WHERE c.utilisateur_id = :utilisateur_id 
                 ORDER BY c.dateCreation DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':utilisateur_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}