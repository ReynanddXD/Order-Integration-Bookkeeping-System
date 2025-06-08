<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Pemasukan</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .content {
      max-width: 600px;
      margin: 30px auto;
      padding: 20px;
      background-color: #f5f5f5;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .content h3 {
      font-size: 1.5em;
      color: #333;
      margin-bottom: 10px;
    }

    .content .saldo {
      font-size: 1.8em;
      font-weight: bold;
      color: #2e8b57;
      background-color: #e0f7e9;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      text-align: center;
    }

    .content h4 {
      font-size: 1.2em;
      color: #444;
      margin-bottom: 8px;
    }

    .content .transaksi {
      background-color: #fff;
      border: 1px solid #ddd;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 20px;
      min-height: 40px;
    }

    .content .show-more {
      display: inline-block;
      background-color: #007bff;
      color: white;
      padding: 10px 18px;
      text-decoration: none;
      border-radius: 6px;
      transition: background-color 0.3s ease;
    }

    .content .show-more:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="logo"><img src="GAMBAR/logo.png" alt="logo"></div>
    <ul class="menu">
      <li><a href="pesanan.php">Pesanan</a></li>
      <li class="active">Info Saldo</li>
      <li><a href="laporan.php">Laporan</a></li>
    </ul>
    <div class="logout">Log Out</div>
  </div>
  <div class="main">
    <div class="header">
      <h2>TOKO SAYA</h2>
      <div class="profile">Nama Admin<br><small>Admin</small></div>
    </div>
    <div class="content">
      <h3>Informasi Saldo</h3>
      <div class="saldo">Rp. 100.000.000</div>
      <h4>Transaksi Terakhir</h4>
      <div class="transaksi">[Data Transaksi]</div>
      <a href="#" class="show-more">Show All My Transactions</a>
    </div>
  </div>
</body>
</html>
