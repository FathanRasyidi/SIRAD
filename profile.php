<?php 
if ($_SESSION['usertype'] == 'pasien') {
    $profil_akses = "Pasien";
} elseif ($_SESSION['usertype'] == 'dpjp') {
    $profil_akses = "DPJP";
} elseif ($_SESSION['usertype'] == 'admin') {
    $profil_akses = "Admin";
} elseif ($_SESSION['usertype'] == 'radiografer') {
    $profil_akses = "Radiografer";
} elseif ($_SESSION['usertype'] == 'radiologi') {
    $profil_akses = "Dokter Radiologi";
}
?>

<div>
    <span class="block font-bold text-gray-900"><?= $_SESSION['login'] ?></span>
    <span class="block text-sm text-gray-500"><?= $profil_akses ?></span>
</div>