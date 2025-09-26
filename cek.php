<?php
session_start();
include 'config.php';

$user = $_POST['username'];
$pass = md5($_POST['password']);

$q = mysqli_query($conn, "SELECT * FROM users WHERE username='$user' AND password='$pass'");
if (mysqli_num_rows($q) > 0) {

    if ($user === 'wali') {
        $_SESSION['wali'] = $user;
        header("Location: jam_absensi.php");
    } else {
        $_SESSION['admin'] = $user;
        header("Location: dashboard.php");
    }
    exit;
    
} else {
    echo "Login gagal";
}
?>
