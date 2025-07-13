<?php
require_once '../config/BaseDeDonnees.php';

class ModeleCategorie {
    private $db;

    public function __construct() {
        $this->db = BaseDeDonnees::obtenirInstance()->obtenirConnexion();
    }

    public function obtenirToutesCategories() {
        try {
            $query = "SELECT * FROM Categorie ORDER BY libelle ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des catégories : " . $e->getMessage());
        }
    }

    public function obtenirCategorieParId($categorieId) {
        try {
            $query = "SELECT * FROM Categorie WHERE id = :categorieId";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':categorieId', $categorieId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération de la catégorie : " . $e->getMessage());
        }
    }

    public function obtenirArticlesParCategorie($categorieId) {
        try {
            $query = "SELECT a.*, u.pseudo as auteurPseudo 
                      FROM Article a 
                      JOIN ArticleCategorie ac ON a.id = ac.articleId 
                      JOIN Utilisateur u ON a.auteurId = u.id 
                      WHERE ac.categorieId = :categorieId 
                      ORDER BY a.dateCreation DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':categorieId', $categorieId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des articles de la catégorie : " . $e->getMessage());
        }
    }

    public function creerCategorie($donnees) {
        try {
            $query = "INSERT INTO Categorie (libelle) VALUES (:libelle)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':libelle', $donnees['libelle'], PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la création de la catégorie : " . $e->getMessage());
        }
    }

    public function modifierCategorie($categorieId, $donnees) {
        try {
            $query = "UPDATE Categorie SET libelle = :libelle WHERE id = :categorieId";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':libelle', $donnees['libelle'], PDO::PARAM_STR);
            $stmt->bindParam(':categorieId', $categorieId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la modification de la catégorie : " . $e->getMessage());
        }
    }

    public function supprimerCategorie($categorieId) {
        try {
            $query = "DELETE FROM Categorie WHERE id = :categorieId";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':categorieId', $categorieId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression de la catégorie : " . $e->getMessage());
        }
    }
}
?>