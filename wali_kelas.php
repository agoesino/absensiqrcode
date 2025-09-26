<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}
include 'config.php';

// Tambah wali kelas
if (isset($_POST['tambah'])) {
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);
    $nama_wali = mysqli_real_escape_string($conn, $_POST['nama_wali']);
    $nip_wali = mysqli_real_escape_string($conn, $_POST['nip_wali']);
    mysqli_query($conn, "INSERT INTO wali_kelas (kelas, nama_wali, nip_wali) 
                         VALUES ('$kelas', '$nama_wali', '$nip_wali')");
    header("Location: wali_kelas.php");
    exit;
}

// Edit wali kelas
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);
    $nama_wali = mysqli_real_escape_string($conn, $_POST['nama_wali']);
    $nip_wali = mysqli_real_escape_string($conn, $_POST['nip_wali']);
    mysqli_query($conn, "UPDATE wali_kelas SET kelas='$kelas', nama_wali='$nama_wali', nip_wali='$nip_wali' WHERE id=$id");
    header("Location: wali_kelas.php");
    exit;
}

// Hapus wali kelas
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM wali_kelas WHERE id=$id");
    header("Location: wali_kelas.php");
    exit;
}

$waliList = mysqli_query($conn, "SELECT * FROM wali_kelas ORDER BY kelas");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Wali Kelas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <h2 class="mb-4">Manajemen Wali Kelas</h2>

  <!-- Form Tambah -->
  <div class="card mb-4">
    <div class="card-header bg-primary text-white">Tambah Wali Kelas</div>
    <div class="card-body">
      <form method="post">
        <div class="row mb-3">
          <div class="col-md-3">
            <input type="text" name="kelas" class="form-control" placeholder="Kelas (misal 5A)" required>
          </div>
          <div class="col-md-5">
            <input type="text" name="nama_wali" class="form-control" placeholder="Nama Wali" required>
          </div>
          <div class="col-md-4">
            <input type="text" name="nip_wali" class="form-control" placeholder="NIP Wali (opsional)">
          </div>
        </div>
        <button type="submit" name="tambah" class="btn btn-success">Tambah</button>
        <a href="dashboard.php" class="btn btn-secondary">⬅ Kembali</a>
      </form>
    </div>
  </div>

  <!-- Tabel Data -->
  <div class="card">
    <div class="card-header bg-dark text-white">Daftar Wali Kelas</div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
          <tr>
            <th width="5%">No</th>
            <th>Kelas</th>
            <th>Nama Wali</th>
            <th>NIP</th>
            <th width="20%">Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $no = 1;
        while ($row = mysqli_fetch_assoc($waliList)) {
            echo "<tr>";
            echo "<td>{$no}</td>";
            echo "<td>{$row['kelas']}</td>";
            echo "<td>{$row['nama_wali']}</td>";
            echo "<td>{$row['nip_wali']}</td>";
            echo "<td>
                    <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editModal{$row['id']}'>Edit</button>
                    <a href='wali_kelas.php?hapus={$row['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin hapus data ini?')\">Hapus</a>
                  </td>";
            echo "</tr>";

            // Modal Edit
            echo "
            <div class='modal fade' id='editModal{$row['id']}' tabindex='-1'>
              <div class='modal-dialog'>
                <div class='modal-content'>
                  <form method='post'>
                    <div class='modal-header'>
                      <h5 class='modal-title'>Edit Wali Kelas</h5>
                      <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                    </div>
                    <div class='modal-body'>
                      <input type='hidden' name='id' value='{$row['id']}'>
                      <div class='mb-3'>
                        <label>Kelas</label>
                        <input type='text' name='kelas' class='form-control' value='{$row['kelas']}' required>
                      </div>
                      <div class='mb-3'>
                        <label>Nama Wali</label>
                        <input type='text' name='nama_wali' class='form-control' value='{$row['nama_wali']}' required>
                      </div>
                      <div class='mb-3'>
                        <label>NIP Wali</label>
                        <input type='text' name='nip_wali' class='form-control' value='{$row['nip_wali']}'>
                      </div>
                    </div>
                    <div class='modal-footer'>
                      <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Batal</button>
                      <button type='submit' name='edit' class='btn btn-primary'>Simpan Perubahan</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>";
            $no++;
        }
        ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
