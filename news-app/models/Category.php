<?php

require_once '../config/database.php';

class Category {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllCategories() {
        $query = "SELECT * FROM Categorie ORDER BY libelle";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById($id) {
        $query = "SELECT * FROM Categorie WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCategory($libelle) {
        $query = "INSERT INTO Categorie (libelle) VALUES (:libelle)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':libelle', $libelle, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function updateCategory($id, $libelle) {
        $query = "UPDATE Categorie SET libelle = :libelle WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':libelle', $libelle, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function deleteCategory($id) {
        $checkQuery = "SELECT COUNT(*) FROM ArticleCategorie WHERE categorie_id = :id";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $checkStmt->execute();
        
        if ($checkStmt->fetchColumn() > 0) {
            return false; 
        }

        $query = "DELETE FROM Categorie WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getCategoryArticles($categoryId) {
        $query = "SELECT a.* FROM Article a 
                 INNER JOIN ArticleCategorie ac ON a.id = ac.article_id 
                 WHERE ac.categorie_id = :category_id 
                 ORDER BY a.dateCreation DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}