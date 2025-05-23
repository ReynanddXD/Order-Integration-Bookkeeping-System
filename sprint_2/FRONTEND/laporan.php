<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Laporan</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="sidebar">
    <div class="logo"><img src="GAMBAR/logo.png" alt="logo"></div>
    <ul class="menu">
      <li><a href="pesanan.php">Pesanan</a></li>
      <li><a href="pemasukan.php">Info Saldo</a></li>
      <li class="active">Laporan</li>
    </ul>
    <div class="logout">Log Out</div>
  </div>
  <div class="main">
    <div class="header">
      <h2>TOKO SAYA</h2>
      <div class="profile">Nama Admin<br><small>Admin</small></div>
    </div>
    <div class="content">
      <h3>Informasi Laporan</h3>
      <input type="date" />
      <table class="laporan-table">
        <!-- Tabel dummy -->
        <tr><th>Kolom 1</th><th>Kolom 2</th><th>Kolom 3</th></tr>
        <tr><td>Data</td><td>Data</td><td>Data</td></tr>
        <tr><td>Data</td><td>Data</td><td>Data</td></tr>
      </table>
      <button class="btn-unduh">Unduh</button>
    </div>
  </div>
</body>
</html>
