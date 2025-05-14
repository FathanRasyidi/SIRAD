<?php
session_start();
include 'koneksi.php';
$user_type = empty($_SESSION['usertype']) ? '' : $_SESSION['usertype'];

function decodeBase64($encodedString) {
    return base64_decode($encodedString);
}

if (empty($_SESSION['login'])) {
    header("location:login.php?pesan=belum_login");
    exit();
}

if ($user_type == "pasien") {
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
    $sql = "SELECT * FROM user WHERE akses = 'pasien' AND (nama LIKE '%$search%' OR username LIKE '%$search%' OR password LIKE '%$search%') ";
    $q = mysqli_query($connect, $sql);
} else {
    $sql = "SELECT * FROM user WHERE akses = 'pasien' ORDER BY dibuat DESC";
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

    <div style="margin: 20px; width: 100%; margin-left: 280px;">
        <div class="flex flex-row mb-2">
            <div class="overflow-hidden rounded-lg border border-gray-300 shadow-md bg-gray-100 w-full pt-2 p-4 "
                style="align-content: center; background-image: url(https://previews.dropbox.com/p/thumb/AAvyFru8elv-S19NMGkQcztLLpDd6Y6VVVMqKhwISfNEpqV59iR5sJaPD4VTrz8ExV7WU9ryYPIUW8Gk2JmEm03OLBE2zAeQ3i7sjFx80O-7skVlsmlm0qRT0n7z9t07jU_E9KafA9l4rz68MsaZPazbDKBdcvEEEQPPc3TmZDsIhes1U-Z0YsH0uc2RSqEb0b83A1GNRo86e-8TbEoNqyX0gxBG-14Tawn0sZWLo5Iv96X-x10kVauME-Mc9HGS5G4h_26P2oHhiZ3SEgj6jW0KlEnsh2H_yTego0grbhdcN1Yjd_rLpyHUt5XhXHJwoqyJ_ylwvZD9-dRLgi_fM_7j/p.png?fv_content=true&size_mode=5); background-position: 90% center;">
                <h1 class="text-2xl font-medium mb-2">Akun Pasien</h1>
                <div class="flex bg-blue-100 rounded-lg p-4 text-sm text-blue-700" role="alert">
                    <svg class="w-5 h-5 inline mr-3" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <span class="font-medium">Info!</span> Akun pasien akan otomatis dibuat setelah input data
                        pasien.
                    </div>
                </div>
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
            </div>
        </div>
        <div class="overflow-hidden rounded-lg border border-gray-300 shadow-md m-0">
            <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-medium text-gray-900">Nama Pasien</th>
                        <th scope="col" class="px-6 py-4 font-medium text-gray-900">Username</th>
                        <th scope="col" class="px-6 py-4 font-medium text-gray-900">Password</th>
                        <th scope="col" class="px-6 py-4 font-medium text-gray-900">Hak Akses</th>
                        <th scope="col" class="px-6 py-4 font-medium text-gray-900">Created date</th>
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
                                <?php 
                                echo decodeBase64($db['password']);
                                ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-yellow-50 px-2 py-1 text-xs font-semibold text-yellow-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-yellow-600"></span>
                                        Pasien
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <?= $db['dibuat'] ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    </div>
</main>


</body>

</html>