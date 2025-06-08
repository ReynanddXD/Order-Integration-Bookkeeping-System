<?php
define("PARTNER_ID", "2011441");
define("PARTNER_KEY", "456266784c464f54504d6b536a4362486b6d525a5842567762516675486b594d");
define("REDIRECT_URL", "https://885f-182-0-134-136.ngrok-free.app/project_rpl/shopee-integration/callback.php");
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