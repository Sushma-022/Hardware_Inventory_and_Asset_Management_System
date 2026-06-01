<?php
include "../auth/auth.php";
include "../db.php";

/* Employees list */
$employees = $conn->query("SELECT * FROM employees ORDER BY name ASC");

/* Get equipment types dynamically from all equipment tables */
$equipment_types = $conn->query("
SELECT 'CPU' as equipment_type FROM cpu
UNION
SELECT 'MONITOR' FROM monitor
UNION
SELECT 'KEYBOARD' FROM keyboard
UNION
SELECT 'MOUSE' FROM mouse
UNION
SELECT 'COMBO' FROM combo_set
UNION
SELECT equipment_type FROM configurations
");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Issue Equipment</title>
  <link rel="stylesheet" href="../assets/css/admin.css"><style>
select {
  all: unset;
  border: 1px solid black;
  padding: 10px;
  width: 100%;
}
</style>
</head>
<body>

<div class="layout issue-page">

  <aside class="sidebar">
    <h2>Hardware Admin</h2>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="add_equipment.php">Add Equipment</a>
    <a href="view_equipment.php">View Equipment</a>
    
    <a class="active" href="issue_equipment_employee.php">Issue Equipment</a>
    <a href="view_issued.php">View Issued</a>
    <a href="upload_employees.php">Upload Employees</a>
    <a href="stock_available.php">Stock Available</a>
    <a href="backup_restore.php">Backup & Restore</a>

  </aside>

  <div class="main">
<div class="navbar">
  <span>Issue Equipment</span>

  <div class="nav-right">
    <a href="../auth/logout.php" class="logout-btn"
    onclick="return confirm('Are you sure you want to Logout?')">Logout</a>
  </div>
</div>
    <div class="content">

      <div class="card">

        <h2>Issue Equipment</h2>

        <form method="POST" action="save_issue.php" class="issue-form">

          <!-- Employee ID -->
          <label>Employee ID</label>
          <input type="text" name="employee_id" id="employee_id" required>

          <!-- Employee Name -->
          <label>Employee Name</label>
          <input type="text" name="employee_name" id="employee_name" readonly>

          <!-- Equipment Type Dropdown -->
          <label>Equipment Type</label>
       <select name="equipment_type" id="equipmentType" required>


            <option value="">Select Type</option>

            <?php while($row = $equipment_types->fetch_assoc()): ?>

              <option value="<?= $row['equipment_type'] ?>">
                <?= ucfirst(strtolower($row['equipment_type'])) ?>
              </option>

            <?php endwhile; ?>
            </select>
            <div id="stockInfo" style="display:none; margin-top:15px;">
    <b>Available Stock:</b> <span id="stockCount">0</span>

    <br><br>

    <b>Select Serial Numbers:</b>
    <div id="serialCheckboxList"></div>
</div>

<label>Block</label>

<select name="block" id="blockSelect" required>

  <option value="H Block">H Block</option>
  <option value="A Block">A Block</option>
  <option value="N Block">N Block</option>
  <option value="Pharmacy">Pharmacy</option>
  <option value="U Block">U Block</option>
  <option value="Other">Other</option>
</select>

<input type="text" name="other_block" placeholder="Enter Block"
style="display:none;" id="otherBlock">


<label>Department</label>
<select name="department" required>
 
  <option>CSE</option>
  <option>ECE</option>
  <option>EEE</option>
  <option>Mechanical</option>
  <option>Civil</option>
  <option>IT</option>
  <option value="Other">Other</option>
</select>

<input type="text" name="other_department" placeholder="Enter Department"
style="display:none;" id="otherDepartment">

<label>Lab</label>
<input type="text" name="lab" placeholder="Enter Lab Name" required>

<label>Purpose</label>
<input type="text" name="purpose" placeholder="Enter Purpose" required>

<label>No. of Equipments</label>
<input type="number" name="quantity" min="1" value="1" required>
          <!-- Issue Date -->
          <label>Issue Date</label>
          <input type="date" name="issue_date" required>

          <label>Return Date</label>
<input type="date" name="return_date" >

          <button type="submit" class="btn-primary">Issue Equipment</button>

        </form>

      </div>

    </div>

  </div>

</div>

<script>

function fetchEmployee() {
    let id = document.getElementById("employee_id").value;

    if (id !== "") {
        fetch("fetch_employee.php?id=" + id)
        .then(response => response.json())
        .then(data => {
            document.getElementById("employee_name").value = data.name || "";
        });
    } else {
        document.getElementById("employee_name").value = "";
    }
}

/* Run after page fully loads */
document.addEventListener("DOMContentLoaded", function () {

    /* Employee autofill trigger */
    document.getElementById("employee_id")
        .addEventListener("input", fetchEmployee);


    /* Other Block toggle */
    const blockSelect = document.getElementById("blockSelect");

    blockSelect.addEventListener("change", function () {
        document.getElementById("otherBlock").style.display =
            this.value === "Other" ? "block" : "none";
    });


    /* Equipment Stock Fetch */
    document.getElementById("equipmentType")
        .addEventListener("change", function () {

        let type = this.value;

        if (type !== "") {
            fetch("fetch_stock.php?type=" + encodeURIComponent(type))
            .then(response => response.json())
            .then(data => {

                document.getElementById("stockInfo").style.display = "block";
                document.getElementById("stockCount").innerText = data.count;

                let list = document.getElementById("serialCheckboxList");
                list.innerHTML = "";

                data.serials.forEach(function (serial) {

                    let label = document.createElement("label");
                    label.style.display = "block";

                    let checkbox = document.createElement("input");
                    checkbox.type = "checkbox";
                    checkbox.name = "selected_serials[]";
                    checkbox.value = serial;

                    label.appendChild(checkbox);
                    label.appendChild(document.createTextNode(" " + serial));

                    list.appendChild(label);
                });

            });
        }

    });

});


/* Force reload if page comes from browser cache */
window.addEventListener("pageshow", function (event) {
    if (event.persisted) {
        window.location.reload();
    }
});

</script>

</body>
</html>