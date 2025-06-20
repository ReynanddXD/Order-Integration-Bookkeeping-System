<?php
include '../includes/db.php';

$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

$query = "SELECT * FROM bookkeeping WHERE MONTH(Tanggal)=? AND YEAR(Tanggal)=? ORDER BY Tanggal ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $bulan, $tahun);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan</title>
  <link rel="stylesheet" href="../assets/css/style-laporan.css" />
</head>
<body>
  <div class="sidebar" id="sidebar">
    <div class="logo"><img src="../assets/img/logo.png" alt="logo"></div>
    <ul class="menu">
      <li><a href="../index.php">Home</a></li>
      <li><a href="pesanan.php">Pesanan</a></li>
      <li><a href="saldo.php">Info Saldo</a></li>
      <li class="active">Laporan</li>
    </ul>
    <div class="logout">Log Out</div>
  </div>
  <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

  <div class="main">
    <div class="header">
      <button class="toggle-sidebar" onclick="toggleSidebar()">☰</button>
      <h2>TOKO SAYA</h2>
      <div class="profile">Nama Admin<br><small>Admin</small></div>
    </div>

    <div class="content">
      <h3>Informasi Laporan</h3>

      <form method="get" class="filter-bar">
        <label for="bulan">Bulan</label>
        <select name="bulan" id="bulan" class="styled-select">
          <?php for ($i = 1; $i <= 12; $i++): ?>
            <option value="<?= $i ?>" <?= ($i == $bulan) ? 'selected' : '' ?>>
              <?= date('F', mktime(0, 0, 0, $i, 10)) ?>
            </option>
          <?php endfor; ?>
        </select>

        <label for="tahun">Tahun</label>
        <select name="tahun" id="tahun" class="styled-select">
          <?php for ($y = 2020; $y <= date('Y'); $y++): ?>
            <option value="<?= $y ?>" <?= ($y == $tahun) ? 'selected' : '' ?>><?= $y ?></option>
          <?php endfor; ?>
        </select>

        <button type="submit" class="btn-filter">Tampilkan</button>
      </form>

      <div class="laporan-wrapper">
        <table class="laporan-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Tanggal</th>
              <th>Jenis</th>
              <th>Jumlah</th>
              <th>Keterangan</th>
              <th>Total</th> 
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            $totalPemasukan = 0;
            $totalPengeluaran = 0;

            while ($row = $result->fetch_assoc()):
              $jumlah = $row['Jumlah'];
              $jenis = strtolower(trim($row['Jenis_Transaksi']));
              if ($jenis === 'pemasukan') {
                $totalPemasukan += $jumlah;
              } elseif ($jenis === 'pengeluaran') {
                $totalPengeluaran += $jumlah;
              }
            ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= $row['Tanggal'] ?></td>
              <td><?= ucfirst($row['Jenis_Transaksi']) ?></td>
              <td>Rp <?= number_format($jumlah, 0, ',', '.') ?></td>
              <td><?= $row['Keterangan'] ?></td>
              <td>Rp <?= number_format($row['Total'], 0, ',', '.') ?></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

        <div class="laporan-summary bawah">
          <div class="summary-item">
            <span class="label">Total Pemasukan:</span>
            <span class="value">Rp <?= number_format($totalPemasukan, 0, ',', '.') ?></span>
          </div>
          <div class="summary-item">
            <span class="label">Total Pengeluaran:</span>
            <span class="value">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></span>
          </div>
          <div class="summary-item">
            <span class="label">Saldo Akhir:</span>
            <span class="value">Rp <?= number_format($totalPemasukan - $totalPengeluaran, 0, ',', '.') ?></span>
          </div>
        </div>

        <form method="get" action="../includes/export_excel.php" style="margin-top: 20px;">
          <input type="hidden" name="bulan" value="<?= $bulan ?>">
          <input type="hidden" name="tahun" value="<?= $tahun ?>">
          <button type="submit" class="btn-unduh">Unduh Excel</button>
        </form>
      </div>
    </div>
  </div>

  <script>
  const sidebar = document.getElementById("sidebar");
  const toggleBtn = document.querySelector(".toggle-sidebar");
  const overlay = document.querySelector(".sidebar-overlay");

  function toggleSidebar() {
    sidebar.classList.toggle("show");
    overlay.classList.toggle("active");
  }

  // Tutup sidebar saat klik di luar sidebar dan bukan tombol toggle
  document.addEventListener("click", function (e) {
    if (
      sidebar.classList.contains("show") &&
      !sidebar.contains(e.target) &&
      !toggleBtn.contains(e.target)
    ) {
      sidebar.classList.remove("show");
      overlay.classList.remove("active");
    }
  });
</script>

</body>
</html>
