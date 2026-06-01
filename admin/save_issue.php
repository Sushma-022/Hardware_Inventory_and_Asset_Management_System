<?php
include "../auth/auth.php";
include "../db.php";

/* 🔹 Get form data */
$employee_id   = $_POST['employee_id'];
$employee_name = $_POST['employee_name'];
$type          = strtoupper($_POST['equipment_type']);
$date          = $_POST['issue_date'];
$return_date = !empty($_POST['return_date']) ? $_POST['return_date'] : NULL;
$block = $_POST['block'] == "Other" ? $_POST['other_block'] : $_POST['block'];
$department = $_POST['department'];
$lab = $_POST['lab'];
$purpose = $_POST['purpose'];
$qty = (int)$_POST['quantity'];

/* 🔹 Decide table */
$old_tables = [
    'CPU'=>'cpu',
    'MONITOR'=>'monitor',
    'KEYBOARD'=>'keyboard',
    'MOUSE'=>'mouse',
    'COMBO'=>'combo_set'
];

if(isset($old_tables[$type])){
    $table = $old_tables[$type];
} else {
    $table = "configurations";
}
$selected_serials = $_POST['selected_serials'] ?? [];

/* CASE 1: Admin selected serials */
if(!empty($selected_serials)){

    foreach($selected_serials as $serial){

        /* update stock */
        $conn->query("
            UPDATE $table 
            SET quantity = quantity - 1 
            WHERE serial_number = '$serial'
        ");

        /* insert */
        $conn->query("
            INSERT INTO issued_equipment_employee
            (employee_id, employee_name, equipment_type, serial_number, issue_date, return_date, block, department, lab, purpose, quantity, status)
            VALUES
            ('$employee_id','$employee_name','$type','$serial','$date',".($return_date ? "'$return_date'" : "NULL").",'$block','$department','$lab','$purpose',1,'ISSUED')
        ");
    }

}
else{

    /* CASE 2: Auto assign (old logic) */

    $items = $conn->query("
        SELECT serial_number 
        FROM $table 
        WHERE quantity > 0
        AND serial_number NOT IN (
            SELECT serial_number 
            FROM issued_equipment_employee 
            WHERE status='ISSUED'
        )
        LIMIT $qty
    ");

    if($items->num_rows < $qty){
        echo "<script>alert('Not enough stock');window.location='issue_equipment_employee.php';</script>";
        exit();
    }

    while($item = $items->fetch_assoc()){

        $serial = $item['serial_number'];

        $conn->query("
            UPDATE $table 
            SET quantity = quantity - 1 
            WHERE serial_number = '$serial'
        ");

        $conn->query("
            INSERT INTO issued_equipment_employee
            (employee_id, employee_name, equipment_type, serial_number, issue_date, return_date, block, department, lab, purpose, quantity, status)
            VALUES
            ('$employee_id','$employee_name','$type','$serial','$date',".($return_date ? "'$return_date'" : "NULL").",'$block','$department','$lab','$purpose',1,'ISSUED')
        ");
    }
}

/* Redirect AFTER loop */
header("Location: view_issued.php");
exit();