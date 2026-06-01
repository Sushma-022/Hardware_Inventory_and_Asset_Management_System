<?php
include "../auth/auth.php";
include "../db.php";

$equipment_id = $_POST['equipment_id'];
$field_name = $_POST['field_name'];
$field_type = $_POST['field_type'];

$stmt = $conn->prepare("INSERT INTO equipment_fields (equipment_id, field_name, field_type) VALUES (?, ?, ?)");

$stmt->bind_param("iss", $equipment_id, $field_name, $field_type);

$stmt->execute();

header("Location: manage_fields.php?equipment_id=".$equipment_id);

?>