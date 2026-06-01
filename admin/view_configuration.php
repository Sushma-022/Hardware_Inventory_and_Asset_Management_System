<?php
include "../auth/auth.php";
include "../db.php";

$result = $conn->query("
SELECT c.id, e.name AS equipment_name, c.vendor, c.model, c.company, c.price, c.quantity
FROM configurations c
LEFT JOIN equipment_types e ON c.equipment_type_id = e.id
ORDER BY c.id DESC
");
?>

<!DOCTYPE html>
<html>

<head>

<title>View Configurations</title>

<link rel="stylesheet" href="../assets/css/admin.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>

<body>

<div class="layout">

<!-- SIDEBAR -->
<aside class="sidebar">

<h2>Hardware Admin</h2>

<a href="admin_dashboard.php">Dashboard</a>
<a href="add_equipment.php">Add Equipment</a>
<a href="view_equipment.php">View Equipment</a>
<a href="manage_equipment.php">Manage Equipment</a>
<a href="add_configuration.php">Add Configuration</a>
<a class="active" href="view_configuration.php">View Configuration</a>

</aside>


<div class="main">

<div class="navbar">
All Equipment Configurations
</div>

<div class="content">

<div class="card">

<h3>Saved Equipment</h3>

<table>

<thead>

<tr>

<th>ID</th>
<th>Equipment</th>
<th>Vendor</th>
<th>Model</th>
<th>Company</th>
<th>Price</th>
<th>Qty</th>
<th>Actions</th>

</tr>

</thead>

<tbody>

<?php while($row = $result->fetch_assoc()) { ?>

<tr>

<td><?= $row['id'] ?></td>

<td><?= htmlspecialchars($row['equipment_name'] ?? '-') ?></td>

<td><?= htmlspecialchars($row['vendor'] ?? '-') ?></td>

<td><?= htmlspecialchars($row['model'] ?? '-') ?></td>

<td><?= htmlspecialchars($row['company'] ?? '-') ?></td>

<td><?= number_format($row['price'],2) ?></td>

<td><?= $row['quantity'] ?></td>

<td>

<a href="view_configuration_details.php?config_id=<?= $row['id'] ?>" class="details-btn">
<i class="fa fa-eye"></i>
</a>

<a href="edit_configuration.php?config_id=<?= $row['id'] ?>" style="color:blue">
<i class="fa fa-edit"></i>
</a>

<a href="delete_configuration.php?config_id=<?= $row['id'] ?>"
onclick="return confirm('Delete this configuration?')"
style="color:red">
<i class="fa fa-trash"></i>
</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</body>
</html>