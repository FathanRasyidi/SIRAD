<?php
session_start();
include 'koneksi.php';
$user_type = empty($_SESSION['usertype']) ? '' : $_SESSION['usertype'];
$usn = empty($_SESSION['username']) ? '' : $_SESSION['username'];

//mengambil data user yang login
if (isset($_COOKIE['user']) && !isset($_SESSION['login'])) {
    $usn = base64_decode($_COOKIE['user']);
    $sqlu = "SELECT * FROM user WHERE username = '$usn'";
    $qu = mysqli_query($connect, $sqlu);
    $dbu = mysqli_fetch_array($qu);
    $_SESSION['login'] = $dbu['nama'];
    header("location:index.php");
    exit();
}

//mengecek apakah user sudah login atau belum
if (empty($_COOKIE['user']) && !isset($_SESSION['login'])) {
    header("location:login.php?pesan=belum_login");
    exit();
}

// query untuk menampilkan data pasien
if ($user_type != 'pasien') {
    $sql = "SELECT * FROM data ORDER BY id DESC";
    $q = mysqli_query($connect, $sql);
} else {
    $sql = "SELECT * FROM data WHERE rekmed = '$usn'";
    $q = mysqli_query($connect, $sql);
}

// query untuk menghapus data pasien
if (isset($_GET['op'])) {
    $op = $_GET['op'];
    $id = empty($_GET['id']) ? '' : $_GET['id'];
    if ($op == 'delete') {
        // menghapus gambar yang sudah diupload
        $sql2 = "SELECT image FROM data WHERE id = '$id'";
        $q2 = mysqli_query($connect, $sql2);
        $row = mysqli_fetch_assoc($q2);
        $imagePath = isset($row['image']) ? explode(',', $row['image']) : '';
        foreach ($imagePath as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }
        // mengambil rekam medis pasien yang dihapus
        $Qrekmed = "SELECT rekmed FROM data WHERE id = '$id'";
        $Rrekmed = mysqli_query($connect, $Qrekmed);
        $row = mysqli_fetch_assoc($Rrekmed);
        $rekmed = $row['rekmed'];
        // menghapus akun pasien yang dihapus
        $sqld = "DELETE FROM user WHERE username = '$rekmed'";
        $qd = mysqli_query($connect, $sqld);
        // menghapus data dari database
        $sql = "DELETE FROM data WHERE id = '$id'";
        $q = mysqli_query($connect, $sql);
        if ($q) {
            echo "<script>alert('Data berhasil dihapus!')</script>";
            echo "<script>location.href='pasien.php'</script>";
        } else {
            echo "<script>alert('Gagal menghapus data!')</script>";
            echo "<script>location.href='pasien.php'</script>";
        }
    }
    if ($op == 'edit_sukses') {
        echo "<script>alert('Data berhasil diubah!')</script>";
        echo "<script>location.href='pasien.php'</script>";
    }
}

// fungsi untuk search data pasien
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT * FROM data WHERE rekmed LIKE '%$search%' OR nama_pasien LIKE '%$search%' OR tanggal_lahir LIKE '%$search%' OR jenis_periksa LIKE '%$search%' OR tanggal LIKE '%$search%'";
    $q = mysqli_query($connect, $sql);
}

