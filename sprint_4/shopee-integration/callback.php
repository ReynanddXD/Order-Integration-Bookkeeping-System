<?php
$code = $_GET['code'] ?? null;
$shop_id = $_GET['shop_id'] ?? null;

if ($code && $shop_id) {
    file_put_contents("temp_code.txt", json_encode([
        "code" => $code,
        "shop_id" => $shop_id
    ]));
    $message = "Code dan Shop ID berhasil diambil.";

    // Tampilkan code dan shop_id
    echo "<strong>Code:</strong> $code<br>";
    echo "<strong>Shop ID:</strong> $shop_id<br><br>";
    
    $success = true;
} else {
    $message = "Gagal mengambil code dan shop_id dari Shopee.";
    $success = false;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Callback Shopee</title>
</head>
<body style="font-family: Arial; text-align: center; padding-top: 50px;">
    <h2><?= $message ?></h2>

    <?php if ($success): ?>
        <form action="get_token.php" method="GET">
            <button type="submit" style="padding: 10px 20px; font-size: 16px;">Lanjut ke get_token.php</button>
        </form>
    <?php else: ?>
        <p>Pastikan URL yang dikembalikan dari Shopee memiliki parameter <code>code</code> dan <code>shop_id</code>.</p>
    <?php endif; ?>
</body>
</html>
