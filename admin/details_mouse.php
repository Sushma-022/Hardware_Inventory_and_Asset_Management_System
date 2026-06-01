<?php
include "../auth/auth.php";
include "../db.php";

$serial = $_GET['serial'] ?? '';
if($serial == '') die("Serial number missing");

/* GET DATA */
$stmt = $conn->prepare("SELECT * FROM mouse WHERE serial_number = ?");
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
    <h2 class="modern-header">Mouse Specification</h2>
    <table>
        <tr><th>Serial Number</th><td><?= htmlspecialchars($row['serial_number']) ?></td></tr>
        <tr><th>Vendor</th><td><?= htmlspecialchars($row['vendor']) ?></td></tr>
        <tr><th>Company</th><td><?= htmlspecialchars($row['company']) ?></td></tr>
        <tr><th>Model</th><td><?= htmlspecialchars($row['model']) ?></td></tr>
        <tr><th>Quantity</th><td><?= htmlspecialchars($row['quantity'] ?? 1) ?> Units</td></tr>
        <tr><th>Price (Unit)</th><td>₹<?= number_format($row['price'], 2) ?></td></tr>
        <tr><th>Total Value</th><td>₹<?= number_format($total_price, 2) ?></td></tr>
        <tr><th>Purchase Date</th><td><?= !empty($row['purchase_date']) ? date('d M Y', strtotime($row['purchase_date'])) : 'Not Set' ?></td></tr>
        <tr><th>Warranty</th><td><?= htmlspecialchars($row['warranty']) ?> Months</td></tr>
        <tr>
            <th>Expiry Date</th>
            <td style="<?= (isset($row['expiry_date']) && strtotime($row['expiry_date']) < time()) ? 'color: red; font-weight: bold;' : '' ?>">
                <?= !empty($row['expiry_date']) ? date('d M Y', strtotime($row['expiry_date'])) : 'N/A' ?>
            </td>
        </tr>
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