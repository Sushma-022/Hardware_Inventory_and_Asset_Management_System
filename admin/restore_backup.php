<?php
include "../auth/auth.php";
include "../db.php";

$file = "../backups/" . $_GET['file'];

$sql = file_get_contents($file);

/* 🔥 DISABLE FOREIGN KEY CHECKS */
$conn->query("SET FOREIGN_KEY_CHECKS=0");

$queries = explode(";\n", $sql);

foreach($queries as $query){
    $query = trim($query);
    if(!empty($query)){
        $conn->query($query);
    }
}

/* 🔥 ENABLE AGAIN */
$conn->query("SET FOREIGN_KEY_CHECKS=1");

header("Location: backup_restore.php");
?>