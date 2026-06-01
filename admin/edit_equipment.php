<?php
include "../auth/auth.php";
include "../db.php";

$serial = $_GET['serial'] ?? '';
$type = $_GET['type'] ?? '';

if($serial == '' || $type == ''){
    die("Serial number or Type missing");
}

$type_lower = strtolower($type);
$type_upper = strtoupper($type);

// --- SMART TABLE FINDER ---
$tables_to_check = [];
if($type_lower == "cpu") $tables_to_check = ['cpu', 'configurations'];
elseif($type_lower == "monitor") $tables_to_check = ['monitor', 'configurations'];
elseif($type_lower == "keyboard") $tables_to_check = ['keyboard', 'configurations'];
elseif($type_lower == "mouse") $tables_to_check = ['mouse', 'configurations'];
elseif($type_lower == "combo") $tables_to_check = ['combo_set', 'configurations'];
else $tables_to_check = ['configurations'];

$row = null;
$table = "";
foreach($tables_to_check as $t) {
    $stmt = $conn->prepare("SELECT * FROM $t WHERE serial_number = ?");
    $stmt->bind_param("s", $serial);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $table = $t;
        break;
    }
}

if(!$row) { die("Equipment not found."); }

if(isset($_POST['update'])){
    $new_company = strtoupper($_POST['company']);
    $vendor = $_POST['vendor'];
    $price = $_POST['price'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;
    $purchase_date = $_POST['purchase_date'];
    $warranty = $_POST['warranty']; 
    $expiry_date = $_POST['expiry_date'];
    
    // Default to the existing bill
    $bill = $_POST['existing_bill'] ?? ''; 

    // --- UPLOAD NEW BILL (If they selected one) ---
    if(isset($_FILES['new_bill']) && $_FILES['new_bill']['error'] == 0){
        $upload_dir = "../uploads/";
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $original_name = basename($_FILES['new_bill']['name']);
        // Clean the name so no spaces break the link again!
        $clean_name = preg_replace("/[^a-zA-Z0-9.-]/", "_", $original_name);
        $bill = time() . "_" . $clean_name;
        
        move_uploaded_file($_FILES['new_bill']['tmp_name'], $upload_dir.$bill);
    }

    // --- 1. SMART SERIAL LOGIC ---
    $final_serial = $serial; // Default: keep existing serial

    if($new_company !== strtoupper($row['company'])){
        $prefix = "VFSTR/TD/" . $new_company . "/" . $type_upper . "/";
        
        // Find latest number for NEW company and TYPE
        $check_sql = "SELECT serial_number FROM $table 
                      WHERE serial_number LIKE '$prefix%' 
                      ORDER BY CAST(SUBSTRING_INDEX(serial_number, '/', -1) AS UNSIGNED) DESC 
                      LIMIT 1";
        
        $check = $conn->query($check_sql);
        
        if($check && $check->num_rows > 0) {
            $last_serial = $check->fetch_assoc()['serial_number'];
            $parts = explode('/', $last_serial);
            $new_num = intval(end($parts)) + 1;
        } else {
            $new_num = 1;
        }
        
        $final_serial = $prefix . str_pad($new_num, 3, "0", STR_PAD_LEFT);
    }
    
    // --- 2. UPDATE QUERIES (Now including the bill field) ---
    if($table == "cpu"){
        $sql = "UPDATE cpu SET serial_number=?, company=?, vendor=?, processor=?, ram=?, storage=?, generation=?, price=?, quantity=?, purchase_date=?, warranty=?, expiry_date=?, bill=? WHERE serial_number=?";
        $up = $conn->prepare($sql);
        $up->bind_param("sssssssdssisss", $final_serial, $new_company, $vendor, $_POST['processor'], $_POST['ram'], $_POST['storage'], $_POST['generation'], $price, $quantity, $purchase_date, $warranty, $expiry_date, $bill, $serial);
    } elseif($table == "combo_set") {
        $kb_model = $_POST['kb_model'] ?? '';
        $mouse_model = $_POST['mouse_model'] ?? '';
        
        $sql = "UPDATE combo_set SET serial_number=?, company=?, vendor=?, kb_serial=?, mouse_serial=?, price=?, quantity=?, purchase_date=?, warranty=?, expiry_date=?, bill=? WHERE serial_number=?";
        $up = $conn->prepare($sql);
        $up->bind_param("sssssdssisss", $final_serial, $new_company, $vendor, $kb_model, $mouse_model, $price, $quantity, $purchase_date, $warranty, $expiry_date, $bill, $serial);
    } else {
        $sql = "UPDATE $table SET serial_number=?, company=?, vendor=?, model=?, price=?, quantity=?, purchase_date=?, warranty=?, expiry_date=?, bill=? WHERE serial_number=?";
        $up = $conn->prepare($sql);
        $up->bind_param("ssssdssisss", $final_serial, $new_company, $vendor, $_POST['model'], $price, $quantity, $purchase_date, $warranty, $expiry_date, $bill, $serial);
    }

    if($up->execute()){
        // --- 3. SYNC THE ISSUED TABLE ---
        if($final_serial !== $serial) {
            $sql_sync = "UPDATE issued_equipment_employee 
                         SET serial_number = '$final_serial' 
                         WHERE serial_number = '$serial'";
            
            if(!$conn->query($sql_sync)) {
                echo "<script>alert('Equipment updated, but failed to sync Issued list: " . $conn->error . "');</script>";
            }
        }
        
        echo "<script>alert('Update Successful!'); window.parent.location.reload();</script>";
    } else {
        echo "<script>alert('Update Failed: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; padding: 20px; }
        .modern-card { background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 550px; margin: 0 auto; overflow: hidden; }
        .modern-header { background: #1B5E20; color: white; padding: 15px; margin: 0; font-size: 18px; }
        .form-container { padding: 20px; padding-bottom: 80px; }
        .form-group { margin-bottom: 15px; display: flex; flex-direction: column; }
        label { font-size: 11px; font-weight: bold; color: #777; margin-bottom: 5px; }
        input { padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .footer-bar { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; padding: 15px; border-top: 1px solid #ddd; text-align: center; }
        .btn-save { background: #2563eb; color: white; padding: 12px 60px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>

<div class="modern-card">
    <h2 class="modern-header">Edit <?= $type_upper ?></h2>
    
    <form method="POST" class="form-container" enctype="multipart/form-data">
        <div class="form-group"><label>Company</label><input type="text" name="company" value="<?= htmlspecialchars($row['company']) ?>" required></div>
        <div class="form-group"><label>Vendor</label><input type="text" name="vendor" value="<?= htmlspecialchars($row['vendor']) ?>" required></div>

      <?php if($table == "cpu"): ?>
            <div class="form-group"><label>Processor</label><input type="text" name="processor" value="<?= htmlspecialchars($row['processor'] ?? '') ?>"></div>
            <div class="form-group"><label>RAM</label><input type="text" name="ram" value="<?= htmlspecialchars($row['ram'] ?? '') ?>"></div>
            <div class="form-group"><label>Storage</label><input type="text" name="storage" value="<?= htmlspecialchars($row['storage'] ?? '') ?>"></div>
            <div class="form-group"><label>Generation</label><input type="text" name="generation" value="<?= htmlspecialchars($row['generation'] ?? '') ?>"></div>
        <?php elseif($table == "combo_set"): ?>
            <div class="form-group"><label>Keyboard Model</label><input type="text" name="kb_model" value="<?= htmlspecialchars($row['kb_serial'] ?? '') ?>"></div>
            <div class="form-group"><label>Mouse Model</label><input type="text" name="mouse_model" value="<?= htmlspecialchars($row['mouse_serial'] ?? '') ?>"></div>
        <?php else: ?>
            <div class="form-group"><label>Model</label><input type="text" name="model" value="<?= htmlspecialchars($row['model'] ?? '') ?>"></div>
        <?php endif; ?>

        <div class="form-group"><label>Quantity</label><input type="number" name="quantity" value="<?= htmlspecialchars($row['quantity']) ?>"></div>
        <div class="form-group"><label>Price</label><input type="number" step="0.01" name="price" value="<?= htmlspecialchars($row['price']) ?>"></div>

        <div class="form-group"><label>Purchase Date</label><input type="date" name="purchase_date" id="p_date" value="<?= htmlspecialchars($row['purchase_date']) ?>" onchange="calculateExpiry()"></div>
        <div class="form-group"><label>Warranty (Months)</label><input type="number" name="warranty" id="warranty" value="<?= htmlspecialchars($row['warranty']) ?>" oninput="calculateExpiry()"></div>
        <div class="form-group"><label>Expiry Date</label><input type="date" name="expiry_date" id="e_date" value="<?= htmlspecialchars($row['expiry_date']) ?>"></div>

        <div class="form-group">
            <label>Current Bill (Read Only)</label>
            <input type="text" name="existing_bill" value="<?= htmlspecialchars($row['bill'] ?? '') ?>" readonly style="background-color: #f3f4f6; color: #666;">
        </div>
        <div class="form-group">
            <label style="color:#2563eb;">Upload NEW Bill (Leave blank to keep current bill)</label>
            <input type="file" name="new_bill" accept=".pdf,.jpg,.jpeg,.png">
        </div>

        <div class="footer-bar"><button type="submit" name="update" class="btn-save">Save Changes</button></div>
    </form>
</div>

<script>
function calculateExpiry() {
    const pDateVal = document.getElementById('p_date').value;
    const warrantyMonths = parseInt(document.getElementById('warranty').value);
    
    if (pDateVal && !isNaN(warrantyMonths)) {
        let pDate = new Date(pDateVal);
        pDate.setMonth(pDate.getMonth() + warrantyMonths);
        pDate.setDate(pDate.getDate() - 1); 
        
        const y = pDate.getFullYear();
        const m = String(pDate.getMonth() + 1).padStart(2, '0');
        const d = String(pDate.getDate()).padStart(2, '0');
        
        document.getElementById('e_date').value = `${y}-${m}-${d}`;
    }
}
</script>
</body>
</html>