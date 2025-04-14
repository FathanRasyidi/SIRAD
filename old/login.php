<?php
session_start();
$op = "";

if (isset($_GET['pesan'])) {
    $op = $_GET['pesan'];
    if ($op == 'logout') {
        echo "<script>alert('Anda telah berhasil logout');</script>";
        header("refresh:0;url=login.php");
    } elseif ($op == 'gagal') {
        echo "<script>alert('Login gagal! email dan password salah!');</script>";
        header("refresh:0;url=login.php");
    } elseif ($op == 'belum_login') {
        echo "<script>alert('Anda harus login terlebih dahulu');</script>";
        header("refresh:0;url=login.php");
    } elseif ($op == 'kosong') {
        echo "<script>alert('Username dan Password harus diisi');</script>";
        header("refresh:0;url=login.php");
    }
}

if (isset($_COOKIE['user']) ) {
    $_SESSION['login'] = $_COOKIE['user'];
    header("location:index.php");
    exit();
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
        body {
            background-color: #bcddae;
            background-size: cover;
            background-position: center;
            justify-content: center;
            align-items: center;
            display: flex;

        }

        .card {
            margin-top: 100px;
            border-radius: 30px;
            padding: 20px;
            padding-top: 10px;
            width: 400px;
        }

        input#username {
            text-indent: 25px;
            background-color: white;
            background-image: url('img/user.png');
            background-size: 20px;
            background-position: 10px 8px;
            background-repeat: no-repeat;
        }
        input#password {
            text-indent: 25px;
            background-color: white;
            background-image: url('img/pass.png');
            background-size: 20px;
            background-position: 10px 8px;
            background-repeat: no-repeat;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="card-body">
            <header style="margin-bottom: 15px">
                <div class="brand" style="font-weight: bold; margin-bottom: 10px; font-size: 25px">
                    <img src="img/sr.png" alt="logo" width="50px" style="margin-right: 15px">SIGRA ROSA
                </div>
                <hr>
                Sistem Informasi Radiografi RSJ. Ghrasia
            </header>
            <form method="POST" action="cek_login.php">
                <div class="mb-3">
                    <input type="username" class="form-control" id="username" name="username" placeholder="Username"
                        required>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password"
                        required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="kuki" name="kuki">
                    <label class="form-check-label" for="kuki" style="color: #81A263">Remember me</label>
                </div>
                <button type="submit" class="btn btn-success btn-lg w-100" name="login">Login</button>
            </form>
        </div>
    </div>
</body>

</html>