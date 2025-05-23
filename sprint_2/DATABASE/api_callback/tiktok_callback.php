<?php
// tiktok_callback.php
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $state = $_GET['state'] ?? '';

    // Simpan atau tampilkan kode
    file_put_contents("tiktok_code_log.txt", "Code: $code | State: $state\n", FILE_APPEND);

    echo "Authorization code received: $code<br>";
    echo "You can now exchange this code for an access token.";
} else {
    echo "No authorization code received.";
}
?>