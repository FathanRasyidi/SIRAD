<?php
session_start();
include 'koneksi.php';
$user_type = empty($_SESSION['usertype']) ? '' : $_SESSION['usertype'];
$usn = empty($_SESSION['username']) ? '' : $_SESSION['username'];
$user_id = empty($_SESSION['id_user']) ? '' : $_SESSION['id_user']; //diganti menjadi dpjp

//mengambil data user yang login
if (isset($_COOKIE['user']) && !isset($_SESSION['login'])) {
    $usn = base64_decode($_COOKIE['user']);
    $sqlu = "SELECT * FROM user WHERE username = '$usn'";
    $qu = mysqli_query($connect, $sqlu);
    $dbu = mysqli_fetch_array($qu);
    $_SESSION['login'] = $dbu['nama'];
    header("location:dashboard.php");
    exit();
}

//mengecek apakah user sudah login atau belum
if (empty($_COOKIE['user']) && !isset($_SESSION['login'])) {
    header("location:login.php?pesan=belum_login");
    exit();
}

// query untuk menampilkan data pasien
if ($user_type != 'pasien' && $user_type != 'dpjp') {
    // $sql = "SELECT * FROM pemeriksaan ORDER BY ID_PEMERIKSAAN DESC";
    $sql = "SELECT pemeriksaan.*, pasien.nama_pasien, pasien.alamat, pasien.tanggal_lahir, user.nama, user.ID_USER
        FROM pemeriksaan
        JOIN pasien ON pemeriksaan.ID_PASIEN = pasien.ID_PASIEN
        JOIN user ON pemeriksaan.ID_USER = user.ID_USER
        ORDER BY pemeriksaan.ID_PEMERIKSAAN DESC";
    $q = mysqli_query($connect, $sql);
} elseif ($user_type == 'dpjp') {
    $sql = "SELECT pemeriksaan.*, pasien.nama_pasien, pasien.alamat, pasien.tanggal_lahir, user.nama, user.ID_USER
        FROM pemeriksaan
        JOIN pasien ON pemeriksaan.ID_PASIEN = pasien.ID_PASIEN
        JOIN user ON pemeriksaan.ID_USER = user.ID_USER
        WHERE pemeriksaan.ID_USER = '$user_id'";
    $q = mysqli_query($connect, $sql);
} else {
    // $sql = "SELECT * FROM pemeriksaan WHERE no_rekam_medis = '$usn'";
    $sql = "SELECT pemeriksaan.*, pasien.nama_pasien, pasien.alamat, pasien.tanggal_lahir, user.nama, user.ID_USER
        FROM pemeriksaan
        JOIN pasien ON pemeriksaan.ID_PASIEN = pasien.ID_PASIEN
        JOIN user ON pemeriksaan.ID_USER = user.ID_USER
        WHERE pemeriksaan.no_rekam_medis = '$usn'";

    $q = mysqli_query($connect, $sql);
}

