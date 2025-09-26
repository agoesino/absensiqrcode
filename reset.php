<?php
// Sertakan file konfigurasi database
include "config.php";

// Pastikan koneksi database tersedia dari config.php
// Contoh config.php biasanya berisi variabel $conn atau $pdo

// Password baru (sudah dalam bentuk hash)
$newPassword = "0192023a7bbd73250516f069df18b500";

// ID user yang ingin direset
$userId = 1;

// Menggunakan MySQLi
$sql = "UPDATE users SET password = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $newPassword, $userId);

if ($stmt->execute()) {
    echo "Password berhasil direset untuk user ID {$userId}.";
} else {
    echo "Gagal mereset password: " . $stmt->error;
}

$stmt->close();
$conn->close();
