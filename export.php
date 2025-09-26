<?php
include "config.php";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="rekap_absensi.csv"');

$output = fopen('php://output', 'w');

// Header kolom
fputcsv($output, ['Tanggal', 'NIS', 'NISN', 'Nama', 'Kelas', 'Status', 'Keterangan']);

// Ambil data dari database
$query = "SELECT a.tanggal, s.nis, s.nisn, s.nama, k.nama_kelas, a.status, a.keterangan 
          FROM absensi a
          JOIN siswa s ON a.id_siswa = s.id
          JOIN kelas k ON s.id_kelas = k.id
          ORDER BY a.tanggal DESC";

$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, $row);
}

fclose($output);
exit;
