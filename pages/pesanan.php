<?php
require_once "../includes/db.php";

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Ambil info user dari database
$sql_user = "SELECT username, role FROM users WHERE username = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $username);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_data = $result_user->fetch_assoc();

// Ambil semua pesanan
$sql = "SELECT * FROM orders ORDER BY order_date ASC";
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
  <link rel="stylesheet" href="../assets/css/style-pesanan.css" />
</head>

<body>
  <div class="sidebar">
    <div class="logo"><img src="../assets/img/logo.png" alt="logo" /></div>
    <ul class="menu">
      <li><a href="../index.php">Home</a></li>
      <li class="active">Pesanan</li>
      <li><a href="saldo.php">Info Saldo</a></li>
      <li><a href="laporan.php">Laporan</a></li>
    </ul>
    <div class="logout"><a class="logout" href="../includes/proses_logout.php">Log Out</a></div>
  </div>

  <div class="main">
    <div class="header">
      <button class="hamburger" onclick="toggleSidebar()">☰</button>
      <h2>TOKO SAYA</h2>
      <div class="tabs"></div>
          <div class="profile">
              <?= htmlspecialchars($user_data['username'] ?? 'Pengguna') ?><br />
              <small><?= ucfirst(htmlspecialchars($user_data['status'] ?? 'admin')) ?></small>
          </div>
    </div>

    <div class="header2">
      <div class="status-buttons">
        <button data-status="semua" onclick="filterStatus('semua')">Semua</button>
        <button data-status="belum bayar" onclick="filterStatus('belum bayar')">Belum Bayar</button>
        <button data-status="siap kirim" onclick="filterStatus('siap kirim')">Siap Kirim</button>
        <button data-status="dikirim" onclick="filterStatus('dikirim')">Dikirim</button>
        <button data-status="selesai" onclick="filterStatus('selesai')">Selesai</button>
        <button data-status="dibatalkan" onclick="filterStatus('dibatalkan')">Pembatalan</button>
      </div>

      <div class="search-bar">
        <select id="search-type">
          <option value="order_id">Cari berdasarkan Order ID</option>
          <option value="username">Cari berdasarkan Username</option>
        </select>
        <input type="text" id="search-input" placeholder="Ketik untuk mencari..." oninput="searchOrders()" />
      </div>
    </div>

    <div class="content">
      <div class = 'header-row'>
        <h3 id="total-paket"><?php echo $result->num_rows; ?> Paket</h3>
        <a href="../shopee-integration/refresh_token.php" class='refresh_btn'>Refresh</a>
      </div>
      <?php while($row = $result->fetch_assoc()): 
        $mappedStatus = mapStatus($row['status']);
      ?>
      <div class="order-card"
        data-status="<?php echo $mappedStatus; ?>"
        data-order-id="<?php echo strtolower($row['order_id']); ?>"
        data-username="<?php echo strtolower($row['username']); ?>">

      <!-- Gambar -->
        <div class="product-photo">
          <img
            src="<?php echo htmlspecialchars($row['product_image']); ?>"
            alt="Foto Produk"
            onerror="this.onerror=null; this.src='gambar/default.jpg';"
          />
        </div>

        <!-- detail -->
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
          <?php echo htmlspecialchars($row['shipping_provider']); ?><br />
          Jumlah: <?php echo htmlspecialchars($row['qty']); ?>
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
              echo $row['platform'] === 'shopee' ? '../assets/img/shopee.png' :
                   ($row['platform'] === 'tokopedia' ? '../assets/img/tokopedia.png' :
                   ($row['platform'] === 'tiktok_shop' ? '../assets/img/tiktok.png' : '../assets/img/unknown.png'));
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

  <!-- untuk mencari pesanan -->
   <script src = "../assets/js/search-pesanan.js"></script>
   <script>
  function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('show');
  }
</script>
<script>
  function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('show');
  }
</script>
</body>
</html>
