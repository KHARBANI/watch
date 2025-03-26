<?php
session_start();
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['product_id'])) {
    $_SESSION['product_id'] = $data['product_id'];
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Product ID not provided']);
}
?>
