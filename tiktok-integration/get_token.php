<?php
require 'config.php';

if (!isset($_GET['code'])) {
    die("Authorization code tidak tersedia.");
}

$code = $_GET['code'];
$shop_region = $_GET['shop_region'] ?? 'ID'; // default Indonesia

$data = [
    'app_key'       => APP_KEY,
    'app_secret'    => APP_SECRET,
    'auth_code'     => $code,
    'grant_type'    => 'authorized_code',
    'shop_region'   => $shop_region
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, TOKEN_URL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
    die("Gagal mendapatkan response dari TikTok.");
}

$result = json_decode($response, true);

if (isset($result['data']['access_token'])) {
    echo "✅ Access Token: " . $result['data']['access_token'] . "<br>";
    echo "🔁 Refresh Token: " . $result['data']['refresh_token'] . "<br>";
    echo "🕒 Expired In: " . $result['data']['access_token_expire_in'] . " detik<br>";
} else {
    echo "❌ Gagal mendapatkan token.<br><br>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
}
