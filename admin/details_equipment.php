<?php
include "../auth/auth.php";
include "../db.php";

$type = $_GET['type'] ?? '';
$serial = $_GET['serial'] ?? '';

if(!$type){
    echo "Invalid equipment";
    exit;
}

switch(strtolower($type)){
    case "cpu": $table = "cpu"; break;
    case "monitor": $table = "monitor"; break;
    case "keyboard": $table = "keyboard"; break;
    case "mouse": $table = "mouse"; break;
    case "combo": $table = "combo_set"; break;
    default: $table = "configurations"; break;
}

// Security: Using prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM $table WHERE serial_number = ?");
$stmt->bind_param("s", $serial);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    echo "No details found";
    exit;
}

$row = $result->fetch_assoc();
$total_price = ($row['price'] ?? 0) * ($row['quantity'] ?? 1);
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body { 
            font-family: 'Segoe UI', Roboto, Arial, sans-serif; 
            background: #f5f5f5; 
            margin: 0; 
            padding: 20px 20px 100px 20px; 
        }
        .modern-card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
        }
        .modern-header {
            background: #1B5E20; 
            color: white;
            padding: 15px 20px;
            margin: 0;
            border-radius: 8px 8px 0 0;
            font-size: 18px;
            font-weight: 600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 20px;
            border-bottom: 1px solid #f0f0f0;
            text-align: left;
        }
        th {
            background: #fafafa;
            width: 40%;
            color: #777;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            font-weight: bold;
        }
        td {
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        .bill-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            padding: 15px 20px;
            border-top: 1px solid #ddd;
            display: flex;
            justify-content: center;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }
        .btn-bill {
            background: #2563eb;
            color: white !important;
            padding: 10px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            display: block;
            width: 80%;
        }
    </style>
</head>
<body>

<div class="modern-card">
    <h2 class="modern-header"><?= strtoupper($type) ?> Specifications</h2>
    <table>
        <?php 
        foreach($row as $key => $value){
            // Skip showing ID or Bill path in the main table list
            if($key == "id" || $key == "bill") continue;

            // Format Dates
            if(($key == "purchase_date" || $key == "expiry_date") && !empty($value)){
                $value = date("d M Y", strtotime($value));
            }

            // Format Price
            if($key == "price"){
                $value = "₹" . number_format($value, 2);
            }

            echo "<tr>";
            echo "<th>".ucwords(str_replace("_"," ",$key))."</th>";
            echo "<td>".htmlspecialchars($value)."</td>";
            echo "</tr>";
        }

        // Add Total Value Row
        echo "<tr><th>Total Value</th><td>₹".number_format($total_price, 2)."</td></tr>";

        # -------- CUSTOM FIELDS --------
        $custom = $conn->query("SELECT field_name, field_value FROM custom_fields WHERE serial_number='$serial'");
        while($cf = $custom->fetch_assoc()){
            echo "<tr>";
            echo "<th>".htmlspecialchars($cf['field_name'])."</th>";
            echo "<td>".htmlspecialchars($cf['field_value'])."</td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>

<div class="bill-footer">
    <?php if (!empty($row['bill'])): ?>
        <a href="../uploads/<?= htmlspecialchars($row['bill']) ?>" target="_blank" class="btn-bill">
            View Original Bill Document
        </a>
    <?php else: ?>
        <span style="color: #999; font-style: italic;">No Bill Document Uploaded</span>
    <?php endif; ?>
</div>

</body>
</html>