<?php
session_start();
if (isset($_SESSION['login_customer']) || isset($_SESSION['login_vendor']) ||  isset($_SESSION['login_shipper'])) {
    session_destroy();
}

include($_SERVER['DOCUMENT_ROOT'] . '/View/UserPage/session.php');
