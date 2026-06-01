<?php
include "../auth/auth.php";
include "../db.php";

/* Prevent undefined index errors */
$equipment_type = $_POST['equipment_type'] ?? 'equipment';
$vendor = $_POST['vendor'] ?? '';
$model = $_POST['model'] ?? '';
$company = $_POST['company'] ?? '';
$price = $_POST['price'] ?? 0;
$quantity = $_POST['quantity'] ?? 0;
$purchase_date = !empty($_POST['purchase_date']) ? $_POST['purchase_date'] : NULL;
$warranty = $_POST['warranty'] ?? 0;
$expiry_date = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : NULL;

/* =========================
   SERIAL NUMBER GENERATOR WITH COMPANY
========================= */
function generateSerial($conn, $type, $company){
    $type = strtoupper($type);
    $company = strtoupper($company);
    $prefix = "VFSTR/TD/".$company."/".$type."/";

    $sql = "SELECT serial_number 
            FROM configurations 
            WHERE serial_number LIKE '$prefix%' 
            ORDER BY CAST(SUBSTRING_INDEX(serial_number, '/', -1) AS UNSIGNED) DESC 
            LIMIT 1";

    $result = $conn->query($sql);

    if($result && $result->num_rows > 0){
        $last = $result->fetch_assoc()['serial_number'];
        $num = intval(substr($last, strrpos($last,"/")+1)) + 1;
    } else {
        $num = 1;
    }

    return $prefix . str_pad($num, 3, "0", STR_PAD_LEFT);
}

for($i = 0; $i < $quantity; $i++){

    $serial_number = generateSerial($conn, $equipment_type, $company);

    // small delay to avoid duplicate in same loop
    usleep(100000);

   // We added created_at to the columns, and NOW() to the values!
    $stmt = $conn->prepare("INSERT INTO configurations 
    (serial_number, equipment_type, vendor, model, company, price, quantity, purchase_date, warranty, expiry_date, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?, ?, NOW())");
    $stmt->bind_param(
        "sssssdsss",
        $serial_number,
        $equipment_type,
        $vendor,
        $model,
        $company,
        $price,
        $purchase_date,
        $warranty,
        $expiry_date
    );

    $stmt->execute();
    $config_id = $conn->insert_id;

    /* BILL UPLOAD FOR EACH ITEM */
    if(isset($_FILES['bill']) && $_FILES['bill']['error'] == 0){
        $upload_dir = "../uploads/";
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $safe_serial = str_replace("/", "_", $serial_number);
        
        // Sanitize the original file name to remove spaces!
        $original_name = basename($_FILES['bill']['name']);
        $clean_name = preg_replace("/[^a-zA-Z0-9.-]/", "_", $original_name);
        
        $bill_path = time() . "_" . $safe_serial . "_" . $clean_name;

        move_uploaded_file($_FILES['bill']['tmp_name'], $upload_dir.$bill_path);
        $conn->query("UPDATE configurations SET bill='$bill_path' WHERE id=$config_id");
    }

    /* SAVE CUSTOM FIELDS FOR EACH SERIAL */
    if(isset($_POST['field_name']) && isset($_POST['field_value'])){
        for($j=0; $j<count($_POST['field_name']); $j++){
            $fname = trim($_POST['field_name'][$j]);
            $fval  = trim($_POST['field_value'][$j]);

            if($fname != ""){
                $conn->query("
                INSERT INTO custom_fields(serial_number, field_name, field_value)
                VALUES('$serial_number', '$fname', '$fval')
                ");
            }
        }
    }
}

/* =========================
   SUCCESS
========================= */
echo "<script>
alert('Configuration saved successfully!');
window.location='view_equipment.php';
</script>";
?>