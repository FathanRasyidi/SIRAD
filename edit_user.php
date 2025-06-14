<?php
session_start();
include 'koneksi.php';
$user_type = empty($_SESSION['usertype']) ? '' : $_SESSION['usertype'];
$user_id =  empty($_SESSION['id_user']) ? '' : $_SESSION['id_user']; 

if (empty($_SESSION['login'])) {
    header("location:login.php?pesan=belum_login");
    exit();
}

if ($user_type != "admin") {
    header("location:javascript://history.go(-1)");
    exit();
}

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

if (isset($_GET['op'])) {
    $op = $_GET['op'];
    if ($op == 'edit') {
        $id = $_GET['id'];
        $sql = "SELECT * FROM user WHERE ID_USER = '$id'";
        $q = mysqli_query($connect, $sql);
        $db = mysqli_fetch_array($q);

        if (!$db) {
            $error = "Data tidak ditemukan";
        } else {
            $nama = $db['nama'];
            $username = $db['username'];
            $decryptPassword = base64_decode($db['password']);
            $password = $decryptPassword;
            $akses = $db['hak_akses'];
        }
    }
}

if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $akses = $_POST['hak_akses'];
    $dibuat = date("d/m/Y");

    $encryptedPassword = encrypt($password);

    //untuk insert data ke database
    if ($nama && $username && $password && $akses) {
        if ($op == 'edit') {
            $sql = "UPDATE user SET nama='$nama', username='$username', password='$encryptedPassword', hak_akses='$akses' WHERE ID_USER = $id";
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
                $sql = "INSERT INTO user (nama, username, password, hak_akses, dibuat) VALUES ('$nama', '$username', '$encryptedPassword', '$akses', '$dibuat')";
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
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SIRAD</title>
    <link rel="icon" href="img/sr.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            background-color: whitesmoke;
        }

        .fixed-sidebar {
            position: fixed;
            z-index: 1;
        }
    </style>
    <style>
        .-z-1 {
            z-index: -1;
        }

        .origin-0 {
            transform-origin: 0%;
        }

        input:focus~label,
        input:not(:placeholder-shown)~label,
        textarea:focus~label,
        textarea:not(:placeholder-shown)~label,
        select:focus~label,
        select:not([value='']):valid~label {
            /* @apply transform; scale-75; -translate-y-6; */
            --tw-translate-x: 0;
            --tw-translate-y: 0;
            --tw-rotate: 0;
            --tw-skew-x: 0;
            --tw-skew-y: 0;
            transform: translateX(var(--tw-translate-x)) translateY(var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
            --tw-scale-x: 0.75;
            --tw-scale-y: 0.75;
            --tw-translate-y: -1.5rem;
        }

        input:focus~label,
        select:focus~label {
            /* @apply text-black; left-0; */
            --tw-text-opacity: 1;
            color: rgba(0, 0, 0, var(--tw-text-opacity));
            left: 0px;
        }
    </style>
</head>
<!-- component -->

<main style="display: flex;">
    <!-- component -->
    <?php include 'navbar.php'; ?>

    <div class="flex-1 p-6 pt-0 ml-64">
        <!-- Header -->
        <div class="p-6 py-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Input/Edit Data Pasien</h1>
            <a class="navbar-brand flex items-center my-2">
                <img src="img/suisei.png" alt="Profile" width="50" height="50" class="rounded-full border-2" id="logo"
                    style="margin-right: 10px; border-color: #16a34a;">
                <?php include 'profile.php'; ?>
            </a>
        </div>
                <div class="mx-auto w-full pt-5 p-10 bg-white border-0 shadow-lg sm:rounded-xl">
                    <form method="POST" action="" id="form">
                        <div class="relative z-0 w-full mb-5">
                            <input type="text" name="nama" value="<?php echo $nama ?>" placeholder=" "
                                class="pt-3 pb-2 block w-full px-0 mt-0 bg-transparent border-0 border-b-2 appearance-none focus:outline-none focus:ring-0 focus:border-black border-gray-200"
                                required>
                            <label for="nama" class="absolute duration-300 top-3 -z-1 origin-0 text-gray-500">
                                Nama</label>
                        </div>

                        <div class="relative z-0 w-full mb-5">
                            <input type="username" name="username" value="<?php echo $username ?>" placeholder=" "
                                class="pt-3 pb-2 block w-full px-0 mt-0 bg-transparent border-0 border-b-2 appearance-none focus:outline-none focus:ring-0 focus:border-black border-gray-200"
                                required>
                            <label for="username" class="absolute duration-300 top-3 -z-1 origin-0 text-gray-500">
                                Username</label>
                        </div>

                        <div class="relative z-0 w-full mb-10">
                            <input type="text" name="password" value="<?php echo $password ?>" placeholder=" "
                                class="pt-3 pb-2 block w-full px-0 mt-0 bg-transparent border-0 border-b-2 appearance-none focus:outline-none focus:ring-0 focus:border-black border-gray-200"
                                required>
                            <label for="password" class="absolute duration-300 top-3 -z-1 origin-0 text-gray-500">
                                Password</label>
                        </div>

                        <fieldset class="relative z-0 w-full p-px mb-5">
                            <legend class="absolute text-gray-500 transform scale-75 -top-3 origin-0">Hak Akses</legend>
                            <div class="block pt-3 pb-2 space-x-20">
                                <label>
                                    <input type="radio" name="hak_akses" value="admin" <?php if ($akses == 'admin')
                                        echo 'checked' ?>
                                            class="mr-2 text-black border-2 border-gray-300 focus:border-gray-300 focus:ring-black"
                                            required />
                                        Admin
                                    </label>
                                    <label>
                                        <input type="radio" name="hak_akses" value="dpjp" <?php if ($akses == 'dpjp')
                                        echo 'checked' ?> <?php if ($op == 'edit' && $user_id == $id) echo 'disabled' ?>
                                            class="mr-2 text-black border-2 border-gray-300 focus:border-gray-300 focus:ring-black"
                                            required />
                                        Dokter Penanggung Jawab Pasien
                                    </label>
                                    <label>
                                        <input type="radio" name="hak_akses" value="radiologi" <?php if ($akses == 'radiologi')
                                        echo 'checked' ?> <?php if ($op == 'edit' && $user_id == $id) echo 'disabled' ?>
                                            class="mr-2 text-black border-2 border-gray-300 focus:border-gray-300 focus:ring-black"
                                            required />
                                        Dokter Radiologi
                                    </label>
                                    <label>
                                        <input type="radio" name="hak_akses" value="radiografer" <?php if ($akses == 'radiografer')
                                        echo 'checked' ?> <?php if ($op == 'edit' && $user_id == $id) echo 'disabled' ?>
                                            class="mr-2 text-black border-2 border-gray-300 focus:border-gray-300 focus:ring-black"
                                            required />
                                        Radiografer
                                    </label>
                                </div>
                            </fieldset>

                            <button id="button" type="submit" name="submit"
                                class="w-full px-6 py-3 mt-3 text-lg text-white transition-all duration-150 ease-linear rounded-lg shadow outline-none bg-yellow-500 hover:bg-yellow-600 hover:shadow-lg focus:outline-none">
                                Submit
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        </div>
    </main>


    </body>

    </html>