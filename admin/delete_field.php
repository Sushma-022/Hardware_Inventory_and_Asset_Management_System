<?php
include "../auth/auth.php";
include "../db.php";

$id = $_GET['id'];

$conn->query("DELETE FROM equipment_fields WHERE id=$id");

header("Location: ".$_SERVER['HTTP_REFERER']);

?>