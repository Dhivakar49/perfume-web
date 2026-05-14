<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$sql = "SELECT * FROM products WHERE stock > 0 ORDER BY id DESC";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode(['success' => true, 'products' => $products]);
$conn->close();
?>
