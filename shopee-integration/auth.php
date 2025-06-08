<?php
require 'config.php';

$timestamp = time();
$redirect = urlencode(REDIRECT_URL);

// Signature
$base_string = PARTNER_ID . "/api/v2/shop/auth_partner" . $timestamp;
$sign = hash_hmac('sha256', $base_string, PARTNER_KEY);

// Ganti ke endpoint sandbox
$auth_url = "https://partner.shopeemobile.com/api/v2/shop/auth_partner?"
    . "partner_id=" . PARTNER_ID
    . "&timestamp=" . $timestamp
    . "&sign=" . $sign
    . "&redirect=" . $redirect;

header("Location: $auth_url");
exit;