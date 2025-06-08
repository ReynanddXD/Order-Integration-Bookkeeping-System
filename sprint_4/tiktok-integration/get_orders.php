<?php
$tokenData = json_decode(file_get_contents('token_tiktok.json'), true);
$accessToken = $tokenData['data']['access_token'] ?? null;

if (!$accessToken) {
    die("Access token tidak ditemukan.");
}

$url = "https://open-api.tiktokglobalshop.com/api/orders/search";
$params = [
    "page_size" => 10,
    "order_status" => "WAIT_SELLER_SEND_GOODS"
];

$ch = curl_init($url . '?' . http_build_query($params));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $accessToken",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
curl_close($ch);

echo "<h3>Daftar Order:</h3><pre>$response</pre>";
