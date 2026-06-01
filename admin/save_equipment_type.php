<?php
include "../auth/auth.php";
include "../db.php";

if(isset($_POST['equipment_name'])){

$name = $_POST['equipment_name'];
$icon = $_POST['icon'];

$stmt = $conn->prepare("INSERT INTO equipment_types (name, icon) VALUES (?, ?)");
$stmt->bind_param("ss", $name, $icon);
$stmt->execute();

header("Location: manage_equipment.php");
exit();

}
?>