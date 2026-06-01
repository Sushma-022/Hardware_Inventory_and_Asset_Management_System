<?php
include "../auth/auth.php";
include "../db.php";

$id = $_GET['id'];
$staff_id = $_GET['staff_id'] ?? 'Unknown';

/* get equipment details */
$res = $conn->query("SELECT * FROM issued_equipment_employee WHERE id=$id");
$row = $res->fetch_assoc();

$type = $row['equipment_type'];
$serial = $row['serial_number'];

/* decide table */
$old_tables = [
    'CPU'=>'cpu',
    'MONITOR'=>'monitor',
    'KEYBOARD'=>'keyboard',
    'MOUSE'=>'mouse',
    'COMBO'=>'combo_set'
];

$table = $old_tables[$type] ?? "configurations";

/* increase stock */
$conn->query("
UPDATE $table 
SET quantity = quantity + 1 
WHERE serial_number = '$serial'
");

/* update issued table */
$conn->query("
UPDATE issued_equipment_employee 
SET status='RETURNED',
    returned_to='$staff_id',
    return_time=NOW()
WHERE id=$id
");

header("Location: view_issued.php");
exit();
?>