<?php
session_start();
include 'koneksi.php';
$user_type = empty($_SESSION['usertype']) ? '' : $_SESSION['usertype'];

if (isset($_COOKIE['user']) &&!isset($_SESSION['login'])) {
    $usn = base64_decode($_COOKIE['user']);
    $sqlu = "SELECT * FROM user WHERE username = '$usn'";
    $qu = mysqli_query($connect, $sqlu);
    $dbu = mysqli_fetch_array($qu);
    $_SESSION['login'] = $dbu['nama'];
    header("location:index.php");
    exit();
}

if (empty($_COOKIE['user']) && !isset($_SESSION['login'])){
    header("location:login.php?pesan=belum_login");
    exit();
}

$sql = "SELECT * FROM data";
$q = mysqli_query($connect, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sigra Rosa</title>
    <link rel="icon" href="img/sr.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .nav-item {
            margin-right: 30px;
        }

        .navbar-brand {
            font-weight: w500;
            font-family: Arial, Helvetica, sans-serif;
            display: flex;
            align-items: center;
        }

        #logo {
            color: white;
            margin-right: 20px;
            margin-left: 20px;
        }

        .fitur {
            background-color: #eee;
            border: 2px solid #ddd;
            padding: 20px;
            margin: 20px;
            margin-left: 40px;
            margin-right: 40px;
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" style="color: white;">
                <img src="img/sr.png" alt="" width="50" height="50" class="d-inline-block" id="logo"
                    style="filter: brightness(0) invert(1);">
                Sistem Informasi Radiografi RSJ. Ghrasia</a>
            <div class="justify-content-end" id="navbarNav">
                <ul class="navbar-nav flex-row">
                    <?php if ($user_type == "admin") { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="user.php">Edit User</a>
                        </li>
                    <?php }
                    if ($user_type == "admin" || $user_type == "radiologi") { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="input.php">Input Data</a>
                        </li>
                    <?php }
                    if (!empty($_SESSION['login'])) { ?>
                        <a class="btn btn-danger" href="logout.php?" role="button" style="margin-right: 20px">Logout</a>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container" style="margin-left: 30px">
        <h1 class="mt-4">Selamat Datang, <?= $_SESSION['login'] ?>!</h1>
    </div>

    <section class="fitur">
        <h4 style="margin-top: 0px;">Data Pasien</h4>
        <div class="info">
            <table class="table table-striped">
                <tr>
                    <th>Tanggal Pemeriksaan</th>
                    <th>No. Rekam Medis</th>
                    <th>Nama Pasien</th>
                    <th>Tanggal Lahir</th>
                    <th>Alamat</th>
                    <th>Jenis Pemeriksaan</th>
                    <th>Radiograf</th>
                    <th>Expertise</th>
                </tr>
                <?php while ($db = mysqli_fetch_array($q)) { ?>
                    <tr>
                        <td><?= $db['tanggal'] ?></td>
                        <td><?= $db['rekmed'] ?></td>
                        <td><?= $db['nama_pasien'] ?></td>
                        <td><?= $db['tanggal_lahir'] ?></td>
                        <td><?= $db['alamat'] ?></td>
                        <td><?= $db['jenis_periksa'] ?></td>
                        <td><?= $db['image'] ?></td>
                        <td><?= $db['expertise'] ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </section>
</body>

</html>