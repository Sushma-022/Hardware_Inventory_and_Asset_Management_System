<?php
include "../auth/auth.php";
include "../db.php";

// =========================================================================
// SCENARIO 1: Deleting an Equipment TYPE (from manage_equipment.php)
// Uses ?delete_name=... instead of ID to prevent wiping the table!
// =========================================================================
if (isset($_GET['delete_name'])) {
    $name = strtoupper(trim($_GET['delete_name']));
    
    // Deletes only the specific name you clicked
    $stmt = $conn->prepare("DELETE FROM equipment_types WHERE UPPER(name) = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->close();

    // Instantly redirect back to the manage page
    header("Location: manage_equipment.php");
    exit(); 
}

// =========================================================================
// SCENARIO 2: Deleting an actual EQUIPMENT ITEM (from view_equipment.php)
// Uses ?type=... & ?serial=...
// =========================================================================
if (isset($_GET['type']) && isset($_GET['serial'])) {
    $type = strtoupper(trim($_GET['type']));
    $serial = rawurldecode(trim($_GET['serial'])); 

    $tableName = "configurations";
    if($type == "CPU") { $tableName = "cpu"; }
    elseif ($type == "MONITOR") { $tableName = "monitor"; }
    elseif ($type == "KEYBOARD") { $tableName = "keyboard"; }
    elseif ($type == "MOUSE") { $tableName = "mouse"; }
    elseif ($type == "COMBO") { $tableName = "combo_set"; }

    // 1. Delete from the mapped table
    $stmt = $conn->prepare("DELETE FROM $tableName WHERE serial_number = ?");
    $stmt->bind_param("s", $serial);
    $stmt->execute();

    // 2. Also try deleting from configurations 
    if($tableName != "configurations") {
        $stmt3 = $conn->prepare("DELETE FROM configurations WHERE serial_number = ?");
        $stmt3->bind_param("s", $serial);
        $stmt3->execute();
    }

    // 3. Clear issued records
    $stmt2 = $conn->prepare("DELETE FROM issued_equipment_employee WHERE serial_number = ?");
    $stmt2->bind_param("s", $serial);
    $stmt2->execute();

    // Instantly redirect back to the view equipment page
    header("Location: view_equipment.php");
    exit();
}

// =========================================================================
// FALLBACK: If accessed directly by mistake, send to dashboard
// =========================================================================
header("Location: admin_dashboard.php");
exit();
?>