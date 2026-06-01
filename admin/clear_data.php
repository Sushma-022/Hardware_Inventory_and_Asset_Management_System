<?php
require "../auth/auth.php";
require "../db.php";

/* 🔥 disable FK checks */
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

/* 🔥 ONLY DATA TABLES */
$tables = [
    "issued_equipment_employee", // child table FIRST
    "configurations",
    "cpu",
    "monitor",
    "keyboard",
    "mouse",
    "combo_set",
    "employees"
];

foreach ($tables as $table) {
    $conn->query("TRUNCATE TABLE $table");
}

/* 🔥 enable FK back */
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

/* redirect */
header("Location: backup_restore.php?cleared=1");
exit;
?>