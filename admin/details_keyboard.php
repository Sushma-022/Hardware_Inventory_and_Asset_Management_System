<?php
include "../auth/auth.php";
include "../db.php";

$serial = $_GET['serial'] ?? '';
if($serial == '') die("Serial number missing");

/* GET DATA */
$stmt = $conn->prepare("SELECT * FROM keyboard WHERE serial_number = ?");
$stmt->bind_param("s", $serial);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if(!$row) die("Equipment not found.");

$total_price = $row['price'] * ($row['quantity'] ?? 1);
?>

<!DOCTYPE html>
<html>
<head>
    <style>
    body { 
        font-family: 'Segoe UI', Roboto, Arial, sans-serif; 
        background: #f5f5f5; 
        margin: 0; 
        padding: 20px; 
    }
    .modern-card {
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: 1px solid #e0e0e0;
        /* Removed overflow:hidden so buttons can breathe */
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
    /* This makes the last row taller so the button isn't squashed */
    tr:last-child td {
        padding-bottom: 30px; 
        border-bottom: none;
    }
    tr:last-child th {
        border-bottom: none;
    }
    .btn-view {
        display: inline-block;
        background: #2563eb;
        color: white !important;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 4px;
        font-size: 13px;
        font-weight: bold;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>
</head>
<body>

<div class="modern-card">
    <h2 class="modern-header">Keyboard Specification</h2>
    <table>
        <tr><th>Serial Number</th><td><?= htmlspecialchars($row['serial_number'] ?? 'N/A') ?></td></tr>
        <tr><th>Vendor</th><td><?= htmlspecialchars($row['vendor'] ?? 'N/A') ?></td></tr>
        <tr><th>Company</th><td><?= htmlspecialchars($row['company'] ?? 'N/A') ?></td></tr>
        <tr><th>Model</th><td><?= htmlspecialchars($row['model'] ?? 'N/A') ?></td></tr>
        <tr><th>Quantity</th><td><?= htmlspecialchars($row['quantity'] ?? 0) ?> Units</td></tr>
        <tr><th>Price (Unit)</th><td>₹<?= number_format($row['price'] ?? 0, 2) ?></td></tr>
        <tr><th>Total Value</th><td>₹<?= number_format($total_price ?? 0, 2) ?></td></tr>
        <tr><th>Purchase Date</th><td><?= !empty($row['purchase_date']) ? date('d M Y', strtotime($row['purchase_date'])) : 'N/A' ?></td></tr>
        <tr><th>Warranty</th><td><?= htmlspecialchars($row['warranty'] ?? 0) ?> Months</td></tr>
        <tr>
            <th>Expiry Date</th>
            <td><?= !empty($row['expiry_date']) ? date('d M Y', strtotime($row['expiry_date'])) : 'N/A' ?></td>
        </tr>
        
        <tr>
            <th style="padding-bottom: 40px;">Original Bill</th>
            <td style="padding-bottom: 40px;">
                <a href="../uploads/<?= htmlspecialchars($row['bill']) ?>" target="_blank" class="btn-view">
                    View Document
                </a>
            </td>
        </tr>
    </table>
</div>
</body>
</html>