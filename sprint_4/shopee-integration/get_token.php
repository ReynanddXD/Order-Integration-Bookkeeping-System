<?php
require 'config.php';

$data = json_decode(file_get_contents("temp_code.txt"), true);
$code = $data['code'] ?? null;
$shop_id = $data['shop_id'] ?? null;

if (!$code || !$shop_id) {
    echo "Code atau Shop ID tidak ditemukan di temp_code.txt";
    exit;
}

$timestamp = time();
$path = "/api/v2/auth/token/get";

$base_string = PARTNER_ID . $path . $timestamp;
$sign = hash_hmac('sha256', $base_string, PARTNER_KEY);

$url = BASE_URL . "/api/v2/auth/token/get?" . http_build_query([
    'partner_id' => PARTNER_ID,
    'timestamp' => $timestamp,
    'sign' => $sign
]);

$body = [
    "code" => $code,
    "shop_id" => (int)$shop_id,
    "partner_id" => (int)PARTNER_ID,
    "redirect_uri" => REDIRECT_URL
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
curl_close($ch);

file_put_contents("token.json", $response);

$tokenData = json_decode($response, true);

if (!isset($tokenData['access_token'])) {
    echo "<strong style='color:red;'>Gagal mendapatkan access_token</strong><br>";
    echo "<pre>$response</pre>";
    exit;
}

echo "<h3>Berhasil mendapatkan Access Token</h3>";
echo "<pre>$response</pre>";

echo '<br><a href="get_orders.php"><button>Lanjutkan ke Get Orders</button></a>';
