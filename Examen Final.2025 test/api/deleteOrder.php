<?php
session_start();
    header("Content-Type: application/json");
    require_once '../config/config.php';
    require_once '../models/Database.php';
    require_once '../models/Order.php';
    
    if (!isset($_GET['order_id'])) {
        echo json_encode(['success' => false, 'message' => 'Order ID missing']);
        exit;
    }
    
    $orderId = intval($_GET['order_id']);
    $orderModel = new \Models\Order();
    $order = $orderModel->getOrderById($orderId);
    
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit;
    }
    
    if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }
    
    $userId = $_SESSION['user']['id'];
    $isAdmin = ($_SESSION['user']['role'] === 'admin');
    
    if (!$isAdmin && $order['user_id'] != $userId) {
        echo json_encode(['success' => false, 'message' => 'Not authorized']);
        exit;
    }
    
    if ($orderModel->deleteOrder($orderId)) {
        echo json_encode(['success' => true, 'message' => 'Order deleted']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Deletion failed']);
}
