<?php
session_start();
$_SESSION["role"] = NULL;
$_SESSION["id"] = NULL;
$_SESSION["user"] = NULL;
header("Location: ./login.php");
die();
?>
