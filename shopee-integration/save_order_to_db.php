<?php
ob_start(); // 🔧 Aktifkan output buffering
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

        $product_title = $item['item_name'];
        $variation = $item['model_name'];
        $price = $order['total_amount'];
        $qty = intval($item['model_quantity_purchased']);
        $status = $order['order_status'];
        $shipping_provider = $order['shipping_carrier'] ?? 'Tidak diketahui';
        $order_date = date('Y-m-d H:i:s', $order['update_time']);
        $product_image = $item['image_info']['image_url'];
        $buyer_note = $order['message_to_seller'] ?? '';

        // Potong jika panjangnya lebih dari 200 karakter
        if (strlen($buyer_note) > 250) {
        $buyer_note = substr($buyer_note, 0, 240) . '...';
}
        // Cek apakah order sudah ada
        $cek = $conn->prepare("SELECT status FROM orders WHERE order_id = ?");
        $cek->bind_param("s", $order_id);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            $cek->bind_result($existing_status);
            $cek->fetch();

            if ($existing_status !== $status) {
                // Status berbeda, lakukan update
                $update = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
                $update->bind_param("ss", $status, $order_id);
                if ($update->execute()) {
                    echo "Order {$order_id} dari $filename: status diperbarui dari '{$existing_status}' ke '{$status}'.<br>";
                } else {
                    echo "Gagal mengupdate status order {$order_id} dari $filename.<br>";
                }
                $update->close();
            } else {
                echo "Order {$order_id} dari $filename sudah ada dengan status sama, dilewati.<br>";
            }

            $cek->close();
            continue;
        }

        $cek->close();

        // Order belum ada, lakukan insert
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

// Redirect jika tidak ada error output sebelum ini
if (!headers_sent()) {
    header("Location: save_saldo_to_db.php");
    ob_end_flush();
    exit;
} else {
    echo "<br><br>⚠️ Tidak bisa redirect ke save_saldo_to_db.php karena sudah ada output sebelumnya.";
}
?>