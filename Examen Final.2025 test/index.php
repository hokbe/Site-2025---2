<?php
session_start();
require('config/config.php');

spl_autoload_register(function($class) {                          
    require_once lcfirst(str_replace('\\','/', $class)) . '.php'; 
});

$route = $_GET['route'] ?? 'home';

try {
    switch ($route) {
        case 'home':
            $controller = new Controllers\HomeController();
            $controller->display();
            break;
        case 'articles':
            $controller = new Controllers\ArticlesController();
            $controller->display();
            break;
        case 'article':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $controller = new Controllers\ArticlesController();
                $controller->displayOne(intval($_GET['id']));
            } else {
                header('Location: index.php?route=home');
            }
            break;
        case 'addArticle':
            $controller = new Controllers\AdminController();
            $controller->addArticle();
            break;
        case 'editArticle':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $controller = new Controllers\AdminController();
                $controller->editArticle(intval($_GET['id']));
            } else {
                header('Location: index.php?route=admin');
            }
            break;
        case 'updateArticle':
            $controller = new Controllers\AdminController();
            $controller->updateArticle();
            break;
        case 'updateQuantityAjax':
            $controller = new Controllers\CartController();
            $controller->updateQuantityAjax();
            break;
        case 'deleteArticle':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $controller = new Controllers\AdminController();
                $controller->deleteArticle(intval($_GET['id']));
            } else {
                header('Location: index.php?route=admin');
            }
            break;
        case 'deleteUser':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $controller = new Controllers\AdminController();
                $controller->deleteUser(intval($_GET['id']));
            } else {
                header('Location: index.php?route=admin');
            }
            break;
        case 'deleteAccount':
            $controller = new Controllers\UserController();
            $controller->deleteAccount();
            break;   
        case 'viewUserCart':
            if (isset($_GET['userId']) && is_numeric($_GET['userId'])) {
                $controller = new Controllers\AdminController();
                $controller->viewUserCart(intval($_GET['userId']));
            } else {
                header('Location: index.php?route=admin');
            }
            break;
        case 'viewUserProfile':
            $controller = new Controllers\UserController();
            $controller->viewUserProfile();
            break;
        case 'editProfile':
            $controller = new Controllers\UserController();
            $controller->editProfile();
            break;
        case 'changeOrderStatus':
            $controller = new Controllers\AdminController();
            $controller->changeOrderStatus();
            break;
        case 'deleteOrderAjax':
            $controller = new Controllers\AdminController();
            $controller->deleteOrderAjax();
            break;
        case 'books':
            $controller = new Controllers\ArticlesController();
            $controller->displayAllByCategorie(0);
            break;
        case 'movies':
            $controller = new Controllers\ArticlesController();
            $controller->displayAllByCategorie(1);
            break;
        case 'contact':
            $controller = new Controllers\ContactController();
            $controller->displayForm();
            break;
        case 'register':
            $controller = new Controllers\RegisterController();
            $controller->display();
            break;
        case 'confirmation':
            $controller = new Controllers\RegisterController();
            $controller->displayConfirmationRegister();
            break;
        case 'login':
            $controller = new Controllers\UserController();
            $controller->login();
            break;
        case 'admin':
            if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
                $controller = new Controllers\AdminController();
                $controller->displayDashboard();
            } else {
                header('Location: index.php?route=home');
            }
            break;
        case 'logout':
            $controller = new Controllers\UserController();
            $controller->logout();
            break;
        case 'profile':
            $controller = new Controllers\UserController();
            $controller->profile();
            break;
        case 'cart':
            $controller = new Controllers\CartController();
            $controller->display();
            break;
        case 'addToCart':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $quantity = (isset($_GET['quantity']) && is_numeric($_GET['quantity'])) ? intval($_GET['quantity']) : 1;
                $controller = new Controllers\CartController();
                $controller->addToCart(intval($_GET['id']), $quantity);
            } else {
                header('Location: index.php?route=cart');
            }
            break;
        case 'removeFromCart':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $controller = new Controllers\CartController();
                $controller->removeFromCart(intval($_GET['id']));
            } else {
                header('Location: index.php?route=cart');
            }
            break;
        case 'removeFromCartAjax':
            $controller = new Controllers\CartController();
            $controller->removeFromCartAjax();
            break;
        case 'updateQuantity':
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                $quantity = (isset($_POST['quantity']) && is_numeric($_POST['quantity'])) ? intval($_POST['quantity']) : 1;
                $action = $_POST['action'] ?? '';
                $controller = new Controllers\CartController();
                $controller->updateQuantity(intval($_POST['id']), $quantity, $action);
            } else {
                header('Location: index.php?route=cart');
                exit();
            }
            break;
        case 'updateQuantityAjax':
            $controller = new Controllers\CartController();
            $controller->updateQuantityAjax();
            break;
        case 'payment':
            $controller = new Controllers\CartController();
            $controller->payment();
            break;
        case 'orderConfirmation':
            $controller = new Controllers\CartController();
            $controller->orderConfirmation();
            break;
        case 'search':
            if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
                $controller = new Controllers\ArticlesController();
                $controller->search(trim($_GET['query']));
            } else {
                header('Location: index.php?route=articles');
            }
            break;
        default:
            header('Location: index.php?route=home');
            exit();
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    header('Location: index.php?route=error');
}
?>
