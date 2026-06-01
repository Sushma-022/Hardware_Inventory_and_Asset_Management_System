<?php
include "../auth/auth.php";
include "../db.php";
/* FETCH DATA FOR CHART */
$stockData = $conn->query("
SELECT t.equipment_type,
       COUNT(t.serial_number) as total,
       COUNT(i.serial_number) as issued
FROM (
    SELECT 'CPU' as equipment_type, serial_number FROM cpu
    UNION ALL
    SELECT 'MONITOR', serial_number FROM monitor
    UNION ALL
    SELECT 'KEYBOARD', serial_number FROM keyboard
    UNION ALL
    SELECT 'MOUSE', serial_number FROM mouse
    UNION ALL
    SELECT 'COMBO', serial_number FROM combo_set
    UNION ALL
    SELECT equipment_type, serial_number FROM configurations
) t
LEFT JOIN issued_equipment_employee i
ON t.serial_number = i.serial_number 
AND i.status = 'ISSUED'
GROUP BY t.equipment_type
");
$labels = [];
$totalArr = [];
$issuedArr = [];
$availableArr = [];
while($row = $stockData->fetch_assoc()){
    $labels[] = $row['equipment_type'];
    $totalArr[] = (int)$row['total'];
    $issuedArr[] = (int)$row['issued'];
    $availableArr[] = (int)$row['total'] - (int)$row['issued'];
}
/* TOTAL COUNTS */
$total = array_sum($totalArr);
$issued = array_sum($issuedArr);
$available = $total - $issued;
/* 🔥 LOW STOCK */
$lowStock = [];
for($i=0; $i<count($labels); $i++){
    if($availableArr[$i] <= 1){
        $lowStock[] = $labels[$i];
    }
}
/* 📅 MONTHLY REPORT */
$monthlyData = $conn->query("
SELECT DATE_FORMAT(issue_date, '%Y-%m') as month,
       COUNT(*) as total
FROM issued_equipment_employee
GROUP BY month
ORDER BY month ASC
");
$months = [];
$monthCounts = [];
while($row = $monthlyData->fetch_assoc()){
    $months[] = $row['month'];
    $monthCounts[] = (int)$row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .dashboard-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 20px;
    }
    .stat-card {
      padding: 20px;
      border-radius: 12px;
      color: #fff;
      text-align: center;
      transition: 0.3s;
    }
    .total { background: #4facfe; }
    .issued { background: #ff4d4d; }
    .available { background: #2ecc71; }
    .stat-card:hover {
      transform: scale(1.05);
    }
    #stockChart, #monthlyChart {
      max-width: 700px;
      margin: 20px auto;
      display: block;
    }
    table {
      width: 100%;
      margin-top: 15px;
      border-collapse: collapse;
    }
    table th, table td {
      padding: 10px;
      border: 1px solid #ccc;
      text-align: center;
    }
  </style>
</head>
<body>
<div class="layout">
<aside class="sidebar">
  <h2>Hardware Admin</h2>
  <a class="active" href="admin_dashboard.php">Dashboard</a>
  <a href="add_equipment.php">Add Equipment</a>
  <a href="view_equipment.php">View Equipment</a>
  <a href="issue_equipment_employee.php">Issue Equipment</a>
  <a href="view_issued.php">View Issued</a>
  <a href="upload_employees.php">Upload Employees</a>
  <a href="stock_available.php">Stock Available</a>
  <a href="backup_restore.php">Backup & Restore</a>
</aside>
<div class="main">
<div class="navbar">
  <span>Welcome to Admin Dashboard</span>
  <div class="nav-right">
    <a href="../auth/logout.php" class="logout-btn"
    onclick="return confirm('Are you sure you want to Logout?')">Logout</a>
  </div>
</div>
<div class="content">
<!-- ⚠ LOW STOCK -->
<?php if(count($lowStock) > 0): ?>
<div style="background:#ffcccc;padding:10px;border-radius:8px;margin-bottom:15px;">
⚠ <b>Low Stock:</b> <?= implode(", ", $lowStock) ?>
</div>
<?php endif; ?>
<!-- 🔥 CARDS -->
<div class="dashboard-cards">
  <div class="stat-card total">
    <h3>Total</h3>
    <p><?= $total ?></p>
  </div>
  <div class="stat-card issued">
    <h3>Issued</h3>
    <p><?= $issued ?></p>
  </div>
  <div class="stat-card available">
    <h3>Available</h3>
    <p><?= $available ?></p>
  </div>
</div>
<!-- 📊 GRAPH -->
<div class="card">
  <h3>Equipment Overview</h3>
  <canvas id="stockChart"></canvas>
</div>
<!-- 📋 TABLE -->
<div class="card">
  <h3>Equipment Summary</h3>
  <table>
    <tr>
      <th>Type</th>
      <th>Total</th>
      <th>Issued</th>
      <th>Available</th>
    </tr>
    <?php for($i=0;$i<count($labels);$i++): ?>
    <tr>
      <td><?= $labels[$i] ?></td>
      <td><?= $totalArr[$i] ?></td>
      <td><?= $issuedArr[$i] ?></td>
      <td><?= $availableArr[$i] ?></td>
    </tr>
    <?php endfor; ?>
  </table>
</div>
</div>
</div>
</div>
<script>
new Chart(document.getElementById('stockChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [
            { label: 'Total', data: <?= json_encode($totalArr) ?>, backgroundColor: 'blue' },
            { label: 'Issued', data: <?= json_encode($issuedArr) ?>, backgroundColor: 'red' },
            { label: 'Available', data: <?= json_encode($availableArr) ?>, backgroundColor: 'green' }
        ]
    }
});
/* Force reload if page comes from browser cache (back button) */
window.addEventListener("pageshow", function (event) {
    if (event.persisted) {
        window.location.reload();
    }
});
</script>
</body>
</html>