<?php
include "../auth/auth.php";
include "../db.php";

// Turn on error reporting so we can see if SQL fails
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if(isset($_POST['items']) && !empty($_POST['items'])){
    foreach($_POST['items'] as $item){

        // Split the Type and Serial
        $parts = explode("|", $item);
        if(count($parts) < 2) continue;

        $type = trim($parts[0]);
        $serial = trim($parts[1]);

        // Convert to uppercase for comparison (since your SQL Union uses caps)
        $compareType = strtoupper($type);
        
        // --- DYNAMIC TABLE NAME MAPPING ---
        // We need to map the Label (e.g., 'KEYBOARD') to the actual DB Table Name (e.g., 'keyboard')
        $tableName = "";

        if($compareType == "CPU") {
            $tableName = "cpu";
        } elseif ($compareType == "MONITOR") {
            $tableName = "monitor";
        } elseif ($compareType == "KEYBOARD") {
            $tableName = "keyboard";
        } elseif ($compareType == "MOUSE") {
            $tableName = "mouse";
        } elseif ($compareType == "COMBO") {
            $tableName = "combo_set";
        } else {
            // Default for anything else (like Laptop, UPS, etc. stored in configurations)
            $tableName = "configurations";
        }

        try {
            // 1. Delete from the specific equipment table
            $stmt = $conn->prepare("DELETE FROM $tableName WHERE serial_number = ?");
            $stmt->bind_param("s", $serial);
            $stmt->execute();

            // 2. Delete from issued table (so the serial is freed up)
            $stmt2 = $conn->prepare("DELETE FROM issued_equipment_employee WHERE serial_number = ?");
            $stmt2->bind_param("s", $serial);
            $stmt2->execute();
            
        } catch (Exception $e) {
            // If there is an error, stop and show it
            die("Error deleting serial $serial from $tableName: " . $e->getMessage());
        }
    }
    // Success! Go back
    header("Location: view_equipment.php?msg=deleted");
    exit();
} else {
    // Nothing was selected
    header("Location: view_equipment.php?msg=nothing_selected");
    exit();
}
?>