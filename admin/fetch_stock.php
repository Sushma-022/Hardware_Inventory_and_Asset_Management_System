<?php
include "../auth/auth.php";
include "../db.php";

$type = strtoupper($_GET['type']);
$serials = [];

/* old equipment tables */
$old_tables = [
    'CPU' => 'cpu',
    'MONITOR' => 'monitor',
    'KEYBOARD' => 'keyboard',
    'MOUSE' => 'mouse',
    'COMBO' => 'combo_set'
];

if(isset($old_tables[$type])){
    $table = $old_tables[$type];

    $query = $conn->query("
        SELECT serial_number 
        FROM $table
        WHERE quantity > 0
        AND serial_number NOT IN (
            SELECT serial_number 
            FROM issued_equipment_employee
            WHERE status='ISSUED'
        )
    ");
} else {
    $query = $conn->query("
        SELECT serial_number
        FROM configurations
        WHERE UPPER(equipment_type)=UPPER('$type')
        AND quantity > 0
        AND serial_number NOT IN (
            SELECT serial_number 
            FROM issued_equipment_employee
            WHERE status='ISSUED'
        )
    ");
}

while($row = $query->fetch_assoc()){
    $serials[] = $row['serial_number'];
}

echo json_encode([
    "count" => count($serials),
    "serials" => $serials
]);
?>