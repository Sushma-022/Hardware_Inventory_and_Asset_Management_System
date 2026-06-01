<?php
include "../auth/auth.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../db.php";

/* default equipment tables */
$tables = [
"cpu"=>"CPU",
"monitor"=>"MONITOR",
"keyboard"=>"KEYBOARD",
"mouse"=>"MOUSE",
"combo_set"=>"Combo Set"
];

/* also check configurations table for new equipment */
$config = $conn->query("SELECT DISTINCT equipment_type FROM configurations");

while($c = $config->fetch_assoc()){

$type = strtoupper($c['equipment_type']);
$tables[$type] = $type;

}


$stock = [];

foreach($tables as $table=>$type){

/* Default equipment tables */
if(in_array($table,["cpu","monitor","keyboard","mouse","combo_set"])){

   $total = $conn->query("
SELECT COUNT(*) as total
FROM $table
")->fetch_assoc()['total'] ?? 0;

}

/* Newly added equipment from configurations */
else{

$total = $conn->query("
SELECT COUNT(*) as total
FROM configurations
WHERE UPPER(equipment_type)=UPPER('$type')
")->fetch_assoc()['total'] ?? 0;

}

/* Issued count */
$issued = $conn->query("
SELECT COUNT(*) as issued
FROM issued_equipment_employee
WHERE UPPER(equipment_type)=UPPER('$type')
AND status='ISSUED'
")->fetch_assoc()['issued'] ?? 0;

$balance = max(0,$total-$issued);

if($total > 0 || $issued > 0 || $table == $type){
$stock[] = [
    "type"=>$type,
    "total"=>$total,
    "issued"=>$issued,
    "balance"=>$balance
];
}

}

?>

<!DOCTYPE html>
<html>
<head>
<title>Stock Available</title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

<div class="layout">

<!-- Sidebar -->
<aside class="sidebar">

<h2>Hardware Admin</h2>

<a href="admin_dashboard.php">Dashboard</a>
<a href="add_equipment.php">Add Equipment</a>
<a href="view_equipment.php">View Equipment</a>
<a href="issue_equipment_employee.php">Issue Equipment</a>
<a href="view_issued.php">View Issued</a>
<a href="upload_employees.php">Upload Employees</a>

<a class="active" href="stock_available.php">Stock Available</a>
<a href="backup_restore.php">Backup & Restore</a>

</aside>


<!-- Main -->
<div class="main">
<div class="navbar">
  <span>Stock Available</span>

  <div class="nav-right">
    <a href="../auth/logout.php" class="logout-btn"
    onclick="return confirm('Are you sure you want to Logout?')">Logout</a>
  </div>
</div>
<div class="content">

<div class="card">

<h2>Stock Available</h2>

<table border="1" cellpadding="10" cellspacing="0">

<tr>
<th>Type</th>
<th>Total</th>
<th>Issued</th>
<th>Balance</th>
</tr>

<?php foreach($stock as $row): ?>

<tr>

<td><?= $row['type'] ?></td>

<td><?= $row['total'] ?></td>

<td><?= $row['issued'] ?></td>

<td style="color:<?= $row['balance'] == 0 ? 'red' : 'black' ?>; font-weight:bold;">
<?= $row['balance'] ?>
</td>

</tr>

<?php endforeach; ?>

</table>

</div>

</div>

</div>

</div>
<script>
/* Force reload if page comes from browser cache (back button) */
window.addEventListener("pageshow", function (event) {
    if (event.persisted) {
        window.location.reload();
    }
});
</script>
</body>
</html>