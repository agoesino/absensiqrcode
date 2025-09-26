<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}
include "config.php";

$msg = "";

// Ambil data profil
$q = $conn->query("SELECT * FROM profil_sekolah LIMIT 1");
$profil = $q->fetch_assoc();

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $kepala = $_POST['kepala'];
    $nip = $_POST['nip'];

    // Upload logo jika ada
    $logo = $profil['logo'];
    if (!empty($_FILES['logo']['name'])) {
        $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
        $logo = "logo_" . time() . "." . $ext;
        move_uploaded_file($_FILES['logo']['tmp_name'], "uploads/" . $logo);
    }

    $conn->query("UPDATE profil_sekolah SET 
        nama_sekolah='$nama',
        alamat='$alamat',
        kepala_sekolah='$kepala',
        nip_kepala='$nip',
        logo='$logo'
    WHERE id=" . $profil['id']);

    $msg = "Profil sekolah berhasil diperbarui!";
    $q = $conn->query("SELECT * FROM profil_sekolah LIMIT 1");
    $profil = $q->fetch_assoc();
}

// Ubah password admin di tabel users
if (isset($_POST['ubah_password'])) {
    $old_pass = md5($_POST['old_password']);
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    // Ambil password lama dari database
    $res = $conn->query("SELECT password FROM users WHERE id=1");
    $row = $res->fetch_assoc();

    if ($old_pass !== $row['password']) {
        $msg = "<span style='color:red;'>Password lama salah!</span>";
    } elseif ($new_pass !== $confirm_pass) {
        $msg = "<span style='color:red;'>Konfirmasi password baru tidak cocok!</span>";
    } else {
        $new_pass_md5 = md5($new_pass);
        $conn->query("UPDATE users SET password='$new_pass_md5' WHERE id=1");
        $msg = "<span style='color:green;'>Password berhasil diubah!</span>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profil Sekolah</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; padding: 20px; background: #f4f4f4; }
        .container { max-width: 500px; margin: auto; background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input, textarea { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button { margin-top: 10px; padding: 10px; background: #28a745; color: #fff; border: none; width: 100%; border-radius: 5px; cursor: pointer; }
        img { max-width: 150px; display: block; margin-top: 10px; border: 1px solid #ccc; padding: 3px; background: #f9f9f9; }
    </style>
</head>
<body>
<div class="container">
    <a href="dashboard.php" style="display:inline-block; padding:10px 15px; background:#6c757d; color:#fff; text-decoration:none; border-radius:5px; margin-top:15px;">← Kembali ke Dashboard</a>
</div>

    <h2>Profil Sekolah</h2>
    <?php if ($msg) echo "<p>$msg</p>"; ?>
    <form method="POST" enctype="multipart/form-data">
        <label>Nama Sekolah</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($profil['nama_sekolah']) ?>" required>

        <label>Alamat</label>
        <textarea name="alamat" required><?= htmlspecialchars($profil['alamat']) ?></textarea>

        <label>Nama Kepala Sekolah</label>
        <input type="text" name="kepala" value="<?= htmlspecialchars($profil['kepala_sekolah']) ?>" required>

        <label>NIP Kepala Sekolah</label>
        <input type="text" name="nip" value="<?= htmlspecialchars($profil['nip_kepala']) ?>" required>

        <label>Logo Sekolah</label>
        <input type="file" name="logo" accept="image/*">
        <?php
        if ($profil['logo']) {
            $logoPath = "uploads/" . $profil['logo'];
            if (file_exists($logoPath)) {
                $version = filemtime($logoPath);
                echo "<img src='{$logoPath}?v={$version}' alt='Logo Sekolah'>";
            } else {
                echo "<p>Logo tidak ditemukan.</p>";
            }
        }
        ?>

        <button type="submit" name="simpan">Simpan Profil</button>
    </form>

    <hr>

    <h3>Ubah Password Admin</h3>

    <form method="POST">
        <label>Password Lama</label>
        <input type="password" name="old_password" required>

        <label>Password Baru</label>
        <input type="password" name="new_password" required>

        <label>Ulangi Password Baru</label>
        <input type="password" name="confirm_password" required>

        <button type="submit" name="ubah_password">Ubah Password</button>
    </form>
</div>
</body>
</html>
