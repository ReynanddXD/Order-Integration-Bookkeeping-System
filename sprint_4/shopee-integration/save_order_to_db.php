<?php
require 'config.php';

if (!isset($conn)) {
    exit("Koneksi database tidak ditemukan. Periksa config.php.");
}

// Baca isi file order detail JSON
$json = file_get_contents('order_detail.json');
$data = json_decode($json, true);

// Cek validitas data
if (!$data || !isset($data['response']['order_list'])) {
    exit('Data order tidak valid atau kosong.');
}

$orderList = $data['response']['order_list'];

foreach ($orderList as $order) {
    $platform = 'shopee';
    $order_id = $order['order_sn'];
    $username = $order['buyer_username'];
    $item = $order['item_list'][0] ?? null;

    if (!$item) continue;

    // Cek apakah order_id sudah ada di database
    $cek = $conn->prepare("SELECT order_id FROM orders WHERE order_id = ?");
    $cek->bind_param("s", $order_id);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        echo "Order {$order_id} sudah ada, dilewati.<br>";
        $cek->close();
        continue; // Lewati proses insert
    }
    $cek->close();

    $product_title = $item['item_name'];
    $variation = $item['model_name'];
    $price = $order['total_amount'];
    $status = $order['order_status'];
    $shipping_provider = $order['shipping_carrier'] ?? 'Tidak diketahui';
    $order_date = date('Y-m-d H:i:s', $order['ship_by_date']);
    $product_image = $item['image_info']['image_url'];
    $buyer_note = $order['message_to_seller'] ?? '';

    // Insert ke database
    $stmt = $conn->prepare("
        INSERT INTO orders (
            platform, order_id, username, product_title, variation, price,
            status, shipping_provider, order_date, product_image, buyer_note
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sssssisssss",
        $platform,
        $order_id,
        $username,
        $product_title,
        $variation,
        $price,
        $status,
        $shipping_provider,
        $order_date,
        $product_image,
        $buyer_note
    );

    if ($stmt->execute()) {
        echo "Order {$order_id} berhasil disimpan.<br>";
    } else {
        echo "Gagal menyimpan order {$order_id}: " . $stmt->error . "<br>";
    }

    $stmt->close();
}

$conn->close();
?>
