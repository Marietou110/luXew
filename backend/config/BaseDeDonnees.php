<?php

class BaseDeDonnees {
    private static $instance = null;
    private $connexion;

    private $hote = 'localhost';
    private $nomUtilisateur = 'massina';
    private $motDePasse = 'passer';
    private $nomBase = 'Luxew';
    private $charset = 'utf8mb4';

    private function __construct() {
        try {
            $dsn = "mysql:host={$this->hote};dbname={$this->nomBase};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}"
            ];
            $this->connexion = new PDO($dsn, $this->nomUtilisateur, $this->motDePasse, $options);
        } catch (PDOException $e) {
            throw new Exception("Échec de la connexion : " . $e->getMessage());
        }
    }

    private function __clone() {}
    private function __wakeup() {}

    public static function obtenirInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function obtenirConnexion() {
        return $this->connexion;
    }
}
?>