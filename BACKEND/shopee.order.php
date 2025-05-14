<?php
include '../DATABASE/db.php';

$partner_id = '';
$partner_key = '';
$shop_id = '';
$access_token = '';

// ========== Membuat timestamp & tanda tangan ==========
$path = '/api/v2/order/get_order_list';
$timestamp = time();
$base_string = "$partner_id$path$timestamp$access_token$shop_id";
$sign = hash_hmac('sha256', $base_string, $partner_key);

// ========== Panggil API untuk dapatkan daftar pesanan ==========
$url = "https://partner.shopeemobile.com$path?shop_id=$shop_id&access_token=$access_token&partner_id=$partner_id&timestamp=$timestamp&sign=$sign&time_range_field=create_time&time_from=" . ($timestamp - 3600*24*3) . "&time_to=$timestamp&order_status=ALL";

$response = file_get_contents($url);
$data = json_decode($response, true);

if (!isset($data['response']['order_list'])) {
  die("Gagal ambil data: " . $response);
}

foreach ($data['response']['order_list'] as $order) {
  $order_sn = $order['order_sn'];

  // Ambil detail tiap order
  $path_detail = '/api/v2/order/get_order_detail';
  $base_detail = "$partner_id$path_detail$timestamp$access_token$shop_id";
  $sign_detail = hash_hmac('sha256', $base_detail, $partner_key);

  $url_detail = "https://partner.shopeemobile.com$path_detail?shop_id=$shop_id&access_token=$access_token&partner_id=$partner_id&timestamp=$timestamp&sign=$sign_detail";

  $body = json_encode([
    'order_sn_list' => [$order_sn]
  ]);

  $ch = curl_init($url_detail);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
  ]);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);

  $res = curl_exec($ch);
  curl_close($ch);

  $order_data = json_decode($res, true);
  if (!isset($order_data['response']['order_list'][0])) {
    continue;
  }

  $o = $order_data['response']['order_list'][0];

  $customer_name = $o['recipient_address']['name'] ?? 'Unknown';
  $product_title = $o['items'][0]['item_name'] ?? '';
  $variation = $o['items'][0]['model_name'] ?? '';
  $price = $o['total_amount'] ?? 0;
  $payment = $o['payment_method'] ?? '';
  $status = $o['order_status'] ?? '';
  $shipping = $o['shipping_carrier'] ?? '';
  $order_date = date('Y-m-d', $o['create_time'] ?? time());

  $price_float = floatval($price);

  $stmt = $conn->prepare("REPLACE INTO orders 
      (order_id, platform, username, product_title, variation, price, payment_method, status, shipping_provider, order_date) 
      VALUES (?, 'shopee', ?, ?, ?, ?, ?, ?, ?, ?)");

  $stmt->bind_param("sssssdssss", 
      $order_sn, 
      $customer_name, 
      $product_title, 
      $variation, 
      $price_float, 
      $payment, 
      $status, 
      $shipping, 
      $order_date
  );

  $stmt->execute();
  $stmt->close();
}

$conn->close();
echo "Sukses simpan semua pesanan Shopee.";
?>