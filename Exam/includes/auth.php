<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

if (empty($_SESSION['user_id'])){
    header('Location:login.php');
    exit();
}

$userEmail = $_SESSION['email'] ?? '';