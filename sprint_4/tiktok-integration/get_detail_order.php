<?php
$tokenData = json_decode(file_get_contents('token_tiktok.json'), true);
$accessToken = $tokenData['data']['access_token'] ?? null;

if (!$accessToken) {
    die("Access token tidak ditemukan.");
}

$orderId = $_GET['order_id'] ?? '';
if (!$orderId) {
    die("Order ID wajib diisi di parameter ?order_id=...");
}

$url = "https://open-api.tiktokglobalshop.com/api/orders/detail/query";
$body = [
    "order_id_list" => [$orderId]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $accessToken",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
curl_close($ch);

echo "<h3>Detail Order:</h3><pre>$response</pre>";
