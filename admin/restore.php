<?php
include "../auth/auth.php";
include "../db.php";

if(isset($_POST['restore'])){

    if($_FILES['backup_file']['error'] != 0){
        die("Upload failed");
    }

    $ext = pathinfo($_FILES['backup_file']['name'], PATHINFO_EXTENSION);

    if($ext != "sql"){
        die("Only .sql file allowed");
    }

    $sql = file_get_contents($_FILES['backup_file']['tmp_name']);

    $queries = explode(";\n", $sql);

    foreach($queries as $query){
        $query = trim($query);
        if(!empty($query)){
            $conn->query($query);
        }
    }

    echo "<script>
    alert('Database Restored Successfully');
    window.location='backup_restore.php';
    </script>";
}
?>