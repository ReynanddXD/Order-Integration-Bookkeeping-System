<?php
require_once 'includes/db.php';

// ====== Data Hari Ini ======
$sql_siap = "SELECT COUNT(*) as total FROM orders WHERE status = 'READY_TO_SHIP'";
$siap = $conn->query($sql_siap)->fetch_assoc()['total'];

$sql_kirim = "SELECT COUNT(*) as total FROM orders WHERE status = 'SHIPPED' OR status = 'TO_CONFIRM_RECEIVE'";
$kirim = $conn->query($sql_kirim)->fetch_assoc()['total'];

$sql_selesai = "SELECT COUNT(*) as total FROM orders WHERE status = 'COMPLETED'";
$selesai = $conn->query($sql_selesai)->fetch_assoc()['total'];

$sql_cancel = "SELECT COUNT(*) as total FROM orders WHERE status = 'IN_CANCEL' OR status = 'CANCELLED'";
$cancel = $conn->query($sql_cancel)->fetch_assoc()['total'];

$sql_danamasuk = "SELECT SUM(amount) as total FROM transactions WHERE type = 'masuk' AND date = CURDATE()";
$danamasuk = $conn->query($sql_danamasuk)->fetch_assoc()['total'] ?? 0;

$sql_danamasuk_k = "SELECT SUM(amount) as total FROM transactions WHERE type = 'masuk' AND date = CURDATE() - INTERVAL 1 DAY";
$danamasuk_k = $conn->query($sql_danamasuk_k)->fetch_assoc()['total'] ?? 0;

$sql_transaksi = "SELECT SUM(price) as total FROM orders WHERE order_date = CURDATE()";
$transaksi = $conn->query($sql_transaksi)->fetch_assoc()['total'] ?? 0;

$sql_transaksi_k = "SELECT SUM(price) as total FROM orders WHERE order_date = CURDATE() - INTERVAL 1 DAY";
$transaksi_k = $conn->query($sql_transaksi_k)->fetch_assoc()['total'] ?? 0;

$sql_top_produk = "
    SELECT product_title, variation, SUM(qty) AS total_terjual
    FROM orders
    GROUP BY product_title, variation
    ORDER BY total_terjual DESC
    LIMIT 10
";
$result_top = $conn->query($sql_top_produk);

// ====== Fungsi Persentase ======
function hitungPersentase($sekarang, $kemarin) {
    if ($kemarin == 0) return "100%";
    $persen = (($sekarang - $kemarin) / $kemarin) * 100;
    return round($persen, 1) . '%';
}

// ====== Data Grafik Penjualan Bulan Ini & Lalu ======
$bulan_ini = date('Y-m');
$bulan_lalu = date('Y-m', strtotime('-1 month'));

// Data bulan ini
$sql_bulan_ini = "
    SELECT DATE(order_date) as tanggal, SUM(price * qty) as total
    FROM orders
    WHERE DATE_FORMAT(order_date, '%Y-%m') = '$bulan_ini'
    GROUP BY DATE(order_date)
";
$result_ini = $conn->query($sql_bulan_ini);
$labels = [];
$data_ini = [];
while ($row = $result_ini->fetch_assoc()) {
    $labels[] = $row['tanggal'];
    $data_ini[] = (int)$row['total'];
}

// Data bulan lalu
$sql_bulan_lalu = "
    SELECT DATE(order_date) as tanggal, SUM(price * qty) as total
    FROM orders
    WHERE DATE_FORMAT(order_date, '%Y-%m') = '$bulan_lalu'
    GROUP BY DATE(order_date)
";
$result_lalu = $conn->query($sql_bulan_lalu);
$map_lalu = [];
while ($row = $result_lalu->fetch_assoc()) {
    $map_lalu[$row['tanggal']] = (int)$row['total'];
}

// Samakan tanggal
$data_lalu = [];
foreach ($labels as $tgl) {
    $data_lalu[] = $map_lalu[$tgl] ?? 0;
}

// Target
$target_bulan = 5000000;
$target_minggu = 1000000;

// Transaksi masuk bulan ini
$sql_bulan = "SELECT SUM(amount) as total FROM transactions 
              WHERE type = 'masuk' 
              AND MONTH(date) = MONTH(CURDATE()) 
              AND YEAR(date) = YEAR(CURDATE())";
$amount_bulan = $conn->query($sql_bulan)->fetch_assoc()['total'] ?? 0;

// Transaksi masuk minggu ini
$sql_minggu = "SELECT SUM(amount) as total FROM transactions 
               WHERE type = 'masuk' 
               AND WEEK(date, 1) = WEEK(CURDATE(), 1) 
               AND YEAR(date) = YEAR(CURDATE())";
$amount_minggu = $conn->query($sql_minggu)->fetch_assoc()['total'] ?? 0;

// Hitung persen
$percent_bulan = $target_bulan ? round(($amount_bulan / $target_bulan) * 100) : 0;
$percent_minggu = $target_minggu ? round(($amount_minggu / $target_minggu) * 100) : 0;

// Encode ke JS
$json_labels = json_encode($labels);
$json_ini = json_encode($data_ini);
$json_lalu = json_encode($data_lalu);
