<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}


$_SESSION = [];


session_destroy();


header("Location: home.php");
exit;
