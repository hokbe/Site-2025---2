<?php
namespace Models;

class Users {
    private $db;
    
    public function __construct() {
        try {
            require_once 'config/config.php';
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
            $this->db = new \PDO($dsn, DB_USER, DB_PASS, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]);
        } catch (\PDOException $e) {
            error_log("Erreur de connexion : " . $e->getMessage());
            die('Erreur interne, veuillez réessayer plus tard.');
        }
    }
    
    // Récupérer un utilisateur par son ID
    public function getUserById($id) {
        $query = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $query->execute([':id' => $id]);
        return $query->fetch();
    }
    public function getAllUsers() {
        try {
            $query = $this->db->query("SELECT * FROM users ORDER BY username ASC");
            return $query->fetchAll();
        } catch (\PDOException $e) {
            error_log("Erreur getAllUsers: " . $e->getMessage());
            return [];
        }
    }
    // Récupérer un utilisateur par email ou username 
    public function getUserByIdentifier($identifier) {
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return $this->getUserByEmail($identifier);
        } else {
            return $this->getUserByUsername($identifier);
        }
    }
    
    public function getUserByEmail($email) {
        $query = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $query->execute([':email' => $email]);
        return $query->fetch();
    }
    
    public function getUserByUsername($username) {
        $query = $this->db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $query->execute([':username' => $username]);
        return $query->fetch();
    }
   // Vérifie si un utilisateur existe par email ou par nom d'utilisateur
    public function getUserByEmailOrUsername($email, $username) {
        $user = $this->getUserByIdentifier($email);
        if (!$user) {
            $user = $this->getUserByIdentifier($username);
        }
        return $user;
    }

    // Crée un nouvel utilisateur
    public function createUser(
        $firstname,
        $lastname,
        $username,
        $email,
        $password,
        $address,
        $city,
        $zipcode,
        $country
    ) {
        try {
            $query = $this->db->prepare(
                "INSERT INTO users
                (firstname, lastname, username, email, password, address, city, zipcode, id_countries, role, created_at)
                VALUES (:firstname, :lastname, :username, :email, :password, :address, :city, :zipcode, :id_countries, 'user', NOW())"
            );
            return $query->execute([
                ':firstname'    => htmlspecialchars($firstname),
                ':lastname'     => htmlspecialchars($lastname),
                ':username'     => htmlspecialchars($username),
                ':email'        => filter_var($email, FILTER_SANITIZE_EMAIL),
                ':password'     => $password,
                ':address'      => htmlspecialchars($address),
                ':city'         => htmlspecialchars($city),
                ':zipcode'      => htmlspecialchars($zipcode),
                ':id_countries' => intval($country)
            ]);
        } catch (\PDOException $e) {
            error_log("Erreur createUser: " . $e->getMessage());
            return false;
        }
    }
    // Mettre à jour les informations de l'utilisateur (sauf le mot de passe)
    public function updateUser($id, $data) {
        try {
            $query = $this->db->prepare("
                UPDATE users 
                SET firstname = :firstname, 
                    lastname = :lastname, 
                    username = :username, 
                    email = :email, 
                    address = :address, 
                    zipcode = :zipcode, 
                    city = :city, 
                    id_countries = :id_countries
                WHERE id = :id
            ");
            return $query->execute([
                ':firstname'    => htmlspecialchars($data['firstname']),
                ':lastname'     => htmlspecialchars($data['lastname']),
                ':username'     => htmlspecialchars($data['username']),
                ':email'        => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
                ':address'      => htmlspecialchars($data['address']),
                ':zipcode'      => htmlspecialchars($data['zipcode']),
                ':city'         => htmlspecialchars($data['city']),
                ':id_countries' => intval($data['id_countries']),
                ':id'           => intval($id)
            ]);
        } catch (\PDOException $e) {
            error_log("Erreur updateUser: " . $e->getMessage());
            return false;
        }
    }
    
    // Supprimer un utilisateur (utilisé pour la suppression de compte)
    public function deleteUser($id) {
        try {
            $query = $this->db->prepare("DELETE FROM users WHERE id = ?");
            return $query->execute([$id]);
        } catch (\PDOException $e) {
            error_log("Erreur deleteUser: " . $e->getMessage());
            return false;
        }
    }
}
