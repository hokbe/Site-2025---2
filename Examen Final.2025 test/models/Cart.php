<?php
namespace Models;

class Cart extends Database {
    public function addOrUpdateItem($userId, $articleId, $quantity) {
        $stmt = $this->bdd->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND article_id = ?");
        $stmt->execute([$userId, $articleId]);
        $existing = $stmt->fetch();
        if ($existing) {
            $newQuantity = $existing['quantity'] + $quantity;
            $stmt = $this->bdd->prepare("UPDATE cart_items SET quantity = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$newQuantity, $existing['id']]);
        } else {
            $stmt = $this->bdd->prepare("INSERT INTO cart_items (user_id, article_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $articleId, $quantity]);
        }
    }
    
    public function removeItem($userId, $articleId) {
        $stmt = $this->bdd->prepare("DELETE FROM cart_items WHERE user_id = ? AND article_id = ?");
        return $stmt->execute([$userId, $articleId]);
    }
    
    public function updateItemQuantity($userId, $articleId, $quantity) {
        $stmt = $this->bdd->prepare("UPDATE cart_items SET quantity = ?, updated_at = NOW() WHERE user_id = ? AND article_id = ?");
        return $stmt->execute([$quantity, $userId, $articleId]);
    }
    
    public function clearCartByUserId($userId) {
        $stmt = $this->bdd->prepare("DELETE FROM cart_items WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
    
    public function getCartByUserId($userId) {
        $stmt = $this->bdd->prepare("
            SELECT ci.article_id, ci.quantity, a.title, a.price, a.image, a.alt
            FROM cart_items ci
            INNER JOIN articles a ON ci.article_id = a.id
            WHERE ci.user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
?>