// fungsi untuk mengisi expertise
if (isset($_POST['submit'])) {
    $expertise = $_POST['expertise'];
    $id = $_POST['id'];
    $sql = "UPDATE data SET expertise = '$expertise' WHERE id = '$id'";
    $q = mysqli_query($connect, $sql);
    if ($q) {
        echo "<script>location.href='pasien.php'</script>";
    } else {
        echo "<script>alert('Gagal mengisi expertise!')</script>";
        echo "<script>location.href='pasien.php'</script>";
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

                    <a class="flex items-center px-3 py-2 bg-gray-300 text-gray-600 transition-colors duration-300 transform rounded-lg  hover:bg-gray-200 hover:text-gray-700"
                        href="pasien.php">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>

                        <span class="mx-2 text-sm font-medium">Data Pasien</span>
                    </a>
                    <?php if ($user_type == "admin") { ?>
                        <a class="flex items-center px-3 py-2 text-gray-600 transition-colors duration-300 transform rounded-lg  hover:bg-gray-200 hover:text-gray-700"
                            href="user.php">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                aria-hidden="true" class="h-5 w-5">
                                <path fill-rule="evenodd"
                                    d="M18.685 19.097A9.723 9.723 0 0021.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 003.065 7.097A9.716 9.716 0 0012 21.75a9.716 9.716 0 006.685-2.653zm-12.54-1.285A7.486 7.486 0 0112 15a7.486 7.486 0 015.855 2.812A8.224 8.224 0 0112 20.25a8.224 8.224 0 01-5.855-2.438zM15.75 9a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"
                                    clip-rule="evenodd"></path>
                            </svg>

                            <span class="mx-2 text-sm font-medium">Data User</span>
                        </a>
                    <?php } ?>
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
            <div class="overflow-hidden rounded-lg border border-gray-300 shadow-md bg-gray-100 w-full pt-2 p-4 "
                style="align-content: center; background-image: url(https://previews.dropbox.com/p/thumb/AAvyFru8elv-S19NMGkQcztLLpDd6Y6VVVMqKhwISfNEpqV59iR5sJaPD4VTrz8ExV7WU9ryYPIUW8Gk2JmEm03OLBE2zAeQ3i7sjFx80O-7skVlsmlm0qRT0n7z9t07jU_E9KafA9l4rz68MsaZPazbDKBdcvEEEQPPc3TmZDsIhes1U-Z0YsH0uc2RSqEb0b83A1GNRo86e-8TbEoNqyX0gxBG-14Tawn0sZWLo5Iv96X-x10kVauME-Mc9HGS5G4h_26P2oHhiZ3SEgj6jW0KlEnsh2H_yTego0grbhdcN1Yjd_rLpyHUt5XhXHJwoqyJ_ylwvZD9-dRLgi_fM_7j/p.png?fv_content=true&size_mode=5); background-position: 90% center;">
                <h1 class="text-2xl font-medium mb-2">Data Pasien</h1>
                <?php if ($user_type == "admin" || $user_type == "radiologi") { ?>
                    <div class="inline-flex items-center rounded-md shadow-sm">
                        <a href="edit_pasien.php">
                            <button
                                class="text-slate-800 hover:text-blue-600 text-sm bg-white hover:bg-slate-100 border border-slate-200 rounded-lg font-medium px-4 py-2 inline-flex space-x-1 items-center">
                                <span><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </span>
                                <span>Input Data</span>
                            </button>
                        </a>
                    </div>
                <?php } ?>
                <?php if ($user_type != 'pasien') { ?>
                    <form action="" method="GET">
                        <div
                            class="w-full h-10 pl-3 pr-2 mt-3 bg-white border rounded-full flex justify-between items-center relative">
                            <input type="search" name="search" id="search" placeholder="Search"
                                value="<?php echo $search ?>"
                                class="appearance-none w-full outline-none focus:outline-none active:outline-none" />
                            <button type="submit" class="ml-1 outline-none focus:outline-none active:outline-none">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" viewBox="0 0 24 24" class="w-6 h-6">
                                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                <?php } ?>
            </div>
        </div>
        <div class="overflow-hidden rounded-lg border border-gray-300 shadow-md m-0">
            <?php if (mysqli_num_rows($q) == 0) { ?>
                <div class="flex bg-red-100 rounded-lg p-4 text-sm text-red-700" role="alert">
                    <svg class="w-5 h-5 inline mr-3" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <span class="font-medium">Data pasien tidak ditemukan!</span> Silahkan hubungi pihak yang
                        bersangkutan.
                    </div>
                </div>
            <?php } else { ?>
                <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                    <thead class="bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-700">Nama Pasien</div>
                                    <div class="text-gray-400">Tgl. Lahir</div>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900">No. Rekam Medis</th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900">Tgl. Pemeriksaan</th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900">Alamat</th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900">Jenis Pemeriksaan</th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900">Radiograf</th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900">Expertise</th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 border-t border-gray-100">
                        <?php while ($db = mysqli_fetch_array($q)) { ?>
                            <tr class="hover:bg-gray-50">
                                <th class="flex gap-3 px-6 py-4 font-normal text-gray-900">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-700"><?= $db['nama_pasien'] ?></div>
                                        <div class="text-gray-400"><?= $db['tanggal_lahir'] ?></div>
                                    </div>
                                </th>
                                <td class="px-6 py-4"><?= $db['rekmed'] ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-1 text-s font-semibold text-blue-600">
                                            <?= $db['tanggal'] ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4"><?= $db['alamat'] ?></td>
                                <td class="px-6 py-4"><?= $db['jenis_periksa'] ?></td>
                                <td class="px-6 py-4">
                                    <?php if ($db['image'] != null) {
                                        $image_arr = explode(',', $db['image']);
                                        foreach ($image_arr as $gambar) { ?>
                                            <a href="<?php echo $gambar ?>" target="_blank">
                                                <span
                                                    class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-600">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-blue-600"></span>
                                                    Lihat
                                                </span>
                                            </a>
                                        <?php } ?>
                                    </td>
                                <?php } ?>
                                <td class="px-6 py-4">
                                    <?php if ($db['expertise'] != null) { ?>
                                        <div class="flex gap-2">
                                            <div x-data="{ open: false }">
                                                <button @click="open = true">
                                                    <span
                                                        class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-1 text-xs font-semibold text-green-600">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-green-600"></span>
                                                        Lihat
                                                    </span>
                                                </button>
                                                <div x-show="open" class="fixed inset-0 flex items-center justify-center z-50">
                                                    <div class="bg-white rounded-lg p-5">
                                                        <h2 class="text-black w-96 text-lg font-medium mb-4">Expertise</h2>
                                                        <textarea cols="30" class="w-full border border-gray-300 rounded-lg p-2"
                                                            placeholder="Masukkan expertise"
                                                            readonly><?php echo $db['expertise'] ?></textarea>
                                                        <button @click="open = false"
                                                            class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if ($user_type == 'radiologi' || $user_type == 'admin') { ?>
                                                <div x-data="{ open: false }">
                                                    <button @click="open = true">
                                                        <span
                                                            class="inline-flex items-center gap-1 rounded-full bg-yellow-50 px-2 py-1 text-xs font-semibold text-yellow-600">
                                                            <span class="h-1.5 w-1.5 rounded-full bg-yellow-600"></span>
                                                            Edit
                                                        </span>
                                                    </button>
                                                    <div x-show="open" class="fixed inset-0 flex items-center justify-center z-50">
                                                        <div class="bg-white rounded-lg p-5">
                                                            <h2 class="text-black w-96 text-lg font-medium mb-4">Isi Expertise</h2>
                                                            <form action="" method="POST">
                                                                <input type="hidden" name="id" value="<?= $db['id'] ?>">
                                                                <textarea name="expertise" id="expertise" cols="30"
                                                                    class="w-full border border-gray-300 rounded-lg p-2"
                                                                    placeholder="Masukkan expertise"><?php echo $db['expertise'] ?></textarea>
                                                                <button type="submit" name="submit"
                                                                    class="mt-4 px-4 py-2 bg-green-500 text-white rounded-lg">Close</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } else if ($db['expertise'] == null && ($user_type == 'radiologi' || $user_type == 'admin')) { ?>
                                            <div x-data="{ open: false }">
                                                <button @click="open = true">
                                                    <span
                                                        class="inline-flex items-center gap-1 rounded-full bg-yellow-50 px-2 py-1 text-xs font-semibold text-yellow-600">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-yellow-600"></span>
                                                        Isi
                                                    </span>
                                                </button>
                                                <div x-show="open" class="fixed inset-0 flex items-center justify-center z-50">
                                                    <div class="bg-white rounded-lg p-5">
                                                        <h2 class="text-black w-96 text-lg font-medium mb-4">Isi Expertise</h2>
                                                        <form action="" method="POST">
                                                            <input type="hidden" name="id" value="<?= $db['id'] ?>">
                                                            <textarea name="expertise" id="expertise" cols="30" rows="10"
                                                                class="w-full border border-gray-300 rounded-lg p-2"
                                                                placeholder="Masukkan expertise"></textarea>
                                                            <button @click="open = false"
                                                                class="mt-4 px-4 py-2 bg-blue-500 mr-3 text-white rounded-lg">Close</button>
                                                            <button type="submit" name="submit"
                                                                class="mt-4 px-4 py-2 bg-green-500 text-white rounded-lg">Submit</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                    <?php } ?>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-4">
                                        <?php if ($user_type == "admin" || $user_type == "radiologi") { ?>
                                            <a x-data="{ tooltip: 'Edite' }" href="edit_pasien.php?op=edit&id=<?= $db['id'] ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="h-6 w-6" x-tooltip="tooltip">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                                </svg>
                                            </a>
                                        <?php } ?>
                                        <?php if ($user_type == "admin") { ?>
                                            <a x-data="{ tooltip: 'Delete' }" href="pasien.php?op=delete&id=<?= $db['id'] ?>"
                                                onclick="return confirm('Are you sure you want to delete this user?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="h-6 w-6" x-tooltip="tooltip">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php }
            } ?>
                </tbody>
            </table>
        </div>
    </div>

    </div>
</main>


</body>

</html>