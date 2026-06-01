<?php
include "../auth/auth.php";
include "../db.php";

$serial = $_GET['serial'] ?? '';
if($serial == '') die("Serial number missing");

$stmt = $conn->prepare("SELECT * FROM cpu WHERE serial_number = ?");
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
            /* THE FIX FOR THE CUT-OFF: Extra padding at the bottom! */
            padding: 20px 20px 40px 20px; 
        }
        .modern-card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 1px solid #e0e0e0;
            margin-bottom: 20px;
        }
        .modern-header {
            background: #1B5E20; 
            color: white;
            padding: 15px 20px;
            margin: 0;
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
            width: 35%;
            color: #777;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            font-weight: bold;
            border-right: 1px solid #f0f0f0;
        }
        td {
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        tr:last-child th, tr:last-child td {
            border-bottom: none;
        }
        .btn-view {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 6px 15px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="modern-card">
    <h2 class="modern-header">CPU Specification: <?= htmlspecialchars($row['serial_number']) ?></h2>
    <table>
        <tr><th>Vendor</th><td><?= htmlspecialchars($row['vendor']) ?></td></tr>
        <tr><th>Company</th><td><?= htmlspecialchars($row['company']) ?></td></tr>
        <tr><th>Processor</th><td><?= htmlspecialchars($row['processor']) ?></td></tr>
        <tr><th>RAM</th><td><?= htmlspecialchars($row['ram']) ?></td></tr>
        <tr><th>Storage</th><td><?= htmlspecialchars($row['storage']) ?></td></tr>
        <tr><th>Generation</th><td><?= htmlspecialchars($row['generation']) ?></td></tr>
        <tr><th>LAN MAC</th><td><?= htmlspecialchars($row['lan_mac']) ?></td></tr>
        <tr><th>Wifi Card / MAC</th><td><?= htmlspecialchars($row['wifi_card']) ?> &nbsp;|&nbsp; <?= htmlspecialchars($row['wifi_mac']) ?></td></tr>
        
        <tr><th>Quantity</th><td><?= htmlspecialchars($row['quantity'] ?? 1) ?> Units</td></tr>
        
        <tr><th>Price (Per Unit)</th><td>₹<?= number_format($row['price'], 2) ?></td></tr>
        <tr><th>Total Value</th><td>₹<?= number_format($total_price, 2) ?></td></tr>
        
        <tr><th>Purchase Date</th><td><?= date('d M Y', strtotime($row['purchase_date'])) ?></td></tr>
        <tr><th>Warranty Period</th><td><?= htmlspecialchars($row['warranty']) ?> Months</td></tr>
        <tr><th>Expiry Date</th>
            <td style="<?= (strtotime($row['expiry_date']) < time()) ? 'color: red; font-weight: bold;' : '' ?>">
                <?= date('d M Y', strtotime($row['expiry_date'])) ?>
            </td>
        </tr>
        <tr><th>Original Bill</th>
            <td>
                <?php if (!empty($row['bill'])): ?>
                    <a href="../uploads/<?= htmlspecialchars($row['bill']) ?>" target="_blank" class="btn-view">View Document</a>
                <?php else: ?>
                    <span style="color: #999; font-style: italic;">No document uploaded</span>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>

</body>
</html>