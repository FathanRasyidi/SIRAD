<?php 
$hostname = "localhost";
$username = "root";
$password = "1122";
$database = "sr";

$connect = new mysqli($hostname, $username, $password, $database);
if ($connect->connect_error) {
    die ('Koneksi Gagal: ' . $connect->connect_error);
}
?>