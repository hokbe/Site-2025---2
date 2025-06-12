<?php
namespace Controllers;

class CartController {
    public function display() {
        $template = "cart.phtml";
        include_once 'views/layout.phtml';
    }

    public function addToCart($id, $quantity = 1) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['user'])) {
            $userId = $_SESSION['user']['id'];
            $cartModel = new \Models\Cart();
            $cartModel->addOrUpdateItem($userId, $id, $quantity);
        }
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += $quantity;
        } else {
            $model = new \Models\Articles();
            $article = $model->getOneArticle($id);
            if ($article && is_array($article)) {
                $_SESSION['cart'][$id] = [
                    'id'       => $article['id'],
                    'title'    => $article['title'],
                    'price'    => $article['price'],
                    'quantity' => $quantity,
                    'image'    => $article['image'],
                    'alt'      => $article['alt']
                ];
            }
        }
        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?route=cart';
        header("Location: $referer");
        exit();
    }

    public function removeFromCart($id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['user'])) {
            $userId = $_SESSION['user']['id'];
            $cartModel = new \Models\Cart();
            $cartModel->removeItem($userId, $id);
        }
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?route=cart';
        header("Location: $referer");
        exit();
    }
    public function removeFromCartAjax() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : null;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Paramètre manquant']);
                exit;
            }
           
            if (isset($_SESSION['user'])) {
                $userId = $_SESSION['user']['id'];
                $cartModel = new \Models\Cart();
                $cartModel->removeItem($userId, $id); 
            }
         
            if (isset($_SESSION['cart'][$id])) {
                unset($_SESSION['cart'][$id]);
            }
            echo json_encode(['success' => true]);
            exit;
        }
        echo json_encode(['success' => false, 'message' => 'Mauvaise méthode']);
        exit;
    }

    public function updateQuantity($id, $quantity, $action = '') {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['cart'][$id])) {
            $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?route=cart';
            header("Location: $referer");
            exit();
        }
        $currentQuantity = $_SESSION['cart'][$id]['quantity'];
        if ($action === 'increment') {
            $newQuantity = $currentQuantity + 1;
        } elseif ($action === 'decrement') {
            $newQuantity = $currentQuantity - 1;
        } else {
            $newQuantity = $quantity;
        }
        if ($newQuantity <= 0) {
            if (isset($_SESSION['user'])) {
                $userId = $_SESSION['user']['id'];
                $cartModel = new \Models\Cart();
                $cartModel->removeItem($userId, $id);
            }
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id]['quantity'] = $newQuantity;
            if (isset($_SESSION['user'])) {
                $userId = $_SESSION['user']['id'];
                $cartModel = new \Models\Cart();
                $cartModel->updateItemQuantity($userId, $id, $newQuantity);
            }
        }
        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?route=cart';
        header("Location: $referer");
        exit();
    }
   public function updateQuantityAjax() {
        header('Content-Type: application/json');
        session_start();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : null;
            $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : null;
            
            if (!$id || !$quantity) {
                echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
                exit;
            }
            
            // Mise à jour de la quantité dans la session
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['quantity'] = $quantity;
            } else {
                echo json_encode(['success' => false, 'message' => 'Produit non trouvé dans le panier']);
                exit;
            }
            
            // Si l'utilisateur est connecté, mettre à jour aussi la base de données
            if (isset($_SESSION['user'])) {
                $userId = $_SESSION['user']['id'];
                $cartModel = new \Models\Cart();
                $cartModel->updateItemQuantity($userId, $id, $quantity);
            }
            
            // Recalcul du total du panier
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['price'] * $item['quantity'];
            }
            
            // Renvoyer le nouveau total en JSON (formaté avec deux décimales)
            echo json_encode(['success' => true, 'total' => number_format($total, 2)]);
            exit;
        }
        
        echo json_encode(['success' => false, 'message' => 'Mauvaise méthode']);
        exit;
    }


    public function payment() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['cart'])) {
            header("Location: index.php?route=cart");
            exit();
        }
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?route=login");
            exit();
        }
        $userId = $_SESSION['user']['id'];
        $orderModel = new \Models\Order();
        $orderId = $orderModel->createOrder($userId, $_SESSION['cart']);
    
        if ($orderId) {
            unset($_SESSION['cart']);
            header("Location: index.php?route=orderConfirmation");
            exit();
        } else {
            echo "Une erreur est survenue lors du traitement de votre commande.";
        }
    }

    public function orderConfirmation() {
        $template = "orderConfirmation.phtml";
        include_once 'views/layout.phtml';
    }

    public function clearCart() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['user'])) {
            $userId = $_SESSION['user']['id'];
            $cartModel = new \Models\Cart();
            $cartModel->clearCartByUserId($userId);
        }
        unset($_SESSION['cart']);
        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?route=cart';
        header("Location: $referer");
        exit();
    }
}
?>
