<?php
include "../auth/auth.php";
include "../db.php";

echo "<div style='font-family: sans-serif; padding: 20px;'>";
echo "<h2>🛠️ Auto-Fixing Broken Bills...</h2>";

$upload_dir = "../uploads/";
// Check all equipment tables
$tables = ['cpu', 'monitor', 'keyboard', 'mouse', 'combo_set', 'configurations'];

$fixed_count = 0;
$missing_count = 0;
$perfect_count = 0;

foreach ($tables as $table) {
    $result = $conn->query("SELECT serial_number, bill FROM $table WHERE bill IS NOT NULL AND bill != ''");
    
    while ($row = $result->fetch_assoc()) {
        $serial = $row['serial_number'];
        $old_bill = $row['bill'];
        
        // This removes spaces and special characters, turning them into underscores
        $clean_bill = preg_replace("/[^a-zA-Z0-9.-]/", "_", $old_bill);
        
        if ($old_bill !== $clean_bill) {
            $old_path = $upload_dir . $old_bill;
            $clean_path = $upload_dir . $clean_bill;
            
            $ready_to_update = false;

            // Scenario 1: The messy file exists in the folder. Let's rename it nicely!
            if (file_exists($old_path)) {
                rename($old_path, $clean_path);
                $ready_to_update = true;
            } 
            // Scenario 2: The file is already nicely named in the folder, but the database has the messy name.
            elseif (file_exists($clean_path)) {
                $ready_to_update = true;
            } 
            // Scenario 3: The file is completely missing from the uploads folder.
            else {
                echo "<p style='color:red;'>❌ <strong>Missing:</strong> Could not find physical file for $serial ($old_bill)</p>";
                $missing_count++;
            }

            // Update the database to the clean name
            if ($ready_to_update) {
                $stmt = $conn->prepare("UPDATE $table SET bill = ? WHERE serial_number = ?");
                $stmt->bind_param("ss", $clean_bill, $serial);
                $stmt->execute();
                echo "<p style='color:green;'>✅ <strong>Fixed:</strong> $serial is now linked to <em>$clean_bill</em></p>";
                $fixed_count++;
            }
        } else {
            $perfect_count++;
        }
    }
}

echo "<hr>";
echo "<h3>🎉 Script Finished!</h3>";
echo "<p><strong>$fixed_count</strong> broken bills were successfully fixed.</p>";
echo "<p><strong>$perfect_count</strong> bills were already perfect.</p>";
if($missing_count > 0) {
    echo "<p style='color:red;'><strong>$missing_count</strong> files are missing from the 'uploads' folder entirely. You will have to re-upload those manually.</p>";
}
echo "<br><br><a href='view_equipment.php' style='background: #0d6efd; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Back to View Equipment</a>";
echo "</div>";
?>