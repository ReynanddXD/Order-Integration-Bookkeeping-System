<?php
require_once 'includes/db.php';
include 'includes/dashboard-data.php';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - D’ajib Creative House</title>
    <link rel="stylesheet" href="assets/css/style-dashboard.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="assets/img/logo.png" alt="Logo Toko">
        </div>
        <ul class="menu">
            <li class="active">Home</li>
            <li><a href="pages/pesanan.php">Pesanan</a></li>
            <li><a href="pages/saldo.php">Info Saldo</a></li>
            <li><a href="pages/laporan.php">Laporan</a></li>
        </ul>
        <div class="logout">Log Out</div>
    </div>

    <div class="main">
        <div class="sidebar-overlay"></div>
        <div class="header">
            <div class="hamburger-menu"><span></span><span></span><span></span></div>
            <h2>TOKO SAYA</h2>
            <div class="profile">Nama Admin<br /><small>Admin</small></div>
        </div>

        <div class="content-wrapper">
            <section class="summary-cards">
                <div class="card" onclick="location.href='pages/pesanan.php'">
                    <h3>Pesanan Siap dikirim</h3>
                    <p><?= $siap ?></p>
                </div>
                <div class="card" onclick="location.href='pages/pesanan.php'">
                    <h3>Pesanan Sedang Dikirim</h3>
                    <p><?= $kirim ?></p>
                </div>
                <div class="card" onclick="location.href='pages/pesanan.php'">
                    <h3>Total Pesanan Selesai</h3>
                    <p><?= $selesai ?></p>
                </div>
                <div class="card" onclick="location.href='pages/saldo.php'">
                    <h3>Dana Masuk Hari ini</h3>
                    <p>Rp <?= number_format($danamasuk, 0, ',', '.') ?></p>
                    <p style="color: <?= $danamasuk > $danamasuk_k ? 'green' : 'red' ?>">
                        <?= hitungPersentase($danamasuk, $danamasuk_k) ?>
                    </p>
                </div>
                <div class="card" onclick="location.href='pages/saldo.php'">
                    <h3>Transaksi Hari ini</h3>
                    <p>Rp <?= number_format($transaksi, 0, ',', '.') ?></p>
                    <p style="color: <?= $transaksi > $transaksi_k ? 'green' : 'red' ?>">
                        <?= hitungPersentase($transaksi, $transaksi_k) ?>
                    </p>
                </div>
                <div class="card" onclick="location.href='pages/pesanan.php'">
                    <h3>Pembatalan</h3>
                    <p><?= $cancel ?></p>
                </div>
            </section>

            <section class="charts">
                <div class="chart-container">
                    <h3>Grafik Penjualan Bulanan</h3>
                    <canvas id="salesChart"></canvas>
                </div>
                <div class="sales-target-box">
                    <!-- Target Bulanan -->
                    <div class="progress-item">
                        <canvas id="monthlyTargetChart" width="60" height="60"></canvas>
                        <div class="text">
                            <h4>
                                Target Bulanan <br>
                                (Rp <?= number_format($target_bulan ?? 0, 0, ',', '.') ?>)
                            </h4>
                            <p id="monthlyPercentage"><?= isset($percent_bulan) ? $percent_bulan : 0 ?>%</p>
                        </div>
                    </div>

                    <!-- Target Mingguan -->
                    <div class="progress-item">
                        <canvas id="weeklyTargetChart" width="60" height="60"></canvas>
                        <div class="text">
                            <h4>
                                Target Mingguan <br>
                                (Rp <?= number_format($target_minggu ?? 0, 0, ',', '.') ?>)
                            </h4>
                            <p id="weeklyPercentage"><?= isset($percent_minggu) ? $percent_minggu : 0 ?>%</p>
                        </div>
                    </div>
                </div>
            </section>
            <div class="top-products">
                    <h3>Top Produk</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Produk</th>
                                <th>Variasi</th>
                                <th>Total Terjual</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($row = $result_top->fetch_assoc()) : ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['product_title']) ?></td>
                                    <td><?= htmlspecialchars($row['variation']) ?></td>
                                    <td><?= $row['total_terjual'] ?> pcs</td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
        </div>
    </div>

    <!-- Kirim data chart ke JS -->
    <script>
        const chartLabels = <?= $json_labels ?>;
        const chartDataBulanIni = <?= $json_ini ?>;
        const chartDataBulanLalu = <?= $json_lalu ?>;
    </script>
    <script src="assets/js/chart-data.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    function renderCircularChart(canvasId, percentage, color) {
        new Chart(document.getElementById(canvasId), {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [percentage, 100 - percentage],
                    backgroundColor: [color, '#e5e5e5'],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '80%',
                responsive: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                }
            }
        });
    }

    renderCircularChart("monthlyTargetChart", <?= $percent_bulan ?>, '#4f46e5');
    renderCircularChart("weeklyTargetChart", <?= $percent_minggu ?>, '#14b8a6');
    </script>

</body>
</html>
