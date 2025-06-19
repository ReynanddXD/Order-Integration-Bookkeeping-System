<?php
define("PARTNER_ID", "");
define("PARTNER_KEY", "");
define("REDIRECT_URL", "/rpl_project/shopee-integration/callback.php");
define("BASE_URL", "https://partner.shopeemobile.com");
// define("BASE_URL", "https://partner.test-stable.shopeemobile.com");

$host = "localhost";
$user = "root";
$pass = ""; // Ganti jika ada password
$dbname = "intregasi"; // Ganti sesuai nama database

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>
