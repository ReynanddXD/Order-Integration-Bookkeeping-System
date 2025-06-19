<?php
define("PARTNER_ID", "2011441");
define("PARTNER_KEY", "4a516757696b6a6f59724d6467766947494a6265486e766744475973564a4a64");
define("REDIRECT_URL", "https://383c-2400-9800-171-7e88-c9d1-72be-343d-60f1.ngrok-free.app/rpl_project/shopee-integration/callback.php");
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