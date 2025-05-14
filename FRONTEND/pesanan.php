<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "intregasi";

// Koneksi database
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari tabel pesanan
$sql = "SELECT * FROM orders";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pesanan - D’ajib Creative House</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="sidebar">
    <div class="logo">LOGO</div>
    <ul class="menu">
      <li class="active">Pesanan</li>
      <li><a href="pemasukan.html">Pemasukan</a></li>
      <li><a href="laporan.html">Laporan</a></li>
    </ul>
    <div class="logout">Log Out</div>
  </div>

  <div class="main">
    <div class="header">
      <h2>TOKO SAYA</h2>
      <div class="tabs"></div>
      <div class="profile">Nama Admin<br /><small>Admin</small></div>
    </div>

    <div class="header2">
      <button>Semua</button>
      <button>Diproses</button>
      <button>Siap Kirim</button>
      <button>Dikirim</button>
      <button>Selesai</button>
    </div>

    <div class="content">
      <h3><?php echo $result->num_rows; ?> Paket</h3>

      <?php while($row = $result->fetch_assoc()): ?>
        <div class="order-card">
          <div class="product-photo">Foto Produk</div>
          <div class="order-detail">
            <strong><?php echo htmlspecialchars($row['username']); ?></strong><br />
            <?php echo htmlspecialchars($row['product_title']); ?><br />
            <?php echo htmlspecialchars($row['variation']); ?>
          </div>
          <div class="order-info">
            <?php echo htmlspecialchars($row['order_id']); ?><br />
            Rp <?php echo number_format($row['price'], 0, ',', '.'); ?><br />
            <?php echo htmlspecialchars($row['shipping_provider']); ?>
          </div>
          <div class="status">
            <?php echo htmlspecialchars($row['status']); ?>
          </div>
          <div class="platform">
            <img
              src="<?php
                echo $row['platform'] === 'shopee' ? 'shopee.png' :
                    ($row['platform'] === 'tokopedia' ? 'tokopedia.png' :
                    ($row['platform'] === 'tiktok_shop' ? 'tiktok.png' : 'unknown.png'));
              ?>"
              alt="<?php echo htmlspecialchars($row['platform']); ?>"
            />
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</body>
</html>