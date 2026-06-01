<?php
include "../auth/auth.php";
include "../db.php";

$config_id = intval($_GET['config_id']);

$stmt = $conn->prepare("SELECT * FROM configurations WHERE id=?");
$stmt->bind_param("i",$config_id);
$stmt->execute();
$result = $stmt->get_result();
$eq = $result->fetch_assoc();
?>

<h2>Equipment Details</h2>

Vendor: <?= $eq['vendor_name'] ?><br>
Model: <?= $eq['model'] ?><br>
Company: <?= $eq['company'] ?><br>
Price: <?= $eq['price'] ?><br>
Quantity: <?= $eq['quantity'] ?><br>
Total: <?= $eq['total'] ?><br>
Purchase Date: <?= $eq['purchase_date'] ?><br>
Warranty: <?= $eq['warranty'] ?><br>
Expiry: <?= $eq['expiry_date'] ?><br>

<h3>Custom Fields</h3>

<?php

$stmt2 = $conn->prepare("
SELECT ef.field_name, cv.value
FROM configuration_values cv
JOIN equipment_fields ef ON ef.id=cv.field_id
WHERE cv.configuration_id=?
");

$stmt2->bind_param("i",$config_id);
$stmt2->execute();
$res2 = $stmt2->get_result();

while($row = $res2->fetch_assoc()){
echo $row['field_name']." : ".$row['value']."<br>";
}

?>