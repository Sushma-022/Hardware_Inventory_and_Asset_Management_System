<?php
include "../auth/auth.php";
include "../db.php";
date_default_timezone_set("Asia/Kolkata");
$backup_dir = "../backups/";
$filename = "backup_" . date("Y-m-d_H-i-s") . ".sql";
$filepath = $backup_dir . $filename;

$tables = [];
$result = $conn->query("SHOW TABLES");

while($row = $result->fetch_row()){
    $tables[] = $row[0];
}

$file = fopen($filepath, 'w');

foreach($tables as $table){

    $res = $conn->query("SHOW CREATE TABLE $table");
    $row = $res->fetch_row();

    fwrite($file, "DROP TABLE IF EXISTS `$table`;\n");
    fwrite($file, $row[1] . ";\n\n");

    $res = $conn->query("SELECT * FROM $table");

    while($row = $res->fetch_assoc()){
        $values = array_map(function($val) use ($conn){
            return "'".$conn->real_escape_string($val)."'";
        }, array_values($row));

        fwrite($file, "INSERT INTO `$table` VALUES(".implode(",", $values).");\n");
    }

    fwrite($file, "\n\n");
}

fclose($file);

header("Location: backup_restore.php?success=1");
?>