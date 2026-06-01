<?php
include "../auth/auth.php";
include "../db.php";

$id = trim($_GET['id']);

$result = $conn->query("SELECT name FROM issue_employee WHERE employee_id='$id'");

if($result->num_rows > 0){
    $row = $result->fetch_assoc();
    echo json_encode(["name" => $row['name']]);
} else {
    echo json_encode([]);
}
?>