<?php
// Konfigurasi koneksi database
$host = "localhost";
$user = "root";
$password = ""; 
$database = "intregasi";

// Membuat koneksi
$conn = new mysqli($host, $user, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Membaca file JSON
$json = file_get_contents("sample_orders.json");
$data = json_decode($json, true);

// Memasukkan data ke tabel
foreach ($data as $row) {
    $id = $row['id'];
    $platform = $row['platform'];
    $order_id = $row['order_id'];
    $username = $row['username'];
    $product_title = $row['product_title'];
    $variation = $row['variation'];
    $price = $row['price'];
    $status = $row['status'];
    $note = $row['note'];
    $shipping_provider = $row['shipping_provider'];
    $order_date = $row['order_date'];

    $sql = "INSERT INTO orders 
        (id, platform, order_id, username, product_title, variation, price, status, note, shipping_provider, order_date) 
        VALUES 
        ('$id', '$platform', '$order_id', '$username', '$product_title', '$variation', '$price', '$status', '$note', '$shipping_provider', '$order_date')";

    if ($conn->query($sql) === TRUE) {
        echo "Data order_id $order_id berhasil diinput.<br>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error . "<br>";
    }
}

$conn->close();
?>