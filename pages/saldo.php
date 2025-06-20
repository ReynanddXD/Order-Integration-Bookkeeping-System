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

// Ambil data saldo
$sql_saldo = "SELECT * FROM saldo WHERE id = 1 LIMIT 1";
$result_saldo = $conn->query($sql_saldo);
$saldo = $result_saldo->fetch_assoc();

// Ambil semua pesanan
$sql = "SELECT * FROM transactions ORDER BY date DESC";
$result = $conn->query($sql);

$transactions = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <title>Pemasukan - D’ajib Creative House</title>
    <link rel="stylesheet" href="../assets/css/style-saldo.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  </head>
  <body>
    <div class="sidebar">
      <div class="logo"><img src="../assets/img/logo.png" alt="logo" /></div>
      <ul class="menu">
        <li><a href="../index.php">Home</a></li>
        <li><a href="pesanan.php">Pesanan</a></li>
        <li class="active">info Saldo</li>
        <li><a href="laporan.php">Laporan</a></li>
      </ul>
      <div class="logout"><a class="logout" href="../includes/proses_logout.php">Log Out</a></div>
    </div>
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <div class="main">
      <div class="header">
        <button class="toggle-sidebar" onclick="toggleSidebar()">☰</button>
        <h2>TOKO SAYA</h2>
            <div class="profile">
                <?= htmlspecialchars($user_data['username'] ?? 'Pengguna') ?><br />
                <small><?= ucfirst(htmlspecialchars($user_data['status'] ?? 'admin')) ?></small>
            </div>
      </div>

      <div class="saldo-content">
        <div class="saldo-row saldo-top-section">
          <!-- Left Column -->
          <div class="saldo-left-panel">
            <!-- Balance Information -->
            <div class="saldo-card saldo-info-card">
              <h3>Saldo Keseluruhan</h3>
              <div class="saldo-amount">Rp. <?php echo number_format($saldo['total_balance'], 0, ',', '.'); ?></div>
            </div>

            <!-- Balance by Platform -->
            <div class="saldo-card saldo-platform-card">
              <div class="saldo-platform-list">
                <div class="saldo-platform-entry">
                  <span>Shopee</span>
                  <strong>Rp. <?php echo number_format($saldo['shopee_balance'], 0, ',', '.'); ?></strong>
                </div>
                <div class="saldo-platform-entry">
                  <span>TikTok</span>
                  <strong>Rp. <?php echo number_format($saldo['tiktok_balance'], 0, ',', '.'); ?></strong>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column -->
          <div class="saldo-right-panel">
            <div class="saldo-card saldo-profit-capital">
              <div class="saldo-sub-card saldo-profit">
                <h4>Keuntungan</h4>
                <strong>Rp. <?php echo number_format($saldo['profit'], 0, ',', '.'); ?></strong>
              </div>
              <div class="saldo-sub-card saldo-capital">
                <h4>Modal</h4>
                <strong>Rp. <?php echo number_format($saldo['capital'], 0, ',', '.'); ?></strong>
              </div>
            </div>
          </div>
        </div>

        <!-- Bottom Section: Recent Transactions Full Width -->
        <div class="content-wrapper">
          <div class="transaksi-header-bar">
            <h3 class="sub-title">Daftar Transaksi</h3>

            <?php
            // Auto-generate order ID
            $tanggal = date('Ymd');
            $prefix = "ORD-" . $tanggal;

            // Ambil nomor terakhir
            $sql_max = "SELECT MAX(order_id) as max_id FROM transactions WHERE order_id LIKE '$prefix%'";
            $result_max = $conn->query($sql_max);
            $row_max = $result_max->fetch_assoc();
            $last_id = $row_max['max_id'] ?? null;

            if ($last_id) {
                $last_number = (int)substr($last_id, -4);
                $new_number = str_pad($last_number + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $new_number = '0001';
            }

            $generated_order_id = $prefix . '-' . $new_number;
            ?>
            <form class="form-pengeluaran" action="../includes/tambah_transaksi.php" method="post">
              <input type="hidden" name="type" value="keluar" />
              <input type="text" name="order_id" value="<?= $generated_order_id ?>" readonly />
              <select name="description" required>
                <option value="">-- Pilih Platform --</option>
                <option value="shopee">Shopee</option>
                <option value="tikTok_shop">TikTok Shop</option>
              </select>
              <input type="number" name="amount" placeholder="Jumlah (Rp)" required />
              <input type="text" name="keterangan" placeholder="Keterangan (opsional)" />
              <button type="submit">+ Tambah Pengeluaran</button>
            </form>

          </div>

          <!-- Header kolom -->
          <div class="transaksi-header">
            <div>Order ID</div>
            <div>Deskripsi</div>
            <div>Type</div>
            <div>Tanggal</div>
            <div>Harga</div>
            <div>Keterangan</div>
          </div>

          <div id="transaction-container">
            <?php foreach ($transactions as $i => $trans): ?>
              <?php
                $jenis = strtolower($trans['type']) === 'masuk' ? 'Masuk' : 'Keluar';
                $warna = $jenis === 'Masuk' ? 'trans-in' : 'trans-out';
              ?>
              <div class="transaksi-card <?php echo $warna; ?> <?php echo $i > 1 ? 'hidden' : ''; ?>" data-index="<?= $i ?>">
                <div><?php echo htmlspecialchars($trans['order_id'] ?? '-'); ?></div>
                <div><?php echo ucfirst($trans['description']); ?></div>
                <div><?php echo $jenis; ?></div>
                <div><?php echo htmlspecialchars($trans['date']); ?></div>
                <div>Rp <?php echo number_format($trans['amount'], 0, ',', '.'); ?></div>
                <div><?php echo htmlspecialchars($trans['keterangan'] ?? '-'); ?></div>
              </div>
            <?php endforeach; ?>
          </div>
          <!-- Tombol View More -->
          <div style="text-align: center; margin-top: 10px;">
            <span id="toggle-transaksi" class="view-more-text">Lihat semua transaksi</span>
          </div>
        </div>

      </div>
    </div>
    <script>
      const toggleLink = document.getElementById('toggle-transaksi');
      let isShowingAll = false;

      toggleLink.addEventListener('click', function () {
        const cards = document.querySelectorAll('.transaksi-card');

        cards.forEach((card, index) => {
          const dataIndex = parseInt(card.getAttribute('data-index'));
          if (isShowingAll) {
            // Saat sembunyikan: tampilkan hanya 2 pertama
            card.classList.toggle('hidden', dataIndex > 1);
          } else {
            // Saat tampilkan semua: hanya tampilkan hingga data ke-10
            card.classList.toggle('hidden', dataIndex > 9);
          }
        });

        isShowingAll = !isShowingAll;
        this.textContent = isShowingAll ? 'Sembunyikan transaksi' : 'Lihat semua transaksi';
      });
    </script>
    <script>
  const sidebar = document.querySelector('.sidebar');
  const toggleBtn = document.querySelector('.toggle-sidebar');

  function toggleSidebar() {
    sidebar.classList.toggle('active');
  }

  document.addEventListener('click', function (e) {
    // Jika sidebar sedang aktif, dan klik bukan di sidebar atau tombol toggle
    if (
      sidebar.classList.contains('active') &&
      !sidebar.contains(e.target) &&
      !toggleBtn.contains(e.target)
    ) {
      sidebar.classList.remove('active');
    }
  });
</script>

  </body>
</html>
