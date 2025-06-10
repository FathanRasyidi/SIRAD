<?php 
if (empty($_COOKIE['user']) && !isset($_SESSION['login'])) {
    header("location:login.php");
    exit();
} else {
 header("location:dashboard.php");}
 ?>