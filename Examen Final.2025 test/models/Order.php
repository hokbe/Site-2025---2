<?php
namespace Models;

class Order extends Database {
    public function createOrder($userId, $cart) {
        try {
            $this->bdd->beginTransaction();
            
           
            $total = 0;
            foreach ($cart as $item) {
              
                $total += $item['price'] * $item['quantity'];
            }
            $stmt = $this->bdd->prepare("INSERT INTO orders (user_id, total, status, created_at) VALUES (?, ?, 'paid', NOW())");
            $stmt->execute([$userId, $total]);
            $orderId = $this->bdd->lastInsertId();
            
            foreach ($cart as $item) {
                $stmtDetail = $this->bdd->prepare("INSERT INTO order_details (order_id, article_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmtDetail->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
            }
            
            $this->bdd->commit();
            return $orderId;
        } catch (\PDOException $e) {
            $this->bdd->rollBack();
            error_log("Erreur lors de la création de commande : " . $e->getMessage());
            return false;
        }
    }
    
    public function getAllOrders() {
        $sql = "SELECT * FROM orders ORDER BY created_at DESC";
        return $this->findAll($sql);
    }
    
    public function updateOrderStatus($orderId, $status) {
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = $this->bdd->prepare($sql);
        $stmt->execute([$status, $orderId]);
    }
    public function getOrdersByUserId($userId) {
        $stmt = $this->bdd->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    public function deleteOrdersOlderThan($months) {
        $date = date('Y-m-d H:i:s', strtotime("-{$months} months"));
        $stmt = $this->bdd->prepare("DELETE FROM orders WHERE created_at < ?");
        $stmt->execute([$date]);
        return $stmt->rowCount();
    }
    
    public function getOrderById($orderId) {
        $stmt = $this->bdd->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetch();
    }
    
    public function deleteOrder($orderId) {
        $stmt = $this->bdd->prepare("DELETE FROM orders WHERE id = ?");
        return $stmt->execute([$orderId]);
    }
}
?>
