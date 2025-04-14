<?php
session_start();
include 'koneksi.php';
$user_type = empty($_SESSION['usertype']) ? '' : $_SESSION['usertype'];

if (empty($_SESSION['login'])) {
    header("location:login.php?pesan=belum_login");
    exit();
}

if (isset($_GET['op'])) {
    $op = $_GET['op'];
    if ($op == 'delete') {
        $id = $_GET['id'];

        $sql = "DELETE FROM user WHERE id = $id";
        mysqli_query($connect, $sql);
    }
}
$sql = "SELECT * FROM user";
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
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <?php if ($user_type == "admin") { ?>
                        <li class="nav-item">
                            <a class="nav-link active" href="user.php">Data User</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="edit_user.php">Tambah User</a>
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
        <h1 class="mt-4">User</h1>
    </div>
    <section class="fitur">
        <h4 style="margin-top: 0px;">Data User</h4>
        <div class="info">
            <table class="table table-striped">
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Akses</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($db = mysqli_fetch_array($q)) { ?>
                    <tr>
                        <td><?= $db['nama'] ?></td>
                        <td><?= $db['username'] ?></td>
                        <td><?= str_repeat('*', strlen($db['password'])) ?></td>
                        <td><?php if ($db['akses'] == "admin") {
                            echo "Admin";
                        } else if ($db['akses'] == "radiologi") {
                            echo "Dokter Radiologi";
                        } else if ($db['akses'] == "pasien") {
                            echo "Pasien";
                        } else if ($db['akses'] == "dokter") {
                            echo "Dokter/Bangsal";
                        } ?></td>
                        <td>
                            <a href="edit_user.php?op=edit&id=<?= $db['id'] ?>" class="btn btn-warning">Edit</a>
                            <a href="user.php?op=delete&id=<?= $db['id'] ?>" class="btn btn-danger">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </section>

</body>

</html>