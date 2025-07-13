<?php
require_once '../config/BaseDeDonnees.php';

class ModeleReaction {
    private $db;

    public function __construct() {
        $this->db = BaseDeDonnees::obtenirInstance()->obtenirConnexion();
    }

    public function ajouterReaction($donnees) {
        try {
            $query = "INSERT INTO Reaction (utilisateurId, articleId, commentaireId, type) 
                      VALUES (:utilisateurId, :articleId, :commentaireId, :type)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':utilisateurId', $donnees['utilisateurId'], PDO::PARAM_INT);
            $stmt->bindParam(':articleId', $donnees['articleId'], PDO::PARAM_INT, $donnees['articleId'] ?? null);
            $stmt->bindParam(':commentaireId', $donnees['commentaireId'], PDO::PARAM_INT, $donnees['commentaireId'] ?? null);
            $stmt->bindParam(':type', $donnees['type'], PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'ajout de la réaction : " . $e->getMessage());
        }
    }

    public function supprimerReaction($reactionId) {
        try {
            $query = "DELETE FROM Reaction WHERE id = :reactionId";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':reactionId', $reactionId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression de la réaction : " . $e->getMessage());
        }
    }

    public function obtenirReactionsParArticle($articleId) {
        try {
            $query = "SELECT type, COUNT(*) as nombre 
                      FROM Reaction 
                      WHERE articleId = :articleId 
                      GROUP BY type";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':articleId', $articleId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des réactions : " . $e->getMessage());
        }
    }

    public function obtenirReactionsParCommentaire($commentaireId) {
        try {
            $query = "SELECT type, COUNT(*) as nombre 
                      FROM Reaction 
                      WHERE commentaireId = :commentaireId 
                      GROUP BY type";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':commentaireId', $commentaireId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des réactions : " . $e->getMessage());
        }
    }
}
?>