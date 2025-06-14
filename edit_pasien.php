<?php
session_start();
include 'koneksi.php';
$user_type = empty($_SESSION['usertype']) ? '' : $_SESSION['usertype'];

if (empty($_SESSION['login'])) {
    header("location:login.php?pesan=belum_login");
    exit();
}

if ($user_type != "admin" && $user_type != "radiografer") {
    header("location:javascript://history.go(-1)");
    exit();
}

$op = "";
$dpjp = "";
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
        // $sql = "SELECT * FROM pemeriksaan WHERE ID_PEMERIKSAAN = '$id'";
        $sql = "SELECT pemeriksaan.*, pasien.nama_pasien, pasien.alamat, pasien.tanggal_lahir, user.ID_USER, user.nama
        FROM pemeriksaan
        JOIN pasien ON pemeriksaan.ID_PASIEN = pasien.ID_PASIEN
        JOIN user ON pemeriksaan.ID_USER = user.ID_USER
        WHERE pemeriksaan.ID_PEMERIKSAAN = '$id'";

        $q = mysqli_query($connect, $sql);
        $db = mysqli_fetch_array($q);

        if (!$db) {
            $error = "Data tidak ditemukan";
        } else {
            $rekmed = $db['no_rekam_medis'];
            $tanggal = $db['tanggal_pemeriksaan'];
            $name = $db['nama_pasien']; //tabel pasien
            $tgl_lahir = $db['tanggal_lahir']; //tabel pasien
            $alamat = $db['alamat']; //tabel pasien
            $jenis_periksa = $db['jenis_pemeriksaan'];
            $image = $db['gambar_pemeriksaan'];
            $dpjp = $db['ID_USER']; //id user dokter penanggung jawab
        }
    }
}

