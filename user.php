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

if (isset($_GET['op'])) {
    $op = $_GET['op'];
    if ($op == 'delete') {
        $id = $_GET['id'];
        $sql = "DELETE FROM user WHERE id = $id";
        mysqli_query($connect, $sql);
    } else if ($op == 'tambah_sukses') {
        echo "<script>alert('Data user berhasil ditambahkan');</script>";
    } else if ($op == 'edit_sukses') {
        echo "<script>alert('Data user berhasil diubah');</script>";
    }
}

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT * FROM user WHERE akses != 'pasien' AND (nama LIKE '%$search%' OR username LIKE '%$search%' OR akses LIKE '%$search%') ORDER BY akses";
    $q = mysqli_query($connect, $sql);
} else {
    $sql = "SELECT * FROM user WHERE akses != 'pasien' ORDER BY akses";
    $q = mysqli_query($connect, $sql);
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
            <h1 class="text-2xl font-bold">Akun User</h1>
            <a class="navbar-brand flex items-center my-2">
                <img src="img/suisei.png" alt="Profile" width="50" height="50" class="rounded-full border-2" id="logo"
                    style="margin-right: 10px; border-color: #16a34a;">
                <div>
                    <span class="block font-bold text-gray-900"><?= $_SESSION['login'] ?></span>
                    <span class="block text-sm text-gray-500"><?= $_SESSION['usertype'] ?></span>
                </div>
            </a>
        </div>
        <!-- Card -->
        <div class="bg-white border rounded-xl shadow-lg px-8 py-4 w-full">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl ml-7 mt-4">List Akun</h2>
                </div>
                <div class="inline-flex items-center rounded-md shadow-sm">
                    <a href="edit_user.php">
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
            </div>
            <hr class="border-t-2 border-gray-400 transform translate-y-1 -mx-8 mt-2 ml-4 w-28">
            <hr class="border border-gray-200 transform translate-y-1 -mx-8">
            <!-- Search -->
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
            <!-- Tabel -->
            <div class="overflow-hidden rounded-2xl border border-gray-300 shadow-md mb-4">
                <?php if (mysqli_num_rows($q) == 0) { ?>
                    <div class="flex bg-red-100 rounded-lg p-4 text-sm text-red-700" role="alert">
                        <svg class="w-5 h-5 inline mr-3" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <span class="font-medium">Data  tidak ditemukan!</span> Periksa kembali kata kunci pencarian
                        </div>
                    </div>
                <?php } else { ?>
                <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                    <thead class="bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900">Nama</th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900">Username</th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900">Password</th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900">Hak Akses</th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 border-t border-gray-100">
                        <?php while ($db = mysqli_fetch_array($q)) { ?>
                            <tr class="hover:bg-gray-50">
                                <th class="flex gap-3 px-6 py-4 font-normal text-gray-900">
                                    <div class="font-medium text-gray-700">
                                        <?= $db['nama'] ?>
                                    </div>
                                </th>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-600">
                                            <?= $db['username'] ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?= str_repeat('*', strlen($db['password'])) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-yellow-50 px-2 py-1 text-xs font-semibold text-yellow-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-yellow-600"></span>
                                            <?php if ($db['akses'] == "admin") {
                                                echo "Admin";
                                            } else if ($db['akses'] == "radiologi") {
                                                echo "Dokter Radiologi";
                                            } else if ($db['akses'] == "pasien") {
                                                echo "Pasien";
                                            } else if ($db['akses'] == "dokter") {
                                                echo "Dokter/Bangsal";
                                            } ?>
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-4">
                                        <a x-data="{ tooltip: 'Edite' }" href="edit_user.php?op=edit&id=<?= $db['id'] ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="h-6 w-6"
                                                x-tooltip="tooltip">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                            </svg>
                                        </a>
                                        <a x-data="{ tooltip: 'Delete' }" href="user.php?op=delete&id=<?= $db['id'] ?>"
                                            onclick="return confirm('Are you sure you want to delete this user?')">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="h-6 w-6"
                                                x-tooltip="tooltip">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } ?>
            </div>
        </div>
    </div>
</main>


</body>

</html>