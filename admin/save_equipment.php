<?php
include "../auth/auth.php";
include "../db.php";

/* Prevent undefined index errors */
$type    = isset($_POST['type']) ? strtoupper($_POST['type']) : 'UNKNOWN';
$vendor  = $_POST['vendor'] ?? '';
$model   = $_POST['model'] ?? '';
$company = strtoupper($_POST['company'] ?? '');
$price   = $_POST['price'] ?? 0;
$quantity= intval($_POST['quantity'] ?? 1);
$purchase_date = $_POST['purchase_date'] ?? '';
$warranty      = $_POST['warranty'] ?? 0;
$expiry_date   = $_POST['expiry_date'] ?? '';

/* =========================
   SERIAL NUMBER GENERATOR
========================= */
function generateSerial($conn, $type, $company){
    $type    = strtoupper($type);
    $company = strtoupper($company);
    $prefix  = "VFSTR/TD/".$company."/".$type."/";

    switch($type){
        case "CPU": $table="cpu"; break;
        case "MONITOR": $table="monitor"; break;
        case "KEYBOARD": $table="keyboard"; break;
        case "MOUSE": $table="mouse"; break;
        case "COMBO": $table="combo_set"; break; // Add this line!
        default: $table="configurations"; break;
    }

    $sql = "SELECT serial_number FROM $table 
            WHERE serial_number LIKE '$prefix%' 
            ORDER BY CAST(SUBSTRING_INDEX(serial_number, '/', -1) AS UNSIGNED) DESC LIMIT 1";

    $result = $conn->query($sql);
    if($result && $result->num_rows > 0){
        $last = $result->fetch_assoc()['serial_number'];
        $num = intval(substr($last, strrpos($last,"/")+1)) + 1;
    } else {
        $num = 1;
    }
    return $prefix . str_pad($num, 3, "0", STR_PAD_LEFT);
}

/* =========================
   SAVE BILL
========================= */
$bill_name = "";
if(isset($_FILES['bill']) && $_FILES['bill']['error'] == 0){
    $upload_dir = "../uploads/";
    if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    
    // 1. Get the original name
    $original_name = basename($_FILES['bill']['name']);
    
    // 2. SANITIZE: Replace spaces and special characters with underscores!
    // This allows only letters, numbers, dots, and dashes.
    $clean_name = preg_replace("/[^a-zA-Z0-9.-]/", "_", $original_name);
    
    // 3. Add timestamp and save
    $bill_name = time() . "_" . $clean_name;
    move_uploaded_file($_FILES['bill']['tmp_name'], $upload_dir.$bill_name);
}

