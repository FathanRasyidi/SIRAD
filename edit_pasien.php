<?php
session_start();
include 'koneksi.php';
$user_type = empty($_SESSION['usertype']) ? '' : $_SESSION['usertype'];

if (empty($_SESSION['login'])) {
    header("location:login.php?pesan=belum_login");
    exit();
}

if ($user_type != "admin" && $user_type != "radiologi") {
    header("location:javascript://history.go(-1)");
    exit();
}

$op = "";
$name = "";
$rekmed = "";
$tanggal = "";
$tgl_lahir = "";
$alamat = "";
$jenis_periksa = "";
$image = "";


if (isset($_GET['op'])) {
    $op = $_GET['op'];
    if ($op == 'edit') {
        $id = $_GET['id'];
        $sql = "SELECT * FROM data WHERE id = '$id'";
        $q = mysqli_query($connect, $sql);
        $db = mysqli_fetch_array($q);

        if (!$db) {
            $error = "Data tidak ditemukan";
        } else {
            $rekmed = $db['rekmed'];
            $tanggal = $db['tanggal'];
            $name = $db['nama_pasien'];
            $tgl_lahir = $db['tanggal_lahir'];
            $alamat = $db['alamat'];
            $jenis_periksa = $db['jenis_periksa'];
            $image = $db['image'];
        }
    }
}

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $rekmed = $_POST['rekmed'];
    $tanggal = date('d/m/Y', strtotime($_POST['tanggal']));
    $tgl_lahir = date('d/m/Y', strtotime($_POST['tgl_lahir']));
    $alamat = $_POST['alamat'];
    $jenis_periksa = $_POST['jenis_periksa'];
    $image = [];
    $sekarang = date('d/m/Y');

    //untuk insert data ke database
    if ($name && $rekmed && $tanggal && $tgl_lahir && $alamat && $jenis_periksa) {
        if ($op == 'edit') {
            // hapus gambar lama
            $sqlG = "SELECT image FROM data WHERE id = '$id'";
            $qG = mysqli_query($connect, $sqlG);
            $row = mysqli_fetch_assoc($qG);
            $imagePath = isset($row['image']) ? explode(',', $row['image']) : '';
            foreach ($imagePath as $path) {
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            // untuk upload foto
            if (isset($_FILES["foto"])) {
                for ($i = 0; $i < count($_FILES["foto"]["name"]); $i++) {
                    $file_name = $_FILES["foto"]["name"][$i];
                    $file_tmp = $_FILES["foto"]["tmp_name"][$i];
                    $file_type = $_FILES["foto"]["type"][$i];
                    $file_size = $_FILES["foto"]["size"][$i];
                    $file_error = $_FILES["foto"]["error"][$i];

                    if ($file_error === 0) {
                        $file_destination = "uploads/" . $rekmed . "_" . uniqid() . "_" . $file_name;
                        if (!is_dir("uploads")) {
                            mkdir("uploads");
                        }
                        move_uploaded_file($file_tmp, $file_destination);
                        $image[] = $file_destination;
                    } else {
                        $error = "Gagal mengunggah file";
                    }
                }
            }
            $image_path = implode(',', $image);
            $sql = "UPDATE data SET tanggal = '$tanggal', nama_pasien = '$name', tanggal_lahir = '$tgl_lahir', alamat = '$alamat', jenis_periksa = '$jenis_periksa', image = '$image_path' WHERE id = '$id'";
            $query = mysqli_query($connect, $sql);
            // if ($image_path != "") {
            //     $sqli = "UPDATE data SET image = IF(image = '', '$image_path', CONCAT(image, ',', '$image_path')) WHERE id = '$id'";
            //     $queryi = mysqli_query($connect, $sqli);
            // }
            if ($query) {
                header("location:pasien.php?op=edit_sukses");
            } else {
                echo "<script>alert('Data gagal diubah');</script>";
            }
        } else {
            $checkRekmedQuery = "SELECT * FROM data WHERE rekmed = '$rekmed'";
            $checkRekmedResult = mysqli_query($connect, $checkRekmedQuery);
            if (mysqli_num_rows($checkRekmedResult) > 0) {
                header("location:javascript://history.go(-1)");
            } else {
                // untuk upload foto
                if (isset($_FILES["foto"])) {
                    for ($i = 0; $i < count($_FILES["foto"]["name"]); $i++) {
                        $file_name = $_FILES["foto"]["name"][$i];
                        $file_tmp = $_FILES["foto"]["tmp_name"][$i];
                        $file_type = $_FILES["foto"]["type"][$i];
                        $file_size = $_FILES["foto"]["size"][$i];
                        $file_error = $_FILES["foto"]["error"][$i];

                        if ($file_error === 0) {
                            $file_destination = "uploads/" . $rekmed . "_" . uniqid() . "_" . $file_name;
                            if (!is_dir("uploads")) {
                                mkdir("uploads");
                            }
                            move_uploaded_file($file_tmp, $file_destination);
                            $image[] = $file_destination;
                        } else {
                            $error = "Gagal mengunggah file";
                        }
                    }
                }
                $image_path = implode(',', $image);
                // insert data
                $sql = "INSERT INTO data (rekmed, tanggal, nama_pasien, tanggal_lahir, alamat, jenis_periksa, image, expertise) VALUES ('$rekmed', '$tanggal', '$name', '$tgl_lahir', '$alamat', '$jenis_periksa', '$image_path', '')";
                $query = mysqli_query($connect, $sql);
                function encrypt($data)
                {
                    $encryptedData = base64_encode($data);
                    return $encryptedData;
                }
                $encryptedData = encrypt($tgl_lahir);
                // membuat akun pasien
                $sqln = "INSERT INTO user (username, password, nama, akses, dibuat) VALUES ('$rekmed', '$encryptedData', '$name', 'pasien', '$sekarang')";
                $queryn = mysqli_query($connect, $sqln);
                if ($query) {
                    header("location:pasien.php?op=tambah_sukses");
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
                        <a class="flex items-center px-3 py-2  text-gray-600 transition-colors duration-300 transform rounded-lg  hover:bg-gray-200 hover:text-gray-700"
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
            <div class="overflow-hidden rounded-lg border border-gray-300 shadow-md bg-gray-200 w-full"
                style="align-content: center; background-image: url(https://previews.dropbox.com/p/thumb/AAvyFru8elv-S19NMGkQcztLLpDd6Y6VVVMqKhwISfNEpqV59iR5sJaPD4VTrz8ExV7WU9ryYPIUW8Gk2JmEm03OLBE2zAeQ3i7sjFx80O-7skVlsmlm0qRT0n7z9t07jU_E9KafA9l4rz68MsaZPazbDKBdcvEEEQPPc3TmZDsIhes1U-Z0YsH0uc2RSqEb0b83A1GNRo86e-8TbEoNqyX0gxBG-14Tawn0sZWLo5Iv96X-x10kVauME-Mc9HGS5G4h_26P2oHhiZ3SEgj6jW0KlEnsh2H_yTego0grbhdcN1Yjd_rLpyHUt5XhXHJwoqyJ_ylwvZD9-dRLgi_fM_7j/p.png?fv_content=true&size_mode=5); background-position: 90% center;">
                <h1 class="text-2xl font-medium p-4">Input/Edit Data Pasien</h1>
                <div class="mx-auto w-full pt-5 px-6 py-6 bg-white border-0 shadow-lg sm:rounded-xl">
                    <div class="flex bg-yellow-100 rounded-lg p-4 mb-4 text-sm text-yellow-700" role="alert">
                        <svg class="w-5 h-5 inline mr-3" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <span class="font-medium">Warning!</span> Apabila No. Rekam Medis sudah ada di database,
                            data baru tidak dapat di submit.
                        </div>
                    </div>
                    <form method="POST" action="" id="form" enctype="multipart/form-data">
                        <div class="relative z-0 w-full mb-5">
                            <input type="number" name="rekmed" value="<?php echo $rekmed ?>" placeholder=" " min="0"
                                class="pt-3 pb-2 block w-full px-0 mt-0 bg-transparent border-0 border-b-2 appearance-none focus:outline-none focus:ring-0 focus:border-black border-gray-200"
                                required>
                            <label for="rekmed" class="absolute duration-300 top-3 -z-1 origin-0 text-gray-500">
                                No. Rekam Medis</label>
                        </div>

                        <div class="relative z-0 w-full mb-5">
                            <input type="date" name="tanggal" value="<?php if ($tanggal != null)
                                echo date('Y-m-d', strtotime(str_replace('/', '-', $tanggal))) ?>" placeholder=" "
                                    class="pt-3 pb-2 block w-full px-0 mt-0 bg-transparent border-0 border-b-2 appearance-none focus:outline-none focus:ring-0 focus:border-black border-gray-200"
                                    required>
                                <label for="tanggal" class="absolute duration-300 top-3 -z-1 origin-0 text-gray-500">
                                    Tgl. Pemeriksaan</label>
                            </div>

                            <div class="relative z-0 w-full mb-10">
                                <input type="text" name="name" value="<?php echo $name ?>" placeholder=" "
                                class="pt-3 pb-2 block w-full px-0 mt-0 bg-transparent border-0 border-b-2 appearance-none focus:outline-none focus:ring-0 focus:border-black border-gray-200"
                                required>
                            <label for="name" class="absolute duration-300 top-3 -z-1 origin-0 text-gray-500">
                                Nama Pasien</label>
                        </div>

                        <div class="relative z-0 w-full mb-10">
                            <input type="date" name="tgl_lahir" value="<?php if ($tgl_lahir != null)
                                echo date('Y-m-d', strtotime(str_replace('/', '-', $tgl_lahir))) ?>" placeholder=" "
                                    class="pt-3 pb-2 block w-full px-0 mt-0 bg-transparent border-0 border-b-2 appearance-none focus:outline-none focus:ring-0 focus:border-black border-gray-200"
                                    required>
                                <label for="tgl_lahir" class="absolute duration-300 top-3 -z-1 origin-0 text-gray-500">
                                    Tgl. Lahir</label>
                            </div>

                            <div class="relative z-0 w-full mb-10">
                                <input type="text" name="alamat" value="<?php echo $alamat ?>" placeholder=" "
                                class="pt-3 pb-2 block w-full px-0 mt-0 bg-transparent border-0 border-b-2 appearance-none focus:outline-none focus:ring-0 focus:border-black border-gray-200"
                                required>
                            <label for="alamat" class="absolute duration-300 top-3 -z-1 origin-0 text-gray-500">
                                Alamat</label>
                        </div>

                        <div class="relative z-0 w-full mb-5">
                            <label for="jenis_periksa" name="jenis_periksa"
                                class=" duration-300 top-3 -z-1 origin-0 text-gray-500">
                                Jenis Pemeriksaan</label>
                            <select name="jenis_periksa" id="jenis_periksa"
                                class="pt-3 pb-2 block w-full px-0 mt-0 bg-transparent border-0 border-b-2 appearance-none focus:outline-none focus:ring-0 focus:border-black border-gray-200"
                                required>
                                <option value="THORAX PA" <?php if ($jenis_periksa == 'THORAX PA')
                                    echo 'selected'; ?>>
                                    THORAX PA</option>
                                <option value="THORAX PA/LAT" <?php if ($jenis_periksa == 'THORAX PA/LAT')
                                    echo 'selected'; ?>>THORAX PA/LAT</option>
                                <option value="THORAX AP" <?php if ($jenis_periksa == 'THORAX AP')
                                    echo 'selected'; ?>>
                                    THORAX AP</option>
                                <option value="CRANIUM 2 POSISI" <?php if ($jenis_periksa == 'CRANIUM 2 POSISI')
                                    echo 'selected'; ?>>CRANIUM 2 POSISI</option>
                                <option value="TMJ" <?php if ($jenis_periksa == 'TMJ')
                                    echo 'selected'; ?>>TMJ</option>
                                <option value="CERVICAL AP/LAT" <?php if ($jenis_periksa == 'CERVICAL AP/LAT')
                                    echo 'selected'; ?>>CERVICAL AP/LAT</option>
                                <option value="CLAVICULA" <?php if ($jenis_periksa == 'CLAVICULA')
                                    echo 'selected'; ?>>CLAVICULA</option>
                                <option value="ABDOMEN/BNO" <?php if ($jenis_periksa == 'ABDOMEN/BNO')
                                    echo 'selected'; ?>>ABDOMEN/BNO</option>
                                <option value="PELVIS AP" <?php if ($jenis_periksa == 'PELVIS AP')
                                    echo 'selected'; ?>>PELVIS AP</option>
                                <option value="SHOULDER JOINT" <?php if ($jenis_periksa == 'SHOULDER JOINT')
                                    echo 'selected'; ?>>SHOULDER JOINT</option>
                                <option value="CUBITI/ELBOW" <?php if ($jenis_periksa == 'CUBITI/ELBOW')
                                    echo 'selected'; ?>>CUBITI/ELBOW</option>
                                <option value="ANTEBRACHI" <?php if ($jenis_periksa == 'ANTEBRACHI')
                                    echo 'selected'; ?>>ANTEBRACHI</option>
                                <option value="WRIST JOINT" <?php if ($jenis_periksa == 'WRIST JOINT')
                                    echo 'selected'; ?>>WRIST JOINT</option>
                                <option value="MANUS" <?php if ($jenis_periksa == 'MANUS')
                                    echo 'selected'; ?>>MANUS</option>
                                <option value="GENU" <?php if ($jenis_periksa == 'GENU')
                                    echo 'selected'; ?>>GENU</option>
                                <option value="CRURIS" <?php if ($jenis_periksa == 'CRURIS')
                                    echo 'selected'; ?>>CRURIS</option>
                                <option value="ANKLE JOINT" <?php if ($jenis_periksa == 'ANKLE JOINT')
                                    echo 'selected'; ?>>ANKLE JOINT</option>
                                <option value="PEDIS" <?php if ($jenis_periksa == 'PEDIS')
                                    echo 'selected'; ?>>PEDIS</option>
                                <option value="LUMBAL AP/LAT" <?php if ($jenis_periksa == 'LUMBAL AP/LAT')
                                    echo 'selected'; ?>>LUMBAL AP/LAT</option>
                                <option value="LUMBAL AP/LAT/OBL" <?php if ($jenis_periksa == 'LUMBAL AP/LAT/OBL')
                                    echo 'selected'; ?>>LUMBAL AP/LAT/OBL</option>
                                <option value="LUMBO SACRAL AP/LAT" <?php if ($jenis_periksa == 'LUMBO SACRAL AP/LAT')
                                    echo 'selected'; ?>>LUMBO SACRAL AP/LAT</option>
                                <option value="VERT.THORACHAL AP/LAT" <?php if ($jenis_periksa == 'VERT.THORACHAL AP/LAT')
                                    echo 'selected'; ?>>VERT.THORACHAL AP/LAT</option>
                                <option value="FEMUR" <?php if ($jenis_periksa == 'FEMUR')
                                    echo 'selected'; ?>>FEMUR</option>
                                <option value="NASAL" <?php if ($jenis_periksa == 'NASAL')
                                    echo 'selected'; ?>>NASAL</option>
                                <option value="SKY LINE" <?php if ($jenis_periksa == 'SKY LINE')
                                    echo 'selected'; ?>>SKY LINE</option>
                                <option value="PATELLA" <?php if ($jenis_periksa == 'PATELLA')
                                    echo 'selected'; ?>>PATELLA</option>
                                <option value="CALCANEUS" <?php if ($jenis_periksa == 'CALCANEUS')
                                    echo 'selected'; ?>>CALCANEUS</option>
                                <option value="SCAPULA" <?php if ($jenis_periksa == 'SCAPULA')
                                    echo 'selected'; ?>>SCAPULA</option>
                                <option value="HUMERUS" <?php if ($jenis_periksa == 'HUMERUS')
                                    echo 'selected'; ?>>HUMERUS</option>
                                <option value="USG" <?php if ($jenis_periksa == 'USG')
                                    echo 'selected'; ?>>USG</option>
                            </select>
                        </div>


                        <!-- component -->
                        <div class="w-full  bg-white">
                            <div class="container mx-auto h-full flex flex-col justify-center items-center">
                                <div id="images-container"></div>
                                <div class="flex w-full">
                                    <div id="multi-upload-button"
                                        class="w-2/12 inline-flex items-center px-4 py-2 bg-gray-600 border border-gray-600 rounded-l font-semibold cursor-pointer text-sm text-white tracking-widest hover:bg-gray-500 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ">
                                        Pilih File
                                    </div>
                                    <div
                                        class="w-full lg:w-full border border-gray-300 rounded-r-md flex items-center justify-between">
                                        <span id="multi-upload-text" class="p-2"></span>
                                        <button id="multi-upload-delete" class="hidden" onclick="removeMultiUpload()">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="fill-current text-red-700 w-3 h-3" viewBox="0 0 320 512">
                                                <path
                                                    d="M310.6 361.4c12.5 12.5 12.5 32.75 0 45.25C304.4 412.9 296.2 416 288 416s-16.38-3.125-22.62-9.375L160 301.3L54.63 406.6C48.38 412.9 40.19 416 32 416S15.63 412.9 9.375 406.6c-12.5-12.5-12.5-32.75 0-45.25l105.4-105.4L9.375 150.6c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L160 210.8l105.4-105.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-105.4 105.4L310.6 361.4z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <input type="file" name="foto[]" id="multi-upload-input" class="hidden" multiple
                                    required>
                            </div>
                        </div>

                        <script>
                            //all ids and some classes are importent for this script

                            multiUploadButton = document.getElementById("multi-upload-button");
                            multiUploadInput = document.getElementById("multi-upload-input");
                            imagesContainer = document.getElementById("images-container");
                            multiUploadDisplayText = document.getElementById("multi-upload-text");
                            multiUploadDeleteButton = document.getElementById("multi-upload-delete");

                            multiUploadButton.onclick = function () {
                                multiUploadInput.click(); // this will trigger the click event
                            };

                            multiUploadInput.addEventListener('change', function (event) {

                                if (multiUploadInput.files) {
                                    let files = multiUploadInput.files;

                                    // show the text for the upload button text filed
                                    multiUploadDisplayText.innerHTML = files.length + ' files selected';

                                    // removes styles from the images wrapper container in case the user readd new images
                                    imagesContainer.innerHTML = '';
                                    imagesContainer.classList.remove("w-full", "grid", "grid-cols-1", "sm:grid-cols-2", "md:grid-cols-3", "lg:grid-cols-4", "gap-4");

                                    // add styles to the images wrapper container
                                    imagesContainer.classList.add("w-full", "grid", "grid-cols-1", "sm:grid-cols-2", "md:grid-cols-3", "lg:grid-cols-4", "gap-4");

                                    // the delete button to delete all files
                                    multiUploadDeleteButton.classList.add("z-100", "p-2", "my-auto");
                                    multiUploadDeleteButton.classList.remove("hidden");

                                    Object.keys(files).forEach(function (key) {

                                        let file = files[key];

                                        // the FileReader object is needed to display the image
                                        let reader = new FileReader();
                                        reader.readAsDataURL(file);
                                        reader.onload = function () {

                                            // for each file we create a div to contain the image
                                            let imageDiv = document.createElement('div');
                                            imageDiv.classList.add("h-64", "mb-3", "w-full", "p-3", "rounded-lg", "bg-cover", "bg-center", "border", "border-gray-300");
                                            imageDiv.style.backgroundImage = 'url(' + reader.result + ')';
                                            imagesContainer.appendChild(imageDiv);
                                        }
                                    })
                                }
                            })

                            function removeMultiUpload() {
                                imagesContainer.innerHTML = '';
                                imagesContainer.classList.remove("w-full", "grid", "grid-cols-1", "sm:grid-cols-2", "md:grid-cols-3", "lg:grid-cols-4", "gap-4");
                                multiUploadInput.value = '';
                                multiUploadDisplayText.innerHTML = '';
                                multiUploadDeleteButton.classList.add("hidden");
                                multiUploadDeleteButton.classList.remove("z-100", "p-2", "my-auto");
                            }
                        </script>

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