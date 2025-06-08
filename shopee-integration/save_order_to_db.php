<?php
ob_start(); // 🔧 Aktifkan buffering di awal
require 'config.php';

if (!isset($conn)) {
    exit("Koneksi database tidak ditemukan. Periksa config.php.");
}

for ($i = 1; $i <= 3; $i++) {
    $filename = "order_detail{$i}.json";

    if (!file_exists($filename)) {
        echo "File $filename tidak ditemukan.<br>";
        continue;
    }

    $json = file_get_contents($filename);
    $data = json_decode($json, true);

    if (!$data || !isset($data['response']['order_list'])) {
        echo "Data pada file $filename tidak valid atau kosong.<br>";
        continue;
    }

    $orderList = $data['response']['order_list'];

    foreach ($orderList as $order) {
        $platform = 'shopee';
        $order_id = $order['order_sn'];
        $username = $order['buyer_username'];
        $item = $order['item_list'][0] ?? null;

        if (!$item) continue;

        $cek = $conn->prepare("SELECT order_id FROM orders WHERE order_id = ?");
        $cek->bind_param("s", $order_id);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            echo "Order {$order_id} dari $filename sudah ada, dilewati.<br>";
            $cek->close();
            continue;
        }
        $cek->close();

        $product_title = $item['item_name'];
        $variation = $item['model_name'];
        $price = $order['total_amount'];
        $qty = intval($item['model_quantity_purchased']);
        $status = $order['order_status'];
        $shipping_provider = $order['shipping_carrier'] ?? 'Tidak diketahui';
        $order_date = date('Y-m-d H:i:s', $order['ship_by_date']);
        $product_image = $item['image_info']['image_url'];
        $buyer_note = $order['message_to_seller'] ?? '';

        $stmt = $conn->prepare("
            INSERT INTO orders (
                platform, order_id, username, product_title, variation, price, qty,
                status, shipping_provider, order_date, product_image, buyer_note
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssssiisssss",
            $platform,
            $order_id,
            $username,
            $product_title,
            $variation,
            $price,
            $qty,
            $status,
            $shipping_provider,
            $order_date,
            $product_image,
            $buyer_note
        );

        if ($stmt->execute()) {
            echo "Order {$order_id} dari $filename berhasil disimpan.<br>";
        } else {
            echo "Gagal menyimpan order {$order_id} dari $filename: " . $stmt->error . "<br>";
        }

        $stmt->close();
    }
}

$conn->close();

header("Location: save_saldo_to_db.php"); // ✅ Sekarang bisa dipanggil karena tidak ada output langsung
ob_end_flush(); // 🔚 Kirim output setelah redirect (tidak error)
exit;
?>