/* =========================
   SAVE TO DATABASE (LOOP FOR QUANTITY)
========================= */
switch($type){
case "CPU":
        for($i = 0; $i < $quantity; $i++){
            $serial_number = generateSerial($conn, $type, $company);
            
            // There are 15 question marks here
            $stmt = $conn->prepare("INSERT INTO cpu (serial_number, vendor, company, processor, generation, ram, storage, lan_mac, wifi_card, wifi_mac, price, quantity, purchase_date, warranty, expiry_date, bill) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?, ?)");
            
            // There are 15 type letters here: ssssssssssdsiss
            // s=string, d=double(price), i=integer(warranty)
            $stmt->bind_param("ssssssssssdsiss", 
                $serial_number,     // 1 s
                $vendor,            // 2 s
                $company,           // 3 s
                $_POST['processor'], // 4 s
                $_POST['generation'],// 5 s
                $_POST['ram'],       // 6 s
                $_POST['storage'],   // 7 s
                $_POST['lan_mac'],   // 8 s
                $_POST['wifi_card'], // 9 s
                $_POST['wifi_mac'],  // 10 s
                $price,             // 11 d (Double)
                $purchase_date,     // 12 s
                $warranty,          // 13 i (Integer)
                $expiry_date,       // 14 s
                $bill_name          // 15 s
            );
            
            $stmt->execute();
        }
        break;
    case "MONITOR":
        for($i = 0; $i < $quantity; $i++){
            $serial_number = generateSerial($conn, $type, $company);
            
            // 9 Question marks (?)
            $stmt = $conn->prepare("INSERT INTO monitor (serial_number, vendor, model, company, price, quantity, purchase_date, warranty, expiry_date, bill) VALUES (?, ?, ?, ?, ?, 1, ?, ?, ?, ?)");
            
            // 9 Type definitions: 4 strings, 1 double, 3 strings, 1 string
            // Corrected string: "ssssdssss"
            $stmt->bind_param("ssssdssss", 
                $serial_number, 
                $vendor, 
                $model, 
                $company, 
                $price, 
                $purchase_date, 
                $warranty, 
                $expiry_date, 
                $bill_name
            );

            $stmt->execute();
        }
        break;

    case "KEYBOARD":
        for($i = 0; $i < $quantity; $i++){
            // This generates a unique serial for each of the 2 keyboards
            $serial_number = generateSerial($conn, $type, $company);

            // 9 Question marks for the 9 columns we are filling
            $stmt = $conn->prepare("INSERT INTO keyboard 
                (serial_number, vendor, model, company, price, quantity, purchase_date, warranty, expiry_date, bill) 
                VALUES (?, ?, ?, ?, ?, 1, ?, ?, ?, ?)");

            // "ssssdssss" = 4 strings, 1 decimal(price), 4 strings
            $stmt->bind_param("ssssdssss", 
                $serial_number, 
                $vendor, 
                $model, 
                $company, 
                $price, 
                $purchase_date, 
                $warranty, 
                $expiry_date, 
                $bill_name
            );

            if (!$stmt->execute()) {
                die("Execute failed: " . $stmt->error);
            }
        }
        break; // CRITICAL: This stops the code from running into the next case

case "MOUSE":
        for($i = 0; $i < $quantity; $i++){
            $serial_number = generateSerial($conn, $type, $company);

            // 9 Question marks for 9 columns
            $stmt = $conn->prepare("INSERT INTO mouse 
                (serial_number, vendor, model, company, price, quantity, purchase_date, warranty, expiry_date, bill) 
                VALUES (?, ?, ?, ?, ?, 1, ?, ?, ?, ?)");

            // FIX: String changed to "ssssdssss" (9 characters for 9 variables)
            $stmt->bind_param("ssssdssss", 
                $serial_number, 
                $vendor, 
                $model, 
                $company, 
                $price, 
                $purchase_date, 
                $warranty, 
                $expiry_date, 
                $bill_name
            );

            $stmt->execute();
        }
        break;
case "COMBO":
        // 1. Grab exactly what is typed into your form
        $kb_model = $_POST['kb_model'] ?? 'N/A';
        $kb_company = $_POST['kb_company'] ?? '';
        $mouse_model = $_POST['mouse_model'] ?? 'N/A';
        $mouse_company = $_POST['mouse_company'] ?? '';

        // 2. Merge the two companies since the DB only has one 'company' column
        // Example: "DELL & LOGITECH"
        $merged_company = strtoupper($kb_company . " & " . $mouse_company);

        for($i = 0; $i < $quantity; $i++){
            // Generate a serial number using the merged company name
            $serial_number = generateSerial($conn, $type, $merged_company);
            
            // 3. Insert into the exact columns your database has
            $stmt = $conn->prepare("INSERT INTO combo_set 
                (serial_number, vendor, kb_serial, mouse_serial, price, quantity, purchase_date, warranty, expiry_date, bill, company) 
                VALUES (?, ?, ?, ?, ?, 1, ?, ?, ?, ?, ?)");
            
            // We map Form's 'Model' to DB's 'Serial' column
            $stmt->bind_param("ssssdsssss", 
                $serial_number, 
                $vendor, 
                $kb_model,      // Goes into kb_serial column
                $mouse_model,   // Goes into mouse_serial column
                $price, 
                $purchase_date, 
                $warranty, 
                $expiry_date, 
                $bill_name,
                $merged_company // Goes into the single company column
            );

            if(!$stmt->execute()){
                die("Execute failed: " . $stmt->error);
            }
        }
        break;
        
    default: // FOR CUSTOM EQUIPMENT IN CONFIGURATIONS
        for($i = 0; $i < $quantity; $i++){
            $serial_number = generateSerial($conn, $type, $company);
            $stmt = $conn->prepare("INSERT INTO configurations (serial_number, equipment_type, vendor, model, company, price, quantity, purchase_date, warranty, expiry_date, bill) VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?, ?, ?)");
            $stmt->bind_param("sssssdssiss", $serial_number, $type, $vendor, $model, $company, $price, $purchase_date, $warranty, $expiry_date, $bill_name);
            $stmt->execute();
        }
        break;
}

// Replace your old echo alert with this clear one:
echo "<script>alert('Successfully added $quantity $type(s)!'); window.location='view_equipment.php';</script>";?>