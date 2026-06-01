<?php
include "../auth/auth.php";
require '../vendor/autoload.php';
include "../db.php";

use PhpOffice\PhpSpreadsheet\IOFactory;

if(isset($_POST['upload'])) {

    $file = $_FILES['excel']['tmp_name'];
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray();

    // 🔹 Expected header format
    $expected_headers = ['employee_id', 'name', 'department'];

    // 🔹 Get first row from Excel
    $excel_headers = array_map('trim', $data[0]);

    // 🔹 Compare headers
    if($excel_headers !== $expected_headers) {
        echo "<div style='color:red; margin-bottom:15px;'>
                ❌ Invalid Excel Format! 
                Columns must be: employee_id, name, department
              </div>";
        return; // Stop execution
    }

    // 🔹 If headers correct, insert data
    for($i = 1; $i < count($data); $i++) {

        $employee_id = $data[$i][0];
        $name = $data[$i][1];
        $department = $data[$i][2];

        $conn->query("INSERT INTO employees (employee_id, name, department)
                      VALUES ('$employee_id', '$name', '$department')");
    }

    echo "<div style='color:green; margin-bottom:15px;'>
            ✅ Employees uploaded successfully!
          </div>";
}
if(isset($_POST['upload'])){

    if($_FILES['excel']['error'] == 0){

        $filePath = $_FILES['excel']['tmp_name'];

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        foreach($rows as $index => $row){

            if($index == 0) continue; // Skip header row

            $employee_id = $row[0];
            $name        = $row[1];
            $department  = $row[2];

            $conn->query("INSERT INTO issue_employee 
            (employee_id, name, department)
            VALUES 
            ('$employee_id','$name','$department')");
        }

        echo "<script>alert('Employees Uploaded Successfully!');</script>";
    }
}
?>



<!DOCTYPE html>
<html>
<head>
  <title>Upload Employees</title>
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
    <a href="stock_available.php">Stock Available</a>
    <a href="backup_restore.php">Backup & Restore</a>


  
  
  </aside>

  <!-- Main Content -->
  <div class="main">
<div class="navbar">
  <span>Upload Employees</span>

  <div class="nav-right">
    <a href="../auth/logout.php" class="logout-btn"
    onclick="return confirm('Are you sure you want to Logout?')">Logout</a>
  </div>
</div>
    <div class="content">
      <div class="card-upload card">

<h4 style="margin-top:20px;">Excel Format Example:</h4>

<table border="1" cellpadding="8" cellspacing="0" style="margin-top:10px; border-collapse: collapse; width:100%;">
  <tr style="background:#f2f2f2;">
    <th>employee_id</th>
    <th>name</th>
    <th>department</th>
  </tr>
  <tr>
    <td>101</td>
    <td>Sushma</td>
    <td>CSE</td>
  </tr>
  <tr>
    <td>102</td>
    <td>Ravi</td>
    <td>ECE</td>
  </tr>
</table>

<p style="margin-top:10px; color:#555;">
⚠ Column names must match exactly as shown above.
</p>
        <form method="POST" enctype="multipart/form-data">
          <label>Select Excel File</label><br><br>
          <input type="file" name="excel" required><br><br>

          <div class="btn-group">

<button type="submit" name="upload" class="btn-primary">
  Upload
</button>

<a href="templates/employee_template.xlsx" class="btn-secondary">
  Download Excel Template
</a>

</div>
        </form>

      </div>
    </div>
  </div>

</div>
<script>
/* Force reload if page comes from browser cache (back button) */
window.addEventListener("pageshow", function (event) {
    if (event.persisted ){
        window.location.reload();
    }
});
</script>
</body>
</html>