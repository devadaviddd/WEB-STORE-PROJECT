<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/System.php');

session_start();

$user_login;
if (isset($_SESSION['login_customer'])) {
    $user_login = $_SESSION['login_customer'];
    // header("location:/View/CustomerPage/AllProductPage.php");
}
if (isset($_SESSION['login_vendor'])) {
    $user_login = $_SESSION['login_vendor'];
    // header("location:/View/VendorPage/VendorPage.php");
}

if (isset($_SESSION['login_shipper'])) {
    $user_login = $_SESSION['login_shipper'];
    // header("location:/View/ShipperPage/ShipperPage.php");
}
$_SESSION['user_login'] = $user_login;
$path = $_SERVER["DOCUMENT_ROOT"];
if (!isset($user_login)) {
    header("location:/View/UserPage/LoginPage.php");
    die('should have redirected by now');
}
