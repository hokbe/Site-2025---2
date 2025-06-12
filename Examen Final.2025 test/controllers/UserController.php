<?php
namespace Controllers;

class UserController {

    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identifier = trim($_POST['identifier']);
            $password = trim($_POST['password']);

            if (empty($identifier) || empty($password)) {
                $this->render('login.phtml', ['errorMessage' => "Tous les champs sont requis."]);
                return;
            }

            $userModel = new \Models\Users();
            // Récupérer l'utilisateur par email ou par username
            $user = $userModel->getUserByIdentifier($identifier);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id'       => $user['id'],
                    'username' => htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'),
                    'email'    => htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'),
                    'role'     => htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8')
                ];
                session_regenerate_id(true);
                $this->loadUserCart();
                if ($_SESSION['user']['role'] === 'admin') {
                    header("Location: index.php?route=admin");
                    exit();
                } else {
                    header("Location: index.php?route=home");
                    exit();
                }
            } else {
                $this->render('login.phtml', ['errorMessage' => "Identifiant ou mot de passe incorrect."]);
            }
        } else {
            $this->render('login.phtml');
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        session_unset();
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
        header("Location: index.php?route=login");
        exit();
    }

    // Affiche le profil de l'utilisateur connecté
    public function profile() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?route=login");
            exit();
        }
        $userModel = new \Models\Users();
        $user = $userModel->getUserById($_SESSION['user']['id']);
        $this->render('profile.phtml', ['user' => $user]);
    }

    // Permet à l'admin de voir le profil complet d'un utilisateur (incluant le nom du pays)
    public function viewUserProfile() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header("Location: index.php?route=home");
            exit();
        }
        if (!isset($_GET['userId']) || !is_numeric($_GET['userId'])) {
            header("Location: index.php?route=admin");
            exit();
        }
        $userId = intval($_GET['userId']);
        $userModel = new \Models\Users();
        $user = $userModel->getUserById($userId);
        if (!$user) {
            $this->render('error.phtml', ['errorMessage' => "Utilisateur introuvable"]);
            return;
        }
        // Récupérer le nom du pays via le modèle Countries
        $countryModel = new \Models\Countries();
        $country = $countryModel->getCountryById($user['id_countries']);
        $user['country_name'] = $country ? $country['name'] : 'Non spécifié';
        $this->render('userProfile.phtml', ['user' => $user]);
    }

    // Permet à l'utilisateur de modifier son profil (sauf le mot de passe)
    public function editProfile() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?route=login");
            exit();
        }
        $userModel = new \Models\Users();
        $user = $userModel->getUserById($_SESSION['user']['id']);
        
        // Charger la liste des pays depuis le modèle Countries
        $countryModel = new \Models\Countries();
        $countries = $countryModel->getAllCountries();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et nettoyer les données du formulaire (pas de mot de passe ici)
            $data = [
                'firstname'    => trim($_POST['firstname']),
                'lastname'     => trim($_POST['lastname']),
                'username'     => trim($_POST['username']),
                'email'        => trim($_POST['email']),
                'address'      => trim($_POST['address']),
                'zipcode'      => trim($_POST['zipcode']),
                'city'         => trim($_POST['city']),
                'id_countries' => intval($_POST['id_countries'])
            ];
            $result = $userModel->updateUser($_SESSION['user']['id'], $data);
            if ($result) {
                // Mettre à jour la session si nécessaire
                $_SESSION['user']['username'] = htmlspecialchars($data['username']);
                $_SESSION['user']['email'] = htmlspecialchars($data['email']);
                header("Location: index.php?route=profile");
                exit();
            } else {
                $errorMessage = "Une erreur est survenue lors de la mise à jour.";
                $this->render('editProfile.phtml', ['user' => $user, 'countries' => $countries, 'errorMessage' => $errorMessage]);
                return;
            }
        } else {
            $this->render('editProfile.phtml', ['user' => $user, 'countries' => $countries]);
        }
    }

    // Fonction pour charger le panier de l'utilisateur depuis la base
    public function loadUserCart() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['user'])) {
            $userId = $_SESSION['user']['id'];
            $cartModel = new \Models\Cart();
            $cartItems = $cartModel->getCartByUserId($userId);
            
            $_SESSION['cart'] = [];
            $articleModel = new \Models\Articles();
            foreach ($cartItems as $item) {
                $article = $articleModel->getOneArticle($item['article_id']);
                if ($article) {
                    $_SESSION['cart'][$item['article_id']] = [
                        'id'       => $article['id'],
                        'title'    => $article['title'],
                        'price'    => $article['price'],
                        'image'    => $article['image'],
                        'alt'      => $article['alt'],
                        'quantity' => $item['quantity']
                    ];
                }
            }
        }
    }
    public function deleteAccount() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] === 'admin') {
            header("Location: index.php?route=home");
            exit();
        }

        $userModel = new \Models\Users();
        $userModel->deleteUser($_SESSION['user']['id']);

        $_SESSION = [];
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
        header("Location: index.php?route=home");
        exit();
    }
    
    // Méthode de rendu pour inclure la vue dans le layout
    private function render($template, $data = []) {
        extract($data);
        include_once 'views/layout.phtml';
    }
}
