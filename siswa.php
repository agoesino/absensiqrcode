<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

include 'config.php';
require 'vendor/phpqrcode/qrlib.php';

/* ==== Tambah kolom status di tabel siswa (jalankan sekali di phpMyAdmin) ====
ALTER TABLE siswa ADD status VARCHAR(10) NOT NULL DEFAULT 'aktif';
============================================================================= */

// Proses simpan (tambah baru)
if (isset($_POST['simpan'])) {
    $nis = $_POST['nis'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];

    mysqli_query($conn, "INSERT INTO siswa (nis, nisn, nama, kelas, status) 
                         VALUES ('$nis', '$nisn', '$nama', '$kelas', 'aktif')");

    // Generate QR Code
    $qr_dir = "assets/qr/";
    if (!is_dir($qr_dir)) mkdir($qr_dir, 0777, true);
    QRcode::png($nisn, $qr_dir . "$nisn.png", QR_ECLEVEL_L, 4);

    header("Location: siswa.php");
    exit;
}

// Proses update data (edit)
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $nis = $_POST['nis'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];

    mysqli_query($conn, "UPDATE siswa SET nis='$nis', nisn='$nisn', nama='$nama', kelas='$kelas' WHERE id=$id");

    // Update QR Code jika NISN berubah
    $qr_dir = "assets/qr/";
    if (!is_dir($qr_dir)) mkdir($qr_dir, 0777, true);
    QRcode::png($nisn, $qr_dir . "$nisn.png", QR_ECLEVEL_L, 4);

    header("Location: siswa.php");
    exit;
}

// Tandai siswa keluar
if (isset($_GET['keluar'])) {
    $id = intval($_GET['keluar']);
    mysqli_query($conn, "UPDATE siswa SET status='keluar' WHERE id=$id");
    header("Location: siswa.php");
    exit;
}

// Ambil data untuk edit jika ada
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM siswa WHERE id=$id LIMIT 1");
    $edit_data = mysqli_fetch_assoc($res);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Siswa</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Running text modern */
    .running-text-container {
      width: 100%;
      overflow: hidden;
      background: linear-gradient(90deg, #007bff, #00c4ff);
      padding: 8px 0;
      border-radius: 5px;
    }
    .running-text {
      display: inline-block;
      padding-left: 100%;
      white-space: nowrap;
      font-size: 1rem;
      font-weight: bold;
      color: white;
      animation: marquee 15s linear infinite;
    }
    @keyframes marquee {
      0% { transform: translateX(0); }
      100% { transform: translateX(-100%); }
    }
  </style>
</head>
<body class="container py-4">

  <h2 class="text-center mb-4">Data Siswa</h2>

  <!-- Running Text Modern -->
  <div class="running-text-container mb-4">
    <div class="running-text">
      📚 Suatu keuntungan tersendiri bagi seorang Wali Kelas/Guru Wali yang memiliki kemampuan menghafal nama siswa
    </div>
  </div>

  <a href="dashboard.php" class="btn btn-secondary mb-3">← Kembali</a>

  <!-- Form Input / Edit -->
<form method="post" class="row g-2 mb-4">
  <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">
  <div class="col-6 col-md-2">
    <input type="number" name="nis" class="form-control" placeholder="NIS" required inputmode="numeric" pattern="[0-9]*" value="<?= $edit_data['nis'] ?? '' ?>">
  </div>
  <div class="col-6 col-md-2">
    <input type="number" name="nisn" class="form-control" placeholder="NISN" required inputmode="numeric" pattern="[0-9]*" value="<?= $edit_data['nisn'] ?? '' ?>">
  </div>
  <div class="col-12 col-md-4">
    <input type="text" name="nama" class="form-control" placeholder="Nama" required value="<?= $edit_data['nama'] ?? '' ?>">
  </div>
  <div class="col-6 col-md-2">
    <input type="text" name="kelas" class="form-control" placeholder="Kelas" required value="<?= $edit_data['kelas'] ?? '' ?>">
  </div>
  <div class="col-6 col-md-2">
    <?php if ($edit_data): ?>
      <button type="submit" name="update" class="btn btn-warning w-100">Update</button>
      <a href="siswa.php" class="btn btn-secondary w-100 mt-2">Batal</a>
    <?php else: ?>
      <button type="submit" name="simpan" class="btn btn-primary w-100">Simpan</button>
    <?php endif; ?>
  </div>
</form>

  <a href="cetak_kartu.php" class="btn btn-success mb-3" target="_blank">Cetak Semua Kartu QR</a>
  <a href="siswa_keluar.php" class="btn btn-outline-danger mb-3">Lihat Siswa Keluar</a>

<a href="import_siswa.php" class="btn btn-success mb-3">📥 Import dari Excel</a>

  <!-- Tabel Data Siswa -->
  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-light text-center">
        <tr>
          <th>NIS</th>
          <th>NISN</th>
          <th>Nama</th>
          <th>Kelas</th>
          <th>QR Code</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $q = mysqli_query($conn, "SELECT * FROM siswa WHERE status='aktif' ORDER BY nama ASC");
        while ($row = mysqli_fetch_assoc($q)) {
          echo "<tr>
            <td>{$row['nis']}</td>
            <td>{$row['nisn']}</td>
            <td>{$row['nama']}</td>
            <td>{$row['kelas']}</td>
            <td class='text-center'>
              <a href='assets/qr/{$row['nisn']}.png' target='_blank'>
                <img src='assets/qr/{$row['nisn']}.png' width='50'>
              </a>
            </td>
            <td class='text-center'>
              <a href='siswa.php?edit={$row['id']}' class='btn btn-info btn-sm'>Edit</a>
              <a href='siswa.php?keluar={$row['id']}' class='btn btn-warning btn-sm' onclick='return confirm(\"Yakin siswa ini keluar?\")'>Keluar</a>
            </td>
          </tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

</body>
</html>
