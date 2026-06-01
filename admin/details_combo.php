<?php
include "../auth/auth.php";
include "../db.php";
$serial = $_GET['serial'] ?? '';
$stmt = $conn->prepare("SELECT * FROM combo_set WHERE serial_number = ?");
$stmt->bind_param("s", $serial);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$kbSerial = $row['kb_serial'] ?? '';
$mouseSerial = $row['mouse_serial'] ?? '';
?>
<div style="font-family: Arial; line-height: 1.6;">
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <tr><td><strong>Combo Serial:</strong></td><td><?= $row['serial_number'] ?></td></tr>
        <tr><td><strong>Vendor:</strong></td><td><?= $row['vendor'] ?></td></tr>
        <tr><td><strong>Keyboard Serial:</strong></td><td><?= $kbSerial ?></td></tr>
        <tr><td><strong>Mouse Serial:</strong></td><td><?= $mouseSerial ?></td></tr>
        <tr><td><strong>Price:</strong></td><td>₹<?= number_format($row['price'], 2) ?></td></tr>
        <tr><td><strong>Quantity:</strong></td><td><?= $row['quantity'] ?></td></tr>
        <tr><td><strong>Total:</strong></td><td>₹<?= number_format($row['price'] * $row['quantity'], 2) ?></td></tr>
        <tr><td><strong>Purchase Date:</strong></td><td><?= $row['purchase_date'] ?: 'Not Set' ?></td></tr>
        <tr><td><strong>Warranty:</strong></td><td><?= $row['warranty'] ?> months</td></tr>
        <tr><td><strong>Expiry Date:</strong></td><td><?= $row['expiry_date'] ?: 'Not Set' ?></td></tr>
        <?php if ($row['bill']): ?>
        <tr><td><strong>Bill:</strong></td><td><a href="../uploads/<?= $row['bill'] ?>" target="_blank">View Bill</a></td></tr>
        <?php endif; ?>
    </table>
</div>
