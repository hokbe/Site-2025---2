<?php
namespace Models;

class Database {
    protected $bdd;
    
    public function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
            $this->bdd = new \PDO($dsn, DB_USER, DB_PASS, [
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            ]);
        } catch (\PDOException $e) {
            die('Une erreur est survenue lors de la connexion à la base de données.');
        }
    }
    
    protected function findAll(string $req, array $params = []) : array {
        try {
            $query = $this->bdd->prepare($req);
            $query->execute($params);
            return $query->fetchAll();
        } catch (\PDOException $e) {
            $this->logError($e->getMessage());
            $this->redirectToErrorPage();
        }
    }
    
    protected function findOne(string $req, array $params = []) {
        try {
            $query = $this->bdd->prepare($req);
            $query->execute($params);
            return $query->fetch();
        } catch (\PDOException $e) {
            $this->logError($e->getMessage());
            $this->redirectToErrorPage();
        }
    }
    
    protected function logError(string $message) {
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }
        $logFile = $logDir . '/errors.log';
        error_log($message, 3, $logFile);
    }
     protected function redirectToErrorPage() {
     header('Location: index.php?route=error');
     exit();
  }
}
   
