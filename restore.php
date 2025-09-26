<?php
// restore.php
include 'config.php';

// Lokasi file SQL
$sqlFile = 'db-absensi-qr-v2.sql';

// Cek file SQL ada atau tidak
if (!file_exists($sqlFile)) {
    die("File $sqlFile tidak ditemukan!");
}

// Baca isi file SQL
$sqlContent = file_get_contents($sqlFile);
if ($sqlContent === false) {
    die("Gagal membaca file SQL.");
}

// Hapus semua tabel di database (RESET)
$result = mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");
$res = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_array($res)) {
    mysqli_query($conn, "DROP TABLE IF EXISTS `" . $row[0] . "`");
}
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");

// Eksekusi isi file SQL (multi-query)
if (mysqli_multi_query($conn, $sqlContent)) {
    do {
        // Kosongkan hasil query jika ada
        if ($result = mysqli_store_result($conn)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($conn));

    echo "✅ Database berhasil direset dan diisi dari $sqlFile";
} else {
    echo "❌ Gagal restore: " . mysqli_error($conn);
}

mysqli_close($conn);
