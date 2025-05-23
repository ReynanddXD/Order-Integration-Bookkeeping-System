<?php
$host = 'localhost';
$db   = 'intregasi';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $json = file_get_contents('orders_sample.json');
    $orders = json_decode($json, true);

    $stmt = $pdo->prepare("
        INSERT INTO orders (id, platform, order_id, username, product_title, variation, price, status, shipping_provider, order_date, product_image, buyer_note)
        VALUES (:id, :platform, :order_id, :username, :product_title, :variation, :price, :status, :shipping_provider, :order_date, :product_image, :buyer_note)
    ");

    foreach ($orders as $order) {
        $stmt->execute($order);
    }

    echo "Import berhasil. Total data: " . count($orders);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
