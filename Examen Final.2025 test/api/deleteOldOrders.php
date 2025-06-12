<?php

require_once '../config/config.php';
require_once '../models/Database.php';
require_once '../models/Order.php';

$orderModel = new \Models\Order();
$deletedCount = $orderModel->deleteOrdersOlderThan(6);
echo json_encode(['deleted' => $deletedCount]);
?>