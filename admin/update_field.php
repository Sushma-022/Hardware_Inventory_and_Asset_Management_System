<?php
include "../auth/auth.php";
include "../db.php";

$id = intval($_POST['id']);
$equipment_id = intval($_POST['equipment_id']);

$field_name = $_POST['field_name'];
$field_type = $_POST['field_type'];
$field_options = $_POST['field_options'];

$is_required = isset($_POST['required']) ? 1 : 0;

$stmt = $conn->prepare(
"UPDATE equipment_fields
SET field_name=?,field_type=?,field_options=?,is_required=?
WHERE id=?"
);

$stmt->bind_param(
"sssii",
$field_name,
$field_type,
$field_options,
$is_required,
$id
);

$stmt->execute();

header("Location: manage_fields.php?equipment_id=".$equipment_id);

?>