if (isset($_POST['submit'])) {
    $name = $_POST['name']; //diambil dari form (jangan dari database)
    $dpjp = $_POST['dpjp']; //id user dokter penanggung jawab
    $rekmed = $_POST['rekmed'];
    $tanggal = date('d/m/Y', strtotime($_POST['tanggal']));
    $tgl_lahir = date('d/m/Y', strtotime($_POST['tgl_lahir']));
    $alamat = $_POST['alamat'];
    $jenis_periksa = $_POST['jenis_periksa'];
    $image = $_FILES['foto']['tmp_name']; //untuk upload foto
    $sekarang = date('d/m/Y');

    //untuk insert data ke database
    if ($name && $rekmed && $tanggal && $tgl_lahir && $alamat && $jenis_periksa && $dpjp) {
        if ($op == 'edit') {
            // hapus gambar lama
            // $sqlG = "SELECT gambar_pemeriksaan FROM pemeriksaan WHERE ID_PEMERIKSAAN = '$id'";
            // $qG = mysqli_query($connect, $sqlG);
            // $row = mysqli_fetch_assoc($qG);
            // $imagePath = isset($row['gambar_pemeriksaan']) ? explode(',', $row['gambar_pemeriksaan']) : '';
            // foreach ($imagePath as $path) {
            //     if (file_exists($path)) {
            //         unlink($path);
            //     }
            // }
            // untuk upload foto
            // if (isset($_FILES["foto"])) {
            //     for ($i = 0; $i < count($_FILES["foto"]["name"]); $i++) {
            //         $file_name = $_FILES["foto"]["name"][$i];
            //         $file_tmp = $_FILES["foto"]["tmp_name"][$i];
            //         $file_type = $_FILES["foto"]["type"][$i];
            //         $file_size = $_FILES["foto"]["size"][$i];
            //         $file_error = $_FILES["foto"]["error"][$i];

            //         if ($file_error === 0) {
            //             $file_destination = "uploads/" . $rekmed . "_" . uniqid() . "_" . $file_name;
            //             if (!is_dir("uploads")) {
            //                 mkdir("uploads");
            //             }
            //             move_uploaded_file($file_tmp, $file_destination);
            //             $image[] = $file_destination;
            //         } else {
            //             $error = "Gagal mengunggah file";
            //         }
            //     }
            // }
            // $image_path = implode(',', $image);

            if (!empty($_FILES['foto']['tmp_name'])) {
                $fotoContent = addslashes(file_get_contents($image));
            }

            // Update data di tabel pasien terlebih dahulu
            $sql_pasien = "UPDATE pasien 
                JOIN pemeriksaan ON pasien.ID_PASIEN = pemeriksaan.ID_PASIEN 
                SET pasien.nama_pasien = '$name', pasien.tanggal_lahir = '$tgl_lahir', pasien.alamat = '$alamat'
                WHERE pemeriksaan.ID_PEMERIKSAAN = '$id'";
            mysqli_query($connect, $sql_pasien);

            // Cek apakah ada gambar baru yang diupload
            if (!empty($_FILES['foto']['tmp_name'])) {
                // New image uploaded, update with new image
                $fotoContent = addslashes(file_get_contents($image));
                $sql = "UPDATE pemeriksaan 
                    SET ID_USER = '$dpjp', 
                        tanggal_pemeriksaan = '$tanggal', 
                        no_rekam_medis = '$rekmed',
                        jenis_pemeriksaan = '$jenis_periksa', 
                        gambar_pemeriksaan = '$fotoContent' 
                    WHERE ID_PEMERIKSAAN = '$id'";
            } else {
                // Jika tidak ada gambar baru, update tanpa mengubah gambar
                $sql = "UPDATE pemeriksaan 
                    SET ID_USER = '$dpjp', 
                        tanggal_pemeriksaan = '$tanggal', 
                        no_rekam_medis = '$rekmed',
                        jenis_pemeriksaan = '$jenis_periksa'
                    WHERE ID_PEMERIKSAAN = '$id'";
            }
            $query = mysqli_query($connect, $sql);
            // if ($image_path != "") {
            //     $sqli = "UPDATE data SET image = IF(image = '', '$image_path', CONCAT(image, ',', '$image_path')) WHERE ID_PEMERIKSAAN = '$id'";
            //     $queryi = mysqli_query($connect, $sqli);
            // }

//Tambahkan untuk update user

            if ($query) {
                header("location:pasien.php?op=edit_sukses");
            } else {
                echo "<script>alert('Data gagal diubah');</script>";
            }
        } else {
            // UNTUK NON EDIT
            $checkRekmedQuery = "SELECT * FROM pemeriksaan WHERE no_rekam_medis = '$rekmed'";
            $checkRekmedResult = mysqli_query($connect, $checkRekmedQuery);
            if (mysqli_num_rows($checkRekmedResult) > 0) {
                header("location:javascript://history.go(-1)");
            } else {
                // untuk upload foto
                // if (isset($_FILES["foto"])) {
                //     for ($i = 0; $i < count($_FILES["foto"]["name"]); $i++) {
                //         $file_name = $_FILES["foto"]["name"][$i];
                //         $file_tmp = $_FILES["foto"]["tmp_name"][$i];
                //         $file_type = $_FILES["foto"]["type"][$i];
                //         $file_size = $_FILES["foto"]["size"][$i];
                //         $file_error = $_FILES["foto"]["error"][$i];

                //         if ($file_error === 0) {
                //             $file_destination = "uploads/" . $rekmed . "_" . uniqid() . "_" . $file_name;
                //             if (!is_dir("uploads")) {
                //                 mkdir("uploads");
                //             }
                //             move_uploaded_file($file_tmp, $file_destination);
                //             $image[] = $file_destination;
                //         } else {
                //             $error = "Gagal mengunggah file";
                //         }
                //     }
                // }
                // $image_path = implode(',', $image);

                $fotoContent =  addslashes(file_get_contents($image));

                // insert data
                $sqlp = "INSERT INTO pasien (nama_pasien, tanggal_lahir, alamat) VALUES ('$name', '$tgl_lahir', '$alamat')";
                $queryp = mysqli_query($connect, $sqlp);

                //NANTI DIGANTI DENGAN PENANGGUNGJAWAB
                $sqlp_id = "SELECT ID_PASIEN FROM pasien WHERE nama_pasien = '$name' AND tanggal_lahir = '$tgl_lahir' AND alamat = '$alamat'";
                $queryp_id = mysqli_query($connect, $sqlp_id);
                $rowp_id = mysqli_fetch_assoc($queryp_id);
                $id_pasien = $rowp_id['ID_PASIEN'];

                $sql = "INSERT INTO pemeriksaan (ID_USER, ID_PASIEN, no_rekam_medis, tanggal_pemeriksaan, jenis_pemeriksaan, gambar_pemeriksaan, expertise) VALUES ('$dpjp','$id_pasien' , '$rekmed', '$tanggal', '$jenis_periksa', '$fotoContent', '')";
                $query = mysqli_query($connect, $sql);
                function encrypt($data)
                {
                    $encryptedData = base64_encode($data);
                    return $encryptedData;
                }
                $encryptedData = encrypt($tgl_lahir);

                // membuat akun pasien
                $sqln = "INSERT INTO user (username, password, nama, hak_akses, dibuat) VALUES ('$rekmed', '$encryptedData', '$name', 'pasien', '$sekarang')";
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
        <!-- Card -->
        <div class="mx-auto min-w-[32rem] w-full pt-8 p-10 bg-white border-0 shadow-lg rounded-xl">
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
                <div class="relative z-0 w-full mb-8">
                    <input type="number" name="rekmed" value="<?php echo $rekmed ?>" placeholder=" " min="0"
                        class="pt-3 pb-2 block w-full px-0 mt-0 bg-transparent border-0 border-b-2 appearance-none focus:outline-none focus:ring-0 focus:border-black border-gray-200"
                        <?php echo ($op == 'edit') ? 'readonly' : ''; ?> required>
                    <label for="rekmed" class="absolute duration-300 top-3 -z-1 origin-0 text-gray-500">
                        No. Rekam Medis</label>
                </div>

                <div class="relative z-0 w-full mb-8">
                    <label for="dpjp" name="dpjp"
                        class=" duration-300 top-3 -z-1 origin-0 text-gray-500">
                        Dokter Penanggungjawab</label>
                    <select name="dpjp" id="dpjp"
                        class="pt-3 pb-2 block w-full px-0 mt-0 bg-transparent border-0 border-b-2 appearance-none focus:outline-none focus:ring-0 focus:border-black border-gray-200"
                        <?php echo ($op == 'edit') ? 'disabled readonly' : ''; ?> required>
                        <?php
                        // Ambil daftar DPJP dari tabel user
                        $sql_dpjp = "SELECT ID_USER, nama FROM user WHERE hak_akses = 'dpjp'";
                        $result_dpjp = mysqli_query($connect, $sql_dpjp);
                        while ($row_dpjp = mysqli_fetch_assoc($result_dpjp)) {
                            $selected = ($dpjp == $row_dpjp['ID_USER']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($row_dpjp['ID_USER']) . '" ' . $selected . '>' . htmlspecialchars($row_dpjp['nama']) . '</option>';
                        }
                        ?>
                    </select>
                    <?php if ($op == 'edit'): ?>
                    <input type="hidden" name="dpjp" value="<?php echo htmlspecialchars($dpjp); ?>">
                    <?php endif; ?>
                </div>

                <div class="relative z-0 w-full mb-8">
                    <input type="date" name="tanggal" value="<?php if ($tanggal != null)
                        echo date('Y-m-d', strtotime(str_replace('/', '-', $tanggal))) ?>" placeholder=" "
                            class="pt-3 pb-2 block w-full px-0 mt-0 bg-transparent border-0 border-b-2 appearance-none focus:outline-none focus:ring-0 focus:border-black border-gray-200"
                            required>
                        <label for="tanggal" class="absolute duration-300 top-3 -z-1 origin-0 text-gray-500">
                            Tgl. Pemeriksaan</label>
                    </div>

                    <div class="relative z-0 w-full mb-8">
                        <input type="text" name="name" value="<?php echo $name ?>" placeholder=" "
                        class="pt-3 pb-2 block w-full px-0 mt-0 bg-transparent border-0 border-b-2 appearance-none focus:outline-none focus:ring-0 focus:border-black border-gray-200"
                        required>
                    <label for="name" class="absolute duration-300 top-3 -z-1 origin-0 text-gray-500">
                        Nama Pasien</label>
                </div>

                <div class="relative z-0 w-full mb-8">
                    <input type="date" name="tgl_lahir" value="<?php if ($tgl_lahir != null)
                        echo date('Y-m-d', strtotime(str_replace('/', '-', $tgl_lahir))) ?>" placeholder=" "
                            class="pt-3 pb-2 block w-full px-0 mt-0 bg-transparent border-0 border-b-2 appearance-none focus:outline-none focus:ring-0 focus:border-black border-gray-200"
                            required>
                        <label for="tgl_lahir" class="absolute duration-300 top-3 -z-1 origin-0 text-gray-500">
                            Tgl. Lahir</label>
                    </div>

                    <div class="relative z-0 w-full mb-5">
                        <input type="text" name="alamat" value="<?php echo $alamat ?>" placeholder=" "
                        class="pt-3 pb-2 block w-full px-0 mt-0 bg-transparent border-0 border-b-2 appearance-none focus:outline-none focus:ring-0 focus:border-black border-gray-200"
                        required>
                    <label for="alamat" class="absolute duration-300 top-3 -z-1 origin-0 text-gray-500">
                        Alamat</label>
                </div>

                <div class="relative z-0 w-full mb-8">
                    <label for="jenis_periksa" name="jenis_periksa"
                        class=" duration-300 top-3 -z-1 origin-0 text-gray-500">
                        Jenis Pemeriksaan</label>
                    <select name="jenis_periksa" id="jenis_periksa"
                        class="pt-3 pb-2 block w-full px-0 mt-0 bg-transparent border-0 border-b-2 appearance-none focus:outline-none focus:ring-0 focus:border-black border-gray-200"
                        <?php echo ($op == 'edit') ? 'disabled readonly' : ''; ?> required>
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
                            echo 'selected'; ?>>CLAVICULA
                        </option>
                        <option value="ABDOMEN/BNO" <?php if ($jenis_periksa == 'ABDOMEN/BNO')
                            echo 'selected'; ?>>ABDOMEN/BNO</option>
                        <option value="PELVIS AP" <?php if ($jenis_periksa == 'PELVIS AP')
                            echo 'selected'; ?>>PELVIS AP
                        </option>
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
                            echo 'selected'; ?>>SKY LINE
                        </option>
                        <option value="PATELLA" <?php if ($jenis_periksa == 'PATELLA')
                            echo 'selected'; ?>>PATELLA
                        </option>
                        <option value="CALCANEUS" <?php if ($jenis_periksa == 'CALCANEUS')
                            echo 'selected'; ?>>CALCANEUS
                        </option>
                        <option value="SCAPULA" <?php if ($jenis_periksa == 'SCAPULA')
                            echo 'selected'; ?>>SCAPULA
                        </option>
                        <option value="HUMERUS" <?php if ($jenis_periksa == 'HUMERUS')
                            echo 'selected'; ?>>HUMERUS
                        </option>
                        <option value="USG" <?php if ($jenis_periksa == 'USG')
                            echo 'selected'; ?>>USG</option>
                    </select>
                    <?php if ($op == 'edit'): ?>
                    <input type="hidden" name="jenis_periksa" value="<?php echo htmlspecialchars($jenis_periksa); ?>">
                    <?php endif; ?>
                </div>


                <!-- Upload -->
                <div class="w-full mt-8 mb-4 bg-white">
                    <div class="container h-full flex flex-col justify-center items-center">
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
                                    <svg xmlns="http://www.w3.org/2000/svg" class="fill-current text-red-700 w-3 h-3"
                                        viewBox="0 0 320 512">
                                        <path
                                            d="M310.6 361.4c12.5 12.5 12.5 32.75 0 45.25C304.4 412.9 296.2 416 288 416s-16.38-3.125-22.62-9.375L160 301.3L54.63 406.6C48.38 412.9 40.19 416 32 416S15.63 412.9 9.375 406.6c-12.5-12.5-12.5-32.75 0-45.25l105.4-105.4L9.375 150.6c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L160 210.8l105.4-105.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-105.4 105.4L310.6 361.4z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <input type="file" name="foto" id="multi-upload-input" class="hidden" accept="image/*" multiple <?php echo (empty($image) && $op != 'edit') ? 'required' : ''; ?>>
                    </div>
                </div>

                <script>
                    //all ids and some classes are importent for this script

                    multiUploadButton = document.getElementById("multi-upload-button");
                    multiUploadInput = document.getElementById("multi-upload-input");
                    imagesContainer = document.getElementById("images-container");
                    multiUploadDisplayText = document.getElementById("multi-upload-text");
                    multiUploadDeleteButton = document.getElementById("multi-upload-delete");

                    // Show existing image when editing
                    <?php if ($op == 'edit' && !empty($db['gambar_pemeriksaan'])): ?>
                        <?php $tampil_foto = base64_encode($db['gambar_pemeriksaan']); ?>
                        // Display existing image
                        imagesContainer.classList.add("w-full", "grid", "grid-cols-1", "sm:grid-cols-2", "md:grid-cols-3", "lg:grid-cols-4", "gap-4");
                        
                        let existingImageDiv = document.createElement('div');
                        existingImageDiv.classList.add("h-64", "mb-3", "w-full", "p-3", "rounded-lg", "bg-cover", "bg-center", "border", "border-gray-300", "relative");
                        existingImageDiv.style.backgroundImage = 'url(data:image/jpeg;base64,<?php echo $tampil_foto; ?>)';
                        
                        // Add label for existing image
                        let existingLabel = document.createElement('div');
                        existingLabel.classList.add("absolute", "top-1", "left-1", "bg-green-600", "text-white", "px-2", "py-1", "rounded", "text-xs");
                        existingLabel.innerHTML = 'Gambar saat ini';
                        existingImageDiv.appendChild(existingLabel);
                        
                        imagesContainer.appendChild(existingImageDiv);
                        
                        multiUploadDisplayText.innerHTML = 'Gambar saat ini tersimpan';
                        multiUploadDeleteButton.classList.add("z-100", "p-2", "my-auto");
                        multiUploadDeleteButton.classList.remove("hidden");
                    <?php else: ?>
                        // Show default text when not editing or no file selected
                        multiUploadDisplayText.innerHTML = 'MAX 4MB';
                    <?php endif; ?>

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
                        <?php if ($op == 'edit'): ?>
                            multiUploadDisplayText.innerHTML = 'Gambar saat ini tersimpan';
                        <?php else: ?>
                            multiUploadDisplayText.innerHTML = 'MAX 4MB';
                        <?php endif; ?>
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

</main>


</body>

</html>