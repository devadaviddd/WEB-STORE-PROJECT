<?php
session_start();

if (session_destroy()) {
    header("Location:/View/UserPage/LoginPage.php");
}
