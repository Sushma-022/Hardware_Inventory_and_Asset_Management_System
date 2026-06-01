<?php
include "../auth/auth.php";
include "../db.php"; 

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=backup_'.date("Y-m-d_H-i-s").'.sql');

$tables = [];
$result = $conn->query("SHOW TABLES");

while($row = $result->fetch_row()){
    $tables[] = $row[0];
}

foreach($tables as $table){

    echo "DROP TABLE IF EXISTS `$table`;\n";

    $res = $conn->query("SHOW CREATE TABLE $table");
    $row = $res->fetch_row();

    echo $row[1].";\n\n";

    $res = $conn->query("SELECT * FROM $table");

    while($row = $res->fetch_assoc()){
        $values = array_map(function($val) use ($conn){
            return "'".$conn->real_escape_string($val)."'";
        }, array_values($row));

        echo "INSERT INTO `$table` VALUES(".implode(",", $values).");\n";
    }

    echo "\n\n";
}
exit;
?>