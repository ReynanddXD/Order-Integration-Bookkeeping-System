<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "intregasi";

// Koneksi database
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); } //
Ambil data dari tabel pesanan $sql = "SELECT * FROM orders"; $result =
$conn->query($sql); ?>

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
        <button onclick="filterStatus('semua')">Semua</button>
        <button onclick="filterStatus('diproses')">Diproses</button>
        <button onclick="filterStatus('siap kirim')">Siap Kirim</button>
        <button onclick="filterStatus('dikirim')">Dikirim</button>
        <button onclick="filterStatus('selesai')">Selesai</button>
      </div>

      <div class="content">
        <h3 id="total-paket"><?php echo $result->num_rows; ?> Paket</h3>

        <?php while($row = $result->fetch_assoc()): ?>
        <div
          class="order-card"
          data-status="<?php echo strtolower($row['status']); ?>"
        >
          <div class="product-photo">
            <img
              src="GAMBAR/PRODUK/<?php echo htmlspecialchars($row['product_image']); ?>"
              alt="Foto Produk"
            />
          </div>
          <div class="order-detail">
            <strong><?php echo htmlspecialchars($row['username']); ?></strong
            ><br />
            <?php echo htmlspecialchars($row['product_title']); ?><br />
            <?php echo "Variasi: ". htmlspecialchars($row['variation']); ?>
          </div>
          <div class="order-info">
            <?php echo htmlspecialchars($row['order_id']); ?><br />
            Rp
            <?php echo number_format($row['price'], 0, ',', '.'); ?><br />
            <?php echo htmlspecialchars($row['shipping_provider']); ?>
          </div>
          <div class="buyer-note">
            <div class="note-title">Catatan:</div>
            <div class="note-content">
              <?php
                $note_raw = $row['buyer_note'];
                if (is_null($note_raw) || trim($note_raw) === '') {
                  echo "-";
                } else {
                  $note = htmlspecialchars($note_raw);
                  echo nl2br(strlen($note) >
              200 ? substr($note, 0, 200) . '...' : $note); } ?>
            </div>
          </div>
          <div class="status">
            <?php echo htmlspecialchars($row['status']); ?>
          </div>
          <div class="platform">
            <img
              src="<?php
                echo $row['platform'] === 'shopee' ? 'GAMBAR/shopee.png' :
                    ($row['platform'] === 'tokopedia' ? 'GAMBAR/tokopedia.png' :
                    ($row['platform'] === 'tiktok_shop' ? 'GAMBAR/tiktok.png' : 'unknown.png'));
              ?>"
              alt="<?php echo htmlspecialchars($row['platform']); ?>"
            />
          </div>
        </div>
        <?php endwhile; ?>
      </div>
    </div>

    <script>
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
      }
    </script>
  </body>
</html>
