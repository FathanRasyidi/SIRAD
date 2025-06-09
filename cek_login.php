<?php
include 'koneksi.php';
session_start();

function encrypt($data) {
    $encryptedData = base64_encode($data);
    return $encryptedData;
}

$username = $_POST['username'];
$password = $_POST['password'];

if (empty($username) || empty($password)) {
    header("location:login.php?pesan=kosong");
    exit();
}

$encryptedPassword = encrypt($password);

$data = mysqli_query($connect, "SELECT * FROM user WHERE username = '$username' AND password = '$encryptedPassword'");
$cek = mysqli_num_rows($data);

if ($cek > 0) {
    $row = mysqli_fetch_assoc($data);
    $login = $row['nama'];
    $type = $row['hak_akses'];
    $_SESSION['id_user'] = $row['ID_USER'];
    $_SESSION['login'] = $login;
    $_SESSION['usertype'] = $type;
    $_SESSION['username'] = $username;
    if (isset($_POST['kuki'])) {
        $encryptedUsername = encrypt($username);
        setcookie('user', $encryptedUsername, time() + 60 * 60 * 24);
    }
    header("location:dashboard.php");
    exit();
} else {
    header("location:login.php?pesan=gagal");
}
?>