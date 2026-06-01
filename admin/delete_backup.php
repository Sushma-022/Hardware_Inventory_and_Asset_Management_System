<?php
include "../auth/auth.php";
$file = "../backups/" . $_GET['file'];

if(file_exists($file)){
    unlink($file);
}

header("Location: backup_restore.php");
?>