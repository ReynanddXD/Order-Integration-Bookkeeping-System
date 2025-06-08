<?php
ob_start(); // Mulai output buffering
require 'config.php';

if (!isset($conn)) {
    exit("Koneksi database tidak ditemukan. Periksa config.php.");
}

// Ambil saldo awal dari database
$sql = "SELECT total_balance FROM saldo WHERE id = 1 LIMIT 1";
$result = $conn->query($sql);
$total = 0;

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total = (float)$row['total_balance'];
} else {
    echo "Saldo tidak ditemukan, nilai awal di-set ke 0.<br>";
}

// Loop file order_detail1.json hingga order_detail3.json
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
        $order_id = $order['order_sn'];
        $item = $order['item_list'][0] ?? null;

        if (!$item) continue;

        $status = strtoupper($order['order_status']);

        if ($status !== 'COMPLETED') {
            echo "Order {$order_id} dari $filename statusnya bukan COMPLETED, dilewati.<br>";
            continue;
        }

        // Cek duplikasi di database
        $cek = $conn->prepare("SELECT order_id FROM transactions WHERE order_id = ?");
        $cek->bind_param("s", $order_id);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            echo "Order {$order_id} dari $filename sudah ada, dilewati.<br>";
            $cek->close();
            continue;
        }
        $cek->close();

        $date = date('Y-m-d H:i:s', $order['update_time']);
        $description = 'shopee';
        $type = 'masuk';
        $price = $item['model_discounted_price'];
        $qty = (int)$item['model_quantity_purchased'];
        $amount = $price * $qty - ($price * $qty * 0.13); // Potong 13%
        $note = '-';

        // Update saldo
        $total += $amount;
        $shopee_balance = $total;
        $profit = $total * 0.25;
        $capital = $total * 0.75;

        // Insert transaksi
        $stmt = $conn->prepare("
            INSERT INTO transactions (
                order_id, date, description, type, amount, note
            ) VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ssssis",
            $order_id,
            $date,
            $description,
            $type,
            $amount,
            $note
        );

        if ($stmt->execute()) {
            echo "Order {$order_id} dari $filename berhasil disimpan.<br>";
        } else {
            echo "Gagal menyimpan order {$order_id} dari $filename: " . $stmt->error . "<br>";
        }

        // Update saldo
        $updateStmt = $conn->prepare("
            UPDATE saldo 
            SET total_balance = ?, 
                shopee_balance = ?, 
                profit = ?, 
                capital = ?
            WHERE id = 1
        ");
        $updateStmt->bind_param("dddd", $total, $shopee_balance, $profit, $capital);
        if ($updateStmt->execute()) {
            echo "Saldo berhasil diperbarui: Rp " . number_format($total, 0, ',', '.') . "<br>";
        } else {
            echo "Gagal memperbarui saldo: " . $updateStmt->error . "<br>";
        }
        
        // laporan pemasukan
        $jenis_transaksi = 'pemasukan';
        $deskripsi_laporan = 'Pemasukan dari Shopee';
        $stmt = $conn->prepare("
            INSERT INTO bookkeeping (
                jenis_transaksi, jumlah, tanggal, keterangan, total
            ) VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sdssd",
            $jenis_transaksi,
            $amount,
            $date,
            $deskripsi_laporan,
            $total

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

header("Location: ../pages/pesanan.php");
ob_end_flush(); // Flush output buffer
exit;
?>
