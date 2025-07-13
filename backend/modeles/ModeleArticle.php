<?php
require_once '../config/BaseDeDonnees.php';

class ModeleArticle {
    private $db;

    public function __construct() {
        $this->db = BaseDeDonnees::obtenirInstance()->obtenirConnexion();
    }

    public function obtenirArticlesPagine($offset, $limite) {
        try {
            $query = "SELECT a.*, u.pseudo as auteurPseudo 
                      FROM Article a 
                      LEFT JOIN Utilisateur u ON a.auteurId = u.id 
                      ORDER BY a.dateCreation DESC 
                      LIMIT :limite OFFSET :offset";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des articles : " . $e->getMessage());
        }
    }

    public function obtenirArticleParId($id) {
        try {
            $query = "SELECT a.*, u.pseudo as auteurPseudo 
                      FROM Article a 
                      LEFT JOIN Utilisateur u ON a.auteurId = u.id 
                      WHERE a.id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $article = $stmt->fetch();
            if ($article) {
                $article['medias'] = $this->obtenirMediasArticle($id);
            }
            return $article;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération de l'article : " . $e->getMessage());
        }
    }

    public function creerArticle($donnees) {
        try {
            $query = "INSERT INTO Article (titre, contenu, auteurId) 
                      VALUES (:titre, :contenu, :auteurId)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':titre', $donnees['titre'], PDO::PARAM_STR);
            $stmt->bindParam(':contenu', $donnees['contenu'], PDO::PARAM_STR);
            $stmt->bindParam(':auteurId', $donnees['auteurId'], PDO::PARAM_INT);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la création de l'article : " . $e->getMessage());
        }
    }

    public function ajouterCategorieArticle($articleId, $categorieId) {
        try {
            $query = "INSERT INTO ArticleCategorie (articleId, categorieId) 
                      VALUES (:articleId, :categorieId)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':articleId', $articleId, PDO::PARAM_INT);
            $stmt->bindParam(':categorieId', $categorieId, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'association de la catégorie : " . $e->getMessage());
        }
    }

    public function ajouterMedia($donnees) {
        try {
            $query = "INSERT INTO Media (articleId, type, url, description) 
                      VALUES (:articleId, :type, :url, :description)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':articleId', $donnees['articleId'], PDO::PARAM_INT);
            $stmt->bindParam(':type', $donnees['type'], PDO::PARAM_STR);
            $stmt->bindParam(':url', $donnees['url'], PDO::PARAM_STR);
            $stmt->bindParam(':description', $donnees['description'], PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'ajout du média : " . $e->getMessage());
        }
    }

    public function obtenirMediasArticle($articleId) {
        try {
            $query = "SELECT * FROM Media WHERE articleId = :articleId";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':articleId', $articleId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des médias : " . $e->getMessage());
        }
    }

    public function obtenirCommentairesArticle($articleId) {
        try {
            $query = "SELECT c.*, u.pseudo as auteurPseudo 
                      FROM Commentaire c 
                      LEFT JOIN Utilisateur u ON c.utilisateurId = u.id 
                      WHERE c.articleId = :articleId 
                      ORDER BY c.dateCreation ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':articleId', $articleId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des commentaires : " . $e->getMessage());
        }
    }
}
?>