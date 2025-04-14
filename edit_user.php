<?php
session_start();
include 'koneksi.php';
$user_type = empty($_SESSION['usertype']) ? '' : $_SESSION['usertype'];

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
    $dibuat = date("d/m/Y");

    $encryptedPassword = encrypt($password);

    //untuk insert data ke database
    if ($nama && $username && $password && $akses) {
        if ($op == 'edit') {
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
                $sql = "INSERT INTO user (nama, username, password, akses, dibuat) VALUES ('$nama', '$username', '$encryptedPassword', '$akses', '$dibuat')";
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
    <title>Sigra Rosa</title>
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
    <aside
        class="fixed-sidebar flex flex-col w-64 h-screen pb-6 px-5 py-8 overflow-y-auto bg-white border-r rtl:border-r-0 rtl:border-l">
        <a class="navbar-brand text-gray-600 flex items-center">
            <img src="img/sr.png" alt="" width="50" height="50" class="d-inline-block" id="logo"
                style="margin-right: 10px">
            <span class="ml-2">Sistem Informasi Radiografi RSJ. Ghrasia</span>
        </a>

        <div class="flex flex-col justify-between flex-1 mt-6">
            <nav class="-mx-3 space-y-6 ">
                <div class="space-y-3 ">
                    <label class="px-3 text-xs text-gray-500 uppercase ">navigasi</label>

                    <a class="flex items-center px-3 py-2 text-gray-600 transition-colors duration-300 transform rounded-lg  hover:bg-gray-200 hover:text-gray-700"
                        href="index.php">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
                        </svg>

                        <span class="mx-2 text-sm font-medium">Dashboard</span>
                    </a>

                    <a class="flex items-center px-3 py-2 text-gray-600 transition-colors duration-300 transform rounded-lg  hover:bg-gray-200 hover:text-gray-700"
                        href="pasien.php">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>

                        <span class="mx-2 text-sm font-medium">Data Pasien</span>
                    </a>
                    <a class="flex items-center px-3 py-2 bg-gray-300 text-gray-600 transition-colors duration-300 transform rounded-lg  hover:bg-gray-200 hover:text-gray-700"
                        href="user.php">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            aria-hidden="true" class="h-5 w-5">
                            <path fill-rule="evenodd"
                                d="M18.685 19.097A9.723 9.723 0 0021.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 003.065 7.097A9.716 9.716 0 0012 21.75a9.716 9.716 0 006.685-2.653zm-12.54-1.285A7.486 7.486 0 0112 15a7.486 7.486 0 015.855 2.812A8.224 8.224 0 0112 20.25a8.224 8.224 0 01-5.855-2.438zM15.75 9a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"
                                clip-rule="evenodd"></path>
                        </svg>

                        <span class="mx-2 text-sm font-medium">Data User</span>
                    </a>
                    <?php if ($user_type != 'pasien') { ?>
                        <a class="flex items-center px-3 py-2 text-gray-600 transition-colors duration-300 transform rounded-lg  hover:bg-gray-200 hover:text-gray-700"
                            href="akun_pasien.php">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6 group-hover:text-indigo-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            <span class="mx-2 text-sm font-medium">Akun Pasien</span>
                        </a>
                    <?php } ?>
                </div>
            </nav>
            <a class="flex align-bottom items-center px-3 py-2 text-red-600 transition-colors duration-300 transform rounded-lg hover:bg-gray-200 hover:text-red-600"
                href="logout.php">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"
                    class="h-5 w-5">
                    <path fill-rule="evenodd"
                        d="M12 2.25a.75.75 0 01.75.75v9a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM6.166 5.106a.75.75 0 010 1.06 8.25 8.25 0 1011.668 0 .75.75 0 111.06-1.06c3.808 3.807 3.808 9.98 0 13.788-3.807 3.808-9.98 3.808-13.788 0-3.808-3.807-3.808-9.98 0-13.788a.75.75 0 011.06 0z"
                        clip-rule="evenodd"></path>
                </svg>

                <span class="mx-2 text-sm font-medium">Logout</span>
            </a>
        </div>
    </aside>

    <div style="margin: 20px; width: 100%; margin-left: 280px;">
        <div class="flex flex-row mb-2">
            <div class="overflow-hidden rounded-lg border border-gray-300 shadow-md bg-gray-200 w-full"
                style="align-content: center; background-image: url(https://previews.dropbox.com/p/thumb/AAvyFru8elv-S19NMGkQcztLLpDd6Y6VVVMqKhwISfNEpqV59iR5sJaPD4VTrz8ExV7WU9ryYPIUW8Gk2JmEm03OLBE2zAeQ3i7sjFx80O-7skVlsmlm0qRT0n7z9t07jU_E9KafA9l4rz68MsaZPazbDKBdcvEEEQPPc3TmZDsIhes1U-Z0YsH0uc2RSqEb0b83A1GNRo86e-8TbEoNqyX0gxBG-14Tawn0sZWLo5Iv96X-x10kVauME-Mc9HGS5G4h_26P2oHhiZ3SEgj6jW0KlEnsh2H_yTego0grbhdcN1Yjd_rLpyHUt5XhXHJwoqyJ_ylwvZD9-dRLgi_fM_7j/p.png?fv_content=true&size_mode=5); background-position: 90% center;">
                <h1 class="text-2xl font-medium p-4">Input/Edit Data User</h1>
                <div class="mx-auto w-full pt-5 px-6 py-6 bg-white border-0 shadow-lg sm:rounded-xl">
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
                                    <input type="radio" name="akses" value="admin" <?php if ($akses == 'admin')
                                        echo 'checked' ?>
                                            class="mr-2 text-black border-2 border-gray-300 focus:border-gray-300 focus:ring-black"
                                            required />
                                        Admin
                                    </label>
                                    <label>
                                        <input type="radio" name="akses" value="dokter" <?php if ($akses == 'dokter')
                                        echo 'checked' ?>
                                            class="mr-2 text-black border-2 border-gray-300 focus:border-gray-300 focus:ring-black"
                                            required />
                                        Dokter/Bangsal
                                    </label>
                                    <label>
                                        <input type="radio" name="akses" value="radiologi" <?php if ($akses == 'radiologi')
                                        echo 'checked' ?>
                                            class="mr-2 text-black border-2 border-gray-300 focus:border-gray-300 focus:ring-black"
                                            required />
                                        Dokter Radiologi
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