<?php 
include "../auth/auth.php"; 
$backup_dir = "../backups/";
$files = array_diff(scandir($backup_dir), array('.', '..'));

date_default_timezone_set("Asia/Kolkata");
?>

<!DOCTYPE html>
<html>
<head>
<title>Backup Manager</title>
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

  <a class="active" href="backup_restore.php">Backup & Restore</a>
</aside>

<div class="main">

<div class="navbar">
  <span>Backup Manager</span>

  <div class="nav-right">
    <a href="../auth/logout.php" class="logout-btn"
    onclick="return confirm('Are you sure you want to Logout?')">Logout</a>
  </div>
</div>

<div class="content">

<!-- SUCCESS MESSAGE -->
<?php if(isset($_GET['success'])): ?>
<div class="success-msg">
  Backup Created Successfully!
</div>
<?php endif; ?>

<!-- 🔥 CLEAR DATA SUCCESS -->
<?php if(isset($_GET['cleared'])): ?>
<div class="success-msg" style="background:#ffe0e0;color:#c00;">
  All data cleared successfully!
</div>
<?php endif; ?>

<!-- CREATE BACKUP -->
<div class="card">
  <div class="center-box">
    <a href="create_backup.php" class="btn-primary">
      Create Backup
    </a>
  </div>
</div>

<!-- 🔥 NEW: RESET SYSTEM -->
<div class="card">

<h3 style="color:red;">Reset System</h3>
<p>This will delete ALL data permanently.</p>

<form method="POST" action="clear_data.php" onsubmit="return confirmReset();">

<input type="text" name="confirmText" id="confirmText"
placeholder="Type RESET to confirm" required
style="padding:8px; margin-bottom:10px; width:250px;">

<br>

<button type="submit" class="btn-danger">
Clear All Data
</button>

</form>

</div>

<!-- TABLE -->
<div class="card">
<h3>Available Backups</h3>

<table>

<tr>
<th>File Name</th>
<th>Size (KB)</th>
<th>Date</th>
<th>Download</th>
<th>Restore</th>
<th>Delete</th>
</tr>

<?php foreach($files as $file): ?>

<tr>

<td><?= $file ?></td>

<td><?= round(filesize($backup_dir.$file)/1024,2) ?></td>

<td><?= date("d-m-Y h:i A", filemtime($backup_dir.$file)) ?></td>

<td>
<a href="../backups/<?= $file ?>" class="btn-primary">Download</a>
</td>

<td>
<a href="restore_backup.php?file=<?= $file ?>" 
onclick="return confirm('Restore this backup?')" 
class="btn-primary">Restore</a>
</td>

<td>
<a href="delete_backup.php?file=<?= $file ?>" 
onclick="return confirm('Delete this backup?')" 
class="btn-danger">Delete</a>
</td>

</tr>

<?php endforeach; ?>

</table>

</div>

</div>
</div>
</div>

<!-- SCRIPTS -->
<script>
setTimeout(() => {
  let msg = document.querySelector('.success-msg');
  if(msg) msg.style.display = 'none';
}, 3000);

/* Force reload if page comes from browser cache */
window.addEventListener("pageshow", function (event) {
    if (event.persisted ) {
        window.location.reload();
    }
});

/* 🔥 RESET CONFIRMATION */
function confirmReset(){
    let val = document.getElementById("confirmText").value;

    if(val !== "RESET"){
        alert("Type RESET to confirm!");
        return false;
    }

    return confirm("This will DELETE ALL DATA permanently. Continue?");
}
</script>

</body>
</html>