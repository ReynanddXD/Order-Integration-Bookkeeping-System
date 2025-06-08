<?php
require 'config.php';

// Ambil token dan shop_id
$tokenData = json_decode(file_get_contents("token_refresh.json"), true);
$data = json_decode(file_get_contents("temp_code.txt"), true);
$access_token = $tokenData['access_token'] ?? null;
$shop_id = $data['shop_id'] ?? null;

if (!$access_token || !$shop_id) {
    exit("Access token atau Shop ID tidak ditemukan.");
}

// Ambil daftar order_sn dari file JSON
$orders = json_decode(file_get_contents("orders3.json"), true);
$order_list = $orders['response']['order_list'] ?? [];

if (empty($order_list)) {
    exit("Daftar order kosong.");
}

// Ambil hanya order_sn
$order_sn_array = array_map(function ($item) {
    return $item['order_sn'];
}, $order_list);

// Ubah array menjadi string dipisahkan koma
$order_sn_string = implode(',', $order_sn_array);

$timestamp = time();
$path = "/api/v2/order/get_order_detail";
$sign = hash_hmac(
    'sha256',
    PARTNER_ID . $path . $timestamp . $access_token . $shop_id,
    PARTNER_KEY
);

// Susun URL dengan query string
$query = http_build_query([
    'partner_id' => PARTNER_ID,
    'timestamp' => $timestamp,
    'access_token' => $access_token,
    'shop_id' => $shop_id,
    'sign' => $sign,
    'order_sn_list' => $order_sn_string,
    'request_order_status_pending' => 'true',
    'response_optional_fields' => 'buyer_user_id,buyer_username,recipient_address,note,item_list,pay_time,total_amount, shipping_carrier'
]);

$url = BASE_URL . $path . '?' . $query;

// Kirim request GET
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Simpan respons ke file
file_put_contents("order_detail3.json", $response);

header("Location: save_order_to_db.php");
exit;
?>
