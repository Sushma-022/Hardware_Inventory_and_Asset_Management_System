<?php
include "../auth/auth.php";
include "../db.php";

$search = $_GET['employee_search'] ?? '';

if($search != ''){
    $stmt = $conn->prepare("
        SELECT * 
        FROM issued_equipment_employee
        WHERE employee_id = ?
        ORDER BY issue_date DESC
    ");
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();
}
else{
    $result = $conn->query("
        SELECT * 
        FROM issued_equipment_employee
        ORDER BY issue_date DESC
    ");
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Issued Equipment</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="layout">

  <aside class="sidebar">
    <h2>Hardware Admin</h2>
    <a href="admin_dashboard.php">Dashboard</a>
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
  <span>Issued Equipment</span>

  <div class="nav-right">
    <a href="../auth/logout.php" class="logout-btn"
    onclick="return confirm('Are you sure you want to Logout?')">Logout</a>
  </div>
</div>
    <div class="content">

      <div class="card">

        <!-- SEARCH -->
        <form method="GET" class="search-form">

<input type="text" name="search" placeholder="Search by Employee ID or Name">

<button class="btn-primary">Search</button>

</form>

        <!-- ALERT -->
 <?php
if($result->num_rows == 0){
    echo "<div style='background:#ffe6e6;padding:10px;border-left:5px solid red;margin-bottom:10px;'>
            ⚠ No issued equipment found for this employee
          </div>";
}
?>

        <!-- TABLE -->
        <table>
          
            <tr>
  <th>Employee</th>
  <th>ID</th>
  <th>Equipment</th>
  <th>Serial No</th>
  <th>Block</th>
  <th>Department</th>
  <th>Lab</th>
<th>Purpose</th>
  <th>Quantity</th>
  <th>Issue Date</th>
  <th>Status</th>
  <th>Action</th>
<th>Returned to</th>
<th>Return Time</th>
  <th>Return Date</th>
</tr>
           
          

          <?php while($row = $result->fetch_assoc()): ?>
<tr>

<td><?= $row['employee_name'] ?></td>
<td><?= $row['employee_id'] ?></td>
<td><?= $row['equipment_type'] ?></td>
<td><?= $row['serial_number'] ?></td>
<td><?= $row['block'] ?></td>
<td><?= $row['department'] ?></td>
<td><?= $row['lab'] ?></td>
<td><?= $row['purpose'] ?></td>
<td><?= $row['quantity'] ?></td>

<td><?= $row['issue_date'] ?></td>

<!-- STATUS -->
<td>
<?php
$today = date('Y-m-d');

if($row['status'] == 'ISSUED' && !empty($row['return_date']) && $row['return_date'] < $today){
    echo "<span style='color:red;font-weight:bold;'>⚠ Overdue</span>";
} else {
    echo $row['status'];
}
?>
</td>

<!-- ACTION -->
<td>
<?php if($row['status'] == 'ISSUED'){ ?>
<a href="#" onclick="returnItem(<?= $row['id']; ?>)">Return</a>
<?php } else { ?>
  <span class="returned-text">Returned</span>
<?php } ?>
</td>
<td><?= $row['returned_to'] ?? '-' ?></td>
<td>
<?= !empty($row['return_time']) ? date("d-m-Y H:i", strtotime($row['return_time'])) : '-' ?>
</td>
<!-- RETURN DATE -->
<td>
<?php 
if(!empty($row['return_date'])){
    echo date("d-m-Y", strtotime($row['return_date']));
} else {
    echo "-";
}
?>
</td>

</tr>
<?php endwhile; ?>

        </table>

      </div>

    </div>

  </div>

</div>
<script>
function returnItem(id){

    let staff_id = prompt("Enter Staff/Admin ID:");

    if(staff_id == null || staff_id.trim() === ""){
        alert("Staff ID required!");
        return;
    }

    window.location = "return_equipment.php?id=" + id + "&staff_id=" + encodeURIComponent(staff_id);
}
/* Force reload if page comes from browser cache (back button) */
window.addEventListener("pageshow", function (event) {
    if (event.persisted ) {
        window.location.reload();
    }
});
</script>
</body>
</html>