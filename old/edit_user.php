<?php
session_start();
include 'koneksi.php';
$user_type = empty($_SESSION['usertype']) ? '' : $_SESSION['usertype'];

function encrypt($data)
{
    $encryptedData = base64_encode($data);
    return $encryptedData;
}

$op = "";
$nama = "";
$username = "";
$password = "";
$akses = "";


if (empty($_SESSION['login'])) {
    header("location:login.php?pesan=belum_login");
    exit();
}

if (isset($_GET['op'])) {
    $op = $_GET['op'];
    if ($op == 'edit') {
        $id = $_GET['id'];
        $sql = "SELECT * FROM user WHERE id = '$id'";
        $q = mysqli_query($connect, $sql);
        $db = mysqli_fetch_array($q);

        if (!$db) {
            $error = "Data tidak ditemukan";
        } else {
            $nama = $db['nama'];
            $username = $db['username'];
            $decryptPassword = base64_decode($db['password']);
            $password = $decryptPassword;
            $akses = $db['akses'];
        }
    }
}

if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $akses = $_POST['akses'];

    $encryptedPassword = encrypt($password);

    // untuk upload foto
    // if (isset($_FILES["foto"])) {
    //     $file_name = $_FILES["foto"]["name"];
    //     $file_tmp = $_FILES["foto"]["tmp_name"];
    //     $file_type = $_FILES["foto"]["type"];
    //     $file_size = $_FILES["foto"]["size"];
    //     $file_error = $_FILES["foto"]["error"];

    //     if ($file_error === 0) {
    //         $file_destination = "uploads/" . $file_name;
    //         if (!is_dir("uploads")) {
    //             mkdir("uploads");
    //         }
    //         move_uploaded_file($file_tmp, $file_destination);
    //         $image = $file_destination;
    //     } else {
    //         $error = "Gagal mengunggah file";
    //     }
    // }

    //untuk insert data ke database
    if ($nama && $username && $password && $akses) {
        if ($op == 'edit') {
            // //jika tidak mengupload gambar maka hapus gambar lama
            // if ($image == "") {
            //     $sqlG = "SELECT image FROM review WHERE id = '$id'";
            //     $qG = mysqli_query($connect, $sqlG);
            //     $row = mysqli_fetch_assoc($qG);
            //     $imagePath = isset($row['image']) ? $row['image'] : '';
            //     if (file_exists($imagePath)) {
            //         unlink($imagePath);
            //     }
            // }
            $sql = "UPDATE user SET nama='$nama', username='$username', password='$encryptedPassword', akses='$akses' WHERE id = $id";
            $query = mysqli_query($connect, $sql);
            if ($query) {
                header("location:user.php?op=edit_sukses");
            } else {
                echo "<script>alert('Data gagal diubah');</script>";
            }
        } else {
            $checkUsernameQuery = "SELECT * FROM user WHERE username = '$username'";
            $checkUsernameResult = mysqli_query($connect, $checkUsernameQuery);
            if (mysqli_num_rows($checkUsernameResult) > 0) {
                echo "<script>alert('Username already exists');</script>";
            } else {
                $sql = "INSERT INTO user (nama, username, password, akses) VALUES ('$nama', '$username', '$encryptedPassword', '$akses')";
                $query = mysqli_query($connect, $sql);
                if ($query) {
                    header("location:user.php?op=tambah_sukses");
                } else {
                    echo "<script>alert('Data gagal ditambahkan');</script>";
                }
            }
        }

    }
}

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
                            <a class="nav-link" href="user.php">Data User</a>
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
        <h1 class="mt-4">Use with caution</h1>
    </div>

    <section class="fitur">
        <h4 style="margin-top: 0px;">Edit User</h4>
        <div class="info">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $nama ?>" required>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username"
                        value="<?php echo $username ?>" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="text" class="form-control" id="password" name="password"
                        value="<?php echo $password ?>" required>
                </div>
                <label for="akses" class="form-label">User Type</label>
                <select class="form-select" name="akses" aria-label="Default select example">
                    <option value="admin" <?php if ($akses == "admin")
                        echo "selected"; ?>>Admin</option>
                    <option value="dokter" <?php if ($akses == "dokter")
                        echo "selected"; ?>>Dokter/Bangsal</option>
                    <option value="pasien" <?php if ($akses == "pasien")
                        echo "selected"; ?>>Pasien</option>
                    <option value="radiologi" <?php if ($akses == "radiologi")
                        echo "selected"; ?>>Dokter Radiologi
                    </option>
                </select>
                <button type="submit" name="submit" class="btn btn-primary" style="margin-top: 30px">Submit</button>
            </form>
        </div>
    </section>
</body>

</html>