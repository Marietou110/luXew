<?php
require_once '../config/database.php';

class Article {
    private $db;
    private $id;
    private $titre;
    private $contenu;
    private $dateCreation;
    private $dateModification;
    private $auteur_id;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getId() { return $this->id; }
    public function getTitre() { return $this->titre; }
    public function getContenu() { return $this->contenu; }
    public function getDateCreation() { return $this->dateCreation; }
    public function getDateModification() { return $this->dateModification; }
    public function getAuteurId() { return $this->auteur_id; }

    public function setTitre($titre) { $this->titre = $titre; }
    public function setContenu($contenu) { $this->contenu = $contenu; }
    public function setAuteurId($auteur_id) { $this->auteur_id = $auteur_id; }

    public function getAllArticles() {
        $query = "SELECT a.*, u.pseudo as auteur_pseudo 
                 FROM Article a 
                 LEFT JOIN Utilisateur u ON a.auteur_id = u.id 
                 ORDER BY a.dateCreation DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getArticleById($id) {
        $query = "SELECT a.*, u.pseudo as auteur_pseudo 
                 FROM Article a 
                 LEFT JOIN Utilisateur u ON a.auteur_id = u.id 
                 WHERE a.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createArticle($data) {
        $query = "INSERT INTO Article (titre, contenu, auteur_id) 
                 VALUES (:titre, :contenu, :auteur_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':titre', $data['titre'], PDO::PARAM_STR);
        $stmt->bindParam(':contenu', $data['contenu'], PDO::PARAM_STR);
        $stmt->bindParam(':auteur_id', $data['auteur_id'], PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function updateArticle($id, $data) {
        $query = "UPDATE Article 
                 SET titre = :titre, 
                     contenu = :contenu, 
                     dateModification = CURRENT_TIMESTAMP 
                 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':titre', $data['titre'], PDO::PARAM_STR);
        $stmt->bindParam(':contenu', $data['contenu'], PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function deleteArticle($id) {
        $this->deleteArticleCategories($id);
        $this->deleteArticleComments($id);
        $this->deleteArticleImages($id);
        
        $query = "DELETE FROM Article WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getArticleCategories($articleId) {
        $query = "SELECT c.* 
                 FROM Categorie c 
                 INNER JOIN ArticleCategorie ac ON c.id = ac.categorie_id 
                 WHERE ac.article_id = :article_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':article_id', $articleId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addArticleCategory($articleId, $categoryId) {
        $query = "INSERT INTO ArticleCategorie (article_id, categorie_id) 
                 VALUES (:article_id, :categorie_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':article_id', $articleId, PDO::PARAM_INT);
        $stmt->bindParam(':categorie_id', $categoryId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteArticleCategories($articleId) {
        $query = "DELETE FROM ArticleCategorie WHERE article_id = :article_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':article_id', $articleId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getArticleComments($articleId) {
        $query = "SELECT c.*, u.pseudo as auteur_pseudo 
                 FROM Commentaire c 
                 LEFT JOIN Utilisateur u ON c.utilisateur_id = u.id 
                 WHERE c.article_id = :article_id 
                 ORDER BY c.dateCreation DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':article_id', $articleId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function deleteArticleComments($articleId) {
        $query = "DELETE FROM Commentaire WHERE article_id = :article_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':article_id', $articleId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getArticleImages($articleId) {
        $query = "SELECT * FROM ImageArticle WHERE article_id = :article_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':article_id', $articleId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function deleteArticleImages($articleId) {
        $query = "DELETE FROM ImageArticle WHERE article_id = :article_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':article_id', $articleId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}