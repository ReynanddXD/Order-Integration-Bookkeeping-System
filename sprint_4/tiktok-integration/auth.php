<?php
require 'config.php';

// Gantilah 'xyz123' dengan nilai acak yang bisa Anda verifikasi nanti (untuk keamanan CSRF)
$state  = 'xyz123'; 
$region = 'ID'; // Kode wilayah: ID (Indonesia), MY (Malaysia), TH (Thailand), SG (Singapore), PH (Philippines), VN (Vietnam)

$auth_url = "https://auth.tiktok-shops.com/oauth/authorize?" . http_build_query([
    'app_key'       => APP_KEY,
    'redirect_uri'  => REDIRECT_URI,
    'state'         => $state,
    'response_type' => 'code',
    'shop_region'   => $region
]);

header("Location: $auth_url");
exit;
