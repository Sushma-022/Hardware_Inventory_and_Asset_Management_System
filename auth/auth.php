<?php
session_start();

/* strict check */
if(empty($_SESSION['user'])){
    header("Location: /hardware/index.html");
    exit;
}

/* strong no-cache */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
?>