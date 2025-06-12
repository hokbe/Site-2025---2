<?php
namespace Controllers;

class RegisterController {
    public function display() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $model = new \Models\Countries();
            $countries = $model->getAllCountries();
            $template = "registerForm.phtml";
            include_once 'views/layout.phtml';
        } else {
            if (
                empty(trim($_POST['firstname'])) || 
                empty(trim($_POST['lastname'])) || 
                empty(trim($_POST['username'])) || 
                empty(trim($_POST['email'])) || 
                empty(trim($_POST['password'])) || 
                empty(trim($_POST['country'])) || 
                empty(trim($_POST['address'])) || 
                empty(trim($_POST['zipcode'])) || 
                empty(trim($_POST['city']))
            ) {
                $errorMessage = "Tous les champs doivent être remplis.";
                $template = "registerForm.phtml";
                include_once 'views/layout.phtml';
                return;
            }
            $firstname = htmlspecialchars(trim($_POST['firstname']));
            $lastname  = htmlspecialchars(trim($_POST['lastname']));
            $username  = htmlspecialchars(trim($_POST['username']));
            $email     = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $password  = $_POST['password'];
            $country   = htmlspecialchars(trim($_POST['country']));
            $address   = htmlspecialchars(trim($_POST['address']));
            $zipcode   = htmlspecialchars(trim($_POST['zipcode']));
            $city      = htmlspecialchars(trim($_POST['city']));
            
            $userModel = new \Models\Users();
            $existingUser = $userModel->getUserByEmailOrUsername($email, $username);
            if ($existingUser) {
                $errorMessage = "Cet email ou nom d'utilisateur est déjà utilisé.";
                $template = "registerForm.phtml";
                include_once 'views/layout.phtml';
                return;
            }
            
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            $result = $userModel->createUser(
                $firstname,
                $lastname,
                $username,
                $email,
                $hashedPassword,
                $address,
                $city,
                $zipcode,
                $country
            );
            
            if ($result) {
                header('Location: index.php?route=confirmation');
                exit();
            } else {
                $errorMessage = "Une erreur est survenue lors de l'inscription. Veuillez réessayer.";
                $template = "registerForm.phtml";
                include_once 'views/layout.phtml';
            }
        }
    }
    
    public function displayConfirmationRegister() {
        $template = "registerConfirmation.phtml";
        include_once 'views/layout.phtml';
    }
}
?>
