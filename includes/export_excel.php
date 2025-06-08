<?php
include '../includes/db.php';

$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=Laporan-$bulan-$tahun.csv");

$output = fopen("php://output", "w");

// Header kolom
fputcsv($output, ['No', 'Tanggal', 'Jenis', 'Jumlah', 'Keterangan', 'Total']);

$query = "SELECT * FROM bookkeeping WHERE MONTH(tanggal)=? AND YEAR(tanggal)=? ORDER BY tanggal ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $bulan, $tahun);
$stmt->execute();
$result = $stmt->get_result();

$no = 1;
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
    $no++,
        $row['Tanggal'],
        ucfirst($row['Jenis_Transaksi']),
        $row['Jumlah'],
        $row['Keterangan'],
        $row['Total']
    ]);
    $no++;
}

fclose($output);
exit;
?>
