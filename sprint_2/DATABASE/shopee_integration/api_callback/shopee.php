<?php
// Step 1: Get the code and shop_id from query
$code = $_GET['code'];
$shop_id = $_GET['shop_id'];
$partner_id = getenv("PARTNER_ID");
$partner_key = getenv("PARTNER_KEY");
$redirect = getenv("REDIRECT_URL");
$timestamp = time();

// Step 2: Generate sign
$path = "/api/v2/auth/token/get";
$base_string = "$partner_id$path$timestamp";
$sign = hash_hmac('sha256', $base_string, $partner_key, false);

// Step 3: Request access token
$body = json_encode([
    "code" => $code,
    "partner_id" => (int)$partner_id,
    "shop_id" => (int)$shop_id
]);

$ch = curl_init("https://partner.test-stable.shopeemobile.com$path?partner_id=$partner_id&timestamp=$timestamp&sign=$sign");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (isset($data['access_token'])) {
    // Simpan token ke database atau file
    file_put_contents("shopee_token.json", json_encode($data));
    echo "Token berhasil didapatkan dan disimpan.";
} else {
    echo "Gagal mengambil token: ";
    print_r($data);
}
?>
