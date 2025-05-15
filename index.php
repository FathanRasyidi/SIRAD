<?php
session_start();
include 'koneksi.php';
$user_type = empty($_SESSION['usertype']) ? '' : $_SESSION['usertype'];

if (isset($_COOKIE['user']) && !isset($_SESSION['login'])) {
  $usn = base64_decode($_COOKIE['user']);
  $sqlu = "SELECT * FROM user WHERE username = '$usn'";
  $qu = mysqli_query($connect, $sqlu);
  $dbu = mysqli_fetch_array($qu);
  $_SESSION['login'] = $dbu['nama'];
  header("location:index.php");
  exit();
}

if (empty($_COOKIE['user']) && !isset($_SESSION['login'])) {
  header("location:login.php?pesan=belum_login");
  exit();
}

// $sql = "SELECT * FROM data";
// $q = mysqli_query($connect, $sql);
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

<body>
  <main style="display: flex;">
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>
    <!-- Isian -->
    <div class="flex-1 p-6 pt-0 ml-64">
      <!-- Header -->
      <div class="p-6 py-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <a class="navbar-brand flex items-center my-2">
          <img src="img/suisei.png" alt="Profile" width="50" height="50" class="rounded-full border-2" id="logo"
            style="margin-right: 10px; border-color: #16a34a;">
          <div>
            <span class="block font-bold text-gray-900"><?= $_SESSION['login'] ?></span>
            <span class="block text-sm text-gray-500"><?= $_SESSION['usertype'] ?></span>
          </div>
        </a>
      </div>
      <!-- Content -->
      <div style="width: 100%;">
        <div class="flex flex-row h-max mr-2 w-full">
          <!-- Card Kiri -->
          <div class="bg-white border rounded-xl shadow-lg p-6 pt-4 w-3/12 min-w-[20rem]">
            <p class="text-xl font-bold mb-4">Activity Overview</p>
            <div class="grid grid-cols-2 gap-4">
              <!-- Pemeriksaan -->
              <div class="flex flex-col items-center justify-center bg-yellow-100 rounded-lg p-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mb-2 ml-2" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15 19C15 16.7909 12.3137 15 9 15C5.68629 15 3 16.7909 3 19M21 10L17 14L15 12M9 12C6.79086 12 5 10.2091 5 8C5 5.79086 6.79086 4 9 4C11.2091 4 13 5.79086 13 8C13 10.2091 11.2091 12 9 12Z" />
                </svg>
                <p class="text-2xl font-bold text-gray-800">48</p>
                <p class="text-sm text-gray-600">Pemeriksaan</p>
              </div>
              <!-- Pasien Dirujuk -->
              <div class="flex flex-col items-center justify-center bg-green-100 rounded-lg p-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                  stroke="currentColor" class="w-9 h-9 mb-2 group-hover:text-indigo-400">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                <p class="text-2xl font-bold text-gray-800">50</p>
                <p class="text-sm text-gray-600">Pasien Dirujuk</p>
              </div>
              <!-- Laporan Expertise -->
              <div class="col-span-2 flex flex-col items-center justify-center bg-blue-100 rounded-lg p-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                  stroke="currentColor" class="w-8 h-8 mb-2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                <p class="text-2xl font-bold text-gray-800">47</p>
                <p class="text-sm text-gray-600">Laporan Expertise</p>
              </div>
            </div>
          </div>
          <!-- Card Kanan -->
          <div class="bg-white border rounded-xl shadow-lg mx-6 mr-0 px-6 py-4 w-9/12 min-w-[24rem]">
            <p class="text-xl"><strong>Informasi</strong></p>
            <div class="flex bg-blue-100 rounded-lg p-4 my-4 text-sm text-blue-700" role="alert">
              <svg class="w-5 h-5 inline mr-3" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                  clip-rule="evenodd"></path>
              </svg>
              <div>
                <span class="font-medium"><u>Jadwal Pelayanan</u></span><br><span class="font-medium">SENIN -
                  KAMIS</span>
                : 07.30 WIB - 14.30 WIB <br>
                <span class="font-medium">JUM'AT</span> : 07.30 WIB - 11.30 WIB <br>
                <span class="font-medium">SABTU</span> : 07.30 WIB - 13.00 WIB
              </div>
            </div>
            <div class="flex bg-orange-100 rounded-lg p-4 my-4 text-sm text-orange-700" role="alert">
              <svg class="w-5 h-5 inline mr-3" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                  clip-rule="evenodd"></path>
              </svg>
              <div>
                <span class="font-medium"><u>Jadwal Pendaftaran Rawat Jalan</u></span><br><span
                  class="font-medium">SENIN
                  - KAMIS</span> : 07.30 WIB - 11.30 WIB <br>
                <span class="font-medium">JUM'AT</span> : 07.30 WIB - 09.30 WIB <br>
                <span class="font-medium">SABTU</span> : 07.30 WIB - 10.30 WIB
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

</body>

</html>