// query untuk menghapus data pasien
if (isset($_GET['op'])) {
    $op = $_GET['op'];
    $id = empty($_GET['id']) ? '' : $_GET['id'];
    if ($op == 'delete') {
        // menghapus gambar yang sudah diupload
        $sql2 = "SELECT gambar_pemeriksaan FROM pemeriksaan WHERE ID_PEMERIKSAAN = '$id'";
        $q2 = mysqli_query($connect, $sql2);
        $row = mysqli_fetch_assoc($q2);
        $imagePath = isset($row['gambar_pemeriksaan']) ? explode(',', $row['gambar_pemeriksaan']) : '';
        foreach ($imagePath as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }
        // mengambil rekam medis pasien yang dihapus
        $Qrekmed = "SELECT no_rekam_medis FROM pemeriksaan WHERE ID_PEMERIKSAAN = '$id'";
        $Rrekmed = mysqli_query($connect, $Qrekmed);
        $row = mysqli_fetch_assoc($Rrekmed);
        $rekmed = $row['no_rekam_medis'];
        // menghapus akun pasien yang dihapus
        $sqld = "DELETE FROM user WHERE username = '$rekmed'";
        $qd = mysqli_query($connect, $sqld);
        // menghapus data dari database
        $sql = "DELETE FROM pemeriksaan WHERE ID_PEMERIKSAAN = '$id'";
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
    // SEHARUSNYA ADA PERUBAHAN PADA QUERY KARENA ADANYA PERUBAHAN STRUKTUR DATABASE
    $sql = "SELECT pemeriksaan.*, pasien.nama_pasien, pasien.alamat, pasien.tanggal_lahir, user.nama, user.ID_USER
            FROM pemeriksaan
            JOIN pasien ON pemeriksaan.ID_PASIEN = pasien.ID_PASIEN
            JOIN user ON pemeriksaan.ID_USER = user.ID_USER
            WHERE pemeriksaan.no_rekam_medis LIKE '%$search%' 
            OR pasien.nama_pasien LIKE '%$search%' 
            OR pasien.tanggal_lahir LIKE '%$search%' 
            OR pemeriksaan.jenis_pemeriksaan LIKE '%$search%' 
            OR pemeriksaan.tanggal_pemeriksaan LIKE '%$search%'
            OR user.nama LIKE '%$search%'";
    $q = mysqli_query($connect, $sql);
}

// fungsi untuk mengisi expertise
if (isset($_POST['submit'])) {
    $expertise = $_POST['expertise'];
    $id = $_POST['id'];
    $sql = "UPDATE pemeriksaan SET expertise = '$expertise' WHERE ID_PEMERIKSAAN = '$id'";
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
</head>
<!-- component -->

<main style="display: flex;">
    <!-- component -->
    <?php include 'navbar.php'; ?>

    <div class="flex-1 p-6 pt-0 ml-64">
        <!-- Header -->
        <div class="p-6 py-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Data Pasien</h1>
            <a class="navbar-brand flex items-center my-2">
                <img src="img/suisei.png" alt="Profile" width="50" height="50" class="rounded-full border-2" id="logo"
                    style="margin-right: 10px; border-color: #16a34a;">
                <?php include 'profile.php'; ?>
            </a>
        </div>
        <!-- Card -->
        <div class="bg-white border rounded-xl shadow-lg px-8 py-4 w-full">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl ml-7 mt-4">Pemeriksaan</h2>
                </div>
                <?php if ($user_type == "admin" || $user_type == "radiografer") { ?>
                    <div class="inline-flex items-center rounded-md shadow-sm">
                        <a href="edit_pasien.php">
                            <button
                                class="flex items-center bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Input Data</span>
                            </button>
                        </a>
                    </div>
                <?php } ?>
            </div>
            <hr class="border-t-2 border-gray-400 transform translate-y-1 -mx-8 mt-2 ml-4 w-36">
            <hr class="border border-gray-200 transform translate-y-1 -mx-8">
            <!-- Search -->
            <?php if ($user_type != 'pasien') { ?>
                <form action="" method="GET">
                    <div
                        class="w-2/12 h-10 pl-3 pr-2 my-5 bg-white border rounded-full flex justify-between items-center relative">
                        <button type="submit"
                            class="mr-2 outline-none focus:outline-none active:outline-none text-gray-400 hover:text-gray-700">
                            <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" viewBox="0 0 24 24" class="w-6 h-6">
                                <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                        <input type="search" name="search" id="search" placeholder="Search" value="<?php echo $search ?>"
                            class="appearance-none w-full outline-none focus:outline-none active:outline-none" />

                    </div>
                </form>
            <?php } ?>
            <!-- Tabel  -->
            <div class="overflow-hidden rounded-2xl border border-gray-300 shadow-md mb-4 mt-6">
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
                                <th scope="col" class="px-6 py-4 font-medium text-gray-900">DPJP</th>
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
                                    <td class="px-6 py-4"><?= $db['no_rekam_medis'] ?></td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2">
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-1 text-s font-semibold text-blue-600">
                                                <?= $db['tanggal_pemeriksaan'] ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4"><?= $db['alamat'] ?></td>
                                    <td class="px-6 py-4"><?= $db['jenis_pemeriksaan'] ?></td>
                                    <td class="px-6 py-4"><?= $db['nama'] ?></td>
                                    <td class="px-6 py-4">
                                        <?php if ($db['gambar_pemeriksaan'] != null) {
                                            // $image_arr = explode(',', $db['gambar_pemeriksaan']);
                                            // foreach ($image_arr as $gambar) { ?>
                                            <div x-data="{ open: false }">
                                                <button @click="open = true">
                                                    <span
                                                        class="inline-flex items-center gap-1 rounded-xl bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-600">
                                                        Lihat
                                                    </span>
                                                </button>
                                                <div x-show="open" class="fixed inset-0 flex items-center justify-center z-50 ">
                                                    <div
                                                        class="bg-gray-50 border border-gray-400 shadow-xl rounded-xl p-5 py-2 pb-1">
                                                        <?php $tampil_foto = base64_encode($db['gambar_pemeriksaan']); ?>
                                                        <div class="flex justify-between items-center mb-2">
                                                            <h2 class="text-xl font-bold text-gray-900">Radiograf</h2>
                                                            <button @click="open = false"
                                                                class="text-red-600 hover:text-red-700 text-4xl font-bold">
                                                                ×
                                                            </button>
                                                        </div>
                                                        <hr class="border border-gray-200 -mx-5">
                                                        <img src="data:image/jpeg;base64,<?= $tampil_foto ?>"
                                                            class="min-w-96 min-h-16 max-w-[70vw] max-h-[70vh] w-auto h-auto object-contain my-4"
                                                            alt="Gambar tidak dapat ditampilkan">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    <?php } ?>
                                    <td class="px-6 py-4">
                                        <?php if ($db['expertise'] != null) { ?>
                                            <div class="flex gap-2">
                                                <div x-data="{ open: false }">
                                                    <button @click="open = true">
                                                        <span
                                                            class="inline-flex items-center gap-1 rounded-xl bg-green-100 px-3 py-1 text-xs font-semibold text-green-600">
                                                            Lihat
                                                        </span>
                                                    </button>
                                                    <div x-show="open" class="fixed inset-0 flex items-center justify-center z-50">
                                                        <div class="bg-gray-50 border border-gray-400 shadow-xl rounded-xl p-5">
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
                                                                class="inline-flex items-center gap-1 rounded-xl bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-600">
                                                                Edit
                                                            </span>
                                                        </button>
                                                        <div x-show="open" class="fixed inset-0 flex items-center justify-center z-50">
                                                            <div class="bg-gray-50 border border-gray-400 shadow-xl rounded-xl p-5">
                                                                <h2 class="text-black w-96 text-lg font-medium mb-4">Isi Expertise</h2>
                                                                <form action="" method="POST">
                                                                    <input type="hidden" name="id" value="<?= $db['ID_PEMERIKSAAN'] ?>">
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
                                                            class="inline-flex items-center gap-1 rounded-xl bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-600">
                                                            Isi
                                                        </span>
                                                    </button>
                                                    <div x-show="open" class="fixed inset-0 flex items-center justify-center z-50">
                                                        <div class="bg-gray-50 border border-gray-400 shadow-xl rounded-xl p-5">
                                                            <h2 class="text-black w-96 text-lg font-medium mb-4">Isi Expertise</h2>
                                                            <form action="" method="POST">
                                                                <input type="hidden" name="id" value="<?= $db['ID_PEMERIKSAAN'] ?>">
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
                                            <?php if ($user_type == "admin" || $user_type == "radiografer") { ?>
                                                <a x-data="{ tooltip: 'Edite' }"
                                                    href="edit_pasien.php?op=edit&id=<?= $db['ID_PEMERIKSAAN'] ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="1.5" stroke="currentColor" class="h-6 w-6"
                                                        x-tooltip="tooltip">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                                    </svg>
                                                </a>
                                            <?php } ?>
                                            <?php if ($user_type == "admin" || $user_type == "radiografer") { ?>
                                                <a x-data="{ tooltip: 'Delete' }"
                                                    href="pasien.php?op=delete&id=<?= $db['ID_PEMERIKSAAN'] ?>"
                                                    onclick="return confirm('Are you sure you want to delete this user?')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="1.5" stroke="currentColor" class="h-6 w-6"
                                                        x-tooltip="tooltip">
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

    </div>
</main>


</body>

</html>