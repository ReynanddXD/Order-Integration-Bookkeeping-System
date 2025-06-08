<?php
if (isset($_GET['code'])) {
    $data = [
        'code' => $_GET['code'],
        'shop_region' => $_GET['shop_region'] ?? '',
        'state' => $_GET['state'] ?? ''
    ];
    file_put_contents('temp_code.txt', json_encode($data));
    echo "Kode berhasil diterima.<br><a href='get_token.php'>Klik untuk ambil token</a>";
} else {
    echo "Gagal menerima kode otorisasi.";
}
