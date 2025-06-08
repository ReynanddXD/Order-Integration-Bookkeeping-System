<?php
require 'config.php';

// Ambil data token dan shop_id dari file
$tokenData = json_decode(file_get_contents("token.json"), true);
$data = json_decode(file_get_contents("temp_code.txt"), true);

$access_token = $tokenData['access_token'] ?? null;
$shop_id = $data['shop_id'] ?? null;

if (!$access_token || !$shop_id) {
    echo "Access token atau Shop ID tidak ditemukan.";
    exit;
}

$timestamp = time();

// Fungsi generate signature sesuai dokumentasi Shopee
function generateSign($partner_id, $path, $timestamp, $access_token, $shop_id, $partner_key) {
    $base_string = $partner_id . $path . $timestamp . $access_token . $shop_id;
    return hash_hmac('sha256', $base_string, $partner_key);
}

// 1. Tes koneksi dengan get_shop_info dulu
$path = "/api/v2/shop/get_shop_info";

$sign = generateSign(PARTNER_ID, $path, $timestamp, $access_token, $shop_id, PARTNER_KEY);

$url = BASE_URL . $path . "?" . http_build_query([
    'partner_id' => PARTNER_ID,
    'timestamp' => $timestamp,
    'sign' => $sign,
    'shop_id' => $shop_id,
    'access_token' => $access_token
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<h3>Shop Info Response (HTTP Status: $http_code):</h3><pre>$response</pre>";

if ($http_code != 200) {
    exit("Gagal mendapatkan info toko. Periksa token dan shop_id.");
}

// 2. GET order list sesuai format dokumentasi kamu
$timestamp = time();
$path = "/api/v2/order/get_order_list";

$sign = generateSign(PARTNER_ID, $path, $timestamp, $access_token, $shop_id, PARTNER_KEY);

// Tentukan waktu (24 jam terakhir)
$time_from = $timestamp - 86400;
$time_to = $timestamp;

// Siapkan URL GET seperti dokumentasi asli kamu
$url = BASE_URL . $path . "?" . http_build_query([
    'partner_id' => PARTNER_ID,
    'timestamp' => $timestamp,
    'sign' => $sign,
    'access_token' => $access_token,
    'shop_id' => $shop_id,
    'time_from' => $time_from,
    'time_to' => $time_to,
    'time_range_field' => 'create_time',
    'page_size' => 20,
    'cursor' => '',
    'response_optional_fields' => 'order_status'
]);

// Gunakan GET sesuai dokumentasi
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

file_put_contents("orders.json", $response); // Tambahkan baris ini
echo "<h3>Order List Response (HTTP Status: $http_code):</h3><pre>$response</pre>";

