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

// Ambil semua pesanan
$sql = "SELECT * FROM orders ORDER BY order_date DESC";
$result = $conn->query($sql);

// Fungsi mapping status dari Shopee ke kategori lokal
function mapStatus($status) {
    switch (strtoupper($status)) {
        case "UNPAID": 
            return "belum dibayar";
        case "READY_TO_SHIP": 
            return "siap kirim";
        case "SHIPPED":
        case "TO_CONFIRM_RECEIVE": 
            return "dikirim";
        case "COMPLETED": 
            return "selesai";
        case "IN_CANCEL":
        case "CANCELLED": 
            return "dibatalkan";
        default: 
            return "lainnya";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pesanan - D’ajib Creative House</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="sidebar">
    <div class="logo"><img src="GAMBAR/logo.png" alt="logo" /></div>
    <ul class="menu">
      <li class="active">Pesanan</li>
      <li><a href="pemasukan.php">Info Saldo</a></li>
      <li><a href="laporan.php">Laporan</a></li>
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
      <button data-status="semua" onclick="filterStatus('semua')">Semua</button>
      <button data-status="belum bayar" onclick="filterStatus('belum bayar')">Belum Bayar</button>
      <button data-status="siap kirim" onclick="filterStatus('siap kirim')">Siap Kirim</button>
      <button data-status="dikirim" onclick="filterStatus('dikirim')">Dikirim</button>
      <button data-status="selesai" onclick="filterStatus('selesai')">Selesai</button>
      <button data-status="dibatalkan" onclick="filterStatus('dibatalkan')">Pembatalan</button>
    </div>

    <div class="content">
      <h3 id="total-paket"><?php echo $result->num_rows; ?> Paket</h3>

      <?php while($row = $result->fetch_assoc()): 
        $mappedStatus = mapStatus($row['status']);
      ?>
      <div class="order-card" data-status="<?php echo $mappedStatus; ?>">

        <div class="product-photo">
          <img
            src="<?php echo htmlspecialchars($row['product_image']); ?>"
            alt="Foto Produk"
            onerror="this.onerror=null; this.src='gambar/default.jpg';"
          />
        </div>

        <div class="order-detail">
          <strong><?php echo htmlspecialchars($row['username']); ?></strong><br />
          <?php
            $product_title = htmlspecialchars($row['product_title']);
            echo mb_strlen($product_title) > 50 ? mb_substr($product_title, 0, 50) . '...' : $product_title;
          ?><br />
          <?php echo "Variasi: " . htmlspecialchars($row['variation']); ?>
        </div>

        <div class="order-info">
          <?php echo htmlspecialchars($row['order_id']); ?><br />
          Rp <?php echo number_format($row['price'], 0, ',', '.'); ?><br />
          <?php echo htmlspecialchars($row['shipping_provider']); ?>
        </div>

        <div class="buyer-note">
          <div class="note-title">Catatan:</div>
          <div class="note-content">
            <?php
              $note_raw = $row['buyer_note'];
              echo is_null($note_raw) || trim($note_raw) === '' ? "-" :
                nl2br(strlen($note_raw) > 200 ? htmlspecialchars(substr($note_raw, 0, 200)) . '...' : htmlspecialchars($note_raw));
            ?>
          </div>
        </div>

        <div class="status">
          <?php echo $mappedStatus; ?>
        </div>

        <div class="platform">
          <img
            src="<?php
              echo $row['platform'] === 'shopee' ? 'gambar/shopee.png' :
                   ($row['platform'] === 'tokopedia' ? 'gambar/tokopedia.png' :
                   ($row['platform'] === 'tiktok_shop' ? 'gambar/tiktok.png' : 'gambar/unknown.png'));
            ?>"
            alt="<?php echo htmlspecialchars($row['platform']); ?>"
          />
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>

  <script>
    // Fungsi untuk memfilter kartu berdasarkan status
    function filterStatus(status) {
      const cards = document.querySelectorAll(".order-card");
      let visibleCount = 0;

      cards.forEach((card) => {
        const cardStatus = card.getAttribute("data-status");
        if (status === "semua" || cardStatus === status) {
          card.style.display = "flex";
          visibleCount++;
        } else {
          card.style.display = "none";
        }
      });

      document.getElementById("total-paket").textContent =
        visibleCount + " Paket";

      document.querySelectorAll('.header2 button').forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.status === status) {
          btn.classList.add('active');
        }
      });
    }

    // Filter default saat halaman dimuat
    window.addEventListener('DOMContentLoaded', function () {
      filterStatus('siap kirim');
    });
  </script>
</body>
</html>
