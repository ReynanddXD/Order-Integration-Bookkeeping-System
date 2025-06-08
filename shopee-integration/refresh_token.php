<?php
require 'config.php';

// Baca refresh_token dan shop_id dari file
$tokenData = json_decode(file_get_contents("token_refresh.json"), true);
$data = json_decode(file_get_contents("temp_code.txt"), true);

$refresh_token = $tokenData['refresh_token'] ?? null;
$shop_id = $data['shop_id'] ?? null;

if (!$refresh_token || !$shop_id) {
    echo "<strong style='color:red;'>Refresh Token atau Shop ID tidak ditemukan</strong><br>";
    exit;
}

$timestamp = time();
$path = "/api/v2/auth/access_token/get";

// Buat string tanda tangan (signature) sesuai dokumentasi Shopee:
// signature = HMAC_SHA256(partner_id + path + timestamp + shop_id)
$base_string = PARTNER_ID . $path . $timestamp . $shop_id;
$sign = hash_hmac('sha256', $base_string, PARTNER_KEY);

// Buat URL endpoint dengan query parameter
// Saat buat query parameter
$url = BASE_URL . $path . '?' . http_build_query([
    'partner_id' => (int)PARTNER_ID,
    'timestamp' => $timestamp,
    'sign' => $sign,
    'shop_id' => $shop_id
]);

// Body request
$body = [
    "refresh_token" => $refresh_token,
    "partner_id" => (int)PARTNER_ID,
    "shop_id" => (int)$shop_id
];

// Inisialisasi cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo "<strong style='color:red;'>cURL Error: " . curl_error($ch) . "</strong><br>";
    curl_close($ch);
    exit;
}

curl_close($ch);

// Simpan respons ke file token.json
file_put_contents("token_refresh.json", $response);

// Decode respons JSON
$responseData = json_decode($response, true);

if (!isset($responseData['access_token'])) {
    echo "<strong style='color:red;'>Gagal me-refresh access_token</strong><br>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    exit;
}

// Jika berhasil
echo "<h3>Berhasil refresh Access Token</h3>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

// Contoh: akses token baru
$new_access_token = $responseData['access_token'];

// Jika ingin, simpan token baru ke file lain atau database
header("Location: get_orders1.php");
exit;
