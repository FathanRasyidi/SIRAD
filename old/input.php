<?php
session_start();
$user_type = empty($_SESSION['usertype']) ? '' : $_SESSION['usertype'];

if (empty($_SESSION['login'])) {
    header("location:login.php?pesan=belum_login");
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
    <link href="./src/output.css" rel="stylesheet">
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
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="input.php">Input Data</a>
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
        <h1 class="mt-4">Input/Edit Data</h1>
    </div>

    <section class="fitur">
        <h4 style="margin-top: 0px;" >Input Data</h4>
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