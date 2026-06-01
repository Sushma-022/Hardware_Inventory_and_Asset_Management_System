<?php
include "../auth/auth.php";
include "../db.php";

if(isset($_POST['serial_range']) && !empty($_POST['serial_range'])){
    
    // Clean up the input
    $input = trim($_POST['serial_range']);
    
    // Split the string at the hyphen "-"
    // e.g., parts[0] = "VFSTR/TD/HP/CPU/001 " | parts[1] = " 015"
    $parts = explode("-", $input);
    
    if(count($parts) == 2){
        $startPart = trim($parts[0]); // "VFSTR/TD/HP/CPU/001"
        $endStr = trim($parts[1]);    // "015"
        
        // Find the position of the last slash '/'
        $lastSlashPos = strrpos($startPart, '/');
        
        if($lastSlashPos !== false){
            // Extract the prefix and the starting number
            $prefix = substr($startPart, 0, $lastSlashPos + 1); // "VFSTR/TD/HP/CPU/"
            $startStr = substr($startPart, $lastSlashPos + 1);  // "001"
            
            $startNum = (int)$startStr; // converts "001" to 1
            $endNum = (int)$endStr;     // converts "015" to 15
            
            // Figure out how many zeros to pad (e.g., length of "001" is 3)
            $padLength = strlen($startStr);
            
            // List ALL your equipment tables here
            $tables = ['configurations', 'cpu', 'monitor', 'keyboard', 'mouse', 'combo_set'];
            
            // Loop from the start number to the end number
            for($i = $startNum; $i <= $endNum; $i++){
                
                // Reconstruct the exact serial number (e.g., VFSTR/TD/HP/CPU/005)
                // str_pad ensures 5 becomes "005"
                $currentSerial = $prefix . str_pad($i, $padLength, "0", STR_PAD_LEFT);
                
                // Because we don't know the exact type, try deleting it from every table
                foreach($tables as $table){
                    $stmt = $conn->prepare("DELETE FROM $table WHERE serial_number=?");
                    $stmt->bind_param("s", $currentSerial);
                    $stmt->execute();
                }
            }
        }
    }
}

// Send the admin back to the view page when done
header("Location: view_equipment.php");
exit();
?>